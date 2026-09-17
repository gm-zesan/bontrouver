<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\SmartAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SmartAlertMatched extends Notification implements ShouldQueue
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
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // For MVP, only database. Later can add 'mail'.
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New match for your Smart Alert: ' . $this->alert->name)
                    ->line('A new listing matches your alert criteria.')
                    ->line('Listing: ' . $this->listing->title)
                    ->line('Price: $' . number_format($this->listing->price, 2))
                    ->action('View Listing', url('/listing/' . $this->listing->slug))
                    ->line('Thank you for using Bon Trouver!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'alert_name' => $this->alert->name,
            'listing_id' => $this->listing->id,
            'listing_title' => $this->listing->title,
            'listing_slug' => $this->listing->slug,
            'price' => $this->listing->price,
        ];
    }
}
