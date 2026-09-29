<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkReportActionRequest;
use App\Http\Requests\Admin\ResolveReportRequest;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use App\Services\AdminReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function __construct(
        protected AdminReportService $reportService
    ) {}

    /**
     * Display a listing of reports & safety flags for administrators.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'status' => $request->get('status'),
                'reason' => $request->get('reason'),
                'target_type' => $request->get('target_type'),
            ];

            $query = $this->reportService->getReportsQuery($filters);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check m-0"><input class="form-check-input report-checkbox border-secondary" type="checkbox" value="' . $row->id . '"></div>';
                })
                ->editColumn('id', function ($row) {
                    return '<span class="fw-bold text-dark" style="font-size: 13px;">#' . $row->id . '</span>';
                })
                ->addColumn('reporter', function ($row) {
                    if (!$row->reporter) {
                        return '<span class="text-muted small">System / Deleted</span>';
                    }
                    $avatar = $row->reporter->avatar_url;
                    $name = e($row->reporter->name);
                    $profileUrl = route('admin.users.show', $row->reporter->id);
                    $email = e($row->reporter->email);

                    return '<div class="d-flex align-items-center">
                        <img src="' . $avatar . '" class="rounded-circle me-2 object-fit-cover shadow-xs" style="width: 32px; height: 32px;" onerror="this.src=\'https://placehold.co/60x60?text=U\'">
                        <div class="d-flex flex-column min-w-0">
                            <a href="' . $profileUrl . '" class="fw-semibold text-dark text-decoration-none text-truncate" style="font-size: 13px;">' . $name . '</a>
                            <span class="text-muted text-truncate" style="font-size: 11.5px;">' . $email . '</span>
                        </div>
                    </div>';
                })
                ->addColumn('target', function ($row) {
                    $reportable = $row->reportable;
                    if (!$reportable) {
                        return '<span class="badge bg-secondary-subtle text-secondary border">Item Removed / N/A</span>';
                    }

                    if ($reportable instanceof Listing) {
                        $img = $reportable->primary_image_url;
                        $url = route('admin.listings.show', $reportable->id);
                        $title = e($reportable->title);
                        $price = $reportable->price_type === 'free' ? 'FREE' : '$' . number_format($reportable->price, 2);

                        return '<div class="d-flex align-items-center">
                            <img src="' . $img . '" class="rounded-2 me-2 object-fit-cover shadow-xs" style="width: 36px; height: 36px;" onerror="this.onerror=null; this.src=\'' . asset('images/no-image.svg') . '\'">
                            <div class="d-flex flex-column min-w-0">
                                <span class="badge bg-primary-subtle text-primary border mb-0.5" style="font-size: 10px; width: fit-content;">Listing</span>
                                <a href="' . $url . '" class="fw-semibold text-dark text-decoration-none text-truncate" style="font-size: 12.5px; max-width: 180px;">' . $title . '</a>
                                <span class="text-muted" style="font-size: 11px;">' . $price . '</span>
                            </div>
                        </div>';
                    }

                    if ($reportable instanceof User) {
                        $avatar = $reportable->avatar_url;
                        $url = route('admin.users.show', $reportable->id);
                        $name = e($reportable->name);
                        $suspendedBadge = $reportable->is_suspended ? '<span class="badge bg-danger ms-1" style="font-size: 9.5px;">Suspended</span>' : '';

                        return '<div class="d-flex align-items-center">
                            <img src="' . $avatar . '" class="rounded-circle me-2 object-fit-cover shadow-xs" style="width: 36px; height: 36px;" onerror="this.src=\'https://placehold.co/60x60?text=U\'">
                            <div class="d-flex flex-column min-w-0">
                                <span class="badge bg-warning-subtle text-dark border mb-0.5" style="font-size: 10px; width: fit-content;">User Account</span>
                                <a href="' . $url . '" class="fw-semibold text-dark text-decoration-none text-truncate" style="font-size: 12.5px; max-width: 180px;">' . $name . '</a>
                                ' . $suspendedBadge . '
                            </div>
                        </div>';
                    }

                    if ($reportable instanceof CompanionshipRequest) {
                        $url = route('admin.meetups.show', $reportable->id);
                        $title = e($reportable->title);
                        $activity = ucfirst($reportable->activity_type ?? 'Meetup');

                        return '<div class="d-flex flex-column min-w-0">
                            <span class="badge bg-info-subtle text-info border mb-0.5" style="font-size: 10px; width: fit-content;">Meetup (' . $activity . ')</span>
                            <a href="' . $url . '" class="fw-semibold text-dark text-decoration-none text-truncate" style="font-size: 12.5px; max-width: 180px;">' . $title . '</a>
                            <span class="text-muted" style="font-size: 11px;">' . e($reportable->city ?? 'Canada') . '</span>
                        </div>';
                    }

                    return '<span class="badge bg-light text-secondary border">Entity</span>';
                })
                ->editColumn('reason', function ($row) {
                    $reasonEnum = $row->reason instanceof ReportReason ? $row->reason : (ReportReason::tryFrom($row->reason) ?? ReportReason::OTHER);
                    $colorClass = match ($reasonEnum) {
                        ReportReason::FRAUD => 'bg-danger-subtle text-danger border border-danger-subtle',
                        ReportReason::HARASSMENT => 'bg-danger-subtle text-danger border border-danger-subtle',
                        ReportReason::INAPPROPRIATE => 'bg-warning-subtle text-dark border border-warning-subtle',
                        ReportReason::SPAM => 'bg-secondary-subtle text-secondary border',
                        default => 'bg-light text-dark border',
                    };

                    $icon = match ($reasonEnum) {
                        ReportReason::FRAUD => '<i class="ri-alert-fill text-danger me-1"></i>',
                        ReportReason::HARASSMENT => '<i class="ri-user-unfollow-line text-danger me-1"></i>',
                        ReportReason::INAPPROPRIATE => '<i class="ri-forbid-line text-warning me-1"></i>',
                        ReportReason::SPAM => '<i class="ri-spam-2-line text-secondary me-1"></i>',
                        default => '<i class="ri-flag-line text-secondary me-1"></i>',
                    };

                    return '<span class="badge ' . $colorClass . ' fw-semibold" style="font-size: 11.5px; padding: 4px 8px;">' . $icon . $reasonEnum->label() . '</span>';
                })
                ->editColumn('description', function ($row) {
                    if (!$row->description) {
                        return '<span class="text-muted fst-italic small">No additional description provided.</span>';
                    }
                    $text = e($row->description);
                    if (mb_strlen($text) > 80) {
                        return '<span class="text-dark" style="font-size: 12.5px;" title="' . $text . '">' . mb_substr($text, 0, 78) . '...</span>';
                    }
                    return '<span class="text-dark" style="font-size: 12.5px;">' . $text . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $status = strtolower($row->status ?? 'pending');
                    return match ($status) {
                        'resolved' => '<span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size: 11.5px; padding: 4px 8px;"><i class="ri-checkbox-circle-fill me-1"></i> Resolved</span>',
                        'dismissed' => '<span class="badge bg-secondary-subtle text-secondary border fw-medium" style="font-size: 11.5px; padding: 4px 8px;"><i class="ri-close-circle-line me-1"></i> Dismissed</span>',
                        default => '<span class="badge bg-warning-subtle text-dark border border-warning-subtle fw-bold" style="font-size: 11.5px; padding: 4px 8px;"><i class="ri-time-line me-1"></i> Pending Review</span>',
                    };
                })
                ->editColumn('created_at', function ($row) {
                    return '<div class="d-flex flex-column" style="white-space: nowrap;">
                        <span class="text-dark fw-medium" style="font-size: 12.5px;">' . $row->created_at->format('M d, Y') . ' <span class="text-muted" style="font-size: 11px;">' . $row->created_at->format('H:i') . '</span></span>
                        <span class="text-muted" style="font-size: 11px;">' . $row->created_at->diffForHumans() . '</span>
                    </div>';
                })
                ->addColumn('action', function ($row) {
                    $status = strtolower($row->status ?? 'pending');
                    $reportJson = htmlspecialchars(json_encode([
                        'id' => $row->id,
                        'reason' => $row->reason instanceof ReportReason ? $row->reason->label() : ($row->reason ?? 'Other'),
                        'description' => $row->description ?? '',
                        'status' => $row->status,
                        'created_at' => $row->created_at->format('M d, Y H:i'),
                        'reporter_name' => $row->reporter->name ?? 'System',
                        'reporter_email' => $row->reporter->email ?? 'N/A',
                        'target_type' => class_basename($row->reportable_type),
                        'target_id' => $row->reportable_id,
                        'reviewed_by' => $row->reviewer->name ?? null,
                        'reviewed_at' => $row->reviewed_at ? $row->reviewed_at->format('M d, Y H:i') : null,
                    ]), ENT_QUOTES, 'UTF-8');

                    $btn = '<div class="action-btn d-flex align-items-center justify-content-end gap-1">';

                    // View / Inspect button
                    $btn .= '<button type="button" class="btn btn-sm btn-light border" onclick="openReportInspector(' . $reportJson . ')" title="Inspect Report Details" style="padding: 4px 8px; background: #fff;"><i class="ri-eye-line text-primary"></i></button>';

                    if ($status === 'pending') {
                        // Quick Resolve Button
                        $btn .= '<button type="button" class="btn btn-sm btn-light border" onclick="openResolveModal(' . $row->id . ', \'' . class_basename($row->reportable_type) . '\')" title="Resolve & Take Action" style="padding: 4px 8px; background: #fff;"><i class="ri-checkbox-circle-line text-success"></i></button>';

                        // Quick Dismiss Button
                        $btn .= '<button type="button" class="btn btn-sm btn-light border" onclick="openDismissModal(' . $row->id . ')" title="Dismiss Report" style="padding: 4px 8px; background: #fff;"><i class="ri-close-circle-line text-danger"></i></button>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['checkbox', 'id', 'reporter', 'target', 'reason', 'description', 'status', 'created_at', 'action'])
                ->make(true);
        }

        $stats = $this->reportService->getStats();
        $reasons = ReportReason::cases();

        return view('admin.reports.index', compact('stats', 'reasons'));
    }

    /**
     * Show report details JSON for inspection modal.
     */
    public function show(Report $report): JsonResponse
    {
        $report->load(['reporter', 'reviewer', 'reportable']);

        return response()->json([
            'success' => true,
            'report' => [
                'id' => $report->id,
                'status' => $report->status,
                'reason' => $report->reason instanceof ReportReason ? $report->reason->label() : ($report->reason ?? 'Other'),
                'description' => $report->description,
                'created_at' => $report->created_at->format('M d, Y H:i:s'),
                'reviewed_at' => $report->reviewed_at ? $report->reviewed_at->format('M d, Y H:i:s') : null,
                'reporter' => $report->reporter ? [
                    'id' => $report->reporter->id,
                    'name' => $report->reporter->name,
                    'email' => $report->reporter->email,
                    'avatar' => $report->reporter->avatar_url,
                ] : null,
                'reviewer' => $report->reviewer ? [
                    'id' => $report->reviewer->id,
                    'name' => $report->reviewer->name,
                ] : null,
                'target_type' => class_basename($report->reportable_type),
                'target_id' => $report->reportable_id,
            ],
        ]);
    }

    /**
     * Resolve a report with optional disciplinary action.
     */
    public function resolve(ResolveReportRequest $request, Report $report): JsonResponse
    {
        $result = $this->reportService->resolveReport(
            report: $report,
            reviewer: $request->user(),
            notes: $request->validated('notes'),
            action: $request->validated('action', 'none')
        );

        return response()->json([
            'success' => true,
            'message' => 'Report #' . $report->id . ' has been resolved successfully.' . ($result['action_taken'] !== 'none' ? ' ' . $result['action_taken'] : ''),
        ]);
    }

    /**
     * Dismiss a report as invalid.
     */
    public function dismiss(Request $request, Report $report): JsonResponse
    {
        $notes = $request->input('notes');
        $this->reportService->dismissReport($report, $request->user(), $notes);

        return response()->json([
            'success' => true,
            'message' => 'Report #' . $report->id . ' has been dismissed.',
        ]);
    }

    /**
     * Perform bulk moderation action on selected reports.
     */
    public function bulkAction(BulkReportActionRequest $request): JsonResponse
    {
        $result = $this->reportService->bulkAction(
            action: $request->validated('action'),
            reportIds: $request->validated('ids'),
            reviewer: $request->user(),
            notes: $request->validated('notes')
        );

        $actionVerb = match ($result['action']) {
            'resolve' => 'resolved',
            'dismiss' => 'dismissed',
            'delete' => 'deleted',
            default => 'processed',
        };

        return response()->json([
            'success' => true,
            'message' => "Successfully {$actionVerb} {$result['count']} report(s).",
        ]);
    }
}
