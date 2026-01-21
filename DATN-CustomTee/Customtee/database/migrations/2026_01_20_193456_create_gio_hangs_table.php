<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gio_hangs', function (Blueprint $table) {
            $table->id();

            // Người dùng sở hữu giỏ
            $table->foreignId('nguoi_dung_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Sản phẩm
            $table->foreignId('san_pham_id')
                ->constrained('san_phams')
                ->cascadeOnDelete();

            // Thiết kế áo (nếu người dùng tự thiết kế)
            $table->foreignId('thiet_ke_ao_id')
                ->nullable()
                ->constrained('thiet_ke_ao')
                ->nullOnDelete();

            // Số lượng
            $table->integer('so_luong')->default(1);

            // Giá tại thời điểm thêm vào giỏ
            $table->decimal('don_gia', 12, 0);

            // Thành tiền
            $table->decimal('thanh_tien', 12, 0);

            // Trạng thái dòng giỏ
            $table->enum('trang_thai', ['dang_trong_gio', 'da_dat_hang'])
                ->default('dang_trong_gio');

            $table->timestamps();

            // Một sản phẩm + 1 thiết kế chỉ tồn tại 1 lần trong giỏ
            $table->unique([
                'nguoi_dung_id',
                'san_pham_id',
                'thiet_ke_ao_id',
                'trang_thai'
            ], 'gio_hang_unique_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gio_hangs');
    }
};
