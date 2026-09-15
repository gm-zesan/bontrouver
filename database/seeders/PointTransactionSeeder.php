<?php

namespace Database\Seeders;

use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class PointTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Client Requirement: Reward mutual aid and trust
     * Actions: answering a request, helping someone, giving away an item, providing a service, getting good reviews.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@bontrouver.ca')->first();
        $metro = User::where('email', 'metro.auto@bontrouver.ca')->first();
        $sarah = User::where('email', 'seller@bontrouver.ca')->first();
        $david = User::where('email', 'david.miller@example.ca')->first();
        $alex  = User::where('email', 'buyer@bontrouver.ca')->first();

        $transactions = [
            // Admin / Moderator trust points
            [
                'user_id'     => $admin?->id,
                'points'      => 100,
                'action_type' => 'verified_identity',
                'description' => 'Official ID & Identity Verification completed',
                'created_at'  => now()->subDays(40),
            ],
            [
                'user_id'     => $admin?->id,
                'points'      => 400,
                'action_type' => 'community_service',
                'description' => 'Community moderation & local event organization',
                'created_at'  => now()->subDays(20),
            ],

            // Metro Auto
            [
                'user_id'     => $metro?->id,
                'points'      => 100,
                'action_type' => 'verified_dealer',
                'description' => 'OMVIC Licensed Dealer & Business Verification',
                'created_at'  => now()->subDays(35),
            ],
            [
                'user_id'     => $metro?->id,
                'points'      => 150,
                'action_type' => 'positive_reviews',
                'description' => 'Earned 5-star verified buyer ratings and testimonials',
                'created_at'  => now()->subDays(15),
            ],
            [
                'user_id'     => $metro?->id,
                'points'      => 100,
                'action_type' => 'answering_requests',
                'description' => 'Answered 20+ vehicle inquiries and buyer guidance requests',
                'created_at'  => now()->subDays(5),
            ],

            // Sarah (TechVault)
            [
                'user_id'     => $sarah?->id,
                'points'      => 75,
                'action_type' => 'verified_identity',
                'description' => 'ID Verified and phone confirmed',
                'created_at'  => now()->subDays(30),
            ],
            [
                'user_id'     => $sarah?->id,
                'points'      => 50,
                'action_type' => 'giving_away_item',
                'description' => 'Free item giveaway to university student in community',
                'created_at'  => now()->subDays(18),
            ],
            [
                'user_id'     => $sarah?->id,
                'points'      => 95,
                'action_type' => 'helping_someone',
                'description' => 'Helped 3 community members with tech repair & setup advice',
                'created_at'  => now()->subDays(8),
            ],

            // David Miller
            [
                'user_id'     => $david?->id,
                'points'      => 50,
                'action_type' => 'verified_identity',
                'description' => 'Profile and phone number verified',
                'created_at'  => now()->subDays(25),
            ],
            [
                'user_id'     => $david?->id,
                'points'      => 50,
                'action_type' => 'giving_away_item',
                'description' => 'Donated moving boxes and surplus furniture for free',
                'created_at'  => now()->subDays(12),
            ],
            [
                'user_id'     => $david?->id,
                'points'      => 50,
                'action_type' => 'companionship_host',
                'description' => 'Organized friendly neighbourhood walking meetup in Kitsilano',
                'created_at'  => now()->subDays(3),
            ],

            // Alex Chen (Active buyer)
            [
                'user_id'     => $alex?->id,
                'points'      => 50,
                'action_type' => 'verified_identity',
                'description' => 'Community profile completed and verified',
                'created_at'  => now()->subDays(10),
            ],
            [
                'user_id'     => $alex?->id,
                'points'      => 30,
                'action_type' => 'community_review',
                'description' => 'Provided thorough and honest transaction feedback',
                'created_at'  => now()->subDays(2),
            ],
        ];

        $count = 0;
        foreach ($transactions as $txn) {
            if (!empty($txn['user_id'])) {
                PointTransaction::create($txn);
                $count++;
            }
        }

        $this->command->info("PointTransactionSeeder: {$count} mutual aid point transactions seeded.");
    }
}
