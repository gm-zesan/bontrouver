<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewVerificationRequest;
use App\Models\UserVerification;
use App\Services\VerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationReviewController extends Controller
{
    public function __construct(
        private readonly VerificationService $verificationService
    ) {}

    /**
     * Display the Admin Verification Queue.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        $verifications = $this->verificationService->getAllVerifications($status === 'all' ? null : $status, 20);

        return view('admin.verifications.index', [
            'verifications' => $verifications,
            'currentStatus' => $status,
            'counts' => [
                'pending' => UserVerification::where('status', 'pending')->count(),
                'approved' => UserVerification::where('status', 'approved')->count(),
                'rejected' => UserVerification::where('status', 'rejected')->count(),
                'all' => UserVerification::count(),
            ],
        ]);
    }

    /**
     * Approve a verification request.
     */
    public function approve(UserVerification $verification): RedirectResponse
    {
        $this->verificationService->reviewVerification(
            $verification,
            'approved',
            null,
            Auth::user()
        );

        return redirect()->back()->with('status', "Verification for user {$verification->user->name} has been approved. Badge granted and +50 points awarded.");
    }

    /**
     * Reject a verification request.
     */
    public function reject(ReviewVerificationRequest $request, UserVerification $verification): RedirectResponse
    {
        $this->verificationService->reviewVerification(
            $verification,
            'rejected',
            $request->input('reason', 'Document unreadable or invalid.'),
            Auth::user()
        );

        return redirect()->back()->with('status', "Verification for user {$verification->user->name} was marked as rejected.");
    }
}
