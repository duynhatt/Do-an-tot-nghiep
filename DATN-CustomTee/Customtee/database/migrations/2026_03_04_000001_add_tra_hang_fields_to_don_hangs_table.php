<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->boolean('yeu_cau_tra')
                ->default(false)
                ->after('trang_thai');

            $table->text('ly_do_tra')
                ->nullable()
                ->after('yeu_cau_tra');

            $table->timestamp('ngay_yeu_cau_tra')
                ->nullable()
                ->after('ly_do_tra');
        });
    }

    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn([
                'yeu_cau_tra',
                'ly_do_tra',
                'ngay_yeu_cau_tra',
            ]);
        });
    }
};

