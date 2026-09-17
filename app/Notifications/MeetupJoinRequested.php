<?php

namespace App\Notifications;

use App\Models\CompanionshipRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MeetupJoinRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CompanionshipRequest $meetup,
        public User $requester
    ) {}

    public function via(object $notifiable): array
    {
        if (method_exists($notifiable, 'wantsNotification') && !$notifiable->wantsNotification('meetups')) {
            return [];
        }

        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'meetup_id' => $this->meetup->id,
            'meetup_title' => $this->meetup->title,
            'requester_id' => $this->requester->id,
            'requester_name' => $this->requester->name,
            'city' => $this->meetup->city,
        ];
    }
}
