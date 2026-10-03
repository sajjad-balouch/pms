<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unlocked_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Payment karne wala user
            $table->foreignId('unlocked_user_id')->constrained('users')->onDelete('cascade'); // Agent ya Town Owner
            $table->bigint('plot_id'); // Agent ya Town Owner
            $table->timestamp('expires_at'); // 1 month baad ki date
            $table->timestamps();

            // Duplicate entries se bachne ke liye index
            $table->unique(['user_id', 'unlocked_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlocked_contacts');
    }
};