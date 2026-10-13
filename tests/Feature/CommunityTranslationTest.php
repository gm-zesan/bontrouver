<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CompanionshipRequest;
use App\Models\Province;
use App\Models\User;
use App\Services\Translation\Drivers\MockTranslationDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CommunityTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Province $province;
    protected City $city;
    protected CompanionshipRequest $meetup;

    protected function setUp(): void
    {
        parent::setUp();

        MockTranslationDriver::reset();
        config(['translation.driver' => 'mock']);

        $this->user = User::factory()->create([
            'email' => 'host@example.com',
            'name' => 'Host User',
        ]);

        $this->province = Province::create([
            'name_en'      => 'Quebec',
            'name_fr'      => 'Québec',
            'code'         => 'QC',
            'slug'         => 'qc',
            'country_code' => 'CA',
            'is_active'    => true,
        ]);

        $this->city = City::create([
            'name_en'     => 'Montréal',
            'name_fr'     => 'Montréal',
            'slug'        => 'montreal',
            'province_id' => $this->province->id,
            'latitude'    => 45.5017,
            'longitude'   => -73.5673,
            'is_active'   => true,
        ]);

        $this->meetup = CompanionshipRequest::create([
            'user_id' => $this->user->id,
            'type' => 'Coffee & Chat',
            'title' => 'Sunday Morning Coffee at Mile End',
            'description' => 'Looking to meet fellow tech enthusiasts and chat about AI over coffee.',
            'meetup_date_time' => now()->addDays(3),
            'city_id' => $this->city->id,
            'location_name' => 'Café Olimpico',
            'city' => 'Montréal',
            'province' => 'QC',
            'latitude' => 45.5222,
            'longitude' => -73.5954,
            'headcount_limit' => 5,
            'expense_type' => 'free',
            'status' => 'open',
        ]);
    }

    public function test_translate_meetup_returns_successful_translation(): void
    {
        $response = $this->postJson(route('community.translate', $this->meetup), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_locale' => 'fr',
                'scope' => 'detail',
            ]);

        $translated = $response->json('translated');
        $this->assertNotEmpty($translated['title']);
        $this->assertNotEmpty($translated['description']);

        // Database record must NOT be modified
        $fresh = $this->meetup->fresh();
        $this->assertSame('Sunday Morning Coffee at Mile End', $fresh->title);
        $this->assertSame('Looking to meet fellow tech enthusiasts and chat about AI over coffee.', $fresh->description);
    }

    public function test_translate_batch_translates_multiple_meetup_cards(): void
    {
        $meetup2 = CompanionshipRequest::create([
            'user_id' => $this->user->id,
            'type' => 'Walk',
            'title' => 'Mount Royal Evening Walk',
            'description' => 'Casual walk around the park.',
            'meetup_date_time' => now()->addDays(2),
            'city_id' => $this->city->id,
            'location_name' => 'Mount Royal Park',
            'city' => 'Montréal',
            'province' => 'QC',
            'status' => 'open',
        ]);

        $response = $this->postJson(route('community.translate.batch'), [
            'meetup_ids' => [$this->meetup->id, $meetup2->id],
            'target_locale' => 'fr',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_locale' => 'fr',
            ]);

        $translations = $response->json('translations');
        $this->assertArrayHasKey($this->meetup->id, $translations);
        $this->assertArrayHasKey($meetup2->id, $translations);
    }

    public function test_community_detail_page_renders_translate_button(): void
    {
        $response = $this->get(route('community.show', $this->meetup->id));

        $response->assertStatus(200)
            ->assertSee('data-action="translate-meetup"', false)
            ->assertSee('data-meetup-id="' . $this->meetup->id . '"', false);
    }

    public function test_community_index_page_renders_translate_buttons_for_cards(): void
    {
        $response = $this->get(route('community.index'));

        $response->assertStatus(200)
            ->assertSee('data-action="translate-meetup"', false)
            ->assertSee('data-scope="card"', false);
    }
}
