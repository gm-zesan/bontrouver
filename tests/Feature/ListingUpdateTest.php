<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected Category $category;
    protected Province $province;
    protected City $city;
    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->province = Province::create([
            'name' => 'Ontario',
            'code' => 'ON',
            'slug' => 'ontario',
            'country_code' => 'CA',
            'is_active' => true,
            'sort_order' => 1,
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
            'icon' => 'bi-laptop',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->user = User::factory()->create([
            'city' => 'Toronto',
            'province' => 'ON',
            'community_points' => 100,
        ]);

        $this->otherUser = User::factory()->create([
            'city' => 'Montreal',
            'province' => 'QC',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'title' => 'Original iPhone 14 Pro Max 256GB',
            'slug' => 'original-iphone-14-pro-max-256gb',
            'description' => 'Great condition iPhone 14 with original box and accessories.',
            'price' => 850.00,
            'price_type' => 'fixed',
            'condition' => 'Used — Like New',
            'city' => 'Toronto',
            'province' => 'ON',
            'latitude' => 43.6532,
            'longitude' => -79.3832,
            'status' => 'active',
        ]);
    }

    public function test_user_can_view_edit_listing_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('listings.edit', $this->listing->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Your Listing');
        $response->assertSee('Original iPhone 14 Pro Max 256GB');
        $response->assertSee('850');
    }

    public function test_user_cannot_view_edit_page_for_other_users_listing(): void
    {
        $response = $this->actingAs($this->otherUser)->get(route('listings.edit', $this->listing->id));

        $response->assertStatus(403);
    }

    public function test_user_can_successfully_update_listing(): void
    {
        $response = $this->actingAs($this->user)->put(route('listings.update', $this->listing->id), [
            'title' => 'Updated iPhone 15 Pro Max 512GB Titanium',
            'category_slug' => 'electronics',
            'price' => 1100.00,
            'price_type' => 'negotiable',
            'condition' => 'New',
            'description' => 'Brand new sealed in box titanium finish iPhone 15 with full warranty.',
            'city' => 'Toronto',
            'province' => 'ON',
            'postal_code' => 'M5V 2T6',
        ]);

        $response->assertRedirect();
        $this->listing->refresh();

        $this->assertEquals('Updated iPhone 15 Pro Max 512GB Titanium', $this->listing->title);
        $this->assertEquals(1100.00, (float) $this->listing->price);
        $this->assertEquals('negotiable', $this->listing->price_type);
        $this->assertEquals('New', $this->listing->condition);
        $this->assertEquals('M5V 2T6', $this->listing->postal_code);
    }

    public function test_update_listing_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->put(route('listings.update', $this->listing->id), [
            'title' => 'Short', // min:6 required
            'description' => 'Too short', // min:15 required
            'price_type' => 'invalid_type',
        ]);

        $response->assertSessionHasErrors(['title', 'description', 'price_type', 'city', 'province']);
    }

    public function test_json_update_request_returns_json_response(): void
    {
        $response = $this->actingAs($this->user)->putJson(route('listings.update', $this->listing->id), [
            'title' => 'Updated via JSON API Request',
            'category_slug' => 'electronics',
            'price' => 799.99,
            'price_type' => 'fixed',
            'condition' => 'Used — Good',
            'description' => 'Detailed description for testing json update api endpoint.',
            'city' => 'Toronto',
            'province' => 'ON',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Listing updated successfully!',
            'listing_id' => $this->listing->id,
        ]);

        $this->listing->refresh();
        $this->assertEquals('Updated via JSON API Request', $this->listing->title);
    }
}

