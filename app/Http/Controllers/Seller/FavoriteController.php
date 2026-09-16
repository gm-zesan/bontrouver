<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\FavoriteService;
use App\Services\SellerListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function __construct(
        private readonly FavoriteService $favoriteService,
        private readonly SellerListingService $listingService
    ) {}

    /**
     * Display Saved Favorites dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        return view('frontend.account.favorites', [
            'user' => $user,
            'categories' => CategoryService::getAll(),
            'favorites' => $this->favoriteService->getUserFavorites($user),
            'stats' => $this->listingService->getDashboardHeaderStats($user),
            'currentCategory' => $request->query('category', 'all'),
            'currentSort' => $request->query('sort', 'newest'),
            'searchQuery' => $request->query('q', ''),
        ]);
    }

    /**
     * Remove item from Favorites.
     */
    public function destroy(Request $request, $id)
    {
        $this->favoriteService->removeFavorite(Auth::user(), (int) $id);

        return response()->json([
            'success' => true,
            'id' => (int) $id,
            'message' => 'Listing removed from your favorites.'
        ]);
    }

    /**
     * Toggle item in Favorites.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|integer|exists:listings,id'
        ]);

        $added = $this->favoriteService->toggleFavorite(
            Auth::user(), 
            (int) $request->input('listing_id')
        );

        return response()->json([
            'success' => true,
            'status' => $added ? 'added' : 'removed',
            'message' => $added ? 'Listing saved to your favorites!' : 'Listing removed from saved items.'
        ]);
    }
}
