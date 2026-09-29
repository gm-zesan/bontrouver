<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ListingExpired extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Listing $listing
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'listing_expired',
            'title' => '📦 Listing Expired',
            'message' => "Your listing \"{$this->listing->title}\" has expired after its active duration. You can renew or repost it anytime from your dashboard.",
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'action_url' => route('my-listings'),
            'action_label' => 'Manage Listings',
            'icon' => 'bi-archive text-secondary',
        ];
    }
}
