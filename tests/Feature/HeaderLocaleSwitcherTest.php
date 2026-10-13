<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderLocaleSwitcherTest extends TestCase
{
    use RefreshDatabase;
    public function test_default_locale_renders_english_header(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Categories');
        $response->assertSee('What are you looking for?');
        $response->assertSee('Post');
        $response->assertSee('Sign In');
        $response->assertSee('All Canada');
    }

    public function test_switching_to_french_sets_session_and_cookie_and_redirects(): void
    {
        $response = $this->get(route('locale.switch', 'fr'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'fr');
        $response->assertCookie('locale', 'fr');
    }

    public function test_french_session_renders_french_header_elements(): void
    {
        $response = $this->withSession(['locale' => 'fr'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Catégories');
        $response->assertSee('Que recherchez-vous ?');
        $response->assertSee('Publier');
        $response->assertSee('Connexion');
        $response->assertSee('Tout le Canada');
        $response->assertSee('title="Interface en français"', false);
    }

    public function test_french_cookie_renders_french_header_when_session_is_fresh(): void
    {
        $response = $this->withCookie('locale', 'fr')->get('/');

        $response->assertStatus(200);
        $response->assertSee('Catégories');
        $response->assertSee('Que recherchez-vous ?');
        $response->assertSee('Publier');
        $response->assertSee('Connexion');
        $response->assertSee('Tout le Canada');
    }

    public function test_switching_back_to_english_restores_english_header(): void
    {
        // First switch to fr
        $this->get(route('locale.switch', 'fr'));

        // Then switch to en
        $switchResponse = $this->get(route('locale.switch', 'en'));
        $switchResponse->assertRedirect();
        $switchResponse->assertSessionHas('locale', 'en');
        $switchResponse->assertCookie('locale', 'en');

        // Then visit home with en session
        $homeResponse = $this->withSession(['locale' => 'en'])->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Categories');
        $homeResponse->assertSee('What are you looking for?');
        $homeResponse->assertSee('Post');
        $homeResponse->assertSee('Sign In');
        $homeResponse->assertSee('All Canada');
        $homeResponse->assertSee('title="English interface"', false);
    }
}

