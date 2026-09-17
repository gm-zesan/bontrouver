<?php

namespace App\Notifications;

use App\Models\CompanionshipRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MeetupCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CompanionshipRequest $meetup
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'meetup_id' => $this->meetup->id,
            'meetup_title' => $this->meetup->title,
            'host_name' => $this->meetup->user->name ?? 'Host',
        ];
    }
}
