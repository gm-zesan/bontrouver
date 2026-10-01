<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\PointTransaction;
use App\Models\Report;
use App\Models\SearchQuery;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * Compile complete dashboard analytical metrics and datasets.
     */
    public function getDashboardMetrics(): array
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sixtyDaysAgo = $now->copy()->subDays(60);

        // 1. Today's Real-Time Operations Pulse
        $todayStats = [
            'listings_created'        => Listing::whereDate('created_at', $today)->count(),
            'users_joined'            => User::whereDate('created_at', $today)->count(),
            'meetups_created'         => CompanionshipRequest::whereDate('created_at', $today)->count(),
            'verifications_submitted' => UserVerification::whereDate('created_at', $today)->count(),
            'reports_submitted'       => Report::whereDate('created_at', $today)->count(),
            'transactions_completed'  => Transaction::whereDate('created_at', $today)->count(),
            'points_circulated'       => (int) PointTransaction::whereDate('created_at', $today)->sum('points'),
        ];

        // 2. Process Lifecycle & Pipeline Breakdowns
        $lifecycle = [
            'listings' => [
                'total'          => Listing::count(),
                'active'         => Listing::where('status', 'active')->count(),
                'pending_review' => Listing::where('status', 'pending_review')->count(),
                'sold'           => Listing::where('status', 'sold')->count(),
                'draft'          => Listing::where('status', 'draft')->count(),
                'featured'       => Listing::where('is_featured', true)->count(),
                'sponsored'      => Listing::where('is_sponsored', true)->count(),
            ],
            'users' => [
                'total'     => User::count(),
                'verified'  => User::where('is_verified', true)->count(),
                'dealers'   => User::where('is_dealer', true)->count(),
                'suspended' => User::where('is_suspended', true)->count(),
            ],
            'verifications' => [
                'total'    => UserVerification::count(),
                'pending'  => UserVerification::where('status', 'pending')->count(),
                'approved' => UserVerification::where('status', 'approved')->count(),
                'rejected' => UserVerification::where('status', 'rejected')->count(),
            ],
            'meetups' => [
                'total'     => CompanionshipRequest::count(),
                'open'      => CompanionshipRequest::where('status', 'open')->count(),
                'completed' => CompanionshipRequest::where('status', 'completed')->count(),
                'cancelled' => CompanionshipRequest::where('status', 'cancelled')->count(),
            ],
            'reports' => [
                'total'      => Report::count(),
                'unresolved' => Report::whereNull('reviewed_at')->count(),
                'resolved'   => Report::whereNotNull('reviewed_at')->count(),
            ],
        ];

        // 3. Core Growth Deltas (Last 30 days vs previous 30 days)
        $usersLast30 = User::where('created_at', '>=', $thirtyDaysAgo)->count();
        $usersPrev30 = User::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])->count();
        $userGrowth = $this->calculateGrowthPercent($usersLast30, $usersPrev30);

        $listingsLast30 = Listing::where('created_at', '>=', $thirtyDaysAgo)->count();
        $listingsPrev30 = Listing::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])->count();
        $listingGrowth = $this->calculateGrowthPercent($listingsLast30, $listingsPrev30);

        // 4. Timeseries, Categories, Top Cities & Feeds
        $timeseries = $this->get30DayTimeseries();
        $categoryBreakdown = $this->getCategoryDistribution();
        $topCities = $this->getTopCities();

        $recentVerifications = UserVerification::with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentListings = Listing::with(['user', 'category'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentReports = Report::with(['reporter', 'reportable'])
            ->whereNull('reviewed_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return [
            'today'               => $todayStats,
            'lifecycle'           => $lifecycle,
            'kpi' => [
                'total_users'           => $lifecycle['users']['total'],
                'user_growth'           => $userGrowth,
                'verified_users'        => $lifecycle['users']['verified'],
                'dealers_count'         => $lifecycle['users']['dealers'],
                'total_listings'        => $lifecycle['listings']['total'],
                'active_listings'       => $lifecycle['listings']['active'],
                'listing_growth'        => $listingGrowth,
                'featured_listings'     => $lifecycle['listings']['featured'],
                'sponsored_listings'    => $lifecycle['listings']['sponsored'],
                'total_meetups'         => $lifecycle['meetups']['total'],
                'active_meetups'        => $lifecycle['meetups']['open'],
                'circulating_points'    => (int) User::sum('community_points'),
                'pending_verifications' => $lifecycle['verifications']['pending'],
                'pending_reports'       => $lifecycle['reports']['unresolved'],
                'pending_listings'      => $lifecycle['listings']['pending_review'],
                'total_searches'        => (int) SearchQuery::sum('hits_count'),
            ],
            'timeseries'          => $timeseries,
            'category_breakdown'  => $categoryBreakdown,
            'top_cities'          => $topCities,
            'recent_verifications'=> $recentVerifications,
            'recent_listings'     => $recentListings,
            'recent_reports'      => $recentReports,
        ];
    }

    /**
     * Generate daily activity counts for listings and users over the past 30 days.
     */
    private function get30DayTimeseries(): array
    {
        $dates = [];
        $listingCounts = [];
        $userCounts = [];
        $pointsEarned = [];
        $pointsSpent = [];

        for ($i = 29; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $dateStr = $day->format('Y-m-d');
            $dates[] = $day->format('M d');

            $listingCounts[] = Listing::whereDate('created_at', $dateStr)->count();
            $userCounts[] = User::whereDate('created_at', $dateStr)->count();
            
            $earned = PointTransaction::whereDate('created_at', $dateStr)->where('points', '>', 0)->sum('points');
            $spent = abs(PointTransaction::whereDate('created_at', $dateStr)->where('points', '<', 0)->sum('points'));
            
            $pointsEarned[] = (int) $earned;
            $pointsSpent[] = (int) $spent;
        }

        return [
            'labels'         => $dates,
            'listings'       => $listingCounts,
            'users'          => $userCounts,
            'points_earned'  => $pointsEarned,
            'points_spent'   => $pointsSpent,
        ];
    }

    /**
     * Get listing counts grouped by top categories.
     */
    private function getCategoryDistribution(): array
    {
        $categories = Category::whereNull('parent_id')
            ->withCount('listings')
            ->orderByDesc('listings_count')
            ->limit(6)
            ->get();

        $labels = [];
        $series = [];

        foreach ($categories as $cat) {
            $labels[] = $cat->name;
            $series[] = $cat->listings_count;
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    /**
     * Get top Canadian cities by listing volume.
     */
    private function getTopCities(): array
    {
        return Listing::join('cities', 'listings.city_id', '=', 'cities.id')
            ->select('cities.name as city', DB::raw('count(listings.id) as count'))
            ->whereNotNull('listings.city_id')
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('count')
            ->limit(6)
            ->get()
            ->toArray();
    }

    /**
     * Calculate percentage growth between current and prior periods.
     */
    private function calculateGrowthPercent(int $current, int $prior): array
    {
        if ($prior === 0) {
            return [
                'percent'   => $current > 0 ? 100 : 0,
                'direction' => 'up',
            ];
        }

        $diff = $current - $prior;
        $pct = round(($diff / $prior) * 100, 1);

        return [
            'percent'   => abs($pct),
            'direction' => $pct >= 0 ? 'up' : 'down',
        ];
    }
}
