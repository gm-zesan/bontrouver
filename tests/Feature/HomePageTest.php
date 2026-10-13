<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_loads_with_all_dynamic_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Verify view variables for all 10 sections
        $response->assertViewHas([
            'featuredAds',
            'trendingListings',
            'featuredListings',
            'locations',
            'companionshipRequests',
            'housing',
            'jobs',
            'classifieds',
            'whyUsListing',
            'sellerCtaListing',
            'locationName',
            'availableCities',
        ]);

        // Check dynamic locations
        $locations = $response->viewData('locations');
        $this->assertNotEmpty($locations);
        $this->assertArrayHasKey('city', $locations->first());
        $this->assertArrayHasKey('province', $locations->first());

        // Check dynamic meetups
        $meetups = $response->viewData('companionshipRequests');
        $this->assertNotEmpty($meetups);

        // Check dynamic spotlight categories
        $housing = $response->viewData('housing');
        $this->assertNotEmpty($housing['category']);
        $this->assertNotEmpty($housing['url']);

        $jobs = $response->viewData('jobs');
        $this->assertNotEmpty($jobs['category']);

        $classifieds = $response->viewData('classifieds');
        $this->assertNotEmpty($classifieds['items']);

        // Check dynamic preview listings
        $whyUsListing = $response->viewData('whyUsListing');
        $this->assertNotEmpty($whyUsListing['title']);

        $sellerCtaListing = $response->viewData('sellerCtaListing');
        $this->assertNotEmpty($sellerCtaListing['title']);
    }

    public function test_home_page_personalizes_to_selected_city(): void
    {
        $response = $this->get('/?city=Montreal');

        $response->assertStatus(200);
        $response->assertViewHas('locationName', 'Montreal');
        $response->assertViewHas('selectedCity', 'Montreal');
    }

    public function test_home_page_renders_french_translations_when_french_active(): void
    {
        $response = $this->withSession(['locale' => 'fr'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Explorer les catégories');
        $response->assertSee('Tendances près de chez vous');
        $response->assertSee('Annonces en vedette');
        $response->assertSee('Alertes intelligentes');
        $response->assertSee('Besoin de compagnie ?');
        $response->assertSee('Parcourir par région');
        $response->assertSee('PUBLIEZ EN MOINS DE 2 MINUTES');
        $response->assertSee('Tout ce qui est local');
    }

    public function test_home_page_renders_english_content_when_english_active(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Explore Categories');
        $response->assertSee('Trending Near You');
        $response->assertSee('Featured Listings');
        $response->assertSee('Smart Alerts');
        $response->assertSee('Need Companionship?');
        $response->assertSee('Browse by Location');
        $response->assertSee('POST IN UNDER 2 MINUTES');
        $response->assertSee('Everything Local');
    }
}

