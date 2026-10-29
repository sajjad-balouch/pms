<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Do kaam karti hai:
 * 1. Purane database (jahan plots/installments pehle ban chuke the) mein naye columns jodti hai — data safe rehta hai.
 *    Fresh database (updated create migrations) mein columns pehle se hote hain, to ye step skip ho jata hai.
 * 2. customers / plot_bookings ke foreign keys lagati hai.
 */
return new class extends Migration
{
    public function up(): void
    {
        $plotColumns = Schema::getColumnListing('plots');

        Schema::table('plots', function (Blueprint $table) use ($plotColumns) {
            if (! in_array('customer_id', $plotColumns)) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('town_id')->index();
            }
            if (! in_array('rate_per_marla', $plotColumns)) {
                $table->decimal('rate_per_marla', 12, 2)->nullable()->after('size');
            }
            if (! in_array('base_price', $plotColumns)) {
                $table->decimal('base_price', 14, 2)->nullable()->after('rate_per_marla');
            }
            if (! in_array('features', $plotColumns)) {
                $table->json('features')->nullable()->after('base_price');
            }
            if (! in_array('booked_at', $plotColumns)) {
                $table->date('booked_at')->nullable()->after('status');
            }
            if (! in_array('sold_at', $plotColumns)) {
                $table->date('sold_at')->nullable()->after('booked_at');
            }
        });

        if (! Schema::hasColumn('installments', 'plot_booking_id')) {
            Schema::table('installments', function (Blueprint $table) {
                $table->unsignedBigInteger('plot_booking_id')->nullable()->after('plot_id')->index();
            });
        }

        // Foreign key pehle se ho (dobara chalne par) to skip
        if (! $this->hasForeignKey('plots', 'customer_id')) {
            Schema::table('plots', function (Blueprint $table) {
                $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            });
        }

        if (! $this->hasForeignKey('installments', 'plot_booking_id')) {
            Schema::table('installments', function (Blueprint $table) {
                $table->foreign('plot_booking_id')->references('id')->on('plot_bookings')->nullOnDelete();
            });
        }
    }

    // Simple KEY_COLUMN_USAGE lookup — Schema::getForeignKeys() joins referential_constraints,
    // which crashes MariaDB 10.4 (XAMPP) on this server.
    private function hasForeignKey(string $table, string $column): bool
    {
        return (bool) DB::selectOne(
            'SELECT 1 FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL
             LIMIT 1',
            [$table, $column]
        );
    }

    public function down(): void
    {
        // Sirf foreign keys hatate hain; columns create-migrations ka hissa bhi ho sakte hain
        Schema::table('installments', function (Blueprint $table) {
            $table->dropForeign(['plot_booking_id']);
        });

        Schema::table('plots', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
    }
};
