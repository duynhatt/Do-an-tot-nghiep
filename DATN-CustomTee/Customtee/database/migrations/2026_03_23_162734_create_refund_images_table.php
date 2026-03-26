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
        Schema::create('refund_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('refund_id')
                  ->constrained('refunds')           
                  ->onDelete('cascade');             

            $table->string('path');                  

            $table->string('original_name')->nullable();

            $table->string('mime_type')->nullable(); 
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();

            $table->index('refund_id');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('refund_images');
    }
};