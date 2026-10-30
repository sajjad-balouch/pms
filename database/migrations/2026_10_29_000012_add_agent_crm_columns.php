<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agent CRM:
 * - leads.property_id: kis listing se lead aayi (auto-leads from contact unlocks)
 * - properties.views_count / last_viewed_at: listing analytics
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'property_id')) {
                $table->foreignId('property_id')->nullable()->after('plot_id')->constrained('properties')->nullOnDelete();
            }
            $table->index(['agent_id', 'client_phone']);
        });

        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'views_count')) {
                $table->unsignedInteger('views_count')->default(0);
            }
            if (! Schema::hasColumn('properties', 'last_viewed_at')) {
                $table->timestamp('last_viewed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['views_count', 'last_viewed_at']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['agent_id', 'client_phone']);
            $table->dropConstrainedForeignId('property_id');
        });
    }
};
