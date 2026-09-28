<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerAd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerAdController extends Controller
{
    /**
     * Display listing of banner ads and AdSense slots.
     */
    public function index(Request $request): View|JsonResponse
    {
        $banners = BannerAd::orderBy('position')->orderBy('sort_order')->get();

        $stats = [
            'total_banners' => BannerAd::count(),
            'active_banners' => BannerAd::where('is_active', true)->count(),
            'total_impressions' => BannerAd::sum('impressions_count'),
            'total_clicks' => BannerAd::sum('clicks_count'),
        ];

        if ($request->ajax() && $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'banners' => $banners,
                'stats' => $stats,
            ]);
        }

        return view('admin.banners.index', compact('banners', 'stats'));
    }

    /**
     * Store a newly created banner ad.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'position' => ['required', 'in:search_sidebar,listing_detail_bottom,homepage_leaderboard,homepage_top,homepage_middle,homepage_bottom,community_sidebar'],
            'image_path' => ['nullable', 'string', 'max:500'],
            'target_url' => ['nullable', 'url', 'max:500'],
            'html_code' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $banner = BannerAd::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Banner ad created successfully.',
                'banner' => $banner,
            ]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner ad created successfully.');
    }

    /**
     * Update an existing banner ad.
     */
    public function update(Request $request, BannerAd $banner): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'position' => ['required', 'in:search_sidebar,listing_detail_bottom,homepage_leaderboard,homepage_top,homepage_middle,homepage_bottom,community_sidebar'],
            'image_path' => ['nullable', 'string', 'max:500'],
            'target_url' => ['nullable', 'url', 'max:500'],
            'html_code' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? $banner->sort_order;

        $banner->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Banner ad updated successfully.',
                'banner' => $banner,
            ]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner ad updated successfully.');
    }

    /**
     * Toggle banner active status.
     */
    public function toggleStatus(BannerAd $banner): JsonResponse
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $banner->is_active,
            'message' => 'Banner status updated.',
        ]);
    }

    /**
     * Remove the specified banner ad.
     */
    public function destroy(BannerAd $banner): RedirectResponse|JsonResponse
    {
        $banner->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Banner ad deleted successfully.',
            ]);
        }

        return redirect()->route('admin.banners.index')->with('success', 'Banner ad deleted successfully.');
    }
}
