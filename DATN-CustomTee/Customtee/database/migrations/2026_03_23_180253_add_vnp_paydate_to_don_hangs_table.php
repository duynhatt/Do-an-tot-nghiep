<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->string('vnp_PayDate', 14)->nullable()->after('vnp_TxnRef');
        });
    }

    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {
            $table->dropColumn('vnp_PayDate');
        });
    }
};