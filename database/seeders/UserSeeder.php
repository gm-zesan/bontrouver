<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // 1. Super Administrator
            [
                'name' => 'Bontrouver Admin',
                'email' => 'admin@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '+1 (800) 555-0100',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Head of Marketplace Operations at Bontrouver Canada.',
                'community_points' => 500,
                'is_verified' => true,
                'is_dealer' => false,
            ],

            // 2. Pro Automotive & Vehicles Seller
            [
                'name' => 'Metro Auto & Pre-Owned Gallery',
                'email' => 'metro.auto@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => 'seller',
                'phone' => '+1 (416) 555-0192',
                'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Certified pre-owned automotive specialist in Greater Toronto Area. Clean CARFAX guarantee.',
                'community_points' => 350,
                'is_verified' => true,
                'is_dealer' => true,
            ],

            // 3. Pro Electronics & Tech Seller
            [
                'name' => 'Sarah Tremblay (TechVault)',
                'email' => 'seller@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => 'seller',
                'phone' => '+1 (514) 555-0177',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Electronics enthusiast and verified Apple reseller. Fast public meetups and guaranteed tested gadgets.',
                'community_points' => 220,
                'is_verified' => true,
                'is_dealer' => false,
            ],

            // 4. Verified Private Seller (Home & Furniture)
            [
                'name' => 'David Miller',
                'email' => 'david.miller@example.ca',
                'password' => Hash::make('password123'),
                'role' => 'seller',
                'phone' => '+1 (604) 555-0133',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Moving sale! High quality mid-century furniture, cycling gear, and home goods.',
                'community_points' => 150,
                'is_verified' => true,
                'is_dealer' => false,
            ],

            // 5. Active Canadian Buyer
            [
                'name' => 'Alex Chen',
                'email' => 'buyer@bontrouver.ca',
                'password' => Hash::make('password123'),
                'role' => 'buyer',
                'phone' => '+1 (403) 555-0188',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
                'bio' => 'Verified buyer searching for vehicles, tech gear, and outdoor sports equipment in Alberta.',
                'community_points' => 80,
                'is_verified' => true,
                'is_dealer' => false,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
