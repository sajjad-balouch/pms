<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_demands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->string('title'); // e.g. ارجنٹ 10 مرلہ کیش خریدار درکار ہے
            $table->enum('property_type', ['house', 'plot', 'commercial', 'agricultural', 'apartment'])->default('plot');
            $table->string('city');
            $table->string('target_location'); // e.g. بحریہ ٹاؤن فیز 8 یا ڈی ایچ اے
            $table->string('area_size'); // e.g. 10 مرلہ
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->enum('urgency', ['urgent', 'standard', 'flexible'])->default('urgent');
            $table->text('details')->nullable();
            $table->enum('status', ['open', 'matched', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_demands');
    }
};