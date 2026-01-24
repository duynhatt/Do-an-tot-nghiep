<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('danh_mucs', function (Blueprint $table) {
            $table->id();

            $table->string('ten_danh_muc');
            $table->string('slug')->unique();
            $table->text('mo_ta')->nullable();

            $table->tinyInteger('trang_thai')->default(1);
            // 1: hiển thị, 0: ẩn

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danh_mucs');
    }
};
