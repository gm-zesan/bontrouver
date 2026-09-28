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
        $sarah   = User::where('email', 'seller@bontrouver.ca')->first();
        $marcus  = User::where('email', 'marcus.v@bontrouver.ca')->first();
        $david   = User::where('email', 'david.miller@bontrouver.ca')->first();
        $elena   = User::where('email', 'elena.r@bontrouver.ca')->first();
        $alex    = User::where('email', 'buyer@bontrouver.ca')->first();
        $priya   = User::where('email', 'priya.p@bontrouver.ca')->first();
        $jeanluc = User::where('email', 'jeanluc.d@bontrouver.ca')->first();

        if (!$sarah || !$marcus || !$david || !$alex) {
            $this->command->warn('Required users not found. Skipping ReviewSeeder.');
            return;
        }

        $reviews = [
            // Review for Marcus from Alex
            [
                'reviewer_id' => $alex->id,
                'reviewee_id' => $marcus->id,
                'listing_id'  => Listing::where('user_id', $marcus->id)->first()?->id,
                'rating'      => 5,
                'comment'     => 'Exceptional experience! Marcus demoed the audio turntable setup in person. Everything matched the description perfectly and sound quality is sublime.',
            ],
            // Review for Sarah (Photography & Tech) from Alex
            [
                'reviewer_id' => $alex->id,
                'reviewee_id' => $sarah->id,
                'listing_id'  => Listing::where('user_id', $sarah->id)->first()?->id,
                'rating'      => 5,
                'comment'     => 'Purchased the camera lens in person. Sarah is super trustworthy, gear was in mint condition with original packaging. Fast and safe meetup near Metro Mont-Royal!',
            ],
            // Review for David from Priya
            [
                'reviewer_id' => $priya->id,
                'reviewee_id' => $david->id,
                'listing_id'  => Listing::where('user_id', $david->id)->first()?->id,
                'rating'      => 5,
                'comment'     => 'Smooth transaction and great communication. David helped load the custom solid wood table safely into my SUV. Beautiful craftsmanship!',
            ],
            // Review for Elena from Marcus
            [
                'reviewer_id' => $marcus->id,
                'reviewee_id' => $elena->id,
                'listing_id'  => Listing::where('user_id', $elena->id)->first()?->id,
                'rating'      => 5,
                'comment'     => 'High performance ski gear as described. Elena is super responsive and knows mountain gear inside out. A+ seller in Calgary!',
            ],
            // Review for Jean-Luc from Sarah
            [
                'reviewer_id' => $sarah->id,
                'reviewee_id' => $jeanluc->id,
                'listing_id'  => Listing::where('user_id', $jeanluc->id)->first()?->id,
                'rating'      => 5,
                'comment'     => 'Livre d\'art rare reçu en parfait état, emballé avec grand soin. Un grand passionné d\'histoire fort sympathique!',
            ],
            // Review for Alex (buyer) from David
            [
                'reviewer_id' => $david->id,
                'reviewee_id' => $alex->id,
                'listing_id'  => null,
                'rating'      => 5,
                'comment'     => 'A+ community member! Prompt payment via Interac e-Transfer, showed up right on time for pickup, and very courteous.',
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
