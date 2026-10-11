<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterGeneralUserRequest;
use App\Http\Requests\Auth\RegisterMerchantRequest;
use App\Models\Province;
use App\Services\LocationService;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the dedicated Personal / General Account registration view.
     */
    public function create(): View
    {
        $provinces = LocationService::getProvinces();
        $citiesMap = LocationService::getCitiesMap();

        return view('auth.register', compact('provinces', 'citiesMap'));
    }

    /**
     * Display the dedicated Merchant & Dealer registration view.
     */
    public function createMerchant(): View
    {
        $provinces = LocationService::getProvinces();
        $citiesMap = LocationService::getCitiesMap();
        $commercialCategories = \App\Models\Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('auth.register-merchant', compact('provinces', 'citiesMap', 'commercialCategories'));
    }

    /**
     * Handle incoming general user registration.
     */
    public function store(RegisterGeneralUserRequest $request, RegistrationService $registrationService): RedirectResponse
    {
        $user = $registrationService->registerGeneralUser($request->validated());

        Auth::login($user);

        return redirect(route('profile.edit', absolute: false))
            ->with('success', 'Welcome to Bontrouver! Your account has been successfully created.');
    }

    /**
     * Handle incoming commercial merchant / dealer registration.
     */
    public function storeMerchant(RegisterMerchantRequest $request, RegistrationService $registrationService): RedirectResponse
    {
        $user = $registrationService->registerMerchant(
            $request->validated(),
            $request->file('license_document')
        );

        Auth::login($user);

        return redirect(route('profile.edit', absolute: false))
            ->with('success', 'Welcome to Bontrouver Merchant Network! Your commercial dealer profile and verification submission have been established.');
    }
}
