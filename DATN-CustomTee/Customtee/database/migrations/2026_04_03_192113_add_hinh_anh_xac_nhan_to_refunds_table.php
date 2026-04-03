<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->string('hinh_anh_xac_nhan')
                  ->nullable()
                  ->after('phuong_thuc_thanh_toan')
                  ->comment('Ảnh xác nhận hoàn tiền (bill chuyển khoản)');
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn('hinh_anh_xac_nhan');
        });
    }
};