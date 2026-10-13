<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Province;
use App\Models\User;
use App\Services\Translation\Drivers\MockTranslationDriver;
use App\Services\Translation\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ListingTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Province $province;
    protected City $city;
    protected Category $category;
    protected Listing $activeListing;

    protected function setUp(): void
    {
        parent::setUp();

        MockTranslationDriver::reset();

        // Configure translation driver to use mock driver for tests
        config(['translation.driver' => 'mock']);

        $this->user = User::factory()->create([
            'email' => 'seller@example.com',
        ]);

        $this->province = Province::create([
            'name' => 'Quebec',
            'code' => 'QC',
            'slug' => 'qc',
            'country_code' => 'CA',
            'is_active' => true,
        ]);

        $this->city = City::create([
            'name' => 'Montréal',
            'slug' => 'montreal',
            'province_id' => $this->province->id,
            'latitude' => 45.5017,
            'longitude' => -73.5673,
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Apartments & Housing',
            'slug' => 'apartments-housing',
            'is_active' => true,
        ]);

        $this->activeListing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'province_id' => $this->province->id,
            'city_id' => $this->city->id,
            'title' => 'Bright 2-Bedroom Apartment in Plateau',
            'description' => 'Beautiful sunny apartment close to Mont-Royal metro with balcony.',
            'price' => 1450.00,
            'price_type' => 'fixed',
            'status' => ListingStatus::ACTIVE,
            'slug' => 'bright-2-bedroom-apartment-in-plateau',
        ]);
    }

    protected function tearDown(): void
    {
        MockTranslationDriver::reset();
        parent::tearDown();
    }

    /**
     * Test 1: Locale switcher updates session and respects allowed locales.
     */
    public function test_locale_switcher_updates_session_and_validates_allowed_locales(): void
    {
        $responseFr = $this->get(route('locale.switch', 'fr'));
        $responseFr->assertRedirect();
        $this->assertEquals('fr', session('locale'));

        $responseEn = $this->get(route('locale.switch', 'en'));
        $responseEn->assertRedirect();
        $this->assertEquals('en', session('locale'));

        // Unsupported locale should not update session
        $this->get(route('locale.switch', 'de'));
        $this->assertEquals('en', session('locale'));
    }

    /**
     * Test 2: Active listing translates title and description for detail scope.
     */
    public function test_listing_translation_endpoint_translates_title_and_description(): void
    {
        $response = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_locale' => 'fr',
                'scope' => 'detail',
                'cached' => false,
            ])
            ->assertJsonStructure([
                'translated' => [
                    'title',
                    'description',
                ],
            ]);

        $data = $response->json();
        $this->assertStringContainsString('Bright 2-Bedroom', $data['translated']['title']);
        $this->assertStringContainsString('Beautiful sunny apartment', $data['translated']['description']);
    }

    /**
     * Test 3: Card scope translates only the title to optimize bandwidth.
     */
    public function test_card_scope_translates_only_title(): void
    {
        $response = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'card',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_locale' => 'fr',
                'scope' => 'card',
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('title', $data['translated']);
        $this->assertArrayNotHasKey('description', $data['translated']);
    }

    /**
     * Test 4: Content-aware caching reuses cached translations.
     */
    public function test_translation_is_cached_with_sha256_content_fingerprint_and_reused(): void
    {
        // First request - miss
        $firstResponse = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);
        $firstResponse->assertStatus(200);
        $this->assertFalse($firstResponse->json('cached'));

        // Second request - hit
        $secondResponse = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);
        $secondResponse->assertStatus(200);
        $this->assertTrue($secondResponse->json('cached'));
        $this->assertEquals($firstResponse->json('translated'), $secondResponse->json('translated'));
    }

    /**
     * Test 5: Editing listing content invalidates old translation cache.
     */
    public function test_editing_listing_content_invalidates_old_translation_cache(): void
    {
        // First call caches original
        $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);

        // Seller updates listing title
        $this->activeListing->update([
            'title' => 'Renovated Modern Studio in Mile End',
        ]);

        // Request again
        $updatedResponse = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);

        $updatedResponse->assertStatus(200);
        $this->assertFalse($updatedResponse->json('cached'));
        $this->assertStringContainsString('Renovated Modern Studio', $updatedResponse->json('translated.title'));
    }

    /**
     * Test 6: Inactive or draft listings cannot be translated via public endpoint.
     */
    public function test_inactive_or_draft_listing_cannot_be_translated(): void
    {
        $draftListing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'province_id' => $this->province->id,
            'city_id' => $this->city->id,
            'title' => 'Unpublished Draft Car Ad',
            'description' => 'Draft description',
            'price' => 5000.00,
            'price_type' => 'fixed',
            'status' => ListingStatus::DRAFT,
            'slug' => 'unpublished-draft-car-ad',
        ]);

        $response = $this->postJson(route('listings.translate', $draftListing), [
            'target_locale' => 'fr',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test 7: Validation rejects unsupported target locales.
     */
    public function test_validation_rejects_unsupported_target_locale(): void
    {
        $response = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'es',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['target_locale']);
    }

    /**
     * Test 8: Provider failure returns graceful error without caching.
     */
    public function test_provider_failure_returns_graceful_error_without_caching(): void
    {
        MockTranslationDriver::simulateFailure(true);

        $response = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
            ]);

        // Verify failure was not cached
        MockTranslationDriver::simulateFailure(false);
        $retryResponse = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);
        $retryResponse->assertStatus(200);
        $this->assertFalse($retryResponse->json('cached'));
    }

    /**
     * Test 9: Translation never modifies the database listing record.
     */
    public function test_database_raw_content_remains_strictly_unmodified(): void
    {
        $originalTitle = $this->activeListing->title;
        $originalDesc = $this->activeListing->description;

        $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);

        $this->activeListing->refresh();

        $this->assertEquals($originalTitle, $this->activeListing->title);
        $this->assertEquals($originalDesc, $this->activeListing->description);
    }

    /**
     * Test 10: Multilingual authorship (Arabic ad) translates without character set rejection.
     */
    public function test_multilingual_arabic_authorship_translates_cleanly(): void
    {
        $arabicListing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'province_id' => $this->province->id,
            'city_id' => $this->city->id,
            'title' => 'شقة مفروشة للإيجار في وسط مونتريال',
            'description' => 'شقة نظيفة ومفروشة بالكامل بالقرب من محطة المترو والجامعة.',
            'price' => 1200.00,
            'price_type' => 'fixed',
            'status' => ListingStatus::ACTIVE,
            'slug' => 'arabic-furnished-apartment-montreal',
        ]);

        $response = $this->postJson(route('listings.translate', $arabicListing), [
            'target_locale' => 'en',
            'scope' => 'detail',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_locale' => 'en',
            ]);

        // Ensure database remains in Arabic
        $arabicListing->refresh();
        $this->assertEquals('شقة مفروشة للإيجار في وسط مونتريال', $arabicListing->title);
    }

    /**
     * Test 11: Batch translate multiple listing cards simultaneously.
     */
    public function test_translate_batch_translates_multiple_listing_cards_successfully(): void
    {
        $secondListing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'province_id' => $this->province->id,
            'city_id' => $this->city->id,
            'title' => 'Vintage Wooden Desk in Excellent Shape',
            'description' => 'Solid oak desk with 4 drawers.',
            'price' => 250.00,
            'price_type' => 'fixed',
            'status' => ListingStatus::ACTIVE,
            'slug' => 'vintage-wooden-desk',
        ]);

        $response = $this->postJson(route('listings.translate.batch'), [
            'listing_ids' => [$this->activeListing->id, $secondListing->id],
            'target_locale' => 'fr',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_locale' => 'fr',
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('translations', $data);
        $this->assertArrayHasKey((string) $this->activeListing->id, $data['translations']);
        $this->assertArrayHasKey((string) $secondListing->id, $data['translations']);
        $this->assertNotEmpty($data['translations'][$this->activeListing->id]['title']);
    }

    /**
     * Test 12: Hero scope batch translates both title and description.
     */
    public function test_translate_hero_scope_translates_both_title_and_description_in_batch(): void
    {
        $heroListing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'province_id' => $this->province->id,
            'city_id' => $this->city->id,
            'title' => 'Luxury Condo with Panoramic City View',
            'description' => 'Spectacular executive condo featuring floor-to-ceiling windows and indoor parking.',
            'price' => 3200.00,
            'price_type' => 'fixed',
            'status' => ListingStatus::ACTIVE,
            'is_sponsored' => true,
            'slug' => 'luxury-condo-panoramic-city-view',
        ]);

        $response = $this->postJson(route('listings.translate.batch'), [
            'listing_ids' => [$heroListing->id],
            'target_locale' => 'fr',
            'scope' => 'hero',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_locale' => 'fr',
                'scope' => 'hero',
            ]);

        $data = $response->json();
        $this->assertArrayHasKey('translations', $data);
        $this->assertArrayHasKey((string) $heroListing->id, $data['translations']);
        $this->assertNotEmpty($data['translations'][$heroListing->id]['title']);
        $this->assertNotEmpty($data['translations'][$heroListing->id]['description']);
    }

    /**
     * Test 13: Single listing on-demand translation accepts hero scope.
     */
    public function test_single_listing_accepts_hero_scope(): void
    {
        $response = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'hero',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_locale' => 'fr',
                'scope' => 'hero',
            ]);

        $this->assertNotEmpty($response->json('translated.title'));
        $this->assertNotEmpty($response->json('translated.description'));
    }

    /**
     * Test 14: GoogleWebDriver handles network timeout gracefully without throwing unhandled exceptions.
     */
    public function test_google_web_driver_handles_timeout_gracefully(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'translate.googleapis.com/*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timed out after 5000ms');
            },
        ]);

        $driver = new \App\Services\Translation\Drivers\GoogleWebDriver();
        $result = $driver->translate('Hello world', 'fr');

        $this->assertNull($result);
    }

    /**
     * Test 15: GoogleWebDriver handles rate limiting (HTTP 429) gracefully without looping.
     */
    public function test_google_web_driver_handles_rate_limiting_429_gracefully(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'translate.googleapis.com/*' => \Illuminate\Support\Facades\Http::response('Too Many Requests', 429),
        ]);

        $driver = new \App\Services\Translation\Drivers\GoogleWebDriver();
        $result = $driver->translate('Hello world', 'fr');

        $this->assertNull($result);
    }

    /**
     * Test 16: GoogleWebDriver handles invalid or malformed JSON responses safely.
     */
    public function test_google_web_driver_handles_invalid_or_malformed_provider_payload(): void
    {
        // Malformed structure (empty array / non-standard format)
        \Illuminate\Support\Facades\Http::fake([
            'translate.googleapis.com/*' => \Illuminate\Support\Facades\Http::response('<html><body>Blocked</body></html>', 200),
        ]);

        $driver = new \App\Services\Translation\Drivers\GoogleWebDriver();
        $result = $driver->translate('Hello world', 'fr');

        $this->assertNull($result);
    }

    /**
     * Test 17: Cache reuse prevents repeated external provider requests for identical content.
     */
    public function test_cache_reuse_prevents_repeated_external_provider_requests(): void
    {
        config(['translation.driver' => 'google_web']);

        $requestCount = 0;
        \Illuminate\Support\Facades\Http::fake([
            'translate.googleapis.com/*' => function () use (&$requestCount) {
                $requestCount++;
                return \Illuminate\Support\Facades\Http::response([
                    [
                        ['Appartement lumineux sur le Plateau', 'Bright 2-Bedroom Apartment in Plateau']
                    ]
                ], 200);
            },
        ]);

        // First request triggers provider HTTP call
        $firstResponse = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'card',
        ]);
        $firstResponse->assertStatus(200);
        $this->assertEquals(1, $requestCount);

        // Second request for identical content MUST reuse cache and NOT call provider again
        $secondResponse = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'card',
        ]);
        $secondResponse->assertStatus(200);
        $this->assertTrue($secondResponse->json('cached'));
        $this->assertEquals(1, $requestCount, 'Cache hit must prevent repeated provider network requests');
    }

    /**
     * Test 18: Provider failure preserves raw listing content and listing page renders seamlessly.
     */
    public function test_provider_failure_preserves_original_listing_content_and_pages_do_not_break(): void
    {
        MockTranslationDriver::simulateFailure(true);

        $originalTitle = $this->activeListing->title;
        $originalDesc = $this->activeListing->description;

        // Translation API returns 503 error without crashing
        $transResponse = $this->postJson(route('listings.translate', $this->activeListing), [
            'target_locale' => 'fr',
            'scope' => 'detail',
        ]);
        $transResponse->assertStatus(503)
            ->assertJson([
                'success' => false,
            ]);

        // Raw database listing content is 100% preserved
        $this->activeListing->refresh();
        $this->assertEquals($originalTitle, $this->activeListing->title);
        $this->assertEquals($originalDesc, $this->activeListing->description);

        // Public listing detail page loads with 200 OK and intact original content
        $pageResponse = $this->get('/listing/' . $this->activeListing->slug);
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee($originalTitle);
    }

    /**
     * Test 19: TranslationService never silently falls back to a paid provider.
     */
    public function test_translation_service_never_silently_falls_back_to_paid_provider(): void
    {
        // Simulate both free web drivers failing
        \Illuminate\Support\Facades\Http::fake([
            'translate.googleapis.com/*' => \Illuminate\Support\Facades\Http::response('Rate limited', 429),
            'api.mymemory.translated.net/*' => \Illuminate\Support\Facades\Http::response('Quota exceeded', 429),
        ]);

        config(['translation.driver' => 'google_web']);

        $service = new TranslationService();
        $result = $service->translateText('Sample text', 'fr');

        // Must return null safely rather than invoking any paid API
        $this->assertNull($result);
    }

    /**
     * Test 20: Long description with paragraph breaks, special characters, and quotes translates cleanly.
     */
    public function test_long_description_with_paragraph_breaks_and_special_characters_translates_cleanly(): void
    {
        $longDescription = "Paragraph 1: Executive 3-Bedroom Penthouse in downtown Montreal with high ceilings & hardwood floors.\n\n" .
            "Paragraph 2: Key Amenities & Features:\n" .
            "• Heated indoor pool & 24/7 fitness center\n" .
            "• Underground parking spot #42 included\n" .
            "• High-end stainless steel appliances (Fridge, Stove & Dishwasher)\n\n" .
            "Paragraph 3: Terms & Contact:\n" .
            "Price: $2,850.00 / month. Available \"immediately\"! No smoking, pets allowed with deposit. Call (514) 555-0199 or email <owner@montreal-condos.ca>.";

        $detailedListing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'province_id' => $this->province->id,
            'city_id' => $this->city->id,
            'title' => 'Executive 3-Bedroom Penthouse & Terrace <Rare Find>',
            'description' => $longDescription,
            'price' => 2850.00,
            'price_type' => 'fixed',
            'status' => ListingStatus::ACTIVE,
            'slug' => 'executive-penthouse-terrace-montreal',
        ]);

        $response = $this->postJson(route('listings.translate', $detailedListing), [
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
        // Verify line breaks are maintained in translated payload
        $this->assertStringContainsString("\n", $translated['description']);

        // Verify raw database values remain completely unmodified
        $detailedListing->refresh();
        $this->assertEquals($longDescription, $detailedListing->description);
    }

    /**
     * Test 21: Arabic text with emojis and token batching matches accurately.
     */
    public function test_arabic_text_with_emojis_and_token_batching_matches_accurately(): void
    {
        $arabicListing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'province_id' => $this->province->id,
            'city_id' => $this->city->id,
            'title' => 'شقة فاخرة للإيجار 🏡 وسط المدينة',
            'description' => 'شقة مفروشة بالكامل بالقرب من محطة المترو 🚇 مع موقف سيارات خاص 🚗 وتدفئة مجانية.',
            'price' => 1500.00,
            'price_type' => 'fixed',
            'status' => ListingStatus::ACTIVE,
            'is_sponsored' => true,
            'slug' => 'luxury-arabic-furnished-apartment-downtown',
        ]);

        $response = $this->postJson(route('listings.translate.batch'), [
            'listing_ids' => [$arabicListing->id],
            'target_locale' => 'fr',
            'scope' => 'hero',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'target_locale' => 'fr',
                'scope' => 'hero',
            ]);

        $translations = $response->json('translations');
        $this->assertArrayHasKey((string) $arabicListing->id, $translations);
        $this->assertNotEmpty($translations[$arabicListing->id]['title']);
        $this->assertNotEmpty($translations[$arabicListing->id]['description']);

        // Verify Arabic text in database remains strictly unmodified
        $arabicListing->refresh();
        $this->assertEquals('شقة فاخرة للإيجار 🏡 وسط المدينة', $arabicListing->title);
    }
}

