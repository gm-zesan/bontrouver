<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Client Requirement: Track user reputation with number of verified transactions
     */
    public function run(): void
    {
        $metro = User::where('email', 'metro.auto@bontrouver.ca')->first();
        $sarah = User::where('email', 'seller@bontrouver.ca')->first();
        $david = User::where('email', 'david.miller@example.ca')->first();
        $alex  = User::where('email', 'buyer@bontrouver.ca')->first();

        if (!$alex || !$metro || !$sarah || !$david) {
            $this->command->warn('Users missing. Skipping TransactionSeeder.');
            return;
        }

        $listings = Listing::all();

        $transactions = [
            [
                'listing_id'   => $listings->firstWhere('user_id', $metro->id)?->id,
                'buyer_id'     => $alex->id,
                'seller_id'    => $metro->id,
                'amount'       => 22800.00,
                'status'       => 'completed',
                'completed_at' => now()->subDays(15),
            ],
            [
                'listing_id'   => $listings->firstWhere('user_id', $sarah->id)?->id,
                'buyer_id'     => $alex->id,
                'seller_id'    => $sarah->id,
                'amount'       => 1280.00,
                'status'       => 'completed',
                'completed_at' => now()->subDays(10),
            ],
            [
                'listing_id'   => $listings->firstWhere('user_id', $david->id)?->id,
                'buyer_id'     => $alex->id,
                'seller_id'    => $david->id,
                'amount'       => 1150.00,
                'status'       => 'completed',
                'completed_at' => now()->subDays(5),
            ],
            [
                'listing_id'   => null,
                'buyer_id'     => $david->id,
                'seller_id'    => $sarah->id,
                'amount'       => 450.00,
                'status'       => 'completed',
                'completed_at' => now()->subDays(2),
            ],
        ];

        foreach ($transactions as $txn) {
            Transaction::create($txn);
        }

        $this->command->info('TransactionSeeder: ' . count($transactions) . ' completed transactions seeded.');
    }
}
