<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'town_owner', 'agent', 'user'])->default('user')->after('email');
            $table->timestamp('trial_ends_at')->nullable()->after('role');
            $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('trial_ends_at');
            $table->boolean('is_approved')->default(true)->after('wallet_balance'); // For Agents & Town Owners
            $table->string('phone')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'trial_ends_at', 'wallet_balance', 'is_approved', 'phone']);
        });
    }
};