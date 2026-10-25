<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bayana_deeds', function (Blueprint $table) {$table->id();
            $table->uuid('deed_uuid')->unique();$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Fariq Awal (Seller / Farokht-kuninda)
            $table->string('seller_name');
            $table->string('seller_father');$table->string('seller_cnic');
            $table->string('seller_phone')->nullable();$table->text('seller_address');

            // Fariq Doem (Buyer / Khareedar)
            $table->string('buyer_name');
            $table->string('buyer_father');$table->string('buyer_cnic');
            $table->string('buyer_phone')->nullable();$table->text('buyer_address');

            // Property Particulars
            $table->string('property_title'); // Plot / Makan / Zameen
            $table->string('city');$table->string('location_mouza');
            $table->string('khasra_khewat')->nullable();$table->string('area_size'); // e.g. 5 Marla / 2 Kanal

            // Financial & Dates
            $table->decimal('total_amount', 15, 2);
            $table->decimal('bayana_advance_amount', 15, 2);$table->decimal('remaining_amount', 15, 2);
            $table->string('payment_method')->default('cash'); // cash, pay_order, cheque, online$table->string('payment_reference')->nullable(); // Cheque #
            $table->date('deed_date');$table->date('target_registry_date'); // Aakhri tareekh

            // Witnesses (Gawaahan)
            $table->string('witness1_name')->nullable();$table->string('witness1_cnic')->nullable();
            $table->string('witness2_name')->nullable();$table->string('witness2_cnic')->nullable();

            $table->string('ip_address')->nullable();$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bayana_deeds');
    }
};