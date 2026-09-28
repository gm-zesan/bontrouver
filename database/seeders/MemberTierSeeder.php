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
                'name'        => '🥉 New Member',
                'icon'        => '🥉',
                'badge_color' => '#cd7f32',
                'badge_class' => 'tier-badge tier-bronze',
                'min_points'  => 0,
                'max_points'  => 99,
                'description' => 'Entry-level tier for newly registered community members.',
                'perks'       => [
                    'Standard ad posting and search browsing',
                    'Direct messaging with verified sellers and buyers',
                    'Basic profile showcase',
                ],
            ],
            [
                'name'        => '🥈 Active Member',
                'icon'        => '🥈',
                'badge_color' => '#94a3b8',
                'badge_class' => 'tier-badge tier-silver',
                'min_points'  => 100,
                'max_points'  => 299,
                'description' => 'Active community contributor who has completed verification and initial deals.',
                'perks'       => [
                    'Active Member badge displayed on public listings and profile',
                    'Access to Community Companionship Meetup hosting',
                    'Priority response queue in support',
                ],
            ],
            [
                'name'        => '🥇 Trusted Member',
                'icon'        => '🥇',
                'badge_color' => '#eab308',
                'badge_class' => 'tier-badge tier-gold',
                'min_points'  => 300,
                'max_points'  => 699,
                'description' => 'High-trust community pillar with positive feedback and mutual aid history.',
                'perks'       => [
                    'Gold Trusted Member badge with instant buyer confidence',
                    '10% point discount on Featured Ad listings',
                    'Smart Alert priority matching speed',
                    'Verified Community Host status for events',
                ],
            ],
            [
                'name'        => '⭐ Highly Appreciated Member',
                'icon'        => '⭐',
                'badge_color' => '#8b5cf6',
                'badge_class' => 'tier-badge tier-platinum',
                'min_points'  => 700,
                'max_points'  => null,
                'description' => 'Elite community leader recognized for exceptional mutual aid, reliability, and service.',
                'perks'       => [
                    'Elite Platinum Star badge across all ads, profile, and messages',
                    '25% point discount on all promotional upgrades',
                    'Direct moderation priority line',
                    'Permanent verified badge and spotlight visibility',
                ],
            ],
        ];

        foreach ($tiers as $tier) {
            MemberTier::updateOrCreate(
                ['name' => $tier['name']],
                $tier
            );
        }

        cache()->forget(MemberTier::CACHE_KEY);

        $this->command->info('MemberTierSeeder: 4 client tiers seeded.');
    }
}
