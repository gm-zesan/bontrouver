<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserVerification;
use App\Models\PointRule;
use Database\Seeders\MemberTierSeeder;
use Database\Seeders\PointRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(MemberTierSeeder::class);
        $this->seed(PointRuleSeeder::class);
    }

    public function test_admin_can_view_verification_queue(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user = User::factory()->create();

        UserVerification::create([
            'user_id'       => $user->id,
            'document_type' => 'drivers_license',
            'document_path' => 'verifications/lic_test.jpg',
            'id_number'     => 'DL-998877-CA',
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.verifications.index'));

        $response->assertOk();
        $response->assertSee('Canadian ID Verification Center');
        $response->assertSee('Total Submissions');
        $response->assertSee('Pending Review');
    }

    public function test_admin_can_fetch_verifications_via_ajax_datatables(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user = User::factory()->create(['name' => 'Marc Bouchard']);

        UserVerification::create([
            'user_id'       => $user->id,
            'document_type' => 'drivers_license',
            'document_path' => 'verifications/lic_test.jpg',
            'id_number'     => 'DL-998877-CA',
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.verifications.index'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
        $this->assertStringContainsString('Marc Bouchard', json_encode($response->json()));
    }

    public function test_admin_can_view_verification_inspector_details(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user = User::factory()->create([
            'name'  => 'Sarah Tremblay',
            'email' => 'sarah.tremblay@test.ca',
            'city'  => 'Montréal',
        ]);

        $verification = UserVerification::create([
            'user_id'       => $user->id,
            'document_type' => 'passport',
            'document_path' => 'verifications/passport_ca.jpg',
            'id_number'     => 'CA-PASSPORT-445566',
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.verifications.show', $verification));

        $response->assertOk();
        $response->assertJson([
            'success'      => true,
            'verification' => [
                'id'            => $verification->id,
                'status'        => 'pending',
                'document_type' => 'Passport',
                'id_number'     => 'CA-PASSPORT-445566',
                'user'          => [
                    'id'    => $user->id,
                    'name'  => 'Sarah Tremblay',
                    'email' => 'sarah.tremblay@test.ca',
                ],
            ],
        ]);
    }

    public function test_admin_can_approve_verification_and_award_points(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user = User::factory()->create([
            'community_points' => 10,
            'is_verified'      => false,
        ]);

        $verification = UserVerification::create([
            'user_id'       => $user->id,
            'document_type' => 'drivers_license',
            'document_path' => 'verifications/lic_test.jpg',
            'id_number'     => 'DL-12345-QC',
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.verifications.approve', $verification));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $verification->refresh();
        $this->assertEquals('approved', $verification->status);
        $this->assertEquals($admin->id, $verification->reviewed_by);
        $this->assertNotNull($verification->reviewed_at);

        $user->refresh();
        $this->assertTrue((bool) $user->is_verified);
        $this->assertEquals(60, $user->community_points); // 10 initial + 50 points

        $this->assertDatabaseHas('point_transactions', [
            'user_id'     => $user->id,
            'points'      => 50,
            'action_type' => 'verified_identity',
        ]);
    }

    public function test_admin_can_reject_verification_with_reason(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user = User::factory()->create([
            'is_verified' => false,
        ]);

        $verification = UserVerification::create([
            'user_id'       => $user->id,
            'document_type' => 'drivers_license',
            'document_path' => 'verifications/blurry_scan.jpg',
            'status'        => 'pending',
        ]);

        $reason = 'Document scan is blurry and unreadable. Please submit a clearer photo.';

        $response = $this->actingAs($admin)->postJson(route('admin.verifications.reject', $verification), [
            'reason' => $reason,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $verification->refresh();
        $this->assertEquals('rejected', $verification->status);
        $this->assertEquals($reason, $verification->rejection_reason);
        $this->assertEquals($admin->id, $verification->reviewed_by);

        $user->refresh();
        $this->assertFalse((bool) $user->is_verified);
    }

    public function test_admin_can_perform_bulk_actions(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $v1 = UserVerification::create([
            'user_id'       => User::factory()->create()->id,
            'document_type' => 'drivers_license',
            'status'        => 'pending',
        ]);

        $v2 = UserVerification::create([
            'user_id'       => User::factory()->create()->id,
            'document_type' => 'passport',
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.verifications.bulk'), [
            'action' => 'approve',
            'ids'    => [$v1->id, $v2->id],
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertEquals('approved', $v1->fresh()->status);
        $this->assertEquals('approved', $v2->fresh()->status);
    }

    public function test_non_admin_cannot_access_verification_endpoints(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER]);
        $targetUser = User::factory()->create();

        $verification = UserVerification::create([
            'user_id'       => $targetUser->id,
            'document_type' => 'drivers_license',
            'status'        => 'pending',
        ]);

        $this->actingAs($user)->get(route('admin.verifications.index'))->assertForbidden();
        $this->actingAs($user)->getJson(route('admin.verifications.show', $verification))->assertForbidden();
        $this->actingAs($user)->postJson(route('admin.verifications.approve', $verification))->assertForbidden();
        $this->actingAs($user)->postJson(route('admin.verifications.reject', $verification))->assertForbidden();
    }
}
