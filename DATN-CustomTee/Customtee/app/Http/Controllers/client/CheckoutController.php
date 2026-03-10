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

        $validated = $request->validate([
            'full_name'      => 'required|string|max:100',
            'phone'          => 'required|regex:/^0[0-9]{9,10}$/',
            'province'       => 'required|string|max:100',
            'district'       => 'required|string|max:100',
            'ward'           => 'required|string|max:100',
            'address'        => 'required|string|max:255',
            'note'           => 'nullable|string|max:500',
            'payment_method' => 'required|in:cod,vnpay', 
            'selected_items' => 'required|string',
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

        if ($cartItems->isEmpty() || count($cartItems->pluck('id')->toArray()) !== count($selectedIds)) {
            return back()->with('error', 'Các sản phẩm bạn chọn không hợp lệ hoặc đã thay đổi');
        }

        DB::beginTransaction();

        try {
            $subtotal    = $cartItems->sum('thanh_tien');
            $shippingFee = $this->calculateShippingFee($subtotal);
            $discount    = $this->calculateDiscount($cartItems);
            $total       = $subtotal + $shippingFee - $discount;

            $fullAddress = trim("{$request->address}, {$request->ward}, {$request->district}, {$request->province}");

            $maDonHang = 'DH' . date('ymd') . strtoupper(\Illuminate\Support\Str::random(6));

            $donHang = DonHang::create([
                'nguoi_dung_id'           => $user->id,
                'ma_don_hang'             => $maDonHang,
                'tam_tinh'                => $subtotal,
                'tien_giam'               => $discount,
                'phi_van_chuyen'          => $shippingFee,
                'tong_tien'               => $total,
                'phuong_thuc_thanh_toan'  => $request->payment_method, 
                'trang_thai_thanh_toan'   => 'chua_thanh_toan',
                'trang_thai'              => 'cho_xac_nhan',
                'ghi_chu'                 => $request->note,
                'dia_chi_chi_tiet'        => $fullAddress,
                'so_dien_thoai_nhan_hang' => $request->phone,
                'ten_nguoi_nhan'          => $request->full_name,
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
                    $oldStock = $item->bienThe->so_luong;
                    $item->bienThe->decrement('so_luong', $item->so_luong);
                }

                $item->update(['trang_thai' => GioHang::TRANG_THAI_DA_DAT_HANG]);
            }

            GioHang::whereIn('id', $selectedIds)
                ->where('nguoi_dung_id', $user->id)
                ->delete();

            DB::commit();

            if ($request->payment_method === 'cod') {
                return redirect()->route('order.success', $donHang->ma_don_hang)
                    ->with('success', 'Đặt hàng thành công!');
            }

            if ($request->payment_method === 'vnpay') { 

                $vnp_Url        = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
                $vnp_TmnCode    = "KE8AMY5Q";
                $vnp_HashSecret = "QIN1IHTRN9CSYSUGV2EK6MV3ZC2OKNLT";
                $vnp_ReturnUrl  = route('vnpay.return'); 

                $vnp_TxnRef     = $donHang->ma_don_hang;
                $vnp_OrderInfo  = "Thanh toan don hang " . $donHang->ma_don_hang;
                $vnp_OrderType  = "order";
                $vnp_Amount     = $total * 100;
                $vnp_Locale     = 'vn';
                $vnp_BankCode   = 'NCB'; 

                $inputData = [
                    "vnp_Version"    => "2.1.0",
                    "vnp_TmnCode"    => $vnp_TmnCode,
                    "vnp_Amount"     => $vnp_Amount,
                    "vnp_Command"    => "pay",
                    "vnp_CreateDate" => now()->format('YmdHis'),
                    "vnp_CurrCode"   => "VND",
                    "vnp_IpAddr"     => $request->ip(),
                    "vnp_Locale"     => $vnp_Locale,
                    "vnp_OrderInfo"  => $vnp_OrderInfo,
                    "vnp_OrderType"  => $vnp_OrderType,
                    "vnp_ReturnUrl"  => $vnp_ReturnUrl,
                    "vnp_TxnRef"     => $vnp_TxnRef,
                ];

                if ($vnp_BankCode !== '') {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }

                ksort($inputData);

                $hashdata = '';
                $query = '';
                $first = true;

                foreach ($inputData as $key => $value) {
                    if ($first) {
                        $first = false;
                    } else {
                        $hashdata .= '&';
                        $query .= '&';
                    }
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $query    .= urlencode($key) . "=" . urlencode($value);
                }

                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

                $vnp_Url = $vnp_Url . "?" . $query . '&vnp_SecureHash=' . $vnpSecureHash;

                return redirect($vnp_Url);
            }

            return redirect()->route('order.success', $donHang->ma_don_hang)
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Đặt hàng thất bại. Vui lòng thử lại. (Lỗi đã được ghi log)');
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

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = "QIN1IHTRN9CSYSUGV2EK6MV3ZC2OKNLT";

        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;

        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $maDonHang = $request->vnp_TxnRef ?? null;
        $donHang = $maDonHang ? DonHang::where('ma_don_hang', $maDonHang)->first() : null;

        if (!$donHang) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if (strtolower($secureHash) === strtolower($vnp_SecureHash)) {
            $responseCode = $request->vnp_ResponseCode ?? '99';

            if ($responseCode === '00') {
                $donHang->update([
                    'trang_thai_thanh_toan' => 'da_thanh_toan',
                    'trang_thai'            => 'dang_xu_ly',
                ]);

                return redirect()->route('order.success', $donHang->ma_don_hang)
                    ->with('success', 'Thanh toán VNPAY thành công! Đơn hàng đã được xác nhận.');
            } else {
                return redirect()->route('checkout.index')
                    ->with('error', 'Thanh toán thất bại: ' . $this->getVnpayErrorMessage($responseCode));
            }
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Chữ ký giao dịch không hợp lệ. Vui lòng liên hệ hỗ trợ.');
    }

    private function getVnpayErrorMessage($code)
    {
        $errors = [
            '00'  => 'Giao dịch thành công',
            '07'  => 'Trừ tiền thành công. Giao dịch bị nghi ngờ',
            '09'  => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking',
            '10'  => 'Xác thực thông tin thẻ/tài khoản không thành công',
            '11'  => 'Hết hạn chờ thanh toán',
            '12'  => 'Hủy giao dịch',
            '13'  => 'Lỗi OTP',
            '24'  => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51'  => 'Số dư không đủ để thanh toán',
            '65'  => 'Tài khoản đã vượt hạn mức thanh toán trong ngày',
            '75'  => 'Ngân hàng thanh toán đang bảo trì',
            '79'  => 'Thanh toán không thành công do: Khách hàng hủy giao dịch',
            '99'  => 'Các lỗi khác (lỗi không xác định)',
        ];

        return $errors[$code] ?? 'Lỗi không xác định (mã: ' . $code . ')';
    }
}
