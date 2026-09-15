<?php

namespace Database\Seeders;

use App\Models\MemberTier;
use Illuminate\Database\Seeder;

class MemberTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Client Ranking: 🥉 New Member → 🥈 Active Member → 🥇 Trusted Member → ⭐ Highly Appreciated Member
     */
    public function run(): void
    {
        $tiers = [
            [
                'name'       => '🥉 New Member',
                'min_points' => 0,
                'max_points' => 99,
            ],
            [
                'name'       => '🥈 Active Member',
                'min_points' => 100,
                'max_points' => 299,
            ],
            [
                'name'       => '🥇 Trusted Member',
                'min_points' => 300,
                'max_points' => 699,
            ],
            [
                'name'       => '⭐ Highly Appreciated Member',
                'min_points' => 700,
                'max_points' => null,
            ],
        ];

        foreach ($tiers as $tier) {
            MemberTier::updateOrCreate(
                ['name' => $tier['name']],
                $tier
            );
        }

        $this->command->info('MemberTierSeeder: 4 client tiers seeded.');
    }
}
