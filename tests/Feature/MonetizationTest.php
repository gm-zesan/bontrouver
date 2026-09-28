<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\BannerAd;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PointTransaction;
use App\Models\PromotionPackage;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\MonetizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonetizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected Category $category;
    protected Listing $listing;
    protected PromotionPackage $sponsoredPkg;
    protected PromotionPackage $featuredPkg;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@bontrouver.ca',
            'community_points' => 1000,
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
            'email' => 'seller@bontrouver.ca',
            'community_points' => 500,
        ]);

        $this->category = Category::create([
            'name' => 'Vehicles',
            'slug' => 'vehicles',
            'is_active' => true,
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => '2022 Toyota RAV4 AWD',
            'slug' => '2022-toyota-rav4-awd',
            'description' => 'Clean title vehicle in Montreal.',
            'price' => 28500.00,
            'price_type' => 'fixed',
            'city' => 'Montréal',
            'province' => 'QC',
            'status' => ListingStatus::ACTIVE,
            'is_featured' => false,
            'is_sponsored' => false,
        ]);

        $this->sponsoredPkg = PromotionPackage::create([
            'name' => 'Sponsored Spotlight',
            'slug' => 'sponsored-spotlight',
            'type' => 'sponsored',
            'price' => 9.99,
            'point_cost' => 300,
            'duration_days' => 7,
            'is_active' => true,
        ]);

        $this->featuredPkg = PromotionPackage::create([
            'name' => 'Featured Highlight',
            'slug' => 'featured-highlight',
            'type' => 'featured',
            'price' => 4.99,
            'point_cost' => 150,
            'duration_days' => 7,
            'is_active' => true,
        ]);
    }

    public function test_user_can_view_promotion_page_for_own_listing(): void
    {
        $response = $this->actingAs($this->user)->get(route('listings.promote.show', $this->listing->id));

        $response->assertStatus(200);
        $response->assertSee('Sponsored Spotlight');
        $response->assertSee('Featured Highlight');
        $response->assertSee('500 pts');
    }

    public function test_user_cannot_view_promotion_page_for_another_users_listing(): void
    {
        $otherUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($otherUser)->get(route('listings.promote.show', $this->listing->id));

        $response->assertStatus(403);
    }

    public function test_user_can_promote_listing_with_cad_payment(): void
    {
        $response = $this->actingAs($this->user)->post(route('listings.promote.store', $this->listing->id), [
            'package_id' => $this->sponsoredPkg->id,
            'payment_method' => 'stripe',
        ]);

        $response->assertRedirect(route('listings.show', $this->listing->slug));
        $response->assertSessionHas('success');

        $this->listing->refresh();
        $this->assertTrue($this->listing->is_sponsored);
        $this->assertNotNull($this->listing->sponsored_until);

        $this->assertDatabaseHas('listing_promotions', [
            'listing_id' => $this->listing->id,
            'user_id' => $this->user->id,
            'type' => 'sponsored',
            'payment_method' => 'stripe',
            'price_paid' => 9.99,
            'points_spent' => 0,
            'is_active' => true,
        ]);
    }

    public function test_user_can_promote_listing_by_redeeming_community_points(): void
    {
        $initialPoints = $this->user->community_points; // 500

        $response = $this->actingAs($this->user)->post(route('listings.promote.store', $this->listing->id), [
            'package_id' => $this->featuredPkg->id, // 150 points
            'payment_method' => 'points',
        ]);

        $response->assertRedirect(route('listings.show', $this->listing->slug));

        $this->user->refresh();
        $this->assertEquals($initialPoints - 150, $this->user->community_points); // 350

        $this->listing->refresh();
        $this->assertTrue($this->listing->is_featured);
        $this->assertNotNull($this->listing->featured_until);

        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $this->user->id,
            'points' => -150,
            'action_type' => 'listing_boost_featured',
        ]);

        $this->assertDatabaseHas('listing_promotions', [
            'listing_id' => $this->listing->id,
            'points_spent' => 150,
            'payment_method' => 'points',
        ]);
    }

    public function test_promotion_fails_if_user_has_insufficient_points(): void
    {
        $this->user->update(['community_points' => 50]); // needs 300 for sponsored

        $response = $this->actingAs($this->user)->post(route('listings.promote.store', $this->listing->id), [
            'package_id' => $this->sponsoredPkg->id,
            'payment_method' => 'points',
        ]);

        $response->assertSessionHasErrors(['points']);
        $this->listing->refresh();
        $this->assertFalse($this->listing->is_sponsored);
    }

    public function test_admin_can_view_and_update_promotion_packages(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.promotions.index'));
        $response->assertStatus(200);
        $response->assertSee('Listing Promotions & Monetization Hub', false);
        $response->assertSee('Sponsored Spotlight');

        // Update package price
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.promotions.packages.update', $this->sponsoredPkg->id), [
            'name' => 'Sponsored Spotlight Plus',
            'price' => 14.99,
            'point_cost' => 450,
            'duration_days' => 10,
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect(route('admin.promotions.index'));
        $this->assertDatabaseHas('promotion_packages', [
            'id' => $this->sponsoredPkg->id,
            'name' => 'Sponsored Spotlight Plus',
            'price' => 14.99,
            'point_cost' => 450,
            'duration_days' => 10,
        ]);
    }

    public function test_admin_can_manage_banner_ads(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.banners.index'));
        $response->assertStatus(200);
        $response->assertSee('Banner Ads & Google AdSense Management', false);

        // Create new banner ad for homepage_top
        $createResponse = $this->actingAs($this->admin)->post(route('admin.banners.store'), [
            'title' => 'Vancouver Clean Energy HVAC',
            'position' => 'homepage_top',
            'image_path' => 'https://example.ca/banner.jpg',
            'target_url' => 'https://example.ca/hvac',
            'city' => 'Vancouver',
            'province' => 'BC',
            'is_active' => '1',
        ]);

        $createResponse->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banner_ads', [
            'title' => 'Vancouver Clean Energy HVAC',
            'city' => 'Vancouver',
            'position' => 'homepage_top',
        ]);

        $banner = BannerAd::where('title', 'Vancouver Clean Energy HVAC')->first();

        // Toggle status
        $toggleResponse = $this->actingAs($this->admin)->post(route('admin.banners.toggle', $banner->id));
        $toggleResponse->assertJson(['success' => true, 'is_active' => false]);

        // Delete banner
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.banners.destroy', $banner->id));
        $deleteResponse->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseMissing('banner_ads', ['id' => $banner->id]);
    }

    public function test_quota_service_checks_free_listing_limits(): void
    {
        SiteSetting::set('free_listing_limit_per_user', 2, 'marketplace', 'number');

        $monetizationService = app(MonetizationService::class);

        // User currently has 1 active listing, limit is 2 -> can create
        $this->assertTrue($monetizationService->canCreateFreeListing($this->user));

        // Create 2nd active listing
        Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Listing 2',
            'slug' => 'listing-2',
            'description' => 'Test listing 2',
            'price' => 50,
            'price_type' => 'fixed',
            'status' => ListingStatus::ACTIVE,
        ]);

        // Now user has 2 listings, limit is 2 -> cannot create 3rd free listing
        $this->assertFalse($monetizationService->canCreateFreeListing($this->user));

        // Admin is unrestricted
        $this->assertTrue($monetizationService->canCreateFreeListing($this->admin));
    }

    public function test_admin_can_update_marketplace_quota_settings(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.promotions.quota.update'), [
            'free_listing_limit_per_user' => 8,
            'enable_listing_promotions' => '1',
            'auto_approve_listings' => '0',
        ]);

        $response->assertRedirect(route('admin.promotions.index'));
        $this->assertEquals(8, (int) SiteSetting::get('free_listing_limit_per_user'));
        $this->assertFalse(SiteSetting::get('auto_approve_listings'));
    }

    public function test_user_can_quick_boost_from_my_listings_using_points(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('listings.my.promote', $this->listing->id), [
            'type' => 'bump_up',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'badge' => 'BUMPED',
            ]);

        $this->listing->refresh();
        $this->assertNotNull($this->listing->bumped_at);
        $this->assertTrue($this->listing->isBumped());
    }

    public function test_user_can_access_promotion_page_via_slug(): void
    {
        $response = $this->actingAs($this->user)->get("/listing/{$this->listing->slug}/promote");

        $response->assertStatus(200);
        $response->assertSee($this->listing->title);
    }

    public function test_owner_sees_promote_and_boost_on_listing_detail_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('listings.show', $this->listing->slug));

        $response->assertStatus(200);
        $response->assertSee('Promote & Boost Ad', false);
        $response->assertSee('Your Listing');
    }

    public function test_cannot_re_sponsor_already_sponsored_listing(): void
    {
        $this->listing->update([
            'is_sponsored' => true,
            'sponsored_until' => now()->addDays(5),
        ]);

        // Attempting via full promotion checkout
        $response = $this->actingAs($this->user)->post(route('listings.promote.store', $this->listing->id), [
            'package_id' => $this->sponsoredPkg->id,
            'payment_method' => 'points',
        ]);

        $response->assertSessionHasErrors('package_id');

        // Attempting via quick boost API from my-listings
        $quickResponse = $this->actingAs($this->user)->postJson(route('listings.my.promote', $this->listing->id), [
            'type' => 'sponsored',
        ]);

        $quickResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_cannot_re_feature_already_featured_listing(): void
    {
        $this->listing->update([
            'is_featured' => true,
            'featured_until' => now()->addDays(4),
        ]);

        // Attempting via full promotion checkout
        $response = $this->actingAs($this->user)->post(route('listings.promote.store', $this->listing->id), [
            'package_id' => $this->featuredPkg->id,
            'payment_method' => 'points',
        ]);

        $response->assertSessionHasErrors('package_id');

        // Attempting via quick boost API from my-listings
        $quickResponse = $this->actingAs($this->user)->postJson(route('listings.my.promote', $this->listing->id), [
            'type' => 'featured',
        ]);

        $quickResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_cannot_re_bump_already_bumped_listing_today(): void
    {
        $this->listing->update([
            'bumped_at' => now(),
        ]);

        $bumpPkg = PromotionPackage::create([
            'name' => 'Instant Bump',
            'slug' => 'instant-bump-test',
            'type' => 'bump_up',
            'price' => 1.99,
            'point_cost' => 60,
            'duration_days' => 0,
            'is_active' => true,
        ]);

        // Attempting via full promotion checkout
        $response = $this->actingAs($this->user)->post(route('listings.promote.store', $this->listing->id), [
            'package_id' => $bumpPkg->id,
            'payment_method' => 'points',
        ]);

        $response->assertSessionHasErrors('package_id');

        // Attempting via quick boost API from my-listings
        $quickResponse = $this->actingAs($this->user)->postJson(route('listings.my.promote', $this->listing->id), [
            'type' => 'bump_up',
        ]);

        $quickResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }
}

