<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('lead_unlock_fee_agent', 8, 2)->default(50.00); // Fee to unlock agent details
            $table->decimal('lead_unlock_fee_town', 8, 2)->default(100.00); // Fee to unlock town owner details
            $table->decimal('town_owner_monthly_fee', 8, 2)->default(5000.00); // Monthly subscription after free 1st month
            $table->decimal('agent_monthly_fee', 8, 2)->default(2000.00); // Monthly subscription after free 1st month
            $table->integer('user_free_trial_days')->default(3); // General user trial period
            $table->integer('provider_free_months')->default(1); // Town owner / Agent free trial months
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};