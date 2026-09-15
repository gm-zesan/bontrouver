# Bontrouver Final Database Schema Specification

This document provides the authoritative Laravel migration specification for the Bontrouver marketplace. It outlines tables, columns, data types, indexes, foreign keys, and unique constraints.

---

## 1. USERS & REPUTATION

### `users`
Core authentication and profile table.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `name` (VARCHAR 255)
- `email` (VARCHAR 255, UNIQUE)
- `password` (VARCHAR 255)
- `role` (VARCHAR 50) - Default: 'user'. Enum: 'user', 'admin', 'moderator'
- `is_dealer` (BOOLEAN) - Default: false. Represents if the user operates as a commercial entity.
- `phone` (VARCHAR 50, NULLABLE)
- `avatar` (VARCHAR 255, NULLABLE)
- `bio` (TEXT, NULLABLE)
- `community_points` (INT) - Default: 0. Calculated aggregate of points.
- `is_verified` (BOOLEAN) - Default: false.
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
- `deleted_at` (TIMESTAMP, NULLABLE) - Soft deletes.

### `member_tiers`
Configuration for member tiers based on community points.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `name` (VARCHAR 255) - e.g., 'New', 'Active', 'Trusted', 'Highly Appreciated'
- `min_points` (INT)
- `max_points` (INT, NULLABLE) - Nullable for the highest tier.
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)

### `point_transactions`
Ledger tracking all point changes for users.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `user_id` (FK -> users.id, CASCADE DELETE)
- `points` (INT) - Can be positive (e.g., +10) or negative (e.g., -5).
- `action_type` (VARCHAR 255) - e.g., 'received_review', 'verified_identity'
- `reference_type` (VARCHAR 255, NULLABLE) - Polymorphic type (e.g., 'App\Models\Review')
- `reference_id` (BIGINT, UNSIGNED, NULLABLE) - Polymorphic ID
- `description` (TEXT, NULLABLE)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
*Indexes: `user_id`, `[reference_type, reference_id]`*

### `transactions`
Tracks completed deals between buyers and sellers to display "number of transactions" on profiles.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `listing_id` (FK -> listings.id, NULLABLE, SET NULL)
- `buyer_id` (FK -> users.id, CASCADE DELETE)
- `seller_id` (FK -> users.id, CASCADE DELETE)
- `amount` (DECIMAL 10,2, NULLABLE)
- `status` (VARCHAR 50) - 'initiated', 'completed', 'cancelled', 'disputed'
- `completed_at` (TIMESTAMP, NULLABLE)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
*Indexes: `buyer_id`, `seller_id`*

---

## 2. CATEGORIES & DYNAMIC ATTRIBUTES

### `categories`
Hierarchical category structure supporting unlimited nesting.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `parent_id` (FK → categories.id, NULLABLE, RESTRICT ON DELETE) - Null for root categories
- `name` (VARCHAR 255)
- `slug` (VARCHAR 255, UNIQUE)
- `icon` (VARCHAR 255, NULLABLE) - Bootstrap Icons class e.g., `bi-house-door`
- `description` (TEXT, NULLABLE) - Short tagline displayed on category cards and drawer
- `sort_order` (SMALLINT, UNSIGNED) - Default: 0. Controls display order within the same parent level
- `is_active` (BOOLEAN) - Default: true
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
- `deleted_at` (TIMESTAMP, NULLABLE) - Soft deletes to prevent orphan listings.
*Indexes: `parent_id`, `slug`*

### `category_attributes`
Defines which attributes belong to which category (e.g., 'Make', 'Bedrooms').
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `category_id` (FK -> categories.id, CASCADE DELETE)
- `name` (VARCHAR 255)
- `slug` (VARCHAR 255)
- `type` (VARCHAR 50) - 'select', 'number', 'text', 'boolean'
- `is_required` (BOOLEAN) - Default: false
- `is_filterable` (BOOLEAN) - Default: false
- `is_active` (BOOLEAN) - Default: true
- `sort_order` (INT) - Default: 0
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
*Unique Constraint: `(category_id, slug)`*

