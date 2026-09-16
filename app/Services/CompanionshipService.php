<?php

namespace App\Services;

use App\Models\CompanionshipRequest;
use App\Models\CompanionshipAttendee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanionshipService
{
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
            throw new \Exception('You cannot join your own meetup.');
        }

        if ($request->status !== 'open') {
            throw new \Exception('This meetup is no longer open.');
        }

        $existing = CompanionshipAttendee::where('companionship_request_id', $request->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            throw new \Exception('You have already sent a request to join this meetup.');
        }

        return CompanionshipAttendee::create([
            'companionship_request_id' => $request->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
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
            if ($status === 'approved' && $request->headcount_limit) {
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
        });
    }
}
