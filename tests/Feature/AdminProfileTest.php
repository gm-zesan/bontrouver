<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\City;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        Province::create([
            'name'         => 'Quebec',
            'code'         => 'QC',
            'slug'         => 'quebec',
            'country_code' => 'CA',
            'is_active'    => true,
            'sort_order'   => 1,
        ]);
    }

    public function test_admin_can_view_profile_page(): void
    {
        $admin = User::factory()->create([
            'role'     => UserRole::ADMIN,
            'name'     => 'System Admin',
            'email'    => 'admin@bontrouver.ca',
            'phone'    => '514-555-0100',
            'city'     => 'Montreal',
            'province' => 'QC',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.profile.index'));

        $response->assertOk();
        $response->assertSee('Administrator Profile & Security', false);
        $response->assertSee('System Admin');
        $response->assertSee('admin@bontrouver.ca');
    }

    public function test_non_admin_cannot_view_admin_profile_page(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::USER,
        ]);

        $response = $this->actingAs($user)->get(route('admin.profile.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.profile.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_update_profile_info(): void
    {
        $admin = User::factory()->create([
            'role'  => UserRole::ADMIN,
            'name'  => 'Old Admin Name',
            'email' => 'oldadmin@bontrouver.ca',
        ]);

        $avatar = UploadedFile::fake()->image('admin_avatar.png', 200, 200);

        $response = $this->actingAs($admin)->putJson(route('admin.profile.updateInfo'), [
            'name'     => 'New Admin Master',
            'email'    => 'newadmin@bontrouver.ca',
            'phone'    => '+1 (514) 555-9999',
            'city'     => 'Montreal',
            'province' => 'QC',
            'bio'      => 'Senior Administrator and Platform Manager',
            'avatar'   => $avatar,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'user'    => [
                'name'  => 'New Admin Master',
                'email' => 'newadmin@bontrouver.ca',
            ],
        ]);

        $admin->refresh();
        $this->assertEquals('New Admin Master', $admin->name);
        $this->assertEquals('newadmin@bontrouver.ca', $admin->email);
        $this->assertEquals('+1 (514) 555-9999', $admin->phone);
        $this->assertEquals('Montreal', $admin->city);
        $this->assertEquals('QC', $admin->province);
        $this->assertEquals('Senior Administrator and Platform Manager', $admin->bio);
        $this->assertNotNull($admin->avatar);
        Storage::disk('public')->assertExists($admin->avatar);
    }

    public function test_admin_can_change_password_with_valid_current_password(): void
    {
        $admin = User::factory()->create([
            'role'     => UserRole::ADMIN,
            'password' => Hash::make('CurrentSecret123!'),
        ]);

        $response = $this->actingAs($admin)->putJson(route('admin.profile.updatePassword'), [
            'current_password'      => 'CurrentSecret123!',
            'password'              => 'BrandNewSecretPass456!',
            'password_confirmation' => 'BrandNewSecretPass456!',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $admin->refresh();
        $this->assertTrue(Hash::check('BrandNewSecretPass456!', $admin->password));
    }

    public function test_admin_password_change_fails_with_wrong_current_password(): void
    {
        $admin = User::factory()->create([
            'role'     => UserRole::ADMIN,
            'password' => Hash::make('CurrentSecret123!'),
        ]);

        $response = $this->actingAs($admin)->putJson(route('admin.profile.updatePassword'), [
            'current_password'      => 'WrongPassword!',
            'password'              => 'BrandNewSecretPass456!',
            'password_confirmation' => 'BrandNewSecretPass456!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['current_password']);
    }

    public function test_frontend_header_shows_simplified_dropdown_for_admin(): void
    {
        $admin = User::factory()->create([
            'role'  => UserRole::ADMIN,
            'name'  => 'Super Admin',
            'email' => 'superadmin@bontrouver.ca',
        ]);

        $response = $this->actingAs($admin)->get(route('home'));

        $response->assertOk();
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Logout');
        $response->assertDontSee('Account Settings');
        $response->assertDontSee('My Listings');
    }

    public function test_frontend_header_shows_full_dropdown_for_regular_user(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::USER,
            'name' => 'Regular Buyer',
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('Account Settings');
        $response->assertSee('My Listings');
        $response->assertSee('Smart Alerts');
        $response->assertSee('My Points');
        $response->assertDontSee('Admin Dashboard');
    }
}
