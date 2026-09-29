<?php

namespace App\Http\Controllers;

use App\Models\SupportConversation;
use App\Services\SupportChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportChatController extends Controller
{
    public function __construct(
        protected SupportChatService $supportChatService
    ) {}

    /**
     * Initialize widget data for current visitor.
     */
    public function init(Request $request): JsonResponse
    {
        $faqs = $this->supportChatService->getFaqTopics();

        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false,
                'faqs' => $faqs,
                'login_url' => route('login'),
                'register_url' => route('register'),
                'message' => 'Please log in to chat with our Canadian support & moderation team.',
            ]);
        }

        $user = Auth::user();
        $conversation = $this->supportChatService->getOrCreateActiveConversation($user);
        $this->supportChatService->markMessagesAsRead($conversation, 'user');
        $messages = $this->supportChatService->getMessages($conversation);
        $unreadCount = $this->supportChatService->getUnreadCountForUser($user);

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'is_verified' => (bool) $user->is_verified,
                'member_tier' => $user->member_tier,
            ],
            'conversation' => [
                'id' => $conversation->id,
                'subject' => $conversation->subject,
                'status' => $conversation->status,
                'last_message_at' => $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : null,
            ],
            'messages' => $messages->map(fn($msg) => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_type === 'user' ? $user->name : ($msg->sender_type === 'admin' ? ($msg->sender ? $msg->sender->name : 'Bon Trouver Support') : 'Bon Trouver Bot'),
                'sender_avatar' => $msg->sender_type === 'user' ? ($user->avatar_url ?: asset('images/default-avatar.svg')) : ($msg->sender?->avatar_url ?: asset('images/support-avatar.svg')),
                'message' => $msg->message,
                'attachments' => $msg->attachment_urls,
                'attachment_files' => $msg->attachment_files,
                'created_at_human' => $msg->created_at ? $msg->created_at->format('M d, g:i A') : '',
                'created_at_time' => $msg->created_at ? $msg->created_at->format('g:i A') : '',
                'is_sender' => $msg->sender_id === $user->id && $msg->sender_type === 'user',
            ]),
            'unread_count' => $unreadCount,
            'faqs' => $faqs,
        ]);
    }

    /**
     * Poll / retrieve messages for the active conversation.
     */
    public function getMessages(Request $request, SupportConversation $conversation): JsonResponse
    {
        if (!Auth::check() || ($conversation->user_id !== Auth::id() && !Auth::user()->isAdmin())) {
            return response()->json(['error' => 'Unauthorized conversation access.'], 403);
        }

        $afterId = $request->query('after_id') ? (int) $request->query('after_id') : null;
        $messages = $this->supportChatService->getMessages($conversation, $afterId);
        $this->supportChatService->markMessagesAsRead($conversation, 'user');

        return response()->json([
            'status' => 'success',
            'conversation_status' => $conversation->fresh()->status,
            'messages' => $messages->map(fn($msg) => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_type === 'user' ? ($msg->sender ? $msg->sender->name : 'User') : ($msg->sender_type === 'admin' ? ($msg->sender ? $msg->sender->name : 'Bon Trouver Support') : 'Bon Trouver Bot'),
                'sender_avatar' => $msg->sender_type === 'user' ? ($msg->sender?->avatar_url ?: asset('images/default-avatar.svg')) : ($msg->sender?->avatar_url ?: asset('images/support-avatar.svg')),
                'message' => $msg->message,
                'attachments' => $msg->attachment_urls,
                'attachment_files' => $msg->attachment_files,
                'created_at_human' => $msg->created_at ? $msg->created_at->format('M d, g:i A') : '',
                'created_at_time' => $msg->created_at ? $msg->created_at->format('g:i A') : '',
                'is_sender' => $msg->sender_id === Auth::id() && $msg->sender_type === 'user',
            ]),
        ]);
    }

    /**
     * Send a new message from the user.
     */
    public function sendMessage(Request $request, SupportConversation $conversation): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Please log in to send a support message.',
                'login_url' => route('login'),
            ], 401);
        }

        if ($conversation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'message' => 'required_without:attachments|nullable|string|max:3000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,webp,gif,svg,pdf,doc,docx,txt,rtf,xls,xlsx,csv|max:10240',
        ]);

        $message = $this->supportChatService->sendMessage(
            $conversation,
            Auth::user(),
            $request->input('message') ?? '',
            $request->file('attachments', []),
            'user'
        );

        return response()->json([
            'status' => 'success',
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_type' => $message->sender_type,
                'sender_name' => Auth::user()->name,
                'sender_avatar' => Auth::user()->avatar_url,
                'message' => $message->message,
                'attachments' => $message->attachment_urls,
                'attachment_files' => $message->attachment_files,
                'created_at_human' => $message->created_at->format('M d, g:i A'),
                'created_at_time' => $message->created_at->format('g:i A'),
                'is_sender' => true,
            ],
        ]);
    }

    /**
     * Get standalone FAQ list.
     */
    public function getFaqs(): JsonResponse
    {
        return response()->json([
            'faqs' => $this->supportChatService->getFaqTopics(),
        ]);
    }
}
