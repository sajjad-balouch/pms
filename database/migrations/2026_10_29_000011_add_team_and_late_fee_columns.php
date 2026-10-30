<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - users.owner_id / permissions: town owner ki team (recovery officer, accountant, sales)
 * - installments.received_by / late_fee: kis ne wusooli ki, aur late fee
 * - towns.late_fee_percent: har mahine late hone par kitna % surcharge (suggestion)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->after('role')->constrained('users')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('owner_id');
            }
        });

        Schema::table('installments', function (Blueprint $table) {
            if (! Schema::hasColumn('installments', 'late_fee')) {
                $table->decimal('late_fee', 12, 2)->default(0)->after('amount');
            }
            if (! Schema::hasColumn('installments', 'received_by')) {
                $table->foreignId('received_by')->nullable()->after('paid_date')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('towns', function (Blueprint $table) {
            if (! Schema::hasColumn('towns', 'late_fee_percent')) {
                $table->decimal('late_fee_percent', 5, 2)->default(0)->after('noc_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('towns', function (Blueprint $table) {
            $table->dropColumn('late_fee_percent');
        });

        Schema::table('installments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('received_by');
            $table->dropColumn('late_fee');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
            $table->dropColumn('permissions');
        });
    }
};
