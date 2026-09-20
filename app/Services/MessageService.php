<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;

class MessageService
{
    /**
     * Get or create a conversation between a buyer and seller.
     * Optionally ties to a listing.
     *
     * @param int $buyerId
     * @param int $sellerId
     * @param int|null $listingId
     * @return Conversation
     */
    public static function getOrCreateConversation(int $buyerId, int $sellerId, ?int $listingId = null)
    {
        // Check if an existing conversation exists between these two users (ignoring listing)
        $conversation = Conversation::where(function ($query) use ($buyerId, $sellerId) {
            $query->where('buyer_id', $buyerId)->where('seller_id', $sellerId)
                  ->orWhere('buyer_id', $sellerId)->where('seller_id', $buyerId);
        })
        ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'buyer_id' => $buyerId,
                'seller_id' => $sellerId,
                'listing_id' => $listingId,
                'subject' => $listingId ? 'Inquiry about listing #' . $listingId : 'General Inquiry',
            ]);
        }

        return $conversation;
    }

    /**
     * Send a new message in a conversation.
     *
     * @param int $conversationId
     * @param int $senderId
     * @param string|null $body
     * @param array|null $attachments
     * @return Message
     */
    public static function sendMessage(int $conversationId, int $senderId, ?string $body = null, ?array $attachments = null)
    {
        $attachmentData = [];

        if ($attachments && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                if ($attachment instanceof \Illuminate\Http\UploadedFile) {
                    $path = $attachment->store('messages', 'public');
                    
                    // Determine type
                    $mime = $attachment->getMimeType();
                    $type = str_starts_with($mime, 'image/') ? 'image' : 'file';
                    
                    $attachmentData[] = [
                        'path' => $path,
                        'type' => $type,
                        'url' => \Illuminate\Support\Facades\Storage::url($path)
                    ];
                }
            }
        }

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'body' => $body,
            'attachments' => empty($attachmentData) ? null : $attachmentData,
        ]);

        // Load relationships needed for broadcasting
        $message->load('sender', 'conversation');

        // Broadcast the event over Reverb
        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    /**
     * Mark all unread messages in a conversation as read for the user.
     *
     * @param int $conversationId
     * @param int $userId
     * @return void
     */
    public static function markAsRead(int $conversationId, int $userId)
    {
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get all conversations for a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserConversations(int $userId)
    {
        return Conversation::with(['buyer', 'seller', 'listing.primaryImage', 'messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])
        ->where('buyer_id', $userId)
        ->orWhere('seller_id', $userId)
        ->get()
        ->sortByDesc(function ($conv) {
            return $conv->messages->last()->created_at ?? $conv->created_at;
        })
        ->values();
    }
}
