<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Personal Information
            $table->string('full_name');
            $table->string('job_title');
            $table->string('email');
            $table->string('phone');
            $table->string('city');
            $table->string('linkedin_url')->nullable();
            $table->text('summary')->nullable();

            // Dynamic Sections (JSON arrays)
            $table->json('experiences')->nullable(); // Title, Company, Dates, Description
            $table->json('educations')->nullable();  // Degree, Institute, Year
            $table->json('skills')->nullable();      // Array of skill tags
            $table->json('languages')->nullable();   // Languages with proficiency

            $table->string('theme_color')->default('#0284c7'); // Sky blue, Emerald, Amber, Slate
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};