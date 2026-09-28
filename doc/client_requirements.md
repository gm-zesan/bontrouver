# Bon Trouver — Client Requirements Specification

This document provides a formal, structured overview of the core business logic, feature specifications, and functional requirements for the **Bon Trouver** Canadian classifieds and community marketplace platform.

---

## 1. Services & Ads Based on Location

### Overview
Bon Trouver is built from the ground up as a hyper-local Canadian classifieds and service marketplace. Listings and services are tightly coupled with geographic data to deliver precise local discovery.

### Key Capabilities
- **Geographic Hierarchy**: Search, browse, and filter ads by **Province** (ON, QC, BC, AB, etc.), **City** (Montréal, Toronto, Vancouver, Calgary, Ottawa, etc.), and **Neighbourhood / District** (e.g., Plateau-Mont-Royal, Liberty Village, Kitsilano, Harbourfront).
- **Coordinate Precision**: Storage of exact `latitude` and `longitude` coordinates alongside standard Canadian Postal Codes (`A1A 1A1` format).
- **Radius & Distance Filtering**: Ability for users to find items and services near their current location within customizable distance radii (e.g., within 5km, 10km, 25km, 50km).
- **Local Meetup Safety**: Promotes in-person local transactions and verified public exchange locations.

---

## 2. Smart Alerts

### Overview
Smart Alerts represent a primary value proposition for user acquisition and retention, ensuring users never miss high-demand opportunities.

### Key Capabilities
- **Instant Triggers**: Automated alert notifications when a new listing matches user-defined criteria.
  - *Client Reference Example:* **"A new room for $700 was just posted in Montreal."**
- **Customizable Filter Criteria**:
  - **Category**: Specific categories and subcategories (e.g., Room Rentals, Cars & Trucks, Laptops).
  - **Location & City**: Targeted city or neighbourhood bounding.
  - **Keywords**: Specific search terms (e.g., "Furnished", "RAV4 Hybrid", "M3 Max").
  - **Price Range**: Defined `min_price` and `max_price` limits.
- **Notification Channels**: Instant on-platform alerts and email notifications.

---

## 3. Reputation & Trust System

### Overview
To eliminate fraud and build community trust, each user profile features a transparent, multi-dimensional reputation score.

### Reputation Metrics
1. **Verified Member Badges**:
   - `is_verified`: Identity / phone verification.
   - `is_dealer`: OMVIC / certified business seller status.
2. **Review & Star Ratings**:
   - 1 to 5 star ratings with detailed written feedback from verified transaction partners.
   - Separate aggregates for seller and buyer feedback.
3. **Verified Transaction Volume**:
   - Tracks total completed purchases and sales on the platform.
4. **Community Help Points**:
   - Public accumulation of reputation points earned through mutual aid.

---

## 4. Point System & Member Levels

### Philosophy & Purpose
The point system serves primarily as a **reputation and trust mechanism** on user profiles. It rewards users for actively helping the community and participating in mutual aid. 
> **Important Rule:** Points are **never directly exchangeable for money or cash**.

### How Users Earn Points
- **Answering Requests**: Responding to community questions or helping users find items/services.
- **Giving Away Items**: Donating items for free to other community members.
- **Providing Services**: Offering quality, reliable services within the neighborhood.
- **Positive Feedback**: Receiving 5-star reviews and positive transaction testimonials.
- **Identity Verification**: Completing identity, phone, and profile verification steps.

### How Users Spend / Use Points (Perks)
- **Ad Highlighting & Badges**: Boosting visibility for listings.
- **Featured Placements**: Top-of-category or homepage spotlight exposure.
- **Discounts on Paid Options**: Reduced cost on premium business promotions.
- **Level Progression**: Unlocking higher trust tiers and badges.

### Member Level Hierarchy
```text
🥉 New Member (0 – 99 pts)
   ↓
🥈 Active Member (100 – 299 pts)
   ↓
🥇 Trusted Member (300 – 699 pts)
   ↓
⭐ Highly Appreciated Member (700+ pts)
```

| Tier Name | Min Points | Max Points | Benefits / Significance |
| :--- | :---: | :---: | :--- |
| **🥉 New Member** | `0` | `99` | Standard entry-level tier for newly registered users. |
| **🥈 Active Member** | `100` | `299` | Active contributor who has completed verification and first transactions. |
| **🥇 Trusted Member** | `300` | `699` | High-trust community member with verified track record and positive reviews. |
| **⭐ Highly Appreciated Member** | `700` | `null` | Elite community contributor recognized for exceptional mutual aid and reliability. |

---

## 5. Community: 'Need Companionship' Feature

### Overview
Located within the **Community** section of Bon Trouver, this feature enables individuals to publish friendly, social meetup requests to connect with local people.

### Purpose
Designed explicitly as a **wholesome, social and friendly meetup platform** to reduce social isolation and foster neighborhood camaraderie.

