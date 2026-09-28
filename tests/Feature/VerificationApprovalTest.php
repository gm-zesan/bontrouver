<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserVerification;
use App\Services\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_approval_sets_verified_badge_and_awards_points(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'is_verified' => false,
            'community_points' => 0,
        ]);

        $verification = UserVerification::create([
            'user_id' => $user->id,
            'document_type' => 'drivers_license',
            'document_path' => 'verifications/test_license.jpg',
            'id_number' => 'DL-987654321',
            'status' => 'pending',
        ]);

        // Verify initial state
        $this->assertFalse((bool) $user->is_verified);
        $this->assertEquals('pending', $verification->status);

        // Admin approves the verification request
        $service = app(VerificationService::class);
        $service->reviewVerification($verification, 'approved', null, $admin);

        // Refresh models from DB
        $user->refresh();
        $verification->refresh();

        // 1. Verification status is approved and reviewer recorded
        $this->assertEquals('approved', $verification->status);
        $this->assertEquals($admin->id, $verification->reviewed_by);

        // 2. User gets the verified badge (is_verified = true)
        $this->assertTrue((bool) $user->is_verified);
        $this->assertEquals('approved', $user->verification_status);

        // 3. User receives the identity verification points
        $this->assertGreaterThanOrEqual(50, $user->community_points);

        // 4. Point transaction record exists
        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $user->id,
            'action_type' => 'verified_identity',
        ]);
    }
}
