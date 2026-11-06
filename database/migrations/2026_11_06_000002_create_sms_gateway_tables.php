<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Apna SMS gateway: platform ke apne Android phone (SIM) se SMS jate hain, aur har user ke wallet se paise katte hain.
 * - gateway_devices: admin ke phones (har phone ka apna token; token sirf hash ki shakal mein)
 * - message_logs: queue bhi yahi hai (status queued -> sending -> sent/failed), kis phone ne bheja, kitne paise kate
 * - wallet_transactions.type mein 'message_fee'
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateway_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('token_hash', 64)->unique();
            $table->string('sim_number', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('daily_limit')->default(300); // telecom SIM block na kare
            $table->timestamp('last_seen_at')->nullable();
            $table->json('last_status')->nullable(); // battery, signal, app version
            $table->timestamps();
        });

        Schema::table('message_logs', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('driver')->constrained('gateway_devices')->nullOnDelete();
            $table->unsignedTinyInteger('segments')->default(0)->after('body');
            $table->decimal('cost', 10, 2)->default(0)->after('segments');
            $table->boolean('refunded')->default(false)->after('cost');
            $table->unsignedTinyInteger('attempts')->default(0)->after('refunded');
            $table->timestamp('claimed_at')->nullable()->after('attempts');
            $table->timestamp('sent_at')->nullable()->after('claimed_at');

            $table->index(['status', 'id']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE wallet_transactions MODIFY type ENUM('deposit', 'unlock_fee', 'refund', 'adjustment', 'message_fee') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('wallet_transactions')->where('type', 'message_fee')->update(['type' => 'unlock_fee']);
            DB::statement("ALTER TABLE wallet_transactions MODIFY type ENUM('deposit', 'unlock_fee', 'refund', 'adjustment') NOT NULL");
        }

        Schema::table('message_logs', function (Blueprint $table) {
            $table->dropIndex(['status', 'id']);
            $table->dropConstrainedForeignId('device_id');
            $table->dropColumn(['segments', 'cost', 'refunded', 'attempts', 'claimed_at', 'sent_at']);
        });

        Schema::dropIfExists('gateway_devices');
    }
};
