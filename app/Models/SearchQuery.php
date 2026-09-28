<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SearchQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'hits_count',
        'results_count',
        'last_searched_at',
    ];

    protected $casts = [
        'hits_count'       => 'integer',
        'results_count'    => 'integer',
        'last_searched_at' => 'datetime',
    ];

    /**
     * Record or increment a search query hit asynchronously/safely.
     */
    public static function recordSearch(string $query, int $resultsCount = 0): void
    {
        $normalized = trim(mb_substr($query, 0, 190));
        if (mb_strlen($normalized) < 2) {
            return;
        }

        try {
            $record = static::firstOrNew(['query' => $normalized]);
            $record->hits_count = ($record->exists ? $record->hits_count : 0) + 1;
            $record->results_count = $resultsCount;
            $record->last_searched_at = now();
            $record->save();

            // Clear popular keywords cache
            Cache::forget('bontrouver_trending_keywords');
        } catch (\Throwable) {
            // Non-blocking on database locks/issues
        }
    }

    /**
     * Get top trending keywords from database search history with listing titles as dynamic fallback.
     *
     * @return array<string>
     */
    public static function getTrendingKeywords(int $limit = 8): array
    {
        return Cache::remember('bontrouver_trending_keywords', 3600, function () use ($limit) {
            $searched = static::where('results_count', '>', 0)
                ->orderByDesc('hits_count')
                ->orderByDesc('last_searched_at')
                ->limit($limit)
                ->pluck('query')
                ->toArray();

            if (count($searched) >= $limit) {
                return $searched;
            }

            // Supplement with top active listings titles dynamically
            $needed = $limit - count($searched);
            $listingTitles = Listing::where('status', 'active')
                ->orderByDesc('is_featured')
                ->orderByDesc('views_count')
                ->limit($needed * 2)
                ->pluck('title')
                ->map(fn ($title) => trim($title))
                ->filter(fn ($title) => !in_array($title, $searched, true))
                ->take($needed)
                ->values()
                ->toArray();

            return array_values(array_unique(array_merge($searched, $listingTitles)));
        });
    }
}
