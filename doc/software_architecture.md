# Bontrouver Software Architecture

This document defines the authoritative, enterprise-grade software architecture for the Bontrouver marketplace. It reflects all architectural decisions, patterns, directory structure, and system design.

---

## 1. Architectural Pattern

The application follows the **Model-View-Controller-Service (MVC-S)** architectural pattern, standard for enterprise Laravel applications.

| Layer | Responsibility |
|-------|---------------|
| **Models (Eloquent ORM)** | Data entities, relationships, casts, scopes, and persistence behavior. Business logic must NOT live here. |
| **Views (Blade Templates)** | Server-side rendered HTML with Bootstrap 5. Maximizes SEO compatibility and minimizes client-side overhead. |
| **Controllers** | Traffic cop — receives HTTP requests, delegates to Services, returns View or JSON. Must stay thin. |
| **Form Requests** | Input validation, authorization checks, and category-specific validation rules. |
| **Service Layer** | All core business logic (Reputation, Alerts, Messaging, etc.). Reusable between Web and API. |
| **Policies** | Authorization rules. Who can edit, delete, publish, or pause a listing? |
| **Events & Listeners** | Decouple system actions (e.g., listing published) from their side effects (alert matching, notifications). |
| **API Resources** | Transform Eloquent models into consistent JSON responses for API consumers. |

### Full Request Flow

```
HTTP Request
    ↓
Route (web.php / api.php)
    ↓
Controller (thin — delegates only)
    ↓
Form Request (validation + authorization)
    ↓
Policy (can this user perform this action?)
    ↓
Service (business logic)
    ↓
Model / Eloquent Query
    ↓
Database (MySQL)
    ↓
Resource (JSON) or View (Blade)
```

### Example: Post an Ad

```
POST /post-ad
    ↓
ListingController@store
    ↓
StoreListingRequest  (validate title, price, location, dynamic attributes)
    ↓
ListingPolicy@create (is user allowed to post?)
    ↓
ListingService
    ├── create listing record
    ├── save dynamic attributes (EAV)
    ├── upload and save images
    ├── publish listing (status → active)
    └── fire ListingPublished event
            ↓
    ProcessSmartAlertsJob (queued)
            ↓
    AlertMatcherService
            ↓
    SmartAlertTriggered Notification
```

---

## 2. Technology Stack

| Concern | Technology |
|---------|-----------|
| **Framework** | Laravel 13.x |
| **PHP** | PHP 8.3+ |
| **Database** | MySQL (via Eloquent ORM) |
| **Frontend CSS** | Bootstrap 5.3 + Custom Vanilla CSS (CSS variables) |
| **Frontend JS** | Vanilla JavaScript (dynamic attributes, galleries, drawers) |
| **Authentication** | Laravel Breeze (Session / Cookie-based) |
| **Background Processing** | Laravel Queues (Database or Redis driver) |
| **Notifications** | Laravel Notifications (Database / In-app, optionally Email) |

---

## 3. User Roles & Dealer Status

Roles and dealer status are intentionally kept **separate concerns**:

```
role = 'user'         → standard marketplace user
role = 'admin'        → full platform management
role = 'moderator'    → content moderation only

is_dealer = true      → operates as a commercial entity (business listing badge, no role change)
is_dealer = false     → individual private user
```

**Why `is_dealer` is NOT a role:** Dealers are still regular marketplace users who buy, sell, message, favorite, and earn reputation. Dealer status is a **profile capability**, not an authorization level.

### 3.1. Dual-Tier Registration Architecture
To provide casual community members with a 30-second onboarding while ensuring commercial car dealerships, real estate agencies, and retailers are fully verified:

1. **General User Registration (`/register` → `RegisteredUserController`)**:
   - Streamlined, minimalist form collecting only: `name`, `email`, `password`, `password_confirmation`, `province`, and `city`.
   - `is_dealer` defaults to `false`.
   - No business tax IDs, licensing documents, or commercial fields required.

