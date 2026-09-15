<?php

namespace App\Providers;

use App\Models\City;
use App\Services\CategoryService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $request = request();
            $city = $request->query('city') ?? $request->cookie('bontrouver_city') ?? session('selected_city');
            $label = $request->cookie('bontrouver_location_label') ?? session('selected_location_label');

            $citiesMap = City::getCitiesMap();

            if (!$label && $city) {
                $matched = $citiesMap[$city] ?? null;
                $label = $matched['label'] ?? (ucfirst($city) . ', Canada');
            }

            $view->with([
                'categoryData'          => CategoryService::getAll(),
                'canadianCities'        => $citiesMap,
                'currentSelectedCity'   => $city,
                'currentLocationLabel'  => $label ?: 'All Canada',
            ]);
        });
    }
}
