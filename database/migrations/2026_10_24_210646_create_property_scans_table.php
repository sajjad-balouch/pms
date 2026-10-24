<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_scans', function (Blueprint $table) {
            $table->id();
            $table->uuid('scan_uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('document_type'); // fard, registry, allotment_letter, intiqal
            $table->string('document_file');
            $table->unsignedTinyInteger('risk_score')->default(85); // 0 (Dangerous) - 100 (Clean)
            $table->string('risk_verdict'); // Low Risk / Clean, Moderate Risk, High Risk / Dangerous
            $table->json('extracted_data')->nullable(); // Khasra, Khewat, Names, CNIC, Area, District
            $table->json('audit_flags')->nullable(); // Red flags, Green flags
            $table->boolean('is_paid')->default(false);
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_scans');
    }
};