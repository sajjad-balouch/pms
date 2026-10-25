<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inheritance_calculations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('deceased_gender'); // male (مرد), female (عورت)
            $table->decimal('total_land_marlas', 12, 3)->default(0);
            $table->decimal('cash_amount', 15, 2)->default(0);
            $table->decimal('debts_and_funeral', 15, 2)->default(0); // Janaza aur Qarz ki kataoti
            $table->decimal('net_distributable_cash', 15, 2)->default(0);
            
            // Heirs Counts
            $table->integer('wives_count')->default(0);
            $table->boolean('has_husband')->default(false);
            $table->boolean('has_father')->default(false);
            $table->boolean('has_mother')->default(false);
            $table->integer('sons_count')->default(0);
            $table->integer('daughters_count')->default(0);
            
            $table->json('calculated_shares'); // Detailed inheritance breakdown per person
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inheritance_calculations');
    }
};