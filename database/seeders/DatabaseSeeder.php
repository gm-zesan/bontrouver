<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database based on customer requirements.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MemberTierSeeder::class,
            CategorySeeder::class,
            CategoryAttributeSeeder::class,
            ListingSeeder::class,
            FavoriteSeeder::class,
            SmartAlertSeeder::class,
            TransactionSeeder::class,
            ReviewSeeder::class,
            ConversationSeeder::class,
            CompanionshipSeeder::class,
            PointTransactionSeeder::class,
        ]);
    }
}
