<?php

namespace App\Services;

use App\Models\MemberTier;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminMemberTierService
{
    public const POINT_RULES_CACHE_KEY = 'point_rules';

    /**
     * Get all member tiers with member counts and distribution statistics.
     */
    public function getTiersWithStats(): array
    {
        $tiers = MemberTier::orderBy('min_points', 'asc')->get();
        $totalUsers = User::count();

        return $tiers->map(function ($tier, $idx) use ($tiers, $totalUsers) {
            $min = (int) $tier->min_points;
            $max = $tier->max_points !== null ? (int) $tier->max_points : null;

            $usersCountQuery = User::where('community_points', '>=', $min);
            if ($max !== null) {
                $usersCountQuery->where('community_points', '<=', $max);
            }
            $usersCount = $usersCountQuery->count();
            $percentage = $totalUsers > 0 ? round(($usersCount / $totalUsers) * 100, 1) : 0;

            return [
                'id'          => $tier->id,
                'name'        => $tier->name,
                'clean_name'  => $tier->clean_name,
                'icon'        => $tier->icon ?? '🥉',
                'badge_color' => $tier->badge_color ?? '#cd7f32',
                'badge_class' => $tier->badge_class ?? 'tier-badge tier-bronze',
                'min_points'  => $min,
                'max_points'  => $max,
                'description' => $tier->description,
                'perks'       => is_array($tier->perks) ? $tier->perks : [],
                'users_count' => $usersCount,
                'percentage'  => $percentage,
                'level'       => $idx + 1,
            ];
        })->toArray();
    }

    /**
     * Update an existing member tier's thresholds and details.
     */
    public function updateTier(MemberTier $tier, array $data): MemberTier
    {
        return DB::transaction(function () use ($tier, $data) {
            $perks = $data['perks'] ?? [];
            if (is_string($perks)) {
                $perks = array_values(array_filter(array_map('trim', explode("\n", $perks))));
            }

            $tier->update([
                'name'        => $data['name'] ?? $tier->name,
                'icon'        => $data['icon'] ?? $tier->icon,
                'badge_color' => $data['badge_color'] ?? $tier->badge_color,
                'badge_class' => $data['badge_class'] ?? $tier->badge_class,
                'min_points'  => isset($data['min_points']) ? (int) $data['min_points'] : $tier->min_points,
                'max_points'  => (isset($data['max_points']) && $data['max_points'] !== '' && $data['max_points'] !== null) ? (int) $data['max_points'] : null,
                'description' => $data['description'] ?? $tier->description,
                'perks'       => $perks,
            ]);

            Cache::forget(MemberTier::CACHE_KEY);

            return $tier;
        });
    }

    /**
     * Build the query for Point Transactions ledger with search and filters.
     */
    public function getPointLedgerQuery(array $filters = []): Builder
    {
        $query = PointTransaction::with(['user'])->latest('id');

        if (!empty($filters['action_type']) && $filters['action_type'] !== 'all') {
            $query->where('action_type', $filters['action_type']);
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] === 'earned') {
                $query->where('points', '>', 0);
            } elseif ($filters['type'] === 'spent') {
                $query->where('points', '<', 0);
            }
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    /**
     * Perform an administrative manual points adjustment (Award or Deduct).
     */
    public function adjustUserPoints(User $user, int $amount, string $actionType, string $description, ?int $adminId = null): PointTransaction
    {
        return DB::transaction(function () use ($user, $amount, $actionType, $description) {
            $oldTier = $user->member_tier;

            $transaction = PointTransaction::create([
                'user_id'        => $user->id,
                'points'         => $amount,
                'action_type'    => $actionType ?: ($amount >= 0 ? 'admin_award' : 'admin_deduct'),
                'reference_type' => null,
                'reference_id'   => null,
                'description'    => $description,
            ]);

            // Adjust community_points safely (avoid negative balances)
            $newPoints = max(0, ((int) $user->community_points) + $amount);
            $user->update(['community_points' => $newPoints]);
            $user->refresh();

            $newTier = $user->member_tier;

            if ($amount > 0 && ($newTier['level'] ?? 1) > ($oldTier['level'] ?? 1)) {
                $user->notify(new \App\Notifications\MemberTierUpgraded($newTier, $oldTier, $newPoints));
            }

            return $transaction;
        });
    }

    /**
     * Get system point earning and spending rules.
     */
    public function getPointRules(): array
    {
        return Cache::remember(self::POINT_RULES_CACHE_KEY, 86400 * 30, function () {
            $defaultRules = [
                'earn' => [
                    [
                        'key'         => 'identity_verification',
                        'name'        => 'Identity Verification Approved',
                        'points'      => config('points.earn.identity_verification', 50),
                        'category'    => 'Trust & Verification',
                        'description' => 'Awarded when admin approves official government ID verification.',
                    ],
                    [
                        'key'         => 'verified_dealer',
                        'name'        => 'Business / Dealer License Verified',
                        'points'      => 100,
                        'category'    => 'Trust & Verification',
                        'description' => 'Awarded for licensed dealerships and verified business profiles.',
                    ],
                    [
                        'key'         => 'positive_review',
                        'name'        => 'Receiving 5-Star Buyer/Seller Review',
                        'points'      => config('points.earn.positive_review', 20),
                        'category'    => 'Reputation & Feedback',
                        'description' => 'Awarded for high-quality transaction ratings (4-5 stars).',
                    ],
                    [
                        'key'         => 'free_listing',
                        'name'        => 'Giving Away a Free / Donated Item',
                        'points'      => config('points.earn.free_listing', 25),
                        'category'    => 'Mutual Aid & Giving',
                        'description' => 'Rewarded for giving items to neighbors at $0 free of charge.',
                    ],
                    [
                        'key'         => 'meetup_host',
                        'name'        => 'Hosting a Companionship Meetup',
                        'points'      => config('points.earn.meetup_host', 30),
                        'category'    => 'Community Activities',
                        'description' => 'Awarded when hosting a social meetup with confirmed attendees.',
                    ],
                    [
                        'key'         => 'meetup_attendee',
                        'name'        => 'Attending a Community Meetup',
                        'points'      => 15,
                        'category'    => 'Community Activities',
                        'description' => 'Rewarded for attending and participating in social gatherings.',
                    ],
                    [
                        'key'         => 'first_deal',
                        'name'        => 'First Verified Marketplace Deal',
                        'points'      => 25,
                        'category'    => 'Marketplace Activity',
                        'description' => 'Bonus awarded upon successfully completing first transaction.',
                    ],
                ],
                'spend' => [
                    [
                        'key'         => 'featured_promotion',
                        'name'        => 'Featured Listing Placement (7 Days)',
                        'points'      => config('points.spend.featured_promotion', 100),
                        'category'    => 'Ad Visibility',
                        'description' => 'Reduces point balance to feature ad at top of category results.',
                    ],
                    [
                        'key'         => 'sponsored_promotion',
                        'name'        => 'Hero Carousel Spotlight (7 Days)',
                        'points'      => config('points.spend.sponsored_promotion', 300),
                        'category'    => 'Ad Visibility',
                        'description' => 'Reduces point balance for homepage hero carousel banner exposure.',
                    ],
                ],
            ];

            return $defaultRules;
        });
    }

    /**
     * Update configured point rules and invalidate cache.
     */
    public function updatePointRules(array $rulesData): array
    {
        $currentRules = $this->getPointRules();

        if (isset($rulesData['earn']) && is_array($rulesData['earn'])) {
            foreach ($currentRules['earn'] as &$rule) {
                if (isset($rulesData['earn'][$rule['key']])) {
                    $rule['points'] = max(1, (int) $rulesData['earn'][$rule['key']]);
                }
            }
        }

        if (isset($rulesData['spend']) && is_array($rulesData['spend'])) {
            foreach ($currentRules['spend'] as &$rule) {
                if (isset($rulesData['spend'][$rule['key']])) {
                    $rule['points'] = max(1, (int) $rulesData['spend'][$rule['key']]);
                }
            }
        }

        Cache::put(self::POINT_RULES_CACHE_KEY, $currentRules, 86400 * 30);

        return $currentRules;
    }

    /**
     * Get real-time top KPI metrics for the Member Tiers & Points dashboard.
     */
    public function getPointsKpis(): array
    {
        $totalPoints = (int) User::sum('community_points');
        $totalTransactions = PointTransaction::count();

        $pointsAwarded30d = (int) PointTransaction::where('points', '>', 0)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('points');

        $pointsSpent30d = (int) abs(PointTransaction::where('points', '<', 0)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('points'));

        $eliteMembersCount = User::where('community_points', '>=', 300)->count();
        $totalUsers = max(1, User::count());
        $avgPoints = round($totalPoints / $totalUsers, 1);

        return [
            'total_points_circulation' => $totalPoints,
            'total_transactions'       => $totalTransactions,
            'points_awarded_30d'       => $pointsAwarded30d,
            'points_spent_30d'         => $pointsSpent30d,
            'elite_members_count'      => $eliteMembersCount,
            'avg_points_per_user'      => $avgPoints,
        ];
    }
}
