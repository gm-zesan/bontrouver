<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\SiteSetting;
use App\Services\AdminDashboardService;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AdminDashboardService $dashboardService
    ) {
    }

    /**
     * Display the Admin Dashboard with full analytical metrics.
     */
    public function index(Request $request): View|JsonResponse
    {
        $data = $this->dashboardService->getDashboardMetrics();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return view('admin.dashboard', $data);
    }

    /**
     * Clear all application caches, category navigation trees, Canadian cities map,
     * site settings, and compiled framework views.
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            // 1. Flush application cache
            Cache::flush();

            // 2. Clear category trees & navigation caches
            CategoryService::clearCache();

            // 3. Clear Canadian cities map cache
            City::clearMapCache();

            // 4. Clear site settings cache
            SiteSetting::flushCache();

            // 5. Clear framework compiled views and config cache
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('optimize:clear');

            return response()->json([
                'success' => true,
                'message' => 'System cache, views, and navigation trees cleared successfully!',
            ]);
        } catch (Throwable $e) {
            Log::error('Admin clearCache failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear system cache: ' . $e->getMessage(),
            ], 500);
        }
    }
}

