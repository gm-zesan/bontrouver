<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use App\Events\ListingCreated;
use Tests\TestCase;

class ListingCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_unauthenticated_user_cannot_create_listing(): void
    {
        $response = $this->postJson('/post-ad', [
            'title' => 'Sample Listing Title',
            'category_slug' => 'electronics',
            'description' => 'A valid description with more than 15 characters.',
            'city' => 'Toronto',
            'province' => 'ON',
            'price_type' => 'fixed',
            'price' => 100,
        ]);

        $response->assertStatus(401);
    }

    public function test_validation_fails_when_required_fields_are_missing(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->postJson('/post-ad', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'category_slug', 'description', 'city', 'province', 'price_type']);
    }

    public function test_listing_creates_successfully_with_only_required_fields(): void
    {
        $user = User::first() ?? User::factory()->create();
        $category = Category::whereNotNull('parent_id')->first() ?? Category::first();

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'         => 'Minimal Valid Listing',
            'category_slug' => $category->slug,
            'description'   => 'This is a description that easily exceeds the fifteen character minimum requirement.',
            'city'          => 'Montreal',
            'province'      => 'QC',
            'price_type'    => 'free',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('listings', [
            'title'      => 'Minimal Valid Listing',
            'user_id'    => $user->id,
            'city'       => 'Montreal',
            'province'   => 'QC',
            'price_type' => 'free',
            'price'      => 0,
            'status'     => 'active',
        ]);
    }

    public function test_listing_creates_with_all_optional_fields_and_coordinates(): void
    {
        $user = User::first() ?? User::factory()->create();
        $category = Category::whereNotNull('parent_id')->first() ?? Category::first();

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'             => 'Complete Listing With All Optional Fields',
            'category_slug'     => $category->parent?->slug ?? $category->slug,
            'subcategory_slug'  => $category->slug,
            'description'       => 'Detailed listing description for a high quality item with every single optional field specified.',
            'city'              => 'Vancouver',
            'province'          => 'BC',
            'postal_code'       => 'V6B 1A1',
            'neighbourhood'     => 'Downtown / Yaletown',
            'latitude'          => 49.2827,
            'longitude'         => -123.1207,
            'price_type'        => 'fixed',
            'price'             => 499.99,
            'condition'         => 'like_new',
            'delivery_options'  => ['pickup', 'shipping'],
            'contact_preference'=> ['chat', 'email'],
            'promotions'        => ['featured' => true],
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('listings', [
            'title'         => 'Complete Listing With All Optional Fields',
            'postal_code'   => 'V6B 1A1',
            'location_name' => 'Downtown / Yaletown',
            'price'         => 499.99,
            'condition'     => 'like_new',
            'is_featured'   => 1,
        ]);
    }

    public function test_listing_creates_with_multiple_uploaded_files(): void
    {
        $user = User::first() ?? User::factory()->create();
        $category = Category::first();

        $file1 = UploadedFile::fake()->image('front.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('back.png', 800, 600);
        $file3 = UploadedFile::fake()->image('detail.webp', 800, 600);

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'         => 'Listing with Multiple Uploaded Images',
            'category_slug' => $category->slug,
            'description'   => 'Testing image upload processing for multiple binary images from file inputs.',
            'city'          => 'Calgary',
            'province'      => 'AB',
            'price_type'    => 'negotiable',
            'price'         => 250.00,
            'images'        => [$file1, $file2, $file3],
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $listing = Listing::where('title', 'Listing with Multiple Uploaded Images')->first();
        $this->assertNotNull($listing);
        $this->assertEquals(3, $listing->images()->count());

        $primaryImage = $listing->primaryImage;
        $this->assertNotNull($primaryImage);
        $this->assertTrue((bool)$primaryImage->is_primary);
        $this->assertStringStartsWith('/storage/listings/', $primaryImage->image_path);
    }

    public function test_listing_creates_with_base64_encoded_image(): void
    {
        $user = User::first() ?? User::factory()->create();
        $category = Category::first();

        // 1x1 transparent PNG data URI
        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'         => 'Listing with Base64 Previews',
            'category_slug' => $category->slug,
            'description'   => 'Testing base64 dataUrl conversion into persistent disk storage files.',
            'city'          => 'Ottawa',
            'province'      => 'ON',
            'price_type'    => 'contact',
            'images'        => [$base64Image],
        ]);

        $response->assertStatus(200);

        $listing = Listing::where('title', 'Listing with Base64 Previews')->first();
        $this->assertNotNull($listing);
        $this->assertEquals(1, $listing->images()->count());
        $this->assertStringStartsWith('/storage/listings/', $listing->primaryImage->image_path);
    }

    public function test_listing_creates_with_dynamic_category_attributes(): void
    {
        $user = User::first() ?? User::factory()->create();
        $category = Category::whereHas('attributes')->first() ?? Category::first();
        $catAttr = $category->attributes->first() ?? CategoryAttribute::first();

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'         => 'Listing with Dynamic Attributes Saved',
            'category_slug' => $category->slug,
            'description'   => 'Testing persistence of dynamic category attributes schema values.',
            'city'          => 'Toronto',
            'province'      => 'ON',
            'price_type'    => 'fixed',
            'price'         => 1200,
            'attributes'    => [
                $catAttr->slug => '2 Bedrooms',
            ],
        ]);

        $response->assertStatus(200);

        $listing = Listing::where('title', 'Listing with Dynamic Attributes Saved')->first();
        $this->assertNotNull($listing);
        $this->assertDatabaseHas('listing_attributes', [
            'listing_id'            => $listing->id,
            'category_attribute_id' => $catAttr->id,
            'value'                 => '2 Bedrooms',
        ]);
    }

    public function test_car_and_vehicle_category_listing_creation(): void
    {
        $user = User::first() ?? User::factory()->create();
        $category = Category::where('slug', 'cars-trucks')->first() ?? Category::where('slug', 'cars-vehicles')->first() ?? Category::first();

        $file = UploadedFile::fake()->image('car_front.jpg', 1200, 800);

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'             => '2024 Toyota RAV4 Hybrid AWD XSE',
            'category_slug'     => 'cars-vehicles',
            'subcategory_slug'  => 'cars-trucks',
            'description'       => 'Meticulously maintained, single owner, low mileage hybrid crossover in pristine condition.',
            'city'              => 'Markham',
            'province'          => 'ON',
            'postal_code'       => 'L3R 5H6',
            'neighbourhood'     => 'Unionville',
            'price_type'        => 'fixed',
            'price'             => 42500,
            'condition'         => 'like_new',
            'images'            => [$file],
            'attributes'        => [
                'make'         => 'Toyota',
                'model'        => 'RAV4 Hybrid',
                'year'         => 2024,
                'kilometers'   => 12000,
                'transmission' => 'Automatic',
                'fuel-type'    => 'Hybrid',
                'drivetrain'   => 'AWD',
            ],
            'contact_preference'=> ['chat', 'phone'],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $listing = Listing::where('title', '2024 Toyota RAV4 Hybrid AWD XSE')->first();
        $this->assertNotNull($listing);
        $this->assertEquals(42500, (float)$listing->price);
        $this->assertEquals('Markham', $listing->city);
        $this->assertEquals('ON', $listing->province);
        $this->assertEquals(1, $listing->images()->count());
    }

    public function test_housing_apartment_rental_category_listing_creation(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'             => 'Luxury 2 Bedroom Condo with Balcony in Downtown Montreal',
            'category_slug'     => 'housing',
            'subcategory_slug'  => 'apartments-condos-rent',
            'description'       => 'Bright, spacious corner unit with floor to ceiling windows, in-unit laundry, and parking space.',
            'city'              => 'Montreal',
            'province'          => 'QC',
            'postal_code'       => 'H3B 2Y5',
            'neighbourhood'     => 'Downtown / Ville-Marie',
            'price_type'        => 'fixed',
            'price'             => 2350,
            'attributes'        => [
                'bedrooms'           => '2 Bedrooms',
                'bathrooms'          => '2 Bathrooms',
                'furnished'          => 'Furnished',
                'pet-friendly'       => 'Yes',
                'parking'            => '1 Underground Spot',
                'utilities-included' => ['Heating', 'Water', 'High-Speed Internet'],
            ],
            'promotions'        => ['featured' => true],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $listing = Listing::where('title', 'Luxury 2 Bedroom Condo with Balcony in Downtown Montreal')->first();
        $this->assertNotNull($listing);
        $this->assertEquals(2350, (float)$listing->price);
        $this->assertEquals('Montreal', $listing->city);
        $this->assertTrue((bool)$listing->is_featured);
    }

    public function test_job_posting_category_listing_creation(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'             => 'Senior Full Stack Laravel Developer (Remote Canada)',
            'category_slug'     => 'jobs',
            'subcategory_slug'  => 'technology',
            'description'       => 'Looking for an experienced software engineer to build modern full-stack web applications.',
            'city'              => 'Toronto',
            'province'          => 'ON',
            'price_type'        => 'contact',
            'attributes'        => [
                'job-type'   => 'Full-time',
                'work-setup' => 'Remote',
            ],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $listing = Listing::where('title', 'Senior Full Stack Laravel Developer (Remote Canada)')->first();
        $this->assertNotNull($listing);
        $this->assertEquals('contact', $listing->price_type);
        $this->assertEquals(0, (float)$listing->price);
    }

    public function test_free_donation_category_listing_creation(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'             => 'Solid Wood Dining Table Set - Free for Pickup',
            'category_slug'     => 'community',
            'subcategory_slug'  => 'free-stuff',
            'description'       => 'Downsizing and giving away a 6-person wooden dining table with 4 matching chairs. Great condition!',
            'city'              => 'Calgary',
            'province'          => 'AB',
            'price_type'        => 'free',
            'price'             => 0,
            'condition'         => 'good',
            'delivery_options'  => ['pickup'],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $listing = Listing::where('title', 'Solid Wood Dining Table Set - Free for Pickup')->first();
        $this->assertNotNull($listing);
        $this->assertEquals('free', $listing->price_type);
        $this->assertEquals(0, (float)$listing->price);
    }

    public function test_electronics_category_listing_creation(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->postJson('/post-ad', [
            'title'             => 'Apple iPhone 15 Pro Max 256GB Natural Titanium',
            'category_slug'     => 'electronics',
            'subcategory_slug'  => 'phones',
            'description'       => 'Unlocked, 100% battery health, comes with original box, cable, and MagSafe protective case.',
            'city'              => 'Edmonton',
            'province'          => 'AB',
            'price_type'        => 'negotiable',
            'price'             => 1150,
            'condition'         => 'like_new',
            'attributes'        => [
                'brand'    => 'Apple',
                'storage'  => '256GB',
            ],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $listing = Listing::where('title', 'Apple iPhone 15 Pro Max 256GB Natural Titanium')->first();
        $this->assertNotNull($listing);
        $this->assertEquals(1150, (float)$listing->price);
        $this->assertEquals('negotiable', $listing->price_type);
    }

    public function test_created_listing_renders_on_listing_detail_page(): void
    {
        $user = User::first() ?? User::factory()->create();
        $category = Category::first();

        $postRes = $this->actingAs($user)->postJson('/post-ad', [
            'title'             => 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones',
            'category_slug'     => $category->slug,
            'description'       => 'Industry leading noise canceling headphones with original packaging and carry case included.',
            'city'              => 'Toronto',
            'province'          => 'ON',
            'price_type'        => 'fixed',
            'price'             => 340,
        ]);

        $postRes->assertStatus(200);
        $listingId = $postRes->json('listing_id');
        $this->assertNotNull($listingId);

        $detailRes = $this->get('/listing/' . $listingId);
        $detailRes->assertStatus(200);
        $detailRes->assertSee('Sony WH-1000XM5 Wireless Noise Cancelling Headphones');
        $detailRes->assertSee('340');
        $detailRes->assertSee('Toronto');
    }
}
