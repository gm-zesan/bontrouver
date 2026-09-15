<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinceMap = Province::pluck('id', 'code');

        $cities = [
            // Ontario (ON)
            ['name' => 'Toronto', 'province_code' => 'ON', 'latitude' => 43.653226, 'longitude' => -79.383184, 'population' => 2794356, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Ottawa', 'province_code' => 'ON', 'latitude' => 45.421530, 'longitude' => -75.697193, 'population' => 1017449, 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'Mississauga', 'province_code' => 'ON', 'latitude' => 43.589045, 'longitude' => -79.644120, 'population' => 717961, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Brampton', 'province_code' => 'ON', 'latitude' => 43.731548, 'longitude' => -79.762418, 'population' => 656480, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Hamilton', 'province_code' => 'ON', 'latitude' => 43.255721, 'longitude' => -79.871102, 'population' => 569353, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'London', 'province_code' => 'ON', 'latitude' => 42.984923, 'longitude' => -81.245277, 'population' => 422324, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Markham', 'province_code' => 'ON', 'latitude' => 43.856100, 'longitude' => -79.337019, 'population' => 338503, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Vaughan', 'province_code' => 'ON', 'latitude' => 43.856319, 'longitude' => -79.508537, 'population' => 323103, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Kitchener', 'province_code' => 'ON', 'latitude' => 43.451639, 'longitude' => -80.492534, 'population' => 256885, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Windsor', 'province_code' => 'ON', 'latitude' => 42.314937, 'longitude' => -83.036363, 'population' => 229660, 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Richmond Hill', 'province_code' => 'ON', 'latitude' => 43.882840, 'longitude' => -79.440280, 'population' => 202022, 'is_featured' => false, 'sort_order' => 11],
            ['name' => 'Oakville', 'province_code' => 'ON', 'latitude' => 43.467517, 'longitude' => -79.687666, 'population' => 213759, 'is_featured' => false, 'sort_order' => 12],
            ['name' => 'Burlington', 'province_code' => 'ON', 'latitude' => 43.325520, 'longitude' => -79.799032, 'population' => 186948, 'is_featured' => false, 'sort_order' => 13],
            ['name' => 'Greater Sudbury', 'province_code' => 'ON', 'latitude' => 46.491684, 'longitude' => -80.993029, 'population' => 166004, 'is_featured' => false, 'sort_order' => 14],
            ['name' => 'Oshawa', 'province_code' => 'ON', 'latitude' => 43.897095, 'longitude' => -78.865791, 'population' => 175383, 'is_featured' => false, 'sort_order' => 15],
            ['name' => 'Barrie', 'province_code' => 'ON', 'latitude' => 44.389356, 'longitude' => -79.690331, 'population' => 147829, 'is_featured' => false, 'sort_order' => 16],
            ['name' => 'Kingston', 'province_code' => 'ON', 'latitude' => 44.231172, 'longitude' => -76.485954, 'population' => 132472, 'is_featured' => false, 'sort_order' => 17],
            ['name' => 'Guelph', 'province_code' => 'ON', 'latitude' => 43.544805, 'longitude' => -80.248167, 'population' => 143740, 'is_featured' => false, 'sort_order' => 18],
            ['name' => 'Waterloo', 'province_code' => 'ON', 'latitude' => 43.464258, 'longitude' => -80.520410, 'population' => 121436, 'is_featured' => false, 'sort_order' => 19],
            ['name' => 'Thunder Bay', 'province_code' => 'ON', 'latitude' => 48.380895, 'longitude' => -89.247682, 'population' => 108843, 'is_featured' => false, 'sort_order' => 20],

            // Quebec (QC)
            ['name' => 'Montreal', 'province_code' => 'QC', 'latitude' => 45.501689, 'longitude' => -73.567256, 'population' => 1762949, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Quebec City', 'province_code' => 'QC', 'latitude' => 46.813878, 'longitude' => -71.207981, 'population' => 549459, 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'Laval', 'province_code' => 'QC', 'latitude' => 45.606560, 'longitude' => -73.712395, 'population' => 438366, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Gatineau', 'province_code' => 'QC', 'latitude' => 45.476544, 'longitude' => -75.701272, 'population' => 291041, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Longueuil', 'province_code' => 'QC', 'latitude' => 45.531206, 'longitude' => -73.518059, 'population' => 254483, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Sherbrooke', 'province_code' => 'QC', 'latitude' => 45.404176, 'longitude' => -71.892906, 'population' => 172950, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Saguenay', 'province_code' => 'QC', 'latitude' => 48.427926, 'longitude' => -71.068642, 'population' => 144723, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Levis', 'province_code' => 'QC', 'latitude' => 46.803273, 'longitude' => -71.177926, 'population' => 149683, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Trois-Rivieres', 'province_code' => 'QC', 'latitude' => 46.343209, 'longitude' => -72.542072, 'population' => 139163, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Terrebonne', 'province_code' => 'QC', 'latitude' => 45.692994, 'longitude' => -73.633096, 'population' => 119944, 'is_featured' => false, 'sort_order' => 10],

            // British Columbia (BC)
            ['name' => 'Vancouver', 'province_code' => 'BC', 'latitude' => 49.282729, 'longitude' => -123.120738, 'population' => 662248, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Surrey', 'province_code' => 'BC', 'latitude' => 49.191347, 'longitude' => -122.849012, 'population' => 568322, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Burnaby', 'province_code' => 'BC', 'latitude' => 49.248809, 'longitude' => -122.980510, 'population' => 249125, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Richmond', 'province_code' => 'BC', 'latitude' => 49.166590, 'longitude' => -123.133568, 'population' => 209937, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Victoria', 'province_code' => 'BC', 'latitude' => 48.428421, 'longitude' => -123.365644, 'population' => 91861, 'is_featured' => true, 'sort_order' => 5],
            ['name' => 'Kelowna', 'province_code' => 'BC', 'latitude' => 49.887952, 'longitude' => -119.496011, 'population' => 144576, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Abbotsford', 'province_code' => 'BC', 'latitude' => 49.050438, 'longitude' => -122.304470, 'population' => 153524, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Coquitlam', 'province_code' => 'BC', 'latitude' => 49.283764, 'longitude' => -122.793206, 'population' => 148625, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Langley', 'province_code' => 'BC', 'latitude' => 49.104430, 'longitude' => -122.582672, 'population' => 132603, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Kamloops', 'province_code' => 'BC', 'latitude' => 50.674522, 'longitude' => -120.327293, 'population' => 97902, 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Nanaimo', 'province_code' => 'BC', 'latitude' => 49.165884, 'longitude' => -123.940065, 'population' => 99863, 'is_featured' => false, 'sort_order' => 11],

            // Alberta (AB)
            ['name' => 'Calgary', 'province_code' => 'AB', 'latitude' => 51.044733, 'longitude' => -114.071883, 'population' => 1306784, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Edmonton', 'province_code' => 'AB', 'latitude' => 53.546124, 'longitude' => -113.493823, 'population' => 1010899, 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'Red Deer', 'province_code' => 'AB', 'latitude' => 52.268111, 'longitude' => -113.811241, 'population' => 100844, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Lethbridge', 'province_code' => 'AB', 'latitude' => 49.695564, 'longitude' => -112.845070, 'population' => 98406, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'St. Albert', 'province_code' => 'AB', 'latitude' => 53.630475, 'longitude' => -113.625642, 'population' => 68232, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Medicine Hat', 'province_code' => 'AB', 'latitude' => 50.041670, 'longitude' => -110.677551, 'population' => 63271, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Grande Prairie', 'province_code' => 'AB', 'latitude' => 55.169956, 'longitude' => -118.798622, 'population' => 63166, 'is_featured' => false, 'sort_order' => 7],

            // Manitoba (MB)
            ['name' => 'Winnipeg', 'province_code' => 'MB', 'latitude' => 49.895136, 'longitude' => -97.138374, 'population' => 749607, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Brandon', 'province_code' => 'MB', 'latitude' => 49.848470, 'longitude' => -99.950070, 'population' => 51311, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Steinbach', 'province_code' => 'MB', 'latitude' => 49.525833, 'longitude' => -96.683889, 'population' => 17806, 'is_featured' => false, 'sort_order' => 3],

            // Saskatchewan (SK)
            ['name' => 'Saskatoon', 'province_code' => 'SK', 'latitude' => 52.133214, 'longitude' => -106.670046, 'population' => 266141, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Regina', 'province_code' => 'SK', 'latitude' => 50.454722, 'longitude' => -104.606667, 'population' => 226404, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Prince Albert', 'province_code' => 'SK', 'latitude' => 53.203333, 'longitude' => -105.753056, 'population' => 35926, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Moose Jaw', 'province_code' => 'SK', 'latitude' => 50.393333, 'longitude' => -105.551944, 'population' => 33665, 'is_featured' => false, 'sort_order' => 4],

            // Nova Scotia (NS)
            ['name' => 'Halifax', 'province_code' => 'NS', 'latitude' => 44.648764, 'longitude' => -63.575239, 'population' => 439819, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Sydney', 'province_code' => 'NS', 'latitude' => 46.136780, 'longitude' => -60.183100, 'population' => 29904, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Dartmouth', 'province_code' => 'NS', 'latitude' => 44.665220, 'longitude' => -63.567690, 'population' => 92233, 'is_featured' => false, 'sort_order' => 3],

            // New Brunswick (NB)
            ['name' => 'Moncton', 'province_code' => 'NB', 'latitude' => 46.087817, 'longitude' => -64.778231, 'population' => 79470, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Saint John', 'province_code' => 'NB', 'latitude' => 45.273315, 'longitude' => -66.063308, 'population' => 69885, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Fredericton', 'province_code' => 'NB', 'latitude' => 45.963589, 'longitude' => -66.643115, 'population' => 63116, 'is_featured' => false, 'sort_order' => 3],

            // Newfoundland and Labrador (NL)
            ['name' => "St. John's", 'province_code' => 'NL', 'latitude' => 47.561510, 'longitude' => -52.712577, 'population' => 110525, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Mount Pearl', 'province_code' => 'NL', 'latitude' => 47.518889, 'longitude' => -52.784444, 'population' => 22477, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Corner Brook', 'province_code' => 'NL', 'latitude' => 48.950000, 'longitude' => -57.950000, 'population' => 19806, 'is_featured' => false, 'sort_order' => 3],

            // Prince Edward Island (PE)
            ['name' => 'Charlottetown', 'province_code' => 'PE', 'latitude' => 46.238240, 'longitude' => -63.131070, 'population' => 38809, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Summerside', 'province_code' => 'PE', 'latitude' => 46.395833, 'longitude' => -63.788889, 'population' => 14829, 'is_featured' => false, 'sort_order' => 2],

            // Northwest Territories (NT)
            ['name' => 'Yellowknife', 'province_code' => 'NT', 'latitude' => 62.454000, 'longitude' => -114.371800, 'population' => 20340, 'is_featured' => false, 'sort_order' => 1],

            // Yukon (YT)
            ['name' => 'Whitehorse', 'province_code' => 'YT', 'latitude' => 60.721200, 'longitude' => -135.056800, 'population' => 28201, 'is_featured' => false, 'sort_order' => 1],

            // Nunavut (NU)
            ['name' => 'Iqaluit', 'province_code' => 'NU', 'latitude' => 63.746700, 'longitude' => -68.517000, 'population' => 7429, 'is_featured' => false, 'sort_order' => 1],
        ];

        foreach ($cities as $cityData) {
            $provinceId = $provinceMap[$cityData['province_code']] ?? null;
            if (!$provinceId) {
                continue;
            }

            $slug = Str::slug($cityData['name']);

            City::updateOrCreate(
                ['slug' => $slug],
                [
                    'province_id' => $provinceId,
                    'name' => $cityData['name'],
                    'slug' => $slug,
                    'latitude' => $cityData['latitude'],
                    'longitude' => $cityData['longitude'],
                    'population' => $cityData['population'],
                    'is_featured' => $cityData['is_featured'],
                    'is_active' => true,
                    'sort_order' => $cityData['sort_order'],
                ]
            );
        }
    }
}
