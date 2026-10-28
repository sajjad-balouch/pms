<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bill / receipt ki tasveer ya PDF, aur date filter ke liye index.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('town_expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('town_expenses', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('details');
            }

            $table->index(['town_id', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::table('town_expenses', function (Blueprint $table) {
            $table->dropIndex(['town_id', 'expense_date']);

            if (Schema::hasColumn('town_expenses', 'receipt_path')) {
                $table->dropColumn('receipt_path');
            }
        });
    }
};
