<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('housing_society_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Etihad Garden, DHA, Model Town
            $table->string('city')->index(); // Rahim Yar Khan, Lahore, Multan, etc.
            $table->string('authority'); // LDA, RDA, FDA, MDA, TMA, PHATA
            $table->enum('status', ['approved', 'under_process', 'illegal'])->default('under_process')->index();
            $table->string('noc_number')->nullable(); // e.g. TMA/RYK/NOC-2023-45
            $table->date('approval_date')->nullable();
            $table->string('developer_name')->nullable();
            $table->string('total_sanctioned_area')->nullable(); // e.g. 1200 Kanal
            $table->integer('sanctioned_plots_count')->default(0);
            $table->integer('mortgaged_plots_count')->default(0); // Girvi plots (jo bechna mana hain)
            $table->string('lop_status')->default('Approved LOP'); // Layout Plan
            $table->text('risk_warning')->nullable(); // e.g. "Do not buy plots in Phase 2, land not acquired"
            $table->boolean('is_featured')->default(false); // Paid promotion
            $table->string('website_or_contact')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_society_verifications');
    }
};