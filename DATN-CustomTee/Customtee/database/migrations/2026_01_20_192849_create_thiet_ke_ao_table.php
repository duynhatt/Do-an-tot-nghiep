<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('thiet_ke_ao', function (Blueprint $table) {
            $table->id();

            $table->foreignId('nguoi_dung_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('mau_sac_id')
                ->nullable()
                ->constrained('mau_sacs')
                ->nullOnDelete();

            $table->foreignId('kich_thuoc_id')
                ->nullable()
                ->constrained('kich_thuocs')
                ->nullOnDelete();

            $table->string('ten_thiet_ke')->nullable();

            $table->longText('du_lieu_canvas');
            // JSON canvas (Fabric.js / Konva.js)

            $table->string('hinh_xem_truoc')->nullable();
            // ảnh render preview

            $table->boolean('trang_thai')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thiet_ke_ao');
    }
};
