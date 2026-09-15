<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metro = User::where('email', 'metro.auto@bontrouver.ca')->first();
        $sarah = User::where('email', 'seller@bontrouver.ca')->first();
        $david = User::where('email', 'david.miller@example.ca')->first();
        $alex  = User::where('email', 'buyer@bontrouver.ca')->first();

        if (!$metro || !$sarah || !$david || !$alex) {
            $this->command->warn('Required users not found. Skipping ReviewSeeder.');
            return;
        }

        $reviews = [
            // Review for Metro Auto from Alex
            [
                'reviewer_id' => $alex->id,
                'reviewee_id' => $metro->id,
                'listing_id'  => Listing::where('user_id', $metro->id)->first()?->id,
                'rating'      => 5,
                'comment'     => 'Exceptional dealership experience. The vehicle matched the exact description, CARFAX report provided immediately, and test drive was seamless. Highly recommended!',
            ],
            // Review for Sarah (TechVault) from Alex
            [
                'reviewer_id' => $alex->id,
                'reviewee_id' => $sarah->id,
                'listing_id'  => Listing::where('user_id', $sarah->id)->first()?->id,
                'rating'      => 5,
                'comment'     => 'Purchased the iPhone in person. Sarah is super trustworthy, product was in mint condition with original accessories. Fast and safe meetup in Montreal.',
            ],
            // Review for David from Alex
            [
                'reviewer_id' => $alex->id,
                'reviewee_id' => $david->id,
                'listing_id'  => Listing::where('user_id', $david->id)->first()?->id,
                'rating'      => 5,
                'comment'     => 'Smooth transaction and great communication. David helped load the furniture safely into my vehicle. Would buy from again anytime!',
            ],
            // Review for Sarah from David
            [
                'reviewer_id' => $david->id,
                'reviewee_id' => $sarah->id,
                'listing_id'  => Listing::where('user_id', $sarah->id)->skip(1)->first()?->id,
                'rating'      => 4,
                'comment'     => 'Great communication, prompt response time, and friendly seller. Gadget was exactly as described in the ad.',
            ],
            // Review for Alex (buyer) from Metro Auto
            [
                'reviewer_id' => $metro->id,
                'reviewee_id' => $alex->id,
                'listing_id'  => null,
                'rating'      => 5,
                'comment'     => 'A+ buyer! Prompt payment, showed up on time, and very respectful during negotiations.',
            ],
        ];

        $count = 0;
        foreach ($reviews as $reviewData) {
            Review::create($reviewData);
            $count++;
        }

        $this->command->info("ReviewSeeder: {$count} authentic reviews seeded.");
    }
}
