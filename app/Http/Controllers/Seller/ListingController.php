<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\PromotionPackage;
use App\Services\CategoryService;
use App\Services\MonetizationService;
use App\Services\PointService;
use App\Services\SellerListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function __construct(
        private readonly SellerListingService $listingService,
        private readonly PointService $pointService,
        private readonly MonetizationService $monetizationService
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

        $listing = Listing::where('user_id', Auth::id())->findOrFail($id);

        if ($status === 'renewed') {
            $listing->status = ListingStatus::ACTIVE;
            $listing->created_at = now();
            $listing->published_at = now();
            $listing->bumped_at = now();
            $listing->save();
        } elseif ($status === 'deleted') {
            $listing->delete();
        } else {
            $enumStatus = ListingStatus::tryFrom($status) ?? ListingStatus::ACTIVE;
            $listing->status = $enumStatus;
            $listing->save();
        }

        $messages = [
            'sold' => 'Listing marked as sold successfully.',
            'paused' => 'Listing paused and temporarily hidden from search.',
            'active' => 'Listing activated and visible to buyers.',
            'renewed' => 'Listing renewed and bumped to the top of search!',
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
        $listing = Listing::where('user_id', Auth::id())->findOrFail($id);
        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Listing removed permanently.'
        ]);
    }

    /**
     * Promote a listing using community points or boost package.
     */
    public function promote(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:featured,sponsored,bump_up,bump',
        ]);

        $type = $validated['type'] === 'bump' ? 'bump_up' : $validated['type'];
        $listing = Listing::where('user_id', Auth::id())->findOrFail($id);
        $user = Auth::user();

        // Enforce active cooldowns
        if ($type === 'sponsored' && $listing->is_sponsored && $listing->sponsored_until && $listing->sponsored_until->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => "This listing is already Sponsored until " . $listing->sponsored_until->format('M d, Y') . ". You cannot boost again until the current duration ends."
            ], 422);
        }

        if ($type === 'featured' && $listing->is_featured && $listing->featured_until && $listing->featured_until->isFuture()) {
            return response()->json([
                'success' => false,
                'message' => "This listing is already Featured until " . $listing->featured_until->format('M d, Y') . ". You cannot boost again until the current duration ends."
            ], 422);
        }

        if ($type === 'bump_up' && $listing->bumped_at && $listing->bumped_at->isToday()) {
            return response()->json([
                'success' => false,
                'message' => "This listing was already Bumped today. You can bump it again tomorrow."
            ], 422);
        }

        // Check if matching promotion package exists
        $package = PromotionPackage::where('type', $type)->where('is_active', true)->first();

        if ($package) {
            try {
                $promotion = $this->monetizationService->promoteListing(
                    listing: $listing,
                    package: $package,
                    user: $user,
                    paymentMethod: 'points'
                );

                return response()->json([
                    'success' => true,
                    'message' => "Listing successfully boosted with {$package->name}!",
                    'badge' => strtoupper($type === 'bump_up' ? 'BUMPED' : $type),
                    'promotion' => $promotion,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
        }

        // Direct fallback point calculation if package not seeded
        $pointCosts = [
            'featured' => 150,
            'sponsored' => 300,
            'bump_up' => 60,
        ];
        $cost = $pointCosts[$type] ?? 100;

        if ($user->community_points < $cost) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient points. You have {$user->community_points} pts, but {$cost} pts are required."
            ], 400);
        }

        try {
            $this->pointService->spendPoints(
                $user,
                $cost,
                'listing_boost_' . $type,
                "Spent {$cost} points for {$type} boost",
                $listing
            );

            $now = now();
            if ($type === 'featured') {
                $listing->is_featured = true;
                $listing->featured_until = $now->copy()->addDays(7);
            } elseif ($type === 'sponsored') {
                $listing->is_sponsored = true;
                $listing->sponsored_until = $now->copy()->addDays(7);
            } elseif ($type === 'bump_up') {
                $listing->bumped_at = $now;
                $listing->created_at = $now;
            }
            $listing->save();

            return response()->json([
                'success' => true,
                'message' => "Listing successfully boosted with " . ucfirst(str_replace('_', ' ', $type)) . "!",
                'badge' => strtoupper($type === 'bump_up' ? 'BUMPED' : $type)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
