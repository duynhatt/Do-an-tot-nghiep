<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\BienThe;
use App\Models\Voucher; // Thêm Model Voucher
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
            // Chỉ cho phép checkout các dòng giỏ còn hợp lệ:
            // - Sản phẩm đang bật và danh mục đang bật
            // - Biến thể đang bật
            ->whereHas('sanPham', function ($q) {
                $q->where('trang_thai', true)
                    ->whereHas('danhMuc', function ($q2) {
                        $q2->where('trang_thai', 1);
                    });
            })
            ->whereHas('bienThe', function ($q) {
                $q->where('trang_thai', true);
            })
            ->get();

        // Đồng bộ giá và kiểm tra lại tồn kho cho từng dòng giỏ hàng
        $validCartItems = $cartItems->filter(function ($item) use ($itemQuantities) {
            $item->syncGiaMoi();
            $stock = $item->bienThe?->so_luong ?? 0;

            // Hết hàng hoàn toàn -> loại khỏi danh sách checkout
            if ($stock <= 0) {
                $item->checkout_qty = 0;
                $item->checkout_thanh_tien = 0;
                return false;
            }

            $requestedQty = $itemQuantities[$item->id] ?? $item->so_luong;
            $requestedQty = (int) max(1, $requestedQty);

            // Không cho vượt quá tồn kho hiện tại
            $item->checkout_qty = min($requestedQty, $stock);
            $item->checkout_thanh_tien = (int) round($item->don_gia * $item->checkout_qty);

            return $item->checkout_qty > 0;
        });

        // Nếu sau khi kiểm tra tồn kho mà không còn sản phẩm hợp lệ -> quay lại giỏ hàng
        if ($validCartItems->isEmpty()) {
            return redirect()->route('gio-hang.index')->with('error', 'Sản phẩm trong giỏ đã hết hàng hoặc không còn hợp lệ. Vui lòng kiểm tra lại.');
        }
        $cartItems = $validCartItems->values();

        $subtotal = $cartItems->sum('checkout_thanh_tien');
        $shippingFee = $this->calculateShippingFee($subtotal);
        $total = $subtotal + $shippingFee;

        // Voucher đang hiệu lực, còn lượt dùng
        $now = Carbon::now();
        $availableVouchers = Voucher::where('trang_thai', 1)
            ->where('bat_dau', '<=', $now)
            ->where('ket_thuc', '>=', $now)
            ->whereRaw('(so_luong IS NULL OR da_su_dung < so_luong)')
            ->orderBy('ket_thuc')
            ->get();

        return view('client.checkout.index', compact(
            'cartItems',
            'subtotal',
            'shippingFee',
            'total',
            'availableVouchers'
        ));
    }

    /**
     * Lưu thông tin "mua ngay" vào session, không dùng giỏ hàng.
     */
    public function buyNow(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success'  => false,
                'redirect' => route('login'),
            ], 401);
        }

        $data = $request->validate([
            'san_pham_id' => 'required|integer',
            'bien_the_id' => 'required|integer',
            'so_luong'    => 'required|integer|min:1',
        ]);

        $variant = BienThe::with(['SanPham', 'size', 'color'])
            ->where('id', $data['bien_the_id'])
            ->where('san_pham_id', $data['san_pham_id'])
            ->where('trang_thai', true)
            ->firstOrFail();

        $stock = (int) $variant->so_luong;
        $qty   = min((int) $data['so_luong'], $stock);

        if ($qty < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm đã hết hàng.',
            ], 422);
        }

        $unitPrice = (int) ($variant->gia_khuyen_mai ?? $variant->gia);

        Session::put('buy_now', [
            'user_id'    => $user->id,
            'product_id' => $variant->san_pham_id,
            'variant_id' => $variant->id,
            'qty'        => $qty,
            'unit_price' => $unitPrice,
        ]);

        return response()->json([
            'success'  => true,
            'redirect' => route('checkout.buy-now'),
        ]);
    }

    /**
     * Trang checkout cho "mua ngay" (đọc từ session buy_now).
     */
    public function checkoutBuyNow(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thanh toán');
        }

        $buyNow = Session::get('buy_now');
        if (!$buyNow || ($buyNow['user_id'] ?? null) !== $user->id) {
            return redirect()->route('home')->with('error', 'Không tìm thấy sản phẩm mua ngay.');
        }

        $variant = BienThe::with(['SanPham', 'size', 'color'])
            ->where('id', $buyNow['variant_id'])
            ->where('san_pham_id', $buyNow['product_id'])
            ->where('trang_thai', true)
            ->firstOrFail();

        $stock = (int) $variant->so_luong;
        if ($stock <= 0) {
            return redirect()->route('home')->with('error', 'Sản phẩm đã hết hàng.');
        }

        $qty       = min((int) $buyNow['qty'], $stock);
        $unitPrice = (int) $buyNow['unit_price'];
        $lineTotal = (int) ($unitPrice * $qty);

        $cartItems = collect([
            (object) [
                'sanPham'             => $variant->SanPham,
                'bienThe'             => $variant,
                'checkout_qty'        => $qty,
                'checkout_thanh_tien' => $lineTotal,
            ],
        ]);

        $subtotal    = $lineTotal;
        $shippingFee = $this->calculateShippingFee($subtotal);
        $total       = $subtotal + $shippingFee;

        $now = Carbon::now();
        $availableVouchers = Voucher::where('trang_thai', 1)
            ->where('bat_dau', '<=', $now)
            ->where('ket_thuc', '>=', $now)
            ->whereRaw('(so_luong IS NULL OR da_su_dung < so_luong)')
            ->orderBy('ket_thuc')
            ->get();

        return view('client.checkout.index', [
            'cartItems'          => $cartItems,
            'subtotal'           => $subtotal,
            'shippingFee'        => $shippingFee,
            'total'              => $total,
            'buyNowMode'         => true,
            'availableVouchers'   => $availableVouchers,
        ]);
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
        $cartItems = GioHang::with(['bienThe', 'sanPham.danhMuc'])
            ->whereIn('id', array_keys($itemQuantities))
            ->dangTrongGio()
            // Không cho tạo đơn từ các dòng giỏ đã bị ẩn:
            // - Sản phẩm hoặc danh mục ẩn
            // - Biến thể ẩn
            ->whereHas('sanPham', function ($q) {
                $q->where('trang_thai', true)
                    ->whereHas('danhMuc', function ($q2) {
                        $q2->where('trang_thai', 1);
                    });
            })
            ->whereHas('bienThe', function ($q) {
                $q->where('trang_thai', true);
            })
            ->get();

        $subtotal = 0;
        $orderMeta = [];
        foreach ($cartItems as $item) {
            $stock = $item->bienThe?->so_luong ?? 0;
            $requestedQty = $itemQuantities[$item->id] ?? $item->so_luong;
            $requestedQty = (int) max(1, $requestedQty);

            // Nếu hết hàng hoặc không đủ tồn kho -> dừng và yêu cầu người dùng kiểm tra lại giỏ
            if ($stock <= 0 || $requestedQty > $stock) {
                return redirect()->route('gio-hang.index')
                    ->with('error', 'Sản phẩm "' . ($item->sanPham->ten_san_pham ?? '') . '" không đủ tồn kho. Vui lòng kiểm tra lại giỏ hàng.');
            }

            $qty = min($requestedQty, $stock);
            $amount = (int) round($item->don_gia * $qty);
            $subtotal += $amount;
            $orderMeta[$item->id] = ['qty' => $qty, 'amount' => $amount];
        }

        // Nếu sau khi kiểm tra mà không còn dòng hợp lệ (phòng trường hợp hiếm)
        if (empty($orderMeta)) {
            return redirect()->route('gio-hang.index')
                ->with('error', 'Giỏ hàng không còn sản phẩm hợp lệ để thanh toán.');
        }

        $shippingFee = $this->calculateShippingFee($subtotal);

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
                if ($voucher->don_hang_toi_thieu && $subtotal < $voucher->don_hang_toi_thieu) {
                    return back()->with('error', 'Đơn hàng chưa đủ điều kiện tối thiểu ' . number_format($voucher->don_hang_toi_thieu) . 'đ để áp dụng voucher.');
                }
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

        $totalDiscount = $voucherDiscount;
        $total = max(0, $subtotal + $shippingFee - $totalDiscount);

        DB::beginTransaction();
        try {
            $fullAddress = "{$request->address}, {$request->ward}, {$request->district}, {$request->province}";
            $maDonHang = 'DH' . date('ymd') . strtoupper(\Illuminate\Support\Str::random(6));
            $donHang = DonHang::create([
                'nguoi_dung_id'           => $user->id,
                'ma_don_hang'             => $maDonHang,
                'voucher_id'              => $voucherId,
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
                    $purchasedQty = $orderMeta[$item->id]['qty'] ?? 0;

                    if ($item->bienThe && $purchasedQty > 0) {
                        $item->bienThe->decrement('so_luong', $purchasedQty);
                    }

                    // Giữ lại phần chưa mua trong giỏ (nếu có)
                    $remaining = max(0, $item->so_luong - $purchasedQty);
                    if ($remaining > 0) {
                        $item->so_luong = $remaining;
                        $item->save();
                    } else {
                        $item->delete();
                    }
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

    /**
     * Xử lý đặt hàng cho "mua ngay" (không dùng giỏ hàng).
     */
    public function processBuyNow(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'full_name'      => 'required|string|max:100',
            'phone'          => 'required|regex:/^0[0-9]{9,10}$/',
            'province'       => 'required|string|max:100',
            'district'       => 'required|string|max:100',
            'ward'           => 'required|string|max:100',
            'address'        => 'required|string|max:255',
            'payment_method' => 'required|in:cod,vnpay',
            'voucher_code_applied' => 'nullable|string',
        ]);

        $buyNow = Session::get('buy_now');
        if (!$buyNow || ($buyNow['user_id'] ?? null) !== $user->id) {
            return redirect()->route('home')->with('error', 'Không tìm thấy sản phẩm mua ngay để thanh toán.');
        }

        $variant = BienThe::with(['SanPham'])
            ->where('id', $buyNow['variant_id'])
            ->where('san_pham_id', $buyNow['product_id'])
            ->where('trang_thai', true)
            ->firstOrFail();

        $stock = (int) $variant->so_luong;
        if ($stock <= 0) {
            return redirect()->route('home')->with('error', 'Sản phẩm đã hết hàng.');
        }

        $qty       = min((int) $buyNow['qty'], $stock);
        $unitPrice = (int) $buyNow['unit_price'];
        $subtotal  = (int) ($unitPrice * $qty);

        $shippingFee = $this->calculateShippingFee($subtotal);

        // Xử lý voucher giống process()
        $voucherDiscount = 0;
        $voucherId = null;
        if ($request->voucher_code_applied) {
            $voucher = Voucher::whereRaw('BINARY ma = ?', [$request->voucher_code_applied])
                ->where('bat_dau', '<=', now())
                ->where('ket_thuc', '>=', now())
                ->where('trang_thai', 1)
                ->first();

            if ($voucher && $voucher->da_su_dung < $voucher->so_luong) {
                if ($voucher->don_hang_toi_thieu && $subtotal < $voucher->don_hang_toi_thieu) {
                    return back()->with('error', 'Đơn hàng chưa đủ điều kiện tối thiểu ' . number_format($voucher->don_hang_toi_thieu) . 'đ để áp dụng voucher.');
                }
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

        $totalDiscount = $voucherDiscount;
        $total = max(0, $subtotal + $shippingFee - $totalDiscount);

        DB::beginTransaction();
        try {
            $fullAddress = "{$request->address}, {$request->ward}, {$request->district}, {$request->province}";
            $maDonHang = 'DH' . date('ymd') . strtoupper(\Illuminate\Support\Str::random(6));

            $donHang = DonHang::create([
                'nguoi_dung_id'           => $user->id,
                'ma_don_hang'             => $maDonHang,
                'voucher_id'              => $voucherId,
                'tam_tinh'                => $subtotal,
                'tien_giam'               => $totalDiscount,
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

            ChiTietDonHang::create([
                'don_hang_id' => $donHang->id,
                'san_pham_id' => $variant->san_pham_id,
                'bien_the_id' => $variant->id,
                'don_gia'     => $unitPrice,
                'so_luong'    => $qty,
                'thanh_tien'  => $subtotal,
            ]);

            if ($voucherId) {
                Voucher::find($voucherId)->increment('da_su_dung');
            }

            // Xóa session buy_now sau khi tạo đơn
            Session::forget('buy_now');

            if ($request->payment_method === 'cod') {
                // Trừ tồn kho trực tiếp cho biến thể
                $variant->so_luong = max(0, (int) $variant->so_luong - $qty);
                $variant->save();

                DB::commit();
                return redirect()->route('order.success', $donHang->ma_don_hang);
            }

            if ($request->payment_method === 'vnpay') {
                DB::commit();
                return $this->initiateVnpay($donHang, $total);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi đặt hàng: ' . $e->getMessage());
        }
    }

    private function initiateVnpay($donHang, $total)
    {
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
            "vnp_BankCode" => "NCB"
        ];
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        return redirect($vnp_Url);
    }

    private function calculateShippingFee($subtotal)
    {
        return $subtotal >= 1000000 ? 0 : 35000;
    }

    private function parseItemsQuantities(string $itemsParam): array
    {
        $result = [];
        $parts = array_filter(explode(',', $itemsParam));
        foreach ($parts as $part) {
            if (strpos($part, ':') !== false) {
                [$id, $qty] = explode(':', $part);
                $result[(int)$id] = (int)$qty;
            } else {
                $result[(int)$part] = null;
            }
        }
        return $result;
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

        $txnRef = $request->vnp_TxnRef ?? null;
        $maDonHang = explode('_', $txnRef)[0] ?? null;

        $donHang = $maDonHang ? DonHang::where('ma_don_hang', $maDonHang)->first() : null;

        if (!$donHang) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Nếu đơn đã được xử lý thanh toán trước đó thì không làm lại
        if ($donHang->trang_thai_thanh_toan === 'da_thanh_toan') {
            return redirect()->route('order.success', $donHang->ma_don_hang);
        }

        if (strtolower($secureHash) !== strtolower($vnp_SecureHash)) {
            return redirect()->route('home')
                ->with('error', 'Chữ ký giao dịch không hợp lệ. Vui lòng liên hệ hỗ trợ.');
        }

        $responseCode = $request->vnp_ResponseCode ?? '99';

        if ($responseCode !== '00') {
            return redirect()->route('home')
                ->with('error', 'Thanh toán thất bại, hãy thực hiện thanh toán lại trong 15 phút');
        }

        // Thanh toán VNPAY thành công -> trừ tồn kho + cập nhật giỏ hàng giống COD
        DB::beginTransaction();
        try {
            $donHang->load('chiTietDonHangs.bienThe', 'chiTietDonHangs.sanPham');

            foreach ($donHang->chiTietDonHangs as $ct) {
                $purchasedQty = max(0, (int) $ct->so_luong);

                if ($ct->bienThe && $purchasedQty > 0) {
                    $currentStock = (int) $ct->bienThe->so_luong;
                    $newStock = max(0, $currentStock - $purchasedQty);
                    $ct->bienThe->so_luong = $newStock;
                    $ct->bienThe->save();
                }

                // Cập nhật giỏ hàng của người dùng cho biến thể tương ứng (nếu còn trong giỏ)
                $cartItems = GioHang::where('nguoi_dung_id', $donHang->nguoi_dung_id)
                    ->where('san_pham_id', $ct->san_pham_id)
                    ->where('bien_the_id', $ct->bien_the_id)
                    ->dangTrongGio()
                    ->get();

                foreach ($cartItems as $item) {
                    $remaining = max(0, $item->so_luong - $purchasedQty);
                    if ($remaining > 0) {
                        $item->so_luong = $remaining;
                        $item->save();
                    } else {
                        $item->delete();
                    }
                }
            }

            $donHang->update([
                'trang_thai_thanh_toan' => 'da_thanh_toan',
                'trang_thai'            => 'dang_xu_ly',
            ]);

            DB::commit();

            return redirect()->route('order.success', $donHang->ma_don_hang)
                ->with('success', 'Thanh toán VNPAY thành công! Đơn hàng đã được xác nhận.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('home')
                ->with('error', 'Lỗi xử lý sau thanh toán: ' . $e->getMessage());
        }
    }

    public function repay($id)
    {

        $donHang = DonHang::with('chiTietDonHangs')->findOrFail($id);

        $donHang->checkAutoCancel();

        if ($donHang->trang_thai === 'da_huy') {
            return back()->with('error', 'Đơn hàng đã hết thời gian thanh toán và đã bị hủy.');
        }

        if ($donHang->trang_thai_thanh_toan !== 'chua_thanh_toan') {
            return redirect()->back()->with('error', 'Đơn hàng này đã được thanh toán.');
        }

        if ($donHang->phuong_thuc_thanh_toan !== 'vnpay') {
            return redirect()->back()->with('error', 'Đơn hàng này không sử dụng VNPAY.');
        }

        $vnp_Url        = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_TmnCode    = "KE8AMY5Q";
        $vnp_HashSecret = "QIN1IHTRN9CSYSUGV2EK6MV3ZC2OKNLT";
        $vnp_ReturnUrl  = route('vnpay.return');

        $vnp_TxnRef = $donHang->ma_don_hang . '_' . time();
        $vnp_OrderInfo  = "Thanh toan don hang " . $donHang->ma_don_hang;
        $vnp_OrderType  = "order";
        $vnp_Amount     = $donHang->tong_tien * 100;
        $vnp_Locale     = 'vn';

        $vnp_CreateDate = now();
        $vnp_ExpireDate = $vnp_CreateDate->copy()->addMinute(5);

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => $vnp_Amount,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => $vnp_CreateDate->format('YmdHis'),
            "vnp_ExpireDate" => $vnp_ExpireDate->format('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => request()->ip(),
            "vnp_Locale"     => $vnp_Locale,
            "vnp_OrderInfo"  => $vnp_OrderInfo,
            "vnp_OrderType"  => $vnp_OrderType,
            "vnp_ReturnUrl"  => $vnp_ReturnUrl,
            "vnp_TxnRef"     => $vnp_TxnRef,
        ];

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
}
