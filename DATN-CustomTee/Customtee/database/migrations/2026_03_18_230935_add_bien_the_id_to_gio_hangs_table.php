<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gio_hangs', function (Blueprint $table) {
            $table->foreignId('bien_the_id')
                ->nullable()
                ->constrained('bien_thes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('gio_hangs', function (Blueprint $table) {
            $table->dropForeign(['bien_the_id']);
            $table->dropColumn('bien_the_id');
        });
    }
};

?>