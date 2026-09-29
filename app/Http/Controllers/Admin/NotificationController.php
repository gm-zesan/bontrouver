<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\UserVerification;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
        $perPage = (int) $request->query('per_page', 15);

        // 1. KPI Stats
        $stats = [
            'total' => Report::count() + UserVerification::count(),
            'pending' => Report::whereNull('reviewed_at')->count() + UserVerification::where('status', 'pending')->count(),
            'reports_pending' => Report::whereNull('reviewed_at')->count(),
            'verifications_pending' => UserVerification::where('status', 'pending')->count(),
            'resolved' => Report::whereNotNull('reviewed_at')->count() + UserVerification::whereIn('status', ['approved', 'rejected'])->count(),
        ];

        // 2. Fetch Reports Items
        $reportItems = collect();
        if ($category === 'all' || $category === 'reports') {
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

            $reportItems = $reportsQuery->get()->map(function ($report) {
                $reasonLabel = $report->reason?->label() ?? 'Safety Concern';
                $targetType = class_basename($report->reportable_type ?? 'Listing');
                $targetTitle = $report->reportable?->title ?? $report->reportable?->name ?? "Entity #{$report->reportable_id}";
                $isResolved = !empty($report->reviewed_at);

                return (object) [
                    'id' => 'report-' . $report->id,
                    'type' => 'report',
                    'type_label' => 'Safety Report',
                    'type_badge' => 'bg-danger-subtle text-danger border border-danger-subtle',
                    'type_icon' => 'ri-flag-2-line',
                    'title' => $reasonLabel,
                    'subtitle' => "Target: [{$targetType}] " . $targetTitle,
                    'description' => $report->description ?? 'Safety flag submitted by community member.',
                    'user_name' => $report->reporter?->name ?? 'Community Member',
                    'user_email' => $report->reporter?->email ?? 'N/A',
                    'user_initial' => substr($report->reporter?->name ?? 'U', 0, 1),
                    'created_at' => $report->created_at,
                    'status' => $isResolved ? 'resolved' : 'pending',
                    'status_label' => $isResolved ? 'Resolved' : 'Pending Review',
                    'status_badge' => $isResolved ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle',
                    'action_url' => route('admin.reports.show', $report->id),
                    'action_label' => 'Inspect',
                    'action_icon' => 'ri-eye-line',
                    'action_class' => 'btn-outline-danger',
                ];
            });
        }

        // 3. Fetch ID Verification Items
        $verificationItems = collect();
        if ($category === 'all' || $category === 'verifications') {
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

            $verificationItems = $verificationsQuery->get()->map(function ($verif) {
                $docTypeLabel = ucfirst(str_replace('_', ' ', $verif->document_type ?? 'Government ID'));
                $userLocation = ($verif->user?->city ?? 'Canada') . ($verif->user?->province ? ', ' . $verif->user->province : '');
                $isPending = $verif->status === 'pending';
                $isApproved = $verif->status === 'approved';

                $statusLabel = $isPending ? 'Pending Review' : ($isApproved ? 'Approved' : 'Rejected');
                $statusBadge = $isPending 
                    ? 'bg-warning-subtle text-warning border border-warning-subtle' 
                    : ($isApproved ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle');

                return (object) [
                    'id' => 'verification-' . $verif->id,
                    'type' => 'verification',
                    'type_label' => 'ID Verification',
                    'type_badge' => 'bg-primary-subtle text-primary border border-primary-subtle',
                    'type_icon' => 'ri-shield-user-line',
                    'title' => $docTypeLabel,
                    'subtitle' => 'Doc ID: ' . ($verif->id_number ?? 'Masked') . ' (' . $userLocation . ')',
                    'description' => 'Canadian ' . $docTypeLabel . ' submitted for identity verification.',
                    'user_name' => $verif->user?->name ?? 'Applicant',
                    'user_email' => $verif->user?->email ?? 'N/A',
                    'user_initial' => substr($verif->user?->name ?? 'A', 0, 1),
                    'created_at' => $verif->created_at,
                    'status' => $verif->status,
                    'status_label' => $statusLabel,
                    'status_badge' => $statusBadge,
                    'action_url' => route('admin.verifications.index'),
                    'action_label' => 'Review ID',
                    'action_icon' => 'ri-shield-check-line',
                    'action_class' => 'btn-outline-primary',
                ];
            });
        }

        // 4. Merge, Sort & Paginate
        $merged = $reportItems->concat($verificationItems)
            ->sortByDesc('created_at')
            ->values();

        $page = (int) $request->query('page', 1);
        $totalItems = $merged->count();
        $slice = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $notifications = new LengthAwarePaginator(
            $slice,
            $totalItems,
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('admin.notifications.index', compact(
            'stats',
            'notifications',
            'category',
            'status',
            'search',
            'perPage'
        ));
    }
}
