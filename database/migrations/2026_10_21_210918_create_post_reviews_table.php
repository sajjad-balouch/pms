<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->string('user_name');
            $table->string('user_email')->nullable();
            $table->tinyInteger('rating')->default(5); // 1 to 5 Stars
            $table->text('comment');
            $table->string('ip_address')->nullable();
            $table->boolean('is_approved')->default(true); // Direct publish or moderation
            $table->timestamps();
        });

        // Posts table mein likes counter add karein agar pehle se nahi hai
        if (!Schema::hasColumn('posts', 'likes_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('likes_count')->default(0)->after('views_count');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reviews');
        if (Schema::hasColumn('posts', 'likes_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('likes_count');
            });
        }
    }
};