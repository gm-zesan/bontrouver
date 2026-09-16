<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\UserProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UserProfileService $profileService
    ) {}

    /**
     * Display the public profile view for a user (or self).
     */
    public function show(Request $request)
    {
        $id = $request->query('id');
        
        if ($id && $id != Auth::id()) {
            $user = User::findOrFail($id);
        } else {
            $user = Auth::user();
        }

        return view('frontend.account.profile', [
            'user' => $user,
            'categories' => CategoryService::getAll(),
            'stats' => app(\App\Services\SellerListingService::class)->getDashboardHeaderStats($user),
            'userListings' => $this->profileService->getRecentListings($user),
            'reviews' => $this->profileService->getRecentReviews($user),
            'hostedMeetups' => $this->profileService->getHostedMeetups($user),
        ]);
    }
}
