<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Seeds essential Canadian master data and administrators for production.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MemberTierSeeder::class,
            PointRuleSeeder::class,
            CategorySeeder::class,
            CategoryAttributeSeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
            SiteSettingSeeder::class,
            MonetizationSeeder::class,
        ]);
    }
}
