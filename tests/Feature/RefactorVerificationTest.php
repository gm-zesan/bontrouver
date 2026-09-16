<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefactorVerificationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test the home page.
     */
    public function test_home_page_returns_successful_response()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test the listings index page.
     */
    public function test_listings_index_returns_successful_response()
    {
        $response = $this->get('/listings');
        $response->assertStatus(200);
    }

    /**
     * Test seller dashboard routes when authenticated.
     */
    public function test_seller_routes_return_successful_response()
    {
        $user = User::factory()->create();

        $routes = [
            '/profile/view',
            '/my-listings',
            '/favorites',
            '/messages',
            '/notifications',
            '/settings',
            '/my-meetups',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(200);
        }
    }
}
