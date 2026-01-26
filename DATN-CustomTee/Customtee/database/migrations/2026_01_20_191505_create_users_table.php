<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // trước đây là 'ten'
            $table->string('email')->unique();
            $table->string('password'); // trước đây là 'mat_khau'
            $table->string('phone', 20)->nullable(); // trước đây 'so_dien_thoai'

            $table->enum('role', ['admin', 'client'])->default('client'); // 'vai_tro'

            $table->tinyInteger('status')->default(1); // 'trang_thai'
            // 1: hoạt động, 0: khóa

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
