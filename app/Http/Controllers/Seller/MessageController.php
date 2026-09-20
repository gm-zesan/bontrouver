<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\MessageService;
use App\Services\SellerListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct(
        private readonly SellerListingService $listingService
    ) {}

    /**
     * Display Messages & Inbox Conversations.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Handle starting a new conversation via query parameters
        if ($request->query('c') === 'new' && $request->has('user')) {
            $sellerId = (int) $request->query('user');
            if ($sellerId !== $user->id) {
                $listingId = $request->query('listing') ? (int) $request->query('listing') : null;
                $newConv = MessageService::getOrCreateConversation($user->id, $sellerId, $listingId);
                return redirect()->route('messages.index', ['c' => $newConv->id]);
            }
        }

        $dbConversations = MessageService::getUserConversations($user->id);

        $conversations = $dbConversations->map(function ($conv) use ($user) {
            $otherUser = $conv->buyer_id == $user->id ? $conv->seller : $conv->buyer;
            $listing = $conv->listing;

            $lastMessage = $conv->messages->last();
            $unreadCount = $conv->messages->where('sender_id', '!=', $user->id)->whereNull('read_at')->count();

            $lastMessagePreview = 'No messages yet.';
            if ($lastMessage) {
                if ($lastMessage->body) {
                    $lastMessagePreview = $lastMessage->body;
                } elseif (!empty($lastMessage->attachments)) {
                    $count = count($lastMessage->attachments);
                    $lastMessagePreview = $count > 1 ? "Sent $count attachments" : 'Sent an attachment';
                }
            }

            return [
                'id' => $conv->id,
                'user' => [
                    'name' => $otherUser->name ?? 'Unknown',
                    'avatar' => $otherUser->avatar ?? null,
                    'online' => false,
                    'location' => $otherUser->location ?? 'Canada',
                    'verified' => $otherUser->is_verified ?? false,
                    'rating' => $otherUser->rating ?? 0,
                ],
                'listing' => [
                    'id' => $listing->id ?? null,
                    'title' => $listing->title ?? 'Deleted Listing',
                    'price' => isset($listing->price) ? '$' . number_format($listing->price, 2) : '',
                    'image' => $listing->primaryImage->image_path ?? asset('images/placeholder.jpg'),
                    'status' => $listing->status ?? 'Deleted',
                ],
                'last_message' => $lastMessagePreview,
                'last_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                'unread' => $unreadCount > 0,
                'unread_count' => $unreadCount,
                'messages' => $conv->messages->map(function ($msg) use ($user) {
                    return [
                        'id' => $msg->id,
                        'sender' => $msg->sender_id == $user->id ? 'me' : 'them',
                        'text' => $msg->body,
                        'attachments' => $msg->attachments,
                        'time' => $msg->created_at->format('M d, g:i A'),
                    ];
                })->values()->toArray(),
            ];
        })->toArray();

        $activeConversationId = (int) $request->query('c', $conversations[0]['id'] ?? 0);
        $activeConversation = collect($conversations)->firstWhere('id', $activeConversationId) ?? ($conversations[0] ?? null);

        if ($activeConversation) {
            MessageService::markAsRead($activeConversation['id'], $user->id);
            $activeConversation['unread'] = false;
            $activeConversation['unread_count'] = 0;
            
            $key = collect($conversations)->search(fn($c) => $c['id'] == $activeConversation['id']);
            if ($key !== false) {
                $conversations[$key]['unread'] = false;
                $conversations[$key]['unread_count'] = 0;
            }
        }

        return view('frontend.account.messages', [
            'user' => $user,
            'categories' => CategoryService::getAll(),
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'stats' => $this->listingService->getDashboardHeaderStats($user),
        ]);
    }

    /**
     * Send new message in conversation.
     */
    public function send(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120', // 5MB max per file
        ]);

        $text = $request->input('message');
        $attachments = $request->file('attachments');

        if (empty(trim($text)) && empty($attachments)) {
            return response()->json(['success' => false, 'message' => 'Message cannot be empty.'], 422);
        }

        $user = Auth::user();
        $msg = MessageService::sendMessage($conversationId, $user->id, $text, $attachments);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'sender' => 'me',
                'text' => e($msg->body),
                'attachments' => $msg->attachments,
                'time' => $msg->created_at->format('M d, g:i A'),
            ]
        ]);
    }

    /**
     * Initiate a new conversation and send the first message.
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|integer',
            'listing_id' => 'nullable|integer',
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        $sellerId = $request->input('seller_id');
        $text = $request->input('message');

        if ($sellerId === $user->id) {
            return response()->json(['success' => false, 'message' => 'You cannot message yourself.'], 422);
        }

        $conv = MessageService::getOrCreateConversation($user->id, $sellerId, $request->input('listing_id'));
        MessageService::sendMessage($conv->id, $user->id, $text);

        return response()->json([
            'success' => true,
            'redirect_url' => route('messages.index', ['c' => $conv->id])
        ]);
    }
}
