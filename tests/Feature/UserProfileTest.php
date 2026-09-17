<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\Province;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private Province $province;
    private City $city;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->province = Province::create([
            'name' => 'Quebec',
            'code' => 'QC',
            'slug' => 'quebec',
            'country_code' => 'CA',
        ]);

        $this->city = City::create([
            'province_id' => $this->province->id,
            'name' => 'Montréal',
            'slug' => 'montreal',
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'icon' => 'bi-laptop',
            'is_active' => true,
        ]);
    }

    public function test_user_can_view_own_profile_view(): void
    {
        $user = User::factory()->create([
            'name' => 'Alex Tremblay',
            'community_points' => 150,
            'city' => 'Montréal',
            'province' => 'QC',
        ]);

        $response = $this->actingAs($user)->get(route('profile.view'));

        $response->assertStatus(200);
        $response->assertSee('Alex Tremblay');
        $response->assertSee('Active Member');
        $response->assertSee('150');
    }

    public function test_guest_can_view_public_seller_profile(): void
    {
        $seller = User::factory()->create([
            'name' => 'Sarah Verified',
            'is_verified' => true,
            'community_points' => 450,
            'bio' => 'Trusted electronics seller based in Montreal.',
        ]);

        $response = $this->get(route('user.profile', $seller));

        $response->assertStatus(200);
        $response->assertSee('Sarah Verified');
        $response->assertSee('Trusted Member');
        $response->assertSee('450');
        $response->assertSee('Trusted electronics seller based in Montreal.');
    }

    public function test_user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'phone' => '1111111111',
            'city' => 'Toronto',
            'province' => 'ON',
        ]);

        $response = $this->actingAs($user)->post(route('settings.update'), [
            'name' => 'Updated Canadian Seller',
            'phone' => '+1 (514) 555-0199',
            'city' => 'Montréal',
            'province' => 'QC',
            'postal_code' => 'H2X 1Y4',
            'location' => 'Plateau-Mont-Royal',
            'bio' => 'Passionate community helper and vintage goods collector.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Profile and settings updated successfully.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Canadian Seller',
            'phone' => '+1 (514) 555-0199',
            'city' => 'Montréal',
            'province' => 'QC',
            'postal_code' => 'H2X 1Y4',
            'location' => 'Plateau-Mont-Royal',
            'bio' => 'Passionate community helper and vintage goods collector.',
        ]);
    }

    public function test_user_can_upload_avatar_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('my-avatar.jpg', 300, 300);

        $response = $this->actingAs($user)->post(route('settings.update'), [
            'name' => $user->name,
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        
        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertStringContainsString('/storage/avatars/', $user->avatar);
    }

    public function test_user_can_update_avatar_with_base64_payload(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        // 1x1 transparent png data URI
        $base64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        $response = $this->actingAs($user)->post(route('settings.update'), [
            'name' => $user->name,
            'avatar_base64' => $base64,
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertStringContainsString('/storage/avatars/', $user->avatar);
    }

    public function test_member_tier_progression_brackets(): void
    {
        // 1. New Member (0 - 99 pts)
        $newMember = User::factory()->make(['community_points' => 45]);
        $tier = $newMember->member_tier;
        $this->assertEquals('New Member', $tier['name']);
        $this->assertEquals('🥉', $tier['icon']);
        $this->assertEquals('Active Member', $tier['next_tier']);
        $this->assertEquals(55, $tier['points_needed']);
        $this->assertEquals(45, $tier['progress_percentage']);

        // 2. Active Member (100 - 299 pts)
        $activeMember = User::factory()->make(['community_points' => 200]);
        $tier = $activeMember->member_tier;
        $this->assertEquals('Active Member', $tier['name']);
        $this->assertEquals('🥈', $tier['icon']);
        $this->assertEquals('Trusted Member', $tier['next_tier']);
        $this->assertEquals(100, $tier['points_needed']);
        $this->assertEquals(50, $tier['progress_percentage']);

        // 3. Trusted Member (300 - 699 pts)
        $trustedMember = User::factory()->make(['community_points' => 500]);
        $tier = $trustedMember->member_tier;
        $this->assertEquals('Trusted Member', $tier['name']);
        $this->assertEquals('🥇', $tier['icon']);
        $this->assertEquals('Highly Appreciated Member', $tier['next_tier']);
        $this->assertEquals(200, $tier['points_needed']);
        $this->assertEquals(50, $tier['progress_percentage']);

        // 4. Highly Appreciated Member (700+ pts)
        $eliteMember = User::factory()->make(['community_points' => 850]);
        $tier = $eliteMember->member_tier;
        $this->assertEquals('Highly Appreciated Member', $tier['name']);
        $this->assertEquals('⭐', $tier['icon']);
        $this->assertNull($tier['next_tier']);
        $this->assertEquals(100, $tier['progress_percentage']);
    }

    public function test_completed_transactions_count_accessor(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        // 2 completed sales
        Transaction::create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'amount' => 50.00,
            'status' => 'completed',
        ]);
        Transaction::create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'amount' => 120.00,
            'status' => 'completed',
        ]);
        // 1 pending sale (not counted)
        Transaction::create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'amount' => 200.00,
            'status' => 'initiated',
        ]);

        $this->assertEquals(2, $seller->completed_transactions_count);
        $this->assertEquals(2, $buyer->completed_transactions_count);
    }

    public function test_public_profile_displays_listings_and_reviews(): void
    {
        $seller = User::factory()->create(['name' => 'Elena Marketplace']);
        $buyer = User::factory()->create(['name' => 'Marc Reviewer']);

        $listing = Listing::create([
            'user_id' => $seller->id,
            'category_id' => $this->category->id,
            'city_id' => $this->city->id,
            'title' => 'MacBook Pro M2 Space Gray',
            'slug' => 'macbook-pro-m2-space-gray-' . uniqid(),
            'description' => 'Mint condition MacBook with charger.',
            'price' => 1299.00,
            'price_type' => 'fixed',
            'status' => 'active',
            'city' => 'Montréal',
            'province' => 'QC',
            'postal_code' => 'H2X 1Y4',
        ]);

        Review::create([
            'reviewer_id' => $buyer->id,
            'reviewee_id' => $seller->id,
            'listing_id' => $listing->id,
            'rating' => 5,
            'comment' => 'Fantastic seller! Fast meetup and honest description.',
        ]);

        $response = $this->get(route('user.profile', $seller));

        $response->assertStatus(200);
        $response->assertSee('Elena Marketplace');
        $response->assertSee('MacBook Pro M2 Space Gray');
        $response->assertSee('Marc Reviewer');
        $response->assertSee('Fantastic seller! Fast meetup and honest description.');
    }

    public function test_public_profile_displays_hosted_meetups(): void
    {
        $host = User::factory()->create(['name' => 'David Meetup Host']);

        $meetup = CompanionshipRequest::create([
            'user_id' => $host->id,
            'city_id' => $this->city->id,
            'type' => 'Coffee & Chat',
            'title' => 'Saturday Coffee in Plateau',
            'description' => 'Casual talk about tech and local culture.',
            'meetup_date_time' => now()->addDays(2),
            'location_name' => 'Café Olimpico',
            'city' => 'Montréal',
            'province' => 'QC',
            'headcount_limit' => 6,
            'expense_type' => 'split',
            'status' => 'open',
        ]);

        $response = $this->get(route('user.profile', $host));

        $response->assertStatus(200);
        $response->assertSee('David Meetup Host');
        $response->assertSee('Saturday Coffee in Plateau');
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'old-password-123',
            'password' => 'NewSecurePassword!2026',
            'password_confirmation' => 'NewSecurePassword!2026',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword!2026', $user->password));
    }

    public function test_user_can_delete_account_with_valid_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('delete-me-password'),
        ]);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'delete-me-password',
        ]);

        $response->assertRedirect('/');
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    public function test_user_can_toggle_notification_preferences_via_ajax(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => ['messages' => true, 'alerts' => true, 'meetups' => true],
        ]);

        $response = $this->actingAs($user)->postJson(route('settings.notifications.toggle'), [
            'key' => 'alerts',
            'enabled' => 0,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'key' => 'alerts',
            'enabled' => false,
        ]);

        $user->refresh();
        $this->assertFalse($user->wantsNotification('alerts'));
        $this->assertTrue($user->wantsNotification('messages'));
        $this->assertTrue($user->wantsNotification('meetups'));
    }

    public function test_user_can_update_all_notification_preferences(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('settings.notifications.update'), [
            'messages' => '1',
            'alerts' => '0',
            'meetups' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Notification preferences updated successfully.');

        $user->refresh();
        $this->assertTrue($user->wantsNotification('messages'));
        $this->assertFalse($user->wantsNotification('alerts'));
        $this->assertTrue($user->wantsNotification('meetups'));
    }
}
