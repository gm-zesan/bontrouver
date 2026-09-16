<?php

namespace App\Services;

use App\Models\User;
use App\Models\CompanionshipRequest;

class UserProfileService
{
    /**
     * Get recent listings for the public profile.
     */
    public function getRecentListings(User $user): array
    {
        $dbUserListings = $user->listings()
            ->with(['category', 'primaryImage'])
            ->withCount('favorites as saves')
            ->latest()
            ->take(6)
            ->get();

        return $dbUserListings->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'price' => '$' . number_format($item->price, 2),
                'category' => $item->category->name ?? 'Uncategorized',
                'location' => $item->city . ', ' . $item->province,
                'posted_at' => $item->created_at->diffForHumans(),
                'views' => $item->views_count ?? 0,
                'saves' => $item->saves ?? 0,
                'image' => $item->primaryImage->image_path ?? asset('images/placeholder.jpg'),
                'status' => $item->status,
                'featured' => $item->is_featured,
            ];
        })->toArray();
    }

    /**
     * Get recent reviews for the public profile.
     */
    public function getRecentReviews(User $user): array
    {
        $dbReviews = $user->reviewsReceived()->with(['reviewer', 'listing'])->latest()->take(5)->get();

        return $dbReviews->map(function ($rev) {
            return [
                'author' => $rev->reviewer->name ?? 'Unknown',
                'avatar' => $rev->reviewer->avatar ?? asset('images/avatar-placeholder.png'),
                'rating' => $rev->rating,
                'date' => $rev->created_at->diffForHumans(),
                'comment' => $rev->comment,
                'item_title' => $rev->listing->title ?? 'Deleted Item'
            ];
        })->toArray();
    }

    /**
     * Get upcoming hosted meetups for the public profile.
     */
    public function getHostedMeetups(User $user)
    {
        return CompanionshipRequest::with(['cityRelation', 'attendees.user'])
            ->where('user_id', $user->id)
            ->orderBy('meetup_date_time', 'desc')
            ->take(4)
            ->get();
    }
}
