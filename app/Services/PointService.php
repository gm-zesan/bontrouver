<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointRule;
use App\Models\PointTransaction;
use App\Models\Review;
use App\Models\Listing;
use App\Models\CompanionshipRequest;
use App\Notifications\MemberTierUpgraded;
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

        $oldTier = $user->member_tier;

        $transaction = PointTransaction::create([
            'user_id' => $user->id,
            'points' => $points,
            'action_type' => $actionType,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'description' => $description,
        ]);

        $user->increment('community_points', $points);
        $user->refresh();

        $newTier = $user->member_tier;

        if (($newTier['level'] ?? 1) > ($oldTier['level'] ?? 1)) {
            $user->notify(new MemberTierUpgraded($newTier, $oldTier, $user->community_points));
            if (session()) {
                session()->flash('tier_level_up', [
                    'tier' => $newTier,
                    'points' => $user->community_points,
                    'message' => "Congratulations! You've leveled up to {$newTier['name']}!",
                ]);
            }
        }

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
     * Resolve configured point amount for a specific rule key directly from database.
     */
    public static function getRulePoints(string $key, string $type = 'earn', int $default = 0): int
    {
        try {
            $rule = PointRule::where('rule_key', $key)->where('type', $type)->where('is_active', true)->first();
            if ($rule) {
                return (int) $rule->points;
            }
        } catch (\Throwable) {
            // Fallback during setup
        }

        return $default;
    }

    /**
     * Award points for receiving a positive review.
     */
    public function awardForPositiveReview(Review $review): void
    {
        if ($review->rating >= 4) {
            $points = self::getRulePoints('positive_review', 'earn', 20);
            $this->awardPoints(
                $review->reviewee,
                $points,
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
            $points = self::getRulePoints('free_listing', 'earn', 25);
            $this->awardPoints(
                $listing->user,
                $points,
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
            $points = self::getRulePoints('meetup_host', 'earn', 30);
            $this->awardPoints(
                $meetup->user,
                $points,
                'meetup_host',
                'Hosted a local community companionship meetup',
                $meetup
            );
        }
    }

    /**
     * Award points for attending a community meetup.
     */
    public function awardForMeetupAttendee(User $attendee, CompanionshipRequest $meetup): void
    {
        $points = self::getRulePoints('meetup_attendee', 'earn', 15);
        $this->awardPoints(
            $attendee,
            $points,
            'meetup_attendee',
            'Attended and participated in a local community meetup',
            $meetup
        );
    }

    /**
     * Award points for completing the first marketplace transaction.
     */
    public function awardForFirstDeal(User $user, ?Model $reference = null): void
    {
        $points = self::getRulePoints('first_deal', 'earn', 25);
        $this->awardPoints(
            $user,
            $points,
            'first_deal',
            'Bonus for completing your first community transaction',
            $reference
        );
    }

    /**
     * Check if the user has reached a new tier and update if necessary.
     */
    public function checkTierProgression(User $user): void
    {
        // MemberTier logic is dynamic based on user points via accessor.
    }
}
