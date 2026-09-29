<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingPromotionController;
use App\Http\Controllers\SmartAlertController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SupportChatController;

use App\Http\Controllers\Seller\ListingController as SellerListingController;
use App\Http\Controllers\Seller\ProfileController as SellerProfileController;
use App\Http\Controllers\Seller\FavoriteController;
use App\Http\Controllers\Seller\MessageController;
use App\Http\Controllers\Seller\NotificationController;
use App\Http\Controllers\Seller\SettingsController;
use App\Http\Controllers\Seller\MeetupController;
use App\Http\Controllers\Seller\VerificationController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\VerificationReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ListingController as AdminListingController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Admin\BannerAdController as AdminBannerAdController;
use App\Http\Controllers\Admin\MeetupController as AdminMeetupController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CategoryAttributeController as AdminCategoryAttributeController;
use App\Http\Controllers\Admin\MemberTierController as AdminMemberTierController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SupportManagementController as AdminSupportManagementController;

use Illuminate\Support\Facades\Route;

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

// Public User / Seller Profiles
Route::get('/user/{user}', [SellerProfileController::class, 'show'])->name('user.profile');

// Location Switcher & Auto-Detect API
Route::post('/api/location/set', [HomeController::class, 'setLocation'])->name('location.set');
Route::post('/api/location/detect', [HomeController::class, 'detectLocation'])->name('location.detect');
Route::get('/api/location/cities', [HomeController::class, 'getCities'])->name('location.cities');
Route::get('/api/category-attributes/{categorySlug}', [ListingController::class, 'getCategoryAttributes'])->name('listings.category.attributes');

// Support Chat Wizard API Endpoints
Route::prefix('support-chat')->name('support-chat.')->group(function () {
    Route::get('/init', [SupportChatController::class, 'init'])->name('init');
    Route::get('/faqs', [SupportChatController::class, 'getFaqs'])->name('faqs');
    Route::get('/messages/{conversation}', [SupportChatController::class, 'getMessages'])->name('messages');
    Route::post('/messages/{conversation}/send', [SupportChatController::class, 'sendMessage'])->name('send');
});

// Stripe Payment Webhook
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

