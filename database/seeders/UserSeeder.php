<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Minimal login information for Super Admin and Admin accounts.
     */
    public function run(): void
    {
        // 1. Super Administrator
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('superadmin@123'),
                'role'              => UserRole::SUPERADMIN,
                'email_verified_at' => now(),
                'is_verified'       => true,
            ]
        );

        // 2. Administrator
        User::updateOrCreate(
            ['email' => 'admin@bontrouver.ca'],
            [
                'name'              => 'Bontrouver Admin',
                'password'          => Hash::make('Admin!BT123#Secure'),
                'role'              => UserRole::ADMIN,
                'email_verified_at' => now(),
                'is_verified'       => true,
            ]
        );
    }
}
