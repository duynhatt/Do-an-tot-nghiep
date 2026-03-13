<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->foreignId('don_hang_id')
                ->after('san_pham_id')
                ->constrained('don_hangs')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->dropColumn('don_hang_id');
        });
    }
};
