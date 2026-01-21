<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mau_sacs', function (Blueprint $table) {
            $table->id();
            $table->string('ten_mau');             
            $table->string('ma_mau')->nullable();   // Mã màu: #FF0000
            $table->boolean('trang_thai')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mau_sacs');
    }
};
