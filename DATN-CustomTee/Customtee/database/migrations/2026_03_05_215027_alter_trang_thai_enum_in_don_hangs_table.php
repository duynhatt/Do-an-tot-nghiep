<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void
    {
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
    public function down(): void
    {
        DB::statement("
            ALTER TABLE don_hangs
            MODIFY COLUMN trang_thai ENUM(
                'cho_xac_nhan',
                'dang_xu_ly',
                'dang_giao',
                'da_giao',
                'da_huy'
            ) DEFAULT 'cho_xac_nhan'
        ");
    }
};
