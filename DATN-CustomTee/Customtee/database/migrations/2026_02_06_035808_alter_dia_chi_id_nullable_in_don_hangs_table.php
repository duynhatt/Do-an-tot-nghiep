<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {

            $table->dropForeign(['dia_chi_id']);

            $table->foreignId('dia_chi_id')
                ->nullable()
                ->change();

            $table->foreign('dia_chi_id')
                ->references('id')
                ->on('dia_chis')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('don_hangs', function (Blueprint $table) {

            $table->dropForeign(['dia_chi_id']);

            $table->foreignId('dia_chi_id')
                ->nullable(false)
                ->change();

            $table->foreign('dia_chi_id')
                ->references('id')
                ->on('dia_chis')
                ->cascadeOnDelete();
        });
    }
};