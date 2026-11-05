<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use App\Models\UserVerification;
use App\Models\Province;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_with_all_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        
        $category = Category::create(['name_en' => 'Automotive', 'name_fr' => 'Automobile', 'slug' => 'automotive']);
        $province = Province::create(['name_en' => 'Quebec', 'name_fr' => 'Québec', 'slug' => 'quebec', 'code' => 'QC', 'country_code' => 'CA']);
        $city = City::create(['name_en' => 'Montreal', 'name_fr' => 'Montréal', 'slug' => 'montreal', 'province_id' => $province->id, 'latitude' => 45.5017, 'longitude' => -73.5673]);

        Listing::create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'city_id'     => $city->id,
            'title'       => '2022 Honda Civic Sedan',
            'slug'        => '2022-honda-civic-sedan',
            'description' => 'Clean title, well maintained',
            'price'       => 24000,
            'price_type'  => 'fixed',
            'status'      => 'active',
            'city'        => 'Montreal',
            'province'    => 'QC',
            'latitude'    => 45.5017,
            'longitude'   => -73.5673,
        ]);

        UserVerification::create([
            'user_id'       => $user->id,
            'document_type' => 'drivers_license',
            'document_path' => 'verifications/test_license.jpg',
            'status'        => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard')
            ->assertViewHas('today')
            ->assertViewHas('lifecycle')
            ->assertViewHas('kpi')
            ->assertViewHas('timeseries')
            ->assertViewHas('category_breakdown')
            ->assertViewHas('top_cities')
            ->assertViewHas('recent_verifications')
            ->assertViewHas('recent_listings')
            ->assertSee('Executive Command Center')
            ->assertSee('2022 Honda Civic Sedan');
    }

    public function test_admin_can_request_dashboard_json(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->getJson(route('admin.dashboard'));

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'today' => [
                        'listings_created',
                        'users_joined',
                        'meetups_created',
                        'verifications_submitted',
                    ],
                    'lifecycle' => [
                        'listings',
                        'users',
                        'verifications',
                        'meetups',
                        'reports',
                    ],
                    'kpi' => [
                        'total_users',
                        'active_listings',
                        'pending_verifications',
                        'circulating_points',
                    ],
                    'timeseries' => [
                        'labels',
                        'listings',
                        'users',
                        'points_earned',
                        'points_spent',
                    ],
                ],
            ]);
    }

    public function test_superadmin_is_completely_invisible_in_dashboard_metrics_for_regular_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $regularUser = User::factory()->create(['role' => UserRole::USER]);

        // Regular admin checks dashboard
        $response = $this->actingAs($admin)->getJson(route('admin.dashboard'));
        $response->assertOk();
        
        $data = $response->json('data');
        // Total users must be 2 (admin + regularUser), NOT 3
        $this->assertEquals(2, $data['lifecycle']['users']['total']);
        $this->assertEquals(2, $data['today']['users_joined']);

        // Superadmin checks dashboard
        $superResponse = $this->actingAs($superAdmin)->getJson(route('admin.dashboard'));
        $superResponse->assertOk();
        $superData = $superResponse->json('data');
        // Superadmin sees all 3
        $this->assertEquals(3, $superData['lifecycle']['users']['total']);
        $this->assertEquals(3, $superData['today']['users_joined']);
    }
}
