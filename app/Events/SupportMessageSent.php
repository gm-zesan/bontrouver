<?php

namespace App\Events;

use App\Models\SupportMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SupportMessage $message;

    /**
     * Create a new event instance.
     */
    public function __construct(SupportMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $conversation = $this->message->conversation;
        $userId = $conversation ? $conversation->user_id : $this->message->sender_id;

        return [
            new PrivateChannel('support.conversation.' . $this->message->support_conversation_id),
            new PrivateChannel('admin.support'),
            new PrivateChannel('App.Models.User.' . $userId),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $senderName = 'Bon Trouver Support';
        $senderAvatar = asset('images/support-avatar.svg');

        if ($this->message->sender) {
            $senderName = $this->message->sender->name;
            $senderAvatar = $this->message->sender->avatar_url ?: asset('images/default-avatar.svg');
        } elseif ($this->message->sender_type === 'user') {
            $senderName = 'User';
            $senderAvatar = asset('images/default-avatar.svg');
        }

        return [
            'id' => $this->message->id,
            'support_conversation_id' => $this->message->support_conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_type' => $this->message->sender_type,
            'sender_name' => $senderName,
            'sender_avatar' => $senderAvatar,
            'message' => $this->message->message,
            'attachments' => $this->message->attachments,
            'attachment_files' => $this->message->attachment_files,
            'created_at_time' => $this->message->created_at ? $this->message->created_at->format('g:i A') : 'Just now',
            'time' => $this->message->created_at ? $this->message->created_at->format('g:i A') : 'Just now',
            'is_admin' => $this->message->sender_type === 'admin',
            'is_sender' => false,
            'is_read' => $this->message->is_read,
        ];
    }
}
