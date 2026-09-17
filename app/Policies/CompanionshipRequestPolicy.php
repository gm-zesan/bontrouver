<?php

namespace App\Policies;

use App\Models\CompanionshipRequest;
use App\Models\User;

class CompanionshipRequestPolicy
{
    /**
     * Any authenticated user can create a meetup.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only non-host, authenticated users can request to join an open meetup.
     */
    public function join(User $user, CompanionshipRequest $meetup): bool
    {
        return $meetup->user_id !== $user->id
            && $meetup->status === 'open';
    }

    /**
     * Only the meetup host can manage (approve/reject) attendees.
     */
    public function manage(User $user, CompanionshipRequest $meetup): bool
    {
        return $meetup->user_id === $user->id;
    }

    /**
     * Only the host can update the meetup.
     */
    public function update(User $user, CompanionshipRequest $meetup): bool
    {
        return $meetup->user_id === $user->id;
    }

    /**
     * Only the host can delete/cancel the meetup.
     */
    public function delete(User $user, CompanionshipRequest $meetup): bool
    {
        return $meetup->user_id === $user->id;
    }

    /**
     * Only the specific attendee can cancel their own RSVP.
     */
    public function cancelAttendance(User $user, \App\Models\CompanionshipAttendee $attendee): bool
    {
        return $attendee->user_id === $user->id;
    }
}
