<?php

namespace App\Providers;

use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Policies\CompanionshipRequestPolicy;
use App\Services\AdminNotificationService;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Policy registrations
        Gate::policy(CompanionshipRequest::class, CompanionshipRequestPolicy::class);

        // Event Listeners
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\ListingCreated::class,
            \App\Listeners\EvaluateSmartAlerts::class
        );

        View::composer('*', function ($view) {
            $request = request();
            $user = auth()->user();
            $city = $request->query('city') 
                ?? $request->cookie('bontrouver_city') 
                ?? session('selected_city') 
                ?? $user?->city;
            $label = $request->cookie('bontrouver_location_label') ?? session('selected_location_label');

            $citiesMap = City::getCitiesMap();

            if (!$label && $city) {
                $matched = $citiesMap[$city] ?? null;
                $label = $matched['label'] ?? ($user?->city && $user?->province ? "{$user->city}, {$user->province}" : (ucfirst($city) . ', Canada'));
            }

            // If user is authenticated and session doesn't have the city yet, persist it
            if ($user?->city && !session()->has('selected_city')) {
                session(['selected_city' => $city, 'selected_location_label' => $label]);
            }

            $view->with([
                'categoryData'          => CategoryService::getAll(),
                'categoryNavTree'       => CategoryService::getNavTree(),
                'canadianCities'        => $citiesMap,
                'currentSelectedCity'   => $city,
                'currentLocationLabel'  => $label ?: 'All Canada',
            ]);
        });

        View::composer('admin.includes.header', function ($view) {
            $adminNotifications = app(AdminNotificationService::class)->getImportantNotifications();
            $view->with('adminNotifications', $adminNotifications);
        });
    }
}
