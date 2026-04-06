<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->boolean('hien_thi_trang_chu')->default(false)->after('trang_thai');
        });
    }

    public function down(): void
    {
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->dropColumn('hien_thi_trang_chu');
        });
    }
};
