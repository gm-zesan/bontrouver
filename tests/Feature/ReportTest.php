<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\Province;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $reporter;
    private User $targetUser;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $province = Province::create([
            'name' => 'Ontario',
            'code' => 'ON',
            'slug' => 'ontario',
            'country_code' => 'CA',
        ]);

        $city = City::create([
            'province_id' => $province->id,
            'name' => 'Toronto',
            'slug' => 'toronto',
            'latitude' => 43.6532,
            'longitude' => -79.3832,
            'is_active' => true,
        ]);

        $this->reporter = User::factory()->create([
            'name' => 'John Reporter',
            'email' => 'reporter@example.com',
        ]);

        $this->targetUser = User::factory()->create([
            'name' => 'Jane Target',
            'email' => 'target@example.com',
        ]);

        $category = Category::create([
            'name' => 'Vehicles',
            'slug' => 'vehicles',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->targetUser->id,
            'category_id' => $category->id,
            'title' => 'Suspicious Used Car',
            'slug' => 'suspicious-used-car',
            'description' => 'Listing with suspicious pricing',
            'price' => 500.00,
            'price_type' => 'fixed',
            'city_id' => $city->id,
            'status' => 'active',
        ]);
    }

    public function test_guest_cannot_submit_report(): void
    {
        $response = $this->postJson(route('reports.store'), [
            'reportable_type' => 'listing',
            'reportable_id' => $this->listing->id,
            'reason' => 'scam',
            'description' => 'Suspicious low price',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_report_a_listing(): void
    {
        $response = $this->actingAs($this->reporter)->postJson(route('reports.store'), [
            'reportable_type' => 'listing',
            'reportable_id' => $this->listing->id,
            'reason' => 'scam',
            'description' => 'Seller asking for gift cards.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $this->reporter->id,
            'reportable_type' => Listing::class,
            'reportable_id' => $this->listing->id,
            'reason' => 'scam',
            'description' => 'Seller asking for gift cards.',
            'status' => 'pending',
        ]);
    }

    public function test_user_can_report_another_user(): void
    {
        $response = $this->actingAs($this->reporter)->postJson(route('reports.store'), [
            'reportable_type' => 'user',
            'reportable_id' => $this->targetUser->id,
            'reason' => 'harassment',
            'description' => 'User sent abusive messages.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $this->reporter->id,
            'reportable_type' => User::class,
            'reportable_id' => $this->targetUser->id,
            'reason' => 'harassment',
            'status' => 'pending',
        ]);
    }

    public function test_user_cannot_report_themselves_or_own_listing(): void
    {
        // Self user report
        $response = $this->actingAs($this->reporter)->postJson(route('reports.store'), [
            'reportable_type' => 'user',
            'reportable_id' => $this->reporter->id,
            'reason' => 'spam',
        ]);

        $response->assertStatus(422);

        // Self listing report
        $myListing = Listing::create([
            'user_id' => $this->reporter->id,
            'category_id' => $this->listing->category_id,
            'title' => 'My own listing',
            'slug' => 'my-own-listing',
            'description' => 'Test',
            'price' => 10.00,
            'price_type' => 'fixed',
            'status' => 'active',
        ]);

        $response2 = $this->actingAs($this->reporter)->postJson(route('reports.store'), [
            'reportable_type' => 'listing',
            'reportable_id' => $myListing->id,
            'reason' => 'spam',
        ]);

        $response2->assertStatus(422);
    }

    public function test_user_cannot_submit_duplicate_pending_report(): void
    {
        Report::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => Listing::class,
            'reportable_id' => $this->listing->id,
            'reason' => 'scam',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->reporter)->postJson(route('reports.store'), [
            'reportable_type' => 'listing',
            'reportable_id' => $this->listing->id,
            'reason' => 'scam',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reportable_id']);
    }

    public function test_invalid_report_reason_fails_validation(): void
    {
        $response = $this->actingAs($this->reporter)->postJson(route('reports.store'), [
            'reportable_type' => 'listing',
            'reportable_id' => $this->listing->id,
            'reason' => 'invalid_random_reason',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reason']);
    }
}
