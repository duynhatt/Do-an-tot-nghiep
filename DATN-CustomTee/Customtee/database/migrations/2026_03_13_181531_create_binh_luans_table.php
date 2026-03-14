<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('binh_luans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('san_pham_id')
                ->constrained('san_phams')
                ->cascadeOnDelete();

            $table->text('noi_dung');

            // số sao đánh giá
            $table->integer('so_sao')->default(5);

            // trạng thái duyệt
            $table->boolean('trang_thai')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('binh_luans');
    }
};