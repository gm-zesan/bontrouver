<?php

namespace Database\Seeders;

use App\Models\SearchQuery;
use Illuminate\Database\Seeder;

class SearchQuerySeeder extends Seeder
{
    /**
     * Seed initial search queries.
     */
    public function run(): void
    {
        $initialQueries = [
            ['query' => 'Toyota RAV4 Hybrid', 'hits_count' => 145, 'results_count' => 12],
            ['query' => 'iPhone 16 Pro Max', 'hits_count' => 132, 'results_count' => 8],
            ['query' => 'PlayStation 5 Console', 'hits_count' => 110, 'results_count' => 6],
            ['query' => '1 Bedroom Apartment Toronto', 'hits_count' => 98, 'results_count' => 15],
            ['query' => 'Herman Miller Embody Chair', 'hits_count' => 87, 'results_count' => 4],
            ['query' => 'Honda Civic Touring', 'hits_count' => 76, 'results_count' => 9],
            ['query' => 'Winter Tires Set', 'hits_count' => 65, 'results_count' => 11],
            ['query' => 'MacBook Pro M3', 'hits_count' => 54, 'results_count' => 7],
        ];

        foreach ($initialQueries as $item) {
            SearchQuery::updateOrCreate(
                ['query' => $item['query']],
                [
                    'hits_count'       => $item['hits_count'],
                    'results_count'    => $item['results_count'],
                    'last_searched_at' => now()->subHours(rand(1, 48)),
                ]
            );
        }
    }
}
