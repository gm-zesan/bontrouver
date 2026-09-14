<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.index');
});

// Category and Search Results Page
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/category/{categorySlug}', [ListingController::class, 'index'])->name('listings.category');
Route::get('/listing/{idOrSlug}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/search/suggestions', [ListingController::class, 'suggestions'])->name('search.suggestions');

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
    Route::get('/my-listings', [\App\Http\Controllers\SellerDashboardController::class, 'myListings'])->name('listings.my');
    Route::post('/my-listings/{id}/status', [\App\Http\Controllers\SellerDashboardController::class, 'updateStatus'])->name('listings.my.status');
    Route::delete('/my-listings/{id}', [\App\Http\Controllers\SellerDashboardController::class, 'destroyListing'])->name('listings.my.destroy');

    // 1. Favorites / Saved Ads
    Route::get('/favorites', [\App\Http\Controllers\SellerDashboardController::class, 'favorites'])->name('favorites.index');
    Route::delete('/favorites/{id}', [\App\Http\Controllers\SellerDashboardController::class, 'removeFavorite'])->name('favorites.destroy');

    // 2. Messages / Inbox Conversations
    Route::get('/messages', [\App\Http\Controllers\SellerDashboardController::class, 'messages'])->name('messages.index');
    Route::post('/messages/{conversationId}/reply', [\App\Http\Controllers\SellerDashboardController::class, 'sendMessage'])->name('messages.send');

    // 3. Notifications Center
    Route::get('/notifications', [\App\Http\Controllers\SellerDashboardController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/read-all', [\App\Http\Controllers\SellerDashboardController::class, 'markAllNotificationsRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\SellerDashboardController::class, 'markNotificationRead'])->name('notifications.read');

    // 4. User Public / Account Profile
    Route::get('/profile/view', [\App\Http\Controllers\SellerDashboardController::class, 'profileView'])->name('profile.view');

    // 5. Account Settings & Preferences
    Route::get('/settings', [\App\Http\Controllers\SellerDashboardController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SellerDashboardController::class, 'updateSettings'])->name('settings.update');

    // Breeze Profile edit routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
