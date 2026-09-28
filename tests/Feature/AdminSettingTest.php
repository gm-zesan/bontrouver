<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Province;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\ListingService;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingTest extends TestCase
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

        $this->seed(SiteSettingSeeder::class);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertOk();
        $response->assertSee('Platform &amp; Site Settings', false);
        $response->assertSee('General &amp; Identity', false);
        $response->assertSee('Branding Assets', false);
        $response->assertSee('Canadian SEO &amp; Social', false);
        $response->assertSee('Marketplace Rules', false);
        $response->assertDontSee('Maintenance Mode', false);
        $response->assertDontSee('Localization (Canada)', false);
    }

    public function test_non_admin_cannot_view_settings_page(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::USER,
        ]);

        $response = $this->actingAs($user)->get(route('admin.settings.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.settings.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_update_general_settings(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->putJson(route('admin.settings.update'), [
            'group'            => 'general',
            'site_name'        => 'Bon Trouver Canada',
            'site_tagline'     => 'Canada\'s Premier Local Classifieds & Mutual Aid Hub',
            'contact_email'    => 'hello@bontrouver.ca',
            'contact_phone'    => '+1 (800) 123-4567',
            'contact_address'  => 'Montreal, QC, Canada',
            'footer_copyright' => '© 2026 Bon Trouver Media',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'group'   => 'general',
        ]);

        $this->assertEquals('Bon Trouver Canada', site_setting('site_name'));
        $this->assertEquals('hello@bontrouver.ca', site_setting('contact_email'));
        $this->assertEquals('© 2026 Bon Trouver Media', site_setting('footer_copyright'));
    }

    public function test_admin_can_upload_branding_assets(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $lightLogo = UploadedFile::fake()->image('logo_light.png', 300, 100);
        $darkLogo = UploadedFile::fake()->image('logo_dark.png', 300, 100);
        $favicon = UploadedFile::fake()->image('favicon.png', 32, 32);

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'group'            => 'branding',
            'site_logo_light'  => $lightLogo,
            'site_logo_dark'   => $darkLogo,
            'site_favicon'     => $favicon,
        ]);

        $response->assertRedirect(route('admin.settings.index', ['tab' => 'branding']));

        $storedLightLogo = site_setting('site_logo_light');
        $this->assertNotNull($storedLightLogo);
        Storage::disk('public')->assertExists($storedLightLogo);

        $storedDarkLogo = site_setting('site_logo_dark');
        $this->assertNotNull($storedDarkLogo);
        Storage::disk('public')->assertExists($storedDarkLogo);

        $storedFavicon = site_setting('site_favicon');
        $this->assertNotNull($storedFavicon);
        Storage::disk('public')->assertExists($storedFavicon);
    }

    public function test_admin_can_update_canadian_seo_settings(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->putJson(route('admin.settings.update'), [
            'group'            => 'seo',
            'meta_title'       => 'Bon Trouver — Canadian Classifieds & Local Marketplace',
            'meta_description' => 'Find rentals, cars, electronics, and local meetups across Montreal, Toronto, Vancouver, and all Canadian cities.',
            'meta_keywords'    => 'canadian classifieds, montreal rentals, toronto jobs, vancouver cars, canada mutual aid',
            'geo_region'       => 'CA-QC',
            'geo_placename'    => 'Montreal, Quebec, Canada',
            'geo_position'     => '45.5017;-73.5673',
            'social_twitter'   => 'https://twitter.com/bontrouver_ca',
            'social_facebook'  => 'https://facebook.com/bontrouver_ca',
            'social_instagram' => 'https://instagram.com/bontrouver_ca',
            'social_linkedin'  => 'https://linkedin.com/company/bontrouver-ca',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertEquals('Bon Trouver — Canadian Classifieds & Local Marketplace', site_setting('meta_title'));
        $this->assertEquals('CA-QC', site_setting('geo_region'));
        $this->assertEquals('Montreal, Quebec, Canada', site_setting('geo_placename'));
        $this->assertEquals('https://twitter.com/bontrouver_ca', site_setting('social_twitter'));
    }

    public function test_admin_can_update_marketplace_rules(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->putJson(route('admin.settings.update'), [
            'group'                  => 'marketplace',
            'auto_approve_listings'  => 0,
            'max_images_per_listing' => 15,
            'max_upload_size_mb'     => 20,
            'listing_expiry_days'    => 90,
            'free_listings_limit'    => 100,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertFalse(site_setting('auto_approve_listings'));
        $this->assertEquals(15, site_setting('max_images_per_listing'));
        $this->assertEquals(20, site_setting('max_upload_size_mb'));
        $this->assertEquals(90, site_setting('listing_expiry_days'));
        $this->assertEquals(100, site_setting('free_listings_limit'));
    }

    public function test_frontend_renders_dynamic_site_settings(): void
    {
        SiteSetting::set('site_name', 'BONTROUVER CANADA', 'general', 'string');
        SiteSetting::set('site_tagline', 'The True North Community Classifieds', 'general', 'string');
        SiteSetting::set('geo_region', 'CA', 'seo', 'string');
        SiteSetting::set('geo_placename', 'Canada', 'seo', 'string');
        SiteSetting::set('footer_copyright', '© 2026 Custom Copyright Notice', 'general', 'string');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('The True North Community Classifieds');
        $response->assertSee('© 2026 Custom Copyright Notice');
        $response->assertSee('<meta name="geo.region" content="CA">', false);
        $response->assertSee('<meta name="geo.placename" content="Canada">', false);
    }
}
