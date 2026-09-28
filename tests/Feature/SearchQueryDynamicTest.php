<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\SearchQuery;
use App\Models\User;
use App\Models\Category;
use App\Models\City;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SearchQueryDynamicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_search_query_is_recorded_and_incremented(): void
    {
        SearchQuery::recordSearch('PlayStation 5 Pro', 5);
        
        $this->assertDatabaseHas('search_queries', [
            'query'         => 'PlayStation 5 Pro',
            'hits_count'    => 1,
            'results_count' => 5,
        ]);

        // Second hit increments
        SearchQuery::recordSearch('PlayStation 5 Pro', 8);

        $this->assertDatabaseHas('search_queries', [
            'query'         => 'PlayStation 5 Pro',
            'hits_count'    => 2,
            'results_count' => 8,
        ]);
    }

    public function test_get_trending_keywords_orders_by_hits_count(): void
    {
        SearchQuery::create(['query' => 'Rare Books', 'hits_count' => 10, 'results_count' => 2, 'last_searched_at' => now()]);
        SearchQuery::create(['query' => 'Tesla Model 3', 'hits_count' => 100, 'results_count' => 5, 'last_searched_at' => now()]);
        SearchQuery::create(['query' => 'iPhone 15 Pro', 'hits_count' => 50, 'results_count' => 3, 'last_searched_at' => now()]);

        $trending = SearchQuery::getTrendingKeywords(3);

        $this->assertEquals(['Tesla Model 3', 'iPhone 15 Pro', 'Rare Books'], $trending);
    }

    public function test_suggestions_endpoint_returns_dynamic_trending_and_matched_keywords(): void
    {
        SearchQuery::create(['query' => 'Honda Civic', 'hits_count' => 45, 'results_count' => 10, 'last_searched_at' => now()]);
        SearchQuery::create(['query' => 'Honda CR-V', 'hits_count' => 30, 'results_count' => 4, 'last_searched_at' => now()]);
        SearchQuery::create(['query' => 'Toyota RAV4', 'hits_count' => 20, 'results_count' => 6, 'last_searched_at' => now()]);

        // Empty query -> trending payload
        $response = $this->getJson('/search/suggestions');
        $response->assertStatus(200)
            ->assertJsonPath('type', 'trending')
            ->assertJsonFragment(['trending_keywords' => ['Honda Civic', 'Honda CR-V', 'Toyota RAV4']]);

        // Query with 'honda' -> matched payload
        $response = $this->getJson('/search/suggestions?q=Honda');
        $response->assertStatus(200)
            ->assertJsonPath('type', 'matches')
            ->assertJsonFragment(['keywords' => ['Honda Civic', 'Honda CR-V']]);
    }

    public function test_fallback_to_active_listing_titles_when_no_search_history(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        $province = Province::create(['name' => 'Ontario', 'slug' => 'ontario', 'code' => 'ON']);
        $city = City::create(['name' => 'Toronto', 'slug' => 'toronto', 'province_id' => $province->id, 'latitude' => 43.6532, 'longitude' => -79.3832]);

        Listing::create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'city_id'     => $city->id,
            'title'       => 'MacBook Air M2 Space Gray',
            'slug'        => 'macbook-air-m2-space-gray',
            'description' => 'Mint condition laptop',
            'price'       => 1200,
            'price_type'  => 'fixed',
            'status'      => 'active',
            'location'    => 'Toronto, ON',
            'latitude'    => 43.6532,
            'longitude'   => -79.3832,
        ]);

        $trending = SearchQuery::getTrendingKeywords(5);
        $this->assertContains('MacBook Air M2 Space Gray', $trending);
    }
}
