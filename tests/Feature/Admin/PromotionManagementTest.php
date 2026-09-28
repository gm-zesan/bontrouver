<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use App\Models\User;
use App\Notifications\ListingBoostActivated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PromotionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $seller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ProvinceSeeder::class);
        $this->seed(\Database\Seeders\CitySeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\CategoryAttributeSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
        $this->seed(\Database\Seeders\MonetizationSeeder::class);

        $this->admin = User::where('role', UserRole::ADMIN)->first() ?? User::factory()->create(['role' => UserRole::ADMIN]);
        $this->seller = User::where('role', UserRole::USER)->first() ?? User::factory()->create(['role' => UserRole::USER, 'community_points' => 500]);
    }

    public function test_admin_can_view_promotions_monetization_hub(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/promotions');

        $response->assertStatus(200);
        $response->assertSee('Sponsored Spotlight');
        $response->assertSee('Featured Highlight');
        $response->assertSee('Instant Bump-Up');
        $response->assertSee('Export Revenue CSV');
    }

    public function test_admin_can_export_promotions_ledger_csv(): void
    {
        $category = \App\Models\Category::first();
        $listing = Listing::create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'title' => 'Test CSV Export Listing',
            'slug' => 'test-csv-export-listing-' . uniqid(),
            'description' => 'Test listing for admin CSV export assertion.',
            'price' => 500,
            'price_type' => 'fixed',
            'city' => 'Toronto',
            'province' => 'ON',
            'latitude' => 43.6532,
            'longitude' => -79.3832,
            'status' => 'active',
        ]);
        $package = PromotionPackage::where('type', 'sponsored')->first();

        ListingPromotion::create([
            'listing_id' => $listing->id,
            'user_id' => $this->seller->id,
            'promotion_package_id' => $package->id,
            'type' => 'sponsored',
            'price_paid' => 9.99,
            'points_spent' => 0,
            'payment_method' => 'stripe',
            'payment_status' => 'completed',
            'transaction_reference' => 'BT-STRIPE-TEST123',
            'starts_at' => now(),
            'expires_at' => now()->addDays(7),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/promotions/export-csv');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('BT-STRIPE-TEST123', $content);
        $this->assertStringContainsString('Sponsored Spotlight', $content);
        $this->assertStringContainsString($this->seller->name, $content);
    }

    public function test_user_receives_internal_platform_notification_upon_boost(): void
    {
        $category = \App\Models\Category::first();
        $listing = Listing::create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'title' => 'Notification Boosted Item',
            'slug' => 'notification-boosted-item-' . uniqid(),
            'description' => 'A valid test item description exceeding 15 chars.',
            'price' => 250,
            'price_type' => 'fixed',
            'city' => 'Montreal',
            'province' => 'QC',
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'status' => 'active',
        ]);
        $package = PromotionPackage::where('type', 'featured')->first();

        $promotion = ListingPromotion::create([
            'listing_id' => $listing->id,
            'user_id' => $this->seller->id,
            'promotion_package_id' => $package->id,
            'type' => 'featured',
            'price_paid' => 4.99,
            'points_spent' => 0,
            'payment_method' => 'stripe',
            'payment_status' => 'completed',
            'transaction_reference' => 'BT-STRIPE-NOTIFTEST',
            'starts_at' => now(),
            'expires_at' => now()->addDays(7),
            'is_active' => true,
        ]);

        // Send internal database notification
        $this->seller->notify(new ListingBoostActivated($listing, $package, $promotion));

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->seller->id,
            'notifiable_type' => User::class,
            'type' => ListingBoostActivated::class,
        ]);

        // Verify notification is rendered in Notifications Center view
        $response = $this->actingAs($this->seller)->get('/notifications');
        $response->assertStatus(200);
        $response->assertSee('Boost Activated');
        $response->assertSee($listing->title);
    }
}
