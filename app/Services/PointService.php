<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointTransaction;
use App\Models\Review;
use App\Models\Listing;
use App\Models\CompanionshipRequest;
use Illuminate\Database\Eloquent\Model;

class PointService
{
    /**
     * Core method to award points to a user.
     */
    public function awardPoints(User $user, int $points, string $actionType, string $description, ?Model $reference = null): ?PointTransaction
    {
        if ($points <= 0) {
            return null;
        }

        // Prevent duplicate awards for the same action on the same reference
        if ($reference) {
            $alreadyAwarded = PointTransaction::where('user_id', $user->id)
                ->where('action_type', $actionType)
                ->where('reference_type', get_class($reference))
                ->where('reference_id', $reference->id)
                ->exists();

            if ($alreadyAwarded) {
                return null;
            }
        }

        $transaction = PointTransaction::create([
            'user_id' => $user->id,
            'points' => $points,
            'action_type' => $actionType,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'description' => $description,
        ]);

        $user->increment('community_points', $points);

        return $transaction;
    }

    /**
     * Deduct points from a user for spending actions (e.g., promotions).
     *
     * @throws \Exception if the user doesn't have enough points.
     */
    public function spendPoints(User $user, int $points, string $actionType, string $description, ?Model $reference = null): PointTransaction
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException("Points to spend must be greater than 0.");
        }

        if ($user->community_points < $points) {
            throw new \Exception("Insufficient community points.");
        }

        $transaction = PointTransaction::create([
            'user_id' => $user->id,
            'points' => -$points,
            'action_type' => $actionType,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'description' => $description,
        ]);

        $user->decrement('community_points', $points);

        return $transaction;
    }

    /**
     * Award points for receiving a positive review.
     */
    public function awardForPositiveReview(Review $review): void
    {
        if ($review->rating >= 4) {
            $this->awardPoints(
                $review->reviewee,
                config('points.earn.positive_review'),
                'positive_review',
                'Received a positive review from a community member',
                $review
            );
        }
    }

    /**
     * Award points for listing a free/donated item.
     */
    public function awardForFreeListing(Listing $listing): void
    {
        if ((float) $listing->price == 0 && $listing->status === 'active') {
            $this->awardPoints(
                $listing->user,
                config('points.earn.free_listing'),
                'free_listing',
                'Donated an item to the community for free',
                $listing
            );
        }
    }

    /**
     * Award points for successfully hosting a community meetup.
     */
    public function awardForMeetupHost(CompanionshipRequest $meetup): void
    {
        if ($meetup->status === 'completed' || count($meetup->attendees->where('status', 'approved')) > 0) {
            // Award if the meetup is successfully completed or they successfully gathered attendees.
            $this->awardPoints(
                $meetup->user,
                config('points.earn.meetup_host'),
                'meetup_host',
                'Hosted a local community companionship meetup',
                $meetup
            );
        }
    }

    /**
     * Check if the user has reached a new tier and update if necessary.
     */
    public function checkTierProgression(User $user): void
    {
        // Not explicitly requested to store tier_id on user yet, 
        // as MemberTier logic is dynamic based on user points.
        // But if we need to dispatch a notification when they cross a threshold:
        // (Optional future implementation for email/in-app notifications)
    }
}