### Supported Activity Types
| Activity Type | Icon / Identifier | Example Scenario |
| :--- | :---: | :--- |
| **Coffee & Chat** | ☕ | *"Looking for someone to grab coffee in Montreal tonight and get to know each other."* |
| **Walk & Nature** | 🚶 | Afternoon stroll along Kitsilano Beach or local neighborhood park. |
| **Dining & Foodies** | 🍽️ | Checking out a new restaurant, foodie spot, or street food market. |
| **Cinema & Movies** | 🎬 | Going to watch a newly released film or cinema festival. |
| **Watch a Match** | ⚽ | Catching an NHL, soccer, or sports match at a local sports lounge. |
| **Play Sports & Fitness** | 🏃 | Running, tennis, badminton, or gym workout partners. |
| **Board Games & Gaming** | 🎮 | Casual board game cafe night or multiplayer video game session. |
| **Outings & Exploration** | 🌆 | Exploring city landmarks, museums, markets, or cultural festivals. |
| **Meet New People** | 🗣️ | Casual group introduction for newcomers to a city. |

### Companionship Request Parameters
- **Title & Description**: Clear explanation of the intended meetup.
- **Activity Category / Type**: Selected from supported social activities.
- **Date & Time**: Scheduled meetup timing.
- **Location & City**: Venue name, address, city, and province.
- **Headcount Limit**: Maximum number of people wanted (e.g., 2 for coffee, 4 for dining).
- **Attendee Management**: Creator can review and approve/reject prospective attendees.
- **Status Workflow**: `open` → `full` → `completed` (or `cancelled`).

---

## 6. Enhanced Public User Profiles

### Overview
Instead of a separate business directory, every user in the marketplace is provided with an enhanced, rich public profile. This empowers all users (whether individuals, freelancers, or registered businesses) to present themselves professionally with a unified design.

### Key Capabilities
- **Rich Profiles**: Dedicated pages featuring a cover photo, user avatar (or logo), verified badges, operating hours, and location map.
- **Listings as Offerings**: A user's active classified listings are beautifully showcased as their "Specialties" or "Offerings" on their profile.
- **Features & Amenities**: Dynamic tags such as "Why Choose Me", "Fast Service", or "Quality Products".
- **Ambiance Gallery**: Photo galleries to showcase the user's workspace, previous work, or business environment.
- **Community Reviews**: Integration with the community `Review` system to collect ratings and feedback directly on the enhanced profile.

---

## 7. Platform Income & Monetization Architecture

### Overview
Bon Trouver utilizes a hybrid Canadian marketplace monetization model designed for both commercial power-sellers and community members. It generates revenue through 3 primary pillars while preserving an authentic mutual aid reputation loop.

### Core Revenue Streams

1. **Featured & Sponsored Listings (Promotions & Boosts)**:
   - **🚀 Sponsored Spotlight**: Maximum exposure on homepage hero carousel and pinned at the top of Canadian search results (`$9.99 CAD` or `300 pts` for 7 days).
   - **⭐ Featured Highlight**: Distinct blue verified badge and highlighted card border with top placement (`$4.99 CAD` or `150 pts` for 7 days).
   - **⚡ Instant Bump-Up**: 1-click execution that resets the listing's chronological position to the #1 spot in search results (`$1.99 CAD` or `60 pts`).
   - **Dual-Currency Unlock**: Users can pay directly with Canadian credit card (CAD $) or redeem earned Community Points (pts).

2. **100% Free Unlimited Listings for All Members**:
   - All standard listings across all categories, Canadian cities, and provinces are 100% free with unlimited posting capacity.
   - Zero freemium barriers or listing quotas; monetization is strictly powered by voluntary listing boost upgrades (Sponsored, Featured, Bump-Up) and local sponsor banner advertising.

3. **Banner Advertising & Google AdSense / Programmatic**:
   - Dedicated Canadian sponsor slots across `search_sidebar`, `listing_detail_bottom`, `homepage_leaderboard`, and `community_sidebar`.
   - Geo-targeted by Canadian city and province with real-time impression and CTR tracking.
   - Raw embed support for Google AdSense, media networks, or local Canadian business banner creatives.

---

## 8. Implementation & Database Mapping Reference

| Requirement Module | Database Tables / Models | Seeder Reference |
| :--- | :--- | :--- |
| **Location & Ads** | `listings`, `listing_images`, `listing_attributes` | `ListingSeeder.php` |
| **Smart Alerts** | `smart_alerts`, `smart_alert_attributes` | `SmartAlertSeeder.php` |
| **Reputation & Reviews** | `users`, `reviews`, `transactions` | `UserSeeder.php`, `ReviewSeeder.php`, `TransactionSeeder.php` |
| **Points & Tiers** | `member_tiers`, `point_transactions`, `point_rules` | `MemberTierSeeder.php`, `PointTransactionSeeder.php` |
| **Companionship** | `companionship_requests`, `companionship_attendees` | `CompanionshipSeeder.php` |
| **Messaging & Inquiries** | `conversations`, `messages` | `ConversationSeeder.php` |
| **User Profiles** | `users`, `user_profiles`, `user_galleries` | `UserSeeder.php` |
| **Monetization & Ads** | `promotion_packages`, `listing_promotions`, `banner_ads` | `MonetizationSeeder.php` |
| **Platform Settings** | `site_settings` | `SiteSettingSeeder.php` |
