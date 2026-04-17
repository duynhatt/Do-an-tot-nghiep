<?php

use App\Models\DonHang;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orders:auto-complete-delivered', function () {
    $this->info('Bắt đầu xử lý tự động đơn "Đã giao"...');

    $autoReceivedCount = 0;
    $autoCompletedCount = 0;

    // Tự xác nhận nhận hàng sau 1 ngày nếu khách chưa thao tác.
    DonHang::where('trang_thai', DonHang::TRANG_THAI_DA_GIAO)
        ->whereNull('da_nhan_hang_at')
        ->whereNotNull('da_giao_at')
        ->where('da_giao_at', '<=', now()->subDay())
        ->where(function ($query) {
            $query->where('yeu_cau_tra', false)->orWhereNull('yeu_cau_tra');
        })
        ->chunkById(100, function ($orders) use (&$autoReceivedCount) {
            foreach ($orders as $order) {
                $autoReceivedAt = $order->da_giao_at
                    ? $order->da_giao_at->copy()->addDay()
                    : now();

                $order->update([
                    'da_nhan_hang_at' => $autoReceivedAt,
                ]);
                $autoReceivedCount++;
            }
        });

    DonHang::where('trang_thai', DonHang::TRANG_THAI_DA_GIAO)
        ->where(function ($query) {
            $query->where('yeu_cau_tra', false)->orWhereNull('yeu_cau_tra');
        })
        // Auto hoàn thành sau 3 ngày kể từ mốc đã giao để đồng bộ chính sách hoàn/trả.
        ->whereNotNull('da_giao_at')
        ->where('da_giao_at', '<=', now()->subDays(3))
        ->chunkById(100, function ($orders) use (&$autoCompletedCount) {
            foreach ($orders as $order) {
                // Đảm bảo tuân thủ state machine
                if (!DonHang::coTheChuyenSang($order->trang_thai, DonHang::TRANG_THAI_DA_HOAN_THANH)) {
                    continue;
                }

                DB::transaction(function () use ($order, &$autoCompletedCount) {
                    $order->update([
                        'trang_thai' => DonHang::TRANG_THAI_DA_HOAN_THANH,
                        // Giữ đồng bộ với luồng khách xác nhận nhận hàng
                        'trang_thai_thanh_toan' => 'da_thanh_toan',
                    ]);

                    $autoCompletedCount++;
                });
            }
        });

    $this->info("Đã tự xác nhận nhận hàng {$autoReceivedCount} đơn (sau 1 ngày).");
    $this->info("Đã tự động hoàn thành {$autoCompletedCount} đơn (sau 3 ngày kể từ lúc đã giao).");
})->purpose('Tự động nhận hàng sau 1 ngày và hoàn thành sau 3 ngày kể từ lúc đã giao');
//php artisan schedule:work
Schedule::command('orders:auto-complete-delivered')->everyMinute();