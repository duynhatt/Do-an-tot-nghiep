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
        Schema::table('refunds', function (Blueprint $table) {
            $table->string('ngan_hang')->nullable();
            $table->string('so_tai_khoan')->nullable();
            $table->string('chi_nhanh')->nullable();
            $table->string('ten_chu_tk')->nullable();

            $table->string('qr_code_bank')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn([
                'ngan_hang',
                'so_tai_khoan',
                'chi_nhanh',
                'ten_chu_tk',
                'ten_chu_tk_from_image',
            ]);
        });
    }
};