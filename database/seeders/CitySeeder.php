<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinceMap = Province::pluck('id', 'code');

        $cities = [
            // ==========================================
            // ONTARIO (ON)
            // ==========================================
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
            ['name' => 'Cambridge', 'province_code' => 'ON', 'latitude' => 43.361621, 'longitude' => -80.314428, 'population' => 138479, 'is_featured' => false, 'sort_order' => 19],
            ['name' => 'Whitby', 'province_code' => 'ON', 'latitude' => 43.897545, 'longitude' => -78.942932, 'population' => 138501, 'is_featured' => false, 'sort_order' => 20],
            ['name' => 'Waterloo', 'province_code' => 'ON', 'latitude' => 43.464258, 'longitude' => -80.520410, 'population' => 121436, 'is_featured' => false, 'sort_order' => 21],
            ['name' => 'Thunder Bay', 'province_code' => 'ON', 'latitude' => 48.380895, 'longitude' => -89.247682, 'population' => 108843, 'is_featured' => false, 'sort_order' => 22],
            ['name' => 'Brantford', 'province_code' => 'ON', 'latitude' => 43.139398, 'longitude' => -80.264426, 'population' => 104688, 'is_featured' => false, 'sort_order' => 23],
            ['name' => 'Pickering', 'province_code' => 'ON', 'latitude' => 43.838412, 'longitude' => -79.086765, 'population' => 99186, 'is_featured' => false, 'sort_order' => 24],
            ['name' => 'Niagara Falls', 'province_code' => 'ON', 'latitude' => 43.089558, 'longitude' => -79.084944, 'population' => 94415, 'is_featured' => false, 'sort_order' => 25],
            ['name' => 'Peterborough', 'province_code' => 'ON', 'latitude' => 44.309058, 'longitude' => -78.319747, 'population' => 83651, 'is_featured' => false, 'sort_order' => 26],
            ['name' => 'Kawartha Lakes', 'province_code' => 'ON', 'latitude' => 44.356500, 'longitude' => -78.739700, 'population' => 79247, 'is_featured' => false, 'sort_order' => 27],
            ['name' => 'Sault Ste. Marie', 'province_code' => 'ON', 'latitude' => 46.513580, 'longitude' => -84.335763, 'population' => 72051, 'is_featured' => false, 'sort_order' => 28],
            ['name' => 'Sarnia', 'province_code' => 'ON', 'latitude' => 42.974534, 'longitude' => -82.406563, 'population' => 72047, 'is_featured' => false, 'sort_order' => 29],
            ['name' => 'Norfolk County', 'province_code' => 'ON', 'latitude' => 42.748300, 'longitude' => -80.395700, 'population' => 67490, 'is_featured' => false, 'sort_order' => 30],
            ['name' => 'Welland', 'province_code' => 'ON', 'latitude' => 42.992200, 'longitude' => -79.248300, 'population' => 55750, 'is_featured' => false, 'sort_order' => 31],
            ['name' => 'Belleville', 'province_code' => 'ON', 'latitude' => 44.162760, 'longitude' => -77.383191, 'population' => 55071, 'is_featured' => false, 'sort_order' => 32],
            ['name' => 'North Bay', 'province_code' => 'ON', 'latitude' => 46.309115, 'longitude' => -79.460824, 'population' => 52662, 'is_featured' => false, 'sort_order' => 33],
            ['name' => 'Cornwall', 'province_code' => 'ON', 'latitude' => 45.021271, 'longitude' => -74.730347, 'population' => 47845, 'is_featured' => false, 'sort_order' => 34],
            ['name' => 'St. Thomas', 'province_code' => 'ON', 'latitude' => 42.778800, 'longitude' => -81.192700, 'population' => 42840, 'is_featured' => false, 'sort_order' => 35],
            ['name' => 'Woodstock', 'province_code' => 'ON', 'latitude' => 43.130600, 'longitude' => -80.746700, 'population' => 46705, 'is_featured' => false, 'sort_order' => 36],
            ['name' => 'Bowmanville', 'province_code' => 'ON', 'latitude' => 43.913400, 'longitude' => -78.688100, 'population' => 43000, 'is_featured' => false, 'sort_order' => 37],
            ['name' => 'Stratford', 'province_code' => 'ON', 'latitude' => 43.370000, 'longitude' => -80.982200, 'population' => 33232, 'is_featured' => false, 'sort_order' => 38],
            ['name' => 'Orillia', 'province_code' => 'ON', 'latitude' => 44.608700, 'longitude' => -79.420800, 'population' => 33411, 'is_featured' => false, 'sort_order' => 39],
            ['name' => 'Orangeville', 'province_code' => 'ON', 'latitude' => 43.919700, 'longitude' => -80.094300, 'population' => 30167, 'is_featured' => false, 'sort_order' => 40],
            ['name' => 'Bradford West Gwillimbury', 'province_code' => 'ON', 'latitude' => 44.116700, 'longitude' => -79.566700, 'population' => 42880, 'is_featured' => false, 'sort_order' => 41],
            ['name' => 'Timmins', 'province_code' => 'ON', 'latitude' => 48.475800, 'longitude' => -81.330400, 'population' => 41145, 'is_featured' => false, 'sort_order' => 42],
            ['name' => 'Owen Sound', 'province_code' => 'ON', 'latitude' => 44.566700, 'longitude' => -80.933300, 'population' => 21612, 'is_featured' => false, 'sort_order' => 43],
            ['name' => 'Brockville', 'province_code' => 'ON', 'latitude' => 44.588900, 'longitude' => -75.683300, 'population' => 21854, 'is_featured' => false, 'sort_order' => 44],
            ['name' => 'Collingwood', 'province_code' => 'ON', 'latitude' => 44.500000, 'longitude' => -80.216700, 'population' => 24811, 'is_featured' => false, 'sort_order' => 45],
            ['name' => 'Cobourg', 'province_code' => 'ON', 'latitude' => 43.966700, 'longitude' => -78.166700, 'population' => 20519, 'is_featured' => false, 'sort_order' => 46],
            ['name' => 'Midland', 'province_code' => 'ON', 'latitude' => 44.750000, 'longitude' => -79.883300, 'population' => 17817, 'is_featured' => false, 'sort_order' => 47],
            ['name' => 'Huntsville', 'province_code' => 'ON', 'latitude' => 45.333300, 'longitude' => -79.216700, 'population' => 21147, 'is_featured' => false, 'sort_order' => 48],
            ['name' => 'Bracebridge', 'province_code' => 'ON', 'latitude' => 45.033300, 'longitude' => -79.316700, 'population' => 17305, 'is_featured' => false, 'sort_order' => 49],
            ['name' => 'Pembroke', 'province_code' => 'ON', 'latitude' => 45.816700, 'longitude' => -77.100000, 'population' => 14364, 'is_featured' => false, 'sort_order' => 50],
            ['name' => 'Kenora', 'province_code' => 'ON', 'latitude' => 49.766700, 'longitude' => -94.483300, 'population' => 14967, 'is_featured' => false, 'sort_order' => 51],
            ['name' => 'Port Hope', 'province_code' => 'ON', 'latitude' => 43.950000, 'longitude' => -78.300000, 'population' => 17294, 'is_featured' => false, 'sort_order' => 52],
            ['name' => 'Wasaga Beach', 'province_code' => 'ON', 'latitude' => 44.516700, 'longitude' => -80.016700, 'population' => 24862, 'is_featured' => false, 'sort_order' => 53],
            ['name' => 'Hawkesbury', 'province_code' => 'ON', 'latitude' => 45.608300, 'longitude' => -74.604200, 'population' => 10194, 'is_featured' => false, 'sort_order' => 54],
            ['name' => 'Tillsonburg', 'province_code' => 'ON', 'latitude' => 42.866700, 'longitude' => -80.733300, 'population' => 18615, 'is_featured' => false, 'sort_order' => 55],
            ['name' => 'Elliot Lake', 'province_code' => 'ON', 'latitude' => 46.383300, 'longitude' => -82.633300, 'population' => 11372, 'is_featured' => false, 'sort_order' => 56],
            ['name' => 'Gravenhurst', 'province_code' => 'ON', 'latitude' => 44.916700, 'longitude' => -79.366700, 'population' => 13157, 'is_featured' => false, 'sort_order' => 57],
            ['name' => 'Parry Sound', 'province_code' => 'ON', 'latitude' => 45.348300, 'longitude' => -80.035000, 'population' => 6879, 'is_featured' => false, 'sort_order' => 58],
            ['name' => 'Smiths Falls', 'province_code' => 'ON', 'latitude' => 44.900000, 'longitude' => -76.016700, 'population' => 9254, 'is_featured' => false, 'sort_order' => 59],
            ['name' => 'Kapuskasing', 'province_code' => 'ON', 'latitude' => 49.416700, 'longitude' => -82.433300, 'population' => 8057, 'is_featured' => false, 'sort_order' => 60],
            ['name' => 'Kirkland Lake', 'province_code' => 'ON', 'latitude' => 48.150000, 'longitude' => -80.033300, 'population' => 7750, 'is_featured' => false, 'sort_order' => 61],
            ['name' => 'Fort Frances', 'province_code' => 'ON', 'latitude' => 48.600000, 'longitude' => -93.400000, 'population' => 7466, 'is_featured' => false, 'sort_order' => 62],
            ['name' => 'Dryden', 'province_code' => 'ON', 'latitude' => 49.783300, 'longitude' => -92.833300, 'population' => 7388, 'is_featured' => false, 'sort_order' => 63],
            ['name' => 'Sioux Lookout', 'province_code' => 'ON', 'latitude' => 50.100000, 'longitude' => -91.916700, 'population' => 5839, 'is_featured' => false, 'sort_order' => 64],

            // ==========================================
            // QUEBEC (QC)
            // ==========================================
            ['name' => 'Montreal', 'province_code' => 'QC', 'latitude' => 45.501689, 'longitude' => -73.567256, 'population' => 1762949, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Quebec City', 'province_code' => 'QC', 'latitude' => 46.813878, 'longitude' => -71.207981, 'population' => 549459, 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'Laval', 'province_code' => 'QC', 'latitude' => 45.606560, 'longitude' => -73.712395, 'population' => 438366, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Gatineau', 'province_code' => 'QC', 'latitude' => 45.476544, 'longitude' => -75.701272, 'population' => 291041, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Longueuil', 'province_code' => 'QC', 'latitude' => 45.531206, 'longitude' => -73.518059, 'population' => 254483, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Sherbrooke', 'province_code' => 'QC', 'latitude' => 45.404176, 'longitude' => -71.892906, 'population' => 172950, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Levis', 'province_code' => 'QC', 'latitude' => 46.803273, 'longitude' => -71.177926, 'population' => 149683, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Saguenay', 'province_code' => 'QC', 'latitude' => 48.427926, 'longitude' => -71.068642, 'population' => 144723, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Trois-Rivieres', 'province_code' => 'QC', 'latitude' => 46.343209, 'longitude' => -72.542072, 'population' => 139163, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Terrebonne', 'province_code' => 'QC', 'latitude' => 45.692994, 'longitude' => -73.633096, 'population' => 119944, 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Saint-Jean-sur-Richelieu', 'province_code' => 'QC', 'latitude' => 45.305600, 'longitude' => -73.253300, 'population' => 98036, 'is_featured' => false, 'sort_order' => 11],
            ['name' => 'Brossard', 'province_code' => 'QC', 'latitude' => 45.466700, 'longitude' => -73.450000, 'population' => 91525, 'is_featured' => false, 'sort_order' => 12],
            ['name' => 'Repentigny', 'province_code' => 'QC', 'latitude' => 45.733300, 'longitude' => -73.450000, 'population' => 86100, 'is_featured' => false, 'sort_order' => 13],
            ['name' => 'Saint-Jerome', 'province_code' => 'QC', 'latitude' => 45.783300, 'longitude' => -74.000000, 'population' => 80212, 'is_featured' => false, 'sort_order' => 14],
            ['name' => 'Drummondville', 'province_code' => 'QC', 'latitude' => 45.883300, 'longitude' => -72.483300, 'population' => 79258, 'is_featured' => false, 'sort_order' => 15],
            ['name' => 'Granby', 'province_code' => 'QC', 'latitude' => 45.400000, 'longitude' => -72.733300, 'population' => 69025, 'is_featured' => false, 'sort_order' => 16],
            ['name' => 'Blainville', 'province_code' => 'QC', 'latitude' => 45.666700, 'longitude' => -73.866700, 'population' => 59819, 'is_featured' => false, 'sort_order' => 17],
            ['name' => 'Saint-Hyacinthe', 'province_code' => 'QC', 'latitude' => 45.616700, 'longitude' => -72.950000, 'population' => 57239, 'is_featured' => false, 'sort_order' => 18],
            ['name' => 'Shawinigan', 'province_code' => 'QC', 'latitude' => 46.566700, 'longitude' => -72.750000, 'population' => 49620, 'is_featured' => false, 'sort_order' => 19],
            ['name' => 'Dollard-des-Ormeaux', 'province_code' => 'QC', 'latitude' => 45.483300, 'longitude' => -73.816700, 'population' => 48403, 'is_featured' => false, 'sort_order' => 20],
            ['name' => 'Rimouski', 'province_code' => 'QC', 'latitude' => 48.450000, 'longitude' => -68.533300, 'population' => 48935, 'is_featured' => false, 'sort_order' => 21],
            ['name' => 'Chateauguay', 'province_code' => 'QC', 'latitude' => 45.366700, 'longitude' => -73.750000, 'population' => 50815, 'is_featured' => false, 'sort_order' => 22],
            ['name' => 'Victoriaville', 'province_code' => 'QC', 'latitude' => 46.050000, 'longitude' => -71.966700, 'population' => 47760, 'is_featured' => false, 'sort_order' => 23],
            ['name' => 'Saint-Eustache', 'province_code' => 'QC', 'latitude' => 45.566700, 'longitude' => -73.900000, 'population' => 45276, 'is_featured' => false, 'sort_order' => 24],
            ['name' => 'Mascouche', 'province_code' => 'QC', 'latitude' => 45.750000, 'longitude' => -73.600000, 'population' => 51183, 'is_featured' => false, 'sort_order' => 25],
            ['name' => 'Mirabel', 'province_code' => 'QC', 'latitude' => 45.650000, 'longitude' => -74.083300, 'population' => 61108, 'is_featured' => false, 'sort_order' => 26],
            ['name' => 'Rouyn-Noranda', 'province_code' => 'QC', 'latitude' => 48.233300, 'longitude' => -79.016700, 'population' => 42313, 'is_featured' => false, 'sort_order' => 27],
            ['name' => 'Boucherville', 'province_code' => 'QC', 'latitude' => 45.600000, 'longitude' => -73.450000, 'population' => 41743, 'is_featured' => false, 'sort_order' => 28],
            ['name' => 'Salaberry-de-Valleyfield', 'province_code' => 'QC', 'latitude' => 45.250000, 'longitude' => -74.133300, 'population' => 42957, 'is_featured' => false, 'sort_order' => 29],
            ['name' => 'Sorel-Tracy', 'province_code' => 'QC', 'latitude' => 46.033300, 'longitude' => -73.116700, 'population' => 35165, 'is_featured' => false, 'sort_order' => 30],
            ['name' => 'Vaudreuil-Dorion', 'province_code' => 'QC', 'latitude' => 45.400000, 'longitude' => -74.033300, 'population' => 40035, 'is_featured' => false, 'sort_order' => 31],
            ['name' => 'Val-d\'Or', 'province_code' => 'QC', 'latitude' => 48.100000, 'longitude' => -77.783300, 'population' => 32752, 'is_featured' => false, 'sort_order' => 32],
            ['name' => 'Saint-Georges', 'province_code' => 'QC', 'latitude' => 46.116700, 'longitude' => -70.666700, 'population' => 32935, 'is_featured' => false, 'sort_order' => 33],
            ['name' => 'Alma', 'province_code' => 'QC', 'latitude' => 48.550000, 'longitude' => -71.650000, 'population' => 30331, 'is_featured' => false, 'sort_order' => 34],
            ['name' => 'Sainte-Julie', 'province_code' => 'QC', 'latitude' => 45.583300, 'longitude' => -73.333300, 'population' => 30044, 'is_featured' => false, 'sort_order' => 35],
            ['name' => 'Thetford Mines', 'province_code' => 'QC', 'latitude' => 46.083300, 'longitude' => -71.300000, 'population' => 26072, 'is_featured' => false, 'sort_order' => 36],
            ['name' => 'Sept-Iles', 'province_code' => 'QC', 'latitude' => 50.216700, 'longitude' => -66.383300, 'population' => 25400, 'is_featured' => false, 'sort_order' => 37],
            ['name' => 'Magog', 'province_code' => 'QC', 'latitude' => 45.266700, 'longitude' => -72.150000, 'population' => 28312, 'is_featured' => false, 'sort_order' => 38],
            ['name' => 'Mont-Tremblant', 'province_code' => 'QC', 'latitude' => 46.116700, 'longitude' => -74.600000, 'population' => 10992, 'is_featured' => false, 'sort_order' => 39],
            ['name' => 'Baie-Comeau', 'province_code' => 'QC', 'latitude' => 49.216700, 'longitude' => -68.150000, 'population' => 21536, 'is_featured' => false, 'sort_order' => 40],
            ['name' => 'Gaspe', 'province_code' => 'QC', 'latitude' => 48.833300, 'longitude' => -64.483300, 'population' => 15063, 'is_featured' => false, 'sort_order' => 41],
            ['name' => 'Riviere-du-Loup', 'province_code' => 'QC', 'latitude' => 47.833300, 'longitude' => -69.533300, 'population' => 20118, 'is_featured' => false, 'sort_order' => 42],
            ['name' => 'Matane', 'province_code' => 'QC', 'latitude' => 48.850000, 'longitude' => -67.533300, 'population' => 13987, 'is_featured' => false, 'sort_order' => 43],
            ['name' => 'Roberval', 'province_code' => 'QC', 'latitude' => 48.516700, 'longitude' => -72.233300, 'population' => 10046, 'is_featured' => false, 'sort_order' => 44],
            ['name' => 'Chibougamau', 'province_code' => 'QC', 'latitude' => 49.916700, 'longitude' => -74.366700, 'population' => 7504, 'is_featured' => false, 'sort_order' => 45],
            ['name' => 'Kuujjuaq', 'province_code' => 'QC', 'latitude' => 58.100000, 'longitude' => -68.400000, 'population' => 2668, 'is_featured' => false, 'sort_order' => 46],

            // ==========================================
            // BRITISH COLUMBIA (BC)
            // ==========================================
            ['name' => 'Vancouver', 'province_code' => 'BC', 'latitude' => 49.282729, 'longitude' => -123.120738, 'population' => 662248, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Surrey', 'province_code' => 'BC', 'latitude' => 49.191347, 'longitude' => -122.849012, 'population' => 568322, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Burnaby', 'province_code' => 'BC', 'latitude' => 49.248809, 'longitude' => -122.980510, 'population' => 249125, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Richmond', 'province_code' => 'BC', 'latitude' => 49.166590, 'longitude' => -123.133568, 'population' => 209937, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Victoria', 'province_code' => 'BC', 'latitude' => 48.428421, 'longitude' => -123.365644, 'population' => 91861, 'is_featured' => true, 'sort_order' => 5],
            ['name' => 'Kelowna', 'province_code' => 'BC', 'latitude' => 49.887952, 'longitude' => -119.496011, 'population' => 144576, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Abbotsford', 'province_code' => 'BC', 'latitude' => 49.050438, 'longitude' => -122.304470, 'population' => 153524, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Coquitlam', 'province_code' => 'BC', 'latitude' => 49.283764, 'longitude' => -122.793206, 'population' => 148625, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Langley', 'province_code' => 'BC', 'latitude' => 49.104430, 'longitude' => -122.582672, 'population' => 132603, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Saanich', 'province_code' => 'BC', 'latitude' => 48.483300, 'longitude' => -123.383300, 'population' => 117735, 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Delta', 'province_code' => 'BC', 'latitude' => 49.083300, 'longitude' => -123.066700, 'population' => 108455, 'is_featured' => false, 'sort_order' => 11],
            ['name' => 'Kamloops', 'province_code' => 'BC', 'latitude' => 50.674522, 'longitude' => -120.327293, 'population' => 97902, 'is_featured' => false, 'sort_order' => 12],
            ['name' => 'Nanaimo', 'province_code' => 'BC', 'latitude' => 49.165884, 'longitude' => -123.940065, 'population' => 99863, 'is_featured' => false, 'sort_order' => 13],
            ['name' => 'Chilliwack', 'province_code' => 'BC', 'latitude' => 49.166700, 'longitude' => -121.950000, 'population' => 93203, 'is_featured' => false, 'sort_order' => 14],
            ['name' => 'Maple Ridge', 'province_code' => 'BC', 'latitude' => 49.216700, 'longitude' => -122.600000, 'population' => 90990, 'is_featured' => false, 'sort_order' => 15],
            ['name' => 'Prince George', 'province_code' => 'BC', 'latitude' => 53.916700, 'longitude' => -122.750000, 'population' => 76708, 'is_featured' => false, 'sort_order' => 16],
            ['name' => 'New Westminster', 'province_code' => 'BC', 'latitude' => 49.206900, 'longitude' => -122.911100, 'population' => 78916, 'is_featured' => false, 'sort_order' => 17],
            ['name' => 'North Vancouver', 'province_code' => 'BC', 'latitude' => 49.316700, 'longitude' => -123.066700, 'population' => 58120, 'is_featured' => false, 'sort_order' => 18],
            ['name' => 'Vernon', 'province_code' => 'BC', 'latitude' => 50.266700, 'longitude' => -119.266700, 'population' => 44519, 'is_featured' => false, 'sort_order' => 19],
            ['name' => 'Penticton', 'province_code' => 'BC', 'latitude' => 49.491100, 'longitude' => -119.588600, 'population' => 36885, 'is_featured' => false, 'sort_order' => 20],
            ['name' => 'Campbell River', 'province_code' => 'BC', 'latitude' => 50.016700, 'longitude' => -125.250000, 'population' => 35519, 'is_featured' => false, 'sort_order' => 21],
            ['name' => 'Courtenay', 'province_code' => 'BC', 'latitude' => 49.683300, 'longitude' => -124.983300, 'population' => 28420, 'is_featured' => false, 'sort_order' => 22],
            ['name' => 'Fort St. John', 'province_code' => 'BC', 'latitude' => 56.250000, 'longitude' => -120.850000, 'population' => 21465, 'is_featured' => false, 'sort_order' => 23],
            ['name' => 'Cranbrook', 'province_code' => 'BC', 'latitude' => 49.516700, 'longitude' => -115.766700, 'population' => 20499, 'is_featured' => false, 'sort_order' => 24],
            ['name' => 'Port Alberni', 'province_code' => 'BC', 'latitude' => 49.233300, 'longitude' => -124.800000, 'population' => 18259, 'is_featured' => false, 'sort_order' => 25],
            ['name' => 'Squamish', 'province_code' => 'BC', 'latitude' => 49.700000, 'longitude' => -123.150000, 'population' => 23819, 'is_featured' => false, 'sort_order' => 26],
            ['name' => 'Salmon Arm', 'province_code' => 'BC', 'latitude' => 50.700000, 'longitude' => -119.283300, 'population' => 19432, 'is_featured' => false, 'sort_order' => 27],
            ['name' => 'Terrace', 'province_code' => 'BC', 'latitude' => 54.516700, 'longitude' => -128.600000, 'population' => 12017, 'is_featured' => false, 'sort_order' => 28],
            ['name' => 'Prince Rupert', 'province_code' => 'BC', 'latitude' => 54.316700, 'longitude' => -130.316700, 'population' => 12300, 'is_featured' => false, 'sort_order' => 29],
            ['name' => 'Dawson Creek', 'province_code' => 'BC', 'latitude' => 55.766700, 'longitude' => -120.233300, 'population' => 12978, 'is_featured' => false, 'sort_order' => 30],
            ['name' => 'Parksville', 'province_code' => 'BC', 'latitude' => 49.316700, 'longitude' => -124.316700, 'population' => 13642, 'is_featured' => false, 'sort_order' => 31],
            ['name' => 'Whistler', 'province_code' => 'BC', 'latitude' => 50.116700, 'longitude' => -122.950000, 'population' => 13982, 'is_featured' => false, 'sort_order' => 32],
            ['name' => 'Nelson', 'province_code' => 'BC', 'latitude' => 49.500000, 'longitude' => -117.283300, 'population' => 11106, 'is_featured' => false, 'sort_order' => 33],
            ['name' => 'Quesnel', 'province_code' => 'BC', 'latitude' => 52.983300, 'longitude' => -122.500000, 'population' => 9889, 'is_featured' => false, 'sort_order' => 34],
            ['name' => 'Williams Lake', 'province_code' => 'BC', 'latitude' => 52.133300, 'longitude' => -122.133300, 'population' => 10947, 'is_featured' => false, 'sort_order' => 35],
            ['name' => 'Revelstoke', 'province_code' => 'BC', 'latitude' => 51.000000, 'longitude' => -118.200000, 'population' => 8275, 'is_featured' => false, 'sort_order' => 36],
            ['name' => 'Tofino', 'province_code' => 'BC', 'latitude' => 49.150000, 'longitude' => -125.900000, 'population' => 2516, 'is_featured' => false, 'sort_order' => 37],
            ['name' => 'Golden', 'province_code' => 'BC', 'latitude' => 51.300000, 'longitude' => -116.966700, 'population' => 3986, 'is_featured' => false, 'sort_order' => 38],

            // ==========================================
            // ALBERTA (AB)
            // ==========================================
            ['name' => 'Calgary', 'province_code' => 'AB', 'latitude' => 51.044733, 'longitude' => -114.071883, 'population' => 1306784, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Edmonton', 'province_code' => 'AB', 'latitude' => 53.546124, 'longitude' => -113.493823, 'population' => 1010899, 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'Red Deer', 'province_code' => 'AB', 'latitude' => 52.268111, 'longitude' => -113.811241, 'population' => 100844, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Lethbridge', 'province_code' => 'AB', 'latitude' => 49.695564, 'longitude' => -112.845070, 'population' => 98406, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'St. Albert', 'province_code' => 'AB', 'latitude' => 53.630475, 'longitude' => -113.625642, 'population' => 68232, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Medicine Hat', 'province_code' => 'AB', 'latitude' => 50.041670, 'longitude' => -110.677551, 'population' => 63271, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Grande Prairie', 'province_code' => 'AB', 'latitude' => 55.169956, 'longitude' => -118.798622, 'population' => 63166, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Airdrie', 'province_code' => 'AB', 'latitude' => 51.291700, 'longitude' => -114.014400, 'population' => 74100, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Spruce Grove', 'province_code' => 'AB', 'latitude' => 53.545000, 'longitude' => -113.901100, 'population' => 37645, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Leduc', 'province_code' => 'AB', 'latitude' => 53.259400, 'longitude' => -113.549200, 'population' => 34094, 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Fort Saskatchewan', 'province_code' => 'AB', 'latitude' => 53.712800, 'longitude' => -113.213300, 'population' => 27088, 'is_featured' => false, 'sort_order' => 11],
            ['name' => 'Lloydminster', 'province_code' => 'AB', 'latitude' => 53.280700, 'longitude' => -110.035000, 'population' => 31582, 'is_featured' => false, 'sort_order' => 12],
            ['name' => 'Camrose', 'province_code' => 'AB', 'latitude' => 53.016700, 'longitude' => -112.833300, 'population' => 18772, 'is_featured' => false, 'sort_order' => 13],
            ['name' => 'Chestermere', 'province_code' => 'AB', 'latitude' => 51.050000, 'longitude' => -113.816700, 'population' => 22100, 'is_featured' => false, 'sort_order' => 14],
            ['name' => 'Cochrane', 'province_code' => 'AB', 'latitude' => 51.183300, 'longitude' => -114.466700, 'population' => 32199, 'is_featured' => false, 'sort_order' => 15],
            ['name' => 'Okotoks', 'province_code' => 'AB', 'latitude' => 50.725000, 'longitude' => -113.975000, 'population' => 30405, 'is_featured' => false, 'sort_order' => 16],
            ['name' => 'Brooks', 'province_code' => 'AB', 'latitude' => 50.566700, 'longitude' => -111.900000, 'population' => 14924, 'is_featured' => false, 'sort_order' => 17],
            ['name' => 'Canmore', 'province_code' => 'AB', 'latitude' => 51.083300, 'longitude' => -115.350000, 'population' => 15990, 'is_featured' => false, 'sort_order' => 18],
            ['name' => 'Banff', 'province_code' => 'AB', 'latitude' => 51.178400, 'longitude' => -115.570800, 'population' => 8305, 'is_featured' => false, 'sort_order' => 19],
            ['name' => 'Fort McMurray', 'province_code' => 'AB', 'latitude' => 56.726700, 'longitude' => -111.379000, 'population' => 68002, 'is_featured' => false, 'sort_order' => 20],
            ['name' => 'Cold Lake', 'province_code' => 'AB', 'latitude' => 54.464200, 'longitude' => -110.182500, 'population' => 15661, 'is_featured' => false, 'sort_order' => 21],
            ['name' => 'Wetaskiwin', 'province_code' => 'AB', 'latitude' => 52.966700, 'longitude' => -113.366700, 'population' => 12594, 'is_featured' => false, 'sort_order' => 22],
            ['name' => 'Sylvan Lake', 'province_code' => 'AB', 'latitude' => 52.308300, 'longitude' => -114.095800, 'population' => 15995, 'is_featured' => false, 'sort_order' => 23],
            ['name' => 'Strathmore', 'province_code' => 'AB', 'latitude' => 51.033300, 'longitude' => -113.400000, 'population' => 14339, 'is_featured' => false, 'sort_order' => 24],
            ['name' => 'Jasper', 'province_code' => 'AB', 'latitude' => 52.873700, 'longitude' => -118.081400, 'population' => 4590, 'is_featured' => false, 'sort_order' => 25],

            // ==========================================
            // MANITOBA (MB)
            // ==========================================
            ['name' => 'Winnipeg', 'province_code' => 'MB', 'latitude' => 49.895136, 'longitude' => -97.138374, 'population' => 749607, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Brandon', 'province_code' => 'MB', 'latitude' => 49.848470, 'longitude' => -99.950070, 'population' => 51311, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Steinbach', 'province_code' => 'MB', 'latitude' => 49.525833, 'longitude' => -96.683889, 'population' => 17806, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Thompson', 'province_code' => 'MB', 'latitude' => 55.743300, 'longitude' => -97.855300, 'population' => 13035, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Portage la Prairie', 'province_code' => 'MB', 'latitude' => 49.972800, 'longitude' => -98.291900, 'population' => 13270, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Winkler', 'province_code' => 'MB', 'latitude' => 49.181700, 'longitude' => -97.939700, 'population' => 13745, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Selkirk', 'province_code' => 'MB', 'latitude' => 50.143600, 'longitude' => -96.883900, 'population' => 10504, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Morden', 'province_code' => 'MB', 'latitude' => 49.191900, 'longitude' => -98.101400, 'population' => 9929, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Dauphin', 'province_code' => 'MB', 'latitude' => 51.150000, 'longitude' => -100.050000, 'population' => 8368, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'The Pas', 'province_code' => 'MB', 'latitude' => 53.825000, 'longitude' => -101.254200, 'population' => 5369, 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Flin Flon', 'province_code' => 'MB', 'latitude' => 54.766700, 'longitude' => -101.866700, 'population' => 4982, 'is_featured' => false, 'sort_order' => 11],
            ['name' => 'Churchill', 'province_code' => 'MB', 'latitude' => 58.768400, 'longitude' => -94.165000, 'population' => 870, 'is_featured' => false, 'sort_order' => 12],

            // ==========================================
            // SASKATCHEWAN (SK)
            // ==========================================
            ['name' => 'Saskatoon', 'province_code' => 'SK', 'latitude' => 52.133214, 'longitude' => -106.670046, 'population' => 266141, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Regina', 'province_code' => 'SK', 'latitude' => 50.454722, 'longitude' => -104.606667, 'population' => 226404, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Prince Albert', 'province_code' => 'SK', 'latitude' => 53.203333, 'longitude' => -105.753056, 'population' => 35926, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Moose Jaw', 'province_code' => 'SK', 'latitude' => 50.393333, 'longitude' => -105.551944, 'population' => 33665, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Swift Current', 'province_code' => 'SK', 'latitude' => 50.285800, 'longitude' => -107.797500, 'population' => 16750, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Yorkton', 'province_code' => 'SK', 'latitude' => 51.213900, 'longitude' => -102.462800, 'population' => 16280, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'North Battleford', 'province_code' => 'SK', 'latitude' => 52.757500, 'longitude' => -108.286100, 'population' => 13836, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Warman', 'province_code' => 'SK', 'latitude' => 52.321700, 'longitude' => -106.584200, 'population' => 12419, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Weyburn', 'province_code' => 'SK', 'latitude' => 49.664400, 'longitude' => -103.853600, 'population' => 11019, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Estevan', 'province_code' => 'SK', 'latitude' => 49.139200, 'longitude' => -102.986100, 'population' => 10851, 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Martensville', 'province_code' => 'SK', 'latitude' => 52.289700, 'longitude' => -106.667800, 'population' => 10549, 'is_featured' => false, 'sort_order' => 11],
            ['name' => 'Melfort', 'province_code' => 'SK', 'latitude' => 52.863900, 'longitude' => -104.610000, 'population' => 5955, 'is_featured' => false, 'sort_order' => 12],
            ['name' => 'Humboldt', 'province_code' => 'SK', 'latitude' => 52.201900, 'longitude' => -105.123100, 'population' => 6033, 'is_featured' => false, 'sort_order' => 13],
            ['name' => 'La Ronge', 'province_code' => 'SK', 'latitude' => 55.105600, 'longitude' => -105.281100, 'population' => 2521, 'is_featured' => false, 'sort_order' => 14],

            // ==========================================
            // NOVA SCOTIA (NS)
            // ==========================================
            ['name' => 'Halifax', 'province_code' => 'NS', 'latitude' => 44.648764, 'longitude' => -63.575239, 'population' => 439819, 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'Dartmouth', 'province_code' => 'NS', 'latitude' => 44.665220, 'longitude' => -63.567690, 'population' => 92233, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Sydney', 'province_code' => 'NS', 'latitude' => 46.136780, 'longitude' => -60.183100, 'population' => 29904, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Truro', 'province_code' => 'NS', 'latitude' => 45.364700, 'longitude' => -63.280000, 'population' => 12953, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'New Glasgow', 'province_code' => 'NS', 'latitude' => 45.587800, 'longitude' => -62.645600, 'population' => 9471, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Glace Bay', 'province_code' => 'NS', 'latitude' => 46.196900, 'longitude' => -59.957200, 'population' => 17556, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Kentville', 'province_code' => 'NS', 'latitude' => 45.077500, 'longitude' => -64.495800, 'population' => 6630, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Amherst', 'province_code' => 'NS', 'latitude' => 45.833300, 'longitude' => -64.216700, 'population' => 9413, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Bridgewater', 'province_code' => 'NS', 'latitude' => 44.377500, 'longitude' => -64.518600, 'population' => 8790, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Yarmouth', 'province_code' => 'NS', 'latitude' => 43.837500, 'longitude' => -66.117500, 'population' => 6829, 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Antigonish', 'province_code' => 'NS', 'latitude' => 45.623600, 'longitude' => -61.993100, 'population' => 4656, 'is_featured' => false, 'sort_order' => 11],
            ['name' => 'Wolfville', 'province_code' => 'NS', 'latitude' => 45.091700, 'longitude' => -64.363900, 'population' => 5057, 'is_featured' => false, 'sort_order' => 12],
            ['name' => 'Lunenburg', 'province_code' => 'NS', 'latitude' => 44.376900, 'longitude' => -64.318300, 'population' => 2396, 'is_featured' => false, 'sort_order' => 13],

            // ==========================================
            // NEW BRUNSWICK (NB)
            // ==========================================
            ['name' => 'Moncton', 'province_code' => 'NB', 'latitude' => 46.087817, 'longitude' => -64.778231, 'population' => 79470, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Saint John', 'province_code' => 'NB', 'latitude' => 45.273315, 'longitude' => -66.063308, 'population' => 69885, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Fredericton', 'province_code' => 'NB', 'latitude' => 45.963589, 'longitude' => -66.643115, 'population' => 63116, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Dieppe', 'province_code' => 'NB', 'latitude' => 46.098900, 'longitude' => -64.749200, 'population' => 28114, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Miramichi', 'province_code' => 'NB', 'latitude' => 47.027800, 'longitude' => -65.505600, 'population' => 17692, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Edmundston', 'province_code' => 'NB', 'latitude' => 47.376700, 'longitude' => -68.325300, 'population' => 16034, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Bathurst', 'province_code' => 'NB', 'latitude' => 47.618600, 'longitude' => -65.651400, 'population' => 12157, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Campbellton', 'province_code' => 'NB', 'latitude' => 48.007500, 'longitude' => -66.672800, 'population' => 7047, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Riverview', 'province_code' => 'NB', 'latitude' => 46.061400, 'longitude' => -64.796700, 'population' => 20584, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Quispamsis', 'province_code' => 'NB', 'latitude' => 45.433300, 'longitude' => -65.950000, 'population' => 18768, 'is_featured' => false, 'sort_order' => 10],
            ['name' => 'Shediac', 'province_code' => 'NB', 'latitude' => 46.216700, 'longitude' => -64.533300, 'population' => 7535, 'is_featured' => false, 'sort_order' => 11],

            // ==========================================
            // NEWFOUNDLAND AND LABRADOR (NL)
            // ==========================================
            ['name' => "St. John's", 'province_code' => 'NL', 'latitude' => 47.561510, 'longitude' => -52.712577, 'population' => 110525, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Mount Pearl', 'province_code' => 'NL', 'latitude' => 47.518889, 'longitude' => -52.784444, 'population' => 22477, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Corner Brook', 'province_code' => 'NL', 'latitude' => 48.950000, 'longitude' => -57.950000, 'population' => 19806, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Conception Bay South', 'province_code' => 'NL', 'latitude' => 47.516700, 'longitude' => -52.983300, 'population' => 27168, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Paradise', 'province_code' => 'NL', 'latitude' => 47.533300, 'longitude' => -52.866700, 'population' => 22957, 'is_featured' => false, 'sort_order' => 5],
            ['name' => 'Grand Falls-Windsor', 'province_code' => 'NL', 'latitude' => 48.933300, 'longitude' => -55.666700, 'population' => 13853, 'is_featured' => false, 'sort_order' => 6],
            ['name' => 'Gander', 'province_code' => 'NL', 'latitude' => 48.956900, 'longitude' => -54.608900, 'population' => 11888, 'is_featured' => false, 'sort_order' => 7],
            ['name' => 'Labrador City', 'province_code' => 'NL', 'latitude' => 52.950000, 'longitude' => -66.916700, 'population' => 7412, 'is_featured' => false, 'sort_order' => 8],
            ['name' => 'Happy Valley-Goose Bay', 'province_code' => 'NL', 'latitude' => 53.300000, 'longitude' => -60.300000, 'population' => 8109, 'is_featured' => false, 'sort_order' => 9],
            ['name' => 'Stephenville', 'province_code' => 'NL', 'latitude' => 48.550000, 'longitude' => -58.583300, 'population' => 6540, 'is_featured' => false, 'sort_order' => 10],

            // ==========================================
            // PRINCE EDWARD ISLAND (PE)
            // ==========================================
            ['name' => 'Charlottetown', 'province_code' => 'PE', 'latitude' => 46.238240, 'longitude' => -63.131070, 'population' => 38809, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Summerside', 'province_code' => 'PE', 'latitude' => 46.395833, 'longitude' => -63.788889, 'population' => 14829, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Stratford', 'province_code' => 'PE', 'latitude' => 46.216700, 'longitude' => -63.083300, 'population' => 10927, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Cornwall', 'province_code' => 'PE', 'latitude' => 46.233300, 'longitude' => -63.216700, 'population' => 6574, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Montague', 'province_code' => 'PE', 'latitude' => 46.166700, 'longitude' => -62.650000, 'population' => 2174, 'is_featured' => false, 'sort_order' => 5],

            // ==========================================
            // NORTHWEST TERRITORIES (NT)
            // ==========================================
            ['name' => 'Yellowknife', 'province_code' => 'NT', 'latitude' => 62.454000, 'longitude' => -114.371800, 'population' => 20340, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Hay River', 'province_code' => 'NT', 'latitude' => 60.816700, 'longitude' => -115.800000, 'population' => 3528, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Inuvik', 'province_code' => 'NT', 'latitude' => 68.350000, 'longitude' => -133.716700, 'population' => 3243, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Fort Smith', 'province_code' => 'NT', 'latitude' => 60.005000, 'longitude' => -111.890000, 'population' => 2548, 'is_featured' => false, 'sort_order' => 4],

            // ==========================================
            // YUKON (YT)
            // ==========================================
            ['name' => 'Whitehorse', 'province_code' => 'YT', 'latitude' => 60.721200, 'longitude' => -135.056800, 'population' => 28201, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Dawson City', 'province_code' => 'YT', 'latitude' => 64.060000, 'longitude' => -139.432500, 'population' => 1577, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Watson Lake', 'province_code' => 'YT', 'latitude' => 60.063300, 'longitude' => -128.708300, 'population' => 1133, 'is_featured' => false, 'sort_order' => 3],

            // ==========================================
            // NUNAVUT (NU)
            // ==========================================
            ['name' => 'Iqaluit', 'province_code' => 'NU', 'latitude' => 63.746700, 'longitude' => -68.517000, 'population' => 7429, 'is_featured' => false, 'sort_order' => 1],
            ['name' => 'Rankin Inlet', 'province_code' => 'NU', 'latitude' => 62.808900, 'longitude' => -92.083900, 'population' => 2975, 'is_featured' => false, 'sort_order' => 2],
            ['name' => 'Arviat', 'province_code' => 'NU', 'latitude' => 61.108300, 'longitude' => -94.063900, 'population' => 2864, 'is_featured' => false, 'sort_order' => 3],
            ['name' => 'Baker Lake', 'province_code' => 'NU', 'latitude' => 64.316700, 'longitude' => -96.016700, 'population' => 2069, 'is_featured' => false, 'sort_order' => 4],
            ['name' => 'Cambridge Bay', 'province_code' => 'NU', 'latitude' => 69.116700, 'longitude' => -105.050000, 'population' => 1760, 'is_featured' => false, 'sort_order' => 5],

            // ==========================================
            // Outside Canada (OTHER) Fallback
            // ==========================================
            ['name' => 'Outside Canada', 'province_code' => 'OTHER', 'latitude' => 0.0, 'longitude' => 0.0, 'population' => null, 'is_featured' => false, 'sort_order' => 999],
        ];

        foreach ($cities as $cityData) {
            $provinceId = $provinceMap[$cityData['province_code']] ?? null;
            if (!$provinceId) {
                continue;
            }

            $baseSlug = Str::slug($cityData['name']);
            // If another city has the same slug in a different province, make it unique
            $slug = $baseSlug;
            $existing = City::where('slug', $slug)->first();
            if ($existing && $existing->province_id !== $provinceId) {
                $slug = Str::slug($cityData['name'] . '-' . strtolower($cityData['province_code']));
            }

            City::updateOrCreate(
                ['province_id' => $provinceId, 'name' => $cityData['name']],
                [
                    'slug'        => $slug,
                    'latitude'    => $cityData['latitude'],
                    'longitude'   => $cityData['longitude'],
                    'population'  => $cityData['population'],
                    'is_featured' => $cityData['is_featured'],
                    'is_active'   => true,
                    'sort_order'  => $cityData['sort_order'],
                ]
            );
        }

        Cache::forget(City::CACHE_KEY);
    }
}
