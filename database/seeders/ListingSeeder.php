<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\Listing;
use App\Models\ListingAttribute;
use App\Models\ListingImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@bontrouver.ca')->first();
        $metro = User::where('email', 'metro.auto@bontrouver.ca')->first();
        $sarah = User::where('email', 'seller@bontrouver.ca')->first();
        $david = User::where('email', 'david.miller@example.ca')->first();
        $alex  = User::where('email', 'buyer@bontrouver.ca')->first();

        if (!$admin || !$metro || !$sarah || !$david) {
            $this->command->warn('Required users not found. Run UserSeeder first.');
            return;
        }

        $listings = [
            // ─── HOUSING ───
            [
                'user'          => $metro,
                'category'      => 'apartments-condos-rent',
                'title'         => 'Modern 1-Bedroom Condo with Balcony & City Views',
                'description'   => 'Bright and spacious 1-bedroom condo in Liberty Village. Floor-to-ceiling windows, stainless steel appliances, in-suite laundry, gym and pool access. Just steps from TTC King streetcar and local cafes.',
                'price'         => 2350.00,
                'price_type'    => 'fixed',
                'price_period'  => 'month',
                'condition'     => null,
                'location_name' => 'Liberty Village',
                'city'          => 'Toronto',
                'province'      => 'ON',
                'postal_code'   => 'M6K 3S3',
                'latitude'      => 43.6375,
                'longitude'     => -79.4206,
                'badge'         => 'SPONSORED',
                'is_sponsored'  => true,
                'is_featured'   => false,
                'images'        => [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'bedrooms'         => '1 Bedroom',
                    'bathrooms'        => '1 Bathroom',
                    'furnished'        => 'Unfurnished',
                    'pet-friendly'     => 'Yes',
                    'parking-included' => '1 Spot Included',
                    'square-footage'   => '620',
                ],
            ],
            [
                'user'          => $david,
                'category'      => 'houses-rent',
                'title'         => 'Spacious 3-Bed Detached House with Backyard — Pet Friendly',
                'description'   => 'Beautiful detached house in prime Kitsilano. Large fenced backyard, renovated gourmet kitchen, 2 full bathrooms, attached garage for 2 cars. Close to Kits Beach, schools, and transit.',
                'price'         => 3400.00,
                'price_type'    => 'negotiable',
                'price_period'  => 'month',
                'condition'     => null,
                'location_name' => 'Kitsilano',
                'city'          => 'Vancouver',
                'province'      => 'BC',
                'postal_code'   => 'V6K 1A1',
                'latitude'      => 49.2684,
                'longitude'     => -123.1683,
                'badge'         => 'FEATURED',
                'is_sponsored'  => false,
                'is_featured'   => true,
                'images'        => [
                    'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'bedrooms'     => '3 Bedrooms',
                    'bathrooms'    => '2 Bathrooms',
                    'pet-friendly' => 'Yes',
                    'backyard'     => 'Fenced Private Yard',
                ],
            ],
            [
                'user'          => $sarah,
                'category'      => 'room-rentals-roommates',
                'title'         => 'Furnished Student Room Near McGill — All Inclusive',
                'description'   => 'Fully furnished private room near McGill University in Plateau-Mont-Royal. High-speed gigabit Wi-Fi, hydro, and heating included. Clean and quiet environment with young professionals and students.',
                'price'         => 950.00,
                'price_type'    => 'fixed',
                'price_period'  => 'month',
                'condition'     => null,
                'location_name' => 'Plateau-Mont-Royal',
                'city'          => 'Montréal',
                'province'      => 'QC',
                'postal_code'   => 'H2W 1X6',
                'latitude'      => 45.5231,
                'longitude'     => -73.5825,
                'badge'         => 'NEW',
                'is_sponsored'  => false,
                'is_featured'   => false,
                'images'        => [
                    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'bedrooms'  => '1 Bedroom',
                    'bathrooms' => '1 Bathroom',
                    'furnished' => 'Furnished',
                ],
            ],
            [
                'user'          => $david,
                'category'      => 'apartments-condos-rent',
                'title'         => 'Waterfront 2-Bed Luxury Penthouse with CN Tower Views',
                'description'   => 'Stunning penthouse suite on the 34th floor with panoramic CN Tower and lake views. Two master bedrooms, 2.5 baths, private terrace, 24/7 concierge service, and EV charging stall.',
                'price'         => 3850.00,
                'price_type'    => 'fixed',
                'price_period'  => 'month',
                'condition'     => null,
                'location_name' => 'Harbourfront',
                'city'          => 'Toronto',
                'province'      => 'ON',
                'postal_code'   => 'M5J 2H2',
                'latitude'      => 43.6426,
                'longitude'     => -79.3871,
                'badge'         => 'SPONSORED',
                'is_sponsored'  => true,
                'is_featured'   => false,
                'images'        => [
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'bedrooms'         => '2 Bedrooms',
                    'bathrooms'        => '2.5 Bathrooms',
                    'furnished'        => 'Furnished',
                    'pet-friendly'     => 'Yes',
                    'parking-included' => '1 Spot Included',
                    'square-footage'   => '1150',
                ],
            ],

            // ─── CARS & VEHICLES ───
            [
                'user'          => $metro,
                'category'      => 'cars-trucks',
                'title'         => '2023 Toyota RAV4 Hybrid XSE AWD — One Owner, Low KM',
                'description'   => 'Single-owner 2023 RAV4 Hybrid XSE AWD. Only 12,400 km. Equipped with Technology Package, Apple CarPlay, panoramic sunroof, heated steering wheel, and winter tire set. Clean CARFAX with no accidents.',
                'price'         => 41500.00,
                'price_type'    => 'fixed',
                'price_period'  => null,
                'condition'     => 'used_excellent',
                'location_name' => 'North York',
                'city'          => 'Toronto',
                'province'      => 'ON',
                'postal_code'   => 'M2N 6L7',
                'latitude'      => 43.7615,
                'longitude'     => -79.4111,
                'badge'         => 'SPONSORED',
                'is_sponsored'  => true,
                'is_featured'   => false,
                'images'        => [
                    'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'make'         => 'Toyota',
                    'model'        => 'RAV4 Hybrid XSE',
                    'year'         => '2023',
                    'kilometers'   => '12400',
                    'transmission' => 'Automatic',
                    'fuel-type'    => 'Hybrid',
                    'body-type'    => 'SUV / Crossover',
                    'drivetrain'   => 'AWD',
                ],
            ],
            [
                'user'          => $metro,
                'category'      => 'cars-trucks',
                'title'         => '2022 Tesla Model 3 Long Range AWD — Autopilot & Low Mileage',
                'description'   => 'Immaculate 2022 Tesla Model 3 Long Range with Dual Motor AWD. Pearl White Multi-Coat, Premium Black Interior, 28,000 km. Includes Mobile Connector, tinted windows, and Full Self-Driving computer ready.',
                'price'         => 38900.00,
                'price_type'    => 'negotiable',
                'price_period'  => null,
                'condition'     => 'used_excellent',
                'location_name' => 'Downtown',
                'city'          => 'Calgary',
                'province'      => 'AB',
                'postal_code'   => 'T2P 1J9',
                'latitude'      => 51.0447,
                'longitude'     => -114.0719,
                'badge'         => 'FEATURED',
                'is_sponsored'  => false,
                'is_featured'   => true,
                'images'        => [
                    'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1571127236794-81c0bbfe1ce3?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1536700503339-1e4b06520771?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'make'         => 'Tesla',
                    'model'        => 'Model 3 Long Range',
                    'year'         => '2022',
                    'kilometers'   => '28000',
                    'transmission' => 'Direct Drive (EV)',
                    'fuel-type'    => 'Electric',
                    'body-type'    => 'Sedan',
                    'drivetrain'   => 'AWD',
                ],
            ],
            [
                'user'          => $metro,
                'category'      => 'cars-trucks',
                'title'         => '2021 Ford F-150 Lariat 4x4 SuperCrew 3.5L EcoBoost',
                'description'   => 'Loaded 2021 Ford F-150 Lariat 4x4. 44,000 km, FX4 Off-Road package, 502A Luxury package, B&O Sound System, twin panel moonroof, spray-in bedliner, tonneau cover.',
                'price'         => 46500.00,
                'price_type'    => 'fixed',
                'price_period'  => null,
                'condition'     => 'used_good',
                'location_name' => 'Mississauga City Centre',
                'city'          => 'Mississauga',
                'province'      => 'ON',
                'postal_code'   => 'L5B 4M9',
                'latitude'      => 43.5890,
                'longitude'     => -79.6441,
                'badge'         => 'FEATURED',
                'is_sponsored'  => false,
                'is_featured'   => true,
                'images'        => [
                    'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'make'         => 'Ford',
                    'model'        => 'F-150 Lariat',
                    'year'         => '2021',
                    'kilometers'   => '44000',
                    'transmission' => 'Automatic',
                    'fuel-type'    => 'Gasoline',
                    'body-type'    => 'Truck / Pickup',
                    'drivetrain'   => '4WD',
                ],
            ],
            [
                'user'          => $metro,
                'category'      => 'cars-trucks',
                'title'         => '2020 Honda Civic Sport Hatchback — Manual 6-Speed',
                'description'   => 'Fun to drive 2020 Honda Civic Sport 6-speed manual. 52,000 km. Excellent condition, always serviced at Honda dealer. Apple CarPlay/Android Auto, Honda Sensing safety suite.',
                'price'         => 22800.00,
                'price_type'    => 'negotiable',
                'price_period'  => null,
                'condition'     => 'used_excellent',
                'location_name' => 'Centretown',
                'city'          => 'Ottawa',
                'province'      => 'ON',
                'postal_code'   => 'K1R 5A3',
                'latitude'      => 45.4140,
                'longitude'     => -75.6980,
                'badge'         => null,
                'is_sponsored'  => false,
                'is_featured'   => false,
                'images'        => [
                    'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1590362891988-f77804783182?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'make'         => 'Honda',
                    'model'        => 'Civic Sport',
                    'year'         => '2020',
                    'kilometers'   => '52000',
                    'transmission' => 'Manual',
                    'fuel-type'    => 'Gasoline',
                    'body-type'    => 'Hatchback',
                    'drivetrain'   => 'FWD',
                ],
            ],

            // ─── ELECTRONICS & TECH ───
            [
                'user'          => $sarah,
                'category'      => 'smartphones-tablets',
                'title'         => 'iPhone 15 Pro Max 256GB Natural Titanium — Unlocked w/ AppleCare+',
                'description'   => 'Flawless condition Apple iPhone 15 Pro Max 256GB in Natural Titanium. 100% battery health. Comes with original box, braided USB-C cable, and valid AppleCare+ until October 2026.',
                'price'         => 1280.00,
                'price_type'    => 'fixed',
                'price_period'  => null,
                'condition'     => 'used_excellent',
                'location_name' => 'Downtown',
                'city'          => 'Montréal',
                'province'      => 'QC',
                'postal_code'   => 'H3A 1A1',
                'latitude'      => 45.5017,
                'longitude'     => -73.5673,
                'badge'         => 'FEATURED',
                'is_sponsored'  => false,
                'is_featured'   => true,
                'images'        => [
                    'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'brand'            => 'Apple',
                    'storage-capacity' => '256 GB',
                    'carrier-lock'     => 'Factory Unlocked',
                    'color'            => 'Natural Titanium',
                ],
            ],
            [
                'user'          => $sarah,
                'category'      => 'laptops-computers',
                'title'         => 'MacBook Pro 16" M3 Max (36GB RAM, 1TB SSD) — Space Black',
                'description'   => 'Monster powerhouse laptop for video editing, 3D rendering, and dev work. 16-core CPU, 40-core GPU, 36GB unified memory, 1TB blazing fast SSD. Battery cycle count: only 23.',
                'price'         => 3450.00,
                'price_type'    => 'negotiable',
                'price_period'  => null,
                'condition'     => 'used_excellent',
                'location_name' => 'Downtown Core',
                'city'          => 'Toronto',
                'province'      => 'ON',
                'postal_code'   => 'M5C 1N8',
                'latitude'      => 43.6532,
                'longitude'     => -79.3832,
                'badge'         => 'SPONSORED',
                'is_sponsored'  => true,
                'is_featured'   => false,
                'images'        => [
                    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'brand'        => 'Apple',
                    'processor'    => 'Apple M1 / M2 / M3 / M4',
                    'ram'          => '36 GB',
                    'storage-size' => '1 TB SSD',
                    'screen-size'  => '16 inch',
                ],
            ],
            [
                'user'          => $sarah,
                'category'      => 'smartphones-tablets',
                'title'         => 'Samsung Galaxy S24 Ultra 512GB Titanium Gray — Like New in Box',
                'description'   => 'Samsung Galaxy S24 Ultra with Galaxy AI features. 512GB storage, 12GB RAM, built-in S-Pen. Factory unlocked for all Canadian carriers. Comes with 2 Spigen cases.',
                'price'         => 1320.00,
                'price_type'    => 'fixed',
                'price_period'  => null,
                'condition'     => 'used_excellent',
                'location_name' => 'Yaletown',
                'city'          => 'Vancouver',
                'province'      => 'BC',
                'postal_code'   => 'V6B 1T8',
                'latitude'      => 49.2755,
                'longitude'     => -123.1215,
                'badge'         => 'FEATURED',
                'is_sponsored'  => false,
                'is_featured'   => true,
                'images'        => [
                    'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'brand'            => 'Samsung',
                    'storage-capacity' => '512 GB',
                    'carrier-lock'     => 'Factory Unlocked',
                    'color'            => 'Titanium Gray',
                ],
            ],

            // ─── BUY & SELL / FURNITURE & HOME ───
            [
                'user'          => $david,
                'category'      => 'furniture-home',
                'title'         => 'Mid-Century Modern Teak 6-Seater Dining Table & Chairs Set',
                'description'   => 'Authentic vintage Danish teak dining table with two pull-out leaf extensions and 6 matching solid wood chairs reupholstered in textured wool fabric. Beautiful warm grain.',
                'price'         => 1450.00,
                'price_type'    => 'negotiable',
                'price_period'  => null,
                'condition'     => 'used_good',
                'location_name' => 'Mount Pleasant',
                'city'          => 'Vancouver',
                'province'      => 'BC',
                'postal_code'   => 'V5T 2M9',
                'latitude'      => 49.2635,
                'longitude'     => -123.1009,
                'badge'         => 'FEATURED',
                'is_sponsored'  => false,
                'is_featured'   => true,
                'images'        => [
                    'https://images.unsplash.com/photo-1617806118233-18e1de247200?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'furniture-type' => 'Dining Table & Chairs',
                    'material'       => 'Solid Wood',
                    'color'          => 'Teak Walnut',
                ],
            ],
            [
                'user'          => $david,
                'category'      => 'furniture-home',
                'title'         => 'West Elm Velvet Sectional Sofa in Emerald Green — Mint Condition',
                'description'   => 'Gorgeous West Elm Haven sectional couch in performance velvet emerald green. Left arm chaise, brass leg accents. Non-smoking, pet-free home.',
                'price'         => 1150.00,
                'price_type'    => 'fixed',
                'price_period'  => null,
                'condition'     => 'used_excellent',
                'location_name' => 'Beltline',
                'city'          => 'Calgary',
                'province'      => 'AB',
                'postal_code'   => 'T2R 0S7',
                'latitude'      => 51.0396,
                'longitude'     => -114.0722,
                'badge'         => 'FEATURED',
                'is_sponsored'  => false,
                'is_featured'   => true,
                'images'        => [
                    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'furniture-type' => 'Sofa & Couch',
                    'material'       => 'Velvet / Fabric',
                    'color'          => 'Emerald Green',
                ],
            ],

            // ─── SERVICES & TRADES ───
            [
                'user'          => $admin,
                'category'      => 'home-renovations-repair',
                'title'         => 'Licensed Electrician & Panel Upgrades (200 Amp) — GTA Wide',
                'description'   => 'Master Electrician with 15+ years experience serving the Greater Toronto Area. EV charger installs, 200A panel upgrades, potlights, and basement wiring. Free estimates and ESA certified.',
                'price'         => 95.00,
                'price_type'    => 'fixed',
                'price_period'  => 'hour',
                'condition'     => null,
                'location_name' => 'Greater Toronto Area',
                'city'          => 'Toronto',
                'province'      => 'ON',
                'postal_code'   => 'M1B 2K9',
                'latitude'      => 43.7764,
                'longitude'     => -79.2318,
                'badge'         => 'FEATURED',
                'is_sponsored'  => false,
                'is_featured'   => true,
                'images'        => [
                    'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'service-specialty' => 'Electrical & Lighting',
                    'licensed-insured'  => 'Yes (Fully Licensed & WSIB/Insured)',
                ],
            ],

            // ─── JOBS & CAREERS ───
            [
                'user'          => $admin,
                'category'      => 'software-development',
                'title'         => 'Senior Full Stack Laravel & Vue.js Developer (Remote Canada)',
                'description'   => 'Fast-growing Canadian e-commerce tech platform seeking a Senior Full-Stack Engineer with 5+ years experience in PHP, Laravel 11/12, Vue 3, and PostgreSQL. Full benefits, 4 weeks vacation, RRSP matching.',
                'price'         => 125000.00,
                'price_type'    => 'fixed',
                'price_period'  => 'year',
                'condition'     => null,
                'location_name' => 'Downtown (Remote Option)',
                'city'          => 'Vancouver',
                'province'      => 'BC',
                'postal_code'   => 'V6C 1X8',
                'latitude'      => 49.2827,
                'longitude'     => -123.1207,
                'badge'         => 'FEATURED',
                'is_sponsored'  => false,
                'is_featured'   => true,
                'images'        => [
                    'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=800&q=80',
                ],
                'attributes'    => [
                    'job-type'         => 'Full-time',
                    'work-location'    => 'Remote',
                    'experience-level' => 'Senior (5+ yrs)',
                ],
            ],
        ];

        foreach ($listings as $data) {
            $user = $data['user'];

            // Find category by slug; fallback to first root category
            $category = Category::where('slug', $data['category'])->first()
                ?? Category::whereNull('parent_id')->where('is_active', true)->first();

            if (!$category) {
                $this->command->warn("Skipping listing — no category found for slug: {$data['category']}");
                continue;
            }

            $slug = Str::slug($data['title']) . '-' . Str::random(5);

            // Find matching City from cities table
            $citySlug = Str::slug($data['city']);
            $cityModel = \App\Models\City::where('slug', $citySlug)
                ->orWhere('name', 'like', '%' . $data['city'] . '%')
                ->first();

            $listing = Listing::create([
                'user_id'      => $user->id,
                'category_id'  => $category->id,
                'city_id'      => $cityModel?->id,
                'title'        => $data['title'],
                'slug'         => $slug,
                'description'  => $data['description'],
                'price'        => $data['price'],
                'price_type'   => $data['price_type'],
                'price_period' => $data['price_period'] ?? null,
                'condition'    => $data['condition'] ?? null,
                'location_name'=> $data['location_name'],
                'city'         => $cityModel?->name ?? $data['city'],
                'province'     => $cityModel?->province?->code ?? $data['province'],
                'postal_code'  => $data['postal_code'] ?? null,
                'latitude'     => $data['latitude'] ?? $cityModel?->latitude,
                'longitude'    => $data['longitude'] ?? $cityModel?->longitude,
                'status'       => 'active',
                'is_featured'  => $data['is_featured'] ?? false,
                'is_sponsored' => $data['is_sponsored'] ?? false,
                'views_count'  => rand(45, 1200),
                'published_at' => now()->subHours(rand(1, 48)),
                'expires_at'   => now()->addDays(30),
            ]);

            // Seed Images (Primary + Gallery)
            $images = $data['images'] ?? [];
            foreach ($images as $index => $imageUrl) {
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => $imageUrl,
                    'is_primary' => ($index === 0),
                    'sort_order' => $index,
                ]);
            }

            // Seed Listing Attributes matching Category Attributes
            if (!empty($data['attributes'])) {
                foreach ($data['attributes'] as $attrSlug => $attrValue) {
                    $catAttr = CategoryAttribute::where('category_id', $category->id)
                        ->where('slug', $attrSlug)
                        ->first();

                    if ($catAttr) {
                        ListingAttribute::create([
                            'listing_id'            => $listing->id,
                            'category_attribute_id' => $catAttr->id,
                            'value'                 => (string) $attrValue,
                        ]);
                    }
                }
            }
        }

        $this->command->info('ListingSeeder: ' . count($listings) . ' rich listings seeded with coordinates, gallery images, and attributes.');
    }
}
