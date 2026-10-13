<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->enum('priority', ['hot', 'warm', 'cold'])->default('warm')->after('status');
            $table->enum('source', ['portal', 'whatsapp', 'call', 'walk_in', 'social_media', 'banner'])->default('portal')->after('priority');
            $table->dateTime('next_followup_at')->nullable()->after('source');
            $table->timestamp('last_whatsapp_at')->nullable()->after('next_followup_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['priority', 'source', 'next_followup_at', 'last_whatsapp_at']);
        });
    }
};