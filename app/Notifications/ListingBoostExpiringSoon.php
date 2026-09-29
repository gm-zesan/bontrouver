<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\ListingPromotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ListingBoostExpiringSoon extends Notification
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
            'type' => 'listing_boost_expiring_soon',
            'title' => "⏰ {$boostType} Boost Expiring in 24h",
            'message' => "Your {$boostType} promotion for \"{$this->listing->title}\" will expire in 24 hours. Extend your visibility now to keep buyer inquiries flowing!",
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'package_type' => $this->promotion->type,
            'action_url' => route('listings.promote.show', $this->listing->id),
            'action_label' => 'Extend Boost Now',
            'icon' => 'bi-hourglass-split text-info',
        ];
    }
}
