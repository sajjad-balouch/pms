<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plot_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plot_booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_customer_id')->nullable()->constrained('customers')->restrictOnDelete();
            $table->foreignId('to_customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('transfer_date');
            $table->decimal('transfer_fee', 14, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plot_transfers');
    }
};
