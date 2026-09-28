<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromoteListingRequest;
use App\Models\Listing;
use App\Models\PromotionPackage;
use App\Services\MonetizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingPromotionController extends Controller
{
    public function __construct(
        protected MonetizationService $monetizationService,
        protected \App\Services\StripeService $stripeService
    ) {}

    /**
     * Resolve listing instance from numeric ID, slug, or model instance.
     */
    protected function resolveListing(mixed $listing): Listing
    {
        if ($listing instanceof Listing) {
            return $listing;
        }

        if (is_numeric($listing)) {
            return Listing::findOrFail((int) $listing);
        }

        return Listing::where('slug', (string) $listing)->firstOrFail();
    }

    /**
     * Show promotion selection page/modal data for a listing.
     */
    public function show(mixed $listing, Request $request): View|JsonResponse
    {
        $listing = $this->resolveListing($listing);

        // Authorize listing ownership or admin
        if (auth()->id() !== $listing->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized to promote this listing.');
        }

        $packages = PromotionPackage::active()->orderBy('sort_order')->get();
        $user = auth()->user();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'listing' => [
                    'id' => $listing->id,
                    'title' => $listing->title,
                    'slug' => $listing->slug,
                    'is_featured' => $listing->is_featured,
                    'is_sponsored' => $listing->is_sponsored,
                    'bumped_at' => $listing->bumped_at,
                ],
                'packages' => $packages,
                'user_points' => $user->community_points,
            ]);
        }

        return view('frontend.listings.promote', compact('listing', 'packages', 'user'));
    }

    /**
     * Process listing promotion with CAD (Stripe Checkout) or Points.
     */
    public function store(mixed $listing, PromoteListingRequest $request): RedirectResponse|JsonResponse
    {
        $listing = $this->resolveListing($listing);

        if (auth()->id() !== $listing->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized to promote this listing.');
        }

        $package = PromotionPackage::findOrFail($request->validated('package_id'));
        $paymentMethod = $request->validated('payment_method');

        // 1. Stripe Card Checkout (Redirected Hosted Page)
        if ($paymentMethod === 'stripe') {
            $successUrl = route('listings.promote.success', [
                'listing' => $listing->id,
                'package_id' => $package->id,
            ]);
            $cancelUrl = route('listings.promote.show', [
                'listing' => $listing->id,
                'cancelled' => 1,
            ]);

            $session = $this->stripeService->createCheckoutSession(
                listing: $listing,
                package: $package,
                user: auth()->user(),
                successUrl: $successUrl,
                cancelUrl: $cancelUrl
            );

            if (!$session['success']) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $session['error'] ?? 'Could not initiate Stripe checkout.',
                    ], 422);
                }
                return back()->with('error', $session['error'] ?? 'Could not initiate Stripe checkout.');
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $session['url'],
                    'session_id' => $session['id'],
                ]);
            }

            return redirect()->away($session['url']);
        }

        // 2. Points Redemption
        $promotion = $this->monetizationService->promoteListing(
            listing: $listing,
            package: $package,
            user: auth()->user(),
            paymentMethod: $paymentMethod
        );

        $message = "Listing successfully boosted with {$package->name}!";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'promotion' => $promotion,
                'listing' => $listing->fresh(),
            ]);
        }

        return redirect()->route('listings.show', $listing->slug)->with('success', $message);
    }

    /**
     * Handle return from successful Stripe Checkout.
     */
    public function success(mixed $listing, Request $request): RedirectResponse
    {
        $listing = $this->resolveListing($listing);

        if (auth()->id() !== $listing->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $packageId = $request->get('package_id');
        $sessionId = $request->get('session_id');

        $package = PromotionPackage::find($packageId);
        if (!$package) {
            return redirect()->route('listings.show', $listing->slug ?? $listing->id)
                ->with('error', 'Promotion package not found.');
        }

        // Verify session status
        if ($sessionId) {
            $session = $this->stripeService->retrieveCheckoutSession($sessionId);
            if (!$session || ($session['payment_status'] !== 'paid' && ($session['status'] ?? '') !== 'complete')) {
                return redirect()->route('listings.promote.show', $listing->id)
                    ->with('error', 'Payment verification was not completed. Please try again.');
            }
        }

        // Apply promotion if not already recorded for this session
        $existing = \App\Models\ListingPromotion::where('transaction_reference', 'BT-STRIPE-' . ($sessionId ?? ''))->first();
        if (!$existing) {
            try {
                $this->monetizationService->promoteListing(
                    listing: $listing,
                    package: $package,
                    user: auth()->user(),
                    paymentMethod: 'stripe',
                    transactionRef: 'BT-STRIPE-' . ($sessionId ?? strtoupper(uniqid()))
                );
            } catch (\Illuminate\Validation\ValidationException $e) {
                // If already active or cooldown in effect, ignore
            }
        }

        return redirect()->route('listings.show', $listing->slug ?? $listing->id)
            ->with('success', "🎉 Payment successful! Your listing \"{$listing->title}\" has been upgraded with {$package->name}.");
    }

    /**
     * Handle return when user cancels on Stripe Checkout.
     */
    public function cancel(mixed $listing): RedirectResponse
    {
        $listing = $this->resolveListing($listing);
        return redirect()->route('listings.promote.show', $listing->id)
            ->with('info', 'Stripe checkout was cancelled. You can boost your listing anytime.');
    }
}
