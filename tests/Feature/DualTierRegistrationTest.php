<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DualTierRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_registration_screen_renders_successfully(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Create Personal Account');
        $response->assertSee(route('register.merchant'));
    }

    public function test_merchant_registration_screen_renders_successfully(): void
    {
        $response = $this->get('/register/merchant');

        $response->assertStatus(200);
        $response->assertSee('Merchant & Dealer Registration', false);
        $response->assertSee(route('register'));
        $response->assertSee('License Number / OMVIC / Tax ID');
    }

    public function test_general_user_can_register_with_minimal_data(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Sarah Tremblay',
            'email'                 => 'sarah.tremblay@example.ca',
            'password'              => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'city'                  => 'Montreal',
            'province'              => 'QC',
            'terms'                 => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('profile.edit', absolute: false));

        $this->assertDatabaseHas('users', [
            'name'        => 'Sarah Tremblay',
            'email'       => 'sarah.tremblay@example.ca',
            'city'        => 'Montreal',
            'province'    => 'QC',
            'is_dealer'   => false,
            'is_verified' => false,
        ]);
    }

    public function test_general_user_registration_requires_mandatory_fields(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'terms']);
    }

    public function test_general_user_registration_validates_email_uniqueness(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->post('/register', [
            'name'                  => 'Another User',
            'email'                 => 'duplicate@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms'                 => '1',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_merchant_can_register_with_comprehensive_business_data(): void
    {
        $file = UploadedFile::fake()->create('omvic_permit.pdf', 1024, 'application/pdf');

        $response = $this->post('/register/merchant', [
            'business_name'         => 'Mont-Royal Motors Ltd.',
            'contact_name'          => 'Jean-Philippe Roy',
            'email'                 => 'dealer@montroyalmotors.ca',
            'password'              => 'DealerPass2026!',
            'password_confirmation' => 'DealerPass2026!',
            'phone'                 => '+1 514-555-8899',
            'city'                  => 'Montreal',
            'province'              => 'QC',
            'address'               => '5400 Boulevard Saint-Laurent',
            'postal_code'           => 'H2T 1S1',
            'business_category'     => 'Automotive Dealership',
            'dealer_license_number' => 'AMVOQ-778899',
            'website_url'           => 'https://montroyalmotors.ca',
            'business_bio'          => 'Trusted certified pre-owned dealership in Montreal.',
            'operating_hours'       => [
                'Monday' => '9:00 AM - 6:00 PM',
                'Friday' => '9:00 AM - 5:00 PM',
            ],
            'license_document'      => $file,
            'terms'                 => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('profile.edit', absolute: false));

        // 1. Verify User Record
        $this->assertDatabaseHas('users', [
            'name'        => 'Mont-Royal Motors Ltd.',
            'email'       => 'dealer@montroyalmotors.ca',
            'phone'       => '+1 514-555-8899',
            'city'        => 'Montreal',
            'province'    => 'QC',
            'postal_code' => 'H2T 1S1',
            'location'    => '5400 Boulevard Saint-Laurent',
            'is_dealer'   => true,
            'is_verified' => false,
        ]);

        $user = User::where('email', 'dealer@montroyalmotors.ca')->first();
        $this->assertNotNull($user);

        // 2. Verify Commercial Profile
        $this->assertDatabaseHas('user_profiles', [
            'user_id'     => $user->id,
            'website_url' => 'https://montroyalmotors.ca',
        ]);

        $profile = UserProfile::where('user_id', $user->id)->first();
        $this->assertEquals('Automotive Dealership', $profile->features['dealer_category']);
        $this->assertEquals('Jean-Philippe Roy', $profile->features['contact_person']);
        $this->assertEquals('9:00 AM - 6:00 PM', $profile->operating_hours['Monday']);

        // 3. Verify Verification Record and File
        $this->assertDatabaseHas('user_verifications', [
            'user_id'       => $user->id,
            'document_type' => 'dealer_license',
            'id_number'     => 'AMVOQ-778899',
            'status'        => 'pending',
        ]);

        $verification = UserVerification::where('user_id', $user->id)->first();
        $this->assertNotNull($verification->document_path);
        $cleanPath = str_replace('/storage/', '', $verification->document_path);
        Storage::disk('public')->assertExists($cleanPath);
    }

    public function test_merchant_registration_requires_business_license_and_storefront_address(): void
    {
        $response = $this->post('/register/merchant', [
            'business_name' => 'Incomplete Motors',
            'contact_name'  => 'Test Contact',
            'email'         => 'incomplete@motors.ca',
            'password'      => 'Password123!',
            'terms'         => '1',
        ]);

        $response->assertSessionHasErrors([
            'phone',
            'city',
            'province',
            'address',
            'business_category',
            'dealer_license_number',
        ]);
    }

    public function test_merchant_profile_displays_storefront_details_and_operating_hours_in_view_mode(): void
    {
        $merchant = User::factory()->create([
            'name'        => 'Champlain RV & Marine Ltd.',
            'email'       => 'info@champlainrv.ca',
            'phone'       => '+1 450-555-4321',
            'city'        => 'Saint-Jean-sur-Richelieu',
            'province'    => 'QC',
            'postal_code' => 'J3B 2C9',
            'location'    => '880 Rue Saint-Jacques',
            'is_dealer'   => true,
            'bio'         => 'Premier recreational vehicle dealer in southern Quebec.',
        ]);

        UserProfile::create([
            'user_id'         => $merchant->id,
            'website_url'     => 'https://champlainrv.ca',
            'about_text'      => 'Serving RV enthusiasts with certified sales and service.',
            'operating_hours' => [
                'Monday' => '8:30 AM - 5:30 PM',
                'Friday' => '8:30 AM - 6:00 PM',
                'Sunday' => 'Closed',
            ],
            'features'        => [
                'dealer_category' => 'RV & Boat Dealership',
                'contact_person'  => 'Marc-Andre Tremblay',
                'account_type'    => 'merchant',
            ],
        ]);

        $response = $this->get('/user/' . $merchant->id);

        $response->assertStatus(200);
        $response->assertSee('Champlain RV & Marine Ltd.');
        $response->assertSee('Certified Dealer');
        $response->assertSee('RV & Boat Dealership');
        $response->assertSee('880 Rue Saint-Jacques');
        $response->assertSee('J3B 2C9');
        $response->assertSee('+1 450-555-4321');
        $response->assertSee('https://champlainrv.ca');
        $response->assertSee('Marc-Andre Tremblay');
        $response->assertSee('Store Operating Hours');
        $response->assertSee('8:30 AM - 5:30 PM');
        $response->assertSee('Monday');
    }

    public function test_general_user_profile_renders_cleanly_without_empty_storefront_cards(): void
    {
        $user = User::factory()->create([
            'name'        => 'Alex Bouchard',
            'email'       => 'alex.bouchard@example.ca',
            'city'        => 'Quebec City',
            'province'    => 'QC',
            'is_dealer'   => false,
        ]);

        $response = $this->get('/user/' . $user->id);

        $response->assertStatus(200);
        $response->assertSee('Alex Bouchard');
        $response->assertDontSee('Storefront & Dealer Info');
        $response->assertDontSee('Store Operating Hours');
        $response->assertDontSee('Certified Dealer');
    }

    public function test_authenticated_merchant_views_own_profile_with_storefront_details(): void
    {
        $merchant = User::factory()->create([
            'name'        => 'Laurentian Marine Ltd.',
            'email'       => 'sales@laurentianmarine.ca',
            'phone'       => '+1 450-555-9988',
            'city'        => 'Saint-Jerome',
            'province'    => 'QC',
            'postal_code' => 'J7Z 5T3',
            'location'    => '1200 Boulevard de la Salette',
            'is_dealer'   => true,
        ]);

        UserProfile::create([
            'user_id'         => $merchant->id,
            'website_url'     => 'https://laurentianmarine.ca',
            'about_text'      => 'Full-service boat dealership and marine equipment.',
            'operating_hours' => [
                'Tuesday'  => '9:00 AM - 5:00 PM',
                'Thursday' => '9:00 AM - 5:00 PM',
            ],
            'features'        => [
                'dealer_category' => 'Marine Dealership',
                'contact_person'  => 'Luc Beaudoin',
                'account_type'    => 'merchant',
            ],
        ]);

        $response = $this->actingAs($merchant)->get(route('profile.view'));

        $response->assertStatus(200);
        $response->assertSee('Laurentian Marine Ltd.');
        $response->assertSee('Marine Dealership');
        $response->assertSee('Storefront & Dealer Info');
        $response->assertSee('1200 Boulevard de la Salette');
        $response->assertSee('Store Operating Hours');
        $response->assertSee('Tuesday');
        $response->assertSee('Edit Profile');
        $response->assertSee('Public View');
    }

    public function test_authenticated_merchant_can_preview_public_view_mode_with_owner_banner(): void
    {
        $merchant = User::factory()->create([
            'name'        => 'Gatineau Motors Inc.',
            'email'       => 'contact@gatineaumotors.ca',
            'is_dealer'   => true,
        ]);

        $response = $this->actingAs($merchant)->get('/user/' . $merchant->id);

        $response->assertStatus(200);
        $response->assertSee('Gatineau Motors Inc.');
        $response->assertSee('Public View Mode');
        $response->assertSee('Dashboard View');
    }
}


