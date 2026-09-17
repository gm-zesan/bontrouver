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

        // Fetch and group notifications from the database
        $dbNotifications = $user->notifications()->get();
        $notifications = [
            'today' => [],
            'yesterday' => [],
            'earlier' => [],
        ];

        foreach ($dbNotifications as $dbNotif) {
            $type = class_basename($dbNotif->type);

            $icon = 'bi-bell-fill';
            $title = 'New Notification';
            $body = '';
            $action_url = '#';
            $action_label = 'View';

            if ($type === 'SmartAlertMatched') {
                $icon = 'bi-search-heart text-success';
                $title = 'Smart Alert Match: ' . ($dbNotif->data['alert_name'] ?? '');
                $body = 'A new listing "' . ($dbNotif->data['listing_title'] ?? '') . '" matches your alert criteria.';
                $action_url = isset($dbNotif->data['listing_slug']) ? url('/listing/' . $dbNotif->data['listing_slug']) : '#';
                $action_label = 'View Listing';
            } elseif ($type === 'MeetupJoinRequested') {
                $icon = 'bi-person-plus-fill text-primary';
                $title = 'New Meetup RSVP Request';
                $body = ($dbNotif->data['requester_name'] ?? 'Someone') . ' requested to join your meetup "' . ($dbNotif->data['meetup_title'] ?? '') . '".';
                $action_url = route('meetups.my');
                $action_label = 'Manage Requests';
            } elseif ($type === 'MeetupAttendeeStatusUpdated') {
                $status = $dbNotif->data['status'] ?? 'updated';
                $isApproved = $status === 'approved';
                $icon = $isApproved ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger';
                $title = $isApproved ? 'Meetup Request Approved! 🎉' : 'Meetup Request Update';
                $body = $isApproved
                    ? 'Your request to join "' . ($dbNotif->data['meetup_title'] ?? '') . '" was approved by ' . ($dbNotif->data['host_name'] ?? 'the host') . '!'
                    : 'Your request to join "' . ($dbNotif->data['meetup_title'] ?? '') . '" was declined by the host.';
                $action_url = isset($dbNotif->data['meetup_id']) ? route('community.show', $dbNotif->data['meetup_id']) : route('meetups.my');
                $action_label = 'View Meetup';
            } elseif ($type === 'MeetupCancelled') {
                $icon = 'bi-calendar-x-fill text-warning';
                $title = 'Meetup Cancelled';
                $body = 'The meetup "' . ($dbNotif->data['meetup_title'] ?? '') . '" has been cancelled by the host.';
                $action_url = route('meetups.my');
                $action_label = 'View My Meetups';
            }

            $formatted = [
                'id' => $dbNotif->id,
                'read' => $dbNotif->read_at !== null,
                'icon' => $icon,
                'title' => $title,
                'body' => $body,
                'action_url' => $action_url,
                'action_label' => $action_label,
                'time' => $dbNotif->created_at->diffForHumans()
            ];

            if ($dbNotif->created_at->isToday()) {
                $notifications['today'][] = $formatted;
            } elseif ($dbNotif->created_at->isYesterday()) {
                $notifications['yesterday'][] = $formatted;
            } else {
                $notifications['earlier'][] = $formatted;
            }
        }

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
    public function markRead(Request $request)
    {
        $id = $request->input('id');
        $ids = $request->input('ids', []);
        $all = $request->boolean('all');

        if ($all) {
            Auth::user()->unreadNotifications->markAsRead();
        } elseif (!empty($ids)) {
            Auth::user()->notifications()->whereIn('id', $ids)->get()->markAsRead();
        } elseif ($id) {
            $notification = Auth::user()->notifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Action completed successfully.'
        ]);
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            Auth::user()->notifications()->whereIn('id', $ids)->delete();
        } elseif ($id) {
            $notification = Auth::user()->notifications()->find($id);
            if ($notification) {
                $notification->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Action completed successfully.'
        ]);
    }
}
