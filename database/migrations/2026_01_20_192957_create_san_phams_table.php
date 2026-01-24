<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('san_phams', function (Blueprint $table) {
            $table->id();

            // Danh mục sản phẩm
            $table->foreignId('danh_muc_id')
                ->constrained('danh_mucs')
                ->cascadeOnDelete();

            // Thông tin cơ bản
            $table->string('ten_san_pham');
            $table->string('slug')->unique();

            $table->text('mo_ta_ngan')->nullable();
            $table->longText('mo_ta_chi_tiet')->nullable();

            // Giá
            $table->decimal('gia', 12, 0);
            $table->decimal('gia_khuyen_mai', 12, 0)->nullable();

            // Hình ảnh
            $table->string('hinh_anh_chinh')->nullable();

            // Cho phép thiết kế áo
            $table->boolean('cho_phep_thiet_ke')->default(false);

            // Trạng thái
            $table->boolean('trang_thai')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('san_phams');
    }
};
