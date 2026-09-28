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

        $stats = [
            'total_revenue' => ListingPromotion::where('payment_status', 'completed')->sum('price_paid'),
            'total_points_redeemed' => ListingPromotion::where('payment_status', 'completed')->sum('points_spent'),
            'active_promotions' => ListingPromotion::active()->count(),
            'total_promotions' => ListingPromotion::count(),
        ];

        $promotions = ListingPromotion::with(['listing', 'user', 'package'])
            ->latest()
            ->get();

        $quotaSettings = [
            'free_listing_limit_per_user' => (int) site_setting('free_listing_limit_per_user', 5),
            'enable_listing_promotions' => (bool) site_setting('enable_listing_promotions', true),
            'auto_approve_listings' => (bool) site_setting('auto_approve_listings', true),
        ];

        if ($request->ajax() && $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'packages' => $packages,
                'stats' => $stats,
                'promotions' => $promotions,
                'quota_settings' => $quotaSettings,
            ]);
        }

        return view('admin.promotions.index', compact('packages', 'stats', 'promotions', 'quotaSettings'));
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
