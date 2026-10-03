<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('type');
            $table->string('sender_account_title')->nullable()->after('payment_method');
            $table->string('sender_account_number')->nullable()->after('sender_account_title');
            $table->string('screenshot')->nullable()->after('sender_account_number');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'sender_account_title', 'sender_account_number', 'screenshot', 'status']);
        });
    }
};