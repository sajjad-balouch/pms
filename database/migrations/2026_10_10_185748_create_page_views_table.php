<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('url');
            $table->string('page_title')->nullable();
            $table->string('ip_address', 45);
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->date('view_date')->index(); // Daily grouping ke liye
            $table->timestamp('viewed_at')->useCurrent();
            $table->timestamps();

            // Fast analytics query indexing
            $table->index(['url', 'view_date']);
            $table->index(['ip_address', 'view_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};