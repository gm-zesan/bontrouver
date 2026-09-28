<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Listing $listing;
    protected PromotionPackage $sponsoredPkg;
    protected PromotionPackage $featuredPkg;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'community_points' => 500,
            'is_verified' => true,
        ]);

        $province = Province::create([
            'name' => 'Quebec',
            'code' => 'QC',
            'slug' => 'quebec',
            'country_code' => 'CA',
            'is_active' => true,
        ]);

        $city = City::create([
            'name' => 'Montreal',
            'slug' => 'montreal',
            'province_id' => $province->id,
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'is_active' => true,
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => 'iPhone 15 Pro Max 256GB',
            'slug' => 'iphone-15-pro-max-256gb',
            'description' => 'Mint condition unlocked phone.',
            'price' => 1200.00,
            'price_type' => 'fixed',
            'city' => 'Montreal',
            'province' => 'QC',
            'status' => \App\Enums\ListingStatus::ACTIVE,
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
            'sort_order' => 1,
        ]);

        $this->featuredPkg = PromotionPackage::create([
            'name' => 'Featured Highlight',
            'slug' => 'featured-highlight',
            'type' => 'featured',
            'price' => 4.99,
            'point_cost' => 150,
            'duration_days' => 7,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    public function test_stripe_checkout_session_redirection(): void
    {
        $response = $this->actingAs($this->user)->post(route('listings.promote.store', $this->listing->id), [
            'package_id' => $this->sponsoredPkg->id,
            'payment_method' => 'stripe',
        ]);

        // In simulation/dev without live stripe key, redirects to success URL with simulated session
        $response->assertRedirect();
        $this->assertStringContainsString('promote/success', $response->headers->get('Location'));
    }

    public function test_stripe_checkout_success_activates_promotion(): void
    {
        $response = $this->actingAs($this->user)->get(route('listings.promote.success', [
            'listing' => $this->listing->id,
            'package_id' => $this->sponsoredPkg->id,
            'session_id' => 'cs_sim_test12345',
        ]));

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
            'payment_status' => 'completed',
        ]);
    }

    public function test_points_redemption_activates_promotion_instantly(): void
    {
        $response = $this->actingAs($this->user)->post(route('listings.promote.store', $this->listing->id), [
            'package_id' => $this->featuredPkg->id,
            'payment_method' => 'points',
        ]);

        $response->assertRedirect(route('listings.show', $this->listing->slug));

        $this->listing->refresh();
        $this->user->refresh();

        $this->assertTrue($this->listing->is_featured);
        $this->assertEquals(350, $this->user->community_points); // 500 - 150 = 350

        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $this->user->id,
            'points' => -150,
        ]);
    }

    public function test_stripe_webhook_listener_processes_event(): void
    {
        $payload = json_encode([
            'id' => 'evt_test_123',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_123',
                    'amount' => 999,
                    'currency' => 'cad',
                    'metadata' => [
                        'listing_id' => (string) $this->listing->id,
                        'package_id' => (string) $this->sponsoredPkg->id,
                        'user_id' => (string) $this->user->id,
                    ],
                ],
            ],
        ]);

        $response = $this->postJson(route('stripe.webhook'), json_decode($payload, true));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }
}
