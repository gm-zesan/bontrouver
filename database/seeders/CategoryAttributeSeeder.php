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
            // ─────────────────────────────────────────────────────────────
            // 🚗 1. VEHICLES & AUTOMOTIVE
            // ─────────────────────────────────────────────────────────────
            'cars-trucks' => [
                [
                    'name'          => 'Make',
                    'slug'          => 'make',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes-Benz', 'Audi', 'Hyundai', 'Tesla', 'Chevrolet', 'Subaru', 'Nissan', 'Mazda', 'Porsche', 'Volkswagen', 'Jeep', 'Lexus', 'Kia', 'Volvo']
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

            'motorcycles-scooters' => [
                [
                    'name'          => 'Motorcycle Type',
                    'slug'          => 'motorcycle-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Sport Bike', 'Cruiser', 'Touring / Adventure', 'Dirt Bike / Motocross', 'Dual-Sport', 'Scooter / Moped', 'Cafe Racer']
                ],
                [
                    'name'          => 'Make',
                    'slug'          => 'make',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Yamaha', 'Honda', 'Kawasaki', 'Suzuki', 'Harley-Davidson', 'BMW', 'Ducati', 'KTM', 'Triumph', 'Vespa']
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

            'boats-watercraft' => [
                [
                    'name'          => 'Boat Type',
                    'slug'          => 'boat-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Bowrider', 'Pontoon', 'Fishing Boat', 'Personal Watercraft (Sea-Doo/Jet Ski)', 'Cabin Cruiser', 'Sailboat', 'Canoe / Kayak']
                ],
                [
                    'name'          => 'Length (Feet)',
                    'slug'          => 'boat-length',
                    'type'          => 'number',
                    'is_required'   => false,
                    'is_filterable' => true,
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
                    'options'       => ['Yes (Included in Price)', 'No (Boat Only)']
                ],
            ],

            'rvs-campers' => [
                [
                    'name'          => 'RV Type',
                    'slug'          => 'rv-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Travel Trailer', 'Fifth Wheel', 'Motorhome Class A', 'Motorhome Class B (Camper Van)', 'Motorhome Class C', 'Pop-up Camper', 'Truck Camper']
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
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['2 People', '4 People', '6 People', '8+ People']
                ],
            ],

            'auto-parts-tires' => [
                [
                    'name'          => 'Part Category',
                    'slug'          => 'part-category',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Tires & Rims / Wheels', 'Engine & Drivetrain', 'Brakes & Suspension', 'Body Parts & Mirrors', 'Interior & Seating', 'Lighting & Headlights', 'Car Audio & Electronics']
                ],
                [
                    'name'          => 'Rim / Tire Size',
                    'slug'          => 'tire-size',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['15 inch', '16 inch', '17 inch', '18 inch', '19 inch', '20 inch', '21+ inch']
                ],
                [
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['New / Unused', 'Used (Like New)', 'Used (Normal Wear)', 'For Parts']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 🏠 2. HOUSING & REAL ESTATE
            // ─────────────────────────────────────────────────────────────
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
                    'is_filterable' => true,
                    'options'       => ['Fenced Private Yard', 'Shared Yard', 'Patio / Deck Only', 'None']
                ],
                [
                    'name'          => 'Basement',
                    'slug'          => 'basement',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Finished Full Basement', 'Unfinished Basement', 'Walkout Basement', 'Separate Basement Apartment (Not Included)', 'No Basement']
                ],
            ],

            'room-rentals-roommates' => [
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
                    'options'       => ['Private Attached Ensuite', 'Shared Bathroom']
                ],
                [
                    'name'          => 'Furnished',
                    'slug'          => 'furnished',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Furnished (Bed, Desk, Closet)', 'Unfurnished']
                ],
                [
                    'name'          => 'Gender Preference',
                    'slug'          => 'gender-preference',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Any Gender', 'Female Preferred', 'Male Preferred', 'Couples Welcome']
                ],
            ],

            'houses-sale' => [
                [
                    'name'          => 'Property Type',
                    'slug'          => 'property-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Detached Single Family', 'Semi-Detached', 'Townhouse / Freehold', 'Bungalow', 'Duplex / Triplex', 'Country Home / Acreage']
                ],
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
                    'options'       => ['2 Bathrooms', '3 Bathrooms', '4 Bathrooms', '5+ Bathrooms']
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
                    'options'       => ['1 Car Garage', '2 Car Garage', '3+ Car Garage', 'Driveway Only', 'No Garage']
                ],
            ],

            'condos-sale' => [
                [
                    'name'          => 'Bedrooms',
                    'slug'          => 'bedrooms',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Studio', '1 Bedroom', '1 Bedroom + Den', '2 Bedrooms', '2 Bedrooms + Den', '3+ Bedrooms', 'Penthouse']
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
                    'name'          => 'Balcony / Terrace',
                    'slug'          => 'balcony',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Yes (Private Balcony)', 'Large Terrace', 'None / Juliet Balcony']
                ],
                [
                    'name'          => 'Underground Parking',
                    'slug'          => 'underground-parking',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['1 Owned Spot', '2 Owned Spots', 'Available for Rent', 'None']
                ],
            ],

            'commercial-office-space' => [
                [
                    'name'          => 'Space Type',
                    'slug'          => 'space-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Office Space', 'Retail Storefront', 'Industrial / Warehouse', 'Medical / Dental Clinic', 'Restaurant / Food Service', 'Flex Space']
                ],
                [
                    'name'          => 'Square Footage',
                    'slug'          => 'square-footage',
                    'type'          => 'number',
                    'is_required'   => true,
                    'is_filterable' => true,
                ],
                [
                    'name'          => 'Zoning',
                    'slug'          => 'zoning',
                    'type'          => 'text',
                    'is_required'   => false,
                    'is_filterable' => false,
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 📱 3. ELECTRONICS & COMPUTERS
            // ─────────────────────────────────────────────────────────────
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
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New (Sealed in Box)', 'Like New / Mint', 'Good Condition', 'Fair / Scratches', 'For Parts / Cracked Screen']
                ],
            ],

            'laptops-computers' => [
                [
                    'name'          => 'Brand',
                    'slug'          => 'brand',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Apple', 'Dell', 'Lenovo', 'HP', 'ASUS', 'Acer', 'Microsoft Surface', 'Razer', 'Custom PC / Gaming Rig']
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

            // ─────────────────────────────────────────────────────────────
            // 🛋️ 4. BUY & SELL / HOME & LIVING
            // ─────────────────────────────────────────────────────────────
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
                    'name'          => 'Condition',
                    'slug'          => 'condition',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Brand New', 'Like New', 'Gently Used', 'Good Vintage Condition']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 💼 5. JOBS
            // ─────────────────────────────────────────────────────────────
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

            'accounting-management' => [
                [
                    'name'          => 'Job Type',
                    'slug'          => 'job-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Full-time', 'Part-time', 'Contract']
                ],
                [
                    'name'          => 'Certification Required',
                    'slug'          => 'certification',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['CPA Required', 'CFA Required', 'MBA Preferred', 'Not Required / Experience Based']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 🛠️ 6. SERVICES
            // ─────────────────────────────────────────────────────────────
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
                [
                    'name'          => 'Free Estimates Available',
                    'slug'          => 'free-estimates',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => false,
                    'options'       => ['Yes (Free In-Person or Virtual Quote)', 'Paid Consultation (Credited to Job)']
                ],
            ],

            'cleaning-housekeeping' => [
                [
                    'name'          => 'Service Type',
                    'slug'          => 'service-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Residential Home Cleaning', 'Deep Clean / Move-In & Move-Out', 'Commercial Office Cleaning', 'Post-Construction Cleaning', 'Carpet & Upholstery Steam Clean']
                ],
                [
                    'name'          => 'Frequency Offered',
                    'slug'          => 'frequency',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['One-Time Only', 'Weekly / Bi-Weekly', 'Monthly Recurring', 'Flexible On-Demand']
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // 🤝 7. COMMUNITY & NEED COMPANIONSHIP
            // ─────────────────────────────────────────────────────────────
            'companionship-meetups' => [
                [
                    'name'          => 'Meetup Activity',
                    'slug'          => 'meetup-activity',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['☕ Coffee & Chat', '🚶 Walk in the Park', '🍽️ Dining & Food Tour', '🎬 Cinema & Movies', '⚽ Match & Live Sports', '🏃 Workout & Running', '🎮 Video Games & Board Games', '🌆 City Outings & Museums', '🗣️ Language Exchange']
                ],
                [
                    'name'          => 'Preferred Timing',
                    'slug'          => 'timing',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['Weekend Afternoons', 'Weekday Evenings', 'Morning Coffee (Weekdays)', 'Flexible Weekend']
                ],
                [
                    'name'          => 'Group Size Preference',
                    'slug'          => 'group-size',
                    'type'          => 'select',
                    'is_required'   => false,
                    'is_filterable' => true,
                    'options'       => ['1-on-1 Friendly Chat', 'Small Group (3-5 people)', 'Open Social Group']
                ],
            ],

            'rideshare-carpool' => [
                [
                    'name'          => 'Trip Type',
                    'slug'          => 'trip-type',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['Daily Work Commute', 'Intercity Weekend Trip (e.g. Montreal-Toronto)', 'Airport Ride', 'Cottage / Ski Trip']
                ],
                [
                    'name'          => 'Available Seats',
                    'slug'          => 'available-seats',
                    'type'          => 'select',
                    'is_required'   => true,
                    'is_filterable' => true,
                    'options'       => ['1 Seat', '2 Seats', '3 Seats', '4+ Seats']
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

