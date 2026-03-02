<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Pest\Support\Str;

class CheckoutController extends Controller
{

    public function index(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thanh toán');
        }

        $selectedIds = $request->query('items');
        $selectedIdsArray = $selectedIds ? explode(',', $selectedIds) : [];

        if (empty($selectedIdsArray)) {
            return redirect()->route('cart.index')
                ->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán');
        }

        $cartItems = GioHang::with([
            'sanPham',
            'bienThe',
            'bienThe.size',
            'bienThe.color',
        ])
            ->where('nguoi_dung_id', $userId)
            ->whereIn('id', $selectedIdsArray)
            ->dangTrongGio()
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Không tìm thấy sản phẩm nào hợp lệ để thanh toán');
        }

        $foundIds = $cartItems->pluck('id')->toArray();
        if (count($foundIds) !== count($selectedIdsArray)) {
            return redirect()->route('cart.index')
                ->with('warning', 'Một số sản phẩm bạn chọn không còn tồn tại trong giỏ hàng');
        }

        foreach ($cartItems as $item) {
            $item->syncGiaMoi();

            if ($item->so_luong <= 0 || $item->bienThe?->so_luong < $item->so_luong) {
                return redirect()->route('cart.index')
                    ->with('error', 'Sản phẩm "' . $item->sanPham->ten_san_pham . '" không đủ số lượng hoặc đã hết hàng');
            }

            if ($item->isDirty()) {
                $item->save();
            }
        }

        $cartItems = $cartItems->fresh();

        $subtotal    = $cartItems->sum('thanh_tien');
        $shippingFee = $this->calculateShippingFee($subtotal);
        $discount    = $this->calculateDiscount($cartItems);
        $total       = $subtotal + $shippingFee - $discount;

        return view('client.checkout.index', compact(
            'cartItems',
            'subtotal',
            'shippingFee',
            'discount',
            'total'
        ));
    }

    public function process(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để đặt hàng');
        }

        try {
            $validated = $request->validate([
                'full_name'       => 'required|string|max:100',
                'phone'           => 'required|regex:/^0[0-9]{9,10}$/',
                'province'        => 'required|string|max:100',
                'district'        => 'required|string|max:100',
                'ward'            => 'required|string|max:100',
                'address'         => 'required|string|max:255',
                'note'            => 'nullable|string|max:500',
                'payment_method'  => 'required|in:cod,online',
                'selected_items'  => 'required|string', 
            ]);

            $selectedIds = array_filter(explode(',', $request->selected_items));
            $selectedIds = array_map('intval', $selectedIds);

            if (empty($selectedIds)) {
                return back()->with('error', 'Không có sản phẩm nào được chọn để đặt hàng');
            }

            $cartItems = GioHang::with(['sanPham', 'bienThe'])
                ->where('nguoi_dung_id', $user->id)
                ->whereIn('id', $selectedIds)
                ->dangTrongGio()
                ->get();

            if ($cartItems->isEmpty()) {
                Log::warning('Không tìm thấy sản phẩm được chọn trong giỏ hàng', ['user_id' => $user->id, 'selected' => $selectedIds]);
                return back()->with('error', 'Các sản phẩm bạn chọn không còn tồn tại hoặc đã thay đổi');
            }

            $foundIds = $cartItems->pluck('id')->toArray();
            if (count($foundIds) !== count($selectedIds)) {
                return back()->with('error', 'Một số sản phẩm bạn chọn không hợp lệ hoặc đã thay đổi');
            }

            DB::beginTransaction();

            $subtotal    = $cartItems->sum('thanh_tien');
            $shippingFee = $this->calculateShippingFee($subtotal);
            $discount    = 0; 
            $total       = $subtotal + $shippingFee - $discount;

            $fullAddress = trim("{$request->address}, {$request->ward}, {$request->district}, {$request->province}");

            $maDonHang = 'DH' . date('y') . strtoupper(Str::random(6));

            $donHang = DonHang::create([
                'nguoi_dung_id'          => $user->id,
                'dia_chi_id'             => null,
                'voucher_id'             => null,
                'ma_don_hang'            => $maDonHang,
                'tam_tinh'               => $subtotal,
                'tien_giam'              => $discount,
                'phi_van_chuyen'         => $shippingFee,
                'tong_tien'              => $total,
                'phuong_thuc_thanh_toan' => $request->payment_method,
                'trang_thai_thanh_toan'  => 'chua_thanh_toan',
                'trang_thai'             => 'cho_xac_nhan',
                'ghi_chu'                => $request->note,
                'dia_chi_chi_tiet'       => $request->address,
                'so_dien_thoai_nhan_hang' => $request->phone,
                'ten_nguoi_nhan'         => $request->full_name,
            ]);

            foreach ($cartItems as $item) {
                ChiTietDonHang::create([
                    'don_hang_id' => $donHang->id,
                    'san_pham_id' => $item->san_pham_id,
                    'bien_the_id' => $item->bien_the_id,
                    'don_gia'     => $item->don_gia,
                    'so_luong'    => $item->so_luong,
                    'thanh_tien'  => $item->thanh_tien,
                ]);

                if ($item->bienThe) {
                    $item->bienThe->decrement('so_luong', $item->so_luong);
                    Log::info("Cập nhật tồn kho", ['bien_the_id' => $item->bien_the_id, 'giam' => $item->so_luong]);
                }

                $item->update(['trang_thai' => GioHang::TRANG_THAI_DA_DAT_HANG]);
            }

            DB::commit();

            GioHang::whereIn('id', $selectedIds)
                ->where('nguoi_dung_id', $user->id)
                ->delete();

            return redirect()->route('order.success', $donHang->ma_don_hang)
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Đặt hàng thất bại', ['error' => $e->getMessage(), 'user_id' => $user->id ?? null]);
            return back()->with('error', 'Đặt hàng thất bại. Vui lòng thử lại.');
        }
    }
    private function calculateShippingFee($subtotal)
    {
        if ($subtotal >= 1000000) {
            return 0;
        }
        return 35000;
    }


    private function calculateDiscount($cartItems)
    {
        if ($cartItems->sum('so_luong') >= 3) {
            return $cartItems->sum('thanh_tien') * 0.10;
        }
        return 0;
    }
}
