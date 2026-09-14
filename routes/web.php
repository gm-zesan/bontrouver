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

// Seller & User Dashboard & Listings Management
Route::get('/dashboard', [\App\Http\Controllers\SellerDashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::get('/seller/panel', [\App\Http\Controllers\SellerDashboardController::class, 'index'])->middleware(['auth'])->name('seller.panel');
Route::get('/my-listings', [\App\Http\Controllers\SellerDashboardController::class, 'myListings'])->middleware(['auth'])->name('listings.my');
Route::post('/my-listings/{id}/status', [\App\Http\Controllers\SellerDashboardController::class, 'updateStatus'])->middleware(['auth'])->name('listings.my.status');
Route::delete('/my-listings/{id}', [\App\Http\Controllers\SellerDashboardController::class, 'destroyListing'])->middleware(['auth'])->name('listings.my.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
