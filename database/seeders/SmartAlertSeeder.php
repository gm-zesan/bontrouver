<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SmartAlert;
use App\Models\User;
use Illuminate\Database\Seeder;

class SmartAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Client Requirement: Smart Alerts ("A new room for $700 was just posted in Montreal.")
     */
    public function run(): void
    {
        $alex  = User::where('email', 'buyer@bontrouver.ca')->first();
        $david = User::where('email', 'david.miller@example.ca')->first();
        $sarah = User::where('email', 'seller@bontrouver.ca')->first();

        if (!$alex) {
            $this->command->warn('User not found. Skipping SmartAlertSeeder.');
            return;
        }

        $roomCategory = Category::where('slug', 'room-rentals-roommates')->first();
        $carCategory  = Category::where('slug', 'cars-trucks')->first();
        $phoneCategory = Category::where('slug', 'smartphones-tablets')->first();
        $aptCategory  = Category::where('slug', 'apartments-condos-rent')->first();

        $alerts = [
            // Client example: Room in Montreal
            [
                'user_id'     => $alex->id,
                'name'        => 'Student Room in Montreal under $1000',
                'keyword'     => 'Furnished Room',
                'category_id' => $roomCategory?->id,
                'city'        => 'Montréal',
                'min_price'   => 500.00,
                'max_price'   => 1000.00,
                'is_active'   => true,
            ],
            // Vehicle alert in Toronto
            [
                'user_id'     => $alex->id,
                'name'        => 'Toyota RAV4 Hybrid in Toronto Area',
                'keyword'     => 'RAV4 Hybrid',
                'category_id' => $carCategory?->id,
                'city'        => 'Toronto',
                'min_price'   => 30000.00,
                'max_price'   => 45000.00,
                'is_active'   => true,
            ],
            // Tech gadget in Vancouver
            [
                'user_id'     => $david ? $david->id : $alex->id,
                'name'        => 'iPhone 15 Pro Max under $1400',
                'keyword'     => 'iPhone 15 Pro',
                'category_id' => $phoneCategory?->id,
                'city'        => 'Vancouver',
                'min_price'   => 900.00,
                'max_price'   => 1400.00,
                'is_active'   => true,
            ],
            // Apartment in Toronto
            [
                'user_id'     => $sarah ? $sarah->id : $alex->id,
                'name'        => '1-Bed Downtown Condo in Toronto',
                'keyword'     => 'Condo Balcony',
                'category_id' => $aptCategory?->id,
                'city'        => 'Toronto',
                'min_price'   => 2000.00,
                'max_price'   => 2600.00,
                'is_active'   => true,
            ],
        ];

        foreach ($alerts as $alertData) {
            $cityModel = \App\Models\City::where('slug', \Illuminate\Support\Str::slug($alertData['city']))
                ->orWhere('name', 'like', '%' . $alertData['city'] . '%')
                ->first();

            $alertData['city_id'] = $cityModel?->id;
            $alertData['province_id'] = $cityModel?->province_id;
            $alertData['city'] = $cityModel?->name ?? $alertData['city'];

            SmartAlert::create($alertData);
        }

        $this->command->info('SmartAlertSeeder: ' . count($alerts) . ' smart alerts seeded.');
    }
}
