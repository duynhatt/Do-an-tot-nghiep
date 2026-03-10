<?php

use App\Models\DonHang;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lệnh auto hoàn thành đơn hàng sau 3 ngày ở trạng thái "Đã giao"
Artisan::command('orders:auto-complete-delivered', function () {
    $this->info('Bắt đầu auto hoàn thành đơn "Đã giao" đủ 3 ngày...');

    $count = 0;

    DonHang::where('trang_thai', DonHang::TRANG_THAI_DA_GIAO)
        ->where('yeu_cau_tra', false)
        // Đã ở trạng thái "Đã giao" ít nhất 3 ngày (dựa trên updated_at khi chuyển sang "Đã giao")
        ->where('updated_at', '<=', now()->subDays(3))
        ->chunkById(100, function ($orders) use (&$count) {
            foreach ($orders as $order) {
                // Đảm bảo tuân thủ state machine
                if (!DonHang::coTheChuyenSang($order->trang_thai, DonHang::TRANG_THAI_DA_HOAN_THANH)) {
                    continue;
                }

                DB::transaction(function () use ($order, &$count) {
                    $order->update([
                        'trang_thai' => DonHang::TRANG_THAI_DA_HOAN_THANH,
                        // Giữ đồng bộ với luồng khách xác nhận nhận hàng
                        'trang_thai_thanh_toan' => 'da_thanh_toan',
                    ]);

                    $count++;
                });
            }
        });

    $this->info("Đã tự động hoàn thành {$count} đơn hàng.");
})->purpose('Tự động chuyển các đơn \"Đã giao\" sang \"Đã hoàn thành\" sau 3 ngày');