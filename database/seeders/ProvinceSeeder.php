<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds with Canadian official bilingual names.
     */
    public function run(): void
    {
        $provinces = [
            [
                'name_en'      => 'Ontario',
                'name_fr'      => 'Ontario',
                'code'         => 'ON',
                'slug'         => 'ontario',
                'country_code' => 'CA',
                'sort_order'   => 1,
            ],
            [
                'name_en'      => 'Quebec',
                'name_fr'      => 'Québec',
                'code'         => 'QC',
                'slug'         => 'quebec',
                'country_code' => 'CA',
                'sort_order'   => 2,
            ],
            [
                'name_en'      => 'British Columbia',
                'name_fr'      => 'Colombie-Britannique',
                'code'         => 'BC',
                'slug'         => 'british-columbia',
                'country_code' => 'CA',
                'sort_order'   => 3,
            ],
            [
                'name_en'      => 'Alberta',
                'name_fr'      => 'Alberta',
                'code'         => 'AB',
                'slug'         => 'alberta',
                'country_code' => 'CA',
                'sort_order'   => 4,
            ],
            [
                'name_en'      => 'Manitoba',
                'name_fr'      => 'Manitoba',
                'code'         => 'MB',
                'slug'         => 'manitoba',
                'country_code' => 'CA',
                'sort_order'   => 5,
            ],
            [
                'name_en'      => 'Saskatchewan',
                'name_fr'      => 'Saskatchewan',
                'code'         => 'SK',
                'slug'         => 'saskatchewan',
                'country_code' => 'CA',
                'sort_order'   => 6,
            ],
            [
                'name_en'      => 'Nova Scotia',
                'name_fr'      => 'Nouvelle-Écosse',
                'code'         => 'NS',
                'slug'         => 'nova-scotia',
                'country_code' => 'CA',
                'sort_order'   => 7,
            ],
            [
                'name_en'      => 'New Brunswick',
                'name_fr'      => 'Nouveau-Brunswick',
                'code'         => 'NB',
                'slug'         => 'new-brunswick',
                'country_code' => 'CA',
                'sort_order'   => 8,
            ],
            [
                'name_en'      => 'Newfoundland and Labrador',
                'name_fr'      => 'Terre-Neuve-et-Labrador',
                'code'         => 'NL',
                'slug'         => 'newfoundland-and-labrador',
                'country_code' => 'CA',
                'sort_order'   => 9,
            ],
            [
                'name_en'      => 'Prince Edward Island',
                'name_fr'      => 'Île-du-Prince-Édouard',
                'code'         => 'PE',
                'slug'         => 'prince-edward-island',
                'country_code' => 'CA',
                'sort_order'   => 10,
            ],
            [
                'name_en'      => 'Northwest Territories',
                'name_fr'      => 'Territoires du Nord-Ouest',
                'code'         => 'NT',
                'slug'         => 'northwest-territories',
                'country_code' => 'CA',
                'sort_order'   => 11,
            ],
            [
                'name_en'      => 'Yukon',
                'name_fr'      => 'Yukon',
                'code'         => 'YT',
                'slug'         => 'yukon',
                'country_code' => 'CA',
                'sort_order'   => 12,
            ],
            [
                'name_en'      => 'Nunavut',
                'name_fr'      => 'Nunavut',
                'code'         => 'NU',
                'slug'         => 'nunavut',
                'country_code' => 'CA',
                'sort_order'   => 13,
            ],
            [
                'name_en'      => 'Outside Canada',
                'name_fr'      => 'Hors du Canada',
                'code'         => 'OTHER',
                'slug'         => 'outside-canada',
                'country_code' => 'OTHER',
                'sort_order'   => 99,
            ],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(
                ['code' => $province['code']],
                $province
            );
        }
    }
}
