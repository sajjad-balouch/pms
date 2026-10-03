<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@property.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_approved' => true,
        ]);

        // 2. Town Owner
        User::create([
            'name' => 'Royal City Developers',
            'email' => 'owner@town.com',
            'password' => Hash::make('password123'),
            'role' => 'town_owner',
            'is_approved' => true,
            'phone' => '03001234567',
        ]);

        // 3. Property Agent
        User::create([
            'name' => 'Al-Makkah Estate Agent',
            'email' => 'agent@estate.com',
            'password' => Hash::make('password123'),
            'role' => 'agent',
            'is_approved' => true,
            'phone' => '03019876543',
        ]);

        // 4. Regular User (with Active 3-Day Trial)
        User::create([
            'name' => 'John Doe',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'trial_ends_at' => Carbon::now()->addDays(3),
            'is_approved' => true,
        ]);
    }
}