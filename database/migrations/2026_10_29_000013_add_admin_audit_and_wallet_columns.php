<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Admin panel:
 * - admin_logs: who did what (top-up approve/reject, wallet adjustment, block, delete, settings)
 * - wallet_transactions.processed_by + 'adjustment' type (manual credit/debit by admin)
 * - system_settings.require_provider_approval (new town owners / agents wait for approval)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_logs')) {
            Schema::create('admin_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 60);
                $table->string('target_type', 60)->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
                $table->string('summary', 500)->nullable();
                $table->json('details')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['target_type', 'target_id']);
                $table->index('created_at');
            });
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('wallet_transactions', 'processed_by')) {
                $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE wallet_transactions MODIFY type ENUM('deposit', 'unlock_fee', 'refund', 'adjustment') NOT NULL");
        }

        Schema::table('system_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('system_settings', 'require_provider_approval')) {
                $table->boolean('require_provider_approval')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('require_provider_approval');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::table('wallet_transactions')->where('type', 'adjustment')->update(['type' => 'refund']);
            DB::statement("ALTER TABLE wallet_transactions MODIFY type ENUM('deposit', 'unlock_fee', 'refund') NOT NULL");
        }

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('processed_by');
        });

        Schema::dropIfExists('admin_logs');
    }
};
