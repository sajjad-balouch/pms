<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('towns', function (Blueprint $table) {
            $table->string('master_plan_map')->nullable()->after('google_map_url');
            $table->json('gallery_images')->nullable()->after('master_plan_map');
        });
    }

    public function down(): void
    {
        Schema::table('towns', function (Blueprint $table) {
            $table->dropColumn(['master_plan_map', 'gallery_images']);
        });
    }
};