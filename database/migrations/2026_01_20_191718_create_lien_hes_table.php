<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lien_hes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            // Có thể null nếu khách vãng lai

            $table->string('tieu_de');
            $table->text('noi_dung');

            $table->enum('trang_thai', ['moi', 'da_xu_ly'])->default('moi');
            // moi: chưa xử lý, da_xu_ly: đã phản hồi

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lien_hes');
    }
};
