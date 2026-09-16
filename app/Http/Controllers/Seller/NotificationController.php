<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\SellerListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        private readonly SellerListingService $listingService
    ) {}

    /**
     * Display Notifications Center.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Pending notifications DB implementation
        $notifications = [];

        return view('frontend.account.notifications', [
            'user' => $user,
            'categories' => CategoryService::getAll(),
            'notifications' => $notifications,
            'stats' => $this->listingService->getDashboardHeaderStats($user),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.'
        ]);
    }

    /**
     * Mark single notification as read.
     */
    public function markRead(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'id' => (int) $id,
            'message' => 'Notification marked as read.'
        ]);
    }
}
