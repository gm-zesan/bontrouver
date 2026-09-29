<?php

namespace Tests\Feature\Admin;

use App\Enums\ReportReason;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use App\Models\Report;
use App\Models\User;
use App\Models\UserVerification;
use App\Services\AdminNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
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
        $this->seller = User::where('role', UserRole::USER)->first() ?? User::factory()->create(['role' => UserRole::USER]);
    }

    public function test_admin_notification_service_aggregates_important_notifications(): void
    {
        $category = Category::first();
        $listing = Listing::create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'title' => 'Suspicious Listing',
            'slug' => 'suspicious-listing-' . uniqid(),
            'description' => 'A listing that got reported for fraud.',
            'price' => 1000,
            'price_type' => 'fixed',
            'city' => 'Toronto',
            'province' => 'ON',
            'latitude' => 43.6532,
            'longitude' => -79.3832,
            'status' => 'active',
        ]);

        // 1. Create an unreviewed report
        Report::create([
            'reporter_id' => $this->seller->id,
            'reportable_id' => $listing->id,
            'reportable_type' => Listing::class,
            'reason' => ReportReason::SCAM,
            'details' => 'Seller asked for gift card payment.',
        ]);

        // 2. Create a pending ID verification
        UserVerification::create([
            'user_id' => $this->seller->id,
            'document_type' => 'drivers_license',
            'document_path' => 'verifications/test_doc.jpg',
            'status' => 'pending',
        ]);

        $service = app(AdminNotificationService::class);
        $data = $service->getImportantNotifications();

        $this->assertGreaterThanOrEqual(2, $data['urgent_count']);
        $this->assertEquals(1, $data['stats']['unresolved_reports']);
        $this->assertEquals(1, $data['stats']['pending_verifications']);
    }

    public function test_admin_header_displays_priority_notification_dropdown(): void
    {
        // Create an unreviewed safety report
        $category = Category::first();
        $listing = Listing::create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'title' => 'Flagged Item',
            'slug' => 'flagged-item-' . uniqid(),
            'description' => 'A flagged item for admin moderation queue.',
            'price' => 200,
            'price_type' => 'fixed',
            'city' => 'Montreal',
            'province' => 'QC',
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'status' => 'active',
        ]);

        Report::create([
            'reporter_id' => $this->seller->id,
            'reportable_id' => $listing->id,
            'reportable_type' => Listing::class,
            'reason' => ReportReason::PROHIBITED,
            'details' => 'Contains restricted content.',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Priority Alerts');
        $response->assertSee('Action Required');
        $response->assertSee('Safety Report');
        $response->assertSee('View All Notifications');
    }

    public function test_admin_can_access_dedicated_notifications_page(): void
    {
        $category = Category::first();
        $listing = Listing::create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'title' => 'Suspicious Listing Test',
            'slug' => 'suspicious-listing-test-' . uniqid(),
            'description' => 'A listing that got reported for fraud.',
            'price' => 1000,
            'price_type' => 'fixed',
            'city' => 'Toronto',
            'province' => 'ON',
            'latitude' => 43.6532,
            'longitude' => -79.3832,
            'status' => 'active',
        ]);

        Report::create([
            'reporter_id' => $this->seller->id,
            'reportable_id' => $listing->id,
            'reportable_type' => Listing::class,
            'reason' => ReportReason::SCAM,
            'description' => 'Seller asked for gift card payment.',
        ]);

        UserVerification::create([
            'user_id' => $this->seller->id,
            'document_type' => 'drivers_license',
            'document_path' => 'verifications/test_doc.jpg',
            'id_number' => 'DL-994821',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.notifications.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Priority Notifications & Moderation Queue');
        $response->assertSeeText('Action Required');
        $response->assertSeeText('Safety Reports');
        $response->assertSeeText('ID Verifications');
        $response->assertSeeText('Seller asked for gift card payment.');
        $response->assertSeeText('DL-994821');
    }
}

