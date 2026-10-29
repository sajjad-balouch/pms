<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plot_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->restrictOnDelete();
            $table->date('booking_date');

            $table->decimal('total_price', 14, 2);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('final_price', 14, 2);
            $table->decimal('down_payment', 14, 2)->default(0);
            $table->unsignedSmallInteger('installments_count')->default(0);
            $table->decimal('installment_amount', 14, 2)->default(0);
            $table->unsignedTinyInteger('interval_months')->default(1);
            $table->date('first_due_date')->nullable();

            $table->string('status', 20)->default('active'); // active | cancelled
            $table->date('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->decimal('refund_amount', 14, 2)->default(0);
            $table->date('refund_date')->nullable();

            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['plot_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plot_bookings');
    }
};
