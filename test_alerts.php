<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';
$app = app();
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Category;
use App\Services\ListingService;
use App\Services\SmartAlertService;
use Illuminate\Support\Facades\Hash;

// Ensure users exist
$admin = User::firstOrCreate(
    ['email' => 'admin@bontrouver.ca'],
    ['name' => 'Admin User', 'password' => Hash::make('password123')]
);

$seller = User::firstOrCreate(
    ['email' => 'seller@bontrouver.ca'],
    ['name' => 'Seller User', 'password' => Hash::make('password123')]
);

// If their passwords differ in DB, reset them to password123 just in case
$admin->update(['password' => Hash::make('password123')]);
$seller->update(['password' => Hash::make('password123')]);

$category = Category::where('slug', '!=', '')->first() ?? Category::first();

// Create Alert for Admin
$alertService = app(SmartAlertService::class);
$alertData = [
    'name' => 'Super Cool Alert',
    'keyword' => 'Super Cool',
    'city' => 'Vancouver',
    'max_price' => 2000,
    'category_id' => $category->id ?? null,
];
$alert = $alertService->create($alertData, $admin->id);
echo "Created Alert ID: " . $alert->id . " for Admin\n";

// Create Listing from Seller matching the criteria
$listingService = app(ListingService::class);
\Auth::login($seller);

$listingData = [
    'title' => 'Super Cool Listing',
    'description' => 'This is a super cool test listing',
    'price' => 1500,
    'price_type' => 'fixed',
    'condition' => 'new',
    'city' => 'Vancouver',
    'province' => 'BC',
    'category_slug' => $category->slug ?? null,
];

$listing = $listingService->create($listingData);
echo "Created Listing ID: " . $listing->id . " from Seller ID: " . $seller->id . "\n";
