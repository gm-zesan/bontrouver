<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\CategoryService;
use App\Services\SellerListingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SellerListingService $listingService
    ) {}

    /**
     * Display Account Settings & Preferences.
     * (Merges Breeze's edit method and SellerDashboard's settings method)
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        return view('frontend.account.settings', [
            'user' => $user,
            'categories' => CategoryService::getAll(),
            'stats' => $this->listingService->getDashboardHeaderStats($user),
        ]);
    }

    /**
     * Save basic profile settings (name, phone, location, bio).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }
        if ($request->has('location')) {
            $user->location = $request->input('location');
        }
        if ($request->has('bio')) {
            $user->bio = $request->input('bio');
        }

        $user->save();

        return redirect()->back()->with('status', 'Settings updated successfully.');
    }

    /**
     * Update the user's account information (Email/Auth - from Breeze).
     */
    public function updateAuth(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('settings.index')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
