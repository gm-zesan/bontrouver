<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkVerificationActionRequest;
use App\Http\Requests\ReviewVerificationRequest;
use App\Models\UserVerification;
use App\Services\VerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class VerificationReviewController extends Controller
{
    public function __construct(
        private readonly VerificationService $verificationService
    ) {}

    /**
     * Display the Admin Canadian ID Verification Queue.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'status' => $request->get('status'),
                'document_type' => $request->get('document_type'),
            ];

            $query = $this->verificationService->getVerificationsQuery($filters);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check m-0"><input class="form-check-input verification-checkbox border-secondary" type="checkbox" value="' . $row->id . '"></div>';
                })
                ->editColumn('id', function ($row) {
                    return '<span class="fw-bold text-dark" style="font-size: 13px;">#' . $row->id . '</span>';
                })
                ->addColumn('user', function ($row) {
                    if (!$row->user) {
                        return '<span class="text-muted small">Deleted User</span>';
                    }
                    $avatar = $row->user->avatar_url;
                    $name = e($row->user->name);
                    $email = e($row->user->email);
                    $profileUrl = route('admin.users.show', $row->user->id);
                    $isVerified = $row->user->is_verified ? '<i class="ri-verified-badge-fill text-primary ms-1" style="font-size: 14px;" title="Verified"></i>' : '';

                    return '<div class="d-flex align-items-center">
                        <img src="' . $avatar . '" class="rounded-circle me-2 object-fit-cover shadow-xs" style="width: 34px; height: 34px;" onerror="this.src=\'https://placehold.co/60x60?text=U\'">
                        <div class="d-flex flex-column min-w-0">
                            <a href="' . $profileUrl . '" class="fw-semibold text-dark text-decoration-none text-truncate" style="font-size: 13px;">' . $name . $isVerified . '</a>
                            <span class="text-muted text-truncate" style="font-size: 11.5px;">' . $email . '</span>
                        </div>
                    </div>';
                })
                ->editColumn('document_type', function ($row) {
                    $docType = $row->document_type ?? 'government_id';
                    $label = ucwords(str_replace('_', ' ', $docType));

                    $icon = match ($docType) {
                        'drivers_license' => '<i class="ri-car-line me-1 text-primary"></i>',
                        'passport' => '<i class="ri-passport-line me-1 text-info"></i>',
                        'dealer_license' => '<i class="ri-building-4-line me-1 text-warning"></i>',
                        default => '<i class="ri-id-card-line me-1 text-secondary"></i>',
                    };

                    return '<span class="badge bg-light text-dark border fw-medium" style="font-size: 11.5px; padding: 4px 8px;">' . $icon . $label . '</span>';
                })
                ->editColumn('id_number', function ($row) {
                    if (!$row->id_number) {
                        return '<span class="text-muted small">N/A</span>';
                    }
                    return '<span class="font-monospace text-dark fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">' . e($row->id_number) . '</span>';
                })
                ->addColumn('document_preview', function ($row) {
                    if (!$row->document_path) {
                        return '<span class="badge bg-secondary-subtle text-secondary border">No File</span>';
                    }

                    $filePath = $row->document_path;
                    $url = str_starts_with($filePath, 'http') ? $filePath : (str_starts_with($filePath, '/storage') ? asset($filePath) : asset('storage/' . $filePath));
                    $isPdf = str_ends_with(strtolower($filePath), '.pdf');

                    if ($isPdf) {
                        return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1" style="font-size: 11.5px; padding: 3px 8px; background: #fff;" title="View PDF Document">
                            <i class="ri-file-pdf-fill text-danger" style="font-size: 14px;"></i> View PDF
                        </a>';
                    }

                    return '<div class="d-flex align-items-center gap-1.5">
                        <img src="' . $url . '" class="rounded border object-fit-cover shadow-xs cursor-pointer verification-thumbnail" style="width: 38px; height: 38px;" onclick="openDocumentLightbox(\'' . $url . '\')" onerror="this.src=\'https://placehold.co/60x60?text=Doc\'" title="Click to enlarge">
                        <button type="button" class="btn btn-sm btn-light border p-1" onclick="openDocumentLightbox(\'' . $url . '\')" title="Inspect Document Scan">
                            <i class="ri-zoom-in-line text-primary" style="font-size: 13px;"></i>
                        </button>
                    </div>';
                })
                ->editColumn('status', function ($row) {
                    $status = strtolower($row->status ?? 'pending');
                    return match ($status) {
                        'approved' => '<span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size: 11.5px; padding: 4px 8px;"><i class="ri-checkbox-circle-fill me-1"></i> Approved</span>',
                        'rejected' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-medium" style="font-size: 11.5px; padding: 4px 8px;"><i class="ri-close-circle-line me-1"></i> Rejected</span>',
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

                    $btn = '<div class="action-btn d-flex align-items-center justify-content-end gap-1">';

                    // View / Inspect button (opens 2-column modal)
                    $btn .= '<button type="button" class="btn btn-sm btn-light border" onclick="openVerificationInspector(' . $row->id . ')" title="Inspect Verification Document" style="padding: 4px 8px; background: #fff;"><i class="ri-eye-line text-primary"></i></button>';

                    if ($status === 'pending') {
                        // Approve button
                        $approveUrl = route('admin.verifications.approve', $row->id);
                        $btn .= '<button type="button" class="btn btn-sm btn-light border btn-confirm-modal" data-action="' . $approveUrl . '" data-method="POST" data-title="Approve Canadian ID Verification" data-desc="Are you sure you want to approve ' . e($row->user->name ?? 'this user') . '\'s identity verification? This will award +50 Community Points and activate their Verified Badge." data-btn-class="btn-success" data-btn-text="Yes, Approve" title="Approve Verification" style="padding: 4px 8px; background: #fff;"><i class="ri-checkbox-circle-line text-success"></i></button>';

                        // Reject button (opens reject modal)
                        $btn .= '<button type="button" class="btn btn-sm btn-light border" onclick="openRejectModal(' . $row->id . ', \'' . e($row->user->name ?? 'User') . '\')" title="Reject Document" style="padding: 4px 8px; background: #fff;"><i class="ri-close-circle-line text-danger"></i></button>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['checkbox', 'id', 'user', 'document_type', 'id_number', 'document_preview', 'status', 'created_at', 'action'])
                ->make(true);
        }

        $stats = $this->verificationService->getStats();

        return view('admin.verifications.index', compact('stats'));
    }

    /**
     * Show verification details JSON for 2-column inspector modal.
     */
    public function show(UserVerification $verification): JsonResponse
    {
        $verification->load(['user', 'reviewer']);

        $filePath = $verification->document_path;
        $url = $filePath ? (str_starts_with($filePath, 'http') ? $filePath : (str_starts_with($filePath, '/storage') ? asset($filePath) : asset('storage/' . $filePath))) : null;
        
        $extension = $filePath ? strtolower(pathinfo(parse_url($filePath, PHP_URL_PATH), PATHINFO_EXTENSION)) : '';
        $isPdf = in_array($extension, ['pdf']);
        $isDoc = in_array($extension, ['doc', 'docx']);
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']) || (str_starts_with($filePath ?? '', 'http') && !$isPdf && !$isDoc);
        $fileName = $filePath ? basename(parse_url($filePath, PHP_URL_PATH)) : 'document';

        return response()->json([
            'success' => true,
            'verification' => [
                'id' => $verification->id,
                'status' => $verification->status,
                'document_type' => ucwords(str_replace('_', ' ', $verification->document_type ?? 'government_id')),
                'id_number' => $verification->id_number ?: 'Not provided',
                'phone_number' => $verification->phone_number ?: ($verification->user->phone ?? 'Not provided'),
                'document_url' => $url,
                'file_extension' => $extension,
                'file_name' => $fileName,
                'is_pdf' => $isPdf,
                'is_doc' => $isDoc,
                'is_image' => $isImage,
                'rejection_reason' => $verification->rejection_reason,
                'created_at' => $verification->created_at->format('M d, Y H:i:s'),
                'reviewed_at' => $verification->reviewed_at ? $verification->reviewed_at->format('M d, Y H:i:s') : null,
                'user' => $verification->user ? [
                    'id' => $verification->user->id,
                    'name' => $verification->user->name,
                    'email' => $verification->user->email,
                    'phone' => $verification->user->phone ?? 'N/A',
                    'city' => $verification->user->city ?? 'Canada',
                    'province' => $verification->user->province ?? '',
                    'avatar' => $verification->user->avatar_url,
                    'is_verified' => (bool) $verification->user->is_verified,
                    'is_dealer' => (bool) $verification->user->is_dealer,
                    'community_points' => $verification->user->community_points ?? 0,
                    'member_tier' => $verification->user->member_tier['name'] ?? 'New Member',
                    'email_verified' => (bool) $verification->user->email_verified_at,
                    'created_at' => $verification->user->created_at->format('M d, Y'),
                ] : null,
                'reviewer' => $verification->reviewer ? [
                    'id' => $verification->reviewer->id,
                    'name' => $verification->reviewer->name,
                ] : null,
            ],
        ]);
    }

    /**
     * Approve a verification request.
     */
    public function approve(Request $request, UserVerification $verification): JsonResponse|RedirectResponse
    {
        $this->verificationService->reviewVerification(
            $verification,
            'approved',
            null,
            Auth::user()
        );

        $msg = "Verification for user {$verification->user->name} has been approved (+50 points awarded).";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('status', $msg);
    }

    /**
     * Reject a verification request.
     */
    public function reject(Request $request, UserVerification $verification): JsonResponse|RedirectResponse
    {
        $reason = $request->input('reason') ?: 'Document unreadable, invalid, or expired.';

        $this->verificationService->reviewVerification(
            $verification,
            'rejected',
            $reason,
            Auth::user()
        );

        $msg = "Verification for user {$verification->user->name} was marked as rejected.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('status', $msg);
    }

    /**
     * Bulk action on verification requests.
     */
    public function bulkAction(BulkVerificationActionRequest $request): JsonResponse
    {
        $result = $this->verificationService->bulkAction(
            action: $request->validated('action'),
            ids: $request->validated('ids'),
            reviewer: $request->user(),
            reason: $request->validated('reason')
        );

        $actionVerb = match ($result['action']) {
            'approve' => 'approved',
            'reject' => 'rejected',
            'delete' => 'deleted',
            default => 'processed',
        };

        return response()->json([
            'success' => true,
            'message' => "Successfully {$actionVerb} {$result['count']} verification request(s).",
        ]);
    }
}
