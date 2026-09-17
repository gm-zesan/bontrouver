<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\SellerListingService;
use App\Services\UserProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UserProfileService $profileService,
        private readonly SellerListingService $sellerListingService
    ) {}

    /**
     * Display the public profile view for a user (or self).
     */
    public function show(Request $request, ?User $user = null)
    {
        // If route binding passed a user model
        if (!$user || !$user->exists) {
            $id = $request->query('id');
            if ($id) {
                $user = User::findOrFail($id);
            } elseif (Auth::check()) {
                $user = Auth::user();
            } else {
                return redirect()->route('login')->with('info', 'Please log in to view your profile.');
            }
        }

        return view('frontend.account.profile', [
            'user' => $user,
            'categories' => CategoryService::getAll(),
            'stats' => $this->sellerListingService->getDashboardHeaderStats($user),
            'userListings' => $this->profileService->getRecentListings($user),
            'reviews' => $this->profileService->getRecentReviews($user),
            'hostedMeetups' => $this->profileService->getHostedMeetups($user),
        ]);
    }
}
