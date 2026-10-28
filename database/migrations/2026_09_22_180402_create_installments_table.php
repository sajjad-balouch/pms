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

            $table->foreignId('plot_id')->constrained('plots')->cascadeOnDelete();

            // Kis booking ki qist hai. FK is added later in 2026_10_28_000005 (plot_bookings table banne ke baad)
            $table->unsignedBigInteger('plot_booking_id')->nullable()->index();

            // Booking ke waqt customer se copy hota hai (receipt / WhatsApp reminder ke liye)
            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone', 20)->nullable();

            $table->unsignedSmallInteger('installment_number'); // 0 = down payment, 1, 2, 3...
            $table->decimal('amount', 14, 2);
            $table->date('due_date');               // قسط آنے کی تاریخ
            $table->date('paid_date')->nullable();  // قسط جمع ہونے کی تاریخ
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Dashboard ke overdue / due this month queries ke liye
            $table->index(['plot_id', 'status']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
