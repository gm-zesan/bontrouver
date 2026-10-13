<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use App\Models\Province;
use App\Models\SmartAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndToEndUserJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $buyer;
    protected Province $province;
    protected City $city;
    protected Category $category;
    protected PromotionPackage $sponsoredPackage;
    protected PromotionPackage $featuredPackage;
    protected PromotionPackage $bumpPackage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->province = Province::create([
            'name_en' => 'Quebec',
            'name_fr' => 'Québec',
            'code' => 'QC',
            'slug' => 'quebec',
            'country_code' => 'CA',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->city = City::create([
            'province_id' => $this->province->id,
            'name_en' => 'Montreal',
            'name_fr' => 'Montréal',
            'slug' => 'montreal',
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $this->category = Category::create([
            'name_en' => 'Cameras & Photography',
            'name_fr' => 'Appareils photo et photographie',
            'slug' => 'cameras-photography',
            'icon' => 'camera',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->seller = User::factory()->create([
            'name' => 'Sarah Tremblay',
            'email' => 'seller@bontrouver.ca',
            'role' => UserRole::USER,
            'city' => 'Montreal',
            'province' => 'Quebec',
            'postal_code' => 'H2Y 1C6',
            'community_points' => 500,
        ]);

        $this->buyer = User::factory()->create([
            'name' => 'Jean-Luc Picard',
            'email' => 'buyer@bontrouver.ca',
            'role' => UserRole::USER,
            'city' => 'Montreal',
            'province' => 'Quebec',
            'postal_code' => 'H2Y 1C6',
            'community_points' => 200,
        ]);

        $this->sponsoredPackage = PromotionPackage::create([
            'name_en' => 'Sponsored Spotlight',
            'name_fr' => 'Pleins feux sponsorisés',
            'slug' => 'sponsored-spotlight',
            'type' => 'sponsored',
            'badge_text_en' => 'SPONSORED',
            'badge_text_fr' => 'SPONSORISÉ',
            'badge_color' => '#EAB308',
            'badge_icon' => 'crown',
            'price' => 9.99,
            'point_cost' => 150,
            'duration_days' => 7,
            'description_en' => 'Top placement in hero carousel and search results',
            'description_fr' => 'Emplacement supérieur sur le carrousel d\'accueil et résultats de recherche',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->featuredPackage = PromotionPackage::create([
            'name_en' => 'Featured Highlight',
            'name_fr' => 'Mise en vedette',
            'slug' => 'featured-highlight',
            'type' => 'featured',
            'badge_text_en' => 'FEATURED',
            'badge_text_fr' => 'EN VEDETTE',
            'badge_color' => '#3B82F6',
            'badge_icon' => 'star',
            'price' => 4.99,
            'point_cost' => 75,
            'duration_days' => 7,
            'description_en' => 'Distinctive highlight border and badge in search feeds',
            'description_fr' => 'Bordure et badge distinctifs dans les fils de recherche',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->bumpPackage = PromotionPackage::create([
            'name_en' => 'Instant Bump-Up',
            'name_fr' => 'Remontée instantanée',
            'slug' => 'instant-bump-up',
            'type' => 'bump_up',
            'badge_text_en' => 'BUMPED',
            'badge_text_fr' => 'REMONTÉ',
            'badge_color' => '#10B981',
            'badge_icon' => 'rocket',
            'price' => 1.99,
            'point_cost' => 30,
            'duration_days' => 1,
            'description_en' => 'Instantly push your ad back to the top of fresh listings',
            'description_fr' => 'Remontez instantanément votre annonce en tête des nouvelles annonces',
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }

    /**
     * Complete End-to-End Walkthrough:
     * 1. Buyer creates Smart Alert for Cameras in Montreal.
     * 2. Seller publishes a new listing with Stripe-paid Featured upgrade.
     * 3. Buyer automatically receives a Smart Alert notification in notification feed.
     * 4. Seller pauses the listing and verifies status changes to PAUSED.
     * 5. Seller resumes the listing and verifies status changes back to ACTIVE.
     * 6. Seller opens Promote page and attempts duplicate Featured boost -> rejected by cooldown.
     * 7. Seller boosts with Sponsored Spotlight using points -> succeeds.
     * 8. Seller inspects listing analytics.
     */
    public function test_complete_end_to_end_user_journey()
    {
        // 1. Buyer sets up a Smart Alert
        $this->actingAs($this->buyer);
        $alertResponse = $this->post(route('account.alerts.store'), [
            'name' => 'Sony Mirrorless Montreal Alert',
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'province_id' => $this->province->id,
            'min_price' => 1000,
            'max_price' => 3000,
            'keyword' => 'Sony',
        ]);
        $alertResponse->assertRedirect();
        $this->assertDatabaseHas('smart_alerts', [
            'user_id' => $this->buyer->id,
            'name' => 'Sony Mirrorless Montreal Alert',
            'city_id' => $this->city->id,
        ]);

        // 2. Seller posts an ad with a Featured upgrade
        $this->actingAs($this->seller);
        $postData = [
            'title' => 'Sony A7 IV Mirrorless Camera - Mint Condition',
            'description' => 'Selling pristine condition Sony Alpha 7 IV body with original packaging and extra battery.',
            'price' => 2450.00,
            'price_type' => 'fixed',
            'category_id' => $this->category->id,
            'city' => 'Montreal',
            'province' => 'Quebec',
            'postal_code' => 'H2Y 1C6',
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'condition' => 'like_new',
            'promotions' => [
                $this->featuredPackage->id,
            ],
            'payment_method' => 'card',
            'stripe_cardholder_name' => 'Sarah Tremblay',
            'stripe_card_number' => '4242424242424242',
            'stripe_card_expiry' => '12/28',
            'stripe_card_cvc' => '123',
            'stripe_postal_code' => 'H2Y 1C6',
        ];

        $postResponse = $this->post(route('listings.store'), $postData);
        $postResponse->assertRedirect();

        $listing = Listing::where('title', 'Sony A7 IV Mirrorless Camera - Mint Condition')->first();
        $this->assertNotNull($listing);
        $this->assertEquals(ListingStatus::ACTIVE, $listing->status);
        $this->assertTrue($listing->is_featured);
        $this->assertNotNull($listing->featured_until);

        // Verify listing promotion record created
        $this->assertDatabaseHas('listing_promotions', [
            'listing_id' => $listing->id,
            'user_id' => $this->seller->id,
            'type' => 'featured',
            'payment_status' => 'completed',
        ]);

        // 3. Verify Buyer received the Smart Alert Notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->buyer->id,
            'type' => 'App\Notifications\SmartAlertMatched',
        ]);

        // 4. Seller Pauses the Listing
        $pauseResponse = $this->postJson(route('listings.my.status', $listing->id), [
            'status' => 'paused',
        ]);
        $pauseResponse->assertOk();
        $this->assertEquals(ListingStatus::PAUSED, $listing->fresh()->status);

        // 5. Seller Resumes the Listing
        $resumeResponse = $this->postJson(route('listings.my.status', $listing->id), [
            'status' => 'active',
        ]);
        $resumeResponse->assertOk();
        $this->assertEquals(ListingStatus::ACTIVE, $listing->fresh()->status);

        // 6. Seller attempts duplicate Featured Boost -> Blocked by Cooldown Validation
        $duplicateBoostResponse = $this->postJson(route('listings.my.promote', $listing->id), [
            'type' => 'featured',
            'payment_method' => 'card',
        ]);
        $duplicateBoostResponse->assertStatus(422);
        $duplicateBoostResponse->assertJsonFragment([
            'success' => false,
        ]);

        // 7. Seller Boosts with Sponsored Spotlight using Points -> Allowed & Activated
        $sponsoredPointsResponse = $this->postJson(route('listings.my.promote', $listing->id), [
            'type' => 'sponsored',
            'payment_method' => 'points',
        ]);
        $sponsoredPointsResponse->assertOk();
        $listing->refresh();
        $this->assertTrue($listing->is_sponsored);
        $this->assertNotNull($listing->sponsored_until);
        $this->assertEquals(350, $this->seller->fresh()->community_points); // 500 - 150 points

        // 8. Seller checks Analytics endpoint
        $analyticsResponse = $this->getJson(route('listings.my.analytics', $listing->id));
        $analyticsResponse->assertOk();
        $analyticsResponse->assertJsonStructure([
            'success',
            'data' => [
                'listing' => ['id', 'title', 'slug', 'price', 'status'],
                'stats' => ['total_views', 'favorites_count', 'inquiries_count', 'engagement_rate'],
                'boosts',
                'chart' => ['labels', 'series'],
            ],
        ]);

        // 9. Check Notifications view loads cleanly
        $notificationsPage = $this->get(route('notifications.index'));
        $notificationsPage->assertOk();
    }
}
