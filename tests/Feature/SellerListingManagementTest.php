<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Province;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\ListingStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerListingManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\MemberTierSeeder::class);

        $province = Province::create([
            'name' => 'Quebec',
            'code' => 'QC',
            'slug' => 'quebec',
            'country_code' => 'CA',
        ]);

        $city = City::create([
            'province_id' => $province->id,
            'name' => 'Montreal',
            'slug' => 'montreal',
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'is_active' => true,
        ]);

        $this->seller = User::factory()->create([
            'role' => UserRole::USER,
            'name' => 'Seller Test User',
            'email' => 'seller_test@bontrouver.ca',
        ]);

        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'icon' => 'bi-laptop',
            'is_active' => true,
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => 'Vintage Audio Receiver',
            'slug' => 'vintage-audio-receiver',
            'description' => 'Great condition vintage receiver.',
            'price' => 250.00,
            'price_type' => 'fixed',
            'condition' => 'good',
            'city' => 'Montreal',
            'province' => 'Quebec',
            'postal_code' => 'H2X 1Y4',
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'status' => ListingStatus::ACTIVE,
        ]);
    }

    public function test_seller_can_view_my_listings_page(): void
    {
        $response = $this->actingAs($this->seller)->get(route('listings.my'));

        $response->assertStatus(200);
        $response->assertSee('Vintage Audio Receiver');
        $response->assertSee('My Listings');
    }

    public function test_seller_can_pause_an_active_listing(): void
    {
        $response = $this->actingAs($this->seller)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'paused',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'paused',
        ]);

        $this->listing->refresh();
        $this->assertEquals(ListingStatus::PAUSED, $this->listing->status);
    }

    public function test_seller_can_resume_a_paused_listing(): void
    {
        $this->listing->update(['status' => ListingStatus::PAUSED]);

        $response = $this->actingAs($this->seller)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'active',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'active',
        ]);

        $this->listing->refresh();
        $this->assertEquals(ListingStatus::ACTIVE, $this->listing->status);
    }

    public function test_seller_can_mark_listing_as_sold(): void
    {
        $response = $this->actingAs($this->seller)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'sold',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'sold',
        ]);

        $this->listing->refresh();
        $this->assertEquals(ListingStatus::SOLD, $this->listing->status);
    }

    public function test_seller_can_renew_listing(): void
    {
        $this->listing->update(['status' => ListingStatus::EXPIRED]);

        $response = $this->actingAs($this->seller)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'renewed',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'active',
        ]);

        $this->listing->refresh();
        $this->assertEquals(ListingStatus::ACTIVE, $this->listing->status);
        $this->assertNotNull($this->listing->bumped_at);
    }

    public function test_seller_can_delete_listing(): void
    {
        $response = $this->actingAs($this->seller)->deleteJson(route('listings.my.destroy', $this->listing->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertSoftDeleted('listings', [
            'id' => $this->listing->id,
        ]);
    }

    public function test_other_user_cannot_pause_or_resume_someone_elses_listing(): void
    {
        $otherUser = User::factory()->create([
            'role' => UserRole::USER,
            'name' => 'Other User',
            'email' => 'other@bontrouver.ca',
        ]);

        $response = $this->actingAs($otherUser)->postJson(route('listings.my.status', $this->listing->id), [
            'status' => 'paused',
        ]);

        $response->assertStatus(404);
    }
}
