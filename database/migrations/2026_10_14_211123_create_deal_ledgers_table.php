<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            
            $table->string('deal_title'); // e.g. 5 Marla Plot Deal - Canal City
            $table->string('client_name');
            $table->string('client_phone');
            $table->date('deal_date');
            
            $table->decimal('deal_value', 14, 2); // Kul property keemat
            $table->decimal('total_commission', 12, 2); // Kul commission
            $table->decimal('received_amount', 12, 2)->default(0);
            $table->decimal('pending_amount', 12, 2)->default(0);
            
            $table->string('partner_name')->nullable(); // Boy ya sub-agent
            $table->decimal('partner_share', 12, 2)->default(0); // Boy ka hissa
            
            $table->enum('status', ['paid', 'partial', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_ledgers');
    }
};