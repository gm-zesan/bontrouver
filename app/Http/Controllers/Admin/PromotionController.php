<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    /**
     * Display listing promotion packages and recent boost transaction ledger.
     */
    public function index(Request $request): View|JsonResponse
    {
        $packages = PromotionPackage::orderBy('sort_order')->orderBy('price')->get();

        $completedPromotions = ListingPromotion::where('payment_status', 'completed');
        $totalRevenue = (float) (clone $completedPromotions)->sum('price_paid');
        $totalPointsRedeemed = (int) (clone $completedPromotions)->sum('points_spent');
        $paidOrdersCount = (clone $completedPromotions)->where('payment_method', 'stripe')->where('price_paid', '>', 0)->count();
        $pointsOrdersCount = (clone $completedPromotions)->where('payment_method', 'points')->count();
        $totalPromotions = ListingPromotion::count();
        $activePromotions = ListingPromotion::active()->count();

        $avgOrderValue = $paidOrdersCount > 0 ? round($totalRevenue / $paidOrdersCount, 2) : 0.00;

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_points_redeemed' => $totalPointsRedeemed,
            'active_promotions' => $activePromotions,
            'total_promotions' => $totalPromotions,
            'paid_orders_count' => $paidOrdersCount,
            'points_orders_count' => $pointsOrdersCount,
            'avg_order_value' => $avgOrderValue,
        ];

        // 30-day Daily Revenue Trend (CAD $)
        $days = collect(range(29, 0))->map(fn ($daysAgo) => now()->subDays($daysAgo)->format('Y-m-d'));
        $dailyRevenues = ListingPromotion::where('payment_status', 'completed')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(price_paid) as daily_total, COUNT(*) as orders_count')
            ->groupBy('date')
            ->pluck('daily_total', 'date');

        $chartDates = [];
        $chartRevenues = [];
        foreach ($days as $day) {
            $chartDates[] = \Carbon\Carbon::parse($day)->format('M d');
            $chartRevenues[] = (float) ($dailyRevenues->get($day) ?? 0);
        }

        // Revenue Share by Boost Type
        $revenueByType = ListingPromotion::where('payment_status', 'completed')
            ->selectRaw('type, SUM(price_paid) as total_cad, COUNT(*) as total_count')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $typeSeries = [
            'sponsored' => (float) ($revenueByType->get('sponsored')?->total_cad ?? 0),
            'featured' => (float) ($revenueByType->get('featured')?->total_cad ?? 0),
            'bump_up' => (float) ($revenueByType->get('bump_up')?->total_cad ?? 0),
        ];

        $promotions = ListingPromotion::with(['listing', 'user', 'package'])
            ->latest()
            ->get();

        $quotaSettings = [
            'free_listing_limit_per_user' => (int) site_setting('free_listing_limit_per_user', 5),
            'enable_listing_promotions' => (bool) site_setting('enable_listing_promotions', true),
            'auto_approve_listings' => (bool) site_setting('auto_approve_listings', true),
        ];

        $analytics = [
            'chart_dates' => $chartDates,
            'chart_revenues' => $chartRevenues,
            'type_series' => array_values($typeSeries),
            'type_labels' => ['Sponsored Spotlight', 'Featured Highlight', 'Instant Bump-Up'],
            'payment_split' => [$paidOrdersCount, $pointsOrdersCount],
        ];

        if ($request->ajax() && $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'packages' => $packages,
                'stats' => $stats,
                'analytics' => $analytics,
                'promotions' => $promotions,
                'quota_settings' => $quotaSettings,
            ]);
        }

        return view('admin.promotions.index', compact('packages', 'stats', 'analytics', 'promotions', 'quotaSettings'));
    }

    /**
     * Update package pricing and features.
     */
    public function updatePackage(Request $request, PromotionPackage $package): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'point_cost' => ['nullable', 'integer', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:0'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'badge_color' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $package->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Promotion package '{$package->name}' updated successfully.",
                'package' => $package,
            ]);
        }

        return redirect()->route('admin.promotions.index')->with('success', "Package '{$package->name}' updated successfully.");
    }

    /**
     * Update marketplace freemium listing quota and rules.
     */
    public function updateQuota(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'free_listing_limit_per_user' => ['required', 'integer', 'min:0'],
            'enable_listing_promotions' => ['nullable', 'boolean'],
            'auto_approve_listings' => ['nullable', 'boolean'],
        ]);

        \App\Models\SiteSetting::set('free_listing_limit_per_user', $validated['free_listing_limit_per_user'], 'marketplace', 'number');
        \App\Models\SiteSetting::set('enable_listing_promotions', $request->boolean('enable_listing_promotions') ? '1' : '0', 'marketplace', 'boolean');
        \App\Models\SiteSetting::set('auto_approve_listings', $request->boolean('auto_approve_listings') ? '1' : '0', 'marketplace', 'boolean');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Marketplace listing quota rules updated successfully.',
            ]);
        }

        return redirect()->route('admin.promotions.index')->with('success', 'Marketplace listing quota rules updated successfully.');
    }
}
