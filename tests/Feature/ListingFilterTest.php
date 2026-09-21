<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_listings_page_loads_with_dynamic_data_and_radius_all(): void
    {
        $response = $this->get('/listings');

        $response->assertStatus(200);
        $response->assertViewHas('radius', 'all');
        $response->assertViewHas('listings');
        $response->assertViewHas('categories');

        $listings = $response->viewData('listings');
        $this->assertIsArray($listings);
        $this->assertNotEmpty($listings);
    }

    public function test_listings_ajax_returns_dynamic_json_listings(): void
    {
        $response = $this->getJson('/listings?city=Toronto&radius=50');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'listings' => [
                    '*' => [
                        'id',
                        'title',
                        'price',
                        'city',
                        'province',
                        'distance_km',
                        'category',
                        'subcategory',
                        'bedrooms',
                        'bathrooms',
                        'property_type',
                        'fuel',
                        'transmission',
                        'job_type',
                        'work_setup',
                        'seller_type',
                        'attributes',
                    ]
                ],
                'count',
                'breadcrumbs'
            ]);
    }

    public function test_listings_page_contains_dynamic_canadian_cities_and_category_facets(): void
    {
        $response = $this->get('/listings?category=housing');

        $response->assertStatus(200);
        $response->assertViewHas('canadianCities');
        $cities = $response->viewData('canadianCities');
        $this->assertNotEmpty($cities);
        $this->assertArrayHasKey('Toronto', $cities);
        $this->assertArrayHasKey('Vancouver', $cities);

        $listings = $response->viewData('listings');
        $this->assertNotEmpty($listings);

        // Verify housing specific EAV fields exist
        $firstItem = $listings[0];
        $this->assertArrayHasKey('bedrooms', $firstItem);
        $this->assertArrayHasKey('bathrooms', $firstItem);
        $this->assertArrayHasKey('furnished', $firstItem);
        $this->assertArrayHasKey('parking', $firstItem);
        $this->assertArrayHasKey('pet_friendly', $firstItem);
        $this->assertArrayHasKey('utilities_included', $firstItem);
        $this->assertArrayHasKey('lease_term', $firstItem);
        $this->assertArrayHasKey('property_type', $firstItem);
    }

    public function test_listings_can_be_filtered_by_seller_id(): void
    {
        $user = \App\Models\User::first();
        $this->assertNotNull($user);

        $response = $this->getJson('/listings?seller_id=' . $user->id);
        $response->assertStatus(200);

        $listings = $response->json('listings');
        foreach ($listings as $item) {
            $this->assertEquals($user->id, $item['user_id']);
        }
    }

    public function test_listing_detail_contains_clickable_seller_profile_and_seller_ads_link(): void
    {
        $listing = Listing::first();
        $this->assertNotNull($listing);

        $response = $this->get('/listing/' . $listing->slug);
        $response->assertStatus(200);
        $response->assertSee(route('user.profile', $listing->user_id), false);
        $response->assertSee(route('listings.index', ['seller_id' => $listing->user_id]), false);
    }
}
