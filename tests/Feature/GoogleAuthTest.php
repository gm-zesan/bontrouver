<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_google_redirect_shows_configuration_notice_when_keys_missing(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $response = $this->get(route('auth.google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google']);
    }

    public function test_google_redirect_initiates_oauth_when_configured(): void
    {
        config([
            'services.google.client_id' => 'fake-client-id',
            'services.google.client_secret' => 'fake-client-secret',
        ]);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('redirect')->twice()->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google'));
        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');

        // Test route name matching mess-management
        $response2 = $this->get(route('auth.google.redirect'));
        $response2->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_google_callback_handles_user_cancellation(): void
    {
        $response = $this->get(route('auth.google.callback', [
            'error' => 'access_denied',
            'error_description' => 'User denied access',
        ]));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google']);
        $this->assertGuest();
    }

    public function test_google_callback_registers_new_user_and_logs_in(): void
    {
        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google-id-12345';
        $socialiteUser->name = 'Marie Dubois';
        $socialiteUser->email = 'marie.dubois@gmail.com';
        $socialiteUser->avatar = 'https://lh3.googleusercontent.com/a/avatar123';

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::where('email', 'marie.dubois@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google-id-12345', $user->google_id);
        $this->assertEquals('Marie Dubois', $user->name);
        $this->assertEquals(UserRole::USER, $user->role);
        $this->assertFalse($user->is_dealer);
        $this->assertEquals(50, $user->community_points);
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('https://lh3.googleusercontent.com/a/avatar123', $user->avatar);
        $this->assertNotNull($user->profile);
    }

    public function test_google_callback_logs_in_existing_user_by_google_id(): void
    {
        $existing = User::factory()->create([
            'email' => 'alex@example.com',
            'google_id' => 'google-existing-999',
            'name' => 'Alexandre Cote',
            'community_points' => 120,
        ]);

        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google-existing-999';
        $socialiteUser->name = 'Alexandre Cote';
        $socialiteUser->email = 'alex@example.com';
        $socialiteUser->avatar = 'https://lh3.googleusercontent.com/a/new-avatar';

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($existing);
        $this->assertEquals(120, $existing->fresh()->community_points);
    }

    public function test_google_callback_links_google_id_to_existing_email_account(): void
    {
        $existing = User::factory()->create([
            'email' => 'sarah@example.ca',
            'google_id' => null,
            'name' => 'Sarah Tremblay',
        ]);

        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google-linked-777';
        $socialiteUser->name = 'Sarah Tremblay';
        $socialiteUser->email = 'sarah@example.ca';
        $socialiteUser->avatar = 'https://lh3.googleusercontent.com/a/sarah-pic';

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($existing);
        $this->assertEquals('google-linked-777', $existing->fresh()->google_id);
    }

    public function test_merchant_account_can_login_via_google_and_maintains_dealer_status_and_profile(): void
    {
        $merchant = User::factory()->create([
            'email' => 'dealer@automontreal.ca',
            'name' => 'Montreal Auto Brokers',
            'is_dealer' => true,
            'is_verified' => true,
            'google_id' => null,
        ]);

        $merchant->profile()->create([
            'about_text' => 'Premier certified used vehicles dealership in Montreal.',
            'website_url' => 'https://automontreal.ca',
            'features' => ['OMVIC Certified', 'Financing Available'],
        ]);

        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google-merchant-101';
        $socialiteUser->name = 'Montreal Auto Brokers';
        $socialiteUser->email = 'dealer@automontreal.ca';
        $socialiteUser->avatar = 'https://lh3.googleusercontent.com/a/dealer-logo';

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($merchant);

        $fresh = $merchant->fresh();
        // 1. Google ID is safely linked
        $this->assertEquals('google-merchant-101', $fresh->google_id);
        // 2. Dealer status is 100% maintained
        $this->assertTrue($fresh->is_dealer);
        $this->assertTrue($fresh->is_verified);
        // 3. Merchant profile is untouched
        $this->assertEquals('Premier certified used vehicles dealership in Montreal.', $fresh->profile->about_text);
        $this->assertEquals('https://automontreal.ca', $fresh->profile->website_url);
    }

    public function test_suspended_user_is_prevented_from_logging_in_via_google(): void
    {
        $suspended = User::factory()->create([
            'email' => 'badactor@example.com',
            'google_id' => 'google-bad-actor',
            'is_suspended' => true,
        ]);

        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google-bad-actor';
        $socialiteUser->name = 'Bad Actor';
        $socialiteUser->email = 'badactor@example.com';

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_admin_is_redirected_to_admin_dashboard_upon_google_login(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@bontrouver.ca',
            'role' => UserRole::ADMIN,
            'google_id' => 'google-admin-1',
        ]);

        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = 'google-admin-1';
        $socialiteUser->name = 'Admin User';
        $socialiteUser->email = 'admin@bontrouver.ca';

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }
}
