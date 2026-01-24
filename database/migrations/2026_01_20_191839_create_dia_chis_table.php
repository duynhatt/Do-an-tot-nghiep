<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dia_chis', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->string('ten_nguoi_nhan');
            $table->string('so_dien_thoai', 20);

            $table->string('tinh_thanh');
            $table->string('quan_huyen');
            $table->string('phuong_xa');

            $table->text('dia_chi_chi_tiet');

            $table->boolean('mac_dinh')->default(false);

            $table->text('ghi_chu')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dia_chis');
    }
};
