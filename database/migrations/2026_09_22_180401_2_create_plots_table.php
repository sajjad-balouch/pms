<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            
            // Unsigned Big Integer explicitly matching Towns table ID
            $table->unsignedBigInteger('town_id');
            $table->foreign('town_id')->references('id')->on('towns')->onDelete('cascade');

            $table->string('plot_number');
            $table->string('block_name')->nullable();
            $table->enum('type', ['residential', 'commercial'])->default('residential');
            $table->string('size');
            $table->decimal('total_price', 12, 2);
            $table->decimal('down_payment', 12, 2);
            $table->integer('total_installments')->default(36);
            $table->enum('status', ['available', 'booked', 'sold'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};