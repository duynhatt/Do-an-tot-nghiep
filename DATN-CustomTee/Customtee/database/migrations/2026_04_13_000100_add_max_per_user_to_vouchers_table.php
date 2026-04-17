<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_per_user')
                ->nullable()
                ->after('so_luong')
                ->comment('So lan toi da moi tai khoan duoc dung voucher');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('max_per_user');
        });
    }
};
