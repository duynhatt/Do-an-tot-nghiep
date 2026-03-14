<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\Voucher; // Thêm Model Voucher
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thanh toán');
        }

        $itemsParam = $request->query('items', '');
        $itemQuantities = $this->parseItemsQuantities($itemsParam);
        $selectedIdsArray = array_keys($itemQuantities);

        if (empty($selectedIdsArray)) {
            return redirect()->route('gio-hang.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm');
        }

        $cartItems = GioHang::with(['sanPham', 'bienThe.size', 'bienThe.color'])
            ->where('nguoi_dung_id', $userId)
            ->whereIn('id', $selectedIdsArray)
            ->dangTrongGio()
            ->get();

        foreach ($cartItems as $item) {
            $item->syncGiaMoi();
            $requestedQty = $itemQuantities[$item->id] ?? $item->so_luong;
            $item->checkout_qty = max(1, min($requestedQty, $item->bienThe?->so_luong ?? 0));
            $item->checkout_thanh_tien = (int) round($item->don_gia * $item->checkout_qty);
        }

        $subtotal = $cartItems->sum('checkout_thanh_tien');
        $shippingFee = $this->calculateShippingFee($subtotal);
        
        // Giảm giá mặc định (mua trên 3 sp giảm 10%)
        $systemDiscount = $this->calculateDiscountForCheckout($cartItems);
        
        $total = $subtotal + $shippingFee - $systemDiscount;

        return view('client.checkout.index', compact(
            'cartItems', 'subtotal', 'shippingFee', 'systemDiscount', 'total'
        ));
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'full_name'      => 'required|string|max:100',
            'phone'           => 'required|regex:/^0[0-9]{9,10}$/',
            'province'       => 'required|string|max:100',
            'district'       => 'required|string|max:100',
            'ward'           => 'required|string|max:100',
            'address'        => 'required|string|max:255',
            'payment_method' => 'required|in:cod,vnpay',
            'selected_items' => 'required|string',
            'voucher_code_applied' => 'nullable|string' // Nhận mã voucher từ form
        ]);

        $itemQuantities = $this->parseItemsQuantities($request->selected_items);
        $cartItems = GioHang::whereIn('id', array_keys($itemQuantities))->get();

        $subtotal = 0;
        $orderMeta = [];
        foreach ($cartItems as $item) {
            $qty = $itemQuantities[$item->id] ?? $item->so_luong;
            $amount = (int) round($item->don_gia * $qty);
            $subtotal += $amount;
            $orderMeta[$item->id] = ['qty' => $qty, 'amount' => $amount];
        }

        $shippingFee = $this->calculateShippingFee($subtotal);
        $systemDiscount = $this->calculateDiscountForProcess($orderMeta);
        
        // --- XỬ LÝ VOUCHER (BẮT BUỘC KHỚP HOA THƯỜNG) ---
        $voucherDiscount = 0;
        $voucherId = null;
        if ($request->voucher_code_applied) {
            // Sử dụng BINARY để so sánh chính xác từng ký tự hoa/thường
            $voucher = Voucher::whereRaw('BINARY ma = ?', [$request->voucher_code_applied])
                ->where('bat_dau', '<=', now())
                ->where('ket_thuc', '>=', now())
                ->where('trang_thai', 1)
                ->first();
            
            if ($voucher && $voucher->da_su_dung < $voucher->so_luong) {
                if ($voucher->loai == 'phan_tram') {
                    $voucherDiscount = ($subtotal * $voucher->gia_tri) / 100;
                    if ($voucher->giam_toi_da > 0 && $voucherDiscount > $voucher->giam_toi_da) {
                        $voucherDiscount = $voucher->giam_toi_da;
                    }
                } else {
                    $voucherDiscount = $voucher->gia_tri;
                }
                $voucherId = $voucher->id;
            }
        }

        $totalDiscount = $systemDiscount + $voucherDiscount;
        $total = max(0, $subtotal + $shippingFee - $totalDiscount);

        DB::beginTransaction();
        try {
            $fullAddress = "{$request->address}, {$request->ward}, {$request->district}, {$request->province}";
            $maDonHang = 'DH' . date('ymd') . strtoupper(\Illuminate\Support\Str::random(6));

            $donHang = DonHang::create([
                'nguoi_dung_id'           => $user->id,
                'ma_don_hang'             => $maDonHang,
                'tam_tinh'                => $subtotal,
                'tien_giam'               => $totalDiscount, // Tổng giảm = hệ thống + voucher
                'phi_van_chuyen'          => $shippingFee,
                'tong_tien'               => $total,
                'phuong_thuc_thanh_toan'  => $request->payment_method,
                'trang_thai_thanh_toan'   => 'chua_thanh_toan',
                'trang_thai'              => 'cho_xac_nhan',
                'dia_chi_chi_tiet'        => $fullAddress,
                'so_dien_thoai_nhan_hang' => $request->phone,
                'ten_nguoi_nhan'          => $request->full_name,
                'ghi_chu'                 => $request->note . ($request->voucher_code_applied ? " (Voucher: {$request->voucher_code_applied})" : ""),
            ]);

            foreach ($cartItems as $item) {
                ChiTietDonHang::create([
                    'don_hang_id' => $donHang->id,
                    'san_pham_id' => $item->san_pham_id,
                    'bien_the_id' => $item->bien_the_id,
                    'don_gia'     => $item->don_gia,
                    'so_luong'    => $orderMeta[$item->id]['qty'],
                    'thanh_tien'  => $orderMeta[$item->id]['amount'],
                ]);
            }

            // Cập nhật số lần dùng Voucher
            if ($voucherId) {
                Voucher::find($voucherId)->increment('da_su_dung');
            }

            if ($request->payment_method === 'cod') {
                foreach ($cartItems as $item) {
                    if ($item->bienThe) $item->bienThe->decrement('so_luong', $orderMeta[$item->id]['qty']);
                    $item->delete();
                }
                DB::commit();
                return redirect()->route('order.success', $donHang->ma_don_hang);
            }

            // Xử lý VNPAY (Tiền gửi sang đã trừ voucher)
            if ($request->payment_method === 'vnpay') {
                DB::commit();
                return $this->initiateVnpay($donHang, $total);
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi đặt hàng: ' . $e->getMessage());
        }
    }

    private function initiateVnpay($donHang, $total) {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_TmnCode = "KE8AMY5Q";
        $vnp_HashSecret = "QIN1IHTRN9CSYSUGV2EK6MV3ZC2OKNLT";
        
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $total * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan don hang " . $donHang->ma_don_hang,
            "vnp_OrderType" => "order",
            "vnp_ReturnUrl" => route('vnpay.return'),
            "vnp_TxnRef" => $donHang->ma_don_hang,
        ];
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            else { $hashdata .= urlencode($key) . "=" . urlencode($value); $i = 1; }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        return redirect($vnp_Url);
    }

    private function calculateShippingFee($subtotal) { return $subtotal >= 1000000 ? 0 : 35000; }
    
    private function parseItemsQuantities(string $itemsParam): array {
        $result = [];
        $parts = array_filter(explode(',', $itemsParam));
        foreach ($parts as $part) {
            if (strpos($part, ':') !== false) {
                [$id, $qty] = explode(':', $part);
                $result[(int)$id] = (int)$qty;
            } else { $result[(int)$part] = null; }
        }
        return $result;
    }
    
    private function calculateDiscountForCheckout($cartItems): float {
        return ($cartItems->sum('checkout_qty') >= 3) ? round($cartItems->sum('checkout_thanh_tien') * 0.1, 0) : 0;
    }
    
    private function calculateDiscountForProcess($orderMeta): float {
        $qty = array_sum(array_column($orderMeta, 'qty'));
        $amt = array_sum(array_column($orderMeta, 'amount'));
        return ($qty >= 3) ? round($amt * 0.1, 0) : 0;
    }

    public function vnpayReturn(Request $request) {
        // Giữ nguyên logic vnpayReturn hiện tại của bạn
    }
}