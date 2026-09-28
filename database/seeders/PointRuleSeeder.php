<?php

namespace Database\Seeders;

use App\Models\PointRule;
use Illuminate\Database\Seeder;

class PointRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // Earning Rules
            [
                'rule_key'    => 'identity_verification',
                'name'        => 'Identity Verification Approved',
                'type'        => 'earn',
                'points'      => 50,
                'category'    => 'Trust & Verification',
                'description' => 'Awarded when admin approves official government ID verification.',
                'sort_order'  => 1,
            ],
            [
                'rule_key'    => 'verified_dealer',
                'name'        => 'Business / Dealer License Verified',
                'type'        => 'earn',
                'points'      => 100,
                'category'    => 'Trust & Verification',
                'description' => 'Awarded for licensed dealerships and verified business profiles.',
                'sort_order'  => 2,
            ],
            [
                'rule_key'    => 'positive_review',
                'name'        => 'Receiving 5-Star Buyer/Seller Review',
                'type'        => 'earn',
                'points'      => 20,
                'category'    => 'Reputation & Feedback',
                'description' => 'Awarded for high-quality transaction ratings (4-5 stars).',
                'sort_order'  => 3,
            ],
            [
                'rule_key'    => 'free_listing',
                'name'        => 'Giving Away a Free / Donated Item',
                'type'        => 'earn',
                'points'      => 25,
                'category'    => 'Mutual Aid & Giving',
                'description' => 'Rewarded for giving items to neighbors at $0 free of charge.',
                'sort_order'  => 4,
            ],
            [
                'rule_key'    => 'meetup_host',
                'name'        => 'Hosting a Companionship Meetup',
                'type'        => 'earn',
                'points'      => 30,
                'category'    => 'Community Activities',
                'description' => 'Awarded when hosting a social meetup with confirmed attendees.',
                'sort_order'  => 5,
            ],
            [
                'rule_key'    => 'meetup_attendee',
                'name'        => 'Attending a Community Meetup',
                'type'        => 'earn',
                'points'      => 15,
                'category'    => 'Community Activities',
                'description' => 'Rewarded for attending and participating in social gatherings.',
                'sort_order'  => 6,
            ],
            [
                'rule_key'    => 'first_deal',
                'name'        => 'First Verified Marketplace Deal',
                'type'        => 'earn',
                'points'      => 25,
                'category'    => 'Marketplace Activity',
                'description' => 'Bonus awarded upon successfully completing first transaction.',
                'sort_order'  => 7,
            ],

            // Spending Rules
            [
                'rule_key'    => 'featured_promotion',
                'name'        => 'Featured Listing Placement (7 Days)',
                'type'        => 'spend',
                'points'      => 100,
                'category'    => 'Ad Visibility',
                'description' => 'Reduces point balance to feature ad at top of category results.',
                'sort_order'  => 8,
            ],
            [
                'rule_key'    => 'sponsored_promotion',
                'name'        => 'Hero Carousel Spotlight (7 Days)',
                'type'        => 'spend',
                'points'      => 300,
                'category'    => 'Ad Visibility',
                'description' => 'Reduces point balance for homepage hero carousel banner exposure.',
                'sort_order'  => 9,
            ],
        ];

        foreach ($rules as $rule) {
            PointRule::updateOrCreate(
                ['rule_key' => $rule['rule_key']],
                $rule
            );
        }
    }
}
