# Bon Trouver — AI Agent & Developer Guidelines

This document outlines the architectural standards, Eloquent relationships map, and development guidelines for the **Bon Trouver** Canadian classifieds and community platform.

---

## 1. Project Standards & Sources of Truth (Doc Directory)

Whenever building any functionality, modifying code, designing migrations, or responding to user commands, **ALWAYS strictly adhere** to the specifications in the `doc/` directory:

1. **[Client Requirements Specification](file:///Users/zesan/Desktop/My-Work/bontrouver/doc/client_requirements.md)**:
   - **Location-First**: All listings and services require exact coordinates (`latitude`, `longitude`), postal code (`A1A 1A1`), city, and province.
   - **Smart Alerts**: Category, location, keyword, and min/max price triggers (e.g., *"A new room for $700 in Montreal"*).
   - **Reputation**: Reviews, star ratings (1–5 ⭐), verified transaction counts, community help points, and verification badges (`is_verified`, `is_dealer`).
   - **Point System & Member Levels**: 🥉 New Member (0-99) → 🥈 Active Member (100-299) → 🥇 Trusted Member (300-699) → ⭐ Highly Appreciated Member (700+). Mutual aid reward system, *never directly exchangeable for money*.
   - **Community (Need Companionship)**: Wholesome social meetups (☕ Coffee & Chat, 🚶 Walk, 🍽️ Dining, 🎬 Cinema, ⚽ Match, 🏃 Sports, 🎮 Gaming, 🌆 Outings, 🗣️ Meet people).

2. **[Database Design Specification](file:///Users/zesan/Desktop/My-Work/bontrouver/doc/database_design.md)**:
   - Standardized table naming, indexing on foreign keys/location, soft deletes, and data integrity.

3. **[Software Architecture Specification](file:///Users/zesan/Desktop/My-Work/bontrouver/doc/software_architecture.md)**:
   - **MVC-S Flow**: `Request → Route → Form Request (Validation) → Controller → Service Layer → Model / Query → View / Resource`.
   - **Thin Controllers**: Controllers only handle HTTP orchestration; all business logic lives in dedicated `app/Services/` classes.

4. **[Admin Panel UI & Design System Specification](file:///Users/zesan/Desktop/My-Work/bontrouver/doc/admin_design_system.md)**:
   - **Unified Heights**: 34px toolbar controls across all search fields, filters, bulk selects, and buttons.
   - **Zero Inline Styles**: All layout and component styles strictly compiled from `table.scss` and `style.scss`.
   - **Modal & Toast Standards**: `<x-admin.confirm-modal />` and `window.showWarningModal()` for all destructive actions; `window.showToast()` for AJAX feedback.
   - **100% Dynamic**: Zero mock placeholders; live Eloquent data bindings and AJAX tab pagination.

5. **[Project Status & Feature Implementation Report](file:///Users/zesan/Desktop/My-Work/bontrouver/doc/project_status_report.md)**:
   - Live tracker of implementation percentage, completed modules, and roadmap. Always maintain 100% sync.

---

## 2. Eloquent Models & Relationship Cheat-Sheet

All models are located in `app/Models/`. Use these exact relationship methods:

### User (`App\Models\User`)
- `$user->listings()` → `hasMany(Listing::class)`
- `$user->pointTransactions()` → `hasMany(PointTransaction::class)`
- `$user->purchases()` → `hasMany(Transaction::class, 'buyer_id')`
- `$user->sales()` → `hasMany(Transaction::class, 'seller_id')`
- `$user->favorites()` → `hasMany(Favorite::class)`
- `$user->reviewsReceived()` → `hasMany(Review::class, 'reviewee_id')`
- `$user->reviewsGiven()` → `hasMany(Review::class, 'reviewer_id')`
- `$user->smartAlerts()` → `hasMany(SmartAlert::class)`
- `$user->companionshipRequests()` → `hasMany(CompanionshipRequest::class)`
- `$user->verifications()` → `hasMany(UserVerification::class)`
- `$user->latestVerification()` → `hasOne(UserVerification::class)->latestOfMany()`
- `$user->profile()` → `hasOne(UserProfile::class)`
- `$user->gallery()` → `hasMany(UserGallery::class)->orderBy('sort_order')`
- Accessors: `$user->avatar_url` (safe URL for remote & local avatars), `$user->rating` (avg rating), `$user->reviews_count` (total count), `$user->member_tier` (array of tier name, icon, level, progress %), `$user->completed_transactions_count` (total completed deals), `$user->verification_status`
- Helpers: `$user->isAdmin()`, `$user->isModerator()`, `$user->wantsNotification(string $type)`
- Fields: `name`, `email`, `password`, `role`, `is_dealer`, `phone`, `city`, `province`, `postal_code`, `location`, `avatar`, `bio`, `community_points`, `is_verified`, `notification_preferences` (json)

### Category (`App\Models\Category`)
- `$category->parent()` → `belongsTo(Category::class, 'parent_id')`
- `$category->children()` → `hasMany(Category::class, 'parent_id')`
- `$category->attributes()` → `hasMany(CategoryAttribute::class)`
- `$category->listings()` → `hasMany(Listing::class)`
- Helper: `Category::getTree()` (hierarchical category array)

### CategoryAttribute (`App\Models\CategoryAttribute`)
- `$attr->category()` → `belongsTo(Category::class)`
- `$attr->options()` → `hasMany(AttributeOption::class)`

### AttributeOption (`App\Models\AttributeOption`)
- `$option->attribute()` → `belongsTo(CategoryAttribute::class, 'category_attribute_id')`

### Province (`App\Models\Province`)
- `$province->cities()` → `hasMany(City::class)`
- `$province->listings()` → `hasManyThrough(Listing::class, City::class)`
- `$province->smartAlerts()` → `hasMany(SmartAlert::class)`
- Fields: `name`, `code`, `slug`, `country_code`, `is_active`, `sort_order`

### City (`App\Models\City`)
- `$city->province()` → `belongsTo(Province::class)`
- `$city->listings()` → `hasMany(Listing::class)`
- `$city->companionshipRequests()` → `hasMany(CompanionshipRequest::class)`
- `$city->smartAlerts()` → `hasMany(SmartAlert::class)`
- Helper: `City::getCitiesMap()` (cached active Canadian cities with province metadata & coordinates)

### Listing (`App\Models\Listing`)
- `$listing->user()` → `belongsTo(User::class)`
- `$listing->category()` → `belongsTo(Category::class)`
- `$listing->city()` → `belongsTo(City::class)`
- `$listing->images()` → `hasMany(ListingImage::class)->orderBy('sort_order')`
- `$listing->primaryImage()` → `hasOne(ListingImage::class)->where('is_primary', true)`
- `$listing->attributes()` → `hasMany(ListingAttribute::class)`
- `$listing->favorites()` → `hasMany(Favorite::class)`
- `$listing->views()` → `hasMany(ListingView::class)`
- `$listing->conversations()` → `hasMany(Conversation::class)`
- `$listing->transactions()` → `hasMany(Transaction::class)`
- `$listing->reports()` → `morphMany(Report::class, 'reportable')`
- `$listing->promotions()` → `hasMany(ListingPromotion::class)`
- `$listing->activePromotions()` → `hasMany(ListingPromotion::class)->active()`
- Enums & Casts: `status` (`App\Enums\ListingStatus`: `DRAFT`, `PENDING_REVIEW`, `ACTIVE`, `PAUSED`, `SOLD`, `EXPIRED`, `REJECTED`)
- Flags & Boosts: `is_featured` (for Featured section), `is_sponsored` (for Hero carousel), `featured_until`, `sponsored_until`, `bumped_at` (bump to top)
- Accessor: `$listing->primary_image_url` (returns primary image URL or fallback to `/images/no-image.svg`)
- Scopes: `scopeActive()`, `scopeFeatured()`, `scopeSponsored()`, `scopeBumped()`, `scopeWithinRadius()`
- Helpers: `$listing->isFeatured()`, `$listing->isSponsored()`, `$listing->isBumped()`

### ListingView (`App\Models\ListingView`)
- `$view->listing()` → `belongsTo(Listing::class)`
- `$view->user()` → `belongsTo(User::class)`
- Fields: `listing_id`, `user_id`, `ip_address`, `user_agent`, `referer`, `viewed_date` (date)

### ListingImage (`App\Models\ListingImage`)
- `$image->listing()` → `belongsTo(Listing::class)`
- Accessor: `$image->url` (returns clean absolute URL with fallback)

### ImageOptimizationService & WebP Compression (`App\Services\ImageOptimizationService`)
- Automated image conversion to lightweight, compressed WebP format (85% quality, EXIF orientation correction, max dimension scaling).
- Batch artisan command: `php artisan listings:convert-images-webp`.

### ListingAnalyticsService (`App\Services\ListingAnalyticsService`)
- Aggregates unique daily listing views, saves, inquiries, and engagement rates for seller dashboard.
- Interactive modal with ApexCharts daily views trend line at `/my-listings/{id}/analytics`.

### Scheduled Expiry Command (`App\Console\Commands\CheckListingAndPromotionExpiry`)
- Hourly cron job `php artisan listings:check-expiry`: deactivates expired boosts, sends 24h expiration warning notifications, and marks stale listings expired with instant internal database alerts.

### ListingAttribute (`App\Models\ListingAttribute`)
- `$listingAttr->listing()` → `belongsTo(Listing::class)`
- `$listingAttr->categoryAttribute()` → `belongsTo(CategoryAttribute::class, 'category_attribute_id')`

### MemberTier (`App\Models\MemberTier`)
- Columns: `name`, `icon`, `badge_color`, `badge_class`, `min_points`, `max_points`, `description`, `perks` (json)
- Accessor: `$tier->clean_name` (strips leading emoji for clean display)

### Review (`App\Models\Review`)
- `$review->reviewer()` → `belongsTo(User::class, 'reviewer_id')`
- `$review->reviewee()` → `belongsTo(User::class, 'reviewee_id')`
- `$review->listing()` → `belongsTo(Listing::class)`

### Favorite (`App\Models\Favorite`)
- `$favorite->user()` → `belongsTo(User::class)`
- `$favorite->listing()` → `belongsTo(Listing::class)`

### Conversation (`App\Models\Conversation`)
- `$conv->listing()` → `belongsTo(Listing::class)`
- `$conv->buyer()` → `belongsTo(User::class, 'buyer_id')`
- `$conv->seller()` → `belongsTo(User::class, 'seller_id')`
- `$conv->messages()` → `hasMany(Message::class)`

### Message (`App\Models\Message`)
- `$msg->conversation()` → `belongsTo(Conversation::class)`
- `$msg->sender()` → `belongsTo(User::class, 'sender_id')`

### SmartAlert (`App\Models\SmartAlert`)
- `$alert->user()` → `belongsTo(User::class)`
- `$alert->category()` → `belongsTo(Category::class)`
- `$alert->cityRelation()` → `belongsTo(City::class, 'city_id')`
- `$alert->province()` → `belongsTo(Province::class)`
- `$alert->attributes()` → `hasMany(SmartAlertAttribute::class)`

### SmartAlertAttribute (`App\Models\SmartAlertAttribute`)
- `$alertAttr->smartAlert()` → `belongsTo(SmartAlert::class)`
- `$alertAttr->categoryAttribute()` → `belongsTo(CategoryAttribute::class)`

### CompanionshipRequest (`App\Models\CompanionshipRequest`)
- `$req->user()` → `belongsTo(User::class)`
- `$req->cityRelation()` → `belongsTo(City::class, 'city_id')`
- `$req->attendees()` → `hasMany(CompanionshipAttendee::class)`

### CompanionshipAttendee (`App\Models\CompanionshipAttendee`)
- `$att->companionshipRequest()` → `belongsTo(CompanionshipRequest::class)`
- `$att->user()` → `belongsTo(User::class)`

### Transaction (`App\Models\Transaction`)
- `$txn->listing()` → `belongsTo(Listing::class)`
- `$txn->buyer()` → `belongsTo(User::class, 'buyer_id')`
- `$txn->seller()` → `belongsTo(User::class, 'seller_id')`

### PointTransaction (`App\Models\PointTransaction`)
- `$pt->user()` → `belongsTo(User::class)`
- `$pt->reference()` → `morphTo()`

### PointRule (`App\Models\PointRule`)
- Columns: `rule_key`, `name`, `type` (`earn`, `spend`), `points`, `category`, `description`, `is_active`, `sort_order`
- Scopes: `scopeEarn()`, `scopeSpend()`, `scopeActive()`

### Report (`App\Models\Report`)
- `$report->reporter()` → `belongsTo(User::class, 'reporter_id')`
- `$report->reviewer()` → `belongsTo(User::class, 'reviewed_by')`
- `$report->reportable()` → `morphTo()`
- Casts: `reason` (`App\Enums\ReportReason`), `reviewed_at` (`datetime`)

### UserVerification (`App\Models\UserVerification`)
- `$verif->user()` → `belongsTo(User::class)`
- `$verif->reviewer()` → `belongsTo(User::class, 'reviewed_by')`
- Fields: `user_id`, `document_type`, `document_path`, `id_number`, `phone_number`, `phone_verified_at`, `status`, `rejection_reason`, `reviewed_at`, `reviewed_by`

### UserProfile (`App\Models\UserProfile`)
- `$profile->user()` → `belongsTo(User::class)`
- Fields: `user_id`, `cover_image_path`, `about_text`, `website_url`, `social_links` (json), `operating_hours` (json), `features` (json)

### UserGallery (`App\Models\UserGallery`)
- `$gallery->user()` → `belongsTo(User::class)`
- Fields: `user_id`, `image_path`, `sort_order`

### PromotionPackage (`App\Models\PromotionPackage`)
- `$pkg->listingPromotions()` → `hasMany(ListingPromotion::class)`
- Columns: `name`, `slug`, `type` (`sponsored`, `featured`, `bump_up`), `badge_text`, `badge_color`, `badge_icon`, `price`, `point_cost`, `duration_days`, `description`, `features` (json), `is_active`, `sort_order`

### ListingPromotion (`App\Models\ListingPromotion`)
- `$promo->listing()` → `belongsTo(Listing::class)`
- `$promo->user()` → `belongsTo(User::class)`
- `$promo->package()` → `belongsTo(PromotionPackage::class, 'promotion_package_id')`
- Columns: `listing_id`, `user_id`, `promotion_package_id`, `type`, `price_paid`, `points_spent`, `payment_method`, `payment_status`, `transaction_reference`, `starts_at`, `expires_at`, `is_active`

### BannerAd (`App\Models\BannerAd`)
- Columns: `title`, `position` (`homepage_top`, `homepage_middle`, `homepage_bottom`, `homepage_leaderboard`, `search_sidebar`, `listing_detail_bottom`, `community_sidebar`), `image_path`, `target_url`, `html_code`, `city`, `province`, `is_active`, `sort_order`, `impressions_count`, `clicks_count`, `starts_at`, `expires_at`

### SearchQuery (`App\Models\SearchQuery`)
- Fields: `query`, `hits_count`, `results_count`, `last_searched_at`
- Methods: `SearchQuery::recordSearch(string $query, int $resultsCount)`, `SearchQuery::getTrendingKeywords(int $limit = 8)`

### SiteSetting (`App\Models\SiteSetting`)
- Fields: `key`, `value`, `group`, `type`, `description`
- Methods: `SiteSetting::get(string $key, mixed $default = null)`, `SiteSetting::set(string $key, mixed $value, string $group = 'general', string $type = 'string')`, `SiteSetting::getAllGrouped()`, `SiteSetting::flushCache()`
- Helpers: `site_setting(string $key, mixed $default = null)`

### SupportConversation (`App\Models\SupportConversation`)
- `$conv->user()` → `belongsTo(User::class)`
- `$conv->assignee()` → `belongsTo(User::class, 'assigned_to')`
- `$conv->messages()` → `hasMany(SupportMessage::class)`
- `$conv->latestMessage()` → `hasOne(SupportMessage::class)->latestOfMany()`
- `$conv->unreadMessagesForAdmin()` → `hasMany(SupportMessage::class)->where('sender_type', 'user')->where('is_read', false)`
- Columns: `user_id`, `subject` (nullable), `status` (`open`, `in_progress`, `resolved`, `closed`), `priority` (`normal`, `high`, `urgent`), `assigned_to`, `last_message_at`

### SupportMessage (`App\Models\SupportMessage`)
- `$msg->conversation()` → `belongsTo(SupportConversation::class, 'support_conversation_id')`
- `$msg->sender()` → `belongsTo(User::class, 'sender_id')`
- Accessors: `$msg->attachment_urls` (returns public storage URLs), `$msg->attachment_files` (returns array of structured metadata: `url`, `name`, `extension`, `is_image`, `is_pdf`, `is_doc`, `size_human`)
- Columns: `support_conversation_id`, `sender_id`, `sender_type` (`user`, `admin`, `system`), `message`, `attachments` (json), `is_read`, `read_at`

### SupportChatService & Admin Helpdesk Hub (`App\Services\SupportChatService`, `App\Http\Controllers\Admin\SupportManagementController`, `App\Http\Controllers\SupportChatController`)
- Global floating chat wizard `<x-support-chat-widget />` pinned on every page with direct live chat, pre-send attachment previews, rich file cards, and image lightbox viewer.
- Real-Time WebSocket Infrastructure: Powered by **Laravel Reverb** via `App\Events\SupportMessageSent` on channels `support.conversation.{id}`, `admin.support`, and `App.Models.User.{id}` for instant sub-millisecond bidirectional communication across both the floating widget and `/admin/support` desk.
- Guest Protection: Unauthenticated visitors see a friendly login/registration gate with return URL redirect and browsable instant FAQ topics.
- Authenticated Users: Instant conversation initiation with direct messaging, multi-file attachments (images, PDF, Word doc, Excel), and real-time WebSocket updates.
- Admin Hub: `/admin/support` with live WebSocket stream, instant thread insertion and dynamic sidebar re-ordering, image lightbox modal, and direct downloads.

### AdminNotificationService & Notifications Hub (`App\Services\AdminNotificationService`, `App\Http\Controllers\Admin\NotificationController`)
- Aggregates high-priority admin alerts strictly for User Safety Reports and Canadian ID Verifications.
- Integrated directly into the top navbar notification bell dropdown (`admin.includes.header`) with dynamic count badges and 1-click jump links.
- Dedicated moderation hub at `/admin/notifications` (`notifications.index`) with category switcher tabs (All, Reports, Verifications), status filter (Pending, Resolved), search, pagination, and direct inspection links.

### StripeService & Monetization (`App\Services\StripeService`, `App\Services\MonetizationService`)
- Real-time Stripe API charges, Checkout Sessions & PaymentIntents (`createCheckoutSession`, `createPaymentIntent`, `chargeCard`, `verifyWebhookSignature`).
- Internal platform notifications: `ListingBoostActivated` dispatched upon boost activation to the user's database notification feed (`/notifications`).
- Admin Revenue Hub: `/admin/promotions` with 30-day ApexCharts CAD revenue area chart, Boost tier share donut chart, package pricing & policy manager, live transaction audit ledger, and CSV ledger export (`GET /admin/promotions/export-csv`).

---

## 3. Code Standards & Development Rules

1. **Service Layer Usage**: Place heavy logic (search algorithms, file uploads, notification triggers, point awards) inside `app/Services/`.
2. **Form Request Validation**: Always use dedicated Form Request classes for validation (`app/Http/Requests/`).
3. **Location Consistency**: Always store `latitude`, `longitude`, `city`, and `province` for any location-based model.
4. **Eager Loading**: Prevent N+1 query issues by eager-loading relationships (`with(['user', 'primaryImage', 'category'])`).
5. **Aesthetics & UI**: Use rich, dynamic styles with responsive design, clear contrast, and zero layout overflow.
6. **Mandatory Documentation & Schema Synchronization**: Whenever any model relationship, migration field, schema, feature, or model property is added, modified, or deleted, **immediately update this `AGENTS.md` and all corresponding documents in `doc/` (`database_design.md`, `software_architecture.md`, `client_requirements.md`, `project_status_report.md`)** to maintain 100% sync.

