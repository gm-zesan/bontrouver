<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@bontrouver.ca',
        ]);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'email' => 'user@bontrouver.ca',
        ]);
    }

    public function test_guest_cannot_access_categories_management(): void
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_categories_management(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.categories.index'));
        $response->assertForbidden();
    }

    public function test_guest_cannot_store_category(): void
    {
        $response = $this->post(route('admin.categories.store'), [
            'name_en' => 'Unauthorized Category',
            'slug' => 'unauthorized-category',
        ]);
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_store_category(): void
    {
        $response = $this->actingAs($this->regularUser)->post(route('admin.categories.store'), [
            'name_en' => 'Unauthorized Category',
            'slug' => 'unauthorized-category',
        ]);
        $response->assertForbidden();
    }

    public function test_admin_can_view_category_index_page(): void
    {
        $parent = Category::create([
            'name_en' => 'Vehicles & Automotive',
            'name_fr' => 'Véhicules et automobile',
            'slug' => 'vehicles',
            'icon' => 'ri-car-line',
            'is_active' => true,
        ]);

        $child = Category::create([
            'name_en' => 'Cars & Trucks',
            'name_fr' => 'Voitures et camions',
            'slug' => 'cars-trucks',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Categories Management');
        $response->assertSee('Vehicles', false);
    }

    public function test_admin_can_fetch_category_datatables_json(): void
    {
        $parent = Category::create([
            'name_en' => 'Real Estate',
            'name_fr' => 'Immobilier',
            'slug' => 'real-estate',
            'is_active' => true,
        ]);

        $child = Category::create([
            'name_en' => 'Apartments for Rent',
            'name_fr' => 'Appartements à louer',
            'slug' => 'apartments-for-rent',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.categories.index'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);
        $this->assertGreaterThanOrEqual(2, $response->json('recordsTotal'));
    }

    public function test_admin_can_create_root_category(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name_en' => 'Heavy Equipment',
            'name_fr' => 'Équipement lourd',
            'slug' => 'heavy-equipment',
            'icon' => 'ri-truck-line',
            'description_en' => 'Tractors, bulldozers, and industrial equipment.',
            'description_fr' => 'Tracteurs, bulldozers et équipement industriel.',
            'sort_order' => 5,
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name_en' => 'Heavy Equipment',
            'name_fr' => 'Équipement lourd',
            'slug' => 'heavy-equipment',
            'parent_id' => null,
            'sort_order' => 5,
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_create_sub_category(): void
    {
        $parent = Category::create([
            'name_en' => 'Electronics',
            'name_fr' => 'Électronique',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name_en' => 'Smartphones & Tablets',
            'name_fr' => 'Smartphones et tablettes',
            'slug' => 'smartphones-tablets',
            'parent_id' => $parent->id,
            'icon' => 'ri-smartphone-line',
            'sort_order' => 1,
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name_en' => 'Smartphones & Tablets',
            'name_fr' => 'Smartphones et tablettes',
            'slug' => 'smartphones-tablets',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::create([
            'name_en' => 'Motorcycles',
            'name_fr' => 'Motos',
            'slug' => 'motorcycles',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category->id), [
            'name_en' => 'Motorcycles & ATVs',
            'name_fr' => 'Motos et VTT',
            'slug' => 'motorcycles-atvs',
            'sort_order' => 3,
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name_en' => 'Motorcycles & ATVs',
            'name_fr' => 'Motos et VTT',
            'slug' => 'motorcycles-atvs',
            'sort_order' => 3,
        ]);
    }

    public function test_admin_can_toggle_category_status(): void
    {
        $category = Category::create([
            'name_en' => 'Musical Instruments',
            'name_fr' => 'Instruments de musique',
            'slug' => 'musical-instruments',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.categories.toggleStatus', $category->id));

        $response->assertStatus(200);
        $response->assertJsonPath('is_active', false);
        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_admin_cannot_delete_category_with_subcategories(): void
    {
        $parent = Category::create([
            'name_en' => 'Services',
            'name_fr' => 'Services',
            'slug' => 'services',
            'is_active' => true,
        ]);

        Category::create([
            'name_en' => 'Home Renovation',
            'name_fr' => 'Rénovation domiciliaire',
            'slug' => 'home-renovation',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson(route('admin.categories.destroy', $parent->id));

        $response->assertStatus(422);
        $this->assertDatabaseHas('categories', ['id' => $parent->id]);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $category = Category::create([
            'name_en' => 'Antiques',
            'name_fr' => 'Antiquités',
            'slug' => 'antiques',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson(route('admin.categories.destroy', $category->id));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_admin_can_view_dynamic_attributes_schema_builder(): void
    {
        $category = Category::create([
            'name_en' => 'Cars & Trucks',
            'name_fr' => 'Voitures et camions',
            'slug' => 'cars-trucks',
            'is_active' => true,
        ]);

        CategoryAttribute::create([
            'category_id' => $category->id,
            'name_en' => 'Fuel Type',
            'name_fr' => 'Type de carburant',
            'slug' => 'fuel_type',
            'type' => 'select',
            'is_required' => true,
            'is_filterable' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.attributes.index', $category->id));

        $response->assertStatus(200);
        $response->assertSee('Dynamic EAV Schema Builder');
        $response->assertSee('Fuel Type');
    }

    public function test_admin_can_create_select_attribute_with_options(): void
    {
        $category = Category::create([
            'name_en' => 'Cars & Trucks',
            'name_fr' => 'Voitures et camions',
            'slug' => 'cars-trucks',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.categories.attributes.store', $category->id), [
            'category_id' => $category->id,
            'name_en' => 'Transmission',
            'name_fr' => 'Transmission',
            'slug' => 'transmission',
            'type' => 'select',
            'is_required' => 1,
            'is_filterable' => 1,
            'is_active' => 1,
            'options' => ['Automatic', 'Manual', 'CVT'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('category_attributes', [
            'category_id' => $category->id,
            'name_en' => 'Transmission',
            'name_fr' => 'Transmission',
            'slug' => 'transmission',
            'type' => 'select',
        ]);

        $this->assertDatabaseHas('attribute_options', ['label_en' => 'Automatic']);
        $this->assertDatabaseHas('attribute_options', ['label_en' => 'Manual']);
        $this->assertDatabaseHas('attribute_options', ['label_en' => 'CVT']);
    }

    public function test_admin_can_create_text_or_number_attribute(): void
    {
        $category = Category::create([
            'name_en' => 'Apartments for Rent',
            'name_fr' => 'Appartements à louer',
            'slug' => 'apartments-rent',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.categories.attributes.store', $category->id), [
            'category_id' => $category->id,
            'name_en' => 'Square Footage',
            'name_fr' => 'Superficie en pieds carrés',
            'slug' => 'square_footage',
            'type' => 'number',
            'is_required' => 0,
            'is_filterable' => 1,
            'is_active' => 1,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('category_attributes', [
            'category_id' => $category->id,
            'name_en' => 'Square Footage',
            'type' => 'number',
        ]);
    }

    public function test_admin_can_update_attribute_and_options(): void
    {
        $category = Category::create([
            'name_en' => 'Computers',
            'name_fr' => 'Ordinateurs',
            'slug' => 'computers',
            'is_active' => true,
        ]);

        $attr = CategoryAttribute::create([
            'category_id' => $category->id,
            'name_en' => 'Storage',
            'name_fr' => 'Stockage',
            'slug' => 'storage',
            'type' => 'select',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->putJson(route('admin.categories.attributes.update', [$category->id, $attr->id]), [
            'name_en' => 'Storage Capacity',
            'name_fr' => 'Capacité de stockage',
            'slug' => 'storage_capacity',
            'type' => 'select',
            'is_required' => 1,
            'is_filterable' => 1,
            'is_active' => 1,
            'options' => ['256GB SSD', '512GB SSD', '1TB SSD', '2TB SSD'],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('category_attributes', [
            'id' => $attr->id,
            'name_en' => 'Storage Capacity',
            'name_fr' => 'Capacité de stockage',
            'slug' => 'storage_capacity',
        ]);
        $this->assertDatabaseHas('attribute_options', ['label_en' => '1TB SSD']);
    }

    public function test_admin_can_delete_attribute(): void
    {
        $category = Category::create([
            'name_en' => 'Bicycles',
            'name_fr' => 'Vélos',
            'slug' => 'bicycles',
            'is_active' => true,
        ]);

        $attr = CategoryAttribute::create([
            'category_id' => $category->id,
            'name_en' => 'Frame Size',
            'name_fr' => 'Taille du cadre',
            'slug' => 'frame_size',
            'type' => 'select',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson(route('admin.categories.attributes.destroy', [$category->id, $attr->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('category_attributes', ['id' => $attr->id]);
    }
}
