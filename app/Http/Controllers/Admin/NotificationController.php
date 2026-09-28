<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\UserVerification;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {}

    /**
     * Display the dedicated admin notifications and moderation queue hub.
     */
    public function index(Request $request): View
    {
        $category = $request->query('category', 'all');
        $status = $request->query('status', 'pending');
        $search = $request->query('search');

        // Aggregated stats
        $unresolvedReports = Report::whereNull('reviewed_at')->count();
        $pendingVerifications = UserVerification::where('status', 'pending')->count();
        $totalUrgent = $unresolvedReports + $pendingVerifications;

        $resolvedReportsCount = Report::whereNotNull('reviewed_at')->count();
        $approvedVerificationsCount = UserVerification::where('status', 'approved')->count();

        // Query Reports
        $reportsQuery = Report::with(['reporter', 'reviewer', 'reportable'])->latest();
        if ($status === 'pending') {
            $reportsQuery->whereNull('reviewed_at');
        } elseif ($status === 'resolved') {
            $reportsQuery->whereNotNull('reviewed_at');
        }

        if ($search) {
            $reportsQuery->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('reporter', function ($userQ) use ($search) {
                        $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Query Verifications
        $verificationsQuery = UserVerification::with(['user', 'reviewer'])->latest();
        if ($status === 'pending') {
            $verificationsQuery->where('status', 'pending');
        } elseif ($status === 'resolved') {
            $verificationsQuery->whereIn('status', ['approved', 'rejected']);
        }

        if ($search) {
            $verificationsQuery->where(function ($q) use ($search) {
                $q->where('id_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQ) use ($search) {
                        $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $reports = ($category === 'all' || $category === 'reports') 
            ? $reportsQuery->paginate(10, ['*'], 'reports_page')->withQueryString() 
            : null;

        $verifications = ($category === 'all' || $category === 'verifications') 
            ? $verificationsQuery->paginate(10, ['*'], 'verifications_page')->withQueryString() 
            : null;

        return view('admin.notifications.index', compact(
            'category',
            'status',
            'search',
            'unresolvedReports',
            'pendingVerifications',
            'totalUrgent',
            'resolvedReportsCount',
            'approvedVerificationsCount',
            'reports',
            'verifications'
        ));
    }
}
