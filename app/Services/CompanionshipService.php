<?php

namespace App\Services;

use App\Exceptions\CompanionshipException;
use App\Models\CompanionshipRequest;
use App\Models\CompanionshipAttendee;
use App\Models\User;
use App\Notifications\MeetupJoinRequested;
use App\Notifications\MeetupAttendeeStatusUpdated;
use App\Notifications\MeetupCancelled;
use Illuminate\Support\Facades\DB;
use App\Services\PointService;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanionshipService
{
    public function __construct(
        private readonly PointService $pointService
    ) {}
    /**
     * Get paginated active companionship requests.
     */
    public function getActiveRequests(array $filters = []): LengthAwarePaginator
    {
        $query = CompanionshipRequest::with(['user', 'cityRelation', 'attendees.user'])
            ->where('status', 'open')
            ->where('meetup_date_time', '>', now())
            ->orderBy('meetup_date_time', 'asc');

        if (!empty($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->paginate(15);
    }

    /**
     * Find a single meetup with all relationships needed for the detail view.
     */
    public function findForShow(int|string $id): CompanionshipRequest
    {
        return CompanionshipRequest::with(['user', 'cityRelation', 'attendees.user'])
            ->findOrFail($id);
    }

    /**
     * Create a new companionship request.
     */
    public function createRequest(array $data, User $user): CompanionshipRequest
    {
        $data['user_id'] = $user->id;
        $data['status'] = 'open';

        return CompanionshipRequest::create($data);
    }

    /**
     * User requests to join a meetup.
     */
    public function requestToJoin(CompanionshipRequest $request, User $user): CompanionshipAttendee
    {
        if ($request->user_id === $user->id) {
            throw CompanionshipException::cannotJoinOwn();
        }

        if ($request->status !== 'open') {
            throw CompanionshipException::notOpen();
        }

        $existing = CompanionshipAttendee::where('companionship_request_id', $request->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            throw CompanionshipException::alreadyRequested();
        }

        $attendee = CompanionshipAttendee::create([
            'companionship_request_id' => $request->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Notify meetup host of the new join request
        $request->user->notify(new MeetupJoinRequested($request, $user));

        return $attendee;
    }

    /**
     * Host updates the status of an attendee's request.
     */
    public function updateAttendeeStatus(CompanionshipRequest $request, CompanionshipAttendee $attendee, string $status): void
    {
        if (!in_array($status, ['approved', 'rejected'])) {
            throw new \InvalidArgumentException('Invalid status.');
        }

        DB::transaction(function () use ($request, $attendee, $status) {
            $attendee->update(['status' => $status]);

            // If approved, check if headcount limit is reached
            if ($status === 'approved') {
                // Award points to host for successfully organizing
                $this->pointService->awardForMeetupHost($request);
                $this->pointService->checkTierProgression($request->user);

                if ($request->headcount_limit) {
                    $approvedCount = CompanionshipAttendee::where('companionship_request_id', $request->id)
                        ->where('status', 'approved')
                        ->count();

                    if ($approvedCount >= $request->headcount_limit) {
                        $request->update(['status' => 'full']);
                        
                        // Optional: Reject all other pending requests
                        CompanionshipAttendee::where('companionship_request_id', $request->id)
                            ->where('status', 'pending')
                            ->update(['status' => 'rejected']);
                    }
                }
            }

            // Notify attendee of the decision
            $attendee->user->notify(new MeetupAttendeeStatusUpdated($request, $status));
        });
    }

    /**
     * Update an existing companionship request.
     */
    public function updateRequest(CompanionshipRequest $meetup, array $data): void
    {
        $meetup->update($data);

        // Check if expanding the headcount limit makes a full meetup open again
        if ($meetup->status === 'full' && $meetup->headcount_limit) {
            $approvedCount = CompanionshipAttendee::where('companionship_request_id', $meetup->id)
                ->where('status', 'approved')
                ->count();
                
            if ($approvedCount < $meetup->headcount_limit) {
                $meetup->update(['status' => 'open']);
            }
        }
    }

    /**
     * Host deletes/cancels a companionship request.
     */
    public function deleteRequest(CompanionshipRequest $meetup): void
    {
        DB::transaction(function () use ($meetup) {
            $attendees = CompanionshipAttendee::where('companionship_request_id', $meetup->id)
                ->with('user')
                ->get();

            foreach ($attendees as $att) {
                $att->user->notify(new MeetupCancelled($meetup));
            }

            // Cancel all attendees
            CompanionshipAttendee::where('companionship_request_id', $meetup->id)
                ->update(['status' => 'rejected']);
                
            $meetup->update(['status' => 'cancelled']);
            // If soft deletes is used, delete it. Otherwise keep it as cancelled.
            $meetup->delete();
        });
    }

    /**
     * Attendee withdraws their join request.
     */
    public function withdrawAttendance(CompanionshipAttendee $attendee): void
    {
        DB::transaction(function () use ($attendee) {
            $meetup = $attendee->companionshipRequest;
            $wasApproved = $attendee->status === 'approved';
            
            // Delete the attendance record entirely or mark it as withdrawn
            $attendee->delete();
            
            // If the user was approved and the meetup was full, open it up again
            if ($wasApproved && $meetup->status === 'full') {
                $meetup->update(['status' => 'open']);
            }
        });
    }
}