### `attribute_options`
Predefined options for 'select' type attributes (e.g., 'Toyota', 'Honda' for 'Make').
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `category_attribute_id` (FK -> category_attributes.id, CASCADE DELETE)
- `label` (VARCHAR 255)
- `value` (VARCHAR 255)
- `is_active` (BOOLEAN) - Default: true
- `sort_order` (INT) - Default: 0
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)

---

## 3. LISTINGS & MEDIA

### `listings`
Core marketplace advertisements.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `user_id` (FK -> users.id, CASCADE DELETE)
- `category_id` (FK -> categories.id, RESTRICT ON DELETE)
- `title` (VARCHAR 255)
- `slug` (VARCHAR 255, UNIQUE)
- `description` (TEXT)
- `price` (DECIMAL 10,2, NULLABLE)
- `price_type` (VARCHAR 50) - 'fixed', 'negotiable', 'free', 'contact'
- `price_period` (VARCHAR 50, NULLABLE) - 'one_time', 'hour', 'day', 'week', 'month'
- `condition` (VARCHAR 50, NULLABLE) - 'new', 'used_excellent', etc. (Optional here or moved to dynamic attrs)
- `location_name` (VARCHAR 255, NULLABLE) - e.g., 'Liberty Village'
- `city` (VARCHAR 255) - e.g., 'Toronto'
- `province` (VARCHAR 255) - e.g., 'ON'
- `postal_code` (VARCHAR 20, NULLABLE)
- `latitude` (DECIMAL 10,8, NULLABLE)
- `longitude` (DECIMAL 11,8, NULLABLE)
- `status` (VARCHAR 50) - 'draft', 'pending_review', 'active', 'paused', 'sold', 'expired', 'rejected'
- `is_featured` (BOOLEAN) - Default: false (Promoted in Featured Listings)
- `is_sponsored` (BOOLEAN) - Default: false (Hero Carousel placement)
- `views_count` (INT) - Default: 0
- `published_at` (TIMESTAMP, NULLABLE)
- `expires_at` (TIMESTAMP, NULLABLE)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
- `deleted_at` (TIMESTAMP, NULLABLE)
*Indexes: `status`, `is_featured`, `is_sponsored`, `city`, `province`, `[latitude, longitude]`*

### `listing_images`
Images for listings.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `listing_id` (FK -> listings.id, CASCADE DELETE)
- `image_path` (VARCHAR 255)
- `is_primary` (BOOLEAN) - Default: false
- `sort_order` (INT) - Default: 0
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)

### `listing_attributes`
Values for dynamic category attributes attached to a specific listing.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `listing_id` (FK -> listings.id, CASCADE DELETE)
- `category_attribute_id` (FK -> category_attributes.id, CASCADE DELETE)
- `value` (VARCHAR 255)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
*Unique Constraint: `(listing_id, category_attribute_id)`*

---

## 4. ENGAGEMENT (Favorites, Reviews, Messaging)

### `favorites`
Listings saved by users.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `user_id` (FK -> users.id, CASCADE DELETE)
- `listing_id` (FK -> listings.id, CASCADE DELETE)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
*Unique Constraint: `(user_id, listing_id)`*

### `reviews`
Reviews left by users for other users.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `reviewer_id` (FK -> users.id, CASCADE DELETE)
- `reviewee_id` (FK -> users.id, CASCADE DELETE)
- `listing_id` (FK -> listings.id, NULLABLE, SET NULL)
- `rating` (INT) - 1 to 5
- `comment` (TEXT, NULLABLE)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)

