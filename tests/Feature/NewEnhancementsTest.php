<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use App\Models\Province;
use App\Models\SmartAlert;
use App\Models\User;
use App\Notifications\ListingBoostExpired;
use App\Notifications\ListingBoostExpiringSoon;
use App\Notifications\SmartAlertMatched;
use App\Services\ImageOptimizationService;
use App\Services\ListingAnalyticsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic dependencies if not present
        if (Category::count() === 0) {
            Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
        }
        if (Province::count() === 0) {
            Province::create(['name' => 'Ontario', 'code' => 'ON', 'slug' => 'ontario', 'country_code' => 'CA']);
        }
        if (City::count() === 0) {
            $prov = Province::first();
            City::create(['province_id' => $prov->id, 'name' => 'Toronto', 'slug' => 'toronto', 'latitude' => 43.6532, 'longitude' => -79.3832]);
        }
    }

    /** @test */
    public function test_it_deactivates_expired_promotions_and_sends_database_notifications()
    {
        $user = User::factory()->create();
        $listing = Listing::create([
            'user_id' => $user->id,
            'category_id' => Category::first()->id,
            'title' => 'iPhone 15 Pro Max',
            'slug' => 'iphone-15-pro-max-' . uniqid(),
            'description' => 'Brand new in box',
            'price' => 1200,
            'price_type' => 'fixed',
            'condition' => 'used',
            'status' => ListingStatus::ACTIVE->value,
            'is_sponsored' => true,
            'sponsored_until' => Carbon::now()->subDay(),
            'published_at' => Carbon::now()->subDays(10),
        ]);

        $promo = ListingPromotion::create([
            'listing_id' => $listing->id,
            'user_id' => $user->id,
            'type' => 'sponsored',
            'price_paid' => 9.99,
            'points_spent' => 0,
            'payment_method' => 'card',
            'payment_status' => 'completed',
            'starts_at' => Carbon::now()->subDays(8),
            'expires_at' => Carbon::now()->subDay(),
            'is_active' => true,
        ]);

        $this->artisan('listings:check-expiry')
            ->assertSuccessful();

        // Promo must now be inactive
        $this->assertFalse($promo->fresh()->is_active);

        // Listing boost flags must be reset
        $refreshed = $listing->fresh();
        $this->assertFalse($refreshed->is_sponsored);
        $this->assertNull($refreshed->sponsored_until);

        // Internal database notification must be recorded
        $allNotifs = $user->notifications()->get();
        $this->assertNotEmpty($allNotifs, "User notifications are empty. Count: " . $user->notifications()->count());
        $notif = $allNotifs->first();
        $this->assertNotNull($notif);
        $this->assertEquals('listing_boost_expired', $notif->data['type']);
    }

    /** @test */
    public function test_it_records_view_and_serves_seller_analytics_dashboard_endpoint()
    {
        $seller = User::factory()->create();
        $viewer = User::factory()->create();

        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => Category::first()->id,
            'title' => 'MacBook Air M2',
            'slug' => 'macbook-air-m2-' . uniqid(),
            'description' => 'Mint condition',
            'price' => 950,
            'price_type' => 'fixed',
            'condition' => 'used',
            'status' => ListingStatus::ACTIVE->value,
            'views_count' => 0,
            'published_at' => Carbon::now(),
        ]);

        $analyticsService = app(ListingAnalyticsService::class);
        $req = Request::create('/listing/' . $listing->slug, 'GET', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.100',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 TestBrowser',
        ]);

        $analyticsService->recordView($listing, $req);
        $this->assertEquals(1, $listing->fresh()->views_count);

        // Fetch analytics endpoint as authenticated seller
        $response = $this->actingAs($seller)->getJson(route('listings.my.analytics', $listing->id));
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.listing.id', $listing->id)
            ->assertJsonPath('data.stats.total_views', 1)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'listing' => ['id', 'title', 'price', 'status'],
                    'stats' => ['total_views', 'favorites_count', 'inquiries_count', 'engagement_rate'],
                    'boosts',
                    'chart' => ['labels', 'series'],
                ],
            ]);
    }

    /** @test */
    public function test_it_evaluates_smart_alerts_with_strictly_internal_database_notifications()
    {
        $alertOwner = User::factory()->create();
        $seller = User::factory()->create();

        $alert = SmartAlert::create([
            'user_id' => $alertOwner->id,
            'name' => 'Toronto Camera Search',
            'keyword' => 'Sony Alpha',
            'category_id' => Category::first()->id,
            'min_price' => 500,
            'max_price' => 2000,
            'is_active' => true,
        ]);

        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => Category::first()->id,
            'title' => 'Sony Alpha A7 IV Camera Body',
            'slug' => 'sony-alpha-a7-iv-' . uniqid(),
            'description' => 'Like new with box and 2 extra batteries',
            'price' => 1800,
            'price_type' => 'fixed',
            'condition' => 'used',
            'status' => ListingStatus::ACTIVE->value,
            'published_at' => Carbon::now(),
        ]);

        // Dispatch ListingCreated event
        \App\Events\ListingCreated::dispatch($listing);

        // Must create internal database notification for alertOwner
        $notif = $alertOwner->notifications()->where('type', SmartAlertMatched::class)->first();
        $this->assertNotNull($notif, "SmartAlertMatched notification was not found for alert owner.");
        $this->assertEquals($alert->id, $notif->data['alert_id']);
        $this->assertEquals($listing->id, $notif->data['listing_id']);
        $this->assertStringContainsString('Toronto Camera Search', $notif->data['title']);
        $this->assertStringContainsString('Sony Alpha', $notif->data['message']);
    }

    /** @test */
    public function test_it_optimizes_and_converts_uploaded_images_to_webp()
    {
        Storage::fake('public');
        $optimizer = app(ImageOptimizationService::class);

        $uploadedFile = UploadedFile::fake()->image('camera.jpg', 800, 600);
        $savedPath = $optimizer->optimizeUploadedFile($uploadedFile, 'listings');

        $this->assertNotNull($savedPath);
        $this->assertStringEndsWith('.webp', $savedPath);
    }
}
