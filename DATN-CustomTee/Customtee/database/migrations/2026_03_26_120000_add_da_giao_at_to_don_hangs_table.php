<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->timestamp('da_giao_at')->nullable()->after('ngay_yeu_cau_tra');
        });

        // Backfill cho dữ liệu cũ: đơn đang "Đã giao" lấy mốc hiện có để không bị kẹt auto-complete.
        DB::table('don_hangs')
            ->where('trang_thai', 'da_giao')
            ->whereNull('da_giao_at')
            ->update(['da_giao_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn('da_giao_at');
        });
    }
};