// =========================================================================
// AUTHENTICATED USER & SELLER ACCOUNT ROUTES
// =========================================================================
Route::middleware(['auth'])->group(function () {
    // 0. User Own Profile
    Route::get('/profile', [SellerProfileController::class, 'show'])->name('profile.index');
    Route::get('/profile/view', [SellerProfileController::class, 'show'])->name('profile.view');

    // 1. Post & Edit Listings
    Route::get('/post-ad', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/post-ad', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/listing/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listing/{listing}', [ListingController::class, 'update'])->name('listings.update');

    // 2. My Listings Management
    Route::get('/my-listings', [SellerListingController::class, 'index'])->name('listings.my');
    Route::post('/my-listings/{id}/status', [SellerListingController::class, 'updateStatus'])->name('listings.my.status');
    Route::get('/my-listings/{id}/analytics', [SellerListingController::class, 'analytics'])->name('listings.my.analytics');
    Route::post('/my-listings/{id}/promote', [SellerListingController::class, 'promote'])->name('listings.my.promote');
    Route::delete('/my-listings/{id}', [SellerListingController::class, 'destroy'])->name('listings.my.destroy');

    // 3. Listing Promotion & Boost Hub
    Route::get('/listing/{listing}/promote', [ListingPromotionController::class, 'show'])->name('listings.promote.show');
    Route::post('/listing/{listing}/promote', [ListingPromotionController::class, 'store'])->name('listings.promote.store');
    Route::get('/listing/{listing}/promote/success', [ListingPromotionController::class, 'success'])->name('listings.promote.success');
    Route::get('/listing/{listing}/promote/cancel', [ListingPromotionController::class, 'cancel'])->name('listings.promote.cancel');
    Route::get('/listings/{listing}/promote', [ListingPromotionController::class, 'show']);

    // 4. Community Meetups & Groups
    Route::get('/community/create', [CommunityController::class, 'create'])->name('community.create');
    Route::post('/community', [CommunityController::class, 'store'])->name('community.store');
    Route::get('/community/{id}/edit', [CommunityController::class, 'edit'])->name('community.edit');
    Route::put('/community/{id}', [CommunityController::class, 'update'])->name('community.update');
    Route::delete('/community/{id}', [CommunityController::class, 'destroy'])->name('community.destroy');
    Route::post('/community/meetup/{id}/join', [CommunityController::class, 'requestToJoin'])->name('community.join');
    Route::get('/my-meetups', [MeetupController::class, 'index'])->name('meetups.my');
    Route::post('/my-meetups/{meetupId}/attendees/{attendeeId}/status', [MeetupController::class, 'updateAttendeeStatus'])->name('meetups.my.attendee.status');

    // 5. Smart Alerts
    Route::get('/account/alerts', [SmartAlertController::class, 'index'])->name('account.alerts.index');
    Route::get('/account/alerts/create', [SmartAlertController::class, 'create'])->name('account.alerts.create');
    Route::post('/account/alerts', [SmartAlertController::class, 'store'])->name('account.alerts.store');
    Route::patch('/account/alerts/{alert}/toggle', [SmartAlertController::class, 'toggle'])->name('account.alerts.toggle');
    Route::delete('/account/alerts/{alert}', [SmartAlertController::class, 'destroy'])->name('account.alerts.destroy');

    // 6. Favorites / Saved Ads
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // 7. Messages / Inbox Conversations
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/initiate', [MessageController::class, 'initiate'])->name('messages.initiate');
    Route::post('/messages/{conversationId}/reply', [MessageController::class, 'send'])->name('messages.send');

    // 8. Notifications Center
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('/notifications/delete', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // 9. Account Settings & Preferences
    Route::get('/dashboard', fn() => redirect()->route('settings.index'))->name('dashboard');
    Route::get('/seller/panel', fn() => redirect()->route('settings.index'))->name('seller.panel');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'updateProfile'])->name('settings.update');
    Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::post('/settings/notifications/toggle', [SettingsController::class, 'toggleNotification'])->name('settings.notifications.toggle');
    Route::get('/settings/edit', [SettingsController::class, 'index'])->name('profile.edit');
    Route::get('/account/points', [SettingsController::class, 'pointsLedger'])->name('account.points');
    Route::patch('/settings/auth', [SettingsController::class, 'updateAuth'])->name('profile.update');
    Route::delete('/settings/gallery/{gallery}', [SettingsController::class, 'deleteGalleryPhoto'])->name('settings.gallery.destroy');
    Route::delete('/settings/account', [SettingsController::class, 'destroy'])->name('profile.destroy');

    // 10. Canadian Identity Document Verification Center
    Route::get('/account/verification', [VerificationController::class, 'index'])->name('account.verification.index');
    Route::post('/account/verification/document', [VerificationController::class, 'store'])->name('verification.document.store');

    // 11. Safety & Moderation Reports
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Admin Profile & Security Settings
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/info', [AdminProfileController::class, 'updateInfo'])->name('profile.updateInfo');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // ID Verifications Center
    Route::get('/verifications', [VerificationReviewController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{verification}', [VerificationReviewController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{verification}/approve', [VerificationReviewController::class, 'approve'])->name('verifications.approve');
    Route::post('/verifications/{verification}/reject', [VerificationReviewController::class, 'reject'])->name('verifications.reject');
    Route::post('/verifications/bulk', [VerificationReviewController::class, 'bulkAction'])->name('verifications.bulk');

    // User Management
    Route::resource('users', AdminUserController::class)->except(['create', 'store']);
    Route::post('users/{user}/suspend', [AdminUserController::class, 'toggleSuspend'])->name('users.suspend');
    Route::post('users/assign-role', [AdminUserController::class, 'assignRole'])->name('users.assignRole');
    Route::post('users/{user}/notes', [AdminUserController::class, 'updateNotes'])->name('users.notes');
    Route::post('users/{user}/reports/{report}/resolve', [AdminUserController::class, 'resolveReport'])->name('users.reports.resolve');
    Route::post('users/{user}/reports/{report}/dismiss', [AdminUserController::class, 'dismissReport'])->name('users.reports.dismiss');
    Route::post('users/bulk', [AdminUserController::class, 'bulkAction'])->name('users.bulk');

    // Listing Management
    Route::resource('listings', AdminListingController::class)->only(['index', 'show', 'destroy']);
    Route::post('listings/{listing}/toggle-status', [AdminListingController::class, 'toggleStatus'])->name('listings.toggleStatus');
    Route::post('listings/{listing}/update-status', [AdminListingController::class, 'updateStatus'])->name('listings.updateStatus');
    Route::post('listings/{listing}/toggle-featured', [AdminListingController::class, 'toggleFeatured'])->name('listings.toggleFeatured');
    Route::post('listings/{listing}/toggle-sponsored', [AdminListingController::class, 'toggleSponsored'])->name('listings.toggleSponsored');
    Route::post('listings/{listing}/reports/{report}/resolve', [AdminListingController::class, 'resolveReport'])->name('listings.reports.resolve');
    Route::post('listings/{listing}/reports/{report}/dismiss', [AdminListingController::class, 'dismissReport'])->name('listings.reports.dismiss');
    Route::post('listings/bulk', [AdminListingController::class, 'bulkAction'])->name('listings.bulk');

    // Listing Promotions & Revenue Management Hub
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [AdminPromotionController::class, 'index'])->name('index');
        Route::get('/export-csv', [AdminPromotionController::class, 'exportCsv'])->name('export-csv');
        Route::put('/packages/{package}', [AdminPromotionController::class, 'updatePackage'])->name('packages.update');
        Route::put('/quota', [AdminPromotionController::class, 'updateQuota'])->name('quota.update');
    });

    // Local Sponsor Banner Ads & AdSense Hub
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [AdminBannerAdController::class, 'index'])->name('index');
        Route::post('/', [AdminBannerAdController::class, 'store'])->name('store');
        Route::put('/{banner}', [AdminBannerAdController::class, 'update'])->name('update');
        Route::delete('/{banner}', [AdminBannerAdController::class, 'destroy'])->name('destroy');
        Route::post('/{banner}/toggle', [AdminBannerAdController::class, 'toggleStatus'])->name('toggle');
    });

    // Meetup Management
    Route::resource('meetups', AdminMeetupController::class)->only(['index', 'show', 'destroy']);
    Route::post('meetups/{meetup}/update-status', [AdminMeetupController::class, 'updateStatus'])->name('meetups.updateStatus');
    Route::post('meetups/bulk', [AdminMeetupController::class, 'bulkAction'])->name('meetups.bulk');
    Route::delete('meetups/{meetup}/attendees/{attendee}', [AdminMeetupController::class, 'removeAttendee'])->name('meetups.attendees.remove');

    // Dedicated Admin Notifications & Safety Queue Hub
    Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');

    // Safety & Abuse Reports Moderation Queue
    Route::resource('reports', AdminReportController::class)->only(['index', 'show']);
    Route::post('reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('reports/{report}/dismiss', [AdminReportController::class, 'dismiss'])->name('reports.dismiss');
    Route::post('reports/bulk', [AdminReportController::class, 'bulkAction'])->name('reports.bulk');

    // Category & Dynamic Custom Attribute Schema Management
    Route::resource('categories', AdminCategoryController::class);
    Route::post('categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggleStatus');

    Route::prefix('categories/{category}/attributes')->name('categories.attributes.')->group(function () {
        Route::get('/', [AdminCategoryAttributeController::class, 'index'])->name('index');
        Route::post('/', [AdminCategoryAttributeController::class, 'store'])->name('store');
        Route::get('/{attribute}', [AdminCategoryAttributeController::class, 'show'])->name('show');
        Route::put('/{attribute}', [AdminCategoryAttributeController::class, 'update'])->name('update');
        Route::delete('/{attribute}', [AdminCategoryAttributeController::class, 'destroy'])->name('destroy');
        Route::post('/{attribute}/toggle-status', [AdminCategoryAttributeController::class, 'toggleStatus'])->name('toggleStatus');
    });

    // Member Tiers & Points Configuration
    Route::prefix('member-tiers')->name('member-tiers.')->group(function () {
        Route::get('/', [AdminMemberTierController::class, 'index'])->name('index');
        Route::get('/{memberTier}', [AdminMemberTierController::class, 'show'])->name('show');
        Route::put('/{memberTier}', [AdminMemberTierController::class, 'update'])->name('update');
        Route::post('/adjust-points', [AdminMemberTierController::class, 'adjustPoints'])->name('adjustPoints');
        Route::put('/rules/update', [AdminMemberTierController::class, 'updateRules'])->name('updateRules');
    });

    // Canadian Locations & Cities Management Hub
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [AdminLocationController::class, 'index'])->name('index');
        Route::post('/cities', [AdminLocationController::class, 'storeCity'])->name('cities.store');
        Route::get('/cities/{city}', [AdminLocationController::class, 'showCity'])->name('cities.show');
        Route::put('/cities/{city}', [AdminLocationController::class, 'updateCity'])->name('cities.update');
        Route::post('/cities/{city}/toggle-active', [AdminLocationController::class, 'toggleCityActive'])->name('cities.toggleActive');
        Route::post('/cities/{city}/toggle-featured', [AdminLocationController::class, 'toggleCityFeatured'])->name('cities.toggleFeatured');
        Route::delete('/cities/{city}', [AdminLocationController::class, 'destroyCity'])->name('cities.destroy');

        Route::get('/provinces/{province}', [AdminLocationController::class, 'showProvince'])->name('provinces.show');
        Route::put('/provinces/{province}', [AdminLocationController::class, 'updateProvince'])->name('provinces.update');
        Route::post('/provinces/{province}/toggle-active', [AdminLocationController::class, 'toggleProvinceActive'])->name('provinces.toggleActive');
    });

    // Live Support & Helpdesk Hub
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [AdminSupportManagementController::class, 'index'])->name('index');
        Route::get('/inbox-poll', [AdminSupportManagementController::class, 'pollInbox'])->name('pollInbox');
        Route::get('/{conversation}', [AdminSupportManagementController::class, 'show'])->name('show');
        Route::post('/{conversation}/reply', [AdminSupportManagementController::class, 'reply'])->name('reply');
        Route::post('/{conversation}/status', [AdminSupportManagementController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/{conversation}/poll', [AdminSupportManagementController::class, 'poll'])->name('poll');
    });

    // Platform & Site Settings Management Hub
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('index');
        Route::put('/', [AdminSettingController::class, 'update'])->name('update');
    });
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
