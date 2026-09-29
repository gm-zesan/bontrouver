<?php

namespace App\Services;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupportChatService
{
    /**
     * Get or create the active support conversation for a user.
     */
    public function getOrCreateActiveConversation(User $user, ?string $subject = null): SupportConversation
    {
        $conversation = SupportConversation::where('user_id', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->latest('last_message_at')
            ->first();

        if (!$conversation) {
            $conversation = SupportConversation::create([
                'user_id' => $user->id,
                'subject' => $subject,
                'status' => 'open',
                'priority' => 'normal',
                'last_message_at' => now(),
            ]);
        }

        return $conversation;
    }

    /**
     * Get messages for a conversation, optionally only new messages after a given ID.
     */
    public function getMessages(SupportConversation $conversation, ?int $afterId = null): Collection
    {
        $query = $conversation->messages()->with('sender');

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        return $query->get();
    }

    /**
     * Send a support message.
     */
    public function sendMessage(
        SupportConversation $conversation,
        User $sender,
        string $message,
        array $attachments = [],
        string $senderType = 'user'
    ): SupportMessage {
        return DB::transaction(function () use ($conversation, $sender, $message, $attachments, $senderType) {
            $uploadedPaths = [];

            foreach ($attachments as $file) {
                if ($file instanceof UploadedFile) {
                    $path = $file->store('support/attachments', 'public');
                    $uploadedPaths[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getClientMimeType(),
                    ];
                } elseif (is_array($file) && !empty($file['path'])) {
                    $uploadedPaths[] = $file;
                } elseif (is_string($file)) {
                    $uploadedPaths[] = [
                        'path' => $file,
                        'name' => basename($file),
                    ];
                }
            }

            $supportMessage = SupportMessage::create([
                'support_conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'sender_type' => $senderType,
                'message' => trim($message),
                'attachments' => !empty($uploadedPaths) ? $uploadedPaths : null,
                'is_read' => false,
            ]);

            $conversationUpdates = [
                'last_message_at' => now(),
            ];

            // Re-open if user sends a message in a resolved conversation
            if ($senderType === 'user' && in_array($conversation->status, ['resolved', 'closed'])) {
                $conversationUpdates['status'] = 'open';
            } elseif ($senderType === 'admin' && $conversation->status === 'open') {
                $conversationUpdates['status'] = 'in_progress';
                if (!$conversation->assigned_to) {
                    $conversationUpdates['assigned_to'] = $sender->id;
                }
            }

            $conversation->update($conversationUpdates);

            $supportMessage->load(['sender', 'conversation']);

            // Broadcast real-time support message event via Laravel Reverb
            broadcast(new \App\Events\SupportMessageSent($supportMessage));

            return $supportMessage;
        });
    }

    /**
     * Mark messages in a conversation as read.
     */
    public function markMessagesAsRead(SupportConversation $conversation, string $viewerType = 'user'): int
    {
        $query = $conversation->messages()->where('is_read', false);

        if ($viewerType === 'user') {
            $query->whereIn('sender_type', ['admin', 'system']);
        } elseif ($viewerType === 'admin') {
            $query->where('sender_type', 'user');
        }

        return $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get paginated support conversations for admin dashboard.
     * New unread messages appear first at the top of the list.
     */
    public function getAdminConversations(array $filters = []): LengthAwarePaginator
    {
        $query = SupportConversation::with(['user.profile', 'assignee', 'latestMessage'])
            ->withCount(['unreadMessagesForAdmin']);

        if (!empty($filters['filter']) && $filters['filter'] === 'unread') {
            $query->whereHas('unreadMessagesForAdmin');
        }

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', $search)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', $search)
                         ->orWhere('email', 'like', $search)
                         ->orWhere('phone', 'like', $search);
                  })
                  ->orWhereHas('messages', function ($mq) use ($search) {
                      $mq->where('message', 'like', $search);
                  });
            });
        }

        // New unread messages from users appear FIRST at the top, then ordered by most recent message
        $query->orderByDesc('unread_messages_for_admin_count')
              ->orderByDesc('last_message_at');

        return $query->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Get total unread count for admin navbar/badge.
     */
    public function getUnreadCountForAdmin(): int
    {
        return SupportMessage::where('sender_type', 'user')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get total unread support message count for a specific user.
     */
    public function getUnreadCountForUser(User $user): int
    {
        return SupportMessage::whereHas('conversation', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->whereIn('sender_type', ['admin', 'system'])
        ->where('is_read', false)
        ->count();
    }

    /**
     * Update conversation status and priority.
     */
    public function updateStatus(SupportConversation $conversation, string $status, ?User $admin = null, ?string $priority = null): SupportConversation
    {
        $data = ['status' => $status];
        if ($priority) {
            $data['priority'] = $priority;
        }
        if ($admin && !$conversation->assigned_to) {
            $data['assigned_to'] = $admin->id;
        }

        $conversation->update($data);

        // Add a system update message
        if ($status === 'resolved') {
            $adminName = $admin ? $admin->name : 'Bon Trouver Support';
            $this->sendMessage(
                $conversation,
                $admin ?? $conversation->user,
                "✨ This conversation was marked as resolved by {$adminName}. If you need further assistance, simply send another message here!",
                [],
                'system'
            );
        }

        return $conversation;
    }

    /**
     * Curated interactive FAQ topics and answers.
     */
    public function getFaqTopics(): array
    {
        return [
            [
                'id' => 'verification',
                'icon' => 'bi-patch-check-fill',
                'color' => '#3B82F6',
                'title' => 'ID & Phone Verification',
                'summary' => 'How to get the Verified Canadian badge on your profile.',
                'content' => "To earn the verified badge:
1. Go to your Account Profile > Verification page.
2. Submit a valid Canadian government ID (Driver's License, Passport, or Provincial ID) or verify your Canadian mobile phone via SMS.
3. Our moderation team reviews submissions within 12 to 24 hours. Verified members enjoy higher search rankings and buyer trust.",
            ],
            [
                'id' => 'promotions',
                'icon' => 'bi-stars',
                'color' => '#F59E0B',
                'title' => 'Listing Boosts & Promotions',
                'summary' => 'How Sponsored Carousel, Featured Badges, and Bump-Ups work.',
                'content' => "You can boost your listings with CAD or Community Points:
• 👑 Sponsored Spotlight: Placed on top Hero carousel across Canada & target city.
• ⭐ Featured Badge: Highlighted with a glowing golden border and priority search placement.
• 🚀 Instant Bump-Up: Pushes your listing directly back to the top of the search results.",
            ],
            [
                'id' => 'points',
                'icon' => 'bi-gift-fill',
                'color' => '#10B981',
                'title' => 'Community Points & Levels',
                'summary' => 'Earn points through mutual aid and unlock member tiers.',
                'content' => "Bon Trouver rewards active community members:
• 🥉 New Member (0–99 pts)
• 🥈 Active Member (100–299 pts)
• 🥇 Trusted Member (300–699 pts)
• ⭐ Highly Appreciated (700+ pts)
Points are earned by completing helpful transactions, verifying IDs, writing honest reviews, and organizing companionship meetups. Points can be redeemed for listing boosts!",
            ],
            [
                'id' => 'safety',
                'icon' => 'bi-shield-shaded',
                'color' => '#EF4444',
                'title' => 'Safety, Scams & Reporting',
                'summary' => 'Tips for safe Canadian local trading and reporting suspicious listings.',
                'content' => "Safe Trading Guidelines:
• Always meet in well-lit public places (e.g. coffee shops, transit stations, bank lobbies).
• Inspect items before payment. Never send wire transfers or gift cards in advance.
• If you spot a suspicious user or prohibited listing, click the 'Report Listing' button to notify moderation immediately.",
            ],
            [
                'id' => 'meetups',
                'icon' => 'bi-people-fill',
                'color' => '#8B5CF6',
                'title' => 'Need Companionship Meetups',
                'summary' => 'Wholesome social meetups across Canadian cities.',
                'content' => "Join or create local meetups for coffee, walking, sports, gaming, and dining.
• All companionship meetups are strictly non-monetary and focused on friendship and community.
• Attendees can check in to earn Community Points.",
            ],
        ];
    }
}
