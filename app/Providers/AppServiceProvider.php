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

        View::composer('admin.includes.header', function ($view) {
            $adminNotifications = app(AdminNotificationService::class)->getImportantNotifications();
            $view->with('adminNotifications', $adminNotifications);
        });
    }
}
