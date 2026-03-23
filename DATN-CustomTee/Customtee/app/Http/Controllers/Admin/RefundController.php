<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RefundController extends Controller
{
    private $vnp_Url = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
    private $vnp_TmnCode = "KE8AMY5Q";
    private $vnp_HashSecret = "QIN1IHTRN9CSYSUGV2EK6MV3ZC2OKNLT";

    public function index(Request $request)
    {
        $query = Refund::with(['donHang', 'user', 'items.chiTietDonHang.sanPham'])
            ->latest();

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $refunds = $query->paginate(15);

        return view('admin.hoan-tra.index', compact('refunds'));
    }

    public function show(Refund $refund)
    {
        $refund->load([
            'donHang',
            'user',
            'items.chiTietDonHang.sanPham',
            'items.chiTietDonHang.bienThe',
            'items.chiTietDonHang.bienThe.color',
            'items.chiTietDonHang.bienThe.size',
            'images'
        ]);

        return view('admin.hoan-tra.show', compact('refund'));
    }

    public function accept(Request $request, Refund $refund)
    {
        if ($refund->da_xu_ly) {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        DB::beginTransaction();

        try {
            $refund->update(['trang_thai' => 'da_chap_nhan']);

            $order = $refund->donHang;

            if ($order->phuong_thuc_thanh_toan !== 'vnpay') {
                throw new \Exception('Chỉ hỗ trợ hoàn tiền tự động cho giao dịch VNPAY.');
            }

            $vnp_TxnRef = $order->vnp_TxnRef ?? null;
            if (!$vnp_TxnRef) {
                throw new \Exception('Không tìm thấy mã giao dịch VNPAY (vnp_TxnRef).');
            }

            $vnp_TransactionNo = $order->vnp_TransactionNo ?? '';
            if (empty($vnp_TransactionNo)) {
                throw new \Exception('Không tìm thấy vnp_TransactionNo từ giao dịch gốc. Không thể refund.');
            }

            $vnp_TransactionDate = $this->normalizeVnpDate($order->vnp_PayDate ?? $order->created_at);

            // Sử dụng tổng tiền đã thanh toán (nếu có cột riêng), fallback về tong_tien
            $orderPaidTotal = $order->tong_tien_da_thanh_toan ?? $order->tong_tien;

            $refundResult = $this->vnpayRefund(
                $vnp_TxnRef,
                $refund->so_tien_yeu_cau,
                $vnp_TransactionDate,
                "RefundOrder#{$order->ma_don_hang}",
                $orderPaidTotal,
                $vnp_TransactionNo
            );

            if (!$refundResult['success']) {
                throw new \Exception('Hoàn tiền VNPAY thất bại: ' . $refundResult['message']);
            }

            $refund->update(['trang_thai' => 'da_hoan_tien']);

            foreach ($refund->items as $item) {
                $chiTiet = $item->chiTietDonHang;
                if ($chiTiet && $chiTiet->bienThe) {
                    $chiTiet->bienThe->increment('so_luong', $item->so_luong_yeu_cau);
                }
            }

            $order->update([
                'ghi_chu' => trim(($order->ghi_chu ?? '') . "\nĐã hoàn tiền: " . number_format($refund->so_tien_yeu_cau, 0, ',', '.') . "₫ - " . now()->format('d/m/Y H:i')),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã chấp nhận và hoàn tiền thành công qua VNPAY.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Chấp nhận & hoàn tiền refund thất bại', [
                'refund_id' => $refund->id,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Xử lý chấp nhận & hoàn tiền thất bại: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Refund $refund)
    {
        if ($refund->da_xu_ly) {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        DB::beginTransaction();

        try {
            $refund->update(['trang_thai' => 'da_tu_choi']);
            DB::commit();

            return redirect()->back()->with('success', 'Đã từ chối yêu cầu hoàn trả.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Từ chối refund thất bại', [
                'refund_id' => $refund->id,
                'error'     => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Có lỗi xảy ra khi từ chối: ' . $e->getMessage());
        }
    }

    private function normalizeVnpDate($dateValue): string
    {
        if (is_string($dateValue) && strlen($dateValue) === 14 && ctype_digit($dateValue)) {
            return $dateValue;
        }

        try {
            return Carbon::parse($dateValue)->format('YmdHis');
        } catch (\Exception $e) {
            Log::warning('Không parse được ngày giao dịch, dùng hiện tại', ['value' => $dateValue]);
            return Carbon::now()->format('YmdHis');
        }
    }

    private function vnpayRefund(
        string $txnRef,
        int $amount,
        string $transactionDate,
        string $orderInfo,
        int $orderTotal,
        string $transactionNo
    ): array {
        $requestId = (string) (time() . rand(100000, 999999));
        $createDate = Carbon::now()->format('YmdHis');
        $transactionType = ($amount >= $orderTotal) ? "02" : "03";

        $inputData = [
            "vnp_RequestId"       => $requestId,
            "vnp_Version"         => "2.1.0",
            "vnp_Command"         => "refund",
            "vnp_TmnCode"         => $this->vnp_TmnCode,
            "vnp_TransactionType" => "02",
            "vnp_TxnRef"          => $txnRef,
            "vnp_Amount"          => 115000, 
            "vnp_TransactionNo"   => $transactionNo,
            "vnp_TransactionDate" => $transactionDate,
            "vnp_CreateBy"        => "admin",
            "vnp_CreateDate"      => $createDate,
            "vnp_IpAddr"          => '127.0.0.1',
            "vnp_OrderInfo"       => $orderInfo,
        ];

        $hashData = implode('|', [
            $inputData['vnp_RequestId'],
            $inputData['vnp_Version'],
            $inputData['vnp_Command'],
            $inputData['vnp_TmnCode'],
            $inputData['vnp_TransactionType'],
            $inputData['vnp_TxnRef'],
            $inputData['vnp_Amount'],
            $inputData['vnp_TransactionNo'] ?? '',
            $inputData['vnp_TransactionDate'],
            $inputData['vnp_CreateBy'],
            $inputData['vnp_CreateDate'],
            $inputData['vnp_IpAddr'],
            $inputData['vnp_OrderInfo'],
        ]);

        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);
        $inputData['vnp_SecureHash'] = $secureHash;

        Log::info('FINAL REQUEST DATA (SEND TO VNPAY)', $inputData);

        try {
            $startTime = microtime(true);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post($this->vnp_Url, $inputData);

            $endTime = microtime(true);

            $result = $response->json() ?? [];

            Log::info('VNPAY RESPONSE DETAIL', [
                'http_status'      => $response->status(),
                'response_time_ms' => round(($endTime - $startTime) * 1000, 2),
                'raw_body'         => $response->body(),
                'json_parsed'      => $result,
            ]);

            Log::info('VNPAY RESPONSE IMPORTANT FIELDS', [
                'vnp_ResponseCode' => $result['vnp_ResponseCode'] ?? null,
                'vnp_Message'      => $result['vnp_Message'] ?? null,
                'vnp_TxnRef'       => $result['vnp_TxnRef'] ?? null,
                'vnp_Amount'       => $result['vnp_Amount'] ?? null,
            ]);

            if (isset($result['vnp_ResponseCode']) && $result['vnp_ResponseCode'] === '00') {
                Log::info('=== REFUND SUCCESS ===');
                return [
                    'success' => true,
                    'message' => $result['vnp_Message'] ?? 'Hoàn tiền thành công',
                    'response' => $result,
                ];
            }

            Log::warning('=== REFUND FAILED ===', [
                'code'    => $result['vnp_ResponseCode'] ?? 'unknown',
                'message' => $result['vnp_Message'] ?? 'Không có thông báo',
            ]);

            return [
                'success' => false,
                'message' => ($result['vnp_Message'] ?? 'Refund thất bại') . ' - Mã lỗi: ' . ($result['vnp_ResponseCode'] ?? 'unknown'),
                'response' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('=== VNPAY REFUND EXCEPTION ===', [
                'error_message' => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi kết nối tới VNPAY: ' . $e->getMessage(),
            ];
        }
    }
}
