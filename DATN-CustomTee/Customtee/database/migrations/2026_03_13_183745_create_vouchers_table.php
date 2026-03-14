<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // Mã voucher (để check trùng mã)
    $table->enum('type', ['fixed', 'percent']); // 2 loại giảm giá
    $table->decimal('discount_value', 15, 2); // Giá trị giảm
    $table->decimal('min_order_value', 15, 2)->default(0); // Đơn tối thiểu
    $table->decimal('max_discount_value', 15, 2)->nullable(); // Giảm tối đa (cho loại %)
    $table->integer('quantity'); // Số lượng
    $table->integer('used_count')->default(0); // Số lượt đã dùng
    $table->date('start_date'); // Ngày bắt đầu
    $table->date('end_date'); // Ngày kết thúc
    $table->boolean('status')->default(1); // 1: Hoạt động, 0: Khóa
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
