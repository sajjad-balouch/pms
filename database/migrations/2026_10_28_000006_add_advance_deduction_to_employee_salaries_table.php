<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Salary dete waqt advance mein se kati hui raqam.
 * amount = haath mein di gayi raqam (cash), advance_deduction = advance se adjust hui raqam.
 * Advance balance = SUM(advance amounts) - SUM(advance_deduction)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('employee_salaries', 'advance_deduction')) {
            Schema::table('employee_salaries', function (Blueprint $table) {
                $table->decimal('advance_deduction', 12, 2)->default(0)->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('employee_salaries', 'advance_deduction')) {
            Schema::table('employee_salaries', function (Blueprint $table) {
                $table->dropColumn('advance_deduction');
            });
        }
    }
};
