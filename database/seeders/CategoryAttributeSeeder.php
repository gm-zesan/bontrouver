<?php

namespace Database\Seeders;

use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributeArchetypes = $this->getAttributeArchetypes();

        $allCategories = Category::with(['parent.parent'])->get();
        $totalCategories = $allCategories->count();
        $categoriesWithAttributes = 0;
        $totalAttributes = 0;
        $totalOptions = 0;

        DB::beginTransaction();
        try {
            foreach ($allCategories as $category) {
                $attributes = $this->resolveAttributesForCategory($category, $attributeArchetypes);

                if (empty($attributes)) {
                    // Fallback to general item schema
                    $attributes = $attributeArchetypes['general_goods'];
                }

                $sortOrder = 1;
                foreach ($attributes as $attrData) {
                    $attribute = CategoryAttribute::updateOrCreate(
                        [
                            'category_id' => $category->id,
                            'slug'        => $attrData['slug'],
                        ],
                        [
                            'name'          => $attrData['name'],
                            'type'          => $attrData['type'],
                            'is_required'   => $attrData['is_required'] ?? false,
                            'is_filterable' => $attrData['is_filterable'] ?? false,
                            'is_active'     => true,
                            'sort_order'    => $sortOrder++,
                        ]
                    );

                    $totalAttributes++;

                    if (!empty($attrData['options'])) {
                        $optSort = 1;
                        foreach ($attrData['options'] as $optionLabel) {
                            AttributeOption::updateOrCreate(
                                [
                                    'category_attribute_id' => $attribute->id,
                                    'value'                 => Str::slug($optionLabel),
                                ],
                                [
                                    'label'      => $optionLabel,
                                    'is_active'  => true,
                                    'sort_order' => $optSort++,
                                ]
                            );
                            $totalOptions++;
                        }
                    }
                }

                $categoriesWithAttributes++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $this->command->info("CategoryAttributeSeeder completed successfully!");
        $this->command->info("Categories seeded: {$categoriesWithAttributes} / {$totalCategories}");
        $this->command->info("Total category attributes: {$totalAttributes}");
        $this->command->info("Total attribute options: {$totalOptions}");
    }

    /**
     * Resolve the best attribute set for a given category in the tree.
     */
    protected function resolveAttributesForCategory(Category $category, array $archetypes): array
    {
        $slug = $category->slug;
        $parentSlug = $category->parent?->slug ?? '';
        $rootSlug = $category->parent?->parent?->slug ?? $category->parent?->slug ?? $category->slug;

        // 1. Direct Slug Exact Match
        if (isset($archetypes[$slug])) {
            return $archetypes[$slug];
        }

        // 2. Direct Parent Slug Exact Match
        if (isset($archetypes[$parentSlug])) {
            return $archetypes[$parentSlug];
        }

        // 3. Domain Pattern Matching based on keywords in slug or parent slug
        $combined = "{$rootSlug} {$parentSlug} {$slug}";

        // --- HOUSING & REAL ESTATE ---
        if (Str::contains($combined, ['room-rental', 'roommate', 'room'])) {
            return $archetypes['room_rentals'];
        }
        if (Str::contains($combined, ['condo-rent', 'apartment', 'apartments-condos-rent'])) {
            return $archetypes['apartments_rent'];
        }
        if (Str::contains($combined, ['houses-rent', 'house-rent'])) {
            return $archetypes['houses_rent'];
        }
        if (Str::contains($combined, ['houses-sale', 'house-sale', 'condos-sale', 'condo-sale', 'property-sale'])) {
            return $archetypes['property_sale'];
        }
        if (Str::contains($combined, ['commercial', 'office-space', 'storefront', 'retail-space'])) {
            return $archetypes['commercial_real_estate'];
        }
        if (Str::contains($combined, ['land', 'plot', 'acreage', 'lot'])) {
            return $archetypes['land_sale'];
        }
        if (Str::contains($combined, ['storage', 'parking-rent', 'garage-rent'])) {
            return $archetypes['storage_parking'];
        }
        if (Str::contains($combined, ['housing', 'rental', 'rent', 'lease'])) {
            return $archetypes['apartments_rent'];
        }

        // --- CARS & VEHICLES ---
        if (Str::contains($combined, ['classic-cars', 'vintage-cars', 'muscle-cars'])) {
            return $archetypes['classic_cars'];
        }
        if (Str::contains($combined, ['cars-trucks', 'cars', 'trucks', 'suvs', 'sedan', 'van', 'automobile', 'hatchback'])) {
            return $archetypes['cars_trucks'];
        }
        if (Str::contains($combined, ['motorcycle', 'scooter', 'moped', 'sport-bike', 'cruiser', 'dirt-bike'])) {
            return $archetypes['motorcycles'];
        }
        if (Str::contains($combined, ['atv', 'snowmobile', 'quad', 'side-by-side', 'utv', 'seadoo', 'watercraft', 'boat'])) {
            return $archetypes['powersports_boats'];
        }
        if (Str::contains($combined, ['rv', 'camper', 'trailer', 'motorhome', 'fifth-wheel'])) {
            return $archetypes['rvs_campers'];
        }
        if (Str::contains($combined, ['tire', 'wheel', 'rim', 'parts', 'accessories', 'audio', 'auto-parts'])) {
            return $archetypes['auto_parts'];
        }
        if (Str::contains($combined, ['heavy-equipment', 'farming', 'tractor', 'excavator', 'bobcat', 'industrial-machinery'])) {
            return $archetypes['heavy_equipment'];
        }
        if ($rootSlug === 'cars-vehicles') {
            return $archetypes['cars_trucks'];
        }

        // --- BUY & SELL / ELECTRONICS / GOODS ---
        if (Str::contains($combined, ['phone', 'smartphone', 'iphone', 'samsung', 'cellular', 'telecom'])) {
            return $archetypes['smartphones'];
        }
        if (Str::contains($combined, ['computer', 'laptop', 'macbook', 'pc', 'tablet', 'ipad', 'desktop', 'server'])) {
            return $archetypes['computers_laptops'];
        }
        if (Str::contains($combined, ['video-game', 'console', 'playstation', 'xbox', 'nintendo', 'gaming'])) {
            return $archetypes['video_games_consoles'];
        }
        if (Str::contains($combined, ['audio', 'stereo', 'speaker', 'headphone', 'amplifier', 'home-theater', 'soundbar'])) {
            return $archetypes['audio_electronics'];
        }
        if (Str::contains($combined, ['camera', 'camcorder', 'dslr', 'lens', 'photography', 'drone', 'gopro'])) {
            return $archetypes['cameras'];
        }
        if (Str::contains($combined, ['furniture', 'couch', 'sofa', 'table', 'bed', 'mattress', 'desk', 'chair', 'decor'])) {
            return $archetypes['furniture_home'];
        }
        if (Str::contains($combined, ['cloth', 'shoe', 'apparel', 'dress', 'jacket', 'boot', 'sneaker', 'bag', 'fashion'])) {
            return $archetypes['clothing_shoes'];
        }
        if (Str::contains($combined, ['jewelry', 'watch', 'ring', 'diamond', 'gold', 'luxury', 'necklace'])) {
            return $archetypes['jewelry_watches'];
        }
        if (Str::contains($combined, ['instrument', 'guitar', 'piano', 'keyboard', 'drum', 'bass', 'brass', 'violin', 'synth'])) {
            return $archetypes['musical_instruments'];
        }
        if (Str::contains($combined, ['bike', 'cycling', 'bicycle', 'e-bike', 'mountain-bike', 'road-bike'])) {
            return $archetypes['bikes_cycling'];
        }
        if (Str::contains($combined, ['tool', 'hardware', 'power-tool', 'drill', 'saw', 'welder', 'generator', 'lawnmower'])) {
            return $archetypes['tools_hardware'];
        }
        if (Str::contains($combined, ['sport', 'fitness', 'gym', 'hockey', 'golf', 'ski', 'snowboard', 'workout', 'exercise'])) {
            return $archetypes['sporting_goods'];
        }
        if (Str::contains($combined, ['baby', 'toy', 'stroller', 'crib', 'toddler', 'kids', 'infant', 'car-seat'])) {
            return $archetypes['baby_toys'];
        }
        if (Str::contains($combined, ['book', 'media', 'dvd', 'vinyl', 'comic', 'hobby', 'craft', 'collectible', 'card'])) {
            return $archetypes['books_collectibles'];
        }
        if (Str::contains($combined, ['garden', 'outdoor', 'patio', 'lawn', 'plant', 'bbq', 'grill'])) {
            return $archetypes['home_garden'];
        }
        if (Str::contains($combined, ['health', 'beauty', 'skincare', 'cosmetic', 'perfume', 'wellness'])) {
            return $archetypes['health_beauty'];
        }
        if ($rootSlug === 'buy-sell') {
            return $archetypes['general_goods'];
        }

        // --- JOBS ---
        if ($rootSlug === 'jobs' || Str::contains($combined, ['job', 'employment', 'career', 'hiring', 'recruitment', 'work'])) {
            return $archetypes['jobs_employment'];
        }

        // --- SERVICES ---
        if (Str::contains($combined, ['renovation', 'contractor', 'plumbing', 'electrical', 'hvac', 'roofing', 'handyman', 'carpentry', 'painting'])) {
            return $archetypes['services_home_trade'];
        }
        if (Str::contains($combined, ['clean', 'housekeeping', 'maid', 'janitorial', 'carpet-clean'])) {
            return $archetypes['services_cleaning'];
        }
        if (Str::contains($combined, ['tech-support', 'computer-repair', 'it-service', 'web-design', 'data-recovery'])) {
            return $archetypes['services_tech'];
        }
        if (Str::contains($combined, ['tutor', 'lesson', 'class', 'language', 'coach', 'music-lesson', 'driving-school'])) {
            return $archetypes['services_tutoring'];
        }
        if (Str::contains($combined, ['financial', 'legal', 'tax', 'accounting', 'notary', 'lawyer', 'bookkeeping'])) {
            return $archetypes['services_financial_legal'];
        }
        if (Str::contains($combined, ['childcare', 'babysitt', 'nanny', 'daycare'])) {
            return $archetypes['services_childcare'];
        }
        if (Str::contains($combined, ['moving', 'storage-service', 'hauling', 'delivery-service'])) {
            return $archetypes['services_moving_hauling'];
        }
        if (Str::contains($combined, ['event', 'entertainment', 'dj', 'catering', 'party', 'wedding', 'photography', 'videography'])) {
            return $archetypes['services_events_creative'];
        }
        if ($rootSlug === 'services') {
            return $archetypes['services_general'];
        }

        // --- PETS ---
        if (Str::contains($combined, ['dog', 'puppy', 'puppies', 'canine'])) {
            return $archetypes['pets_dogs'];
        }
        if (Str::contains($combined, ['cat', 'kitten', 'kittens', 'feline'])) {
            return $archetypes['pets_cats'];
        }
        if (Str::contains($combined, ['bird', 'parrot', 'fish', 'aquarium', 'reptile', 'amphibian', 'small-animal', 'rabbit', 'hamster'])) {
            return $archetypes['pets_small_animals'];
        }
        if (Str::contains($combined, ['pet-sitting', 'grooming', 'boarding', 'walking', 'pet-service'])) {
            return $archetypes['pets_services'];
        }
        if (Str::contains($combined, ['pet-accessories', 'pet-food', 'supplies', 'crate', 'cage', 'aquarium-tank'])) {
            return $archetypes['pets_supplies'];
        }
        if ($rootSlug === 'pets') {
            return $archetypes['pets_dogs'];
        }

        // --- RIDESHARING & CARPOOL ---
        if ($rootSlug === 'ridesharing' || $rootSlug === 'rideshare-carpool' || Str::contains($combined, ['rideshare', 'carpool', 'ride', 'commute', 'trip', 'shuttle'])) {
            return $archetypes['rideshare_carpool'];
        }

        // --- COMMUNITY ---
        if ($rootSlug === 'community' || Str::contains($combined, ['community', 'volunteer', 'charity', 'club', 'group', 'meetup', 'activity', 'lost-found'])) {
            return $archetypes['community_social'];
        }

        // --- VACATION RENTALS ---
        if ($rootSlug === 'vacation-rentals' || Str::contains($combined, ['vacation', 'cottage', 'cabin', 'chalet', 'beachfront', 'lodge', 'glamping', 'resort'])) {
            return $archetypes['vacation_rentals'];
        }

        // Default Fallback
        return $archetypes['general_goods'];
    }

    /**
     * Master archetypes dictionary with rich, standardized attributes and options.
     */
    protected function getAttributeArchetypes(): array
    {
        return [
            // ─────────────────────────────────────────────────────────────
            // 🚗 VEHICLES & AUTOMOTIVE ARCHETYPES
            // ─────────────────────────────────────────────────────────────
            'cars_trucks' => [
                [
                    'name'          => 'Make',
                    'slug'          => 'make',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes-Benz', 'Audi', 'Hyundai', 'Tesla', 'Chevrolet', 'Subaru', 'Nissan', 'Mazda', 'Porsche', 'Volkswagen', 'Jeep', 'Lexus', 'Kia', 'Volvo', 'Other']
                ],
                [
                    'name'          => 'Model',
                    'slug'          => 'model',
                    'type'          => 'text',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Year',
                    'slug'          => 'year',
                    'type'          => 'number',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Kilometers',
                    'slug'          => 'kilometers',
                    'type'          => 'number',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Transmission',
                    'slug'          => 'transmission',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Automatic', 'Manual', 'CVT', 'Direct Drive (EV)']
                ],
                [
                    'name'          => 'Fuel Type',
                    'slug'          => 'fuel-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Gasoline', 'Hybrid', 'Plug-in Hybrid', 'Electric', 'Diesel']
                ],
                [
                    'name'          => 'Body Type',
                    'slug'          => 'body-type',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['SUV / Crossover', 'Sedan', 'Truck / Pickup', 'Coupe', 'Hatchback', 'Minivan / Van', 'Convertible', 'Wagon']
                ],
                [
                    'name'          => 'Drivetrain',
                    'slug'          => 'drivetrain',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['AWD', '4WD', 'FWD', 'RWD']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Brand New', 'Excellent / Like New', 'Good', 'Fair / Needs Work', 'For Parts / Scrap']
                ],
            ],

            'classic_cars' => [
                [
                    'name'          => 'Make & Model',
                    'slug'          => 'make-model',
                    'type'          => 'text',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Year',
                    'slug'          => 'year',
                    'type'          => 'number',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Kilometers / Mileage',
                    'slug'          => 'kilometers',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Restoration Status',
                    'slug'          => 'restoration-status',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['100% Original Survivor', 'Fully Restored (Show Quality)', 'Older Restoration / Driver', 'Running Project Car', 'Barn Find / Needs Full Restoration']
                ],
                [
                    'name'          => 'Transmission',
                    'slug'          => 'transmission',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Manual 4-Speed', 'Manual 5-Speed', 'Automatic', 'Column Shift / 3-on-the-tree']
                ],
            ],

            'motorcycles' => [
                [
                    'name'          => 'Motorcycle Type',
                    'slug'          => 'motorcycle-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Sport Bike', 'Cruiser', 'Touring / Adventure', 'Dirt Bike / Motocross', 'Dual-Sport', 'Scooter / Moped', 'Cafe Racer', 'Custom / Chopper']
                ],
                [
                    'name'          => 'Make',
                    'slug'          => 'make',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Yamaha', 'Honda', 'Kawasaki', 'Suzuki', 'Harley-Davidson', 'BMW', 'Ducati', 'KTM', 'Triumph', 'Vespa', 'Indian', 'Other']
                ],
                [
                    'name'          => 'Year',
                    'slug'          => 'year',
                    'type'          => 'number',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Engine Displacement (cc)',
                    'slug'          => 'engine-cc',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Kilometers',
                    'slug'          => 'kilometers',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
            ],

            'powersports_boats' => [
                [
                    'name'          => 'Vehicle / Craft Type',
                    'slug'          => 'craft-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Bowrider Boat', 'Pontoon Boat', 'Fishing Boat', 'Personal Watercraft (Sea-Doo / Jet Ski)', 'Cabin Cruiser', 'Sailboat', 'Canoe / Kayak', 'ATV / 4x4 Quad', 'Snowmobile / Sled', 'UTV / Side-by-Side']
                ],
                [
                    'name'          => 'Make / Brand',
                    'slug'          => 'make',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Sea-Doo', 'Yamaha', 'Polaris', 'Ski-Doo', 'Can-Am', 'Honda', 'Kawasaki', 'Boston Whaler', 'Bayliner', 'Princecraft', 'Tracker', 'Other']
                ],
                [
                    'name'          => 'Year',
                    'slug'          => 'year',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Trailer Included',
                    'slug'          => 'trailer-included',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Yes (Trailer Included in Price)', 'No (Craft Only)']
                ],
            ],

            'rvs_campers' => [
                [
                    'name'          => 'RV Type',
                    'slug'          => 'rv-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Travel Trailer', 'Fifth Wheel', 'Motorhome Class A', 'Motorhome Class B (Camper Van)', 'Motorhome Class C', 'Pop-up Camper', 'Truck Camper', 'Park Model']
                ],
                [
                    'name'          => 'Year',
                    'slug'          => 'year',
                    'type'          => 'number',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Sleeping Capacity',
                    'slug'          => 'sleeping-capacity',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['2 People', '4 People', '6 People', '8+ People']
                ],
                [
                    'name'          => 'Length (Feet)',
                    'slug'          => 'length-feet',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
            ],

            'auto_parts' => [
                [
                    'name'          => 'Part Category',
                    'slug'          => 'part-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Tires & Rims / Wheels', 'Engine & Transmission', 'Brakes & Suspension', 'Body Panels & Mirrors', 'Interior & Seats', 'Lighting & Headlights', 'Car Audio, GPS & Electronics', 'Exhaust & Catalytic Converters', 'Other Accessories']
                ],
                [
                    'name'          => 'Rim / Tire Size',
                    'slug'          => 'tire-size',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['15 inch', '16 inch', '17 inch', '18 inch', '19 inch', '20 inch', '21+ inch', 'Not Applicable']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New in Box', 'Like New / Barely Used', 'Good Working Condition', 'For Parts / Core']
                ],
            ],

            'heavy_equipment' => [
                [
                    'name'          => 'Equipment Type',
                    'slug'          => 'equipment-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Excavator', 'Skid Steer / Bobcat', 'Tractor', 'Forklift', 'Bulldozer', 'Backhoe Loader', 'Commercial Trailer / Flatbed', 'Compactor / Roller', 'Other Machinery']
                ],
                [
                    'name'          => 'Brand / Manufacturer',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Caterpillar', 'John Deere', 'Kubota', 'Bobcat', 'Komatsu', 'Case', 'New Holland', 'JCB', 'Doosan', 'Other']
                ],
                [
                    'name'          => 'Year',
                    'slug'          => 'year',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Operating Hours',
                    'slug'          => 'operating-hours',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 🏠 HOUSING & REAL ESTATE ARCHETYPES
            // ─────────────────────────────────────────────────────────────
            'apartments_rent' => [
                [
                    'name'          => 'Bedrooms',
                    'slug'          => 'bedrooms',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Bachelor / Studio', '1 Bedroom', '1 Bedroom + Den', '2 Bedrooms', '2 Bedrooms + Den', '3+ Bedrooms']
                ],
                [
                    'name'          => 'Bathrooms',
                    'slug'          => 'bathrooms',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['1 Bathroom', '1.5 Bathrooms', '2 Bathrooms', '2.5 Bathrooms', '3+ Bathrooms']
                ],
                [
                    'name'          => 'Furnished',
                    'slug'          => 'furnished',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Furnished', 'Unfurnished', 'Partially Furnished']
                ],
                [
                    'name'          => 'Pet Friendly',
                    'slug'          => 'pet-friendly',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Yes (Pets Allowed)', 'No Pets', 'Cats Only', 'Small Dogs Only']
                ],
                [
                    'name'          => 'Parking Included',
                    'slug'          => 'parking-included',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['1 Spot Included', '2 Spots Included', 'Available for Extra Fee', 'Street Parking Only', 'No Parking']
                ],
                [
                    'name'          => 'Utilities Included',
                    'slug'          => 'utilities-included',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['All Utilities Included (Heat, Hydro, Water)', 'Heat & Water Only', 'Hydro & Water Only', 'Water Only', 'None (Tenant Pays All)']
                ],
                [
                    'name'          => 'Square Footage',
                    'slug'          => 'square-footage',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
            ],

            'houses_rent' => [
                [
                    'name'          => 'Bedrooms',
                    'slug'          => 'bedrooms',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['2 Bedrooms', '3 Bedrooms', '4 Bedrooms', '5+ Bedrooms']
                ],
                [
                    'name'          => 'Bathrooms',
                    'slug'          => 'bathrooms',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['1.5 Bathrooms', '2 Bathrooms', '2.5 Bathrooms', '3+ Bathrooms', '4+ Bathrooms']
                ],
                [
                    'name'          => 'Pet Friendly',
                    'slug'          => 'pet-friendly',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Yes', 'No', 'Cats Only', 'Small Dogs Welcome']
                ],
                [
                    'name'          => 'Backyard',
                    'slug'          => 'backyard',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Fenced Private Yard', 'Shared Yard', 'Patio / Deck Only', 'None']
                ],
                [
                    'name'          => 'Basement',
                    'slug'          => 'basement',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Finished Full Basement', 'Unfinished Basement', 'Walkout Basement', 'Separate In-Law Suite', 'No Basement']
                ],
                [
                    'name'          => 'Garage / Parking',
                    'slug'          => 'garage-parking',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['1 Car Garage + Driveway', '2 Car Garage + Driveway', 'Driveway Only (No Garage)', 'Street Parking']
                ],
            ],

            'room_rentals' => [
                [
                    'name'          => 'Room Type',
                    'slug'          => 'room-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Private Room', 'Master Bedroom with Ensuite Bath', 'Shared Room', 'Basement Suite Room']
                ],
                [
                    'name'          => 'Bathroom Type',
                    'slug'          => 'bathroom-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Private Attached Ensuite', 'Shared Bathroom with 1 Person', 'Shared Bathroom with Multiple Roommates']
                ],
                [
                    'name'          => 'Furnished',
                    'slug'          => 'furnished',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Fully Furnished (Bed, Desk, Closet)', 'Unfurnished']
                ],
                [
                    'name'          => 'Gender Preference',
                    'slug'          => 'gender-preference',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Any Gender Welcome', 'Female Preferred / Only', 'Male Preferred / Only', 'Couples Welcome']
                ],
                [
                    'name'          => 'Utilities & Wi-Fi',
                    'slug'          => 'utilities-wifi',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['All Utilities & High-Speed Wi-Fi Included', 'Utilities Split Evenly', 'Tenant Pays Share']
                ],
            ],

            'property_sale' => [
                [
                    'name'          => 'Property Type',
                    'slug'          => 'property-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Detached Single Family', 'Semi-Detached', 'Townhouse / Freehold', 'Condo Apartment', 'Condo Townhouse', 'Bungalow', 'Duplex / Triplex / Multi-Family', 'Country Estate / Acreage']
                ],
                [
                    'name'          => 'Bedrooms',
                    'slug'          => 'bedrooms',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['1 Bedroom', '2 Bedrooms', '3 Bedrooms', '4 Bedrooms', '5+ Bedrooms']
                ],
                [
                    'name'          => 'Bathrooms',
                    'slug'          => 'bathrooms',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['1 Bathroom', '2 Bathrooms', '3 Bathrooms', '4 Bathrooms', '5+ Bathrooms']
                ],
                [
                    'name'          => 'Square Footage',
                    'slug'          => 'square-footage',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Garage Spaces',
                    'slug'          => 'garage-spaces',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['1 Car Garage', '2 Car Garage', '3+ Car Garage', 'Underground Owned Spot', 'Driveway Only', 'No Garage']
                ],
                [
                    'name'          => 'Year Built',
                    'slug'          => 'year-built',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
            ],

            'commercial_real_estate' => [
                [
                    'name'          => 'Space Type',
                    'slug'          => 'space-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Office Space', 'Retail Storefront', 'Industrial / Warehouse', 'Medical / Dental Clinic', 'Restaurant / Food Service', 'Flex Commercial Space']
                ],
                [
                    'name'          => 'Square Footage',
                    'slug'          => 'square-footage',
                    'type'          => 'number',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Lease Type',
                    'slug'          => 'lease-type',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Triple Net (NNN)', 'Gross Lease', 'Modified Gross', 'Sublease', 'Property For Sale']
                ],
            ],

            'land_sale' => [
                [
                    'name'          => 'Zoning / Land Use',
                    'slug'          => 'land-use',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Residential Building Lot', 'Agricultural / Farm Land', 'Commercial Development', 'Recreational / Cottage Lot', 'Industrial Land']
                ],
                [
                    'name'          => 'Lot Size / Acreage',
                    'slug'          => 'lot-size',
                    'type'          => 'text',
                    'is_required'   => true,
                    'is_filterable' => false,
                ],
                [
                    'name'          => 'Servicing',
                    'slug'          => 'servicing',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Fully Serviced (Municipal Water & Sewer)', 'Well & Septic Required', 'Hydro at Road Only', 'Unserviced / Off-Grid']
                ],
            ],

            'storage_parking' => [
                [
                    'name'          => 'Space Type',
                    'slug'          => 'space-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Indoor Heated Storage Locker', 'Outdoor RV / Boat / Vehicle Storage', 'Underground Parking Space', 'Outdoor Driveway Spot', 'Commercial Warehouse Bay']
                ],
                [
                    'name'          => 'Access Type',
                    'slug'          => 'access-type',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['24/7 Gated / Key Fob Access', 'Business Hours Only', 'By Appointment']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 📱 BUY & SELL / ELECTRONICS ARCHETYPES
            // ─────────────────────────────────────────────────────────────
            'smartphones' => [
                [
                    'name'          => 'Brand',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Apple', 'Samsung', 'Google Pixel', 'OnePlus', 'Xiaomi', 'Motorola', 'Sony', 'Huawei', 'Other']
                ],
                [
                    'name'          => 'Storage Capacity',
                    'slug'          => 'storage-capacity',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['64 GB', '128 GB', '256 GB', '512 GB', '1 TB']
                ],
                [
                    'name'          => 'Carrier Lock',
                    'slug'          => 'carrier-lock',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Factory Unlocked', 'Rogers', 'Bell', 'Telus', 'Freedom Mobile', 'Fido / Koodo / Virgin']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New (Sealed in Box)', 'Like New / Mint (100% Battery)', 'Good Condition (Minor Wear)', 'Fair / Screen Scratches', 'For Parts / Cracked Screen']
                ],
            ],

            'computers_laptops' => [
                [
                    'name'          => 'Brand',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Apple', 'Dell', 'Lenovo', 'HP', 'ASUS', 'Acer', 'Microsoft Surface', 'Razer', 'MSI', 'Custom Built Gaming PC', 'Other']
                ],
                [
                    'name'          => 'Device Category',
                    'slug'          => 'device-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Laptop / MacBook', 'Desktop / iMac', 'Gaming Rig / Tower', 'Tablet / iPad', '2-in-1 Touch Convertible', 'All-in-One PC']
                ],
                [
                    'name'          => 'RAM Memory',
                    'slug'          => 'ram',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['8 GB', '16 GB', '32 GB', '64 GB', '128 GB+']
                ],
                [
                    'name'          => 'Storage Size',
                    'slug'          => 'storage-size',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['256 GB SSD', '512 GB SSD', '1 TB SSD', '2 TB SSD', '4 TB+ SSD']
                ],
                [
                    'name'          => 'Processor / Chip',
                    'slug'          => 'processor',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Apple M1 / M2 / M3 / M4', 'Intel Core i5 / Ultra 5', 'Intel Core i7 / Ultra 7', 'Intel Core i9 / Ultra 9', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New (Sealed)', 'Like New / Mint', 'Gently Used / Fully Functional', 'For Parts / Repair']
                ],
            ],

            'video_games_consoles' => [
                [
                    'name'          => 'Gaming Platform',
                    'slug'          => 'platform',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['PlayStation 5', 'PlayStation 4', 'Xbox Series X / S', 'Xbox One', 'Nintendo Switch / OLED', 'PC Gaming', 'Retro (PS1/PS2/N64/GameCube)', 'VR (Meta Quest/PSVR)']
                ],
                [
                    'name'          => 'Item Category',
                    'slug'          => 'item-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Console System Bundle', 'Video Game Disc / Cartridge', 'Controllers & Gamepads', 'Headsets & Audio', 'Accessories & Memory']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New Sealed', 'Complete in Box (CIB)', 'Loose / Game Only', 'Tested & Working', 'For Parts']
                ],
            ],

            'audio_electronics' => [
                [
                    'name'          => 'Audio / Electronics Type',
                    'slug'          => 'audio-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Television (4K / OLED / QLED)', 'Home Theater Soundbar / Subwoofer', 'Wireless Bluetooth Headphones / Earbuds', 'Hi-Fi Stereo Receiver & Amplifier', 'Floorstanding / Bookshelf Speakers', 'Turntable / Record Player', 'Smart Home / Streaming Box']
                ],
                [
                    'name'          => 'Brand',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Sony', 'Samsung', 'LG', 'Bose', 'Sonos', 'Apple / Beats', 'Yamaha', 'Denon', 'JBL', 'Sennheiser', 'Audio-Technica', 'Other']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New Sealed', 'Like New / Mint', 'Good Used Condition', 'Vintage / Needs Servicing']
                ],
            ],

            'cameras' => [
                [
                    'name'          => 'Camera / Gear Type',
                    'slug'          => 'camera-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Mirrorless Camera Body', 'DSLR Camera Body', 'Camera Lens (Prime/Zoom)', 'Point & Shoot Compact', 'Action Camera (GoPro/Insta360)', 'Drone with Camera', 'Vintage 35mm Film Camera', 'Tripods & Lighting Accessories']
                ],
                [
                    'name'          => 'Brand',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Sony', 'Canon', 'Nikon', 'Fujifilm', 'Panasonic Lumix', 'Leica', 'DJI', 'GoPro', 'Sigma / Tamron', 'Other']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New / Sealed', 'Mint (Low Shutter Count)', 'Good Used (Fully Functional)', 'For Parts / Sensor Defect']
                ],
            ],

            'furniture_home' => [
                [
                    'name'          => 'Furniture Category',
                    'slug'          => 'furniture-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Sofa, Couch & Sectional', 'Dining Table & Chairs Set', 'Bed Frame & Headboard', 'Mattress & Boxspring', 'Office Desk & Ergonomic Chair', 'Coffee Table & Side Tables', 'Dressers, Wardrobes & Storage', 'Bookcases & Shelving Units', 'Patio & Outdoor Furniture']
                ],
                [
                    'name'          => 'Material',
                    'slug'          => 'material',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Solid Hardwood', 'Genuine Leather', 'Fabric / Velvet', 'Engineered Wood / MDF', 'Metal & Steel', 'Glass / Tempered Glass', 'Marble / Stone']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New in Box', 'Like New / Smoke-Free Home', 'Gently Used (No Stains/Tears)', 'Good Vintage Condition', 'Needs Minor TLC / Reupholstery']
                ],
            ],

            'clothing_shoes' => [
                [
                    'name'          => 'Department',
                    'slug'          => 'department',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ["Men's", "Women's", 'Unisex Adult', "Kids / Youth", "Baby / Toddler"]
                ],
                [
                    'name'          => 'Clothing / Apparel Type',
                    'slug'          => 'apparel-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Jackets, Coats & Winter Parkas', 'Hoodies, Sweaters & Cardigans', 'T-Shirts & Tops', 'Pants, Jeans & Leggings', 'Dresses & Skirts', 'Sneakers & Athletic Shoes', 'Boots & Winter Footwear', 'Handbags, Purses & Backpacks', 'Hats, Belts & Accessories']
                ],
                [
                    'name'          => 'Size',
                    'slug'          => 'size',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Extra Small (XS)', 'Small (S)', 'Medium (M)', 'Large (L)', 'Extra Large (XL)', 'XXL / Plus Size', 'Shoe Size 5-7', 'Shoe Size 8-10', 'Shoe Size 11-13', 'One Size / Adjustable']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['New with Tags (NWT)', 'New without Tags (NWOT)', 'Like New / Worn Once', 'Gently Used Pre-owned', 'Vintage / Distressed']
                ],
            ],

            'jewelry_watches' => [
                [
                    'name'          => 'Item Category',
                    'slug'          => 'jewelry-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Luxury Wristwatch', 'Smartwatch (Apple Watch / Garmin)', 'Ring / Engagement & Wedding Band', 'Necklace & Pendant', 'Bracelet / Bangle', 'Earrings', 'Brooch / Luxury Accessory', 'Precious Metal / Bullion']
                ],
                [
                    'name'          => 'Material / Metal',
                    'slug'          => 'material-metal',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['10K / 14K / 18K Yellow Gold', 'White Gold / Platinum', 'Rose Gold', '925 Sterling Silver', 'Stainless Steel / Titanium', 'Diamonds & Gemstones', 'Fashion / Costume Jewelry']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New with Box & Papers', 'Excellent / Serviced Condition', 'Gently Used', 'Vintage Collectible']
                ],
            ],

            'musical_instruments' => [
                [
                    'name'          => 'Instrument Type',
                    'slug'          => 'instrument-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Acoustic Guitar', 'Electric Guitar', 'Bass Guitar', 'Digital Piano & Synthesizer', 'Acoustic Piano', 'Drums & Percussion Set', 'Saxophone, Trumpet & Brass', 'Violin, Cello & Strings', 'DJ Gear & Production Controllers', 'Guitar Amplifiers & Pedals']
                ],
                [
                    'name'          => 'Brand',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Fender', 'Gibson', 'Yamaha', 'Roland', 'Ibanez', 'Taylor', 'Martin', 'Epiphone', 'Casio', 'Pioneer DJ', 'Marshall', 'Boss', 'Other']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New in Box', 'Mint / Studio Use Only', 'Player Grade (Minor Dings)', 'Vintage / Collector Item']
                ],
            ],

            'bikes_cycling' => [
                [
                    'name'          => 'Bicycle Type',
                    'slug'          => 'bicycle-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Mountain Bike (Hardtail / Full Suspension)', 'Road Bike / Racing', 'Hybrid / City Commuter', 'Electric Bike (E-Bike)', 'Gravel / Cyclocross Bike', 'BMX & Dirt Jumper', 'Cruiser / Fat Tire Bike', 'Kids / Youth Bike']
                ],
                [
                    'name'          => 'Frame Size',
                    'slug'          => 'frame-size',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Small (14-16" / Rider 5\'2"-5\'6")', 'Medium (17-18" / Rider 5\'6"-5\'10")', 'Large (19-20" / Rider 5\'10"-6\'2")', 'Extra Large (21"+ / Rider 6\'2"+)', 'Youth / Adjustable']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New', 'Like New / Freshly Tuned Up', 'Good Ready-to-Ride Condition', 'Needs Minor Tune-Up / Brakes / Tubes']
                ],
            ],

            'tools_hardware' => [
                [
                    'name'          => 'Tool Category',
                    'slug'          => 'tool-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Cordless Drills, Drivers & Kits', 'Saws (Miter, Table, Circular, Reciprocating)', 'Air Compressors & Pneumatics', 'Hand Tools & Mechanic Socket Sets', 'Lawn Mowers, Trimmers & Snow Blowers', 'Tool Chests, Boxes & Workbenches', 'Generators & Inverters', 'Welding & Torches']
                ],
                [
                    'name'          => 'Brand',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Milwaukee', 'DeWalt', 'Makita', 'Bosch', 'Ryobi', 'Craftsman', 'Ridgid', 'Snap-On', 'Stihl', 'Toro', 'Honda', 'Other']
                ],
                [
                    'name'          => 'Power Source',
                    'slug'          => 'power-source',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Cordless Battery (18V/20V/M18)', 'Corded Electric (110V/220V)', 'Gasoline / 2-Stroke', 'Manual Hand Powered']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New in Box', 'Like New (Tested Working 100%)', 'Used (Good Working Condition)', 'For Parts / Repair']
                ],
            ],

            'sporting_goods' => [
                [
                    'name'          => 'Sport / Activity',
                    'slug'          => 'sport-activity',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Gym & Strength Training (Dumbbells/Barbells)', 'Cardio Equipment (Treadmill, Bike, Rower)', 'Ice Hockey & Skates', 'Golf (Clubs, Bags, Accessories)', 'Skiing & Snowboarding Gear', 'Camping, Tents & Hiking Equipment', 'Tennis, Pickleball & Racket Sports', 'Fishing Rods, Reels & Tackle', 'Water Sports, Kayaks & SUPs']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New Sealed', 'Like New Pre-owned', 'Good Condition / Fully Usable']
                ],
            ],

            'baby_toys' => [
                [
                    'name'          => 'Item Category',
                    'slug'          => 'baby-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Strollers & Travel Systems', 'Car Seats & Boosters (Non-Expired)', 'Cribs, Bassinets & Nursery Furniture', 'High Chairs & Feeding Accessories', 'Baby Monitors & Safety Gates', 'Toys, LEGO & Educational Games', 'Baby Clothing & Sleepers']
                ],
                [
                    'name'          => 'Age Suitability',
                    'slug'          => 'age-suitability',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Newborn (0-6 Months)', 'Infant (6-12 Months)', 'Toddler (1-3 Years)', 'Kids (4-7 Years)', 'Big Kids / Youth (8+ Years)']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New in Box', 'Like New / Sanitized', 'Gently Used']
                ],
            ],

            'books_collectibles' => [
                [
                    'name'          => 'Collectibles Category',
                    'slug'          => 'collectible-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Books & Textbooks', 'Vinyl Records, LPs & Albums', 'Trading Cards (Pokemon, Sports, MTG)', 'Action Figures & Funko Pops', 'Comic Books & Graphic Novels', 'Coins, Banknotes & Stamps', 'Art, Paintings & Sculptures', 'Board Games & Puzzles']
                ],
                [
                    'name'          => 'Condition / Grade',
                    'slug'          => 'condition-grade',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Gem Mint / PSA / Graded', 'Brand New / Sealed', 'Near Mint / Like New', 'Very Good (Minor Age Wear)', 'Fair / Reader Copy']
                ],
            ],

            'home_garden' => [
                [
                    'name'          => 'Item Type',
                    'slug'          => 'garden-item-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Patio Dining Sets & Umbrellas', 'BBQ Grills & Smokers', 'Lawn Care & Garden Planters', 'Outdoor Fire Pits & Heaters', 'Indoor Houseplants & Pots', 'Home Decor & Wall Art', 'Lighting, Lamps & Fixtures']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New In Box', 'Like New', 'Gently Used Outdoor Ready']
                ],
            ],

            'health_beauty' => [
                [
                    'name'          => 'Category Type',
                    'slug'          => 'beauty-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Hair Styling Tools (Dyson, Dryers, Straighteners)', 'Skincare & Facial Devices', 'Fragrances & Perfumes (Authentic)', 'Makeup & Cosmetics', 'Massage & Wellness Devices', 'Mobility & Medical Equipment (Wheelchairs, Walkers)']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New Sealed in Box', 'Like New / Tested Once', 'Sanitized & Fully Working']
                ],
            ],

            'general_goods' => [
                [
                    'name'          => 'Item Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New in Box', 'Like New / Mint', 'Gently Used', 'Fair / Functional', 'For Parts / Crafting']
                ],
                [
                    'name'          => 'Brand / Manufacturer',
                    'slug'          => 'brand',
                    'type'          => 'text',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Original Packaging Included',
                    'slug'          => 'original-packaging',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => false,
                    'options'       => ['Yes (With Original Box & Manual)', 'No (Item Only)']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 💼 JOBS & EMPLOYMENT ARCHETYPES
            // ─────────────────────────────────────────────────────────────
            'jobs_employment' => [
                [
                    'name'          => 'Job Type',
                    'slug'          => 'job-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Full-time', 'Part-time', 'Contract / Project-Based', 'Casual / On-Call', 'Internship / Co-op', 'Seasonal']
                ],
                [
                    'name'          => 'Work Location',
                    'slug'          => 'work-location',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Remote (Work From Home)', 'Hybrid (Office + Remote)', 'On-site / In-Person']
                ],
                [
                    'name'          => 'Experience Level',
                    'slug'          => 'experience-level',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Entry Level / Student', 'Junior (1-2 Years)', 'Intermediate (3-5 Years)', 'Senior (5+ Years)', 'Manager / Director / Executive']
                ],
                [
                    'name'          => 'Compensation Basis',
                    'slug'          => 'compensation-basis',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Hourly Wage ($/hr)', 'Annual Salary ($/yr)', 'Commission + Base', 'Flat Project Fee / Piecework']
                ],
                [
                    'name'          => 'Required Education / License',
                    'slug'          => 'education-license',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => false,
                    'options'       => ['No Formal Requirement', 'High School Diploma', 'College Diploma / Trade Certification', 'Bachelor\'s Degree', 'Master\'s / Professional License (CPA/P.Eng/MD)']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 🛠️ SERVICES ARCHETYPES
            // ─────────────────────────────────────────────────────────────
            'services_home_trade' => [
                [
                    'name'          => 'Service Specialty',
                    'slug'          => 'service-specialty',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Full Home Remodeling', 'Kitchen & Bathroom Reno', 'Flooring & Tiling', 'Plumbing & Drain Services', 'Electrical & Lighting Installation', 'Painting & Drywall Repair', 'Roofing, Eavestrough & Siding', 'Deck, Fence & Landscaping', 'General Handyman Services']
                ],
                [
                    'name'          => 'Licensed & Insured',
                    'slug'          => 'licensed-insured',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Yes (Fully Licensed, WSIB & Liability Insured)', 'Certified Master Tradesperson', 'Experienced Handyman Services']
                ],
                [
                    'name'          => 'Free Estimates Available',
                    'slug'          => 'free-estimates',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Yes (Free In-Home or Virtual Quote)', 'Paid Consultation (Deducted from Work)']
                ],
                [
                    'name'          => 'Emergency Service Availability',
                    'slug'          => 'emergency-service',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['24/7 Emergency Dispatch Available', 'Same-Day Service', 'Scheduled by Appointment']
                ],
            ],

            'services_cleaning' => [
                [
                    'name'          => 'Cleaning Service Type',
                    'slug'          => 'service-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Residential Home Cleaning', 'Deep Clean / Move-In & Move-Out', 'Commercial Office & Janitorial', 'Post-Construction Cleaning', 'Carpet, Tile & Upholstery Steam Clean', 'Window & Pressure Washing']
                ],
                [
                    'name'          => 'Frequency Offered',
                    'slug'          => 'frequency',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['One-Time Deep Clean', 'Weekly Recurring Service', 'Bi-Weekly Recurring', 'Monthly Recurring', 'Custom Flexible Schedule']
                ],
                [
                    'name'          => 'Supplies Provided',
                    'slug'          => 'supplies-provided',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => false,
                    'options'       => ['All Cleaning Supplies & Equipment Provided', 'Eco-Friendly & Non-Toxic Products Provided', 'Client Supplies Preferred']
                ],
            ],

            'services_tech' => [
                [
                    'name'          => 'Tech Service Type',
                    'slug'          => 'service-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['PC & Mac Repair / Diagnostic', 'Virus, Malware & Ransomware Removal', 'Data Recovery & Cloud Backup', 'Home & Office Wi-Fi / Networking', 'Custom PC Building & Hardware Upgrades', 'Web Design & App Development', 'Smart Home & TV Mounting Setup']
                ],
                [
                    'name'          => 'Service Format',
                    'slug'          => 'service-format',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['On-Site Mobile Visit', 'Remote Support (AnyDesk / TeamViewer)', 'Drop-off at Workshop']
                ],
            ],

            'services_tutoring' => [
                [
                    'name'          => 'Subject / Area of Instruction',
                    'slug'          => 'subject',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Math, Calculus & Statistics', 'Physics, Chemistry & Biology', 'English Language, Essay & Reading', 'French Language (Immersion & ESL)', 'Computer Science & Coding', 'Music & Instrument Lessons', 'Test Prep (IELTS, TOEFL, SAT, GMAT)', 'Driving Lessons (MTO Certified)']
                ],
                [
                    'name'          => 'Student Level',
                    'slug'          => 'student-level',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Elementary School (K-8)', 'High School (Grades 9-12)', 'University & College Undergraduate', 'Adult & Professional Learning']
                ],
                [
                    'name'          => 'Lesson Delivery',
                    'slug'          => 'lesson-delivery',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Online 1-on-1 (Zoom / Google Meet)', 'In-Person at Student\'s Home', 'In-Person at Tutor\'s Studio / Library', 'Small Group Interactive Sessions']
                ],
            ],

            'services_financial_legal' => [
                [
                    'name'          => 'Professional Specialty',
                    'slug'          => 'service-specialty',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Personal & Corporate Tax Filing', 'Bookkeeping & Payroll Services', 'Legal Consultation & Notary Public', 'Immigration Consultation', 'Mortgage & Loan Brokerage', 'Real Estate Buying / Selling Agent', 'Business Plan & Corporate Incorporation']
                ],
                [
                    'name'          => 'Accreditation / Credentials',
                    'slug'          => 'credentials',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Chartered Professional Accountant (CPA)', 'Licensed Lawyer / Barrister & Solicitor', 'Appointed Notary Public / Commissioner', 'Licensed Immigration Consultant (RCIC)', 'Licensed Real Estate Broker (RECO/OACIQ)', 'Certified Financial Planner (CFP)']
                ],
                [
                    'name'          => 'Consultation Format',
                    'slug'          => 'consultation-format',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => false,
                    'options'       => ['Free Initial 15-Min Phone/Video Consultation', 'In-Person Office Meeting', 'Comprehensive Written Assessment']
                ],
            ],

            'services_childcare' => [
                [
                    'name'          => 'Care Type',
                    'slug'          => 'care-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Full-Time Nanny / Au Pair', 'Part-Time Babysitting (Evenings/Weekends)', 'Licensed Home Daycare', 'After-School Care & Pickup', 'Senior Companion & Caregiver']
                ],
                [
                    'name'          => 'Certifications Held',
                    'slug'          => 'certifications',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['First Aid & CPR Certified', 'Early Childhood Education (ECE)', 'Police Vulnerable Sector Check (Clean Record)', 'Valid Driver\'s License & Clean Record']
                ],
            ],

            'services_moving_hauling' => [
                [
                    'name'          => 'Moving / Transport Service',
                    'slug'          => 'service-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Full House / Apartment Move', 'Single Item / Furniture Delivery (Kijiji/IKEA)', 'Office & Commercial Relocation', 'Junk Removal & Estate Cleanout', 'Piano & Heavy Safe Moving', 'Long Distance Interprovincial Moving']
                ],
                [
                    'name'          => 'Truck Size & Crew',
                    'slug'          => 'truck-crew',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Cargo Van + 1 Mover', '16ft Truck + 2 Movers', '24ft-26ft Large Truck + 3 Movers', 'Labour Only (No Truck)']
                ],
            ],

            'services_events_creative' => [
                [
                    'name'          => 'Creative Service Type',
                    'slug'          => 'service-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Wedding & Portrait Photography', 'Event & Commercial Videography', 'DJ, Sound & Party Lighting', 'Catering & Private Chef Service', 'Event Planning & Decoration', 'Graphic Design & Branding', 'Hair, Makeup & Bridal Styling']
                ],
                [
                    'name'          => 'Portfolio / Sample Link Available',
                    'slug'          => 'portfolio-available',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => false,
                    'options'       => ['Yes (Instagram / Website in Listing)', 'Available Upon Request']
                ],
            ],

            'services_general' => [
                [
                    'name'          => 'Service Type',
                    'slug'          => 'service-type',
                    'type'          => 'text',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Service Area',
                    'slug'          => 'service-area',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Citywide / Mobile', 'In-Shop / Studio Only', 'Province-Wide', 'Remote / Online Canada-Wide']
                ],
                [
                    'name'          => 'Free Consultation / Quote',
                    'slug'          => 'free-quote',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Yes (Free Quote)', 'Fixed Hourly Rate', 'Flat Project Fee']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 🐾 PETS ARCHETYPES
            // ─────────────────────────────────────────────────────────────
            'pets_dogs' => [
                [
                    'name'          => 'Breed',
                    'slug'          => 'breed',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Labrador Retriever', 'Golden Retriever', 'French Bulldog', 'German Shepherd', 'Poodle / Doodle Mix', 'Husky', 'Beagle', 'Shih Tzu', 'Pug', 'Yorkshire Terrier', 'Rottweiler', 'Mixed Breed / Rescue Pup', 'Other Breed']
                ],
                [
                    'name'          => 'Age Group',
                    'slug'          => 'age-group',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Puppy (Under 6 Months)', 'Young (6-12 Months)', 'Adult (1-7 Years)', 'Senior (7+ Years)']
                ],
                [
                    'name'          => 'Vaccinations & Health',
                    'slug'          => 'vaccinations',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Up to Date on All Shots & De-wormed', 'First Puppy Shots Complete', 'Vet Checked with Health Guarantee', 'Needs Vaccinations']
                ],
                [
                    'name'          => 'Spayed / Neutered',
                    'slug'          => 'spayed-neutered',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Yes (Spayed / Neutered)', 'No (Intact)', 'Breeding Rights Available']
                ],
                [
                    'name'          => 'Size at Maturity',
                    'slug'          => 'size-maturity',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Toy / Micro (Under 10 lbs)', 'Small (10-25 lbs)', 'Medium (25-50 lbs)', 'Large (50-80 lbs)', 'Extra Large (80+ lbs)']
                ],
            ],

            'pets_cats' => [
                [
                    'name'          => 'Breed / Type',
                    'slug'          => 'breed',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Domestic Short Hair (DSH)', 'Domestic Long Hair (DLH)', 'Maine Coon', 'Siamese', 'Ragdoll', 'Bengal', 'British Shorthair', 'Persian', 'Sphynx', 'Tuxedo / Tabby Rescue', 'Other Breed']
                ],
                [
                    'name'          => 'Age Group',
                    'slug'          => 'age-group',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Kitten (Under 6 Months)', 'Young (6-12 Months)', 'Adult (1-7 Years)', 'Senior (7+ Years)']
                ],
                [
                    'name'          => 'Health & Shots',
                    'slug'          => 'health-shots',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Up to Date on Vaccines & Vet Checked', 'First Kitten Shots Done', 'Needs Vet Exam']
                ],
                [
                    'name'          => 'Litter Box Trained',
                    'slug'          => 'litter-trained',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Yes (100% Litter Box Trained)', 'In Training']
                ],
            ],

            'pets_small_animals' => [
                [
                    'name'          => 'Species / Variety',
                    'slug'          => 'species',
                    'type'          => 'text',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Cage / Tank / Setup Included',
                    'slug'          => 'setup-included',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Full Cage / Aquarium Tank Setup Included', 'Animal Only (Setup Sold Separately)']
                ],
                [
                    'name'          => 'Health Status',
                    'slug'          => 'health-status',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Healthy & Active', 'Vet Checked', 'Hand Tamed']
                ],
            ],

            'pets_services' => [
                [
                    'name'          => 'Pet Service Offered',
                    'slug'          => 'service-offered',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Dog Walking (Private or Pack)', 'In-Home Pet Sitting & Drop-In Visits', 'Cage-Free Pet Boarding', 'Dog Grooming, Bath & Nail Trimming', 'Dog Training & Behaviour Modification']
                ],
                [
                    'name'          => 'Pet Types Accepted',
                    'slug'          => 'pet-types',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Dogs of All Sizes', 'Small Dogs Only', 'Cats Only', 'Dogs & Cats', 'Birds, Reptiles & Small Animals']
                ],
            ],

            'pets_supplies' => [
                [
                    'name'          => 'Supply Category',
                    'slug'          => 'supply-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Crates, Cages, Kennels & Carriers', 'Aquarium Tanks, Filters & Accessories', 'Cat Trees, Scratching Posts & Litter Boxes', 'Dog Beds, Apparel & Harnesses', 'Premium Pet Food & Supplements', 'Toys, Training Pads & Grooming Tools']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New in Box', 'Like New / Sanitized', 'Gently Used']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 🚗 RIDESHARING & CARPOOL ARCHETYPES
            // ─────────────────────────────────────────────────────────────
            'rideshare_carpool' => [
                [
                    'name'          => 'Trip Type',
                    'slug'          => 'trip-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Daily Work / Campus Commute', 'Intercity Weekend Trip (e.g. Toronto-Montreal, Ottawa)', 'Airport Ride / Shuttle', 'Cottage / Ski Trip (Whistler / Mont Tremblant)', 'Cross-Border Trip (US / Canada)']
                ],
                [
                    'name'          => 'Available Seats',
                    'slug'          => 'available-seats',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['1 Seat Available', '2 Seats Available', '3 Seats Available', '4+ Seats Available']
                ],
                [
                    'name'          => 'Luggage Capacity',
                    'slug'          => 'luggage-capacity',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Backpack / Small Handbag Only', '1 Medium Suitcase per Passenger', 'Large Luggage Allowed (Large Trunk)', 'Ski / Snowboard Gear Allowed']
                ],
                [
                    'name'          => 'Pet Policy',
                    'slug'          => 'pet-policy',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['No Pets Allowed', 'Small Pets in Carrier Welcome', 'Dog Friendly Ride']
                ],
                [
                    'name'          => 'Smoking / Vaping',
                    'slug'          => 'smoking-policy',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Strictly Non-Smoking Vehicle', 'Vaping Allowed', 'Smoke Stops on Request']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 🤝 COMMUNITY & NEED COMPANIONSHIP ARCHETYPES
            // ─────────────────────────────────────────────────────────────
            'community_social' => [
                [
                    'name'          => 'Meetup & Activity Focus',
                    'slug'          => 'meetup-activity',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['☕ Coffee & Casual Chat', '🚶 Walk in the Park & Stroll', '🍽️ Dining, Food Tours & Brunch', '🎬 Cinema, Movies & Concerts', '⚽ Live Sports & Playing Matches', '🏃 Workout, Running & Gym Partner', '🎮 Video Games & Board Games', '🌆 City Exploring & Museums', '🗣️ Language Exchange & Conversation', '🤝 Volunteer Work & Mutual Aid']
                ],
                [
                    'name'          => 'Preferred Timing',
                    'slug'          => 'timing',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Weekend Afternoons', 'Weekday Evenings', 'Morning Coffee (Weekdays)', 'Flexible Weekends / Anytime']
                ],
                [
                    'name'          => 'Group Format',
                    'slug'          => 'group-format',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['1-on-1 Friendly Chat', 'Small Group (3-5 People)', 'Open Social Group / Public Event']
                ],
                [
                    'name'          => 'Cost / Budget',
                    'slug'          => 'cost-budget',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['100% Free / Dutch (Everyone pays their own coffee/ticket)', 'Low Cost', 'Split Costs Evenly']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 🏖️ VACATION RENTALS ARCHETYPES
            // ─────────────────────────────────────────────────────────────
            'vacation_rentals' => [
                [
                    'name'          => 'Property Style',
                    'slug'          => 'property-style',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Lakefront Cottage & Cabin', 'Mountain Chalet & Ski Lodge', 'Waterfront Beach Villa', 'Rustic Log Home', 'Country Farmhouse & Ranch', 'Resort Townhome & Condo', 'Glamping & Yurts', 'Bed & Breakfast Suite']
                ],
                [
                    'name'          => 'Bedrooms',
                    'slug'          => 'bedrooms',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['1 Bedroom', '2 Bedrooms', '3 Bedrooms', '4 Bedrooms', '5+ Bedrooms']
                ],
                [
                    'name'          => 'Bathrooms',
                    'slug'          => 'bathrooms',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['1 Bathroom', '2 Bathrooms', '3 Bathrooms', '4+ Bathrooms']
                ],
                [
                    'name'          => 'Max Guests (Sleeps)',
                    'slug'          => 'max-guests',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Up to 2 Guests (Couples)', 'Up to 4 Guests', 'Up to 6 Guests', 'Up to 8 Guests', '10+ Guests (Large Families / Groups)']
                ],
                [
                    'name'          => 'Key Amenities',
                    'slug'          => 'amenities',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Hot Tub / Jacuzzi & Sauna', 'Private Waterfront Dock & Kayaks', 'Fire Pit, Wood Stove & BBQ Grill', 'High-Speed Starlink Wi-Fi (Remote Work Ready)', 'Indoor Heated Pool / Game Room', 'Ski-in / Ski-out Access']
                ],
                [
                    'name'          => 'Pet Policy',
                    'slug'          => 'pet-policy',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Pet Friendly (Dogs Welcome)', 'No Pets Allowed', 'Inquire with Host']
                ],
            ],
        ];
    }
}
