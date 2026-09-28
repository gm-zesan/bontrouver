<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ListingBoostActivated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Listing $listing,
        public readonly PromotionPackage $package,
        public readonly ListingPromotion $promotion
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $packageName = $this->package->name;
        $pricePaid = $this->promotion->price_paid;
        $pointsSpent = $this->promotion->points_spent;
        $paymentSummary = $this->promotion->payment_method === 'points'
            ? "{$pointsSpent} Points"
            : '$' . number_format($pricePaid, 2) . ' CAD';

        $icon = match ($this->package->type) {
            'sponsored' => 'bi-award-fill text-warning',
            'featured' => 'bi-star-fill text-primary',
            'bump_up' => 'bi-rocket-takeoff-fill text-success',
            default => 'bi-lightning-charge-fill text-info',
        };

        return [
            'type' => 'listing_boost_activated',
            'title' => "🚀 Boost Activated: {$packageName}",
            'message' => "Your listing \"{$this->listing->title}\" has been successfully upgraded with {$packageName} ({$paymentSummary}). It is now enjoying top visibility across the platform.",
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'package_name' => $packageName,
            'package_type' => $this->package->type,
            'payment_method' => $this->promotion->payment_method,
            'payment_amount' => $paymentSummary,
            'expires_at' => $this->promotion->expires_at?->toIso8601String(),
            'action_url' => route('listings.show', $this->listing->slug ?? $this->listing->id),
            'action_label' => 'View Boosted Listing',
            'icon' => $icon,
        ];
    }
}
