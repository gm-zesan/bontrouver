<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            [
                'name' => 'Ontario',
                'code' => 'ON',
                'slug' => 'ontario',
                'country_code' => 'CA',
                'sort_order' => 1,
            ],
            [
                'name' => 'Quebec',
                'code' => 'QC',
                'slug' => 'quebec',
                'country_code' => 'CA',
                'sort_order' => 2,
            ],
            [
                'name' => 'British Columbia',
                'code' => 'BC',
                'slug' => 'british-columbia',
                'country_code' => 'CA',
                'sort_order' => 3,
            ],
            [
                'name' => 'Alberta',
                'code' => 'AB',
                'slug' => 'alberta',
                'country_code' => 'CA',
                'sort_order' => 4,
            ],
            [
                'name' => 'Manitoba',
                'code' => 'MB',
                'slug' => 'manitoba',
                'country_code' => 'CA',
                'sort_order' => 5,
            ],
            [
                'name' => 'Saskatchewan',
                'code' => 'SK',
                'slug' => 'saskatchewan',
                'country_code' => 'CA',
                'sort_order' => 6,
            ],
            [
                'name' => 'Nova Scotia',
                'code' => 'NS',
                'slug' => 'nova-scotia',
                'country_code' => 'CA',
                'sort_order' => 7,
            ],
            [
                'name' => 'New Brunswick',
                'code' => 'NB',
                'slug' => 'new-brunswick',
                'country_code' => 'CA',
                'sort_order' => 8,
            ],
            [
                'name' => 'Newfoundland and Labrador',
                'code' => 'NL',
                'slug' => 'newfoundland-and-labrador',
                'country_code' => 'CA',
                'sort_order' => 9,
            ],
            [
                'name' => 'Prince Edward Island',
                'code' => 'PE',
                'slug' => 'prince-edward-island',
                'country_code' => 'CA',
                'sort_order' => 10,
            ],
            [
                'name' => 'Northwest Territories',
                'code' => 'NT',
                'slug' => 'northwest-territories',
                'country_code' => 'CA',
                'sort_order' => 11,
            ],
            [
                'name' => 'Yukon',
                'code' => 'YT',
                'slug' => 'yukon',
                'country_code' => 'CA',
                'sort_order' => 12,
            ],
            [
                'name' => 'Nunavut',
                'code' => 'NU',
                'slug' => 'nunavut',
                'country_code' => 'CA',
                'sort_order' => 13,
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
