<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\MemberTier;
use App\Models\PointRule;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ProvinceSeeder::class);
        $this->seed(\Database\Seeders\CitySeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\MemberTierSeeder::class);
        $this->seed(\Database\Seeders\PointRuleSeeder::class);
    }

    public function test_member_benefits_page_renders_dynamic_tiers_and_rules(): void
    {
        $response = $this->get(route('pages.member-benefits'));

        $response->assertStatus(200)
            ->assertViewIs('frontend.pages.member-benefits')
            ->assertViewHas('tiers')
            ->assertViewHas('rules')
            ->assertViewHas('stats')
            ->assertSee('Member Tiers')
            ->assertSee('New Member')
            ->assertSee('Active Member')
            ->assertSee('Trusted Member');
    }

    public function test_about_page_renders_dynamic_marketplace_statistics(): void
    {
        $response = $this->get(route('pages.about'));

        $response->assertStatus(200)
            ->assertViewIs('frontend.pages.about')
            ->assertViewHas('stats')
            ->assertSee('Active Classified Ads')
            ->assertSee('Provinces &amp; Territories', false);
    }

    public function test_community_connect_page_renders_dynamic_counts(): void
    {
        $response = $this->get(route('pages.community-connect'));

        $response->assertStatus(200)
            ->assertViewIs('frontend.pages.community-connect')
            ->assertViewHas('freeCount')
            ->assertViewHas('openMeetupsCount');
    }

    public function test_all_informational_policy_pages_return_successful_response(): void
    {
        $routes = [
            'pages.terms',
            'pages.privacy',
            'pages.posting-policy',
            'pages.security',
            'pages.verification',
            'pages.advertise',
            'pages.promote-tools',
            'pages.accessibility',
            'pages.ad-choices',
        ];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertStatus(200);
        }
    }

    public function test_static_pages_render_french_when_locale_is_fr(): void
    {
        $aboutResponse = $this->withSession(['locale' => 'fr'])->get(route('pages.about'));
        $aboutResponse->assertStatus(200)
            ->assertSee("À propos de Bontrouver Canada")
            ->assertSee("Petites annonces actives");

        $benefitsResponse = $this->withSession(['locale' => 'fr'])->get(route('pages.member-benefits'));
        $benefitsResponse->assertStatus(200)
            ->assertSee("Niveaux de membres et points de réputation Bontrouver");

        $termsResponse = $this->withSession(['locale' => 'fr'])->get(route('pages.terms'));
        $termsResponse->assertStatus(200)
            ->assertSee("Conditions d'utilisation");

        $privacyResponse = $this->withSession(['locale' => 'fr'])->get(route('pages.privacy'));
        $privacyResponse->assertStatus(200)
            ->assertSee("Politique de confidentialité");

        $securityResponse = $this->withSession(['locale' => 'fr'])->get(route('pages.security'));
        $securityResponse->assertStatus(200)
            ->assertSee("Transactions sûres et prévention de la fraude");
    }
}
