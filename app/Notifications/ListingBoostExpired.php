<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\ListingPromotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ListingBoostExpired extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Listing $listing,
        public readonly ListingPromotion $promotion
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $boostType = ucfirst(str_replace('_', ' ', $this->promotion->type));

        return [
            'type' => 'listing_boost_expired',
            'title' => "⌛ {$boostType} Boost Expired",
            'message' => "The {$boostType} boost for your listing \"{$this->listing->title}\" has expired. Re-boost today to get top placement and reach thousands of buyers!",
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'package_type' => $this->promotion->type,
            'action_url' => route('listings.promote.show', $this->listing->id),
            'action_label' => 'Re-Boost Listing',
            'icon' => 'bi-arrow-repeat text-warning',
        ];
    }
}
