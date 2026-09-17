<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\SmartAlert;
use App\Notifications\SmartAlertMatched;
use Illuminate\Support\Facades\DB;

class SmartAlertService
{
    /**
     * Create a new smart alert for a user.
     */
    public function create(array $data, int $userId): SmartAlert
    {
        return DB::transaction(function () use ($data, $userId) {
            $alert = SmartAlert::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'keyword' => $data['keyword'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'city' => $data['city'] ?? null,
                'min_price' => $data['min_price'] ?? null,
                'max_price' => $data['max_price'] ?? null,
                'is_active' => true,
            ]);

            // Save dynamic attributes if any
            if (!empty($data['attributes']) && is_array($data['attributes'])) {
                foreach ($data['attributes'] as $attrId => $value) {
                    $alert->attributes()->create([
                        'category_attribute_id' => $attrId,
                        'value' => is_array($value) ? json_encode($value) : $value,
                    ]);
                }
            }

            return $alert;
        });
    }

    /**
     * Delete an alert.
     */
    public function delete(SmartAlert $alert): bool
    {
        return $alert->delete();
    }

    /**
     * Evaluate a newly created listing against all active smart alerts.
     */
    public function evaluateNewListing(Listing $listing): void
    {
        // We need to fetch all active alerts and check if the listing matches their criteria.
        // For performance, we can query the database to find alerts that MIGHT match,
        // then do a more thorough check in PHP if needed, or do it all in DB.

        $query = SmartAlert::where('is_active', true)
            ->where('user_id', '!=', $listing->user_id); // Don't notify the user about their own listing

        // If listing has a category, match alerts with no category or matching category
        if ($listing->category_id) {
            $query->where(function ($q) use ($listing) {
                $q->whereNull('category_id')
                  ->orWhere('category_id', $listing->category_id);
            });
        }

        // Price bounds
        if ($listing->price !== null) {
            $query->where(function ($q) use ($listing) {
                $q->whereNull('min_price')
                  ->orWhere('min_price', '<=', $listing->price);
            })->where(function ($q) use ($listing) {
                $q->whereNull('max_price')
                  ->orWhere('max_price', '>=', $listing->price);
            });
        }

        // City matching
        if ($listing->city_id) {
            $query->where(function ($q) use ($listing) {
                $q->whereNull('city_id')
                  ->orWhere('city_id', $listing->city_id);
            });
        } elseif ($listing->city) {
            $query->where(function ($q) use ($listing) {
                $q->whereNull('city')
                  ->orWhere('city', 'like', '%' . $listing->city . '%');
            });
        }

        // Fetch potential matches
        $potentialAlerts = $query->with('attributes')->get();

        foreach ($potentialAlerts as $alert) {
            // Check keyword in PHP (title or description)
            if ($alert->keyword) {
                $keywordMatches = stripos($listing->title, $alert->keyword) !== false ||
                                  stripos($listing->description ?? '', $alert->keyword) !== false;
                if (!$keywordMatches) {
                    continue; // Skip if keyword doesn't match
                }
            }

            // Check dynamic attributes (e.g. bedrooms)
            $attributesMatch = true;
            if ($alert->attributes->isNotEmpty() && $listing->relationLoaded('attributes')) {
                // Not fully implemented attribute matching for MVP unless needed, 
                // but this is where it would loop over alert->attributes and compare with listing->attributes
            }

            if ($attributesMatch) {
                // Match found! Dispatch notification.
                $alert->user->notify(new SmartAlertMatched($alert, $listing));
            }
        }
    }
}
