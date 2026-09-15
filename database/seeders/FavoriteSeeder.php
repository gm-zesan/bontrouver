<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alex  = User::where('email', 'buyer@bontrouver.ca')->first();
        $sarah = User::where('email', 'seller@bontrouver.ca')->first();
        $david = User::where('email', 'david.miller@example.ca')->first();

        $listings = Listing::all();

        if ($listings->isEmpty() || !$alex) {
            $this->command->warn('Listings or users missing. Skipping FavoriteSeeder.');
            return;
        }

        $favoritesData = [];

        // Alex (Buyer) favorites several vehicles, tech & housing listings
        foreach ($listings->take(5) as $listing) {
            $favoritesData[] = [
                'user_id'    => $alex->id,
                'listing_id' => $listing->id,
            ];
        }

        // Sarah favorites some furniture and housing
        if ($sarah) {
            foreach ($listings->skip(2)->take(3) as $listing) {
                $favoritesData[] = [
                    'user_id'    => $sarah->id,
                    'listing_id' => $listing->id,
                ];
            }
        }

        // David favorites some cars and tech
        if ($david) {
            foreach ($listings->skip(4)->take(2) as $listing) {
                $favoritesData[] = [
                    'user_id'    => $david->id,
                    'listing_id' => $listing->id,
                ];
            }
        }

        $count = 0;
        foreach ($favoritesData as $fav) {
            Favorite::firstOrCreate(
                ['user_id' => $fav['user_id'], 'listing_id' => $fav['listing_id']],
                $fav
            );
            $count++;
        }

        $this->command->info("FavoriteSeeder: {$count} favorites seeded.");
    }
}
