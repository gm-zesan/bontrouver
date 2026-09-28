<?php

namespace App\Services;

use App\Models\BannerAd;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PointTransaction;
use App\Models\PromotionPackage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MonetizationService
{
    /**
     * Check if a user can create a free listing or has reached their free quota limit.
     */
    public function canCreateFreeListing(User $user): bool
    {
        // Admins and dealers have unrestricted quotas
        if ($user->isAdmin() || $user->is_dealer) {
            return true;
        }

        $limit = (int) site_setting('free_listing_limit_per_user', 5);

        // If limit is 0 or negative, quota is unlimited
        if ($limit <= 0) {
            return true;
        }

        $activeListingCount = $user->listings()
            ->whereIn('status', [\App\Enums\ListingStatus::ACTIVE, \App\Enums\ListingStatus::PENDING_REVIEW])
            ->count();

        return $activeListingCount < $limit;
    }

    /**
     * Promote a listing using either CAD $ payment or Community Points.
     */
    public function promoteListing(
        Listing $listing,
        PromotionPackage $package,
        User $user,
        string $paymentMethod = 'stripe', // 'stripe' or 'points'
        ?string $transactionRef = null
    ): ListingPromotion {
        // Enforce active promotion cooldowns
        if ($package->type === 'sponsored' && $listing->is_sponsored && $listing->sponsored_until && $listing->sponsored_until->isFuture()) {
            throw ValidationException::withMessages([
                'package_id' => ["This listing is already Sponsored until " . $listing->sponsored_until->format('M d, Y') . ". You cannot boost again until the current duration ends."],
            ]);
        }

        if ($package->type === 'featured' && $listing->is_featured && $listing->featured_until && $listing->featured_until->isFuture()) {
            throw ValidationException::withMessages([
                'package_id' => ["This listing is already Featured until " . $listing->featured_until->format('M d, Y') . ". You cannot boost again until the current duration ends."],
            ]);
        }

        if ($package->type === 'bump_up' && $listing->bumped_at && $listing->bumped_at->isToday()) {
            throw ValidationException::withMessages([
                'package_id' => ["This listing was already Bumped today. You can bump it again tomorrow."],
            ]);
        }

        return DB::transaction(function () use ($listing, $package, $user, $paymentMethod, $transactionRef) {
            $now = Carbon::now();
            $expiresAt = $package->duration_days > 0 ? $now->copy()->addDays($package->duration_days) : null;
            $pointsSpent = 0;
            $pricePaid = 0.00;

            if ($paymentMethod === 'points') {
                if (!$package->point_cost || $package->point_cost <= 0) {
                    throw ValidationException::withMessages([
                        'payment_method' => ['This package cannot be redeemed with points.'],
                    ]);
                }

                if ($user->community_points < $package->point_cost) {
                    throw ValidationException::withMessages([
                        'points' => ["Insufficient community points. You have {$user->community_points} pts, but {$package->point_cost} pts are required."],
                    ]);
                }

                $pointsSpent = $package->point_cost;
                $user->decrement('community_points', $pointsSpent);

                // Record point transaction
                PointTransaction::create([
                    'user_id' => $user->id,
                    'points' => -$pointsSpent,
                    'action_type' => 'listing_boost_' . $package->type,
                    'description' => "Redeemed {$pointsSpent} points for {$package->name} on listing: {$listing->title}",
                    'reference_type' => Listing::class,
                    'reference_id' => $listing->id,
                ]);
            } else {
                $pricePaid = $package->price;
            }

            // Apply boost directly to listing
            switch ($package->type) {
                case 'sponsored':
                    $listing->is_sponsored = true;
                    $listing->sponsored_until = $expiresAt;
                    break;
                case 'featured':
                    $listing->is_featured = true;
                    $listing->featured_until = $expiresAt;
                    break;
                case 'bump_up':
                    $listing->bumped_at = $now;
                    $listing->created_at = $now; // Refreshes to top of chronological search
                    break;
            }
            $listing->save();

            // Create listing promotion audit record
            $promotion = ListingPromotion::create([
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'promotion_package_id' => $package->id,
                'type' => $package->type,
                'price_paid' => $pricePaid,
                'points_spent' => $pointsSpent,
                'payment_method' => $paymentMethod,
                'payment_status' => 'completed',
                'transaction_reference' => $transactionRef ?? ('BT-PROMO-' . strtoupper(uniqid())),
                'starts_at' => $now,
                'expires_at' => $expiresAt,
                'is_active' => true,
            ]);

            // Dispatch internal platform notification
            try {
                $user->notify(new \App\Notifications\ListingBoostActivated($listing, $package, $promotion));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Could not dispatch boost notification', ['error' => $e->getMessage()]);
            }

            return $promotion;
        });
    }

    /**
     * Get active banner ads for a given position and optional city.
     */
    public function getActiveBanners(string $position, ?string $city = null, int $limit = 2)
    {
        $query = BannerAd::active()->forPosition($position);

        if ($city) {
            $query->where(function ($q) use ($city) {
                $q->whereNull('city')
                  ->orWhere('city', 'like', '%' . $city . '%');
            });
        }

        return $query->orderBy('sort_order')->take($limit)->get();
    }
}
