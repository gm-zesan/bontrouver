<?php

namespace App\Services;

use App\Models\MemberTier;
use App\Models\PointRule;
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
     * Get system point earning and spending rules directly from database (PointRule model).
     */
    public function getPointRules(): array
    {
        return Cache::remember(self::POINT_RULES_CACHE_KEY, 86400 * 30, function () {
            $rules = PointRule::where('is_active', true)->orderBy('sort_order', 'asc')->get();

            $earnRules = $rules->where('type', 'earn')->map(fn ($r) => [
                'id'          => $r->id,
                'key'         => $r->rule_key,
                'name'        => $r->name,
                'points'      => (int) $r->points,
                'category'    => $r->category,
                'description' => $r->description,
            ])->values()->toArray();

            $spendRules = $rules->where('type', 'spend')->map(fn ($r) => [
                'id'          => $r->id,
                'key'         => $r->rule_key,
                'name'        => $r->name,
                'points'      => (int) $r->points,
                'category'    => $r->category,
                'description' => $r->description,
            ])->values()->toArray();

            return [
                'earn'  => $earnRules,
                'spend' => $spendRules,
            ];
        });
    }

    /**
     * Update configured point rules in database and invalidate cache.
     */
    public function updatePointRules(array $rulesData): array
    {
        if (isset($rulesData['earn']) && is_array($rulesData['earn'])) {
            foreach ($rulesData['earn'] as $key => $points) {
                PointRule::where('rule_key', $key)->where('type', 'earn')->update([
                    'points' => max(1, (int) $points),
                ]);
            }
        }

        if (isset($rulesData['spend']) && is_array($rulesData['spend'])) {
            foreach ($rulesData['spend'] as $key => $points) {
                PointRule::where('rule_key', $key)->where('type', 'spend')->update([
                    'points' => max(1, (int) $points),
                ]);
            }
        }

        Cache::forget(self::POINT_RULES_CACHE_KEY);

        return $this->getPointRules();
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
