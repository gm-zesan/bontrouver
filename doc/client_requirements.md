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

## 6. Implementation & Database Mapping Reference

| Requirement Module | Database Tables / Models | Seeder Reference |
| :--- | :--- | :--- |
| **Location & Ads** | `listings`, `listing_images`, `listing_attributes` | `ListingSeeder.php` |
| **Smart Alerts** | `smart_alerts`, `smart_alert_attributes` | `SmartAlertSeeder.php` |
| **Reputation & Reviews** | `users`, `reviews`, `transactions` | `UserSeeder.php`, `ReviewSeeder.php`, `TransactionSeeder.php` |
| **Points & Tiers** | `member_tiers`, `point_transactions` | `MemberTierSeeder.php`, `PointTransactionSeeder.php` |
| **Companionship** | `companionship_requests`, `companionship_attendees` | `CompanionshipSeeder.php` |
| **Messaging & Inquiries** | `conversations`, `messages` | `ConversationSeeder.php` |
