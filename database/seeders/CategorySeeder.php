<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesData = $this->getInitialCategories();

        DB::beginTransaction();
        try {
            $rootOrder = 1;

            foreach ($categoriesData as $rootSlug => $rootData) {
                // 1. Insert or update Root Category (Level 1)
                $rootCategory = Category::updateOrCreate(
                    [
                        'parent_id' => null,
                        'slug' => $rootData['slug'] ?? $rootSlug,
                    ],
                    [
                        'name' => $rootData['name'] ?? ucfirst($rootSlug),
                        'icon' => $rootData['icon'] ?? 'bi-tag',
                        'description' => $rootData['description'] ?? null,
                        'sort_order' => $rootOrder++,
                        'is_active' => true,
                    ]
                );

                $subcategories = $rootData['children'] ?? $rootData['subcategories'] ?? [];
                $this->seedChildren($rootCategory->id, $subcategories);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Recursively seed children categories for arbitrary levels of depth.
     */
    protected function seedChildren(int $parentId, array $children, int $startOrder = 1): void
    {
        $order = $startOrder;

        foreach ($children as $childData) {
            $name = $childData['name'] ?? 'Category';
            $slug = $childData['slug'] ?? Str::slug($name);

            $category = Category::updateOrCreate(
                [
                    'parent_id' => $parentId,
                    'slug' => $slug,
                ],
                [
                    'name' => $name,
                    'icon' => $childData['icon'] ?? null,
                    'description' => $childData['description'] ?? null,
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );

            $nestedChildren = $childData['children'] ?? $childData['subcategories'] ?? [];
            if (!empty($nestedChildren)) {
                $this->seedChildren($category->id, $nestedChildren);
            }
        }
    }

    /**
     * Master Category & Subcategory Dataset (Kijiji.ca Architecture).
     */
    protected function getInitialCategories(): array
    {
        return [
            'housing' => [
                'name' => 'Housing',
                'slug' => 'housing',
                'icon' => 'bi-house-door',
                'description' => 'Apartments, condos, houses & rentals',
                'subcategories' => [
                    [
                        'name' => 'Apartments & Condos for Rent',
                        'slug' => 'apartments-condos-rent',
                        'children' => [
                            ['name' => '1 Bedroom Apartments', 'slug' => '1-bedroom-apartments'],
                            ['name' => '2 Bedroom Apartments', 'slug' => '2-bedroom-apartments'],
                            ['name' => '3+ Bedroom Apartments', 'slug' => '3-plus-bedroom-apartments'],
                            ['name' => 'Bachelor & Studio Apartments', 'slug' => 'bachelor-studio'],
                            ['name' => 'Basement Apartments', 'slug' => 'basement-apartments'],
                            ['name' => 'Luxury Condos for Rent', 'slug' => 'luxury-condos'],
                        ],
                    ],
                    [
                        'name' => 'Houses for Rent',
                        'slug' => 'houses-rent',
                        'children' => [
                            ['name' => 'Detached Houses', 'slug' => 'detached-houses-rent'],
                            ['name' => 'Townhouses & Row Houses', 'slug' => 'townhouses-rent'],
                            ['name' => 'Duplex / Triplex', 'slug' => 'duplex-triplex-rent'],
                            ['name' => 'Shared House Rentals', 'slug' => 'shared-house-rentals'],
                        ],
                    ],
                    [
                        'name' => 'Room Rentals & Roommates',
                        'slug' => 'room-rentals-roommates',
                        'children' => [
                            ['name' => 'Student Room Rentals', 'slug' => 'student-room-rentals'],
                            ['name' => 'Furnished Rooms', 'slug' => 'furnished-rooms'],
                            ['name' => 'Roommate Search', 'slug' => 'roommate-search'],
                            ['name' => 'Sublets & Temporary Rooms', 'slug' => 'temporary-rooms'],
                        ],
                    ],
                    [
                        'name' => 'Houses for Sale',
                        'slug' => 'houses-sale',
                        'children' => [
                            ['name' => 'Single Family Homes', 'slug' => 'single-family-homes'],
                            ['name' => 'Townhouses for Sale', 'slug' => 'townhouses-sale'],
                            ['name' => 'Luxury Homes & Estates', 'slug' => 'luxury-homes'],
                            ['name' => 'New Developments & Pre-Construction', 'slug' => 'new-developments-houses'],
                        ],
                    ],
                    [
                        'name' => 'Condos for Sale',
                        'slug' => 'condos-sale',
                        'children' => [
                            ['name' => '1 Bedroom Condos', 'slug' => '1-bed-condos-sale'],
                            ['name' => '2+ Bedroom Condos', 'slug' => '2-plus-bed-condos-sale'],
                            ['name' => 'Penthouses', 'slug' => 'penthouses-sale'],
                            ['name' => 'Pre-Construction Condos', 'slug' => 'pre-construction-condos'],
                        ],
                    ],
                    [
                        'name' => 'Commercial & Office Space',
                        'slug' => 'commercial-office-space',
                        'children' => [
                            ['name' => 'Office Space for Rent / Lease', 'slug' => 'office-space-rent'],
                            ['name' => 'Retail Space for Lease', 'slug' => 'retail-space-lease'],
                            ['name' => 'Industrial & Warehouse Space', 'slug' => 'industrial-warehouse'],
                            ['name' => 'Commercial Properties for Sale', 'slug' => 'commercial-properties-sale'],
                        ],
                    ],
                    [
                        'name' => 'Land & Plots for Sale',
                        'slug' => 'land-plots-sale',
                        'children' => [
                            ['name' => 'Residential Lots', 'slug' => 'residential-lots'],
                            ['name' => 'Commercial Land', 'slug' => 'commercial-land'],
                            ['name' => 'Acreage & Farm Land', 'slug' => 'acreage-farm-land'],
                        ],
                    ],
                    [
                        'name' => 'Storage & Parking for Rent',
                        'slug' => 'storage-parking-rent',
                        'children' => [
                            ['name' => 'Storage Units & Lockers', 'slug' => 'storage-units'],
                            ['name' => 'Parking Spots & Driveways', 'slug' => 'parking-spots'],
                            ['name' => 'Garage Space for Rent', 'slug' => 'garage-space-rent'],
                        ],
                    ],
                    [
                        'name' => 'Short Term & Vacation Rentals',
                        'slug' => 'short-term-rentals',
                        'children' => [
                            ['name' => 'Furnished Monthly Rentals', 'slug' => 'furnished-monthly'],
                            ['name' => 'Weekly Holiday Rentals', 'slug' => 'weekly-rentals'],
                            ['name' => 'Sublets & Temporary Housing', 'slug' => 'sublets-temporary'],
                        ],
                    ],
                    [
                        'name' => 'Other Housing',
                        'slug' => 'other-housing',
                        'children' => [],
                    ],
                ]
            ],
            'jobs' => [
                'name' => 'Jobs',
                'slug' => 'jobs',
                'icon' => 'bi-briefcase',
                'description' => 'Find local jobs or hire great talent',
                'subcategories' => [
                    [
                        'name' => 'Accounting & Management',
                        'slug' => 'accounting-management',
                        'children' => [
                            ['name' => 'Bookkeepers & Accountants', 'slug' => 'bookkeepers-accountants'],
                            ['name' => 'Financial Analysts', 'slug' => 'financial-analysts'],
                            ['name' => 'Payroll Specialists', 'slug' => 'payroll-specialists'],
                            ['name' => 'General Managers & Executives', 'slug' => 'managers-executives'],
                        ],
                    ],
                    [
                        'name' => 'Barista, Restaurant & Food Services',
                        'slug' => 'barista-restaurant-food',
                        'children' => [
                            ['name' => 'Cooks, Chefs & Line Cooks', 'slug' => 'cooks-chefs'],
                            ['name' => 'Bartenders & Baristas', 'slug' => 'bartenders-baristas'],
                            ['name' => 'Servers & Waitstaff', 'slug' => 'servers-waitstaff'],
                            ['name' => 'Kitchen Helpers & Dishwashers', 'slug' => 'kitchen-helpers'],
                            ['name' => 'Restaurant Managers', 'slug' => 'restaurant-managers'],
                        ],
                    ],
                    [
                        'name' => 'Childcare & Caregivers',
                        'slug' => 'childcare-caregivers',
                        'children' => [
                            ['name' => 'Nannies & Babysitters', 'slug' => 'nannies-babysitters'],
                            ['name' => 'Elderly Care & Caregivers', 'slug' => 'elderly-caregivers'],
                            ['name' => 'Daycare Workers', 'slug' => 'daycare-workers'],
                        ],
                    ],
                    [
                        'name' => 'Cleaning & Housekeeping',
                        'slug' => 'cleaning-housekeeping',
                        'children' => [
                            ['name' => 'Residential House Cleaners', 'slug' => 'residential-cleaners'],
                            ['name' => 'Commercial & Office Cleaners', 'slug' => 'commercial-cleaners'],
                            ['name' => 'Carpet, Window & Deep Cleaning', 'slug' => 'carpet-window-cleaners'],
                        ],
                    ],
                    [
                        'name' => 'Construction, Trades & Labour',
                        'slug' => 'construction-trades-labour',
                        'children' => [
                            ['name' => 'Carpenters & Framers', 'slug' => 'carpenters-framers'],
                            ['name' => 'Electricians & Apprentices', 'slug' => 'electricians'],
                            ['name' => 'Plumbers & Pipefitters', 'slug' => 'plumbers-jobs'],
                            ['name' => 'Painters & Drywallers', 'slug' => 'painters-drywallers'],
                            ['name' => 'Roofers & Siding Installers', 'slug' => 'roofers-siding'],
                            ['name' => 'HVAC Technicians', 'slug' => 'hvac-technicians'],
                        ],
                    ],
                    [
                        'name' => 'Customer Service & Call Centre',
                        'slug' => 'customer-service-call-centre',
                        'children' => [
                            ['name' => 'Call Centre Representatives', 'slug' => 'call-centre-reps'],
                            ['name' => 'Customer Support Specialists', 'slug' => 'support-specialists'],
                            ['name' => 'Helpdesk & Client Care', 'slug' => 'helpdesk-client-care'],
                        ],
                    ],
                    [
                        'name' => 'Drivers, Delivery & Logistics',
                        'slug' => 'drivers-delivery-logistics',
                        'children' => [
                            ['name' => 'Courier & Local Delivery Drivers', 'slug' => 'courier-delivery-drivers'],
                            ['name' => 'Truck Drivers (AZ / DZ / Class 1)', 'slug' => 'truck-drivers'],
                            ['name' => 'Warehouse Associates & Forklift', 'slug' => 'warehouse-forklift'],
                            ['name' => 'Dispatchers & Logistics Coordinators', 'slug' => 'dispatchers-logistics'],
                        ],
                    ],
                    [
                        'name' => 'General Labour',
                        'slug' => 'general-labour',
                        'children' => [
                            ['name' => 'Factory & Assembly Workers', 'slug' => 'factory-assembly'],
                            ['name' => 'Landscaping & Groundskeeping', 'slug' => 'landscaping-labour'],
                            ['name' => 'Moving & Heavy Lifting Helpers', 'slug' => 'moving-helpers'],
                        ],
                    ],
                    [
                        'name' => 'Healthcare & Medical',
                        'slug' => 'healthcare-medical',
                        'children' => [
                            ['name' => 'Nurses (RN / RPN / LPN)', 'slug' => 'nurses'],
                            ['name' => 'Personal Support Workers (PSW)', 'slug' => 'psw-workers'],
                            ['name' => 'Dental Assistants & Hygienists', 'slug' => 'dental-assistants'],
                            ['name' => 'Medical Receptionists & Admin', 'slug' => 'medical-admin'],
                        ],
                    ],
                    [
                        'name' => 'Hospitality & Tourism',
                        'slug' => 'hospitality-tourism',
                        'children' => [
                            ['name' => 'Hotel Front Desk & Concierge', 'slug' => 'hotel-front-desk'],
                            ['name' => 'Event Coordinators & Staff', 'slug' => 'event-coordinators'],
                            ['name' => 'Tour Guides & Travel Agents', 'slug' => 'tour-guides'],
                        ],
                    ],
                    [
                        'name' => 'IT, Tech & Software',
                        'slug' => 'it-tech-software',
                        'children' => [
                            ['name' => 'Web & Software Developers', 'slug' => 'web-software-developers'],
                            ['name' => 'IT Support & Systems Admin', 'slug' => 'it-support-admin'],
                            ['name' => 'UI / UX & Graphic Designers', 'slug' => 'ui-ux-designers'],
                            ['name' => 'Data Analysts & AI Specialists', 'slug' => 'data-analysts'],
                            ['name' => 'QA Testers & DevOps', 'slug' => 'qa-devops'],
                        ],
                    ],
                    [
                        'name' => 'Office & Administrative',
                        'slug' => 'office-administrative',
                        'children' => [
                            ['name' => 'Executive & Admin Assistants', 'slug' => 'admin-assistants'],
                            ['name' => 'Data Entry Clerks', 'slug' => 'data-entry'],
                            ['name' => 'Receptionists & Office Clerks', 'slug' => 'receptionists'],
                        ],
                    ],
                    [
                        'name' => 'Sales & Retail',
                        'slug' => 'sales-retail',
                        'children' => [
                            ['name' => 'Retail Associates & Cashiers', 'slug' => 'retail-associates'],
                            ['name' => 'B2B & Sales Representatives', 'slug' => 'sales-reps'],
                            ['name' => 'Store Managers & Supervisors', 'slug' => 'store-managers'],
                            ['name' => 'Real Estate & Insurance Agents', 'slug' => 'agents-brokers'],
                        ],
                    ],
                    [
                        'name' => 'Security & Safety',
                        'slug' => 'security-safety',
                        'children' => [
                            ['name' => 'Licensed Security Guards', 'slug' => 'security-guards'],
                            ['name' => 'Event & Bouncer Security', 'slug' => 'event-security'],
                            ['name' => 'Loss Prevention Officers', 'slug' => 'loss-prevention'],
                        ],
                    ],
                    [
                        'name' => 'Other Jobs',
                        'slug' => 'other-jobs',
                        'children' => [],
                    ],
                ]
            ],
            'ridesharing' => [
                'name' => 'Ridesharing (Carpooling)',
                'slug' => 'ridesharing',
                'icon' => 'bi-car-front',
                'description' => 'Shared rides, carpooling & daily commute',
                'subcategories' => [
                    [
                        'name' => 'Rides Offered',
                        'slug' => 'rides-offered',
                        'children' => [
                            ['name' => 'Intercity Scheduled Rides', 'slug' => 'intercity-rides-offered'],
                            ['name' => 'Daily Commute Seats', 'slug' => 'daily-commute-offered'],
                            ['name' => 'Weekend Trips & Getaways', 'slug' => 'weekend-trips-offered'],
                            ['name' => 'Airport Express Rides', 'slug' => 'airport-rides-offered'],
                        ],
                    ],
                    [
                        'name' => 'Rides Wanted / Requests',
                        'slug' => 'rides-wanted',
                        'children' => [
                            ['name' => 'Daily Commute Requests', 'slug' => 'daily-commute-requests'],
                            ['name' => 'Intercity Ride Requests', 'slug' => 'intercity-ride-requests'],
                            ['name' => 'Campus / Student Ride Requests', 'slug' => 'campus-ride-requests'],
                        ],
                    ],
                    [
                        'name' => 'Daily Commute Carpools',
                        'slug' => 'daily-commute-carpools',
                        'children' => [
                            ['name' => 'Suburbs to Downtown Routes', 'slug' => 'suburbs-downtown'],
                            ['name' => 'Industrial & Tech Park Routes', 'slug' => 'industrial-park-routes'],
                            ['name' => 'Shift Work & Night Carpools', 'slug' => 'shift-work-carpools'],
                        ],
                    ],
                    [
                        'name' => 'Intercity & Long Distance Trips',
                        'slug' => 'intercity-trips',
                        'children' => [
                            ['name' => 'Montreal ↔ Toronto', 'slug' => 'montreal-toronto'],
                            ['name' => 'Calgary ↔ Edmonton', 'slug' => 'calgary-edmonton'],
                            ['name' => 'Vancouver ↔ Kelowna / Whistler', 'slug' => 'vancouver-kelowna'],
                            ['name' => 'Ottawa ↔ Montreal / Toronto', 'slug' => 'ottawa-montreal'],
                        ],
                    ],
                    [
                        'name' => 'Airport Rideshare & Shuttles',
                        'slug' => 'airport-rideshare',
                        'children' => [
                            ['name' => 'Toronto Pearson (YYZ) Rides', 'slug' => 'yyz-rideshare'],
                            ['name' => 'Montreal-Trudeau (YUL) Rides', 'slug' => 'yul-rideshare'],
                            ['name' => 'Vancouver (YVR) Rides', 'slug' => 'yvr-rideshare'],
                        ],
                    ],
                    [
                        'name' => 'Campus & University Carpools',
                        'slug' => 'campus-carpools',
                        'children' => [
                            ['name' => 'University of Toronto & York', 'slug' => 'uoft-york-carpools'],
                            ['name' => 'McGill & Concordia', 'slug' => 'mcgill-concordia-carpools'],
                            ['name' => 'UBC & SFU Rides', 'slug' => 'ubc-sfu-carpools'],
                            ['name' => 'Waterloo & McMaster', 'slug' => 'waterloo-mcmaster-carpools'],
                        ],
                    ],
                    [
                        'name' => 'Weekend & Holiday Getaways',
                        'slug' => 'weekend-getaways',
                        'children' => [
                            ['name' => 'Ski Trips & Resorts', 'slug' => 'ski-trips-carpool'],
                            ['name' => 'Cottage Country & Camping', 'slug' => 'cottage-carpool'],
                        ],
                    ],
                    [
                        'name' => 'Cross-Border Carpooling',
                        'slug' => 'cross-border-carpools',
                        'children' => [
                            ['name' => 'Vancouver ↔ Seattle', 'slug' => 'vancouver-seattle'],
                            ['name' => 'Toronto ↔ Buffalo / NYC', 'slug' => 'toronto-buffalo-nyc'],
                            ['name' => 'Montreal ↔ Boston / NYC', 'slug' => 'montreal-boston-nyc'],
                        ],
                    ],
                    [
                        'name' => 'Other Ridesharing',
                        'slug' => 'other-ridesharing',
                        'children' => [],
                    ],
                ]
            ],
            'cars-vehicles' => [
                'name' => 'Cars & Vehicles',
                'slug' => 'cars-vehicles',
                'icon' => 'bi-car-front-fill',
                'description' => 'Cars, trucks, motorcycles, parts & more',
                'subcategories' => [
                    [
                        'name' => 'Cars & Trucks',
                        'slug' => 'cars-trucks',
                        'children' => [
                            ['name' => 'SUVs, Crossovers & CUVs', 'slug' => 'suvs-crossovers'],
                            ['name' => 'Sedans & Coupes', 'slug' => 'sedans-coupes'],
                            ['name' => 'Pickup Trucks', 'slug' => 'pickup-trucks'],
                            ['name' => 'Hatchbacks & Wagons', 'slug' => 'hatchbacks-wagons'],
                            ['name' => 'Electric & Hybrid Vehicles', 'slug' => 'ev-hybrids'],
                            ['name' => 'Vans & Minivans', 'slug' => 'vans-minivans'],
                        ],
                    ],
                    [
                        'name' => 'Classic Cars',
                        'slug' => 'classic-cars',
                        'children' => [
                            ['name' => 'Muscle Cars', 'slug' => 'muscle-cars'],
                            ['name' => 'Antique & Vintage Cars', 'slug' => 'vintage-antique-cars'],
                            ['name' => 'Restored Classics & Hot Rods', 'slug' => 'restored-classics'],
                        ],
                    ],
                    [
                        'name' => 'Vehicle Parts, Tires & Accessories',
                        'slug' => 'vehicle-parts-tires-accessories',
                        'children' => [
                            [
                                'name' => 'Tires & Rims',
                                'slug' => 'tires-rims',
                                'children' => [
                                    ['name' => 'Winter Tires', 'slug' => 'winter-tires'],
                                    ['name' => 'All-Season Tires', 'slug' => 'all-season-tires'],
                                    ['name' => 'Rims & Custom Wheels', 'slug' => 'rims-custom-wheels'],
                                    ['name' => 'Truck & Off-Road Tires', 'slug' => 'truck-offroad-tires'],
                                ],
                            ],
                            ['name' => 'Engine & Transmission Parts', 'slug' => 'engine-transmission'],
                            ['name' => 'Brakes & Suspension', 'slug' => 'brakes-suspension'],
                            ['name' => 'Auto Body Parts & Mirrors', 'slug' => 'auto-body-parts'],
                            ['name' => 'Audio, GPS & Electronics', 'slug' => 'car-audio-gps'],
                            ['name' => 'Lights, Bulbs & Electrical', 'slug' => 'lights-electrical'],
                            ['name' => 'Other Parts & Accessories', 'slug' => 'other-parts-accessories'],
                        ],
                    ],
                    [
                        'name' => 'Automotive Services',
                        'slug' => 'automotive-services',
                        'children' => [
                            ['name' => 'Auto Repair & Mechanics', 'slug' => 'auto-mechanic-repair'],
                            ['name' => 'Auto Detailing & Ceramic Coating', 'slug' => 'auto-detailing'],
                            ['name' => 'Towing & Roadside Assistance', 'slug' => 'towing-services'],
                            ['name' => 'Auto Body & Paint Repair', 'slug' => 'auto-body-paint'],
                        ],
                    ],
                    [
                        'name' => 'Motorcycles',
                        'slug' => 'motorcycles',
                        'children' => [
                            ['name' => 'Street Bikes & Cruisers', 'slug' => 'street-bikes-cruisers'],
                            ['name' => 'Sport Bikes & Superbikes', 'slug' => 'sport-bikes'],
                            ['name' => 'Dirt Bikes & Motocross', 'slug' => 'dirt-bikes'],
                            ['name' => 'Scooters & Mopeds', 'slug' => 'scooters-mopeds'],
                        ],
                    ],
                    [
                        'name' => 'ATVs & Snowmobiles',
                        'slug' => 'atvs-snowmobiles',
                        'children' => [
                            ['name' => 'ATVs & Quad Bikes', 'slug' => 'atvs-quads'],
                            ['name' => 'UTVs & Side-by-Sides', 'slug' => 'utvs-side-by-sides'],
                            ['name' => 'Snowmobiles & Sleds', 'slug' => 'snowmobiles'],
                        ],
                    ],
                    [
                        'name' => 'Boats & Watercraft',
                        'slug' => 'boats-watercraft',
                        'children' => [
                            ['name' => 'Powerboats & Motorboats', 'slug' => 'powerboats-motorboats'],
                            ['name' => 'Sailboats & Catamarans', 'slug' => 'sailboats'],
                            ['name' => 'Jet Skis & Sea-Doos (PWC)', 'slug' => 'jet-skis-pwc'],
                            ['name' => 'Canoes, Kayaks & Paddleboards', 'slug' => 'kayaks-canoes'],
                        ],
                    ],
                    [
                        'name' => 'RVs, Campers & Trailers',
                        'slug' => 'rvs-campers-trailers',
                        'children' => [
                            ['name' => 'Travel Trailers & Fifth Wheels', 'slug' => 'travel-trailers'],
                            ['name' => 'Motorhomes (Class A, B, C)', 'slug' => 'motorhomes'],
                            ['name' => 'Pop-up Campers & Truck Campers', 'slug' => 'popup-campers'],
                            ['name' => 'Cargo & Utility Trailers', 'slug' => 'utility-trailers'],
                        ],
                    ],
                    [
                        'name' => 'Heavy Equipment',
                        'slug' => 'heavy-equipment',
                        'children' => [
                            ['name' => 'Excavators & Backhoes', 'slug' => 'excavators-backhoes'],
                            ['name' => 'Skid Steers & Loaders', 'slug' => 'skid-steers-loaders'],
                            ['name' => 'Cranes, Forklifts & Lifts', 'slug' => 'forklifts-cranes'],
                        ],
                    ],
                    [
                        'name' => 'Farming Equipment',
                        'slug' => 'farming-equipment',
                        'children' => [
                            ['name' => 'Tractors & Attachments', 'slug' => 'tractors-attachments'],
                            ['name' => 'Harvesters & Seeders', 'slug' => 'harvesters-seeders'],
                            ['name' => 'Hay & Livestock Equipment', 'slug' => 'hay-livestock-equipment'],
                        ],
                    ],
                    [
                        'name' => 'Other Vehicles',
                        'slug' => 'other-vehicles',
                        'children' => [],
                    ],
                ]
            ],
            'buy-sell' => [
                'name' => 'Buy & Sell',
                'slug' => 'buy-sell',
                'icon' => 'bi-tag',
                'description' => 'Electronics, furniture, fashion & items',
                'subcategories' => [
                    [
                        'name' => 'Audio & Stereo Equipment',
                        'slug' => 'audio-stereo',
                        'children' => [
                            ['name' => 'Headphones & Earbuds', 'slug' => 'headphones-earbuds'],
                            ['name' => 'Speakers & Subwoofers', 'slug' => 'speakers-subwoofers'],
                            ['name' => 'Amplifiers, Receivers & DACs', 'slug' => 'amplifiers-receivers'],
                            ['name' => 'Turntables & Vinyl Records', 'slug' => 'turntables-vinyl'],
                        ],
                    ],
                    [
                        'name' => 'Baby Items & Toys',
                        'slug' => 'baby-items-toys',
                        'children' => [
                            ['name' => 'Strollers & Car Seats', 'slug' => 'strollers-car-seats'],
                            ['name' => 'Baby Clothing & Essentials', 'slug' => 'baby-clothing'],
                            ['name' => 'Toys, Dolls & Action Figures', 'slug' => 'toys-action-figures'],
                            ['name' => 'Cribs & Nursery Furniture', 'slug' => 'cribs-nursery'],
                        ],
                    ],
                    [
                        'name' => 'Bikes & Cycling',
                        'slug' => 'bikes-cycling',
                        'children' => [
                            ['name' => 'Mountain Bikes', 'slug' => 'mountain-bikes'],
                            ['name' => 'Road & Racing Bikes', 'slug' => 'road-bikes'],
                            ['name' => 'Electric Bikes (e-Bikes)', 'slug' => 'electric-bikes'],
                            ['name' => 'Hybrid & City Bikes', 'slug' => 'hybrid-city-bikes'],
                            ['name' => 'Kids Bikes & Helmets', 'slug' => 'kids-bikes'],
                        ],
                    ],
                    [
                        'name' => 'Books, DVDs & Media',
                        'slug' => 'books-dvds-media',
                        'children' => [
                            ['name' => 'Fiction & Non-Fiction Books', 'slug' => 'fiction-nonfiction-books'],
                            ['name' => 'Textbooks & Education', 'slug' => 'textbooks-education'],
                            ['name' => 'DVDs, Blu-Ray & Box Sets', 'slug' => 'dvds-bluray'],
                            ['name' => 'Vinyl Records & Music CDs', 'slug' => 'vinyl-cds'],
                        ],
                    ],
                    [
                        'name' => 'Cameras & Camcorders',
                        'slug' => 'cameras-camcorders',
                        'children' => [
                            ['name' => 'DSLR & Mirrorless Cameras', 'slug' => 'dslr-mirrorless-cameras'],
                            ['name' => 'Camera Lenses & Filters', 'slug' => 'camera-lenses'],
                            ['name' => 'Action Cameras, GoPros & Drones', 'slug' => 'action-cams-drones'],
                            ['name' => 'Tripods, Lighting & Flashes', 'slug' => 'tripods-lighting'],
                        ],
                    ],
                    [
                        'name' => 'Clothing, Shoes & Accessories',
                        'slug' => 'clothing-shoes-accessories',
                        'children' => [
                            ['name' => "Men's Clothing", 'slug' => 'mens-clothing'],
                            ['name' => "Women's Clothing", 'slug' => 'womens-clothing'],
                            ['name' => 'Shoes, Sneakers & Boots', 'slug' => 'shoes-sneakers-boots'],
                            ['name' => 'Handbags, Wallets & Backpacks', 'slug' => 'handbags-backpacks'],
                            ['name' => 'Winter Jackets & Coats', 'slug' => 'winter-jackets-coats'],
                        ],
                    ],
                    [
                        'name' => 'Computers, Laptops & Tablets',
                        'slug' => 'computers-laptops-tablets',
                        'children' => [
                            ['name' => 'Laptops & MacBooks', 'slug' => 'laptops-macbooks'],
                            ['name' => 'Desktop PCs & Gaming Rigs', 'slug' => 'desktop-gaming-pcs'],
                            ['name' => 'iPads, Android Tablets & e-Readers', 'slug' => 'tablets-ereaders'],
                            ['name' => 'Monitors & Ultrawide Displays', 'slug' => 'monitors-displays'],
                            ['name' => 'Computer Parts, GPUs & CPUs', 'slug' => 'gpus-cpus-parts'],
                            ['name' => 'Keyboards, Mice & Accessories', 'slug' => 'computer-accessories'],
                        ],
                    ],
                    [
                        'name' => 'Electronics & Home Theater',
                        'slug' => 'electronics-home-theater',
                        'children' => [
                            ['name' => '4K & OLED Smart TVs', 'slug' => 'smart-tvs'],
                            ['name' => 'Soundbars & Home Theaters', 'slug' => 'soundbars-theater'],
                            ['name' => 'Smart Home, Alexa & Google Nest', 'slug' => 'smart-home'],
                            ['name' => 'Projectors & Screens', 'slug' => 'projectors-screens'],
                        ],
                    ],
                    [
                        'name' => 'Furniture & Home Decor',
                        'slug' => 'furniture-home-decor',
                        'children' => [
                            ['name' => 'Sofas, Couches & Sectionals', 'slug' => 'sofas-couches-sectionals'],
                            ['name' => 'Beds, Frames & Mattresses', 'slug' => 'beds-mattresses'],
                            ['name' => 'Dining Tables & Chairs', 'slug' => 'dining-tables-chairs'],
                            ['name' => 'Desks & Ergonomic Chairs', 'slug' => 'desks-office-chairs'],
                            ['name' => 'Rugs, Mirrors & Wall Art', 'slug' => 'rugs-wall-art'],
                        ],
                    ],
                    [
                        'name' => 'Garage Sales',
                        'slug' => 'garage-sales',
                        'children' => [
                            ['name' => 'Multi-Family Garage Sales', 'slug' => 'multi-family-garage-sales'],
                            ['name' => 'Estate Sales & Moving Sales', 'slug' => 'estate-moving-sales'],
                        ],
                    ],
                    [
                        'name' => 'Health, Beauty & Personal Care',
                        'slug' => 'health-beauty',
                        'children' => [
                            ['name' => 'Skincare & Cosmetics', 'slug' => 'skincare-cosmetics'],
                            ['name' => 'Hair Care, Dryers & Styling', 'slug' => 'haircare-styling'],
                            ['name' => 'Perfumes & Fragrances', 'slug' => 'perfumes-fragrances'],
                            ['name' => 'Massagers & Health Devices', 'slug' => 'massagers-health-devices'],
                        ],
                    ],
                    [
                        'name' => 'Hobbies, Crafts & Collectibles',
                        'slug' => 'hobbies-crafts',
                        'children' => [
                            ['name' => 'Board Games & Tabletop', 'slug' => 'board-games'],
                            ['name' => 'Arts, Knitting & Sewing Supplies', 'slug' => 'arts-crafts-supplies'],
                            ['name' => 'Trading Cards & Sports Memorabilia', 'slug' => 'trading-cards-memorabilia'],
                            ['name' => 'Coins, Stamps & Antiques', 'slug' => 'coins-stamps-antiques'],
                        ],
                    ],
                    [
                        'name' => 'Home, Garden & Outdoor Living',
                        'slug' => 'home-garden',
                        'children' => [
                            ['name' => 'Patio Furniture & Gazebos', 'slug' => 'patio-furniture'],
                            ['name' => 'Lawn Mowers & Snowblowers', 'slug' => 'lawn-mowers-snowblowers'],
                            ['name' => 'BBQs, Smokers & Grills', 'slug' => 'bbqs-smokers-grills'],
                            ['name' => 'Indoor Plants & Planters', 'slug' => 'plants-planters'],
                        ],
                    ],
                    [
                        'name' => 'Jewelry, Watches & Luxury',
                        'slug' => 'jewelry-watches',
                        'children' => [
                            ['name' => "Men's Luxury Watches", 'slug' => 'mens-watches'],
                            ['name' => "Women's Watches & Jewelry", 'slug' => 'womens-watches-jewelry'],
                            ['name' => 'Engagement Rings & Diamonds', 'slug' => 'engagement-rings-diamonds'],
                            ['name' => 'Gold, Silver & Necklaces', 'slug' => 'gold-silver-necklaces'],
                        ],
                    ],
                    [
                        'name' => 'Musical Instruments',
                        'slug' => 'musical-instruments',
                        'children' => [
                            ['name' => 'Guitars, Amps & Pedals', 'slug' => 'guitars-amps'],
                            ['name' => 'Pianos & Digital Keyboards', 'slug' => 'pianos-keyboards'],
                            ['name' => 'Drums & Electronic Percussion', 'slug' => 'drums-percussion'],
                            ['name' => 'Studio Mics & Audio Interfaces', 'slug' => 'studio-recording-gear'],
                        ],
                    ],
                    [
                        'name' => 'Phones & Telecommunication',
                        'slug' => 'phones-telecommunication',
                        'children' => [
                            ['name' => 'iPhones & Apple Devices', 'slug' => 'iphones-apple'],
                            ['name' => 'Samsung Galaxy Phones', 'slug' => 'samsung-galaxy'],
                            ['name' => 'Google Pixel & Androids', 'slug' => 'google-pixel-android'],
                            ['name' => 'Smartwatches & Phone Cases', 'slug' => 'smartwatches-accessories'],
                        ],
                    ],
                    [
                        'name' => 'Sporting Goods & Fitness',
                        'slug' => 'sporting-goods-fitness',
                        'children' => [
                            ['name' => 'Home Gyms, Weights & Dumbbells', 'slug' => 'gym-weights-dumbbells'],
                            ['name' => 'Treadmills & Exercise Bikes', 'slug' => 'treadmills-bikes'],
                            ['name' => 'Hockey Skates & Equipment', 'slug' => 'hockey-equipment'],
                            ['name' => 'Golf Clubs, Bags & Rangefinders', 'slug' => 'golf-clubs-bags'],
                            ['name' => 'Camping Tents & Hiking Gear', 'slug' => 'camping-hiking'],
                        ],
                    ],
                    [
                        'name' => 'Tools & Hardware Equipment',
                        'slug' => 'tools-hardware',
                        'children' => [
                            ['name' => 'Power Drills, Saws & Impact Drivers', 'slug' => 'power-drills-saws'],
                            ['name' => 'Hand Tools & Socket Sets', 'slug' => 'hand-tools-sockets'],
                            ['name' => 'Toolboxes & Workshop Cabinets', 'slug' => 'toolboxes-storage'],
                            ['name' => 'Generators & Air Compressors', 'slug' => 'generators-compressors'],
                        ],
                    ],
                    [
                        'name' => 'Video Games & Consoles',
                        'slug' => 'video-games-consoles',
                        'children' => [
                            ['name' => 'PlayStation 5 & PS4 Consoles/Games', 'slug' => 'playstation-5-ps4'],
                            ['name' => 'Xbox Series X/S & Games', 'slug' => 'xbox-series-games'],
                            ['name' => 'Nintendo Switch Consoles/Games', 'slug' => 'nintendo-switch'],
                            ['name' => 'Retro Consoles & Vintage Games', 'slug' => 'retro-gaming'],
                            ['name' => 'VR Headsets & PC Gaming', 'slug' => 'vr-pc-gaming'],
                        ],
                    ],
                    [
                        'name' => 'Other Items',
                        'slug' => 'other-items',
                        'children' => [],
                    ],
                ]
            ],
            'services' => [
                'name' => 'Services',
                'slug' => 'services',
                'icon' => 'bi-tools',
                'description' => 'Skilled trades, repairs & professional services',
                'subcategories' => [
                    [
                        'name' => 'Automotive Services & Repair',
                        'slug' => 'automotive-services-repair',
                        'children' => [
                            ['name' => 'Mobile Mechanics & Diagnostics', 'slug' => 'mobile-mechanics'],
                            ['name' => 'Oil Change, Brakes & Tune-Ups', 'slug' => 'oil-change-brakes'],
                            ['name' => 'Tire Change & Seasonal Swap', 'slug' => 'tire-change-swap'],
                            ['name' => 'Auto Glass Repair & Window Tinting', 'slug' => 'auto-glass-tinting'],
                        ],
                    ],
                    [
                        'name' => 'Childcare & Babysitting',
                        'slug' => 'childcare-babysitting',
                        'children' => [
                            ['name' => 'Experienced Babysitters', 'slug' => 'babysitters-services'],
                            ['name' => 'Home Daycare Services', 'slug' => 'home-daycare-services'],
                            ['name' => 'Special Needs & Tutor Care', 'slug' => 'special-needs-care'],
                        ],
                    ],
                    [
                        'name' => 'Cleaners & Housekeeping',
                        'slug' => 'cleaners-housekeeping',
                        'children' => [
                            ['name' => 'Residential House Cleaners', 'slug' => 'house-cleaning-services'],
                            ['name' => 'Move-In / Move-Out Cleaning', 'slug' => 'movein-moveout-cleaning'],
                            ['name' => 'Commercial Office Janitorial', 'slug' => 'commercial-janitorial'],
                            ['name' => 'Carpet, Couch & Tile Cleaning', 'slug' => 'carpet-couch-cleaning'],
                        ],
                    ],
                    [
                        'name' => 'Computer & IT Tech Support',
                        'slug' => 'computer-tech-support',
                        'children' => [
                            ['name' => 'PC / Mac Repair & Diagnostics', 'slug' => 'pc-mac-repair'],
                            ['name' => 'Wi-Fi & Home Network Setup', 'slug' => 'wifi-network-setup'],
                            ['name' => 'Data Recovery & Virus Removal', 'slug' => 'data-recovery-virus'],
                            ['name' => 'Custom Website & App Development', 'slug' => 'web-app-services'],
                        ],
                    ],
                    [
                        'name' => 'Entertainment & Event Planning',
                        'slug' => 'entertainment-events',
                        'children' => [
                            ['name' => 'DJs, Bands & Live Performers', 'slug' => 'djs-bands-performers'],
                            ['name' => 'Event Catering & Food Trucks', 'slug' => 'event-catering'],
                            ['name' => 'Party Inflatables, Tents & Photo Booths', 'slug' => 'party-rentals-tents'],
                        ],
                    ],
                    [
                        'name' => 'Financial, Legal & Tax Services',
                        'slug' => 'financial-legal-tax',
                        'children' => [
                            ['name' => 'Personal & Corporate Tax Filing', 'slug' => 'tax-filing-services'],
                            ['name' => 'Bookkeeping & Accounting', 'slug' => 'bookkeeping-services'],
                            ['name' => 'Notary Public & Legal Advice', 'slug' => 'notary-legal-services'],
                            ['name' => 'Mortgage Brokers & Loans', 'slug' => 'mortgage-brokers'],
                        ],
                    ],
                    [
                        'name' => 'Fitness & Personal Training',
                        'slug' => 'fitness-training',
                        'children' => [
                            ['name' => 'Personal Trainers (In-Home / Gym)', 'slug' => 'personal-trainers'],
                            ['name' => 'Yoga & Pilates Instructors', 'slug' => 'yoga-pilates'],
                            ['name' => 'Martial Arts & Boxing Coaching', 'slug' => 'martial-arts-coaching'],
                        ],
                    ],
                    [
                        'name' => 'Home Renovation & Contractors',
                        'slug' => 'home-renovation-contractors',
                        'children' => [
                            ['name' => 'General Contractors & Remodeling', 'slug' => 'general-contractors'],
                            ['name' => 'Kitchen & Bathroom Renovation', 'slug' => 'kitchen-bath-renovations'],
                            ['name' => 'Painting, Staining & Drywall', 'slug' => 'painting-drywall-contractors'],
                            ['name' => 'Flooring, Hardwood & Tiling', 'slug' => 'flooring-tiling'],
                            ['name' => 'Roofing, Gutters & Siding', 'slug' => 'roofing-gutters'],
                            ['name' => 'Decks, Fences & Interlocking', 'slug' => 'decks-fences-interlock'],
                        ],
                    ],
                    [
                        'name' => 'Moving & Storage Services',
                        'slug' => 'moving-storage',
                        'children' => [
                            ['name' => 'Local & Long Distance Movers', 'slug' => 'local-long-distance-movers'],
                            ['name' => 'Small Moves & Man with a Van', 'slug' => 'man-with-van'],
                            ['name' => 'Packing & Heavy Furniture Help', 'slug' => 'packing-furniture-help'],
                        ],
                    ],
                    [
                        'name' => 'Photography & Videography',
                        'slug' => 'photography-videography',
                        'children' => [
                            ['name' => 'Wedding & Engagement Photos', 'slug' => 'wedding-photography'],
                            ['name' => 'Portraits, Headshots & Family', 'slug' => 'portrait-family-photo'],
                            ['name' => 'Real Estate & Drone Media', 'slug' => 'real-estate-drone-media'],
                        ],
                    ],
                    [
                        'name' => 'Plumbing, Electrical & HVAC',
                        'slug' => 'plumbing-electrical-hvac',
                        'children' => [
                            ['name' => 'Licensed Plumbers & Drain Cleaning', 'slug' => 'licensed-plumbers'],
                            ['name' => 'Certified Master Electricians', 'slug' => 'certified-electricians'],
                            ['name' => 'Heating, Furnace & AC Repair (HVAC)', 'slug' => 'hvac-furnace-ac'],
                        ],
                    ],
                    [
                        'name' => 'Real Estate & Property Management',
                        'slug' => 'real-estate-services',
                        'children' => [
                            ['name' => 'Property Managers & Airbnb Co-Hosts', 'slug' => 'property-management'],
                            ['name' => 'Real Estate Agents & Realtors', 'slug' => 'realtors-agents'],
                            ['name' => 'Home Inspections & Appraisals', 'slug' => 'home-inspections'],
                        ],
                    ],
                    [
                        'name' => 'Tutors, Classes & Language Lessons',
                        'slug' => 'tutors-languages',
                        'children' => [
                            ['name' => 'Math, Physics & Science Tutors', 'slug' => 'math-science-tutors'],
                            ['name' => 'English, French & ESL Tutors', 'slug' => 'esl-french-tutors'],
                            ['name' => 'Piano, Guitar & Vocal Lessons', 'slug' => 'music-vocal-lessons'],
                            ['name' => 'Driving Instructors & Lessons', 'slug' => 'driving-instructors'],
                        ],
                    ],
                    [
                        'name' => 'Other Services',
                        'slug' => 'other-services',
                        'children' => [],
                    ],
                ]
            ],
            'pets' => [
                'name' => 'Pets',
                'slug' => 'pets',
                'icon' => 'bi-heart',
                'description' => 'Pets, supplies, accessories & services',
                'subcategories' => [
                    [
                        'name' => 'Dogs & Puppies',
                        'slug' => 'dogs-puppies',
                        'children' => [
                            ['name' => 'Golden Retrievers & Labs', 'slug' => 'golden-retrievers-labs'],
                            ['name' => 'French Bulldogs & Pugs', 'slug' => 'french-bulldogs-pugs'],
                            ['name' => 'German Shepherds & Huskies', 'slug' => 'german-shepherds-huskies'],
                            ['name' => 'Doodles & Poodles', 'slug' => 'doodles-poodles'],
                            ['name' => 'Puppies for Adoption & Rescues', 'slug' => 'puppies-adoption-rescues'],
                        ],
                    ],
                    [
                        'name' => 'Cats & Kittens',
                        'slug' => 'cats-kittens',
                        'children' => [
                            ['name' => 'Kittens for Adoption', 'slug' => 'kittens-adoption'],
                            ['name' => 'Purebred Cats (Maine Coon, Bengal)', 'slug' => 'purebred-cats'],
                            ['name' => 'Rescue & Adult Cats', 'slug' => 'rescue-adult-cats'],
                        ],
                    ],
                    [
                        'name' => 'Birds & Parrots',
                        'slug' => 'birds-parrots',
                        'children' => [
                            ['name' => 'Parrots, Cockatiels & Conures', 'slug' => 'parrots-cockatiels'],
                            ['name' => 'Canaries & Finches', 'slug' => 'canaries-finches'],
                            ['name' => 'Bird Cages & Accessories', 'slug' => 'bird-cages-accessories'],
                        ],
                    ],
                    [
                        'name' => 'Fish, Tanks & Aquariums',
                        'slug' => 'fish-aquariums',
                        'children' => [
                            ['name' => 'Freshwater Fish & Cichlids', 'slug' => 'freshwater-fish'],
                            ['name' => 'Aquarium Tanks, Stands & Filters', 'slug' => 'aquariums-filters'],
                            ['name' => 'Saltwater & Coral Reef Supplies', 'slug' => 'saltwater-corals'],
                        ],
                    ],
                    [
                        'name' => 'Reptiles & Amphibians',
                        'slug' => 'reptiles-amphibians',
                        'children' => [
                            ['name' => 'Geckos, Bearded Dragons & Lizards', 'slug' => 'lizards-geckos'],
                            ['name' => 'Snakes & Pythons', 'slug' => 'snakes-pythons'],
                            ['name' => 'Turtles & Tortoises', 'slug' => 'turtles-tortoises'],
                            ['name' => 'Terrariums, Heat Lamps & Enclosures', 'slug' => 'terrariums-heat-lamps'],
                        ],
                    ],
                    [
                        'name' => 'Small Animals (Rabbits, Hamsters)',
                        'slug' => 'small-animals',
                        'children' => [
                            ['name' => 'Rabbits & Bunnies', 'slug' => 'rabbits-bunnies'],
                            ['name' => 'Guinea Pigs & Hamsters', 'slug' => 'guinea-pigs-hamsters'],
                            ['name' => 'Ferrets, Chinchillas & Cages', 'slug' => 'ferrets-chinchillas'],
                        ],
                    ],
                    [
                        'name' => 'Pet Accessories & Food Supplies',
                        'slug' => 'pet-accessories-supplies',
                        'children' => [
                            ['name' => 'Dog Beds, Crates & Leashes', 'slug' => 'dog-beds-crates'],
                            ['name' => 'Cat Trees, Towers & Scratchers', 'slug' => 'cat-trees-scratchers'],
                            ['name' => 'Premium Pet Food & Treats', 'slug' => 'pet-food-treats'],
                        ],
                    ],
                    [
                        'name' => 'Pet Sitting, Boarding & Grooming',
                        'slug' => 'pet-sitting-grooming',
                        'children' => [
                            ['name' => 'Dog Walking & Daily Daycare', 'slug' => 'dog-walking-daycare'],
                            ['name' => 'Pet Grooming & Spa Services', 'slug' => 'pet-grooming'],
                            ['name' => 'Overnight Boarding & Sitter Services', 'slug' => 'overnight-boarding'],
                        ],
                    ],
                    [
                        'name' => 'Lost & Found Pets',
                        'slug' => 'lost-found-pets',
                        'children' => [
                            ['name' => 'Lost Dogs & Puppies', 'slug' => 'lost-dogs'],
                            ['name' => 'Lost Cats & Kittens', 'slug' => 'lost-cats'],
                            ['name' => 'Found Animals', 'slug' => 'found-animals'],
                        ],
                    ],
                    [
                        'name' => 'Other Pets',
                        'slug' => 'other-pets',
                        'children' => [],
                    ],
                ]
            ],
            'community' => [
                'name' => 'Community',
                'slug' => 'community',
                'icon' => 'bi-people',
                'description' => 'Local events, groups, classes & announcements',
                'subcategories' => [
                    [
                        'name' => 'Activities, Groups & Clubs',
                        'slug' => 'activities-groups-clubs',
                        'children' => [
                            ['name' => 'Book Clubs & Discussion Groups', 'slug' => 'book-clubs'],
                            ['name' => 'Outdoor, Hiking & Running Clubs', 'slug' => 'outdoor-hiking-clubs'],
                            ['name' => 'Sports Leagues & Pick-Up Games', 'slug' => 'sports-leagues'],
                            ['name' => 'Board Game & Chess Clubs', 'slug' => 'board-game-clubs'],
                        ],
                    ],
                    [
                        'name' => 'Artists & Musicians',
                        'slug' => 'artists-musicians',
                        'children' => [
                            ['name' => 'Bands Seeking Musicians', 'slug' => 'bands-seeking-musicians'],
                            ['name' => 'Jam Sessions & Music Collaborations', 'slug' => 'jam-sessions'],
                            ['name' => 'Painters, Illustrators & Artists', 'slug' => 'painters-illustrators'],
                        ],
                    ],
                    [
                        'name' => 'Classes, Workshops & Lessons',
                        'slug' => 'classes-workshops-lessons',
                        'children' => [
                            ['name' => 'Art, Pottery & Painting Workshops', 'slug' => 'art-pottery-workshops'],
                            ['name' => 'Cooking & Baking Classes', 'slug' => 'cooking-classes'],
                            ['name' => 'Dance & Salsa Classes', 'slug' => 'dance-classes'],
                        ],
                    ],
                    [
                        'name' => 'Community Events & Gatherings',
                        'slug' => 'community-events',
                        'children' => [
                            ['name' => 'Local Festivals & Fairs', 'slug' => 'festivals-fairs'],
                            ['name' => 'Farmers Markets & Craft Shows', 'slug' => 'farmers-markets'],
                            ['name' => 'Fundraisers & Charity Events', 'slug' => 'fundraisers-charity'],
                        ],
                    ],
                    [
                        'name' => 'Lost & Found Items',
                        'slug' => 'lost-found-items',
                        'children' => [
                            ['name' => 'Lost Wallets, IDs & Passports', 'slug' => 'lost-wallets-ids'],
                            ['name' => 'Lost Phones & Electronics', 'slug' => 'lost-phones-electronics'],
                            ['name' => 'Lost Keys & Jewelry', 'slug' => 'lost-keys-jewelry'],
                        ],
                    ],
                    [
                        'name' => 'Volunteers & Charity Causes',
                        'slug' => 'volunteers-charity',
                        'children' => [
                            ['name' => 'Food Bank Volunteers', 'slug' => 'food-bank-volunteers'],
                            ['name' => 'Animal Shelter Volunteers', 'slug' => 'animal-shelter-volunteers'],
                            ['name' => 'Community Cleanup & Tree Planting', 'slug' => 'community-cleanups'],
                        ],
                    ],
                    [
                        'name' => 'Announcements & Notifications',
                        'slug' => 'announcements',
                        'children' => [
                            ['name' => 'Neighborhood Public Notices', 'slug' => 'public-notices'],
                            ['name' => 'Lost & Found Notices', 'slug' => 'lost-found-notices'],
                        ],
                    ],
                    [
                        'name' => 'Other Community',
                        'slug' => 'other-community',
                        'children' => [],
                    ],
                ]
            ],
            'vacation-rentals' => [
                'name' => 'Vacation Rentals',
                'slug' => 'vacation-rentals',
                'icon' => 'bi-compass',
                'description' => 'Cottages, cabins, chalets & getaways',
                'subcategories' => [
                    [
                        'name' => 'Cottages & Cabins',
                        'slug' => 'cottages-cabins',
                        'children' => [
                            ['name' => 'Lakefront Cottages with Dock', 'slug' => 'lakefront-cottages'],
                            ['name' => 'Rustic Forest Cabins', 'slug' => 'forest-cabins'],
                            ['name' => 'Pet-Friendly Cottage Rentals', 'slug' => 'pet-friendly-cottages'],
                            ['name' => 'Luxury Cottages with Hot Tub', 'slug' => 'luxury-cottages'],
                        ],
                    ],
                    [
                        'name' => 'Chalets & Ski Lodges',
                        'slug' => 'chalets-ski-lodges',
                        'children' => [
                            ['name' => 'Ski-in / Ski-out Mountain Chalets', 'slug' => 'ski-in-chalets'],
                            ['name' => 'Hot Tub Alpine Lodges', 'slug' => 'alpine-lodges'],
                        ],
                    ],
                    [
                        'name' => 'Waterfront & Beachfront Homes',
                        'slug' => 'waterfront-beachfront',
                        'children' => [
                            ['name' => 'Sandy Beachfront Villas', 'slug' => 'beachfront-villas'],
                            ['name' => 'Ocean & Lake Sunset Houses', 'slug' => 'sunset-houses'],
                        ],
                    ],
                    [
                        'name' => 'Condos & Townhomes',
                        'slug' => 'vacation-condos',
                        'children' => [
                            ['name' => 'Resort Condos with Pool', 'slug' => 'resort-condos'],
                            ['name' => 'Downtown Urban Vacation Suites', 'slug' => 'urban-vacation-suites'],
                        ],
                    ],
                    [
                        'name' => 'RVs, Campgrounds & Glamping',
                        'slug' => 'rvs-campgrounds',
                        'children' => [
                            ['name' => 'Luxury Glamping Domes & Yurts', 'slug' => 'glamping-domes'],
                            ['name' => 'RV Campsites & Trailer Parks', 'slug' => 'rv-campsites'],
                            ['name' => 'Treehouses & Unique Stays', 'slug' => 'treehouses-unique'],
                        ],
                    ],
                    [
                        'name' => 'Country Houses & Ranches',
                        'slug' => 'country-houses',
                        'children' => [
                            ['name' => 'Farm Stays & Equestrian Ranches', 'slug' => 'farm-stays-ranches'],
                            ['name' => 'Winery & Vineyard Country Homes', 'slug' => 'winery-country-homes'],
                        ],
                    ],
                    [
                        'name' => 'Bed & Breakfasts / Boutique Stays',
                        'slug' => 'bed-and-breakfasts',
                        'children' => [
                            ['name' => 'Historic Heritage B&Bs', 'slug' => 'historic-bbs'],
                            ['name' => 'Boutique Country Inns', 'slug' => 'boutique-country-inns'],
                        ],
                    ],
                    [
                        'name' => 'Other Vacation Rentals',
                        'slug' => 'other-vacation-rentals',
                        'children' => [],
                    ],
                ]
            ],
        ];
    }
}