### `conversations`
Thread container for messages between a buyer and seller regarding a listing.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `listing_id` (FK -> listings.id, NULLABLE, CASCADE DELETE)
- `buyer_id` (FK -> users.id, CASCADE DELETE)
- `seller_id` (FK -> users.id, CASCADE DELETE)
- `subject` (VARCHAR 255, NULLABLE)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
*Unique Constraint: `(listing_id, buyer_id, seller_id)`*

### `messages`
Individual messages inside a conversation.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `conversation_id` (FK -> conversations.id, CASCADE DELETE)
- `sender_id` (FK -> users.id, CASCADE DELETE)
- `body` (TEXT)
- `read_at` (TIMESTAMP, NULLABLE) - Replaces `is_read` boolean
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)

### `notifications` (Laravel Default)
Standard database notification table.
- `id` (UUID, PK)
- `type` (VARCHAR 255)
- `notifiable_type` (VARCHAR 255)
- `notifiable_id` (BIGINT, UNSIGNED)
- `data` (TEXT)
- `read_at` (TIMESTAMP, NULLABLE)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)

---

## 5. SMART ALERTS

### `smart_alerts`
User preferences for receiving notifications.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `user_id` (FK -> users.id, CASCADE DELETE)
- `name` (VARCHAR 255, NULLABLE)
- `keyword` (VARCHAR 255, NULLABLE)
- `category_id` (FK -> categories.id, NULLABLE, SET NULL ON DELETE)
- `city` (VARCHAR 255, NULLABLE)
- `min_price` (DECIMAL 10,2, NULLABLE)
- `max_price` (DECIMAL 10,2, NULLABLE)
- `is_active` (BOOLEAN) - Default: true
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)

### `smart_alert_attributes`
Dynamic filters for Smart Alerts (e.g., Alert me when 'Make' = 'Honda').
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `smart_alert_id` (FK -> smart_alerts.id, CASCADE DELETE)
- `category_attribute_id` (FK -> category_attributes.id, CASCADE DELETE)
- `value` (VARCHAR 255)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)

---

## 6. COMMUNITY & COMPANIONSHIP

### `companionship_requests`
Social meetups.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `user_id` (FK -> users.id, CASCADE DELETE)
- `type` (VARCHAR 50) - e.g., 'coffee', 'walking', 'dining', 'cinema', 'match', 'sports', 'gaming', 'outing', 'meet_people'
- `title` (VARCHAR 255)
- `description` (TEXT)
- `meetup_date_time` (TIMESTAMP)
- `location_name` (VARCHAR 255)
- `city` (VARCHAR 255)
- `province` (VARCHAR 255)
- `headcount_limit` (INT, NULLABLE)
- `status` (VARCHAR 50) - 'open', 'full', 'cancelled', 'completed'
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)

### `companionship_attendees`
Users joining a companionship request.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `companionship_request_id` (FK -> companionship_requests.id, CASCADE DELETE)
- `user_id` (FK -> users.id, CASCADE DELETE)
- `status` (VARCHAR 50) - 'pending', 'approved', 'rejected'
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
*Unique Constraint: `(companionship_request_id, user_id)`*

---

## 7. MODERATION

### `reports`
Polymorphic table for users reporting listings, messages, or other users.
- `id` (PK, BIGINT, UNSIGNED, AUTO_INCREMENT)
- `reporter_id` (FK -> users.id, CASCADE DELETE)
- `reportable_type` (VARCHAR 255) - e.g., 'App\Models\Listing', 'App\Models\User'
- `reportable_id` (BIGINT, UNSIGNED)
- `reason` (VARCHAR 255)
- `description` (TEXT, NULLABLE)
- `status` (VARCHAR 50) - 'pending', 'reviewed', 'resolved', 'dismissed'
- `reviewed_by` (FK -> users.id, NULLABLE, SET NULL)
- `reviewed_at` (TIMESTAMP, NULLABLE)
- `created_at` (TIMESTAMP, NULLABLE)
- `updated_at` (TIMESTAMP, NULLABLE)
*Indexes: `[reportable_type, reportable_id]`, `status`*
