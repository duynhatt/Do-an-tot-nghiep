<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kich_thuocs', function (Blueprint $table) {
            $table->id();
            $table->string('ten_kich_thuoc');        // S, M, L, XL hoặc 38, 39
            $table->boolean('trang_thai')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kich_thuocs');
    }
};
