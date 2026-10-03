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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('town_id')->nullable()->constrained('towns')->nullOnDelete(); // optional for individual listings
            
            $table->string('title'); // e.g., "5 Marla House in Gulberg", "2 Acre Agri Land"
            $table->enum('property_type', ['residential', 'commercial', 'agricultural', 'shop', 'house', 'apartment', 'plot']);
            $table->enum('purpose', ['for_sale', 'for_rent']);
            
            $table->decimal('price', 15, 2);
            $table->string('area_size'); // e.g., "5 Marla", "1 Kanal", "2 Acre", "120 Sq Ft"
            $table->string('city');
            $table->string('location'); // Specific address or landmark
            
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // e.g., ['gas', 'electricity', 'water', 'corner']
            $table->json('images')->nullable(); // Property image paths
            
            $table->enum('status', ['available', 'under_offer', 'sold', 'rented'])->default('available');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
