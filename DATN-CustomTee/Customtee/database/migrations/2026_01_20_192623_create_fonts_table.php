<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fonts', function (Blueprint $table) {
            $table->id();

            $table->string('ten_font');
            $table->string('file_font'); // .ttf, .woff
            $table->boolean('trang_thai')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fonts');
    }
};
