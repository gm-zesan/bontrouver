<?php

namespace App\Services;

use App\Enums\ListingStatus;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AdminListingService
{
    /**
     * Build base query for admin listings DataTable with eager-loaded relations.
     */
    public function getListingsQuery(array $filters = []): Builder
    {
        $query = Listing::query()
            ->with(['user', 'category', 'city.province', 'primaryImage']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $cat = \App\Models\Category::with('children')->find($filters['category_id']);
            if ($cat && $cat->children->isNotEmpty()) {
                $categoryIds = $cat->children->pluck('id')->push($cat->id)->toArray();
                $query->whereIn('category_id', $categoryIds);
            } else {
                $query->where('category_id', $filters['category_id']);
            }
        }

        if (isset($filters['featured']) && $filters['featured'] !== '') {
            $query->where('is_featured', filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['sponsored']) && $filters['sponsored'] !== '') {
            $query->where('is_sponsored', filter_var($filters['sponsored'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query;
    }

    /**
     * Toggle or update the status of a listing using ListingStatus enum.
     */
    public function updateStatus(Listing $listing, ListingStatus|string $status): Listing
    {
        $statusValue = $status instanceof ListingStatus ? $status : ListingStatus::from($status);
        $listing->update(['status' => $statusValue]);
        return $listing->fresh();
    }

    /**
     * Toggle the featured flag on a listing.
     */
    public function toggleFeatured(Listing $listing): Listing
    {
        $listing->update(['is_featured' => !$listing->is_featured]);
        return $listing->fresh();
    }

    /**
     * Toggle the sponsored flag on a listing.
     */
    public function toggleSponsored(Listing $listing): Listing
    {
        $listing->update(['is_sponsored' => !$listing->is_sponsored]);
        return $listing->fresh();
    }

    /**
     * Delete a listing.
     */
    public function deleteListing(Listing $listing): bool
    {
        return (bool) $listing->delete();
    }

    /**
     * Execute bulk moderation actions on an array of listing IDs.
     */
    public function handleBulkAction(string $action, array $listingIds): array
    {
        return DB::transaction(function () use ($action, $listingIds) {
            $count = count($listingIds);

            switch ($action) {
                case 'activate':
                    Listing::whereIn('id', $listingIds)->update(['status' => ListingStatus::ACTIVE->value]);
                    $message = "Successfully activated {$count} listing(s).";
                    break;

                case 'pause':
                case 'suspend':
                    Listing::whereIn('id', $listingIds)->update(['status' => ListingStatus::PAUSED->value]);
                    $message = "Successfully paused {$count} listing(s).";
                    break;

                case 'feature':
                    Listing::whereIn('id', $listingIds)->update(['is_featured' => true]);
                    $message = "Successfully featured {$count} listing(s).";
                    break;

                case 'unfeature':
                    Listing::whereIn('id', $listingIds)->update(['is_featured' => false]);
                    $message = "Successfully removed featured status for {$count} listing(s).";
                    break;

                case 'delete':
                    Listing::whereIn('id', $listingIds)->delete();
                    $message = "Successfully deleted {$count} listing(s).";
                    break;

                default:
                    throw new \InvalidArgumentException("Invalid bulk action: {$action}");
            }

            return [
                'success' => true,
                'count' => $count,
                'message' => $message,
            ];
        });
    }

    /**
     * Resolve a moderation report on a listing.
     */
    public function resolveReport(\App\Models\Report $report, ?int $reviewerId = null): \App\Models\Report
    {
        $report->update([
            'status' => 'resolved',
            'reviewed_by' => $reviewerId ?? auth()->id(),
            'reviewed_at' => now(),
        ]);
        return $report->fresh();
    }

    /**
     * Dismiss a moderation report on a listing.
     */
    public function dismissReport(\App\Models\Report $report, ?int $reviewerId = null): \App\Models\Report
    {
        $report->update([
            'status' => 'dismissed',
            'reviewed_by' => $reviewerId ?? auth()->id(),
            'reviewed_at' => now(),
        ]);
        return $report->fresh();
    }
}

