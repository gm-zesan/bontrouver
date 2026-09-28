<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AdminDashboardService $dashboardService
    ) {}

    /**
     * Display the Admin Dashboard with full analytical metrics.
     */
    public function index(Request $request): View|JsonResponse
    {
        $data = $this->dashboardService->getDashboardMetrics();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        }

        return view('admin.dashboard', $data);
    }
}
