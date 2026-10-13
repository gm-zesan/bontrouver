<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_locations_directory(): void
    {
        $response = $this->get(route('admin.locations.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_locations_directory(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.locations.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_locations_directory(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $province = Province::create(['name_en' => 'Alberta', 'name_fr' => 'Alberta', 'slug' => 'alberta', 'code' => 'AB']);
        City::create([
            'province_id' => $province->id,
            'name_en'     => 'Calgary',
            'name_fr'     => 'Calgary',
            'slug'        => 'calgary',
            'latitude'    => 51.0447,
            'longitude'   => -114.0719,
            'is_active'   => true,
            'is_featured' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.locations.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.locations.index')
            ->assertViewHas('provinces')
            ->assertSee('Locations &amp; Cities Management', false)
            ->assertSee('Alberta');
    }

    public function test_admin_can_fetch_cities_via_datatables_ajax(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $province = Province::create(['name_en' => 'Alberta', 'name_fr' => 'Alberta', 'slug' => 'alberta', 'code' => 'AB']);
        City::create([
            'province_id' => $province->id,
            'name_en'     => 'Calgary',
            'name_fr'     => 'Calgary',
            'slug'        => 'calgary',
            'latitude'    => 51.0447,
            'longitude'   => -114.0719,
            'is_active'   => true,
            'is_featured' => true,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.locations.index', [
            'draw' => 1,
            'columns' => [
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
                ['data' => 'name', 'name' => 'name', 'orderable' => true, 'searchable' => true],
            ],
            'order' => [
                ['column' => 1, 'dir' => 'asc'],
            ],
            'search' => ['value' => 'Calg', 'regex' => false],
        ]), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data',
            ]);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_admin_can_create_new_city(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $province = Province::create(['name_en' => 'British Columbia', 'name_fr' => 'Colombie-Britannique', 'slug' => 'british-columbia', 'code' => 'BC']);

        $response = $this->actingAs($admin)->postJson(route('admin.locations.cities.store'), [
            'province_id' => $province->id,
            'name_en'     => 'Kelowna',
            'name_fr'     => 'Kelowna',
            'latitude'    => 49.8880,
            'longitude'   => -119.4960,
            'population'  => 142000,
            'is_featured' => 1,
            'is_active'   => 1,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('cities', [
            'name_en'     => 'Kelowna',
            'slug'        => 'kelowna',
            'province_id' => $province->id,
            'is_featured' => 1,
        ]);
    }

    public function test_admin_can_update_city(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $province = Province::create(['name_en' => 'Ontario', 'name_fr' => 'Ontario', 'slug' => 'ontario', 'code' => 'ON']);
        $city = City::create([
            'province_id' => $province->id,
            'name_en'     => 'Ottawa',
            'name_fr'     => 'Ottawa',
            'slug'        => 'ottawa',
            'latitude'    => 45.4215,
            'longitude'   => -75.6972,
        ]);

        $response = $this->actingAs($admin)->putJson(route('admin.locations.cities.update', $city), [
            'province_id' => $province->id,
            'name_en'     => 'City of Ottawa',
            'name_fr'     => 'Ville d\'Ottawa',
            'latitude'    => 45.4215,
            'longitude'   => -75.6972,
            'is_featured' => 1,
            'is_active'   => 1,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('cities', [
            'id'          => $city->id,
            'name_en'     => 'City of Ottawa',
            'is_featured' => 1,
        ]);
    }

    public function test_admin_can_toggle_city_active_and_featured(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $province = Province::create(['name_en' => 'Manitoba', 'name_fr' => 'Manitoba', 'slug' => 'manitoba', 'code' => 'MB']);
        $city = City::create([
            'province_id' => $province->id,
            'name_en'     => 'Winnipeg',
            'name_fr'     => 'Winnipeg',
            'slug'        => 'winnipeg',
            'latitude'    => 49.8951,
            'longitude'   => -97.1384,
            'is_active'   => true,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.locations.cities.toggleActive', $city));
        $response->assertStatus(200)
            ->assertJsonPath('is_active', false);
        $this->assertFalse($city->fresh()->is_active);

        $response = $this->actingAs($admin)->postJson(route('admin.locations.cities.toggleFeatured', $city));
        $response->assertStatus(200)
            ->assertJsonPath('is_featured', true);
        $this->assertTrue($city->fresh()->is_featured);
    }

    public function test_admin_cannot_delete_city_with_listings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name_en' => 'Electronics', 'name_fr' => 'Électronique', 'slug' => 'electronics']);
        $province = Province::create(['name_en' => 'Nova Scotia', 'name_fr' => 'Nouvelle-Écosse', 'slug' => 'nova-scotia', 'code' => 'NS']);
        $city = City::create([
            'province_id' => $province->id,
            'name_en'     => 'Halifax',
            'name_fr'     => 'Halifax',
            'slug'        => 'halifax',
            'latitude'    => 44.6488,
            'longitude'   => -63.5752,
        ]);

        Listing::create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'city_id'     => $city->id,
            'title'       => 'Sony Headphones',
            'slug'        => 'sony-headphones',
            'description' => 'Great sound quality',
            'price'       => 150,
            'price_type'  => 'fixed',
            'status'      => 'active',
            'latitude'    => 44.6488,
            'longitude'   => -63.5752,
        ]);

        $response = $this->actingAs($admin)->deleteJson(route('admin.locations.cities.destroy', $city));
        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('cities', ['id' => $city->id]);
    }

    public function test_admin_can_update_and_toggle_province(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $province = Province::create(['name_en' => 'Yukon Territory', 'name_fr' => 'Territoire du Yukon', 'slug' => 'yukon-territory', 'code' => 'YT']);

        $response = $this->actingAs($admin)->putJson(route('admin.locations.provinces.update', $province), [
            'name_en'    => 'Yukon',
            'name_fr'    => 'Yukon',
            'code'       => 'YT',
            'sort_order' => 12,
            'is_active'  => 1,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('provinces', [
            'id'      => $province->id,
            'name_en' => 'Yukon',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.locations.provinces.toggleActive', $province));
        $response->assertStatus(200)
            ->assertJsonPath('is_active', false);
        $this->assertFalse($province->fresh()->is_active);
    }
}
