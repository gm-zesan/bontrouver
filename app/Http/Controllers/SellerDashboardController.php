<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanionshipRequest;
use App\Models\CompanionshipAttendee;
use App\Models\Favorite;

class SellerDashboardController extends Controller
{
    /**
     * Redirect legacy dashboard route to Profile.
     */
    public function index(Request $request)
    {
        return redirect()->route('profile.edit');
    }

    /**
     * Display the My Listings / Manage Ads page.
     */
    public function myListings(Request $request)
    {
        $user = Auth::user();
        $categories = CategoryService::getAll();


        // Sample comprehensive listings dataset for My Listings dashboard

        $dbListings = $user->listings()
            ->with(['category', 'primaryImage'])
            ->withCount(['favorites as saves', 'conversations as messages'])
            ->latest()
            ->get();

        $allListings = $dbListings->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'price' => '$' . number_format($item->price, 2),
                'category' => $item->category->name ?? 'Uncategorized',
                'location' => $item->city . ', ' . $item->province,
                'status' => $item->status,
                'posted_at' => $item->created_at->diffForHumans(),
                'created_at' => $item->created_at->format('Y-m-d'),
                'views' => $item->views_count ?? 0,
                'saves' => $item->saves ?? 0,
                'messages' => $item->messages ?? 0,
                'image' => $item->primaryImage->image_path ?? asset('images/placeholder.jpg'),
                'featured' => $item->is_featured,
                'draft_progress' => 100,
            ];
        })->toArray();

        // Tab counts computation
        $counts = [
            'all' => count($allListings),
            'active' => count(array_filter($allListings, fn($item) => $item['status'] === 'active' || $item['status'] === 'attention')),
            'drafts' => count(array_filter($allListings, fn($item) => $item['status'] === 'draft')),
            'sold' => count(array_filter($allListings, fn($item) => $item['status'] === 'sold')),
            'expired' => count(array_filter($allListings, fn($item) => $item['status'] === 'expired')),
            'paused' => count(array_filter($allListings, fn($item) => $item['status'] === 'paused')),
        ];

        // Overall Performance Summary
        $stats = [
            'active_count' => $counts['active'],
            'drafts_count' => $counts['drafts'],
            'sold_count' => $counts['sold'],
            'total_views' => array_sum(array_column($allListings, 'views')),
            'total_saves' => array_sum(array_column($allListings, 'saves')),
            'total_messages' => array_sum(array_column($allListings, 'messages')),
        ];

        // Filter status from URL if present
        $currentStatus = $request->query('status', 'all');
        $currentCategory = $request->query('category', 'all');
        $currentSort = $request->query('sort', 'newest');
        $searchQuery = $request->query('q', '');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'listings' => $allListings,
                'counts' => $counts,
                'stats' => $stats,
            ]);
        }

        return view('frontend.account.my-listings', [
            'user' => $user,
            'categories' => $categories,
            'listings' => $allListings,
            'counts' => $counts,
            'stats' => $stats,
            'currentStatus' => $currentStatus,
            'currentCategory' => $currentCategory,
            'currentSort' => $currentSort,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Update listing status (e.g. mark as sold, pause, relist, renew).
     */
    public function updateStatus(Request $request, $id)
    {
        $status = $request->input('status');
        $validStatuses = ['active', 'sold', 'paused', 'renewed', 'draft', 'deleted'];

        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status transition requested.'
            ], 422);
        }

        $messages = [
            'sold' => 'Listing marked as sold successfully.',
            'paused' => 'Listing paused and temporarily hidden from search.',
            'active' => 'Listing activated and visible to buyers.',
            'renewed' => 'Listing renewed successfully for 30 days.',
            'deleted' => 'Listing removed permanently.',
        ];

        return response()->json([
            'success' => true,
            'status' => $status === 'renewed' ? 'active' : $status,
            'message' => $messages[$status] ?? 'Listing updated successfully.'
        ]);
    }

    /**
     * 1. Display Saved Favorites.
     */
    public function favorites(Request $request)
    {
        $user = Auth::user();
        $categories = CategoryService::getAll();

        $stats = $this->getDashboardStats($user);

        $dbFavorites = $user->favorites()->with(['listing.category', 'listing.primaryImage', 'listing.user'])->get();

        $allFavorites = $dbFavorites->map(function ($fav) {
            $listing = $fav->listing;
            if (!$listing)
                return null;
            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'price' => '$' . number_format($listing->price, 2),
                'price_num' => $listing->price,
                'category' => $listing->category->name ?? 'Uncategorized',
                'location' => $listing->city . ', ' . $listing->province,
                'posted_at' => $listing->created_at->diffForHumans(),
                'views' => $listing->views_count ?? 0,
                'seller_name' => $listing->user->name ?? 'Unknown',
                'seller_verified' => $listing->user->is_verified ?? false,
                'image' => $listing->primaryImage->image_path ?? asset('images/placeholder.jpg'),
                'status' => $listing->status,
                'is_featured' => $listing->is_featured,
            ];
        })->filter()->toArray();

        $currentCategory = $request->query('category', 'all');
        $currentSort = $request->query('sort', 'newest');
        $searchQuery = $request->query('q', '');

        return view('frontend.account.favorites', [
            'user' => $user,
            'categories' => $categories,
            'favorites' => $allFavorites,
            'stats' => $stats,
            'currentCategory' => $currentCategory,
            'currentSort' => $currentSort,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Remove item from Favorites.
     */
    public function removeFavorite(Request $request, $id)
    {
        $user = Auth::user();
        Favorite::where('user_id', $user->id)
            ->where('listing_id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'id' => (int) $id,
            'message' => 'Listing removed from your favorites.'
        ]);
    }

    /**
     * Toggle item in Favorites.
     */
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|integer|exists:listings,id'
        ]);

        $user = Auth::user();
        $listingId = $request->input('listing_id');

        $favorite = Favorite::where('user_id', $user->id)
            ->where('listing_id', $listingId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $status = 'removed';
            $message = 'Listing removed from saved items.';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'listing_id' => $listingId
            ]);
            $status = 'added';
            $message = 'Listing saved to your favorites!';
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * 2. Display Messages & Inbox Conversations.
     */
    public function messages(Request $request)
    {
        $user = Auth::user();

        // Handle starting a new conversation
        if ($request->query('c') === 'new' && $request->has('user')) {
            $sellerId = (int) $request->query('user');
            if ($sellerId !== $user->id) {
                $listingId = $request->query('listing') ? (int) $request->query('listing') : null;
                $newConv = MessageService::getOrCreateConversation($user->id, $sellerId, $listingId);
                return redirect()->route('messages.index', ['c' => $newConv->id]);
            }
        }

        $categories = CategoryService::getAll();
        $stats = $this->getDashboardStats($user);

        $dbConversations = MessageService::getUserConversations($user->id);

        $conversations = $dbConversations->map(function ($conv) use ($user) {
            $otherUser = $conv->buyer_id == $user->id ? $conv->seller : $conv->buyer;
            $listing = $conv->listing;

            $lastMessage = $conv->messages->last();
            $unreadCount = $conv->messages->where('sender_id', '!=', $user->id)->whereNull('read_at')->count();

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
                'last_message' => $lastMessage->body ?? 'No messages yet.',
                'last_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                'unread' => $unreadCount > 0,
                'unread_count' => $unreadCount,
                'messages' => $conv->messages->map(function ($msg) use ($user) {
                    return [
                        'id' => $msg->id,
                        'sender' => $msg->sender_id == $user->id ? 'me' : 'them',
                        'text' => $msg->body,
                        'time' => $msg->created_at->format('M d, g:i A'),
                    ];
                })->values()->toArray(),
            ];
        })->toArray();

        $activeConversationId = (int) $request->query('c', $conversations[0]['id'] ?? 0);
        $activeConversation = collect($conversations)->firstWhere('id', $activeConversationId) ?? ($conversations[0] ?? null);

        if ($activeConversation) {
            MessageService::markAsRead($activeConversation['id'], $user->id);
            // reset unread in UI data since we just read it
            $activeConversation['unread'] = false;
            $activeConversation['unread_count'] = 0;
            // modify original collection item so it reflects in the sidebar list too
            $key = collect($conversations)->search(fn($c) => $c['id'] == $activeConversation['id']);
            if ($key !== false) {
                $conversations[$key]['unread'] = false;
                $conversations[$key]['unread_count'] = 0;
            }
        }

        return view('frontend.account.messages', [
            'user' => $user,
            'categories' => $categories,
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'stats' => $stats,
        ]);
    }

    /**
     * Send new message in conversation.
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $text = $request->input('message');
        if (empty(trim($text))) {
            return response()->json(['success' => false, 'message' => 'Message cannot be empty.'], 422);
        }

        $user = Auth::user();

        $msg = MessageService::sendMessage($conversationId, $user->id, $text);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'sender' => 'me',
                'text' => e($msg->body),
                'time' => $msg->created_at->format('M d, g:i A'),
            ]
        ]);
    }

    /**
     * Initiate a new conversation and send the first message.
     */
    public function initiateMessage(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|integer',
            'listing_id' => 'nullable|integer',
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        $sellerId = $request->input('seller_id');
        $listingId = $request->input('listing_id');
        $text = $request->input('message');

        if ($sellerId === $user->id) {
            return response()->json(['success' => false, 'message' => 'You cannot message yourself.'], 422);
        }

        $conv = MessageService::getOrCreateConversation($user->id, $sellerId, $listingId);
        MessageService::sendMessage($conv->id, $user->id, $text);

        return response()->json([
            'success' => true,
            'redirect_url' => route('messages.index', ['c' => $conv->id])
        ]);
    }

    /**
     * 3. Display Notifications Center.
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();
        $categories = CategoryService::getAll();

        $stats = $this->getDashboardStats($user);

        $notifications = [];

        return view('frontend.account.notifications', [
            'user' => $user,
            'categories' => $categories,
            'notifications' => $notifications,
            'stats' => $stats,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.'
        ]);
    }

    /**
     * Mark single notification as read.
     */
    public function markNotificationRead(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'id' => (int) $id,
            'message' => 'Notification marked as read.'
        ]);
    }

    /**
     * 4. Display User Public & Account Profile.
     */
    public function profileView(Request $request)
    {
        $id = $request->query('id');
        if ($id && $id != Auth::id()) {
            $user = \App\Models\User::findOrFail($id);
        } else {
            $user = Auth::user();
        }
        $categories = CategoryService::getAll();

        $stats = $this->getDashboardStats($user);

        $dbUserListings = $user->listings()->with(['category', 'primaryImage'])->withCount('favorites as saves')->latest()->take(6)->get();
        $userListings = $dbUserListings->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'price' => '$' . number_format($item->price, 2),
                'category' => $item->category->name ?? 'Uncategorized',
                'location' => $item->city . ', ' . $item->province,
                'posted_at' => $item->created_at->diffForHumans(),
                'views' => $item->views_count ?? 0,
                'saves' => $item->saves ?? 0,
                'image' => $item->primaryImage->image_path ?? asset('images/placeholder.jpg'),
                'status' => $item->status,
                'featured' => $item->is_featured,
            ];
        })->toArray();

        $dbReviews = $user->reviewsReceived()->with(['reviewer', 'listing'])->latest()->take(5)->get();
        $reviews = $dbReviews->map(function ($rev) {
            return [
                'author' => $rev->reviewer->name ?? 'Unknown',
                'avatar' => $rev->reviewer->avatar ?? asset('images/avatar-placeholder.png'),
                'rating' => $rev->rating,
                'date' => $rev->created_at->diffForHumans(),
                'comment' => $rev->comment,
                'item_title' => $rev->listing->title ?? 'Deleted Item'
            ];
        })->toArray();

        $dbMeetups = CompanionshipRequest::with(['cityRelation', 'attendees.user'])
            ->where('user_id', $user->id)
            ->orderBy('meetup_date_time', 'desc')
            ->take(4)
            ->get();

        return view('frontend.account.profile', [
            'user' => $user,
            'categories' => $categories,
            'stats' => $stats,
            'userListings' => $userListings,
            'reviews' => $reviews,
            'hostedMeetups' => $dbMeetups,
        ]);
    }

    /**
     * 5. Display Account Settings & Preferences.
     */
    public function settings(Request $request)
    {
        $user = Auth::user();
        $categories = CategoryService::getAll();

        $stats = $this->getDashboardStats($user);

        return view('frontend.account.settings', [
            'user' => $user,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    /**
     * Save Account Settings updates.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }
        if ($request->has('location')) {
            $user->location = $request->input('location');
        }
        if ($request->has('bio')) {
            $user->bio = $request->input('bio');
        }

        $user->save();

        return redirect()->back()->with('status', 'Settings updated successfully.');
    }

    /**
     * Community Meetups Dashboard
     */
    public function myMeetups(Request $request)
    {
        $user = auth()->user();

        $hostedMeetups = CompanionshipRequest::with(['cityRelation', 'attendees.user'])
            ->where('user_id', $user->id)
            ->orderBy('meetup_date_time', 'desc')
            ->get();

        $joinedMeetups = CompanionshipRequest::with(['cityRelation', 'user'])
            ->whereHas('attendees', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('meetup_date_time', 'desc')
            ->get();

        return view('frontend.account.meetups', compact('hostedMeetups', 'joinedMeetups'));
    }

    /**
     * Update attendee status for a hosted meetup
     */
    public function updateAttendeeStatus(Request $request, $meetupId, $attendeeId, \App\Services\CompanionshipService $service)
    {
        try {
            $meetup = CompanionshipRequest::where('user_id', auth()->id())->findOrFail($meetupId);
            $attendee = CompanionshipAttendee::where('companionship_request_id', $meetupId)->findOrFail($attendeeId);

            $status = $request->input('status');
            $service->updateAttendeeStatus($meetup, $attendee, $status);

            return back()->with('status', 'Attendee status updated to ' . $status);
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }
    private function getDashboardStats($user)
    {
        return [
            'active_listings' => $user->listings()->where('status', 'active')->count(),
            'saved_favorites_count' => $user->favorites()->count(),
            'unread_messages_count' => \App\Models\Message::whereHas('conversation', function ($query) use ($user) {
                $query->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })->where('sender_id', '!=', $user->id)->whereNull('read_at')->count(),
            'unread_notifications_count' => 0, // Pending notifications DB
        ];
    }
}
