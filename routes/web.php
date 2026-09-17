<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\Seller\ListingController as SellerListingController;
use App\Http\Controllers\Seller\ProfileController as SellerProfileController;
use App\Http\Controllers\Seller\FavoriteController;
use App\Http\Controllers\Seller\MessageController;
use App\Http\Controllers\Seller\NotificationController;
use App\Http\Controllers\Seller\SettingsController;
use App\Http\Controllers\Seller\MeetupController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\StaticPageController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Category, Location and Search Results Page
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/category/{categorySlug}', [ListingController::class, 'index'])->name('listings.category');
Route::get('/location/{cityOrSlug}', [ListingController::class, 'locationRedirect'])->name('listings.location');
Route::get('/listing/{idOrSlug}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/search/suggestions', [ListingController::class, 'suggestions'])->name('search.suggestions');

// Community Hub & Meetups
Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
Route::get('/community/meetup/{id}', [CommunityController::class, 'show'])->name('community.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/community/create', [CommunityController::class, 'create'])->name('community.create');
    Route::post('/community', [CommunityController::class, 'store'])->name('community.store');
    Route::get('/community/{id}/edit', [CommunityController::class, 'edit'])->name('community.edit');
    Route::put('/community/{id}', [CommunityController::class, 'update'])->name('community.update');
    Route::delete('/community/{id}', [CommunityController::class, 'destroy'])->name('community.destroy');
    Route::post('/community/meetup/{id}/join', [CommunityController::class, 'requestToJoin'])->name('community.join');
});
// Location Switcher & Auto-Detect API
Route::post('/api/location/set', [HomeController::class, 'setLocation'])->name('location.set');
Route::post('/api/location/detect', [HomeController::class, 'detectLocation'])->name('location.detect');
Route::get('/api/location/cities', [HomeController::class, 'getCities'])->name('location.cities');

// Post an Ad / Create Listing Flow (Login Required)
Route::middleware(['auth'])->group(function () {
    Route::get('/post-ad', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/post-ad', [ListingController::class, 'store'])->name('listings.store');
});
Route::get('/api/category-attributes/{categorySlug}', [ListingController::class, 'getCategoryAttributes'])->name('listings.category.attributes');

// Seller & User Dashboard & Account Pages
Route::middleware(['auth'])->group(function () {
    // Redirect legacy dashboard routes to Settings
    Route::get('/dashboard', fn() => redirect()->route('settings.index'))->name('dashboard');
    Route::get('/seller/panel', fn() => redirect()->route('settings.index'))->name('seller.panel');

    // My Listings
    Route::get('/my-listings', [SellerListingController::class, 'index'])->name('listings.my');
    Route::post('/my-listings/{id}/status', [SellerListingController::class, 'updateStatus'])->name('listings.my.status');
    Route::delete('/my-listings/{id}', [SellerListingController::class, 'destroy'])->name('listings.my.destroy');

    // Smart Alerts
    Route::get('/account/alerts', [\App\Http\Controllers\SmartAlertController::class, 'index'])->name('account.alerts.index');
    Route::get('/account/alerts/create', [\App\Http\Controllers\SmartAlertController::class, 'create'])->name('account.alerts.create');
    Route::post('/account/alerts', [\App\Http\Controllers\SmartAlertController::class, 'store'])->name('account.alerts.store');
    Route::patch('/account/alerts/{alert}/toggle', [\App\Http\Controllers\SmartAlertController::class, 'toggle'])->name('account.alerts.toggle');
    Route::delete('/account/alerts/{alert}', [\App\Http\Controllers\SmartAlertController::class, 'destroy'])->name('account.alerts.destroy');

    // 1. Favorites / Saved Ads
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // 2. Messages / Inbox Conversations
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/initiate', [MessageController::class, 'initiate'])->name('messages.initiate');
    Route::post('/messages/{conversationId}/reply', [MessageController::class, 'send'])->name('messages.send');

    // 3. Notifications Center
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('/notifications/delete', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // 4. User Public / Account Profile
    Route::get('/profile/view', [SellerProfileController::class, 'show'])->name('profile.view');

    // 5. Account Settings & Preferences
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'updateProfile'])->name('settings.update');
    
    // 6. Community Meetups Management
    Route::get('/my-meetups', [MeetupController::class, 'index'])->name('meetups.my');
    Route::post('/my-meetups/{meetupId}/attendees/{attendeeId}/status', [MeetupController::class, 'updateAttendeeStatus'])->name('meetups.my.attendee.status');
    Route::delete('/my-meetups/{meetupId}/attendees/{attendeeId}/cancel', [MeetupController::class, 'cancelRequest'])->name('meetups.my.attendee.cancel');

    // Breeze Profile edit routes (Merged into Settings Controller)
    Route::get('/profile', [SettingsController::class, 'index'])->name('profile.edit');
    Route::patch('/profile', [SettingsController::class, 'updateAuth'])->name('profile.update');
    Route::delete('/profile', [SettingsController::class, 'destroy'])->name('profile.destroy');
});

// Static Marketplace Info & Footer Pages
Route::controller(StaticPageController::class)->group(function () {
    Route::get('/about', 'about')->name('pages.about');
    Route::get('/member-benefits', 'memberBenefits')->name('pages.member-benefits');
    Route::get('/terms', 'terms')->name('pages.terms');
    Route::get('/privacy', 'privacy')->name('pages.privacy');
    Route::get('/posting-policy', 'postingPolicy')->name('pages.posting-policy');
    Route::get('/security', 'security')->name('pages.security');
    Route::get('/verification', 'verification')->name('pages.verification');
    Route::get('/advertise', 'advertise')->name('pages.advertise');
    Route::get('/promote-tools', 'promoteTools')->name('pages.promote-tools');
    Route::get('/community-connect', 'communityConnect')->name('pages.community-connect');
    Route::get('/accessibility', 'accessibility')->name('pages.accessibility');
    Route::get('/ad-choices', 'adChoices')->name('pages.ad-choices');
});

require __DIR__ . '/auth.php';

