<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->text('ly_do_yeu_cau_huy')
                ->nullable()
                ->after('ngay_yeu_cau_huy');
        });
    }

    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn('ly_do_yeu_cau_huy');
        });
    }
};
