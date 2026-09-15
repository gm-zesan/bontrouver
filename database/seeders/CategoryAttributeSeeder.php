<?php

namespace Database\Seeders;

use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schema = [
            // ─── CARS & VEHICLES ───
            'cars-trucks' => [
                [
                    'name'          => 'Make',
                    'slug'          => 'make',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes-Benz', 'Audi', 'Hyundai', 'Tesla', 'Chevrolet', 'Subaru', 'Nissan', 'Mazda', 'Porsche', 'Volkswagen']
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
            ],

            // ─── HOUSING: APARTMENTS & CONDOS FOR RENT ───
            'apartments-condos-rent' => [
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
                    'options'       => ['Yes', 'No', 'Cats Only', 'Dogs Only']
                ],
                [
                    'name'          => 'Parking Included',
                    'slug'          => 'parking-included',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['1 Spot Included', '2 Spots Included', 'Available for Extra Fee', 'Street Parking', 'None']
                ],
                [
                    'name'          => 'Square Footage',
                    'slug'          => 'square-footage',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
                ],
            ],

            // ─── HOUSING: HOUSES FOR RENT ───
            'houses-rent' => [
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
                    'options'       => ['Yes', 'No']
                ],
                [
                    'name'          => 'Backyard',
                    'slug'          => 'backyard',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => false,
                    'options'       => ['Fenced Private Yard', 'Shared Yard', 'Patio / Deck Only', 'None']
                ],
            ],

            // ─── ELECTRONICS: SMARTPHONES & TABLETS ───
            'smartphones-tablets' => [
                [
                    'name'          => 'Brand',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Apple', 'Samsung', 'Google Pixel', 'OnePlus', 'Xiaomi', 'Motorola', 'Sony', 'Other']
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
                    'options'       => ['Factory Unlocked', 'Rogers', 'Bell', 'Telus', 'Freedom Mobile']
                ],
                [
                    'name'          => 'Color',
                    'slug'          => 'color',
                    'type'          => 'text',
                    'is_required'   => false,
                    'is_filterable' => false,
                ],
            ],

            // ─── ELECTRONICS: LAPTOPS & COMPUTERS ───
            'laptops-computers' => [
                [
                    'name'          => 'Brand',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Apple', 'Dell', 'Lenovo', 'HP', 'ASUS', 'Acer', 'Microsoft Surface', 'Razer', 'Custom PC']
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
                    'name'          => 'RAM',
                    'slug'          => 'ram',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['8 GB', '16 GB', '32 GB', '64 GB', '128 GB']
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
                    'name'          => 'Screen Size',
                    'slug'          => 'screen-size',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['13 inch', '14 inch', '15 inch', '16 inch', '17 inch+']
                ],
            ],

            // ─── BUY & SELL: FURNITURE & HOME ───
            'furniture-home' => [
                [
                    'name'          => 'Furniture Type',
                    'slug'          => 'furniture-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Sofa & Couch', 'Dining Table & Chairs', 'Bed & Mattress', 'Desk & Office Chair', 'Coffee Table', 'Dressers & Storage', 'Bookcases & Shelving', 'Outdoor Patio']
                ],
                [
                    'name'          => 'Material',
                    'slug'          => 'material',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Solid Wood', 'Leather', 'Velvet / Fabric', 'Glass', 'Metal', 'Engineered Wood / MDF']
                ],
                [
                    'name'          => 'Color',
                    'slug'          => 'color',
                    'type'          => 'text',
                    'is_required'   => false,
                    'is_filterable' => false,
                ],
            ],

            // ─── JOBS: SOFTWARE & TECH ───
            'software-development' => [
                [
                    'name'          => 'Job Type',
                    'slug'          => 'job-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Full-time', 'Part-time', 'Contract / Freelance', 'Internship']
                ],
                [
                    'name'          => 'Work Location',
                    'slug'          => 'work-location',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Remote', 'Hybrid', 'On-site']
                ],
                [
                    'name'          => 'Experience Level',
                    'slug'          => 'experience-level',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Junior (0-2 yrs)', 'Intermediate (3-5 yrs)', 'Senior (5+ yrs)', 'Lead / Principal']
                ],
            ],

            // ─── SERVICES: HOME RENOVATIONS & REPAIR ───
            'home-renovations-repair' => [
                [
                    'name'          => 'Service Specialty',
                    'slug'          => 'service-specialty',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Full Home Remodeling', 'Kitchen & Bathroom Reno', 'Flooring & Tiling', 'Plumbing', 'Electrical & Lighting', 'Painting & Drywall', 'Roofing & Siding']
                ],
                [
                    'name'          => 'Licensed & Insured',
                    'slug'          => 'licensed-insured',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Yes (Fully Licensed & WSIB/Insured)', 'Certified Technician', 'Handyman Services']
                ],
            ],
        ];

        $totalAttributes = 0;
        $totalOptions = 0;

        foreach ($schema as $categorySlug => $attributes) {
            $category = Category::where('slug', $categorySlug)->first();

            if (!$category) {
                continue;
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
        }

        $this->command->info("CategoryAttributeSeeder: {$totalAttributes} attributes and {$totalOptions} options seeded.");
    }
}
