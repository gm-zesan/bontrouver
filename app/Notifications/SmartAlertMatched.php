<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\SmartAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SmartAlertMatched extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public SmartAlert $alert,
        public Listing $listing
    ) {}

    /**
     * Get the notification's delivery channels (Internal Database Notification Only).
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (method_exists($notifiable, 'wantsNotification') && !$notifiable->wantsNotification('alerts')) {
            return [];
        }

        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $priceFormatted = '$' . number_format($this->listing->price, 2) . ' CAD';
        $location = trim(($this->listing->city ?? '') . ', ' . ($this->listing->province ?? ''));

        return [
            'type' => 'smart_alert_matched',
            'alert_id' => $this->alert->id,
            'alert_name' => $this->alert->name,
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'listing_slug' => $this->listing->slug,
            'price' => $this->listing->price,
            'price_formatted' => $priceFormatted,
            'location' => $location,
            'title' => "🔔 Smart Alert Match: {$this->alert->name}",
            'message' => "A new listing \"{$this->listing->title}\" ({$priceFormatted} in {$location}) matches your \"{$this->alert->name}\" search criteria.",
            'action_url' => route('listings.show', $this->listing->slug ?? $this->listing->id),
            'action_label' => 'View New Listing',
            'icon' => 'bi-search-heart text-success',
        ];
    }
}
