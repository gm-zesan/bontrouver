<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RegistrationService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService
    ) {}

    /**
     * Redirect the user to the Google OAuth authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->withErrors([
                'google' => 'Google sign-in is currently undergoing configuration. Please sign in with email and password.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function redirect(): RedirectResponse
    {
        return $this->redirectToGoogle();
    }

    /**
     * Obtain the user information from Google callback and login/register.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('login')->withErrors([
                'google' => 'Google authentication was cancelled: ' . $request->get('error_description', $request->get('error')),
            ]);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            Log::error('Google OAuth Authentication Error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'google' => 'Unable to authenticate with Google. Please try again or use your password.',
            ]);
        }

        if (empty($googleUser->getEmail())) {
            return redirect()->route('login')->withErrors([
                'google' => 'No verified email address was returned by your Google account.',
            ]);
        }

        $user = $this->registrationService->findOrCreateGoogleUser($googleUser);

        if ($user->is_suspended) {
            return redirect()->route('login')->withErrors([
                'email' => 'This account has been suspended by administration.',
            ]);
        }

        Auth::login($user, remember: true);

        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home'))
            ->with('success', 'Welcome, ' . $user->name . '!');
    }

    public function callback(Request $request): RedirectResponse
    {
        return $this->handleGoogleCallback($request);
    }
}
