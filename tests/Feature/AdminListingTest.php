<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Province;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminListingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $seller;
    private Category $category;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\MemberTierSeeder::class);

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

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'name' => 'Admin Officer',
            'email' => 'admin@bontrouver.ca',
        ]);

        $this->seller = User::factory()->create([
            'role' => UserRole::USER,
            'name' => 'Regular Seller',
            'email' => 'seller@example.com',
        ]);

        $this->category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $this->listing = Listing::create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'title' => 'iPhone 15 Pro Max',
            'slug' => 'iphone-15-pro-max',
            'description' => 'Mint condition unlocked phone',
            'price' => 1200.00,
            'price_type' => 'fixed',
            'city_id' => $city->id,
            'city' => 'Toronto',
            'province' => 'Ontario',
            'postal_code' => 'M5V 2T6',
            'status' => 'active',
            'is_featured' => false,
            'is_sponsored' => false,
        ]);
    }

    public function test_admin_can_access_listings_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.listings.index'));
        $response->assertStatus(200);
        $response->assertSee('Listings Management');
        $response->assertSee('id="listings-data-table"', false);
    }

    public function test_admin_can_fetch_listings_datatable_ajax(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.listings.index'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'recordsTotal',
            'recordsFiltered'
        ]);
        $response->assertSee('iPhone 15 Pro Max', false);
    }

    public function test_admin_can_view_listing_inspection_details(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.listings.show', $this->listing->id));
        $response->assertStatus(200);
        $response->assertSee($this->listing->title);
        $response->assertSee('Mint condition unlocked phone');
        $response->assertSee($this->seller->name);
    }

    public function test_admin_can_toggle_listing_status(): void
    {
        $this->assertEquals(\App\Enums\ListingStatus::ACTIVE, $this->listing->status);

        $response = $this->actingAs($this->admin)->post(route('admin.listings.toggleStatus', $this->listing->id));
        $response->assertRedirect();

        $this->listing->refresh();
        $this->assertEquals(\App\Enums\ListingStatus::PAUSED, $this->listing->status);

        $response = $this->actingAs($this->admin)->post(route('admin.listings.toggleStatus', $this->listing->id));
        $this->listing->refresh();
        $this->assertEquals(\App\Enums\ListingStatus::ACTIVE, $this->listing->status);
    }

    public function test_admin_can_toggle_featured_and_sponsored(): void
    {
        $this->assertFalse($this->listing->is_featured);
        $response = $this->actingAs($this->admin)->post(route('admin.listings.toggleFeatured', $this->listing->id));
        $response->assertRedirect();
        $this->listing->refresh();
        $this->assertTrue($this->listing->is_featured);

        $this->assertFalse($this->listing->is_sponsored);
        $response = $this->actingAs($this->admin)->post(route('admin.listings.toggleSponsored', $this->listing->id));
        $response->assertRedirect();
        $this->listing->refresh();
        $this->assertTrue($this->listing->is_sponsored);
    }

    public function test_admin_can_delete_listing(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.listings.destroy', $this->listing->id));
        $response->assertRedirect(route('admin.listings.index'));

        $this->assertSoftDeleted('listings', [
            'id' => $this->listing->id,
        ]);
    }

    public function test_admin_can_execute_bulk_actions_on_listings(): void
    {
        $listing2 = Listing::create([
            'user_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'title' => 'MacBook Pro M3',
            'slug' => 'macbook-pro-m3',
            'description' => 'Fast Apple Silicon laptop',
            'price' => 2400.00,
            'price_type' => 'fixed',
            'status' => \App\Enums\ListingStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.listings.bulk'), [
            'action' => 'pause',
            'listing_ids' => [$this->listing->id, $listing2->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 2,
        ]);

        $this->listing->refresh();
        $listing2->refresh();
        $this->assertEquals(\App\Enums\ListingStatus::PAUSED, $this->listing->status);
        $this->assertEquals(\App\Enums\ListingStatus::PAUSED, $listing2->status);
    }

    public function test_admin_can_update_listing_status_via_enum_modal(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.listings.updateStatus', $this->listing->id), [
            'status' => \App\Enums\ListingStatus::SOLD->value,
            'admin_notes' => 'Marked as sold per seller request.',
        ]);

        $response->assertRedirect();
        $this->listing->refresh();
        $this->assertEquals(\App\Enums\ListingStatus::SOLD, $this->listing->status);
    }

    public function test_admin_can_resolve_and_dismiss_listing_moderation_reports(): void
    {
        $report = \App\Models\Report::create([
            'reporter_id' => $this->seller->id,
            'reportable_type' => Listing::class,
            'reportable_id' => $this->listing->id,
            'reason' => \App\Enums\ReportReason::PROHIBITED,
            'description' => 'Suspected prohibited item.',
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $report->status);

        // Resolve report
        $response = $this->actingAs($this->admin)->post(route('admin.listings.reports.resolve', [$this->listing->id, $report->id]));
        $response->assertRedirect();
        $report->refresh();
        $this->assertEquals('resolved', $report->status);
        $this->assertEquals($this->admin->id, $report->reviewed_by);

        // Dismiss report
        $response2 = $this->actingAs($this->admin)->post(route('admin.listings.reports.dismiss', [$this->listing->id, $report->id]));
        $response2->assertRedirect();
        $report->refresh();
        $this->assertEquals('dismissed', $report->status);
    }

    public function test_category_full_path_hierarchy_display(): void
    {
        $subCat = Category::create([
            'parent_id' => $this->category->id,
            'name' => 'Smartphones',
            'slug' => 'smartphones',
        ]);

        $this->assertEquals('Electronics → Smartphones', $subCat->full_path);
        $this->assertEquals('Electronics', $this->category->full_path);
    }
}
