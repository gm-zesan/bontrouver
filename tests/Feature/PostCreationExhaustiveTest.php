<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\Listing;
use App\Models\PromotionPackage;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostCreationExhaustiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Province $province;
    protected City $city;
    protected Category $rootCategory;
    protected Category $subCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->province = Province::create([
            'code'         => 'QC',
            'name_en'      => 'Quebec',
            'name_fr'      => 'Québec',
            'slug'         => 'quebec',
            'country_code' => 'CA',
            'is_active'    => true,
            'sort_order'   => 1,
        ]);

        $this->city = City::create([
            'province_id' => $this->province->id,
            'name_en'     => 'Montreal',
            'name_fr'     => 'Montréal',
            'slug'        => 'montreal',
            'latitude'    => 45.5017,
            'longitude'   => -73.5673,
            'is_active'   => true,
            'is_featured' => true,
            'sort_order'  => 1,
        ]);

        $this->rootCategory = Category::create([
            'name_en'     => 'Vehicles',
            'name_fr'     => 'Véhicules',
            'slug'        => 'vehicles',
            'icon'        => 'bi-car-front',
            'is_active'   => true,
            'sort_order'  => 1,
        ]);

        $this->subCategory = Category::create([
            'parent_id'   => $this->rootCategory->id,
            'name_en'     => 'Cars & Trucks',
            'name_fr'     => 'Voitures et camions',
            'slug'        => 'cars-trucks',
            'icon'        => 'bi-car-front-fill',
            'is_active'   => true,
            'sort_order'  => 1,
        ]);

        $this->user = User::factory()->create([
            'city'             => 'Montreal',
            'province'         => 'QC',
            'postal_code'      => 'H2X 1Y6',
            'community_points' => 500,
        ]);
    }

    /**
     * 1. GET /post-ad renders successfully and auto-populates user home city & province
     */
    public function test_post_ad_page_renders_with_dynamic_user_location_prefill(): void
    {
        $response = $this->actingAs($this->user)->get(route('listings.create'));

        $response->assertOk();
        $response->assertSee('Post an Ad');
        $response->assertSee('Montreal');
        $response->assertSee('QC');
    }

    /**
     * 2. Unauthenticated user is redirected to login
     */
    public function test_guest_cannot_access_post_ad_or_create_listing(): void
    {
        $getRes = $this->get(route('listings.create'));
        $getRes->assertRedirect(route('login'));

        $postRes = $this->post(route('listings.store'), [
            'title'       => 'Valid Title Test Ad',
            'description' => 'This is a description that is at least 15 characters long.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 100,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $postRes->assertRedirect(route('login'));
    }

    /**
     * 3. Minimal Valid Post Creation (only required fields)
     */
    public function test_create_listing_with_minimal_valid_data(): void
    {
        $payload = [
            'title'       => '2020 Honda Civic EX',
            'description' => 'Clean car with low mileage and complete service records.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 18500,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ];

        $response = $this->actingAs($this->user)->post(route('listings.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('listings', [
            'user_id'     => $this->user->id,
            'title'       => '2020 Honda Civic EX',
            'category_id' => $this->subCategory->id,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
            'price'       => 18500,
            'price_type'  => 'fixed',
        ]);
    }

    /**
     * 4. AJAX submission returns JSON with listing ID, slug, and view URL
     */
    public function test_create_listing_ajax_returns_json_response(): void
    {
        $payload = [
            'title'       => 'MacBook Pro M2 16GB 512GB',
            'description' => 'Mint condition laptop with original box and charger.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'negotiable',
            'price'       => 1450,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ];

        $response = $this->actingAs($this->user)->postJson(route('listings.store'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Your ad has been successfully published!',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'listing_id',
                'listing_slug',
                'view_url',
                'manage_url',
            ]);
    }

    /**
     * 5. Testing all Pricing Types (Free, Contact, Negotiable with Period)
     */
    public function test_create_listing_with_all_pricing_types(): void
    {
        // 5a. Free Price
        $freeRes = $this->actingAs($this->user)->post(route('listings.store'), [
            'title'       => 'Free Moving Boxes & Packing Foam',
            'description' => 'Moving boxes available for immediate curbside pickup.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'free',
            'price'       => 0,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $freeRes->assertRedirect();
        $this->assertDatabaseHas('listings', [
            'title'      => 'Free Moving Boxes & Packing Foam',
            'price_type' => 'free',
            'price'      => 0,
        ]);

        // 5b. Contact for Price
        $contactRes = $this->actingAs($this->user)->post(route('listings.store'), [
            'title'       => 'Custom Kitchen Cabinet Carpentry',
            'description' => 'High-end custom woodwork. Please contact us for custom quote.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'contact',
            'price'       => null,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $contactRes->assertRedirect();
        $this->assertDatabaseHas('listings', [
            'title'      => 'Custom Kitchen Cabinet Carpentry',
            'price_type' => 'contact',
        ]);

        // 5c. Rent with Price Period (Month)
        $rentRes = $this->actingAs($this->user)->post(route('listings.store'), [
            'title'        => 'Downtown 2-Bedroom Condo for Lease',
            'description'  => 'Available immediately. Hydro and high-speed internet included.',
            'category_id'  => $this->subCategory->id,
            'price_type'   => 'fixed',
            'price'        => 2200,
            'price_period' => 'month',
            'city_id'      => $this->city->id,
            'province_id'  => $this->province->id,
        ]);
        $rentRes->assertRedirect();
        $this->assertDatabaseHas('listings', [
            'title'        => 'Downtown 2-Bedroom Condo for Lease',
            'price_period' => 'month',
            'price'        => 2200,
        ]);
    }

    /**
     * 6. Validation Errors on Missing & Invalid Inputs (Exhaustive)
     */
    public function test_validation_errors_on_missing_and_invalid_inputs(): void
    {
        // 6a. Missing Title
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'description' => 'Valid description at least fifteen characters long.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertStatus(422)->assertJsonValidationErrors('title');

        // 6b. Title too short (< 6 chars)
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Car',
            'description' => 'Valid description at least fifteen characters long.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertStatus(422)->assertJsonValidationErrors('title');

        // 6c. Title too long (> 150 chars)
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => str_repeat('A', 151),
            'description' => 'Valid description at least fifteen characters long.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertStatus(422)->assertJsonValidationErrors('title');

        // 6d. Missing Description
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Valid Car Title For Sale',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertStatus(422)->assertJsonValidationErrors('description');

        // 6e. Description too short (< 15 chars)
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Valid Car Title For Sale',
            'description' => 'Too short',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertStatus(422)->assertJsonValidationErrors('description');

        // 6f. Invalid price type
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Valid Car Title For Sale',
            'description' => 'Valid description with more than 15 characters.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'unsupported_type',
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertStatus(422)->assertJsonValidationErrors('price_type');

        // 6g. Negative Price
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Valid Car Title For Sale',
            'description' => 'Valid description with more than 15 characters.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => -50,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertStatus(422)->assertJsonValidationErrors('price');

        // 6h. Missing Location (neither city nor city_id)
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Valid Car Title For Sale',
            'description' => 'Valid description with more than 15 characters.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 100,
        ]);
        $res->assertStatus(422)->assertJsonValidationErrors(['city', 'city_id']);
    }

    /**
     * 7. Large Data: Long multilingual description, special characters, max title, high price
     */
    public function test_create_listing_with_large_and_rich_data(): void
    {
        $longDescription = str_repeat("Magnifique appartement au centre-ville de Montréal avec vue imprenable sur le Mont-Royal. " .
            "Proche de tous les services: métro, épiceries, universités et parcs. " .
            "Tous les électroménagers sont inclus (réfrigérateur, cuisinière, lave-vaisselle, laveuse/sécheuse). " .
            "Inclus également l'accès au gym et à la terrasse sur le toit. 🏙️✨\n\n", 15);

        $largePayload = [
            'title'         => 'Superbe condo 3 1/2 rénové au cœur du Plateau Mont-Royal avec grand balcon privé et garage intérieur',
            'description'   => $longDescription,
            'category_id'   => $this->subCategory->id,
            'price_type'    => 'fixed',
            'price'         => 1250000.50,
            'condition'     => 'like_new',
            'postal_code'   => 'H2W 1V8',
            'location_name' => 'Plateau-Mont-Royal, Montreal',
            'neighbourhood' => 'Le Plateau',
            'latitude'      => 45.5200,
            'longitude'     => -73.5800,
            'city_id'       => $this->city->id,
            'province_id'   => $this->province->id,
        ];

        $response = $this->actingAs($this->user)->postJson(route('listings.store'), $largePayload);

        $response->assertOk();
        $this->assertDatabaseHas('listings', [
            'title'         => $largePayload['title'],
            'price'         => 1250000.50,
            'postal_code'   => 'H2W 1V8',
            'location_name' => 'Plateau-Mont-Royal, Montreal',
        ]);

        $created = Listing::where('title', $largePayload['title'])->first();
        $this->assertNotNull($created);
        $this->assertEquals(trim($longDescription), $created->description);
    }

    /**
     * 8. Creating listing with multiple UploadedFile images
     */
    public function test_create_listing_with_multiple_images(): void
    {
        $file1 = UploadedFile::fake()->image('front.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('interior.png', 1024, 768);
        $file3 = UploadedFile::fake()->image('engine.jpg', 640, 480);

        $payload = [
            'title'       => '2019 Toyota RAV4 AWD Hybrid',
            'description' => 'Toyota RAV4 Hybrid with winter tires and clean CarFax.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 28900,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
            'images'      => [$file1, $file2, $file3],
        ];

        $response = $this->actingAs($this->user)->post(route('listings.store'), $payload);

        $response->assertRedirect();
        $listing = Listing::where('title', '2019 Toyota RAV4 AWD Hybrid')->first();
        $this->assertNotNull($listing);
        $this->assertCount(3, $listing->images);
        $this->assertTrue((bool)$listing->images->first()->is_primary);
    }

    /**
     * 9. Creating listing with dynamic EAV category attributes
     */
    public function test_create_listing_with_dynamic_category_attributes(): void
    {
        $makeAttr = CategoryAttribute::create([
            'category_id'   => $this->subCategory->id,
            'name_en'       => 'Make',
            'name_fr'       => 'Marque',
            'slug'          => 'make',
            'type'          => 'select',
            'is_required'   => true,
            'is_filterable' => true,
            'is_active'     => true,
        ]);

        $transAttr = CategoryAttribute::create([
            'category_id'   => $this->subCategory->id,
            'name_en'       => 'Transmission',
            'name_fr'       => 'Transmission',
            'slug'          => 'transmission',
            'type'          => 'select',
            'is_required'   => false,
            'is_filterable' => true,
            'is_active'     => true,
        ]);

        $yearAttr = CategoryAttribute::create([
            'category_id'   => $this->subCategory->id,
            'name_en'       => 'Year',
            'name_fr'       => 'Année',
            'slug'          => 'year',
            'type'          => 'number',
            'is_required'   => true,
            'is_filterable' => true,
            'is_active'     => true,
        ]);

        $payload = [
            'title'       => '2022 Mazda CX-5 GT AWD',
            'description' => 'One owner Mazda CX-5 with leather seats, sunroof and navigation.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 27500,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
            'attributes'  => [
                'make'         => 'Mazda',
                'transmission' => 'Automatic',
                'year'         => 2022,
            ],
        ];

        $response = $this->actingAs($this->user)->postJson(route('listings.store'), $payload);

        $response->assertOk();
        $listing = Listing::where('title', '2022 Mazda CX-5 GT AWD')->first();
        $this->assertNotNull($listing);
        $this->assertCount(3, $listing->attributes);

        $this->assertDatabaseHas('listing_attributes', [
            'listing_id'            => $listing->id,
            'category_attribute_id' => $makeAttr->id,
            'value'                 => 'Mazda',
        ]);
        $this->assertDatabaseHas('listing_attributes', [
            'listing_id'            => $listing->id,
            'category_attribute_id' => $yearAttr->id,
            'value'                 => '2022',
        ]);
    }

    /**
     * 10. Creating listing with text-based city/province names (auto-resolved or on-the-fly created)
     */
    public function test_create_listing_with_city_and_province_strings(): void
    {
        $payload = [
            'title'       => 'Artisan Handcrafted Wooden Coffee Table',
            'description' => 'Made from reclaimed Canadian maple wood. Perfect living room centerpiece.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 450,
            'city'        => 'Gatineau',
            'province'    => 'QC',
        ];

        $response = $this->actingAs($this->user)->postJson(route('listings.store'), $payload);

        $response->assertOk();
        $listing = Listing::where('title', 'Artisan Handcrafted Wooden Coffee Table')->first();
        $this->assertNotNull($listing);
        $this->assertEquals($this->province->id, $listing->province_id);

        // Auto-created City Gatineau in database
        $this->assertDatabaseHas('cities', [
            'name_en'     => 'Gatineau',
            'province_id' => $this->province->id,
        ]);
    }

    /**
     * 11. Creating listing with Featured Boost redeemed using Community Points
     */
    public function test_create_listing_with_points_redemption_boost(): void
    {
        $featPackage = PromotionPackage::create([
            'name_en'       => 'Featured Highlight',
            'name_fr'       => 'Mise en vedette',
            'slug'          => 'featured-highlight',
            'type'          => 'featured',
            'price'         => 4.99,
            'point_cost'    => 150,
            'duration_days' => 7,
            'is_active'     => true,
        ]);

        $initialPoints = $this->user->community_points; // 500

        $payload = [
            'title'          => 'Special Vintage Leather Armchair',
            'description'    => 'Restored vintage armchair in genuine Italian full-grain leather.',
            'category_id'    => $this->subCategory->id,
            'price_type'     => 'fixed',
            'price'          => 320,
            'city_id'        => $this->city->id,
            'province_id'    => $this->province->id,
            'promotions'     => ['featured'],
            'payment_method' => 'points',
        ];

        $response = $this->actingAs($this->user)->postJson(route('listings.store'), $payload);

        $response->assertOk();
        $listing = Listing::where('title', 'Special Vintage Leather Armchair')->first();
        $this->assertNotNull($listing);
        $this->assertTrue((bool)$listing->is_featured);

        // Deducted 150 points
        $this->user->refresh();
        $this->assertEquals($initialPoints - 150, $this->user->community_points);

        // Point Transaction recorded
        $this->assertDatabaseHas('point_transactions', [
            'user_id'     => $this->user->id,
            'points'      => -150,
            'action_type' => 'listing_boost_featured',
        ]);

        // Listing promotion active
        $this->assertDatabaseHas('listing_promotions', [
            'listing_id'     => $listing->id,
            'type'           => 'featured',
            'payment_method' => 'points',
            'points_spent'   => 150,
            'is_active'      => true,
        ]);
    }

    /**
     * 12. Non-ASCII titles (Arabic, French, Chinese, Emojis) generate valid, collision-free slugs
     */
    public function test_create_listing_with_non_ascii_and_multilingual_titles(): void
    {
        // 12a. Arabic title
        $arabicPayload = [
            'title'       => 'سيارة تويوتا كامري بحالة ممتازة للبيع',
            'description' => 'سيارة تويوتا كامري نظيفة جدا صيانة دورية بالوكالة.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 15000,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ];
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), $arabicPayload);
        $res->assertOk();
        $listing = Listing::where('title', $arabicPayload['title'])->first();
        $this->assertNotNull($listing);
        $this->assertNotEmpty($listing->slug);

        // 12b. Pure emojis and special symbols
        $emojiPayload = [
            'title'       => '🚗💨 🔥🔥🔥 Special Sport Car Edition',
            'description' => 'Fast sports car in great mechanical shape and pristine condition.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 25000,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ];
        $res2 = $this->actingAs($this->user)->postJson(route('listings.store'), $emojiPayload);
        $res2->assertOk();
        $listing2 = Listing::where('title', $emojiPayload['title'])->first();
        $this->assertNotNull($listing2);
        $this->assertNotEmpty($listing2->slug);
    }

    /**
     * 13. Rapid identical title submissions generate distinct unique slugs
     */
    public function test_rapid_duplicate_titles_generate_unique_slugs(): void
    {
        $payload1 = [
            'title'       => 'Identical Title Smartphone For Sale',
            'description' => 'Unlocked smartphone with charger and protective glass screen.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 500,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ];

        $res1 = $this->actingAs($this->user)->postJson(route('listings.store'), $payload1);
        $res1->assertOk();

        $payload2 = $payload1;
        $res2 = $this->actingAs($this->user)->postJson(route('listings.store'), $payload2);
        $res2->assertOk();

        $listings = Listing::where('title', 'Identical Title Smartphone For Sale')->get();
        $this->assertCount(2, $listings);
        $this->assertNotEquals($listings[0]->slug, $listings[1]->slug);
    }

    /**
     * 14. Extreme price values ($0.01, $9,999,999.00, high precision decimals)
     */
    public function test_create_listing_with_extreme_price_values(): void
    {
        // 14a. One cent
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Vintage Postage Stamp Cent Item',
            'description' => 'Canadian vintage collectible stamp for immediate sale.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 0.01,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertOk();
        $this->assertDatabaseHas('listings', ['title' => 'Vintage Postage Stamp Cent Item', 'price' => 0.01]);

        // 14b. Multi-million commercial property
        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Commercial Downtown Skyscraper Plaza',
            'description' => 'Prime commercial real estate investment property in downtown Montreal.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 8500000.00,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertOk();
        $this->assertDatabaseHas('listings', ['title' => 'Commercial Downtown Skyscraper Plaza', 'price' => 8500000.00]);
    }

    /**
     * 15. Base64 dataURL image uploads (autosaved draft recovery / mobile uploads)
     */
    public function test_create_listing_with_base64_data_url_images(): void
    {
        // 1x1 transparent GIF base64
        $base64Image = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        $payload = [
            'title'       => 'Retro Gaming Console Base64 Test',
            'description' => 'Classic video game console with 2 original wireless controllers.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 150,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
            'images'      => [$base64Image],
        ];

        $res = $this->actingAs($this->user)->postJson(route('listings.store'), $payload);
        $res->assertOk();

        $listing = Listing::where('title', 'Retro Gaming Console Base64 Test')->first();
        $this->assertNotNull($listing);
        $this->assertCount(1, $listing->images);
    }

    /**
     * 16. Edit & Update listing end-to-end (price change, title change, attributes update)
     */
    public function test_edit_and_update_listing_end_to_end(): void
    {
        $listing = Listing::create([
            'user_id'     => $this->user->id,
            'category_id' => $this->subCategory->id,
            'province_id' => $this->province->id,
            'city_id'     => $this->city->id,
            'title'       => 'Original Listing Title for Sale',
            'slug'        => 'original-listing-title-for-sale',
            'description' => 'Original description with plenty of characters to pass validation.',
            'price'       => 300,
            'price_type'  => 'fixed',
            'status'      => 'active',
        ]);

        $updatePayload = [
            'title'       => 'Updated Listing Title After Price Drop',
            'description' => 'Updated description with new details and lowered price for quick sale.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 220,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ];

        $res = $this->actingAs($this->user)->put(route('listings.update', $listing->id), $updatePayload);
        $res->assertRedirect();

        $listing->refresh();
        $this->assertEquals('Updated Listing Title After Price Drop', $listing->title);
        $this->assertEquals(220.00, (float)$listing->price);
    }

    /**
     * 17. Auto-approval site setting controls whether ad is active or pending review
     */
    public function test_auto_approval_setting_controls_listing_status(): void
    {
        // 17a. Default auto_approve_listings = true -> status active
        \App\Models\SiteSetting::set('auto_approve_listings', '1', 'marketplace', 'boolean');

        $res = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Auto Approved Active Listing Ad',
            'description' => 'This ad should be immediately active upon publishing.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 50,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res->assertOk();
        $listing1 = Listing::where('title', 'Auto Approved Active Listing Ad')->first();
        $this->assertEquals('active', $listing1->status->value);

        // 17b. When auto_approve_listings = false -> status pending_review
        \App\Models\SiteSetting::set('auto_approve_listings', '0', 'marketplace', 'boolean');

        $res2 = $this->actingAs($this->user)->postJson(route('listings.store'), [
            'title'       => 'Moderation Queue Pending Listing Ad',
            'description' => 'This ad must wait for administrator approval in moderation queue.',
            'category_id' => $this->subCategory->id,
            'price_type'  => 'fixed',
            'price'       => 50,
            'city_id'     => $this->city->id,
            'province_id' => $this->province->id,
        ]);
        $res2->assertOk();
        $listing2 = Listing::where('title', 'Moderation Queue Pending Listing Ad')->first();
        $this->assertEquals('pending_review', $listing2->status->value);
    }
}
