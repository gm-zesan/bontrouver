<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $allListings = [
            [
                'id' => 101,
                'title' => '2024 Toyota RAV4 Hybrid XSE AWD (Tech Package)',
                'price' => '$41,500',
                'category' => 'Cars & Vehicles',
                'location' => 'Toronto, ON',
                'status' => 'active',
                'posted_at' => '2 days ago',
                'created_at' => '2026-09-12',
                'views' => 248,
                'saves' => 14,
                'messages' => 8,
                'image' => asset('images/hero/toyota-rav4.jpg'),
                'featured' => true,
                'draft_progress' => 100,
            ],
            [
                'id' => 102,
                'title' => 'Apple iPhone 16 Pro Max 256GB Natural Titanium (Sealed)',
                'price' => '$1,250',
                'category' => 'Electronics',
                'location' => 'Mississauga, ON',
                'status' => 'active',
                'posted_at' => '1 day ago',
                'created_at' => '2026-09-13',
                'views' => 186,
                'saves' => 21,
                'messages' => 12,
                'image' => asset('images/hero/iphone-16-pro.jpg'),
                'featured' => false,
                'draft_progress' => 100,
            ],
            [
                'id' => 103,
                'title' => 'Herman Miller Embody Ergonomic Chair (Black/Sync Fabric)',
                'price' => '$1,100',
                'category' => 'Furniture & Home',
                'location' => 'Downtown Toronto, ON',
                'status' => 'active',
                'posted_at' => '4 days ago',
                'created_at' => '2026-09-10',
                'views' => 312,
                'saves' => 33,
                'messages' => 9,
                'image' => asset('images/hero/herman-miller-embody.jpg'),
                'featured' => false,
                'draft_progress' => 100,
            ],
            [
                'id' => 104,
                'title' => 'Trek Domane SL 6 Carbon Disc Road Bike (56cm - Shimano 105 Di2)',
                'price' => '$2,200',
                'category' => 'Sports & Outdoors',
                'location' => 'Oakville, ON',
                'status' => 'paused',
                'posted_at' => '1 week ago',
                'created_at' => '2026-09-07',
                'views' => 142,
                'saves' => 9,
                'messages' => 4,
                'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=600&q=80',
                'featured' => false,
                'draft_progress' => 100,
            ],
            [
                'id' => 105,
                'title' => 'Sony PlayStation 5 Slim Digital Console (New in Box)',
                'price' => '$520',
                'category' => 'Electronics',
                'location' => 'North York, ON',
                'status' => 'draft',
                'posted_at' => 'Draft saved 2 hours ago',
                'created_at' => '2026-09-14',
                'views' => 0,
                'saves' => 0,
                'messages' => 0,
                'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=600&q=80',
                'featured' => false,
                'draft_progress' => 80,
                'missing_fields' => 'Contact Preferences & Delivery Options',
            ],
            [
                'id' => 106,
                'title' => 'Mid-Century Modern Teak Coffee Table with Storage Shelf',
                'price' => '$340',
                'category' => 'Furniture & Home',
                'location' => 'Etobicoke, ON',
                'status' => 'draft',
                'posted_at' => 'Draft saved yesterday',
                'created_at' => '2026-09-13',
                'views' => 0,
                'saves' => 0,
                'messages' => 0,
                'image' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?auto=format&fit=crop&w=600&q=80',
                'featured' => false,
                'draft_progress' => 60,
                'missing_fields' => 'Photos (min 2 recommended) & Price',
            ],
            [
                'id' => 107,
                'title' => 'Apple MacBook Pro 14" M3 Pro 18GB 512GB Space Black',
                'price' => '$1,850',
                'category' => 'Electronics',
                'location' => 'Toronto, ON',
                'status' => 'sold',
                'posted_at' => 'Sold on Sep 08, 2026',
                'created_at' => '2026-08-28',
                'views' => 420,
                'saves' => 28,
                'messages' => 15,
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=600&q=80',
                'featured' => false,
                'draft_progress' => 100,
            ],
            [
                'id' => 108,
                'title' => '2019 Honda Civic EX Sedan (Single Owner, Clean Carfax)',
                'price' => '$17,900',
                'category' => 'Cars & Vehicles',
                'location' => 'Markham, ON',
                'status' => 'sold',
                'posted_at' => 'Sold on Sep 02, 2026',
                'created_at' => '2026-08-15',
                'views' => 690,
                'saves' => 45,
                'messages' => 23,
                'image' => 'https://images.unsplash.com/photo-1590362891991-f776e747a588?auto=format&fit=crop&w=600&q=80',
                'featured' => false,
                'draft_progress' => 100,
            ],
            [
                'id' => 109,
                'title' => 'DeWalt 20V MAX Cordless Drill Combo Kit (2 Batteries + Charger)',
                'price' => '$160',
                'category' => 'Tools & DIY',
                'location' => 'Scarborough, ON',
                'status' => 'expired',
                'posted_at' => 'Expired 3 days ago',
                'created_at' => '2026-08-11',
                'views' => 95,
                'saves' => 6,
                'messages' => 2,
                'image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=600&q=80',
                'featured' => false,
                'draft_progress' => 100,
            ],
            [
                'id' => 110,
                'title' => 'Canon EOS R6 Mark II Mirrorless Camera Body',
                'price' => '$2,450',
                'category' => 'Electronics',
                'location' => 'Toronto, ON',
                'status' => 'attention',
                'attention_reason' => 'Your listing was temporarily hidden because additional serial verification is required.',
                'posted_at' => 'Needs review',
                'created_at' => '2026-09-11',
                'views' => 84,
                'saves' => 5,
                'messages' => 1,
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80',
                'featured' => false,
                'draft_progress' => 100,
            ],
        ];

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

        $stats = [
            'active_listings' => $user->active_ads_count ?? 3,
            'saved_favorites_count' => 6,
            'unread_messages_count' => 2,
            'unread_notifications_count' => 3,
        ];

        $allFavorites = [
            [
                'id' => 101,
                'title' => '2024 Toyota RAV4 Hybrid XSE AWD (Panoramic Sunroof)',
                'price' => '$41,500',
                'price_num' => 41500,
                'category' => 'Cars & Vehicles',
                'location' => 'Toronto, ON',
                'posted_at' => '2 days ago',
                'views' => 248,
                'seller_name' => 'Metro Auto Gallery',
                'seller_verified' => true,
                'image' => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'id' => 102,
                'title' => 'Apple iPhone 16 Pro Max 256GB Desert Titanium (Brand New Sealed)',
                'price' => '$1,250',
                'price_num' => 1250,
                'category' => 'Electronics',
                'location' => 'Mississauga, ON',
                'posted_at' => '1 day ago',
                'views' => 186,
                'seller_name' => 'TechHub Canada',
                'seller_verified' => true,
                'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'is_featured' => false,
            ],
            [
                'id' => 103,
                'title' => 'Herman Miller Aeron Ergonomic Office Chair Size B',
                'price' => '$780',
                'price_num' => 780,
                'category' => 'Home & Furniture',
                'location' => 'Downtown Toronto, ON',
                'posted_at' => '3 hours ago',
                'views' => 64,
                'seller_name' => 'Alexandre Dubois',
                'seller_verified' => true,
                'image' => asset('images/hero/herman-miller-embody.jpg'),
                'status' => 'active',
                'is_featured' => false,
            ],
            [
                'id' => 105,
                'title' => 'Sony PlayStation 5 Disc Edition + 2 DualSense Controllers',
                'price' => '$490',
                'price_num' => 490,
                'category' => 'Electronics',
                'location' => 'Scarborough, ON',
                'posted_at' => '5 days ago',
                'views' => 312,
                'seller_name' => 'David Kim',
                'seller_verified' => false,
                'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'is_featured' => false,
            ],
            [
                'id' => 107,
                'title' => 'Vintage Mid-Century Walnut Dining Table & 6 Chairs',
                'price' => '$850',
                'price_num' => 850,
                'category' => 'Home & Furniture',
                'location' => 'Etobicoke, ON',
                'posted_at' => '1 week ago',
                'views' => 140,
                'seller_name' => 'Elena Rostova',
                'seller_verified' => true,
                'image' => 'https://images.unsplash.com/photo-1617806118233-18e1de247200?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'is_featured' => false,
            ],
            [
                'id' => 108,
                'title' => '2023 Specialized Tarmac SL7 Comp Road Bike (54cm)',
                'price' => '$3,200',
                'price_num' => 3200,
                'category' => 'Sports & Outdoors',
                'location' => 'Vancouver, BC',
                'posted_at' => '4 days ago',
                'views' => 95,
                'seller_name' => 'Ryan Miller',
                'seller_verified' => true,
                'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'is_featured' => true,
            ],
        ];

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
        return response()->json([
            'success' => true,
            'id' => (int) $id,
            'message' => 'Listing removed from your favorites.'
        ]);
    }

    /**
     * 2. Display Messages & Inbox Conversations.
     */
    public function messages(Request $request)
    {
        $user = Auth::user();
        $categories = CategoryService::getAll();

        $stats = [
            'active_listings' => $user->active_ads_count ?? 3,
            'saved_favorites_count' => 6,
            'unread_messages_count' => 2,
            'unread_notifications_count' => 3,
        ];

        $conversations = [
            [
                'id' => 1,
                'user' => [
                    'name' => 'Ahmed Rahman',
                    'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
                    'online' => true,
                    'location' => 'Toronto, ON',
                    'verified' => true,
                    'rating' => 4.9,
                ],
                'listing' => [
                    'id' => 101,
                    'title' => '2024 Toyota RAV4 Hybrid XSE AWD',
                    'price' => '$41,500',
                    'image' => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=300&q=80',
                    'status' => 'Active',
                ],
                'last_message' => 'Is the price negotiable if I pay cash this weekend?',
                'last_time' => '10:45 AM',
                'unread' => true,
                'unread_count' => 1,
                'messages' => [
                    [
                        'id' => 101,
                        'sender' => 'them',
                        'text' => 'Hi Sarah! I saw your 2024 Toyota RAV4 Hybrid ad in Toronto. Is it still available?',
                        'time' => '10:30 AM',
                    ],
                    [
                        'id' => 102,
                        'sender' => 'me',
                        'text' => 'Hello Ahmed! Yes, it is still available. Clean title with only 8,200 km.',
                        'time' => '10:38 AM',
                    ],
                    [
                        'id' => 103,
                        'sender' => 'them',
                        'text' => 'Is the price negotiable if I pay cash this weekend?',
                        'time' => '10:45 AM',
                    ],
                ]
            ],
            [
                'id' => 2,
                'user' => [
                    'name' => 'Jessica Wong',
                    'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80',
                    'online' => false,
                    'location' => 'Mississauga, ON',
                    'verified' => true,
                    'rating' => 5.0,
                ],
                'listing' => [
                    'id' => 102,
                    'title' => 'Apple iPhone 16 Pro Max 256GB Desert Titanium',
                    'price' => '$1,250',
                    'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=300&q=80',
                    'status' => 'Active',
                ],
                'last_message' => 'Can we meet at Square One Shopping Mall today?',
                'last_time' => 'Yesterday',
                'unread' => true,
                'unread_count' => 1,
                'messages' => [
                    [
                        'id' => 201,
                        'sender' => 'them',
                        'text' => 'Hi, is the iPhone 16 Pro Max still sealed in the original Apple box?',
                        'time' => 'Yesterday 3:15 PM',
                    ],
                    [
                        'id' => 202,
                        'sender' => 'me',
                        'text' => 'Yes, 100% factory sealed with 1-year Apple Canada warranty.',
                        'time' => 'Yesterday 3:20 PM',
                    ],
                    [
                        'id' => 203,
                        'sender' => 'them',
                        'text' => 'Can we meet at Square One Shopping Mall today?',
                        'time' => 'Yesterday 4:00 PM',
                    ],
                ]
            ],
            [
                'id' => 3,
                'user' => [
                    'name' => 'Michael Chen',
                    'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
                    'online' => false,
                    'location' => 'Montreal, QC',
                    'verified' => true,
                    'rating' => 4.8,
                ],
                'listing' => [
                    'id' => 104,
                    'title' => 'MacBook Pro 16" M3 Max 36GB / 1TB',
                    'price' => '$3,150',
                    'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=300&q=80',
                    'status' => 'Sold',
                ],
                'last_message' => 'Thanks for the smooth pickup Sarah! Rating you 5 stars.',
                'last_time' => '3 days ago',
                'unread' => false,
                'unread_count' => 0,
                'messages' => [
                    [
                        'id' => 301,
                        'sender' => 'them',
                        'text' => 'I just arrived at the Starbucks on Saint-Laurent.',
                        'time' => 'Sept 11, 2:10 PM',
                    ],
                    [
                        'id' => 302,
                        'sender' => 'me',
                        'text' => 'Great, I am sitting by the window in the black jacket.',
                        'time' => 'Sept 11, 2:12 PM',
                    ],
                    [
                        'id' => 303,
                        'sender' => 'them',
                        'text' => 'Thanks for the smooth pickup Sarah! Rating you 5 stars.',
                        'time' => 'Sept 11, 3:45 PM',
                    ],
                ]
            ],
        ];

        $activeConversationId = (int) $request->query('c', $conversations[0]['id']);
        $activeConversation = collect($conversations)->firstWhere('id', $activeConversationId) ?? $conversations[0];

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

        return response()->json([
            'success' => true,
            'message' => [
                'id' => time(),
                'sender' => 'me',
                'text' => e($text),
                'time' => 'Just now',
            ]
        ]);
    }

    /**
     * 3. Display Notifications Center.
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();
        $categories = CategoryService::getAll();

        $stats = [
            'active_listings' => $user->active_ads_count ?? 3,
            'saved_favorites_count' => 6,
            'unread_messages_count' => 2,
            'unread_notifications_count' => 3,
        ];

        $notifications = [
            'today' => [
                [
                    'id' => 1,
                    'type' => 'message',
                    'icon' => 'bi-chat-left-dots-fill text-primary',
                    'bg' => 'bg-primary-subtle',
                    'title' => 'New message from Ahmed Rahman',
                    'body' => 'Ahmed sent a message about "2024 Toyota RAV4 Hybrid XSE AWD".',
                    'time' => '10:45 AM',
                    'read' => false,
                    'action_url' => url('/messages?c=1'),
                    'action_label' => 'Reply to Message',
                ],
                [
                    'id' => 2,
                    'type' => 'favorite',
                    'icon' => 'bi-heart-fill text-danger',
                    'bg' => 'bg-danger-subtle',
                    'title' => 'Someone saved your ad',
                    'body' => 'Your listing "Apple iPhone 16 Pro Max" was added to favorites by 3 new buyers.',
                    'time' => '8:20 AM',
                    'read' => false,
                    'action_url' => url('/my-listings'),
                    'action_label' => 'View Stats',
                ],
            ],
            'yesterday' => [
                [
                    'id' => 3,
                    'type' => 'price_drop',
                    'icon' => 'bi-arrow-down-circle-fill text-success',
                    'bg' => 'bg-success-subtle',
                    'title' => 'Price Drop Alert on Saved Item',
                    'body' => '"Sony PlayStation 5 Disc Edition" dropped from $530 to $490.',
                    'time' => 'Yesterday at 5:30 PM',
                    'read' => false,
                    'action_url' => url('/listing/105'),
                    'action_label' => 'View Listing',
                ],
                [
                    'id' => 4,
                    'type' => 'listing_approved',
                    'icon' => 'bi-check-circle-fill text-success',
                    'bg' => 'bg-success-subtle',
                    'title' => 'Your listing is now live!',
                    'body' => '"2024 Toyota RAV4 Hybrid" has passed verification and is active across Canada.',
                    'time' => 'Yesterday at 11:15 AM',
                    'read' => true,
                    'action_url' => url('/listing/101'),
                    'action_label' => 'View Public Ad',
                ],
            ],
            'earlier' => [
                [
                    'id' => 5,
                    'type' => 'listing_expiry',
                    'icon' => 'bi-hourglass-bottom text-warning',
                    'bg' => 'bg-warning-subtle',
                    'title' => 'Listing expiring soon',
                    'body' => 'Your listing "Canon EOS R6 Mark II" expires in 4 days. Renew now to maintain search rankings.',
                    'time' => 'Sep 11, 2026',
                    'read' => true,
                    'action_url' => url('/my-listings?status=attention'),
                    'action_label' => 'Renew Ad',
                ],
                [
                    'id' => 6,
                    'type' => 'security',
                    'icon' => 'bi-shield-check text-info',
                    'bg' => 'bg-info-subtle',
                    'title' => 'New login from Chrome on macOS',
                    'body' => 'Your account was accessed from Montreal, QC. If this was not you, please reset your password.',
                    'time' => 'Sep 10, 2026',
                    'read' => true,
                    'action_url' => url('/settings'),
                    'action_label' => 'Security Settings',
                ],
            ]
        ];

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
        $user = Auth::user();
        $categories = CategoryService::getAll();

        $stats = [
            'active_listings' => $user->active_ads_count ?? 3,
            'saved_favorites_count' => 6,
            'unread_messages_count' => 2,
            'unread_notifications_count' => 3,
        ];

        $userListings = [
            [
                'id' => 101,
                'title' => '2024 Toyota RAV4 Hybrid XSE AWD (Panoramic Sunroof)',
                'price' => '$41,500',
                'category' => 'Cars & Vehicles',
                'location' => 'Toronto, ON',
                'posted_at' => '2 days ago',
                'views' => 248,
                'saves' => 14,
                'image' => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'featured' => true,
            ],
            [
                'id' => 102,
                'title' => 'Apple iPhone 16 Pro Max 256GB Desert Titanium',
                'price' => '$1,250',
                'category' => 'Electronics',
                'location' => 'Mississauga, ON',
                'posted_at' => '1 day ago',
                'views' => 186,
                'saves' => 21,
                'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'featured' => false,
            ],
            [
                'id' => 103,
                'title' => 'Herman Miller Aeron Ergonomic Office Chair Size B',
                'price' => '$780',
                'category' => 'Home & Furniture',
                'location' => 'Downtown Toronto, ON',
                'posted_at' => '3 hours ago',
                'views' => 64,
                'saves' => 9,
                'image' => asset('images/hero/herman-miller-embody.jpg'),
                'status' => 'active',
                'featured' => false,
            ],
        ];

        $reviews = [
            [
                'author' => 'David Kim',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80',
                'rating' => 5,
                'date' => '2 weeks ago',
                'comment' => 'Fantastic seller! The MacBook Pro was in mint condition exactly as described. Prompt communication.',
                'item_title' => 'MacBook Pro 16" M3 Max'
            ],
            [
                'author' => 'Marc Bouchard',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=120&q=80',
                'rating' => 5,
                'date' => '1 month ago',
                'comment' => 'Smooth transaction, very honest seller. Met in a secure bank lobby in Toronto.',
                'item_title' => 'Sony Alpha Camera Body'
            ],
        ];

        return view('frontend.account.profile', [
            'user' => $user,
            'categories' => $categories,
            'stats' => $stats,
            'userListings' => $userListings,
            'reviews' => $reviews,
        ]);
    }

    /**
     * 5. Display Account Settings & Preferences.
     */
    public function settings(Request $request)
    {
        $user = Auth::user();
        $categories = CategoryService::getAll();

        $stats = [
            'active_listings' => $user->active_ads_count ?? 3,
            'saved_favorites_count' => 6,
            'unread_messages_count' => 2,
            'unread_notifications_count' => 3,
        ];

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
}
