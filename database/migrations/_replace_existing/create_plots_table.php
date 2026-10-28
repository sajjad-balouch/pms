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

            $table->foreignId('town_id')->constrained('towns')->cascadeOnDelete();

            // Current owner. FK is added later in 2026_10_28_000005 (customers table banne ke baad)
            $table->unsignedBigInteger('customer_id')->nullable()->index();

            $table->string('plot_number', 50);
            $table->string('block_name', 100)->nullable();
            $table->enum('type', ['residential', 'commercial'])->default('residential');
            $table->string('size', 50);

            // Pricing: total_price = base_price + feature premiums (corner, park facing...)
            $table->decimal('rate_per_marla', 12, 2)->nullable();
            $table->decimal('base_price', 14, 2)->nullable();
            $table->json('features')->nullable(); // ["corner","park_facing"]
            $table->decimal('total_price', 14, 2);

            // Default payment plan (booking ke waqt badla ja sakta hai)
            $table->decimal('down_payment', 14, 2)->default(0);
            $table->unsignedSmallInteger('total_installments')->default(36);

            $table->enum('status', ['available', 'booked', 'sold'])->default('available');
            $table->date('booked_at')->nullable();
            $table->date('sold_at')->nullable();

            $table->timestamps();

            // Ek block mein ek plot number sirf ek baar
            $table->unique(['town_id', 'block_name', 'plot_number']);
            $table->index(['town_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
