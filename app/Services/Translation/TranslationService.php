<?php

namespace App\Services\Translation;

use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Services\Translation\Contracts\TranslationDriverInterface;
use App\Services\Translation\Drivers\GoogleWebDriver;
use App\Services\Translation\Drivers\LibreTranslateDriver;
use App\Services\Translation\Drivers\MockTranslationDriver;
use App\Services\Translation\Drivers\MyMemoryDriver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class TranslationService
{
    protected ?TranslationDriverInterface $driver = null;

    public function __construct(?TranslationDriverInterface $driver = null)
    {
        $this->driver = $driver;
    }

    /**
     * Resolve the active translation driver.
     */
    public function getDriver(): TranslationDriverInterface
    {
        if ($this->driver !== null) {
            return $this->driver;
        }

        $driverName = config('translation.driver', 'google_web');

        $this->driver = match (strtolower($driverName)) {
            'mymemory'       => new MyMemoryDriver(),
            'libretranslate' => new LibreTranslateDriver(),
            'mock'           => new MockTranslationDriver(),
            default          => new GoogleWebDriver(),
        };

        return $this->driver;
    }

    /**
     * Explicitly set the driver (useful for tests and runtime swapping).
     */
    public function setDriver(TranslationDriverInterface $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Translate single text string with automatic fallback if primary driver fails.
     */
    public function translateText(string $text, string $targetLocale, string $sourceLocale = 'auto'): ?string
    {
        $driver = $this->getDriver();
        $translated = $driver->translate($text, $targetLocale, $sourceLocale);

        // If mock driver or translation succeeded, return directly
        if ($translated !== null || $driver instanceof MockTranslationDriver) {
            return $translated;
        }

        // Automatic secondary fallback for free web environments
        try {
            $fallback = ($driver instanceof GoogleWebDriver)
                ? new MyMemoryDriver()
                : new GoogleWebDriver();

            return $fallback->translate($text, $targetLocale, $sourceLocale);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Translate a listing's title, description, and key details for detail view
     * using content-aware SHA-256 caching and pluggable drivers.
     */
    public function translateListing(Listing $listing, string $targetLocale, string $scope = 'detail'): array
    {
        $targetLocale = strtolower(trim($targetLocale));
        $driver = $this->getDriver();
        $driverName = $driver->getName();

        $rawTitle = (string) $listing->title;
        $rawDesc = ($scope === 'detail' || $scope === 'hero') ? (string) ($listing->description ?? '') : '';

        // Generate SHA-256 content fingerprint
        $fingerprint = hash('sha256', json_encode([
            'id'    => $listing->id,
            'title' => $rawTitle,
            'desc'  => $rawDesc,
            'scope' => $scope,
        ]));

        $cacheKey = "listing_trans_{$listing->id}_{$fingerprint}_{$targetLocale}_{$driverName}";

        // 1. Cache Hit Check
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            $isValid = is_array($cached) && !empty($cached['title']);

            // Invalidate cache if detail/hero scope has raw description but cached entry is missing description
            if (($scope === 'detail' || $scope === 'hero') && trim($rawDesc) !== '' && empty($cached['description'])) {
                $isValid = false;
                try {
                    Cache::forget($cacheKey);
                } catch (Throwable) {}
            }

            if ($isValid) {
                return [
                    'success'       => true,
                    'target_locale' => $targetLocale,
                    'scope'         => $scope,
                    'translated'    => $cached,
                    'cached'        => true,
                ];
            }
        }

        // 2. Perform translation with safe lock fallback
        $lockKey = "lock_trans_{$listing->id}_{$targetLocale}";
        $lockAcquired = false;
        $lock = null;

        try {
            // Attempt atomic cache lock if supported by the cache store
            if (method_exists(Cache::getStore(), 'lock')) {
                $lock = Cache::lock($lockKey, 10);
                $lockAcquired = $lock->get();
            }
        } catch (Throwable) {
            // Graceful fallback for cache drivers without lock support (e.g. file driver on shared cPanel)
            $lockAcquired = false;
        }

        try {
            // Translate Title
            $translatedTitle = $this->translateText($rawTitle, $targetLocale);

            if ($translatedTitle === null) {
                return [
                    'success'       => false,
                    'message'       => __('Translation currently unavailable'),
                    'target_locale' => $targetLocale,
                    'scope'         => $scope,
                ];
            }

            $result = [
                'title' => $translatedTitle,
            ];

            // Translate Description if scope is 'detail' or 'hero'
            if ($scope === 'detail' || $scope === 'hero') {
                if (trim($rawDesc) !== '') {
                    $maxChars = ($scope === 'hero') ? 300 : (int) config('translation.max_chars', 5000);
                    $truncatedDesc = mb_substr($rawDesc, 0, $maxChars);
                    $translatedDesc = $this->translateText($truncatedDesc, $targetLocale);

                    if ($translatedDesc !== null) {
                        $result['description'] = $translatedDesc;
                    }
                }
            }

            // Cache ONLY complete successful translations for 30 days
            if (($scope !== 'detail' && $scope !== 'hero') || trim($rawDesc) === '' || !empty($result['description'])) {
                $cacheDays = (int) config('translation.cache_days', 30);
                Cache::put($cacheKey, $result, now()->addDays($cacheDays));
            }

            return [
                'success'       => true,
                'target_locale' => $targetLocale,
                'scope'         => $scope,
                'translated'    => $result,
                'cached'        => false,
            ];
        } finally {
            if ($lockAcquired && $lock) {
                try {
                    $lock->release();
                } catch (Throwable) {
                    // Ignore release errors on unsupported stores
                }
            }
        }
    }

    /**
     * Batch translate multiple listings efficiently.
     * Uses content cache first; uncached card/hero items are combined into a single batched HTTP request.
     */
    public function translateListingsBatch(iterable $listings, string $targetLocale, string $scope = 'card'): array
    {
        $targetLocale = strtolower(trim($targetLocale));
        $driver = $this->getDriver();
        $driverName = $driver->getName();
        $cacheDays = (int) config('translation.cache_days', 30);

        $results = [];
        $uncached = [];

        foreach ($listings as $listing) {
            $rawTitle = (string) $listing->title;
            $rawDesc = ($scope === 'detail' || $scope === 'hero') ? (string) ($listing->description ?? '') : '';

            $fingerprint = hash('sha256', json_encode([
                'id'    => $listing->id,
                'title' => $rawTitle,
                'desc'  => $rawDesc,
                'scope' => $scope,
            ]));
            $cacheKey = "listing_trans_{$listing->id}_{$fingerprint}_{$targetLocale}_{$driverName}";

            if (Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                if (is_array($cached) && !empty($cached['title'])) {
                    $results[$listing->id] = $cached;
                    continue;
                }
            }

            $uncached[$listing->id] = [
                'listing'     => $listing,
                'cache_key'   => $cacheKey,
                'raw_title'   => $rawTitle,
                'raw_desc'    => $rawDesc,
                'fingerprint' => $fingerprint,
            ];
        }

        if (empty($uncached)) {
            return $results;
        }

        // Fast path for card scope: batch translate multiple titles in ONE single request
        if ($scope === 'card') {
            $indexedTokens = [];
            $keysList = array_keys($uncached);
            foreach ($keysList as $idx => $id) {
                $cleanTitle = trim(preg_replace('/\s+/', ' ', $uncached[$id]['raw_title']));
                $indexedTokens[] = "[[[BT_{$idx}]]] " . $cleanTitle;
            }

            $batchPayload = implode("\n", $indexedTokens);
            $translatedBatch = $this->translateText($batchPayload, $targetLocale);

            if ($translatedBatch !== null) {
                preg_match_all('/\[\[\[\s*BT_(\d+)\s*\]\]\]\s*(.*?)(?=(?:\[\[\[\s*BT_\d+\s*\]\]\]|$))/si', $translatedBatch, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $idx = (int) $match[1];
                    $transTitle = trim($match[2]);
                    if (isset($keysList[$idx]) && $transTitle !== '') {
                        $id = $keysList[$idx];
                        $itemData = ['title' => $transTitle];
                        Cache::put($uncached[$id]['cache_key'], $itemData, now()->addDays($cacheDays));
                        $results[$id] = $itemData;
                        unset($uncached[$id]);
                    }
                }
            }
        } elseif ($scope === 'hero') {
            // Fast path for hero scope: batch translate both title AND description
            $indexedTokens = [];
            $keysList = array_keys($uncached);
            foreach ($keysList as $idx => $id) {
                $cleanTitle = trim(preg_replace('/\s+/', ' ', $uncached[$id]['raw_title']));
                $cleanDesc = trim(preg_replace('/\s+/', ' ', mb_substr($uncached[$id]['raw_desc'], 0, 300)));
                $indexedTokens[] = "[[[BT_{$idx}_T]]] " . $cleanTitle;
                if ($cleanDesc !== '') {
                    $indexedTokens[] = "[[[BT_{$idx}_D]]] " . $cleanDesc;
                }
            }

            $batchPayload = implode("\n", $indexedTokens);
            $translatedBatch = $this->translateText($batchPayload, $targetLocale);

            if ($translatedBatch !== null) {
                $titles = [];
                $descriptions = [];
                preg_match_all('/\[\[\[\s*BT_(\d+)_T\s*\]\]\]\s*(.*?)(?=(?:\[\[\[\s*BT_\d+_[TD]\s*\]\]\]|$))/si', $translatedBatch, $tMatches, PREG_SET_ORDER);
                foreach ($tMatches as $m) {
                    $titles[(int) $m[1]] = trim($m[2]);
                }
                preg_match_all('/\[\[\[\s*BT_(\d+)_D\s*\]\]\]\s*(.*?)(?=(?:\[\[\[\s*BT_\d+_[TD]\s*\]\]\]|$))/si', $translatedBatch, $dMatches, PREG_SET_ORDER);
                foreach ($dMatches as $m) {
                    $descriptions[(int) $m[1]] = trim($m[2]);
                }

                foreach ($keysList as $idx => $id) {
                    if (isset($titles[$idx]) && $titles[$idx] !== '') {
                        $itemData = ['title' => $titles[$idx]];
                        if (isset($descriptions[$idx]) && $descriptions[$idx] !== '') {
                            $itemData['description'] = $descriptions[$idx];
                        }
                        Cache::put($uncached[$id]['cache_key'], $itemData, now()->addDays($cacheDays));
                        $results[$id] = $itemData;
                        unset($uncached[$id]);
                    }
                }
            }
        }

        // For any remaining uncached items, resolve individually
        foreach ($uncached as $id => $item) {
            $singleRes = $this->translateListing($item['listing'], $targetLocale, $scope);
            if (!empty($singleRes['success']) && !empty($singleRes['translated'])) {
                $results[$id] = $singleRes['translated'];
            }
        }

        return $results;
    }

    /**
     * Translate a community meetup's title and description.
     */
    public function translateMeetup(CompanionshipRequest $meetup, string $targetLocale, string $scope = 'detail'): array
    {
        $targetLocale = strtolower(trim($targetLocale));
        $driver = $this->getDriver();
        $driverName = $driver->getName();

        $rawTitle = (string) $meetup->title;
        $rawDesc = ($scope === 'detail') ? (string) ($meetup->description ?? '') : '';

        $fingerprint = hash('sha256', json_encode([
            'id'    => $meetup->id,
            'title' => $rawTitle,
            'desc'  => $rawDesc,
            'scope' => $scope,
        ]));

        $cacheKey = "meetup_trans_{$meetup->id}_{$fingerprint}_{$targetLocale}_{$driverName}";

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            $isValid = is_array($cached) && !empty($cached['title']);
            if ($scope === 'detail' && trim($rawDesc) !== '' && empty($cached['description'])) {
                $isValid = false;
                try {
                    Cache::forget($cacheKey);
                } catch (Throwable) {}
            }

            if ($isValid) {
                return [
                    'success'       => true,
                    'target_locale' => $targetLocale,
                    'scope'         => $scope,
                    'translated'    => $cached,
                    'cached'        => true,
                ];
            }
        }

        $translatedTitle = $this->translateText($rawTitle, $targetLocale);
        if ($translatedTitle === null) {
            return [
                'success'       => false,
                'message'       => __('Translation currently unavailable'),
                'target_locale' => $targetLocale,
                'scope'         => $scope,
            ];
        }

        $result = ['title' => $translatedTitle];

        if ($scope === 'detail' && trim($rawDesc) !== '') {
            $maxChars = (int) config('translation.max_chars', 5000);
            $truncatedDesc = mb_substr($rawDesc, 0, $maxChars);
            $translatedDesc = $this->translateText($truncatedDesc, $targetLocale);
            if ($translatedDesc !== null) {
                $result['description'] = $translatedDesc;
            }
        }

        if ($scope !== 'detail' || trim($rawDesc) === '' || !empty($result['description'])) {
            $cacheDays = (int) config('translation.cache_days', 30);
            Cache::put($cacheKey, $result, now()->addDays($cacheDays));
        }

        return [
            'success'       => true,
            'target_locale' => $targetLocale,
            'scope'         => $scope,
            'translated'    => $result,
            'cached'        => false,
        ];
    }

    /**
     * Batch translate multiple community meetups.
     */
    public function translateMeetupsBatch(iterable $meetups, string $targetLocale, string $scope = 'card'): array
    {
        $targetLocale = strtolower(trim($targetLocale));
        $driver = $this->getDriver();
        $driverName = $driver->getName();
        $cacheDays = (int) config('translation.cache_days', 30);

        $results = [];
        $uncached = [];

        foreach ($meetups as $meetup) {
            $rawTitle = (string) $meetup->title;
            $rawDesc = ($scope === 'detail') ? (string) ($meetup->description ?? '') : '';

            $fingerprint = hash('sha256', json_encode([
                'id'    => $meetup->id,
                'title' => $rawTitle,
                'desc'  => $rawDesc,
                'scope' => $scope,
            ]));
            $cacheKey = "meetup_trans_{$meetup->id}_{$fingerprint}_{$targetLocale}_{$driverName}";

            if (Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                if (is_array($cached) && !empty($cached['title'])) {
                    $results[$meetup->id] = $cached;
                    continue;
                }
            }

            $uncached[$meetup->id] = [
                'meetup'      => $meetup,
                'cache_key'   => $cacheKey,
                'raw_title'   => $rawTitle,
                'raw_desc'    => $rawDesc,
                'fingerprint' => $fingerprint,
            ];
        }

        if (empty($uncached)) {
            return $results;
        }

        if ($scope === 'card') {
            $indexedTokens = [];
            $keysList = array_keys($uncached);
            foreach ($keysList as $idx => $id) {
                $cleanTitle = trim(preg_replace('/\s+/', ' ', $uncached[$id]['raw_title']));
                $indexedTokens[] = "[[[BT_{$idx}]]] " . $cleanTitle;
            }

            $batchPayload = implode("\n", $indexedTokens);
            $translatedBatch = $this->translateText($batchPayload, $targetLocale);

            if ($translatedBatch !== null) {
                preg_match_all('/\[\[\[\s*BT_(\d+)\s*\]\]\]\s*(.*?)(?=(?:\[\[\[\s*BT_\d+\s*\]\]\]|$))/si', $translatedBatch, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $idx = (int) $match[1];
                    $transTitle = trim($match[2]);
                    if (isset($keysList[$idx]) && $transTitle !== '') {
                        $id = $keysList[$idx];
                        $itemData = ['title' => $transTitle];
                        Cache::put($uncached[$id]['cache_key'], $itemData, now()->addDays($cacheDays));
                        $results[$id] = $itemData;
                        unset($uncached[$id]);
                    }
                }
            }
        }

        foreach ($uncached as $id => $item) {
            $singleRes = $this->translateMeetup($item['meetup'], $targetLocale, $scope);
            if (!empty($singleRes['success']) && !empty($singleRes['translated'])) {
                $results[$id] = $singleRes['translated'];
            }
        }

        return $results;
    }
}
