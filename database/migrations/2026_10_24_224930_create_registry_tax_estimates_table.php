<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registry_tax_estimates', function (Blueprint $table) {$table->id();
            $table->uuid('estimate_uuid')->unique();$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('city');$table->string('property_type'); // residential, commercial, agriculture
            $table->string('area_unit'); // marla, kanal, sqft$table->decimal('area_size', 10, 2);
            $table->decimal('rate_per_unit', 15, 2);$table->decimal('total_declared_value', 15, 2);
            $table->boolean('buyer_is_filer')->default(true);$table->boolean('seller_is_filer')->default(true);
            $table->json('calculated_breakdown'); // Stamp duty, TMA, PLRA, 236K, 236C, etc.$table->decimal('grand_total_taxes', 15, 2);
            $table->string('ip_address')->nullable();$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registry_tax_estimates');
    }
};