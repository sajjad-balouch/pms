<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recovery follow-up log: kis customer ko kab call/WhatsApp/visit kiya, kya jawab mila,
 * aur "promise to pay" ki tareekh.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plot_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plot_booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // kis ne follow-up kiya
            $table->string('channel', 20);  // call | whatsapp | visit | sms
            $table->string('outcome', 30);  // reached | no_answer | promised | refused | sent
            $table->date('promise_date')->nullable();
            $table->string('note', 500)->nullable();
            $table->timestamps();

            $table->index(['plot_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plot_followups');
    }
};
