<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_it_returns_canadian_cities_list(): void
    {
        $response = $this->getJson('/api/location/cities');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'cities' => [
                    '*' => ['name', 'label', 'province', 'latitude', 'longitude']
                ]
            ]);
    }

    public function test_it_sets_user_location_in_session_and_cookie(): void
    {
        $response = $this->postJson('/api/location/set', [
            'city'  => 'Montreal',
            'label' => 'Montreal, QC',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'city'    => 'Montreal',
                'label'   => 'Montreal, QC',
            ])
            ->assertCookie('bontrouver_city', 'Montreal');
    }

    public function test_it_auto_detects_closest_canadian_city_from_gps(): void
    {
        // Coordinates near Montreal (e.g. 45.5088, -73.554)
        $response = $this->postJson('/api/location/detect', [
            'latitude'  => 45.5088,
            'longitude' => -73.5540,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'           => true,
                'is_outside_canada' => false,
                'city'              => 'Montreal',
            ]);
    }

    public function test_it_handles_international_locations_outside_canada(): void
    {
        // Coordinates in Dhaka, Bangladesh (lat: 23.8103, lng: 90.4125)
        $response = $this->postJson('/api/location/detect', [
            'latitude'  => 23.8103,
            'longitude' => 90.4125,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'           => true,
                'is_outside_canada' => true,
                'city'              => null,
                'label'             => 'All Canada',
            ]);
    }

    public function test_provinces_and_cities_relations_and_listing_linkage(): void
    {
        $ontario = \App\Models\Province::where('code', 'ON')->first();
        $this->assertNotNull($ontario);
        $this->assertTrue($ontario->cities()->count() > 0);

        $toronto = \App\Models\City::where('slug', 'toronto')->first();
        $this->assertNotNull($toronto);
        $this->assertEquals('Ontario', $toronto->province->name);
        $this->assertEquals('ON', $toronto->province->code);

        $listings = \App\Models\Listing::where('city_id', $toronto->id)->get();
        if ($listings->isNotEmpty()) {
            $this->assertEquals($toronto->id, $listings->first()->city_id);
            $this->assertEquals('Toronto', $listings->first()->city);
        }
    }

    public function test_post_ad_page_provides_dynamic_provinces_and_cities(): void
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get('/post-ad');
        $response->assertStatus(200);
        $response->assertViewHas('provinces');
        
        $provinces = $response->viewData('provinces');
        $this->assertIsArray($provinces);
        $this->assertArrayHasKey('ON', $provinces);
        $this->assertArrayHasKey('QC', $provinces);
        $this->assertArrayHasKey('BC', $provinces);
        $this->assertEquals('Ontario', $provinces['ON']);
    }

    public function test_post_ad_store_persists_listing_with_dynamic_city_id(): void
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title' => 'Vintage Canadian Acoustic Guitar',
            'category_slug' => 'buy-sell',
            'price' => 450,
            'price_type' => 'fixed',
            'condition' => 'used',
            'description' => 'Beautiful acoustic guitar in excellent condition, local pickup in Toronto.',
            'city' => 'Toronto',
            'province' => 'ON',
            'postal_code' => 'M5V 2T6',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('listings', [
            'title' => 'Vintage Canadian Acoustic Guitar',
            'city' => 'Toronto',
            'province' => 'ON',
        ]);

        $listing = \App\Models\Listing::where('title', 'Vintage Canadian Acoustic Guitar')->first();
        $this->assertNotNull($listing);
        $this->assertNotNull($listing->city_id);
        $this->assertEquals('Toronto', $listing->cityRel?->name ?? $listing->city);
    }

    public function test_location_service_calculates_distance_and_finds_nearest_city(): void
    {
        // Distance between Toronto (43.6532, -79.3832) and Montreal (45.5017, -73.5673) is ~504 km
        $distance = \App\Services\LocationService::calculateDistance(43.6532, -79.3832, 45.5017, -73.5673);
        $this->assertGreaterThan(500, $distance);
        $this->assertLessThan(515, $distance);

        // Nearest city to Montreal coordinates should be Montreal
        $nearest = \App\Services\LocationService::findNearestCity(45.5088, -73.5540);
        $this->assertNotNull($nearest);
        $this->assertEquals('Montreal', $nearest['name']);
        $this->assertLessThan(15, $nearest['distance_km']);
    }
}
