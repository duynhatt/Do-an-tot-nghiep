<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->string('vnp_TxnRef', 100)
                ->nullable()
                ->after('phuong_thuc_thanh_toan')
                ->comment('Mã giao dịch tham chiếu từ VNPAY (vnp_TxnRef)');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn('vnp_TxnRef');
        });
    }
};
