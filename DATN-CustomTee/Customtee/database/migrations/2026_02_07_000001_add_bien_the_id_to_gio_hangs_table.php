<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gio_hangs', function (Blueprint $table) {
            $table->foreignId('bien_the_id')
                ->nullable()
                ->after('san_pham_id')
                ->constrained('bien_thes')
                ->cascadeOnDelete();
        });

        Schema::table('gio_hangs', function (Blueprint $table) {
            $table->dropUnique('gio_hang_unique_item');
        });

        Schema::table('gio_hangs', function (Blueprint $table) {
            $table->unique(
                ['nguoi_dung_id', 'san_pham_id', 'bien_the_id', 'trang_thai'],
                'gio_hang_unique_user_product_variant'
            );
        });
    }

    public function down(): void
    {
        Schema::table('gio_hangs', function (Blueprint $table) {
            $table->dropUnique('gio_hang_unique_user_product_variant');
        });

        Schema::table('gio_hangs', function (Blueprint $table) {
            $table->unique(
                ['nguoi_dung_id', 'san_pham_id', 'thiet_ke_ao_id', 'trang_thai'],
                'gio_hang_unique_item'
            );
        });

        Schema::table('gio_hangs', function (Blueprint $table) {
            $table->dropForeign(['bien_the_id']);
        });
    }
};
