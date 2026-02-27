<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->text('dia_chi_chi_tiet')
                ->nullable()
                ->after('dia_chi_id');

            $table->string('so_dien_thoai_nhan_hang')
                ->nullable()
                ->after('dia_chi_chi_tiet');

            $table->string('ten_nguoi_nhan')
                ->nullable()
                ->after('so_dien_thoai_nhan_hang');
        });
    }

    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn([
                'dia_chi_chi_tiet',
                'so_dien_thoai_nhan_hang',
                'ten_nguoi_nhan'
            ]);
        });
    }
};