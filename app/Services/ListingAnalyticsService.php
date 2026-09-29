<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Favorite;
use App\Models\Listing;
use App\Models\ListingView;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListingAnalyticsService
{
    /**
     * Record a unique daily view for a listing.
     */
    public function recordView(Listing $listing, Request $request): void
    {
        $ip = $request->ip();
        $today = Carbon::today()->toDateString();
        $userId = auth()->id();

        // Don't record seller's own views
        if ($userId && $userId === $listing->user_id) {
            return;
        }

        // Check if viewed today by this IP or User
        $exists = ListingView::where('listing_id', $listing->id)
            ->where('viewed_date', $today)
            ->where(function ($q) use ($ip, $userId) {
                $q->where('ip_address', $ip);
                if ($userId) {
                    $q->orWhere('user_id', $userId);
                }
            })
            ->exists();

        if (!$exists) {
            ListingView::create([
                'listing_id'  => $listing->id,
                'user_id'     => $userId,
                'ip_address'  => $ip,
                'user_agent'  => substr((string)$request->userAgent(), 0, 255),
                'referer'     => substr((string)$request->header('referer'), 0, 255),
                'viewed_date' => $today,
            ]);

            $listing->increment('views_count');
        }
    }

    /**
     * Get aggregated performance analytics for a listing over the given timeframe.
     */
    public function getListingPerformance(Listing $listing, int $days = 14): array
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        // Fetch daily views aggregated by date
        $viewsByDate = ListingView::where('listing_id', $listing->id)
            ->whereBetween('viewed_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->select('viewed_date', DB::raw('COUNT(*) as total_views'))
            ->groupBy('viewed_date')
            ->pluck('total_views', 'viewed_date')
            ->toArray();

        $chartLabels = [];
        $chartSeries = [];

        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $chartLabels[] = $date->format('M d');
            $chartSeries[] = (int) ($viewsByDate[$dateStr] ?? 0);
        }

        $totalViews = (int) $listing->views_count;
        if ($totalViews === 0 && array_sum($chartSeries) > 0) {
            $totalViews = array_sum($chartSeries);
        }

        $favoritesCount = Favorite::where('listing_id', $listing->id)->count();
        $inquiriesCount = Conversation::where('listing_id', $listing->id)->count();

        // Calculate Engagement Rate (Favorites + Messages / Views)
        $engagementRate = $totalViews > 0
            ? round((($favoritesCount + $inquiriesCount) / $totalViews) * 100, 1)
            : 0;

        // Boost Status Information
        $activePromos = $listing->promotions()->where('is_active', true)->get();
        $boostDetails = [];

        foreach ($activePromos as $promo) {
            $daysLeft = $promo->expires_at ? max(0, Carbon::now()->diffInDays($promo->expires_at, false) + 1) : null;
            $boostDetails[] = [
                'type' => $promo->type,
                'name' => ucfirst(str_replace('_', ' ', $promo->type)),
                'days_left' => $daysLeft,
                'expires_at' => $promo->expires_at?->format('M d, Y'),
            ];
        }

        return [
            'listing' => [
                'id' => $listing->id,
                'title' => $listing->title,
                'slug' => $listing->slug,
                'price' => '$' . number_format($listing->price, 2) . ' CAD',
                'primary_image' => $listing->primary_image_url,
                'is_sponsored' => $listing->is_sponsored,
                'is_featured' => $listing->is_featured,
                'is_bumped' => $listing->bumped_at !== null,
                'status' => $listing->status->value,
                'published_at' => $listing->created_at->format('M d, Y'),
            ],
            'stats' => [
                'total_views' => $totalViews,
                'favorites_count' => $favoritesCount,
                'inquiries_count' => $inquiriesCount,
                'engagement_rate' => $engagementRate . '%',
                'views_last_period' => array_sum($chartSeries),
            ],
            'boosts' => $boostDetails,
            'chart' => [
                'labels' => $chartLabels,
                'series' => $chartSeries,
            ],
        ];
    }
}
