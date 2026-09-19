<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\SellerListingService;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Listing;

class ListingController extends Controller
{
    public function __construct(
        private readonly SellerListingService $listingService,
        private readonly PointService $pointService
    ) {}

    /**
     * Display the My Listings / Manage Ads page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $allListings = $this->listingService->getDashboardListings($user);
        $counts = $this->listingService->getCounts($allListings);
        $stats = $this->listingService->getStats($allListings, $counts);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'listings' => $allListings,
                'counts' => $counts,
                'stats' => $stats,
            ]);
        }

        return view('frontend.account.my-listings', [
            'user' => $user,
            'categories' => CategoryService::getAll(),
            'listings' => $allListings,
            'counts' => $counts,
            'stats' => $stats,
            'currentStatus' => $request->query('status', 'all'),
            'currentCategory' => $request->query('category', 'all'),
            'currentSort' => $request->query('sort', 'newest'),
            'searchQuery' => $request->query('q', ''),
        ]);
    }

    /**
     * Update listing status.
     */
    public function updateStatus(Request $request, $id)
    {
        $status = $request->input('status');
        $validStatuses = ['active', 'sold', 'paused', 'renewed', 'draft', 'deleted'];

        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status transition requested.'
            ], 422);
        }

        // TODO: Move the actual DB update into the service in a later refactor
        // if needed, but since it was just mocking an update with no DB call in SellerDashboardController,
        // we'll keep the same behavior.

        $messages = [
            'sold' => 'Listing marked as sold successfully.',
            'paused' => 'Listing paused and temporarily hidden from search.',
            'active' => 'Listing activated and visible to buyers.',
            'renewed' => 'Listing renewed successfully for 30 days.',
            'deleted' => 'Listing removed permanently.',
        ];

        return response()->json([
            'success' => true,
            'status' => $status === 'renewed' ? 'active' : $status,
            'message' => $messages[$status] ?? 'Listing updated successfully.'
        ]);
    }

    /**
     * Delete a listing.
     */
    public function destroy(Request $request, $id)
    {
        // Mocking the delete response as DB implementation is not fully provided yet.
        return response()->json([
            'success' => true,
            'message' => 'Listing removed permanently.'
        ]);
    }

    /**
     * Promote a listing using community points.
     */
    public function promote(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:featured,sponsored',
        ]);

        $listing = Listing::where('user_id', Auth::id())->findOrFail($id);
        $user = Auth::user();

        if ($validated['type'] === 'featured' && $listing->is_featured) {
            return response()->json(['success' => false, 'message' => 'Listing is already featured.'], 400);
        }

        if ($validated['type'] === 'sponsored' && $listing->is_sponsored) {
            return response()->json(['success' => false, 'message' => 'Listing is already sponsored.'], 400);
        }

        $cost = $validated['type'] === 'featured' ? config('points.spend.featured_promotion') : config('points.spend.sponsored_promotion');

        try {
            $this->pointService->spendPoints(
                $user,
                $cost,
                'listing_promotion',
                "Spent points for {$validated['type']} promotion",
                $listing
            );

            if ($validated['type'] === 'featured') {
                $listing->is_featured = true;
            } else {
                $listing->is_sponsored = true;
            }
            $listing->save();

            return response()->json([
                'success' => true,
                'message' => "Listing successfully promoted to " . ucfirst($validated['type']) . "!",
                'badge' => strtoupper($validated['type'])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
