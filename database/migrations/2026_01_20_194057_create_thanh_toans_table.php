<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('thanh_toans', function (Blueprint $table) {
            $table->id();

            // Đơn hàng
            $table->foreignId('don_hang_id')
                ->constrained('don_hangs')
                ->cascadeOnDelete();

            // Mã giao dịch (từ cổng thanh toán)
            $table->string('ma_giao_dich')->nullable();

            // Phương thức thanh toán
            $table->enum('phuong_thuc', [
                'cod',
                'vnpay',
                'zalopay',
                'momo'
            ]);

            // Số tiền thanh toán
            $table->decimal('so_tien', 12, 2);

            // Trạng thái thanh toán
            $table->enum('trang_thai', [
                'cho_thanh_toan',
                'da_thanh_toan',
                'that_bai',
                'hoan_tien'
            ])->default('cho_thanh_toan');

            // Thời gian thanh toán thành công
            $table->timestamp('thoi_gian_thanh_toan')->nullable();

            // Dữ liệu trả về từ cổng thanh toán
            $table->json('du_lieu_phan_hoi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanh_toans');
    }
};
