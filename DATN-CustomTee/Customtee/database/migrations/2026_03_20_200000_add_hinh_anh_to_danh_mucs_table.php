<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('danh_mucs', function (Blueprint $table) {
            $table->string('hinh_anh')->nullable()->after('mo_ta');
        });
    }

    public function down(): void
    {
        Schema::table('danh_mucs', function (Blueprint $table) {
            $table->dropColumn('hinh_anh');
        });
    }
};

