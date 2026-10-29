<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Buyer portal ka secret link (har booking ka alag). Transfer par naya token banta hai,
 * cancel par khatam ho jata hai — purana link khud band.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('plot_bookings', 'portal_token')) {
            Schema::table('plot_bookings', function (Blueprint $table) {
                $table->string('portal_token', 64)->nullable()->unique()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('plot_bookings', 'portal_token')) {
            Schema::table('plot_bookings', function (Blueprint $table) {
                $table->dropUnique(['portal_token']);
                $table->dropColumn('portal_token');
            });
        }
    }
};
