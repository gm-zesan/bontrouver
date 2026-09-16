<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\SellerListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function __construct(
        private readonly SellerListingService $listingService
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
}
