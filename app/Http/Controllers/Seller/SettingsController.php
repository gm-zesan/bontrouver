<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Services\CategoryService;
use App\Services\SellerListingService;
use App\Services\UserProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SellerListingService $listingService,
        private readonly UserProfileService $profileService
    ) {}

    /**
     * Display Account Settings & Preferences.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        return view('frontend.account.settings', [
            'user' => $user,
            'categories' => CategoryService::getAll(),
            'stats' => $this->listingService->getDashboardHeaderStats($user),
            'latestVerification' => $user->latestVerification,
            'verifications' => $user->verifications()->latest()->get(),
        ]);
    }

    /**
     * Save profile settings (name, phone, city, province, postal_code, location, bio, avatar).
     */
    public function updateProfile(UpdateUserProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $avatarFile = $request->file('avatar') ?? $request->input('avatar_base64');

        $this->profileService->updateProfile($user, $request->validated(), $avatarFile);

        return redirect()->back()->with('status', 'Profile and settings updated successfully.');
    }

    /**
     * Save all notification preferences.
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $prefs = [
            'messages' => $request->boolean('messages', false),
            'alerts' => $request->boolean('alerts', false),
            'meetups' => $request->boolean('meetups', false),
        ];

        $user->notification_preferences = $prefs;
        $user->save();

        return redirect()->back()->with('status', 'Notification preferences updated successfully.');
    }

    /**
     * AJAX instant toggle for a single notification preference.
     */
    public function toggleNotification(Request $request)
    {
        $request->validate([
            'key' => 'required|string|in:messages,alerts,meetups',
            'enabled' => 'required|boolean',
        ]);

        $user = Auth::user();
        $key = $request->input('key');
        $enabled = (bool) $request->input('enabled');

        $prefs = $user->notification_preferences ?? [
            'messages' => true,
            'alerts' => true,
            'meetups' => true,
        ];

        $prefs[$key] = $enabled;
        $user->notification_preferences = $prefs;
        $user->save();

        return response()->json([
            'success' => true,
            'key' => $key,
            'enabled' => $enabled,
            'message' => ucfirst($key) . ' notifications ' . ($enabled ? 'enabled' : 'disabled') . '.',
        ]);
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

        return Redirect::route('settings.index')->with('status', 'Login email updated successfully.');
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
