<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Paighamat (WhatsApp / SMS) ka markazi nizam:
 * - messaging_settings: platform ki ek row; kaunsa tareeqa (link / API / SMS) aur us ki settings (encrypted)
 * - message_templates: admin ke badle hue paigham (na hon to code ke default chalte hain)
 * - message_logs: har bheja gaya paigham, owner ke hisaab se (rozana had aur hisaab ke liye)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messaging_settings', function (Blueprint $table) {
            $table->id();
            $table->string('driver', 30)->default('link');
            $table->text('config')->nullable(); // encrypted JSON (token, url waghera)
            $table->unsignedInteger('owner_daily_limit')->default(200);
            $table->timestamps();
        });

        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->text('body');
            $table->string('meta_template', 100)->nullable(); // WhatsApp Cloud API par manzoor shuda template ka naam
            $table->string('meta_language', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('template_key', 50)->nullable();
            $table->string('to', 30);
            $table->string('driver', 30);
            $table->text('body');
            $table->string('status', 20); // sent | failed | limited
            $table->string('provider_id', 191)->nullable();
            $table->text('error')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_logs');
        Schema::dropIfExists('message_templates');
        Schema::dropIfExists('messaging_settings');
    }
};
