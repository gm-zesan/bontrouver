<?php

namespace Tests\Feature;

use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserVerification;
use App\Services\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_settings_or_verification(): void
    {
        $response = $this->get(route('settings.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_document_verification_in_settings(): void
    {
        $user = User::factory()->create(['name' => 'Jean Charest', 'is_verified' => false]);

        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Jean Charest');
        $response->assertSee('Submit ID Document');
        $response->assertSee('Unverified');
        $response->assertSee('Document verification is');
    }

    public function test_legacy_verification_route_redirects_to_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('account.verification.index'));

        $response->assertRedirect(route('settings.index', '#verification-section'));
    }

    public function test_authenticated_user_can_submit_valid_id_document(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('drivers_license.jpg', 1200, 'image/jpeg');

        $response = $this->actingAs($user)->post(route('verification.document.store'), [
            'document_type' => 'drivers_license',
            'id_number' => 'DL-987654321',
            'phone' => '+1 (514) 555-0199',
            'document' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('user_verifications', [
            'user_id' => $user->id,
            'document_type' => 'drivers_license',
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $user->fresh()->verification_status);
    }

    public function test_submitting_invalid_file_type_fails_validation(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('script.exe', 500, 'application/octet-stream');

        $response = $this->actingAs($user)->post(route('verification.document.store'), [
            'document_type' => 'drivers_license',
            'document' => $file,
        ]);

        $response->assertSessionHasErrors(['document']);
        $this->assertDatabaseCount('user_verifications', 0);
    }

    public function test_admin_can_view_verification_queue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        UserVerification::create([
            'user_id' => $user->id,
            'document_type' => 'government_id',
            'document_path' => '/storage/verifications/ramq.jpg',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.verifications.index'));

        $response->assertStatus(200);
        $response->assertSee('ID Verification Queue');
        $response->assertSee($user->name);
    }

    public function test_admin_can_approve_verification_and_points_are_awarded_with_notification(): void
    {
        Storage::fake('public');
        Notification::fake();

        $user = User::factory()->create(['community_points' => 100, 'is_verified' => false]);
        $admin = User::factory()->create(['role' => 'admin']);

        $verification = UserVerification::create([
            'user_id' => $user->id,
            'document_type' => 'passport',
            'document_path' => '/storage/verifications/passport.jpg',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.verifications.approve', $verification->id));

        $response->assertRedirect();
        $user->refresh();
        $verification->refresh();

        $this->assertTrue($user->is_verified);
        $this->assertEquals('approved', $verification->status);
        $this->assertEquals(150, $user->community_points); // 100 + 50 points bonus
        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $user->id,
            'action_type' => 'verified_identity',
            'points' => 50,
        ]);

        Notification::assertSentTo($user, \App\Notifications\VerificationStatusUpdated::class);
    }

    public function test_admin_can_reject_verification_with_reason(): void
    {
        Storage::fake('public');
        Notification::fake();

        $user = User::factory()->create(['is_verified' => false]);
        $admin = User::factory()->create(['role' => 'admin']);

        $verification = UserVerification::create([
            'user_id' => $user->id,
            'document_type' => 'government_id',
            'document_path' => '/storage/verifications/blurry.jpg',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.verifications.reject', $verification->id), [
            'status' => 'rejected',
            'reason' => 'Image was too blurry to read name and expiry date.',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $verification->refresh();

        $this->assertFalse($user->is_verified);
        $this->assertEquals('rejected', $verification->status);
        $this->assertEquals('Image was too blurry to read name and expiry date.', $verification->rejection_reason);

        Notification::assertSentTo($user, \App\Notifications\VerificationStatusUpdated::class);
    }

    public function test_dealer_license_approval_grants_is_dealer_status(): void
    {
        Storage::fake('public');
        Notification::fake();

        $user = User::factory()->create(['is_dealer' => false, 'is_verified' => false]);
        $admin = User::factory()->create(['role' => 'admin']);

        $verification = UserVerification::create([
            'user_id' => $user->id,
            'document_type' => 'dealer_license',
            'document_path' => '/storage/verifications/omvic.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.verifications.approve', $verification->id));

        $user->refresh();
        $this->assertTrue($user->is_verified);
        $this->assertTrue($user->is_dealer);
    }
}
