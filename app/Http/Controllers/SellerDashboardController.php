<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerDashboardController extends Controller
{
    /**
     * Display the seller / user dashboard panel.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $categories = CategoryService::getAll();

        // 1. Dynamic User Marketplace Overview Statistics (Both Buyer & Seller Activity)
        $stats = [
            'active_listings' => $user->active_ads_count ?? 3,
            'sold_listings' => 2,
            'total_views' => 428,
            'total_saves' => 28,
            'saved_favorites_count' => 6,
            'unread_messages_count' => 2,
            'unread_notifications_count' => 3,
        ];

        // 2. Chronological User Activity Feed (Interleaved Buyer & Seller Actions)
        $recentActivities = [
            [
                'id' => 1,
                'type' => 'favorite',
                'icon' => 'bi-heart-fill',
                'color' => 'text-danger',
                'bg' => 'bg-danger-subtle',
                'title' => 'You saved "Apple MacBook Air 15" M3 (512GB Space Grey)"',
                'time' => '25 mins ago',
                'link' => url('/listing/2'),
            ],
            [
                'id' => 2,
                'type' => 'message',
                'icon' => 'bi-chat-left-dots-fill',
                'color' => 'text-primary',
                'bg' => 'bg-primary-subtle',
                'title' => 'New message from Ahmed Rahman regarding "2024 Toyota RAV4 Hybrid"',
                'time' => '1 hour ago',
                'link' => url('/messages'),
            ],
            [
                'id' => 3,
                'type' => 'listing',
                'icon' => 'bi-check2-circle',
                'color' => 'text-success',
                'bg' => 'bg-success-subtle',
                'title' => 'Your listing "Herman Miller Embody Chair" was published and is active',
                'time' => 'Yesterday at 4:15 PM',
                'link' => url('/listing/3'),
            ],
            [
                'id' => 4,
                'type' => 'views',
                'icon' => 'bi-eye-fill',
                'color' => 'text-info',
                'bg' => 'bg-info-subtle',
                'title' => 'Your listings gained 48 new buyer views in Toronto, ON',
                'time' => 'Yesterday',
                'link' => url('/my-listings'),
            ],
            [
                'id' => 5,
                'type' => 'alert',
                'icon' => 'bi-bell-fill',
                'color' => 'text-warning',
                'bg' => 'bg-warning-subtle',
                'title' => 'Your ad "Sony PlayStation 5 Slim" expires in 4 days. Renew to stay on top.',
                'time' => '2 days ago',
                'link' => url('/my-listings?status=active'),
            ],
        ];

        // 3. User's Recent Listings (Seller Activity Preview)
        $recentListings = [
            [
                'id' => 101,
                'title' => '2024 Toyota RAV4 Hybrid XSE AWD (Tech Package)',
                'price' => '$41,500',
                'category' => 'Cars & Vehicles',
                'location' => 'Toronto, ON',
                'status' => 'active',
                'posted_at' => '2 days ago',
                'views' => 248,
                'saves' => 14,
                'messages' => 8,
                'image' => '/images/hero/toyota-rav4.jpg',
            ],
            [
                'id' => 102,
                'title' => 'Apple iPhone 16 Pro Max 256GB Natural Titanium',
                'price' => '$1,250',
                'category' => 'Electronics',
                'location' => 'Mississauga, ON',
                'status' => 'active',
                'posted_at' => '1 day ago',
                'views' => 186,
                'saves' => 21,
                'messages' => 12,
                'image' => '/images/hero/iphone-16-pro.jpg',
            ],
            [
                'id' => 103,
                'title' => 'Herman Miller Embody Ergonomic Chair (Black/Sync)',
                'price' => '$1,100',
                'category' => 'Furniture & Home',
                'location' => 'Downtown Toronto, ON',
                'status' => 'active',
                'posted_at' => '4 days ago',
                'views' => 312,
                'saves' => 33,
                'messages' => 9,
                'image' => '/images/hero/herman-miller-embody.jpg',
            ],
        ];

        // 4. Recently Saved / Favorited Listings (Buyer Activity Preview)
        $recentlySaved = [
            [
                'id' => 201,
                'title' => 'Apple MacBook Air 15" M3 16GB 512GB Midnight (2024)',
                'price' => '$1,450',
                'location' => 'Toronto, ON',
                'category' => 'Computers & Tablets',
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
                'seller_name' => 'David Miller',
                'saved_at' => '25 mins ago',
                'url' => url('/listing/1'),
            ],
            [
                'id' => 202,
                'title' => 'West Elm Modern Velvet 3-Seater Sofa (Olive Green)',
                'price' => '$680',
                'location' => 'North York, ON',
                'category' => 'Furniture',
                'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=800&q=80',
                'seller_name' => 'Sarah Jenkins',
                'saved_at' => 'Yesterday',
                'url' => url('/listing/2'),
            ],
            [
                'id' => 203,
                'title' => 'Specialized Sirrus X 4.0 Gravel Bike (Medium, Hydraulic Disc)',
                'price' => '$890',
                'location' => 'Etobicoke, ON',
                'category' => 'Bikes',
                'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=800&q=80',
                'seller_name' => 'Marc D.',
                'saved_at' => '3 days ago',
                'url' => url('/listing/3'),
            ],
        ];

        // 5. Recent Buyer & Seller Messages Preview
        $recentMessages = [
            [
                'id' => 1,
                'partner_name' => 'Ahmed Rahman',
                'partner_avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80',
                'listing_title' => '2024 Toyota RAV4 Hybrid XSE AWD',
                'last_message' => 'Hi! Is this still available? Can we arrange a test drive this Saturday?',
                'time' => '10 mins ago',
                'unread' => true,
            ],
            [
                'id' => 2,
                'partner_name' => 'Sarah Jenkins (Seller)',
                'partner_avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=120&q=80',
                'listing_title' => 'West Elm Velvet 3-Seater Sofa',
                'last_message' => 'Yes, pickup in North York is available tomorrow evening!',
                'time' => '2 hours ago',
                'unread' => true,
            ],
            [
                'id' => 3,
                'partner_name' => 'Elena Rostova',
                'partner_avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80',
                'listing_title' => 'Apple iPhone 16 Pro Max 256GB',
                'last_message' => 'Great condition, thanks for sending the receipt copy!',
                'time' => '1 day ago',
                'unread' => false,
            ],
        ];

        // 6. Notifications Preview
        $recentNotifications = [
            [
                'id' => 1,
                'type' => 'message',
                'title' => 'You received a new message from Ahmed Rahman',
                'time' => '10 mins ago',
                'read' => false,
                'icon' => 'bi-chat-left-dots-fill text-primary',
            ],
            [
                'id' => 2,
                'type' => 'approval',
                'title' => 'Your listing "Herman Miller Embody Chair" has been approved',
                'time' => 'Yesterday',
                'read' => false,
                'icon' => 'bi-check-circle-fill text-success',
            ],
            [
                'id' => 3,
                'type' => 'expiry',
                'title' => 'Your listing "Sony PS5 Slim" expires in 4 days',
                'time' => '2 days ago',
                'read' => true,
                'icon' => 'bi-hourglass-split text-warning',
            ],
        ];

        // 7. Profile Completion and Trust & Verification Status
        $profileCompletion = [
            'percentage' => 80,
            'is_complete' => false,
            'missing_items' => ['Add a backup phone number', 'Verify Canadian ID badge'],
        ];

        $verification = [
            'email_verified' => true,
            'phone_verified' => true,
            'identity_verified' => false,
        ];

        return view('frontend.dashboard', [
            'user' => $user,
            'categories' => $categories,
            'stats' => $stats,
            'activities' => $recentActivities,
            'recentListings' => $recentListings,
            'recentlySaved' => $recentlySaved,
            'recentMessages' => $recentMessages,
            'recentNotifications' => $recentNotifications,
            'profileCompletion' => $profileCompletion,
            'verification' => $verification,
            'activeNav' => 'dashboard',
        ]);
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

        return view('frontend.my-listings', [
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
     * Delete listing.
     */
    public function destroyListing(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'id' => (int) $id,
            'message' => 'Listing deleted successfully.'
        ]);
    }
}
