<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_agreements', function (Blueprint $table) {$table->id();
            $table->uuid('uuid')->unique();$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Landlord (مالک مکان / دکان)
            $table->string('landlord_name');
            $table->string('landlord_father');$table->string('landlord_cnic');
            $table->string('landlord_phone');$table->text('landlord_address');

            // Tenant (کرایہ دار)
            $table->string('tenant_name');
            $table->string('tenant_father');$table->string('tenant_cnic');
            $table->string('tenant_phone');$table->text('tenant_permanent_address');

            // Property Particulars
            $table->enum('property_type', ['residential', 'commercial', 'portion', 'warehouse'])->default('residential');$table->string('property_title'); // e.g. House No 12, Main Bazar Shop # 4
            $table->string('city');$table->text('property_address');

            // Financial & Tenancy Terms
            $table->decimal('monthly_rent', 12, 2);$table->decimal('security_deposit', 12, 2)->default(0); // ضمانت / سیکیورٹی
            $table->integer('annual_increment_percentage')->default(10); // 10% سالانہ اضافہ
            $table->integer('tenancy_tenure_months')->default(11); // 11 Months standard
            $table->date('start_date');$table->date('end_date');
            $table->integer('rent_due_day')->default(5); // 5th of every month$table->string('payment_method')->default('cash'); // cash, bank_transfer, cheque

            // Utility Meters & Sub-meters
            $table->string('electricity_meter_no')->nullable();
            $table->string('gas_meter_no')->nullable();$table->string('current_electricity_reading')->nullable();

            // Witnesses (گواہان)
            $table->string('witness1_name')->nullable();$table->string('witness1_cnic')->nullable();
            $table->string('witness2_name')->nullable();$table->string('witness2_cnic')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_agreements');
    }
};