2. **Merchant / Professional Dealer Registration (`/register/merchant` → `MerchantRegistrationController`)**:
   - Specialized commercial onboarding collecting:
     - Dealership / Business Name (`name`)
     - Business Contact Phone / WhatsApp (`phone`)
     - Storefront Address & Canadian Postal Code (`location`, `postal_code`)
     - Canadian Province and City (`province`, `city`)
     - Business License / Dealer Number / OMVIC ID
     - Document Verification Upload (stored via `UserVerification`)
     - Operating Hours & Website URL
   - Sets `is_dealer = true` and kicks off commercial identity verification.

---

## 4. Core System Components

### 4.1. The Listing Engine

- **Dynamic Attributes (EAV Pattern):** Because "Cars" have different fields (Make, Model, Year) than "Apartments" (Bedrooms, Bathrooms), the system uses `category_attributes`, `attribute_options`, and `listing_attributes` tables to dynamically attach specs to any listing based on its category.
- **EAV Limitation Note:** The current schema supports one value per attribute. Multi-select (e.g., Amenities: Parking + Balcony + Pool) is a known future requirement and should be addressed when multi-select category attributes are introduced.
- **Location-Based Filtering:** Queries leverage `city`, `province`, and optionally geospatial `latitude`/`longitude` for radius searches, exposed via Eloquent scopes (e.g., `Listing::inCity('Montreal')->active()->get()`).
- **Status Lifecycle:** `draft → pending_review → active → paused / sold / expired / rejected`

### 4.2. Reputation & Points Engine (Trust System)

- **`ReputationService`:** Encapsulates all point-awarding logic. Example: `ReputationService::award($user, 10, 'received_5_star_review')`.
- **Transaction Safety:** `community_points` on the `users` table is a **cached running balance**. `point_transactions` is the **audit source of truth**. Both must always be updated together inside a DB transaction:

```php
DB::transaction(function () use ($user, $points, $action) {
    PointTransaction::create([...]);
    $user->increment('community_points', $points);
});
```

- **`MemberTierService`:** Member tier calculation (New → Active → Trusted → Highly Appreciated) is handled by a **dedicated service**, NOT a User model accessor. Tiers are loaded from the `member_tiers` database table — not hardcoded conditionals.

```
community_points
      ↓
MemberTierService::resolveForUser($user)
      ↓
Queries member_tiers table
      ↓
Returns MemberTier (New / Active / Trusted / Highly Appreciated)
```

### 4.3. Smart Alerts System (Event-Driven)

- **Trigger:** `ListingPublished` event fires when a listing transitions to `active` status — NOT `ListingObserver@created`, since a listing may be created as a `draft` long before it becomes active.
- **Job:** `ProcessSmartAlertsJob` receives the listing ID, loads data, finds candidate alerts, and dispatches notifications.
- **Matcher:** `AlertMatcherService::matches($listing, $alert)` — pure, testable boolean logic with no side effects.

```
Listing published (status → active)
      ↓
ListingPublished event fired
      ↓
ProcessSmartAlertsJob (queued)
      ↓
AlertMatcherService::matches($listing, $alert)
      ↓
SmartAlertTriggered Notification (database / email)
```

### 4.4. Community Companionship Module

- Operates independently from commercial listings via `companionship_requests` and `companionship_attendees` tables.
- Safety is enforced through the Reputation Engine — users with higher trust tiers have their meetup requests highlighted.
- Managed by `CompanionshipService`.

### 4.5. Standard Marketplace Functionality

- **Authentication & Authorization:** Laravel Breeze for auth; Policies for authorization.
- **Ad Management:** Sellers post, edit, pause, promote, and mark listings as sold.
- **Search & Filtering:** Keyword search, category/location filters, price range, dynamic attribute filters.
- **Messaging Engine:** In-app inbox via `conversations` and `messages`. No email exposure.
- **Dashboard:** Unified hub for active ads, saved favorites, messages, notifications, and community points.

### 4.6. Dynamic Profile-Aware Ad Location Pre-Population

