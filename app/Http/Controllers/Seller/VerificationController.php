<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitVerificationRequest;
use App\Services\CategoryService;
use App\Services\SellerListingService;
use App\Services\VerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function __construct(
        private readonly VerificationService $verificationService,
        private readonly SellerListingService $sellerListingService
    ) {}

    /**
     * Redirect to the settings page verification section.
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('settings.index', '#verification-section');
    }

    /**
     * Submit an ID verification document for moderation review.
     */
    public function store(SubmitVerificationRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $this->verificationService->submitDocument(
            $user,
            $request->input('document_type'),
            $request->file('document'),
            $request->input('id_number')
        );

        return redirect()->back()->with('status', 'Your Canadian ID document has been securely submitted and is now under review by our trust & safety team.');
    }
}
