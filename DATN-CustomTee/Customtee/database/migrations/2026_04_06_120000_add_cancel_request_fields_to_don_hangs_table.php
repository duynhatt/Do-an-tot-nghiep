<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE don_hangs
            MODIFY COLUMN trang_thai ENUM(
                'cho_xac_nhan',
                'dang_xu_ly',
                'cho_duyet_huy',
                'dang_giao',
                'da_giao',
                'da_hoan_thanh',
                'da_huy'
            ) DEFAULT 'cho_xac_nhan'
        ");

        Schema::table('don_hangs', function (Blueprint $table) {
            $table->boolean('yeu_cau_huy')
                ->default(false)
                ->after('trang_thai');

            $table->timestamp('ngay_yeu_cau_huy')
                ->nullable()
                ->after('yeu_cau_huy');

            $table->text('ly_do_tu_choi_huy')
                ->nullable()
                ->after('ngay_yeu_cau_huy');
        });
    }

    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn([
                'yeu_cau_huy',
                'ngay_yeu_cau_huy',
                'ly_do_tu_choi_huy',
            ]);
        });

        // Tránh lỗi enum khi rollback.
        DB::statement("UPDATE don_hangs SET trang_thai = 'dang_xu_ly' WHERE trang_thai = 'cho_duyet_huy'");

        DB::statement("
            ALTER TABLE don_hangs
            MODIFY COLUMN trang_thai ENUM(
                'cho_xac_nhan',
                'dang_xu_ly',
                'dang_giao',
                'da_giao',
                'da_hoan_thanh',
                'da_huy'
            ) DEFAULT 'cho_xac_nhan'
        ");
    }
};

