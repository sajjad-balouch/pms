<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('agency_name')->nullable()->after('username');
            $table->string('agency_tagline')->nullable()->after('agency_name');
            $table->string('agency_logo')->nullable()->after('agency_tagline');
            $table->string('city_name')->nullable()->after('agency_logo');
            $table->string('office_address')->nullable()->after('city_name');
            $table->string('google_map_link')->nullable()->after('office_address');
            $table->text('about_agency')->nullable()->after('google_map_link');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'agency_name',
                'agency_tagline',
                'agency_logo',
                'city_name',
                'office_address',
                'google_map_link',
                'about_agency'
            ]);
        });
    }
};