<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use App\Notifications\SmartAlertMatched;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SmartAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_smart_alert(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('account.alerts.store'), [
            'name'      => 'Montreal Room Alert',
            'keyword'   => 'Furnished',
            'city'      => 'Montréal',
            'max_price' => 800,
        ]);

        $response->assertRedirect(route('account.alerts.index'));
        $this->assertDatabaseHas('smart_alerts', [
            'user_id'   => $user->id,
            'name'      => 'Montreal Room Alert',
            'city'      => 'Montréal',
            'max_price' => 800,
            'keyword'   => 'Furnished',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_alert(): void
    {
        $response = $this->post(route('account.alerts.store'), [
            'name' => 'Unauthorized Alert',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_delete_their_own_alert(): void
    {
        $user = User::factory()->create();
        
        $alert = $user->smartAlerts()->create([
            'name' => 'Test Alert',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->delete(route('account.alerts.destroy', $alert));

        $response->assertRedirect(route('account.alerts.index'));
        $this->assertDatabaseMissing('smart_alerts', ['id' => $alert->id]);
    }

    public function test_matching_engine_dispatches_notification_on_listing_creation(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $category = Category::create([
            'name' => 'Real Estate',
            'slug' => 'real-estate',
            'is_active' => true,
        ]);

        // Create an alert for Montreal, under 1000
        $buyer->smartAlerts()->create([
            'name' => 'Montreal Cheap Appt',
            'city' => 'Montréal',
            'max_price' => 1000,
            'is_active' => true,
            'category_id' => $category->id
        ]);

        // Creating a listing via Service should trigger the alert
        $listingService = app(\App\Services\ListingService::class);
        $this->actingAs($seller);
        
        $listingService->create([
            'title' => 'Awesome Room in Montreal',
            'description' => 'Great place',
            'price' => 800,
            'price_type' => 'fixed',
            'condition' => 'used',
            'city' => 'Montréal',
            'province' => 'QC',
            'category_slug' => $category->slug,
        ]);

        Notification::assertSentTo(
            [$buyer], SmartAlertMatched::class
        );

        // Make sure the seller does not get notified for their own listing
        Notification::assertNotSentTo(
            [$seller], SmartAlertMatched::class
        );
    }
}
