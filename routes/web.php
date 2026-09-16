<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerDashboardController;
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
    // Redirect legacy dashboard routes to Profile
    Route::get('/dashboard', fn() => redirect()->route('profile.edit'))->name('dashboard');
    Route::get('/seller/panel', fn() => redirect()->route('profile.edit'))->name('seller.panel');
    Route::get('/my-listings', [SellerDashboardController::class, 'myListings'])->name('listings.my');
    Route::post('/my-listings/{id}/status', [SellerDashboardController::class, 'updateStatus'])->name('listings.my.status');
    Route::delete('/my-listings/{id}', [SellerDashboardController::class, 'destroyListing'])->name('listings.my.destroy');

    // 1. Favorites / Saved Ads
    Route::get('/favorites', [SellerDashboardController::class, 'favorites'])->name('favorites.index');
    Route::delete('/favorites/{id}', [SellerDashboardController::class, 'removeFavorite'])->name('favorites.destroy');
    Route::post('/favorites/toggle', [SellerDashboardController::class, 'toggleFavorite'])->name('favorites.toggle');

    // 2. Messages / Inbox Conversations
    Route::get('/messages', [SellerDashboardController::class, 'messages'])->name('messages.index');
    Route::post('/messages/initiate', [SellerDashboardController::class, 'initiateMessage'])->name('messages.initiate');
    Route::post('/messages/{conversationId}/reply', [SellerDashboardController::class, 'sendMessage'])->name('messages.send');

    // 3. Notifications Center
    Route::get('/notifications', [SellerDashboardController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/read-all', [SellerDashboardController::class, 'markAllNotificationsRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [SellerDashboardController::class, 'markNotificationRead'])->name('notifications.read');

    // 4. User Public / Account Profile
    Route::get('/profile/view', [SellerDashboardController::class, 'profileView'])->name('profile.view');

    // 5. Account Settings & Preferences
    Route::get('/settings', [SellerDashboardController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [SellerDashboardController::class, 'updateSettings'])->name('settings.update');

    // 6. Community Meetups Management
    Route::get('/my-meetups', [SellerDashboardController::class, 'myMeetups'])->name('meetups.my');
    Route::post('/my-meetups/{meetupId}/attendees/{attendeeId}/status', [SellerDashboardController::class, 'updateAttendeeStatus'])->name('meetups.my.attendee.status');

    // Breeze Profile edit routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

