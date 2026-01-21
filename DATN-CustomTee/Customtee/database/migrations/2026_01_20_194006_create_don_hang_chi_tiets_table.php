<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('don_hang_chi_tiets', function (Blueprint $table) {
            $table->id();

            // Đơn hàng
            $table->foreignId('don_hang_id')
                ->constrained('don_hangs')
                ->cascadeOnDelete();

            // Sản phẩm (áo gốc)
            $table->foreignId('san_pham_id')
                ->constrained('san_phams')
                ->cascadeOnDelete();

            // Biến thể (màu + size)
            $table->foreignId('bien_the_id')
                ->constrained('bien_thes')
                ->cascadeOnDelete();

            // File thiết kế người dùng upload
            $table->string('file_thiet_ke')
                ->nullable();

            // Dữ liệu thiết kế (JSON: vị trí, scale, text…)
            $table->json('du_lieu_thiet_ke')
                ->nullable();

            // Giá tại thời điểm mua
            $table->decimal('don_gia', 12, 2);

            // Số lượng
            $table->integer('so_luong');

            // Thành tiền = đơn giá * số lượng
            $table->decimal('thanh_tien', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('don_hang_chi_tiets');
    }
};
