<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\City;
use App\Models\Province;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminPlatformControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $province = Province::create([
            'name_en'      => 'Quebec',
            'name_fr'      => 'Québec',
            'code'         => 'QC',
            'slug'         => 'quebec',
            'country_code' => 'CA',
            'is_active'    => true,
            'sort_order'   => 1,
        ]);

        City::create([
            'province_id' => $province->id,
            'name_en'     => 'Montreal',
            'name_fr'     => 'Montréal',
            'slug'        => 'montreal',
            'latitude'    => 45.5017,
            'longitude'   => -73.5673,
            'is_active'   => true,
        ]);

        $this->superAdmin = User::factory()->create([
            'name'     => 'Super Administrator',
            'email'    => 'superadmin@bontrouver.ca',
            'role'     => UserRole::SUPERADMIN,
            'city'     => 'Montreal',
            'province' => 'QC',
        ]);

        $this->admin = User::factory()->create([
            'name'     => 'Regular Administrator',
            'email'    => 'admin@bontrouver.ca',
            'role'     => UserRole::ADMIN,
            'city'     => 'Montreal',
            'province' => 'QC',
        ]);

        $this->regularUser = User::factory()->create([
            'name'     => 'Standard Member',
            'email'    => 'member@bontrouver.ca',
            'role'     => UserRole::USER,
            'city'     => 'Montreal',
            'province' => 'QC',
        ]);
    }

    protected function tearDown(): void
    {
        SiteSetting::set('platform_lockdown_status', 'active');
        SiteSetting::flushCache();
        parent::tearDown();
    }

    public function test_superadmin_can_access_platform_control_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.platform-control.index'));

        $response->assertOk();
        $response->assertSee('Platform Control', false);
        $response->assertSee('Select Website Operational Mode', false);
    }

    public function test_regular_admin_cannot_access_platform_control_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.platform-control.index'));

        $response->assertForbidden();
    }

    public function test_regular_user_cannot_access_platform_control_page(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.platform-control.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_platform_control(): void
    {
        $response = $this->get(route('admin.platform-control.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_admin_cannot_see_superadmin_in_user_management(): void
    {
        // 1. DataTables user list should not include superadmin for regular admin
        $response = $this->actingAs($this->admin)->getJson(route('admin.users.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertDontSee('superadmin@bontrouver.ca');
        $response->assertSee('admin@bontrouver.ca');
        $response->assertSee('member@bontrouver.ca');

        // 2. Direct inspection of superadmin by regular admin must return 404
        $viewResponse = $this->actingAs($this->admin)->get(route('admin.users.show', $this->superAdmin));
        $viewResponse->assertNotFound();

        // 3. Attempt to suspend superadmin by regular admin must return 404
        $suspendResponse = $this->actingAs($this->admin)->post(route('admin.users.suspend', $this->superAdmin));
        $suspendResponse->assertNotFound();

        // 4. Attempt to change superadmin role by regular admin must return 404
        $roleResponse = $this->actingAs($this->admin)->post(route('admin.users.assignRole'), [
            'email' => $this->superAdmin->email,
            'role'  => UserRole::USER->value,
        ]);
        $roleResponse->assertNotFound();
    }

    public function test_superadmin_can_update_platform_mode_to_image_splash(): void
    {
        $image = UploadedFile::fake()->image('under_maintenance.jpg', 1920, 1080);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.platform-control.update'), [
            'status'     => 'image_splash',
            'title'      => 'System Scheduled Maintenance',
            'message'    => 'We are upgrading our servers. Please check back shortly.',
            'bypass_key' => 'secret_super_key_2026',
            'image'      => $image,
        ]);

        $response->assertRedirect(route('admin.platform-control.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('image_splash', SiteSetting::get('platform_lockdown_status'));
        $this->assertEquals('System Scheduled Maintenance', SiteSetting::get('platform_lockdown_title'));
        $this->assertEquals('secret_super_key_2026', SiteSetting::get('platform_lockdown_bypass_key'));
        $this->assertNotNull(SiteSetting::get('platform_lockdown_image'));
    }

    public function test_public_routes_return_503_when_platform_is_locked_down(): void
    {
        SiteSetting::set('platform_lockdown_status', 'image_splash');
        SiteSetting::set('platform_lockdown_title', 'Under Maintenance');
        SiteSetting::set('platform_lockdown_message', 'Site is currently offline.');

        $response = $this->get(route('home'));

        $response->assertStatus(503);
        $response->assertSee('Under Maintenance');
        $response->assertSee('Site is currently offline.');
    }

    public function test_admin_and_login_routes_remain_accessible_during_lockdown(): void
    {
        SiteSetting::set('platform_lockdown_status', 'image_splash');

        // Login page remains accessible
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertOk();

        // SuperAdmin can access admin panel during lockdown
        $adminResponse = $this->actingAs($this->superAdmin)->get(route('admin.dashboard'));
        $adminResponse->assertOk();
    }

    public function test_bypass_key_parameter_allows_frontend_access_during_lockdown(): void
    {
        SiteSetting::set('platform_lockdown_status', 'image_splash');
        SiteSetting::set('platform_lockdown_bypass_key', 'my_special_secret_key');

        // Request without key gives 503
        $deniedResponse = $this->get(route('home'));
        $deniedResponse->assertStatus(503);

        // Request with valid ?bt_master_key parameter grants access and sets cookie
        $allowedResponse = $this->get(route('home') . '?bt_master_key=my_special_secret_key');
        $allowedResponse->assertOk();
        $allowedResponse->assertPlainCookie('bt_master_bypass', '1');

        // Subsequent requests with the bypass cookie also bypass lockdown
        $cookieResponse = $this->withUnencryptedCookie('bt_master_bypass', '1')
            ->get(route('home'));
        $cookieResponse->assertOk();
    }
}
