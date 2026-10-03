<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('plot_id');
            $table->foreign('plot_id')->references('id')->on('plots')->onDelete('cascade');

            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone')->nullable();
            
            $table->integer('installment_number'); // e.g. 1, 2, 3...
            $table->decimal('amount', 12, 2);
            $table->date('due_date'); // قسط آنے کی تاریخ
            $table->date('paid_date')->nullable(); // قسط جمع ہونے کی تاریخ
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};