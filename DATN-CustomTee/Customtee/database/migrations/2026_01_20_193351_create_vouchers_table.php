<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();

            // Mã voucher
            $table->string('ma')->unique();

            // Tên / mô tả
            $table->string('ten');
            $table->text('mo_ta')->nullable();

            // Loại giảm giá
            $table->enum('loai', ['phan_tram', 'tien_mat']);

            // Giá trị giảm
            $table->decimal('gia_tri', 12, 0);

            // Giảm tối đa (dùng cho %)
            $table->decimal('giam_toi_da', 12, 0)->nullable();

            // Đơn tối thiểu
            $table->decimal('don_hang_toi_thieu', 12, 0)->nullable();

            // Số lượt sử dụng
            $table->integer('so_luong')->nullable();
            $table->integer('da_su_dung')->default(0);

            // Thời gian hiệu lực
            $table->dateTime('bat_dau')->nullable();
            $table->dateTime('ket_thuc')->nullable();

            // Trạng thái
            $table->boolean('trang_thai')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
