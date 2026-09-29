<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use App\Services\SupportChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportManagementController extends Controller
{
    public function __construct(
        protected SupportChatService $supportChatService
    ) {}

    /**
     * Admin Support Helpdesk Hub.
     */
    public function index(Request $request): View
    {
        $filters = [
            'filter' => $request->query('filter', 'all'),
            'search' => $request->query('search', ''),
        ];

        $conversations = $this->supportChatService->getAdminConversations($filters);

        $unreadMessagesCount = $this->supportChatService->getUnreadCountForAdmin();
        $unreadConversationsCount = SupportConversation::whereHas('unreadMessagesForAdmin')->count();

        // Stats summary
        $stats = [
            'total' => SupportConversation::count(),
            'unread_messages' => $unreadMessagesCount,
            'unread_conversations' => $unreadConversationsCount,
        ];

        $selectedConversationId = $request->query('conversation_id');
        $selectedConversation = null;

        if ($selectedConversationId) {
            $selectedConversation = SupportConversation::with(['user.profile', 'assignee', 'messages.sender'])
                ->find($selectedConversationId);
        } elseif ($conversations->isNotEmpty()) {
            $selectedConversation = $conversations->first();
            $selectedConversation->load(['user.profile', 'assignee', 'messages.sender']);
        }

        if ($selectedConversation) {
            $this->supportChatService->markMessagesAsRead($selectedConversation, 'admin');
        }

        return view('admin.support.index', compact('conversations', 'filters', 'stats', 'selectedConversation'));
    }

    /**
     * Get details and messages for an active conversation.
     */
    public function show(SupportConversation $conversation): JsonResponse
    {
        $conversation->load(['user', 'assignee']);
        $this->supportChatService->markMessagesAsRead($conversation, 'admin');
        $messages = $this->supportChatService->getMessages($conversation);

        $user = $conversation->user;
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? 'Not provided',
            'city' => $user->city ?? 'Unknown',
            'province' => $user->province ?? 'Canada',
            'avatar_url' => $user->avatar_url,
            'is_verified' => (bool) $user->is_verified,
            'community_points' => $user->community_points ?? 0,
            'member_tier' => $user->member_tier,
            'listings_count' => $user->listings()->count(),
            'completed_deals' => $user->completed_transactions_count,
            'joined_at' => $user->created_at ? $user->created_at->format('M d, Y') : 'Unknown',
        ];

        return response()->json([
            'status' => 'success',
            'conversation' => [
                'id' => $conversation->id,
                'subject' => $conversation->subject,
                'status' => $conversation->status,
                'priority' => $conversation->priority,
                'assigned_to' => $conversation->assignee ? $conversation->assignee->name : 'Unassigned',
                'created_at' => $conversation->created_at->format('M d, Y g:i A'),
            ],
            'user' => $userData,
            'messages' => $messages->map(fn($msg) => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_type === 'user' ? $user->name : ($msg->sender_type === 'admin' ? ($msg->sender ? $msg->sender->name : 'Bon Trouver Support') : 'System'),
                'sender_avatar' => $msg->sender_type === 'user' ? ($user->avatar_url ?: asset('images/default-avatar.svg')) : ($msg->sender?->avatar_url ?: asset('images/support-avatar.svg')),
                'message' => $msg->message,
                'attachments' => $msg->attachment_urls,
                'attachment_files' => $msg->attachment_files,
                'created_at_human' => $msg->created_at ? $msg->created_at->format('M d, g:i A') : '',
                'created_at_time' => $msg->created_at ? $msg->created_at->format('g:i A') : '',
                'is_sender' => $msg->sender_type === 'admin' && $msg->sender_id === Auth::id(),
                'is_admin' => $msg->sender_type === 'admin',
                'is_system' => $msg->sender_type === 'system',
            ]),
        ]);
    }

    /**
     * Send an admin reply.
     */
    public function reply(Request $request, SupportConversation $conversation): JsonResponse
    {
        $request->validate([
            'message' => 'required_without:attachments|nullable|string|max:4000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,webp,gif,svg,pdf,doc,docx,txt,rtf,xls,xlsx,csv|max:10240',
        ]);

        $message = $this->supportChatService->sendMessage(
            $conversation,
            Auth::user(),
            $request->input('message') ?? '',
            $request->file('attachments', []),
            'admin'
        );

        return response()->json([
            'status' => 'success',
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_type' => 'admin',
                'sender_name' => Auth::user()->name,
                'sender_avatar' => Auth::user()->avatar_url ?: asset('images/support-avatar.svg'),
                'message' => $message->message,
                'attachments' => $message->attachment_urls,
                'attachment_files' => $message->attachment_files,
                'created_at_human' => $message->created_at->format('M d, g:i A'),
                'created_at_time' => $message->created_at->format('g:i A'),
                'is_sender' => true,
                'is_admin' => true,
                'is_system' => false,
            ],
            'conversation_status' => $conversation->fresh()->status,
        ]);
    }

    /**
     * Update status or priority of a support conversation.
     */
    public function updateStatus(Request $request, SupportConversation $conversation): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|in:normal,high,urgent',
        ]);

        $this->supportChatService->updateStatus(
            $conversation,
            $request->input('status'),
            Auth::user(),
            $request->input('priority')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Support conversation updated successfully.',
            'new_status' => $conversation->status,
            'new_priority' => $conversation->priority,
        ]);
    }

    /**
     * Poll new messages for an admin active window.
     */
    public function poll(Request $request, SupportConversation $conversation): JsonResponse
    {
        $afterId = $request->query('after_id') ? (int) $request->query('after_id') : null;
        $messages = $this->supportChatService->getMessages($conversation, $afterId);
        $this->supportChatService->markMessagesAsRead($conversation, 'admin');

        return response()->json([
            'status' => 'success',
            'messages' => $messages->map(fn($msg) => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_type === 'user' ? $conversation->user->name : ($msg->sender_type === 'admin' ? ($msg->sender ? $msg->sender->name : 'Bon Trouver Support') : 'System'),
                'sender_avatar' => $msg->sender_type === 'user' ? ($conversation->user->avatar_url ?: asset('images/default-avatar.svg')) : ($msg->sender?->avatar_url ?: asset('images/support-avatar.svg')),
                'message' => $msg->message,
                'attachments' => $msg->attachment_urls,
                'attachment_files' => $msg->attachment_files,
                'created_at_human' => $msg->created_at ? $msg->created_at->format('M d, g:i A') : '',
                'created_at_time' => $msg->created_at ? $msg->created_at->format('g:i A') : '',
                'is_sender' => $msg->sender_type === 'admin' && $msg->sender_id === Auth::id(),
                'is_admin' => $msg->sender_type === 'admin',
                'is_system' => $msg->sender_type === 'system',
            ]),
        ]);
    }

    /**
     * Real-time polling for the entire admin support inbox / conversation list.
     */
    public function pollInbox(Request $request): JsonResponse
    {
        $filters = [
            'filter' => $request->query('filter', 'all'),
            'search' => $request->query('search', ''),
        ];

        $conversations = $this->supportChatService->getAdminConversations($filters);
        $unreadMessagesCount = $this->supportChatService->getUnreadCountForAdmin();
        $unreadConversationsCount = SupportConversation::whereHas('unreadMessagesForAdmin')->count();

        $selectedConvId = $request->query('current_conversation_id') ? (int) $request->query('current_conversation_id') : null;
        $afterId = $request->query('after_id') ? (int) $request->query('after_id') : null;

        $newMessages = [];
        if ($selectedConvId) {
            $currentConv = SupportConversation::with(['user', 'messages.sender'])->find($selectedConvId);
            if ($currentConv) {
                $rawMessages = $this->supportChatService->getMessages($currentConv, $afterId);
                $this->supportChatService->markMessagesAsRead($currentConv, 'admin');

                $newMessages = $rawMessages->map(fn($msg) => [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_type === 'user' ? $currentConv->user->name : ($msg->sender_type === 'admin' ? ($msg->sender ? $msg->sender->name : 'Bon Trouver Support') : 'System'),
                    'sender_avatar' => $msg->sender_type === 'user' ? ($currentConv->user->avatar_url ?: asset('images/default-avatar.svg')) : ($msg->sender?->avatar_url ?: asset('images/support-avatar.svg')),
                    'message' => $msg->message,
                    'attachments' => $msg->attachment_urls,
                    'attachment_files' => $msg->attachment_files,
                    'created_at_human' => $msg->created_at ? $msg->created_at->format('M d, g:i A') : '',
                    'created_at_time' => $msg->created_at ? $msg->created_at->format('g:i A') : '',
                    'is_sender' => $msg->sender_type === 'admin' && $msg->sender_id === Auth::id(),
                    'is_admin' => $msg->sender_type === 'admin',
                    'is_system' => $msg->sender_type === 'system',
                ]);
            }
        }

        $convList = $conversations->getCollection()->map(function ($conv) use ($selectedConvId) {
            $user = $conv->user;
            $latestMsg = $conv->latestMessage;
            $unreadCount = $conv->unreadMessagesForAdmin()->count();

            return [
                'id' => $conv->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_avatar' => $user->avatar_url ?: asset('images/default-avatar.svg'),
                'is_verified' => (bool) $user->is_verified,
                'unread_count' => $unreadCount,
                'last_message_at' => $conv->last_message_at ? $conv->last_message_at->toIso8601String() : null,
                'last_message_time_human' => $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '',
                'latest_message' => $latestMsg ? $latestMsg->message : '',
                'latest_message_sender_type' => $latestMsg ? $latestMsg->sender_type : '',
                'status' => $conv->status,
                'is_selected' => $selectedConvId === $conv->id,
                'url' => route('admin.support.index', ['conversation_id' => $conv->id]),
            ];
        });

        return response()->json([
            'status' => 'success',
            'stats' => [
                'unread_messages' => $unreadMessagesCount,
                'unread_conversations' => $unreadConversationsCount,
                'total' => SupportConversation::count(),
            ],
            'conversations' => $convList,
            'new_messages' => $newMessages,
        ]);
    }
}
