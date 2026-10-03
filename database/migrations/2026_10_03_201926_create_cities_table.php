<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('province')->default('Punjab'); // Punjab, Sindh, KPK, Balochistan, Islamabad, GB, AJK
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Top cities like Lahore, Karachi, Islamabad
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            // Indexes for fast querying
            $table->index('province');
            $table->index('is_active');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};