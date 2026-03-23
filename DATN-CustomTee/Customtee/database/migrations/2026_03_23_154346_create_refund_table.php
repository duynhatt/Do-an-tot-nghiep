<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('don_hang_id')
                  ->constrained('don_hangs')
                  ->onDelete('cascade'); 

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('trang_thai')
                  ->default('cho_xu_ly')  
                  ->comment('cho_xu_ly, da_chap_nhan, da_tu_choi, da_hoan_tien');

            $table->text('ly_do')->nullable();

            $table->decimal('so_tien_yeu_cau', 15, 0)->nullable()
                  ->comment('Số tiền khách yêu cầu hoàn, tính theo VND');

            $table->string('phuong_thuc_thanh_toan', 155);

            $table->timestamps();
            $table->softDeletes();  
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};