- **Location Priority Engine**: Eliminates static hardcoded defaults (such as Toronto). When an authenticated user initiates ad creation at `/post-ad`:
  - The controller/view extracts the user's registered `city` and `province` from `auth()->user()`.
  - The province and city `<select>` controls automatically pre-select their home locality (e.g., Montreal, Quebec).
  - Unauthenticated users or users with empty location profiles fallback to platform default settings (`site_setting('default_province')`, `site_setting('default_city')`).

### 4.7. Multilingual Content & Dynamic "Traduire" (Translate) Pipeline

- **Bilingual System with Open Multilingual Content**: The system operates natively in French and English, but user-submitted listings can be in any language (French, English, Arabic, Spanish, etc.).
- **On-Demand Client/Server Translation Architecture**:
  - Listing cards (`<x-listing-card />`) and the listing detail page (`listings/show.blade.php`) feature a reactive **"Traduire" (Translate)** action toggle.
  - Clicking "Traduire" dynamically translates the title and description into the viewer's active language (French ↔ English) without reloading the page.
  - Prevents database pollution: original listing text is preserved in MySQL `listings.title` and `listings.description`, while translations are rendered and cached dynamically.

### 4.8. High-Capacity Media Pipeline & Exception Interception

- **High-Resolution Allowance**: Configurable via `site_settings.max_image_upload_size_kb` (up to 25MB per image).
- **Graceful Error Recovery**: `PostTooLargeException` is trapped globally in `bootstrap/app.php` to prevent HTTP 500 fatal errors when users upload massive photo batches, redirecting with actionable error messages.
- **ImageOptimizationService**: Automatically downsizes dimensions, corrects EXIF camera orientations, and recompresses into modern WebP format (85% quality) before persisting to public storage.

---

## 5. Dual-Mode: Web + API Architecture

The same Service Layer serves both Blade web views and REST API responses:

```
Blade (Web)           API Request
    ↓                      ↓
Controller            API Controller
    ↓                      ↓
Service  ←————————————→ Service  (shared)
    ↓                      ↓
View (Blade)          Resource (JSON)
```

---

## 6. Directory Structure

```
app/
├── Events/
│   ├── ListingPublished.php
│   ├── ReviewSubmitted.php
│   ├── TransactionCompleted.php
│   └── PointsAwarded.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Web/          ← Blade-facing controllers
│   │   └── Api/          ← API-facing controllers
│   │
│   ├── Requests/
│   │   ├── StoreListingRequest.php
│   │   ├── UpdateListingRequest.php
│   │   ├── StoreReviewRequest.php
│   │   ├── StoreSmartAlertRequest.php
│   │   ├── StoreCompanionshipRequest.php
│   │   └── SendMessageRequest.php
│   │
│   └── Resources/
│       ├── ListingResource.php
│       ├── UserResource.php
│       ├── CategoryResource.php
│       ├── ReviewResource.php
│       ├── ConversationResource.php
│       ├── MessageResource.php
│       └── SmartAlertResource.php
│
├── Jobs/
│   ├── ProcessSmartAlertsJob.php
│   └── SendListingPublishedNotification.php
│
├── Models/
│   ├── User.php
│   ├── MemberTier.php
│   ├── Province.php
│   ├── City.php
│   ├── Listing.php
│   ├── ListingImage.php
│   ├── ListingAttribute.php
│   ├── Category.php
│   ├── CategoryAttribute.php
│   ├── AttributeOption.php
│   ├── Transaction.php
│   ├── Favorite.php
│   ├── Review.php
│   ├── Conversation.php
│   ├── Message.php
│   ├── SmartAlert.php
│   ├── SmartAlertAttribute.php
│   ├── CompanionshipRequest.php
│   ├── CompanionshipAttendee.php
│   ├── Report.php
│   ├── PointTransaction.php
│   ├── PointRule.php
│   ├── UserVerification.php
│   ├── UserProfile.php
│   ├── UserGallery.php
│   ├── PromotionPackage.php
│   ├── ListingPromotion.php
│   ├── BannerAd.php
│   ├── SiteSetting.php
│   ├── SearchQuery.php
│   ├── SupportConversation.php
│   └── SupportMessage.php
│
├── Notifications/
│   ├── SmartAlertMatched.php
│   ├── ListingBoostActivated.php
│   ├── MeetupJoinRequested.php
│   ├── MeetupAttendeeStatusUpdated.php
│   ├── MeetupCancelled.php
│   └── MemberTierUpgraded.php
│
├── Observers/
│   └── ListingObserver.php
│
├── Policies/
│   ├── ListingPolicy.php       ← Can user edit/delete/pause/publish/mark sold?
│   ├── ReviewPolicy.php        ← Can user leave a review?
│   └── ConversationPolicy.php  ← Can user access this conversation?
│
└── Services/
    ├── CategoryService.php
    ├── LocationService.php
    ├── AdminLocationService.php
    ├── AdminDashboardService.php
    ├── AdminNotificationService.php
    ├── ListingService.php
    ├── ListingSearchService.php
    ├── ReputationService.php
    ├── PointService.php
    ├── MemberTierService.php
    ├── AlertMatcherService.php
    ├── SmartAlertService.php
    ├── MessagingService.php
    ├── VerificationService.php
    ├── MonetizationService.php
    ├── SiteSettingService.php
    ├── SupportChatService.php
    └── CompanionshipService.php
```

