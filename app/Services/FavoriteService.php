<?php

namespace App\Services;

use App\Models\User;
use App\Models\Favorite;

class FavoriteService
{
    /**
     * Fetch formatted favorites for the given user.
     */
    public function getUserFavorites(User $user): array
    {
        $dbFavorites = $user->favorites()->with(['listing.category', 'listing.primaryImage', 'listing.user'])->get();

        return $dbFavorites->map(function ($fav) {
            $listing = $fav->listing;
            if (!$listing) {
                return null;
            }

            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'price' => '$' . number_format($listing->price, 2),
                'price_num' => $listing->price,
                'category' => $listing->category->name ?? 'Uncategorized',
                'location' => $listing->city . ', ' . $listing->province,
                'posted_at' => $listing->created_at->diffForHumans(),
                'views' => $listing->views_count ?? 0,
                'seller_name' => $listing->user->name ?? 'Unknown',
                'seller_verified' => $listing->user->is_verified ?? false,
                'image' => $listing->primaryImage->image_path ?? asset('images/placeholder.jpg'),
                'status' => $listing->status,
                'is_featured' => $listing->is_featured,
            ];
        })->filter()->toArray();
    }

    /**
     * Remove a listing from favorites.
     */
    public function removeFavorite(User $user, int $listingId): void
    {
        Favorite::where('user_id', $user->id)
            ->where('listing_id', $listingId)
            ->delete();
    }

    /**
     * Toggle a favorite state (add/remove).
     * Returns true if added, false if removed.
     */
    public function toggleFavorite(User $user, int $listingId): bool
    {
        $favorite = Favorite::where('user_id', $user->id)
            ->where('listing_id', $listingId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return false;
        }

        Favorite::create([
            'user_id' => $user->id,
            'listing_id' => $listingId
        ]);

        return true;
    }
}
