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

    public function index()
    {
        $userId = Auth::id();

        if (!$userId) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thanh toán');
        }

        $cartItems = GioHang::with([
            'sanPham',
            'bienThe',
            'bienThe.size',
            'bienThe.color',
        ])
            ->where('nguoi_dung_id', $userId)
            ->dangTrongGio()
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('info', 'Giỏ hàng của bạn đang trống');
        }

        foreach ($cartItems as $item) {
            $item->syncGiaMoi();

            if ($item->so_luong <= 0) {
                $item->delete();
                continue;
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
                'full_name'      => 'required|string|max:100',
                'phone'          => 'required|regex:/^0[0-9]{9,10}$/',
                'province'       => 'required|string|max:100',
                'district'       => 'required|string|max:100',
                'ward'           => 'required|string|max:100',
                'address'        => 'required|string|max:255',
                'note'           => 'nullable|string|max:500',
                'payment_method' => 'required|in:cod,online',
            ]);

            $cartItems = GioHang::with(['sanPham', 'bienThe'])
                ->where('nguoi_dung_id', $user->id)
                ->dangTrongGio()
                ->get();

            if ($cartItems->isEmpty()) {
                Log::warning('Giỏ hàng trống');
                return back()->with('error', 'Giỏ hàng của bạn đang trống');
            }

            DB::beginTransaction();

            $subtotal    = $cartItems->sum('thanh_tien');
            $shippingFee = $this->calculateShippingFee($subtotal);
            $discount    = 0;
            $total       = $subtotal + $shippingFee - $discount;

            $fullAddress = trim("{$request->address}, {$request->ward}, {$request->district}, {$request->province}");

            $maDonHang = 'DH' . date('y') . strtoupper(Str::random(6));

            $donHang = DonHang::create([
                'nguoi_dung_id'         => $user->id,
                'dia_chi_id'            => null,
                'voucher_id'            => null,
                'ma_don_hang'           => $maDonHang,
                'tam_tinh'              => $subtotal,
                'tien_giam'             => $discount,
                'phi_van_chuyen'        => $shippingFee,
                'tong_tien'             => $total,
                'phuong_thuc_thanh_toan' => $request->payment_method,
                'trang_thai_thanh_toan' => 'chua_thanh_toan',
                'trang_thai'            => 'cho_xac_nhan',
                'ghi_chu'               => $request->note,
                'dia_chi_chi_tiet'      => $request->address,
                'so_dien_thoai_nhan_hang' => $request->phone,
                'ten_nguoi_nhan'        => $request->full_name,
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
                    Log::info("UPDATED STOCK bien_the_id={$item->bien_the_id}");
                }

                $item->update(['trang_thai' => GioHang::TRANG_THAI_DA_DAT_HANG]);
            }

            DB::commit();

            GioHang::where('nguoi_dung_id', $user->id)
                ->dangTrongGio()
                ->delete();

            return redirect()->route('order.success', $donHang->ma_don_hang);
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
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
