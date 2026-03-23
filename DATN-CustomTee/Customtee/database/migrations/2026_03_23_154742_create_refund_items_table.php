<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('refund_request_id')
                  ->constrained('refunds')
                  ->onDelete('cascade');

            $table->foreignId('chi_tiet_don_hang_id')
                  ->constrained('don_hang_chi_tiets')
                  ->onDelete('cascade');

            $table->unsignedInteger('so_luong_yeu_cau');

            $table->decimal('thanh_tien_yeu_cau', 15, 0)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_items');
    }
};