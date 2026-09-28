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
        protected MonetizationService $monetizationService
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
     * Process listing promotion with CAD or Points.
     */
    public function store(mixed $listing, PromoteListingRequest $request): RedirectResponse|JsonResponse
    {
        $listing = $this->resolveListing($listing);

        if (auth()->id() !== $listing->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized to promote this listing.');
        }

        $package = PromotionPackage::findOrFail($request->validated('package_id'));
        $paymentMethod = $request->validated('payment_method');

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
}
