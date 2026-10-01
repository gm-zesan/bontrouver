<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Conversation;
use App\Models\Favorite;
use App\Models\Listing;
use App\Models\MemberTier;
use App\Models\Message;
use App\Models\PromotionPackage;
use App\Models\Province;
use App\Models\Report;
use App\Models\SmartAlert;
use App\Models\SupportConversation;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensivePagesHealthTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Listing $listing;
    protected CompanionshipRequest $meetup;
    protected Category $category;
    protected Province $province;
    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();

        $this->province = Province::create([
            'code' => 'ON',
            'name' => 'Ontario',
            'slug' => 'ontario',
            'country_code' => 'CA',
            'is_active' => true,
        ]);

        $this->city = City::create([
            'province_id' => $this->province->id,
            'name' => 'Toronto',
            'slug' => 'toronto',
            'latitude' => 43.6532,
            'longitude' => -79.3832,
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'icon_class' => 'bi-laptop',
            'is_active' => true,
        ]);

        MemberTier::create([
            'name' => 'New Member',
            'icon' => '🥉',
            'badge_color' => '#94A3B8',
            'badge_class' => 'bg-secondary-subtle text-light',
            'min_points' => 0,
            'max_points' => 99,
            'description' => 'Starting tier for all new Canadian members.',
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
            'city' => 'Toronto',
            'province' => 'ON',
            'community_points' => 50,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'city' => 'Montreal',
            'province' => 'QC',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'province_id' => $this->province->id,
            'title' => 'MacBook Pro M2 16 inch',
            'slug' => 'macbook-pro-m2-16-inch',
            'description' => 'Mint condition MacBook with original charger.',
            'price' => 1850.00,
            'price_type' => 'fixed',
            'condition' => 'Used — Like New',
            'location_name' => 'Downtown Toronto',
            'postal_code' => 'M5H 2N2',
            'latitude' => 43.6532,
            'longitude' => -79.3832,
            'status' => 'active',
            'is_featured' => true,
        ]);

        $this->meetup = CompanionshipRequest::create([
            'user_id' => $this->user->id,
            'city_id' => $this->city->id,
            'type' => '☕ Coffee & Chat',
            'title' => 'Weekend Coffee Meetup',
            'description' => 'Casual meetup for newcomers in Toronto.',
            'meetup_date_time' => now()->addDays(3),
            'location_name' => 'Starbucks Downtown',
            'city' => 'Toronto',
            'province' => 'ON',
            'headcount_limit' => 4,
            'expense_type' => 'free',
            'status' => 'open',
        ]);

        PromotionPackage::create([
            'name' => 'Featured Highlight',
            'slug' => 'featured-highlight',
            'type' => 'featured',
            'price' => 9.99,
            'point_cost' => 150,
            'duration_days' => 7,
            'is_active' => true,
        ]);
    }

    public function test_all_public_frontend_pages_render_successfully(): void
    {
        // 1. Home Page
        $res = $this->get(route('home'));
        $res->assertOk();

        // 2. Listings Index Page
        $res = $this->get(route('listings.index'));
        $res->assertOk();

        // 3. Category Page
        $res = $this->get(route('listings.category', $this->category->slug));
        $res->assertOk();

        // 4. Listing Detail Page
        $res = $this->get(route('listings.show', $this->listing->slug));
        $res->assertOk();

        // 5. Community Hub Index Page
        $res = $this->get(route('community.index'));
        $res->assertOk();

        // 6. Community Meetup Detail Page
        $res = $this->get(route('community.show', $this->meetup->id));
        $res->assertOk();

        // 7. Public User Profile Page
        $res = $this->get(route('user.profile', $this->user->id));
        $res->assertOk();
    }

    public function test_all_authenticated_member_pages_render_successfully(): void
    {
        $this->actingAs($this->user);

        // 1. User Own Profile
        $res = $this->get(route('profile.index'));
        $res->assertOk();

        // 2. Post an Ad
        $res = $this->get(route('listings.create'));
        $res->assertOk();

        // 3. Edit Listing
        $res = $this->get(route('listings.edit', $this->listing->id));
        $res->assertOk();

        // 4. My Listings
        $res = $this->get(route('listings.my'));
        $res->assertOk();

        // 5. Promote Listing
        $res = $this->get(route('listings.promote.show', $this->listing->id));
        $res->assertOk();

        // 6. Create Community Meetup
        $res = $this->get(route('community.create'));
        $res->assertOk();

        // 7. Edit Community Meetup
        $res = $this->get(route('community.edit', $this->meetup->id));
        $res->assertOk();

        // 8. My Meetups Dashboard
        $res = $this->get(route('meetups.my'));
        $res->assertOk();

        // 9. Smart Alerts Index
        $res = $this->get(route('account.alerts.index'));
        $res->assertOk();

        // 10. Smart Alerts Create
        $res = $this->get(route('account.alerts.create'));
        $res->assertOk();

        // 11. Saved Favorites
        $res = $this->get(route('favorites.index'));
        $res->assertOk();

        // 12. Messages Inbox
        $res = $this->get(route('messages.index'));
        $res->assertOk();

        // 13. Notifications Center
        $res = $this->get(route('notifications.index'));
        $res->assertOk();

        // 14. Account Settings
        $res = $this->get(route('settings.index'));
        $res->assertOk();

        // 15. Points Ledger
        $res = $this->get(route('account.points'));
        $res->assertOk();

        // 16. Identity Verification Center (Redirects to Settings Verification Section)
        $res = $this->get(route('account.verification.index'));
        $res->assertRedirect(route('settings.index', '#verification-section'));

        // 17. Informational Verification Page
        $res = $this->get(route('pages.verification'));
        $res->assertOk();
    }

    public function test_all_admin_panel_pages_render_successfully(): void
    {
        $this->actingAs($this->admin);

        // 1. Admin Dashboard
        $res = $this->get(route('admin.dashboard'));
        $res->assertOk();

        // 2. Admin Profile
        $res = $this->get(route('admin.profile.index'));
        $res->assertOk();

        // 3. ID Verifications Index
        $res = $this->get(route('admin.verifications.index'));
        $res->assertOk();

        // 4. Users Index
        $res = $this->get(route('admin.users.index'));
        $res->assertOk();

        // 5. User Show / Inspector
        $res = $this->get(route('admin.users.show', $this->user->id));
        $res->assertOk();

        // 6. Listings Index
        $res = $this->get(route('admin.listings.index'));
        $res->assertOk();

        // 7. Listing Show / Inspector
        $res = $this->get(route('admin.listings.show', $this->listing->id));
        $res->assertOk();

        // 8. Promotions & Monetization Hub
        $res = $this->get(route('admin.promotions.index'));
        $res->assertOk();

        // 9. Banner Ads Index
        $res = $this->get(route('admin.banners.index'));
        $res->assertOk();

        // 10. Community Meetups Index
        $res = $this->get(route('admin.meetups.index'));
        $res->assertOk();

        // 11. Community Meetup Show
        $res = $this->get(route('admin.meetups.show', $this->meetup->id));
        $res->assertOk();

        // 12. Moderation Notifications Hub
        $res = $this->get(route('admin.notifications.index'));
        $res->assertOk();

        // 13. Reports Moderation Index
        $res = $this->get(route('admin.reports.index'));
        $res->assertOk();

        // 14. Categories Index
        $res = $this->get(route('admin.categories.index'));
        $res->assertOk();

        // 15. Dynamic Category Attributes
        $res = $this->get(route('admin.categories.attributes.index', $this->category->id));
        $res->assertOk();

        // 16. Member Tiers & Points Config
        $res = $this->get(route('admin.member-tiers.index'));
        $res->assertOk();

        // 17. Locations & Canadian Cities
        $res = $this->get(route('admin.locations.index'));
        $res->assertOk();

        // 18. Site Settings & Branding
        $res = $this->get(route('admin.settings.index'));
        $res->assertOk();

        // 19. Support Helpdesk Desk
        $res = $this->get(route('admin.support.index'));
        $res->assertOk();
    }
}
