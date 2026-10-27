<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zakat_calculations', function (Blueprint $table) {$table->id();
            $table->uuid('uuid')->unique();$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('currency')->default('PKR');$table->string('nisab_standard')->default('silver'); // 'silver' (recommended for public benefit) or 'gold'
            
            // Metals
            $table->decimal('gold_value', 15, 2)->default(0);$table->decimal('silver_value', 15, 2)->default(0);

            // Cash & Bank
            $table->decimal('cash_in_hand', 15, 2)->default(0);
            $table->decimal('bank_balances', 15, 2)->default(0);$table->decimal('foreign_currency_value', 15, 2)->default(0);

            // Business & Real Estate
            $table->decimal('real_estate_trading_value', 15, 2)->default(0); // Plots/Files for sale$table->decimal('rental_income_saved', 15, 2)->default(0); // Accumulated rental cash
            $table->decimal('business_stock_value', 15, 2)->default(0); // Maal-e-Tijarat$table->decimal('receivables_good_debt', 15, 2)->default(0); // Qarz-e-Hasana jo wapis aana ho

            // Deductions / Liabilities
            $table->decimal('immediate_debts_payable', 15, 2)->default(0); // Qarz wajibul ada$table->decimal('due_utility_bills_expenses', 15, 2)->default(0);

            // Totals
            $table->decimal('total_assets', 15, 2)->default(0);
            $table->decimal('total_liabilities', 15, 2)->default(0);$table->decimal('net_zakatable_wealth', 15, 2)->default(0);
            $table->decimal('calculated_zakat', 15, 2)->default(0);$table->boolean('is_eligible_nisab')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zakat_calculations');
    }
};