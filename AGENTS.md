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

4. **[Project Status & Feature Implementation Report](file:///Users/zesan/Desktop/My-Work/bontrouver/doc/project_status_report.md)**:
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
- Accessors: `$user->rating` (avg rating), `$user->reviews_count` (total count), `$user->member_tier` (array of tier name, icon, level, progress %), `$user->completed_transactions_count` (total completed deals), `$user->verification_status`
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
- `$listing->conversations()` → `hasMany(Conversation::class)`
- `$listing->transactions()` → `hasMany(Transaction::class)`
- Flags: `is_featured` (for Featured section), `is_sponsored` (for Hero carousel)

### ListingImage (`App\Models\ListingImage`)
- `$image->listing()` → `belongsTo(Listing::class)`

### ListingAttribute (`App\Models\ListingAttribute`)
- `$listingAttr->listing()` → `belongsTo(Listing::class)`
- `$listingAttr->categoryAttribute()` → `belongsTo(CategoryAttribute::class, 'category_attribute_id')`

### MemberTier (`App\Models\MemberTier`)
- Columns: `name`, `min_points`, `max_points`

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

### Report (`App\Models\Report`)
- `$report->reporter()` → `belongsTo(User::class, 'reporter_id')`
- `$report->reportable()` → `morphTo()`

### UserVerification (`App\Models\UserVerification`)
- `$verif->user()` → `belongsTo(User::class)`
- `$verif->reviewer()` → `belongsTo(User::class, 'reviewed_by')`
- Fields: `user_id`, `document_type`, `document_path`, `id_number`, `phone_number`, `phone_verified_at`, `status`, `rejection_reason`, `reviewed_at`, `reviewed_by`

---

## 3. Code Standards & Development Rules

1. **Service Layer Usage**: Place heavy logic (search algorithms, file uploads, notification triggers, point awards) inside `app/Services/`.
2. **Form Request Validation**: Always use dedicated Form Request classes for validation (`app/Http/Requests/`).
3. **Location Consistency**: Always store `latitude`, `longitude`, `city`, and `province` for any location-based model.
4. **Eager Loading**: Prevent N+1 query issues by eager-loading relationships (`with(['user', 'primaryImage', 'category'])`).
5. **Aesthetics & UI**: Use rich, dynamic styles with responsive design, clear contrast, and zero layout overflow.
6. **Mandatory Documentation & Schema Synchronization**: Whenever any model relationship, migration field, schema, feature, or model property is added, modified, or deleted, **immediately update this `AGENTS.md` and all corresponding documents in `doc/` (`database_design.md`, `software_architecture.md`, `client_requirements.md`, `project_status_report.md`)** to maintain 100% sync.

