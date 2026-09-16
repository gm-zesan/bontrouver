<?php

namespace App\Http\Controllers;

use App\Services\HomeDataService;
use App\Services\LocationService;
use App\Services\UserLocationService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        private readonly HomeDataService $homeDataService,
        private readonly UserLocationService $userLocationService
    ) {}

    public function index(Request $request): \Illuminate\View\View
    {
        $selectedCity = $request->query('city') ?? $request->cookie('bontrouver_city') ?? session('selected_city');
        $viewData = $this->homeDataService->getHomepageData($selectedCity);

        return view('frontend.index', $viewData);
    }

    /**
     * Set/Clear user location via API (stores in session and persistent 30-day cookie)
     */
    public function setLocation(Request $request)
    {
        $result = $this->userLocationService->setLocation($request->input('city'));

        if ($result['is_all']) {
            session()->forget(['selected_city', 'selected_location_label']);
            $cookieCity = cookie()->forget('bontrouver_city');
            $cookieLabel = cookie()->forget('bontrouver_location_label');

            return response()->json([
                'success' => true,
                'city' => null,
                'label' => $result['label'],
                'message' => 'Location reset to nationwide (All Canada)'
            ])->withCookie($cookieCity)->withCookie($cookieLabel);
        }

        session(['selected_city' => $result['city'], 'selected_location_label' => $result['label']]);
        $cookieCity = cookie('bontrouver_city', $result['city'], 60 * 24 * 30);
        $cookieLabel = cookie('bontrouver_location_label', $result['label'], 60 * 24 * 30);

        return response()->json([
            'success' => true,
            'city' => $result['city'],
            'label' => $result['label'],
            'message' => "Location set to {$result['label']}"
        ])->withCookie($cookieCity)->withCookie($cookieLabel);
    }

    /**
     * Auto-detect nearest Canadian metropolitan city based on GPS latitude/longitude
     */
    public function detectLocation(Request $request)
    {
        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        if (!$lat || !$lng) {
            return response()->json([
                'success' => false,
                'message' => 'Valid GPS coordinates are required.'
            ], 422);
        }

        $result = $this->userLocationService->detectLocation($lat, $lng);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        if ($result['is_outside_canada']) {
            session()->forget('selected_city');
            session(['selected_location_label' => 'All Canada']);
            $cookieCity = cookie()->forget('bontrouver_city');
            $cookieLabel = cookie('bontrouver_location_label', 'All Canada', 60 * 24 * 30);
        } else {
            session(['selected_city' => $result['city'], 'selected_location_label' => $result['label']]);
            $cookieCity = cookie('bontrouver_city', $result['city'], 60 * 24 * 30);
            $cookieLabel = cookie('bontrouver_location_label', $result['label'], 60 * 24 * 30);
        }

        return response()->json($result)->withCookie($cookieCity)->withCookie($cookieLabel);
    }

    /**
     * Get list of supported Canadian cities
     */
    public function getCities()
    {
        return response()->json([
            'success' => true,
            'cities' => array_values(LocationService::getCitiesMap())
        ]);
    }
}
