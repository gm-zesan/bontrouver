<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromoteListingRequest;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use App\Services\MonetizationService;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingPromotionController extends Controller
{
    public function __construct(
        protected MonetizationService $monetizationService,
        protected StripeService $stripeService
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

        $packageIds = $request->input('package_ids');
        if (empty($packageIds) && $request->input('package_id')) {
            $packageIds = [$request->input('package_id')];
        }

        if (empty($packageIds)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Please select at least one promotion package.'], 422);
            }
            return back()->with('error', 'Please select at least one promotion package.');
        }

        $packages = PromotionPackage::whereIn('id', (array) $packageIds)->get();
        if ($packages->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Selected promotion packages not found.'], 422);
            }
            return back()->with('error', 'Selected promotion packages not found.');
        }

        $paymentMethod = $request->validated('payment_method');

        // 1. Stripe Card Checkout (Redirected Hosted Page)
        if ($paymentMethod === 'stripe') {
            $packageIdsList = $packages->pluck('id')->toArray();
            $successUrl = route('listings.promote.success', [
                'listing' => $listing->id,
                'package_ids' => implode(',', $packageIdsList),
                'package_id' => $packageIdsList[0],
            ]);
            $cancelUrl = route('listings.promote.show', [
                'listing' => $listing->id,
                'cancelled' => 1,
            ]);

            $session = $this->stripeService->createCheckoutSession(
                listing: $listing,
                package: $packages->all(),
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
        $totalPoints = $packages->sum('point_cost');
        if (auth()->user()->community_points < $totalPoints) {
            $err = "You have " . auth()->user()->community_points . " points, but {$totalPoints} points are required.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $err], 422);
            }
            throw \Illuminate\Validation\ValidationException::withMessages(['points' => $err]);
        }

        $promotions = [];
        foreach ($packages as $pkg) {
            $promotions[] = $this->monetizationService->promoteListing(
                listing: $listing,
                package: $pkg,
                user: auth()->user(),
                paymentMethod: $paymentMethod
            );
        }

        $names = $packages->pluck('name')->join(', ');
        $message = "Listing successfully boosted with {$names}!";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'promotions' => $promotions,
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

        $sessionId = $request->get('session_id');
        $session = null;
        if ($sessionId) {
            $session = $this->stripeService->retrieveCheckoutSession($sessionId);
            if (!$session || ($session['payment_status'] !== 'paid' && ($session['status'] ?? '') !== 'complete')) {
                return redirect()->route('listings.promote.show', $listing->id)
                    ->with('error', 'Payment verification was not completed. Please try again.');
            }
        }

        // Collect all package IDs (from request or Stripe session metadata)
        $rawPackageIds = $request->get('package_ids')
            ?? ($session['metadata']['package_ids'] ?? null)
            ?? $request->get('package_id')
            ?? ($session['metadata']['package_id'] ?? null);

        $packageIds = is_array($rawPackageIds)
            ? $rawPackageIds
            : array_filter(explode(',', (string) $rawPackageIds));

        $packages = PromotionPackage::whereIn('id', $packageIds)->get();
        if ($packages->isEmpty() && $request->get('package_id')) {
            $single = PromotionPackage::find($request->get('package_id'));
            if ($single) {
                $packages = collect([$single]);
            }
        }

        if ($packages->isEmpty()) {
            return redirect()->route('listings.show', $listing->slug ?? $listing->id)
                ->with('error', 'Promotion package not found.');
        }

        // Apply all promotions if not already recorded
        $appliedNames = [];
        foreach ($packages as $pkg) {
            $txRef = 'BT-STRIPE-' . ($sessionId ? ($sessionId . '-' . $pkg->id) : strtoupper(uniqid()));
            $existing = ListingPromotion::where('listing_id', $listing->id)
                ->where('promotion_package_id', $pkg->id)
                ->where('transaction_reference', 'LIKE', 'BT-STRIPE-' . ($sessionId ?? 'NOTFOUND') . '%')
                ->first();

            if (!$existing) {
                try {
                    $this->monetizationService->promoteListing(
                        listing: $listing,
                        package: $pkg,
                        user: auth()->user(),
                        paymentMethod: 'stripe',
                        transactionRef: $txRef
                    );
                    $appliedNames[] = $pkg->name;
                } catch (\Illuminate\Validation\ValidationException $e) {
                    // If already active or cooldown in effect, ignore
                }
            } else {
                $appliedNames[] = $pkg->name;
            }
        }

        $namesStr = !empty($appliedNames) ? implode(', ', $appliedNames) : $packages->pluck('name')->join(', ');

        return redirect()->route('listings.show', $listing->slug ?? $listing->id)
            ->with('success', "🎉 Payment successful! Your listing \"{$listing->title}\" has been upgraded with {$namesStr}.");
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
