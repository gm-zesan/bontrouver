<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\MemberTier;
use App\Models\PointTransaction;
use App\Models\Province;
use App\Models\User;
use App\Notifications\MemberTierUpgraded;
use App\Services\AdminMemberTierService;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MemberTierEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Canadian provinces & cities
        $qc = Province::create(['name' => 'Quebec', 'code' => 'QC', 'slug' => 'quebec']);
        City::create([
            'province_id' => $qc->id,
            'name'        => 'Montreal',
            'slug'        => 'montreal',
            'latitude'    => 45.5017,
            'longitude'   => -73.5673,
            'is_active'   => true,
        ]);

        // Seed Member Tiers
        MemberTier::create([
            'name' => '🥉 New Member',
            'icon' => '🥉',
            'badge_color' => '#cd7f32',
            'badge_class' => 'bg-secondary',
            'min_points' => 0,
            'max_points' => 99,
            'description' => 'Entry level member',
            'perks' => ['Post basic ads'],
        ]);

        MemberTier::create([
            'name' => '🥈 Active Member',
            'icon' => '🥈',
            'badge_color' => '#c0c0c0',
            'badge_class' => 'bg-info',
            'min_points' => 100,
            'max_points' => 299,
            'description' => 'Active participant',
            'perks' => ['Featured discounts'],
        ]);

        MemberTier::create([
            'name' => '🥇 Trusted Member',
            'icon' => '🥇',
            'badge_color' => '#ffd700',
            'badge_class' => 'bg-warning text-dark',
            'min_points' => 300,
            'max_points' => 699,
            'description' => 'Trusted Canadian neighbor',
            'perks' => ['Verified trust badge'],
        ]);
    }

    /**
     * Test 1: User Points Ledger (/account/points)
     */
    public function test_authenticated_user_can_view_points_ledger_page(): void
    {
        $user = User::factory()->create([
            'community_points' => 150,
        ]);

        PointTransaction::create([
            'user_id' => $user->id,
            'points' => 50,
            'action_type' => 'identity_verification',
            'description' => 'Canadian ID Verification Bonus',
        ]);

        PointTransaction::create([
            'user_id' => $user->id,
            'points' => -30,
            'action_type' => 'featured_promotion',
            'description' => 'Featured Ad Placement',
        ]);

        $response = $this->actingAs($user)->get(route('account.points'));

        $response->assertStatus(200);
        $response->assertSee('Community Points &amp; Standing', false);
        $response->assertSee('Canadian ID Verification Bonus');
        $response->assertSee('+50 pts');
        $response->assertSee('-30 pts');
        $response->assertSee('Active Member');
    }

    /**
     * Test 2: User can filter points ledger by earned or spent
     */
    public function test_user_can_filter_points_ledger_by_type(): void
    {
        $user = User::factory()->create(['community_points' => 120]);

        PointTransaction::create([
            'user_id' => $user->id,
            'points' => 100,
            'action_type' => 'verified_dealer',
            'description' => 'Dealer License Bonus',
        ]);

        PointTransaction::create([
            'user_id' => $user->id,
            'points' => -50,
            'action_type' => 'sponsored_promotion',
            'description' => 'Spotlight Placement',
        ]);

        $earnedResponse = $this->actingAs($user)->get(route('account.points', ['type' => 'earned']));
        $earnedResponse->assertStatus(200);
        $earnedResponse->assertSee('Dealer License Bonus');
        $earnedResponse->assertDontSee('Spotlight Placement');

        $spentResponse = $this->actingAs($user)->get(route('account.points', ['type' => 'spent']));
        $spentResponse->assertStatus(200);
        $spentResponse->assertSee('Spotlight Placement');
        $spentResponse->assertDontSee('Dealer License Bonus');
    }

    /**
     * Test 3: PointService triggers MemberTierUpgraded notification when crossing tier boundary
     */
    public function test_point_award_triggers_tier_level_up_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'community_points' => 80, // New Member (0-99)
        ]);

        $pointService = app(PointService::class);
        $pointService->awardPoints($user, 50, 'identity_verification', 'ID Verified'); // Now 130 pts -> Active Member (Level 2)

        Notification::assertSentTo(
            $user,
            MemberTierUpgraded::class,
            function (MemberTierUpgraded $notification) {
                return $notification->newTier['level'] === 2 && $notification->points === 130;
            }
        );
    }

    /**
     * Test 4: Admin points adjustment triggers tier level up notification
     */
    public function test_admin_adjust_points_triggers_tier_level_up(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'community_points' => 50, // Level 1
        ]);

        $adminService = app(AdminMemberTierService::class);
        $adminService->adjustUserPoints($user, 300, 'admin_award', 'Exceptional community support');

        Notification::assertSentTo(
            $user,
            MemberTierUpgraded::class,
            function (MemberTierUpgraded $notification) {
                return $notification->newTier['level'] === 3 && $notification->points === 350;
            }
        );
    }

    /**
     * Test 5: Message conversations include counterpart member tier data
     */
    public function test_messages_inbox_contains_counterpart_member_tier(): void
    {
        $buyer = User::factory()->create(['name' => 'Buyer Alice', 'community_points' => 50]);
        $seller = User::factory()->create(['name' => 'Seller Bob', 'community_points' => 350]); // Trusted Member

        $conversation = Conversation::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $response = $this->actingAs($buyer)->get(route('messages.index', ['c' => $conversation->id]));

        $response->assertStatus(200);
        $response->assertSee('Seller Bob');
        $response->assertSee('Trusted Member');
    }

    /**
     * Test 6: Notification index displays MemberTierUpgraded correctly
     */
    public function test_notification_center_renders_tier_upgraded_notification(): void
    {
        $user = User::factory()->create(['community_points' => 320]);

        $tier = $user->member_tier;
        $user->notify(new MemberTierUpgraded($tier, ['level' => 1, 'name' => 'New Member'], 320));

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Level Up!');
        $response->assertSee('Trusted Member');
    }

    /**
     * Test 7: Guest and authenticated users can view the member benefits page
     */
    public function test_guest_and_authenticated_users_can_view_member_benefits_page(): void
    {
        // 1. Guest
        $guestResponse = $this->get(route('pages.member-benefits'));
        $guestResponse->assertStatus(200);
        $guestResponse->assertSee('Reputation Points');
        $guestResponse->assertSee('Canadian Member Tiers');
        $guestResponse->assertSee('Active Member');
        $guestResponse->assertSee('Trusted Member');

        // 2. Authenticated
        $user = User::factory()->create([
            'name' => 'Jean Tremblay',
            'community_points' => 240,
        ]);

        $authResponse = $this->actingAs($user)->get(route('pages.member-benefits'));
        $authResponse->assertStatus(200);
        $authResponse->assertSee('Jean Tremblay');
        $authResponse->assertSee('240 Points');
        $authResponse->assertSee('View Points History');
    }
}
