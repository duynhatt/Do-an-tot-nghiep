<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('yeu_thichs', function (Blueprint $table) {
            $table->id();

            // Người dùng
            $table->foreignId('nguoi_dung_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Sản phẩm được yêu thích
            $table->foreignId('san_pham_id')
                ->constrained('san_phams')
                ->cascadeOnDelete();

            $table->timestamps();

            // Không cho 1 người thích 1 sản phẩm nhiều lần
            $table->unique(
                ['nguoi_dung_id', 'san_pham_id'],
                'unique_nguoi_dung_san_pham'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yeu_thichs');
    }
};
