<?php

namespace App\Services;

use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Collection;

class SellerListingService
{
    /**
     * Get the listings for a seller formatted for the dashboard.
     */
    public function getDashboardListings(User $user): array
    {
        $dbListings = $user->listings()
            ->with(['category', 'primaryImage'])
            ->withCount(['favorites as saves', 'conversations as messages'])
            ->latest()
            ->get();

        return $dbListings->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'price' => '$' . number_format($item->price, 2),
                'category' => $item->category->name ?? 'Uncategorized',
                'location' => $item->city . ', ' . $item->province,
                'status' => $item->status,
                'posted_at' => $item->created_at->diffForHumans(),
                'created_at' => $item->created_at->format('Y-m-d'),
                'views' => $item->views_count ?? 0,
                'saves' => $item->saves ?? 0,
                'messages' => $item->messages ?? 0,
                'image' => $item->primaryImage->image_path ?? asset('images/placeholder.jpg'),
                'featured' => $item->is_featured,
                'draft_progress' => 100,
            ];
        })->toArray();
    }

    /**
     * Compute tab counts for the My Listings dashboard.
     */
    public function getCounts(array $allListings): array
    {
        return [
            'all' => count($allListings),
            'active' => count(array_filter($allListings, fn($item) => $item['status'] === 'active' || $item['status'] === 'attention')),
            'drafts' => count(array_filter($allListings, fn($item) => $item['status'] === 'draft')),
            'sold' => count(array_filter($allListings, fn($item) => $item['status'] === 'sold')),
            'expired' => count(array_filter($allListings, fn($item) => $item['status'] === 'expired')),
            'paused' => count(array_filter($allListings, fn($item) => $item['status'] === 'paused')),
        ];
    }

    /**
     * Compute overall performance stats for the dashboard.
     */
    public function getStats(array $allListings, array $counts): array
    {
        return [
            'active_count' => $counts['active'],
            'drafts_count' => $counts['drafts'],
            'sold_count' => $counts['sold'],
            'total_views' => array_sum(array_column($allListings, 'views')),
            'total_saves' => array_sum(array_column($allListings, 'saves')),
            'total_messages' => array_sum(array_column($allListings, 'messages')),
        ];
    }

    /**
     * Generic dashboard header stats.
     */
    public function getDashboardHeaderStats(User $user): array
    {
        return [
            'active_listings' => $user->listings()->where('status', 'active')->count(),
            'saved_favorites_count' => $user->favorites()->count(),
            'unread_messages_count' => Message::whereHas('conversation', function ($query) use ($user) {
                $query->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })->where('sender_id', '!=', $user->id)->whereNull('read_at')->count(),
            'unread_notifications_count' => $user->unreadNotifications()->count(),
        ];
    }
}
