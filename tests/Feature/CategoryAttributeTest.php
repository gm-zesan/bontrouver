<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryAttributeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_it_returns_dynamic_attributes_for_category_from_database(): void
    {
        $response = $this->getJson('/api/category-attributes/cars-trucks');

        $response->assertStatus(200)
            ->assertJson([
                'success'       => true,
                'category_slug' => 'cars-trucks',
            ])
            ->assertJsonStructure([
                'attributes' => [
                    '*' => ['id', 'name', 'label', 'type', 'required', 'filterable', 'options']
                ]
            ]);

        $attributes = $response->json('attributes');
        $this->assertNotEmpty($attributes);

        // Verify dynamic attribute names from Database
        $slugs = array_column($attributes, 'name');
        $this->assertContains('make', $slugs);
        $this->assertContains('year', $slugs);
    }
}
