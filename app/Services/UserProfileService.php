<?php

namespace App\Services;

use App\Enums\ListingStatus;
use App\Models\User;
use App\Models\CompanionshipRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserProfileService
{
    /**
     * Get recent active listings for the public profile.
     */
    public function getRecentListings(User $user, int $limit = 6): array
    {
        $dbUserListings = $user->listings()
            ->with(['category', 'primaryImage'])
            ->withCount('favorites as saves')
            ->latest()
            ->take($limit)
            ->get();

        return $dbUserListings->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'price' => '$' . number_format($item->price, 2),
                'category' => $item->category->name ?? 'Uncategorized',
                'location' => $item->city ? ($item->city . ', ' . ($item->province ?? '')) : 'Canada',
                'posted_at' => $item->created_at->diffForHumans(),
                'views' => $item->views_count ?? 0,
                'saves' => $item->saves ?? 0,
                'image' => $item->primaryImage->image_path ?? asset('images/placeholder.jpg'),
                'status' => $item->status instanceof ListingStatus ? $item->status->value : (string) ($item->status ?? 'active'),
                'featured' => (bool) $item->is_featured,
            ];
        })->toArray();
    }

    /**
     * Get recent reviews for the public profile.
     */
    public function getRecentReviews(User $user, int $limit = 10): array
    {
        $dbReviews = $user->reviewsReceived()
            ->with(['reviewer', 'listing'])
            ->latest()
            ->take($limit)
            ->get();

        return $dbReviews->map(function ($rev) {
            return [
                'id' => $rev->id,
                'author' => $rev->reviewer->name ?? 'Community Member',
                'avatar' => $rev->reviewer->avatar ?? null,
                'rating' => (int) $rev->rating,
                'date' => $rev->created_at->diffForHumans(),
                'comment' => $rev->comment,
                'item_title' => $rev->listing->title ?? 'Classified Listing',
                'listing_id' => $rev->listing_id ?? null,
            ];
        })->toArray();
    }

    /**
     * Get upcoming or recent hosted meetups for the public profile.
     */
    public function getHostedMeetups(User $user, int $limit = 6)
    {
        return CompanionshipRequest::with(['cityRelation', 'attendees.user'])
            ->where('user_id', $user->id)
            ->orderBy('meetup_date_time', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Update user profile information, including avatar image handling and extended profile data.
     */
    public function updateProfile(User $user, array $data, $avatarInput = null, $coverInput = null, $galleryFiles = []): User
    {
        if (isset($data['name'])) {
            $user->name = trim($data['name']);
        }
        if (isset($data['phone'])) {
            $user->phone = trim($data['phone']);
        }
        if (isset($data['city'])) {
            $user->city = trim($data['city']);
        }
        if (isset($data['province'])) {
            $user->province = trim($data['province']);
        }
        if (isset($data['postal_code'])) {
            $user->postal_code = strtoupper(trim($data['postal_code']));
        }
        if (isset($data['bio'])) {
            $user->bio = trim($data['bio']);
        }

        // Composite location display (e.g. "Montreal, QC")
        if (isset($data['location'])) {
            $user->location = trim($data['location']);
        } elseif (!empty($data['city']) && !empty($data['province'])) {
            $user->location = trim($data['city']) . ', ' . trim($data['province']);
        }

        // Handle Avatar upload
        if ($avatarInput instanceof UploadedFile) {
            $path = $avatarInput->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        } elseif (is_string($avatarInput) && str_starts_with($avatarInput, 'data:image')) {
            $user->avatar = $this->storeBase64Avatar($avatarInput);
        } elseif (is_string($avatarInput) && !empty($avatarInput)) {
            $user->avatar = $avatarInput;
        }

        $user->save();

        // Handle Extended Profile Data
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        if (isset($data['about_text'])) {
            $profile->about_text = trim($data['about_text']);
        }
        if (isset($data['website_url'])) {
            $profile->website_url = trim($data['website_url']);
        }
        if (isset($data['social_links'])) {
            $profile->social_links = array_filter($data['social_links']); // removes nulls
        }
        if (isset($data['operating_hours'])) {
            $profile->operating_hours = array_filter($data['operating_hours']);
        }

        // Handle Cover Image upload
        if ($coverInput instanceof UploadedFile) {
            $path = $coverInput->store('covers', 'public');
            $profile->cover_image_path = '/storage/' . $path;
        }

        $profile->save();

        // Handle Gallery Images
        if (!empty($galleryFiles)) {
            $currentMaxOrder = $user->gallery()->max('sort_order') ?? 0;
            foreach ($galleryFiles as $idx => $file) {
                if ($file instanceof UploadedFile) {
                    $path = $file->store('galleries', 'public');
                    $user->gallery()->create([
                        'image_path' => '/storage/' . $path,
                        'sort_order' => $currentMaxOrder + $idx + 1,
                    ]);
                }
            }
        }

        return $user;
    }

    /**
     * Decode and store base64 avatar images to storage/app/public/avatars.
     */
    private function storeBase64Avatar(string $base64String): string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            $extension = strtolower($matches[1]);
            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }
            $cleanData = substr($base64String, strpos($base64String, ',') + 1);
            $decoded = base64_decode($cleanData);

            if ($decoded !== false) {
                $filename = 'avatars/' . Str::random(24) . '.' . $extension;
                Storage::disk('public')->put($filename, $decoded);
                return '/storage/' . $filename;
            }
        }

        return $base64String;
    }
}
