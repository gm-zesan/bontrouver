<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CompanionshipRequest;
use App\Models\CompanionshipAttendee;
use App\Services\CompanionshipService;
use Illuminate\Http\Request;

class MeetupController extends Controller
{
    /**
     * Community Meetups Dashboard for the user.
     */
    public function index(Request $request)
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
     * Update attendee status for a hosted meetup.
     */
    public function updateAttendeeStatus(Request $request, $meetupId, $attendeeId, CompanionshipService $service)
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
}
