<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bien_thes', function (Blueprint $table) {
            $table->id();

            // Sản phẩm gốc
            $table->foreignId('san_pham_id')
                ->constrained('san_phams')
                ->cascadeOnDelete();

            // Màu sắc
            $table->foreignId('mau_sac_id')
                ->constrained('mau_sacs')
                ->cascadeOnDelete();

            // Kích thước
            $table->foreignId('kich_thuoc_id')
                ->constrained('kich_thuocs')
                ->cascadeOnDelete();

            // Giá theo biến thể 
            $table->decimal('gia', 12, 0)->nullable();
            $table->decimal('gia_khuyen_mai', 12, 0)->nullable();

            // Tồn kho
            $table->integer('so_luong')->default(0);

            // Trạng thái
            $table->boolean('trang_thai')->default(true);

            $table->timestamps();

            // Không cho trùng biến thể
            $table->unique([
                'san_pham_id',
                'mau_sac_id',
                'kich_thuoc_id'
            ], 'unique_bien_the');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bien_thes');
    }
};
