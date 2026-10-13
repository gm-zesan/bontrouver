<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\MemberTier;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMemberTierTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $this->regularUser = User::factory()->create([
            'role' => UserRole::USER,
        ]);

        // Seed basic member tiers
        MemberTier::create([
            'name_en' => '🥉 New Member',
            'name_fr' => '🥉 Nouveau membre',
            'icon' => '🥉',
            'badge_color' => '#cd7f32',
            'badge_class' => 'tier-badge tier-bronze',
            'min_points' => 0,
            'max_points' => 99,
            'description_en' => 'Welcome tier for newly registered members in Canada.',
            'description_fr' => 'Niveau de bienvenue pour les nouveaux membres au Canada.',
            'perks_en' => ['Standard posting rights', 'Basic community messaging'],
            'perks_fr' => ['Droits de publication standard', 'Messagerie communautaire de base'],
        ]);

        MemberTier::create([
            'name_en' => '🥈 Active Member',
            'name_fr' => '🥈 Membre actif',
            'icon' => '🥈',
            'badge_color' => '#a0a0a0',
            'badge_class' => 'tier-badge tier-silver',
            'min_points' => 100,
            'max_points' => 299,
            'description_en' => 'Members actively participating in community aid and marketplace.',
            'description_fr' => 'Membres participant activement à l\'entraide communautaire.',
            'perks_en' => ['Silver badge on listings', 'Priority customer support'],
            'perks_fr' => ['Badge argent sur les annonces', 'Support client prioritaire'],
        ]);

        MemberTier::create([
            'name_en' => '🥇 Trusted Member',
            'name_fr' => '🥇 Membre de confiance',
            'icon' => '🥇',
            'badge_color' => '#f59e0b',
            'badge_class' => 'tier-badge tier-gold',
            'min_points' => 300,
            'max_points' => 699,
            'description_en' => 'Highly trusted Canadian community members.',
            'description_fr' => 'Membres hautement fiables de la communauté canadienne.',
            'perks_en' => ['Gold verified badge', 'Discounts on promotions'],
            'perks_fr' => ['Badge or vérifié', 'Rabais sur les promotions'],
        ]);

        MemberTier::create([
            'name_en' => '⭐ Highly Appreciated Member',
            'name_fr' => '⭐ Membre hautement apprécié',
            'icon' => '⭐',
            'badge_color' => '#8b5cf6',
            'badge_class' => 'tier-badge tier-elite',
            'min_points' => 700,
            'max_points' => null,
            'description_en' => 'Pillar members with exceptional trust and mutual aid score.',
            'description_fr' => 'Piliers de la communauté avec un score exceptionnel.',
            'perks_en' => ['Star Elite badge', 'Free monthly featured ad'],
            'perks_fr' => ['Badge étoile élite', 'Annonce en vedette mensuelle gratuite'],
        ]);
    }

    public function test_guest_cannot_access_member_tiers_management(): void
    {
        $response = $this->get(route('admin.member-tiers.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_update_tier(): void
    {
        $tier = MemberTier::first();

        $response = $this->actingAs($this->regularUser)->putJson(route('admin.member-tiers.update', $tier), [
            'name' => 'Hacked Tier',
            'min_points' => 0,
            'max_points' => 100,
        ]);

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_adjust_points(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->regularUser)->postJson(route('admin.member-tiers.adjustPoints'), [
            'user_id' => $user->id,
            'type' => 'award',
            'amount' => 500,
            'reason' => 'Unauthorized points test',
        ]);

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_update_rules(): void
    {
        $response = $this->actingAs($this->regularUser)->putJson(route('admin.member-tiers.updateRules'), [
            'earn' => ['identity_verification' => 999],
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_view_member_tiers_index_page_and_kpis(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.member-tiers.index'));

        $response->assertOk();
        $response->assertViewIs('admin.member-tiers.index');
        $response->assertViewHas('tiers');
        $response->assertViewHas('rules');
        $response->assertViewHas('kpis');
        $response->assertViewHas('users');

        $response->assertSee('Member Tiers &amp; Points Configuration', false);
        $response->assertSee('New Member');
        $response->assertSee('Active Member');
        $response->assertSee('Trusted Member');
        $response->assertSee('Highly Appreciated Member');
    }

    public function test_admin_can_view_single_tier_details_json(): void
    {
        $tier = MemberTier::first();

        $response = $this->actingAs($this->admin)->get(route('admin.member-tiers.show', $tier));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'tier' => [
                'id' => $tier->id,
                'name' => $tier->name,
                'min_points' => $tier->min_points,
            ],
        ]);
    }

    public function test_admin_can_update_member_tier_thresholds_and_perks(): void
    {
        $tier = MemberTier::where('name_en', 'like', '%Active Member%')->first() ?? MemberTier::first();

        $response = $this->actingAs($this->admin)->put(route('admin.member-tiers.update', $tier), [
            'name_en' => '🥈 Active Member Pro',
            'name_fr' => '🥈 Membre Actif Pro',
            'icon' => '🥈',
            'badge_color' => '#b0b0b0',
            'badge_class' => 'tier-badge tier-silver',
            'min_points' => 120,
            'max_points' => 350,
            'description_en' => 'Updated tier description for active community members.',
            'description_fr' => 'Description mise à jour pour les membres actifs de la communauté.',
            'perks_en' => "Silver badge on listings\nPriority customer support\nExtended listing duration",
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'tier' => [
                'name' => '🥈 Active Member Pro',
                'min_points' => 120,
                'max_points' => 350,
            ],
        ]);

        $this->assertDatabaseHas('member_tiers', [
            'id' => $tier->id,
            'name_en' => '🥈 Active Member Pro',
            'min_points' => 120,
            'max_points' => 350,
        ]);
    }

    public function test_member_tier_update_validates_thresholds(): void
    {
        $tier = MemberTier::first();

        $response = $this->actingAs($this->admin)->putJson(route('admin.member-tiers.update', $tier), [
            'name_en' => '', // required
            'min_points' => -5, // min 0
            'max_points' => 'not-a-number',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name_en', 'min_points', 'max_points']);
    }

    public function test_admin_can_adjust_user_points_with_award(): void
    {
        $user = User::factory()->create([
            'community_points' => 50,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.member-tiers.adjustPoints'), [
            'user_id' => $user->id,
            'type' => 'award',
            'amount' => 75,
            'action_type' => 'admin_award',
            'reason' => 'Exceptional mutual aid assistance in Montreal neighborhood cleanup.',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'new_points' => 125,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'community_points' => 125,
        ]);

        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $user->id,
            'points' => 75,
            'action_type' => 'admin_award',
            'description' => 'Exceptional mutual aid assistance in Montreal neighborhood cleanup.',
        ]);
    }

    public function test_admin_can_adjust_user_points_with_deduct(): void
    {
        $user = User::factory()->create([
            'community_points' => 100,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.member-tiers.adjustPoints'), [
            'user_id' => $user->id,
            'type' => 'deduct',
            'amount' => 40,
            'action_type' => 'admin_deduct',
            'reason' => 'Administrative deduction due to canceled meetup without notice.',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'new_points' => 60,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'community_points' => 60,
        ]);

        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $user->id,
            'points' => -40,
            'action_type' => 'admin_deduct',
        ]);
    }

    public function test_admin_points_deduct_never_drops_user_below_zero(): void
    {
        $user = User::factory()->create([
            'community_points' => 20,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.member-tiers.adjustPoints'), [
            'user_id' => $user->id,
            'type' => 'deduct',
            'amount' => 100, // exceeds current 20
            'reason' => 'Penalty deduction',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'new_points' => 0,
        ]);

        $this->assertEquals(0, $user->fresh()->community_points);
    }

    public function test_admin_can_update_point_rules(): void
    {
        $response = $this->actingAs($this->admin)->putJson(route('admin.member-tiers.updateRules'), [
            'earn' => [
                'identity_verification' => 60,
                'verified_dealer' => 150,
                'positive_review' => 30,
            ],
            'spend' => [
                'featured_promotion' => 120,
                'sponsored_promotion' => 350,
            ],
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Point reward and spending rules updated successfully.',
        ]);
    }

    public function test_datatables_ajax_returns_point_ledger_records_and_filters(): void
    {
        $user = User::factory()->create(['name' => 'Alice Tremblay', 'email' => 'alice@bontrouver.ca']);

        PointTransaction::create([
            'user_id' => $user->id,
            'points' => 50,
            'action_type' => 'identity_verification',
            'description' => 'ID verified successfully',
        ]);

        PointTransaction::create([
            'user_id' => $user->id,
            'points' => -100,
            'action_type' => 'featured_promotion',
            'description' => 'Featured listing for 7 days',
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.member-tiers.index', [
            'draw' => 1,
            'action_type' => 'identity_verification',
            'type' => 'earned',
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Alice Tremblay', $data[0]['user']);
        $this->assertStringContainsString('+50 pts', $data[0]['points']);
    }
}