---

## 7. Full Architecture Diagram

```
                ┌──────────────────────┐
                │    Web / API Layer   │
                │  Blade + REST API    │
                └──────────┬───────────┘
                           ↓
                ┌──────────────────────┐
                │     Controllers      │
                │  (thin — delegate)   │
                └──────────┬───────────┘
                           ↓
                ┌──────────────────────┐
                │   Form Requests      │
                │   Policies           │
                └──────────┬───────────┘
                           ↓
                ┌──────────────────────┐
                │     Services         │
                │  (all business logic)│
                └──────────┬───────────┘
                           ↓
           ┌───────────────┴────────────────┐
           ↓                                ↓
  ┌────────────────┐                ┌───────────────┐
  │ Eloquent Models│                │    Events     │
  │ (relations,    │                │  / Listeners  │
  │  scopes, casts)│                └───────┬───────┘
  └───────┬────────┘                        ↓
          ↓                         ┌───────────────┐
     ┌─────────┐                    │     Jobs      │
     │  MySQL  │                    └───────┬───────┘
     └─────────┘                            ↓
                                    ┌───────────────┐
                                    │ Notifications │
                                    └───────────────┘
```

---

## 8. Architecture Checklist (Frozen)

- [x] Laravel 13.x + PHP 8.3+
- [x] MVC-S pattern with thin Controllers
- [x] Form Requests for all input validation
- [x] Policies for all authorization (`ListingPolicy`, `ReviewPolicy`, `ConversationPolicy`)
- [x] Service Layer as the single home for business logic
- [x] API Resources for consistent JSON output
- [x] Events & Listeners for decoupled side effects
- [x] `ListingPublished` event used (not `ListingObserver@created`)
- [x] `MemberTierService` for tier calculation (not a User accessor)
- [x] `community_points` + `point_transactions` kept transactionally in sync via `DB::transaction`
- [x] `AlertMatcherService::matches($listing, $alert)` is pure and unit-testable
- [x] Dealer status = `is_dealer` boolean, not a primary role
- [x] EAV design for dynamic attributes (multi-select limitation noted for future)
- [x] Dynamic profile-first location auto-population (prioritizes authenticated user's home city over static defaults)
- [x] Dual-tier registration separation (General User frictionless vs Merchant commercial onboarding)
- [x] Multilingual content architecture with on-demand "Traduire" translation pipeline
- [x] High-capacity media pipeline with WebP optimization & `PostTooLargeException` graceful error trapping
- [x] Production deployment configuration on cPanel / LiteSpeed / PHP 8.4 / MySQL / Cloudflare
