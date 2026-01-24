<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stickers', function (Blueprint $table) {
            $table->id();

            $table->string('ten_sticker');
            $table->string('hinh_anh'); // png / svg
            $table->string('loai')->default('sticker'); 
            // sticker | icon | shape

            $table->boolean('trang_thai')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stickers');
    }
};
