<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select('users.*');

            return DataTables::of($users)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    $nameHtml = e($row->name);
                    if ($row->is_suspended) {
                        $nameHtml .= ' <span class="badge bg-danger ms-1" style="font-size: 10px; padding: 2px 5px;">Suspended</span>';
                    }
                    return $nameHtml;
                })
                ->addColumn('role', function ($row) {
                    $badgeStyle = match ($row->role) {
                        UserRole::ADMIN => 'background-color: #fff3ee; color: #f95716; border: 1px solid rgba(249, 87, 22, 0.3);',
                        default => 'background-color: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe;',
                    };
                    return '<span class="badge" style="' . $badgeStyle . ' font-size: 11.5px; padding: 4px 10px; border-radius: 4px; font-weight: 600;">' . e($row->role->label()) . '</span>';
                })
                ->addColumn('action-btn', function ($row) {
                    $id = $row->id;
                    $name = htmlspecialchars($row->name ?? '', ENT_QUOTES);
                    $email = htmlspecialchars($row->email ?? '', ENT_QUOTES);
                    $role = htmlspecialchars($row->role->value ?? '', ENT_QUOTES);

                    $showUrl = route('admin.users.show', $id);
                    $deleteUrl = route('admin.users.destroy', $id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    $btn = '<div class="action-btn d-flex align-items-center gap-1">';

                    // Show Button (Edit Page)
                    $btn .= '<a href="' . $showUrl . '" class="btn btn-sm btn-light border" title="Edit/Full Details" style="padding: 4px 8px; background: #fff;"><i class="ri-edit-line text-primary"></i></a>';

                    // Assign Role Modal Button
                    $btn .= '<button type="button" class="btn btn-sm btn-light border btn-assign-modal" data-id="' . $id . '" data-name="' . $name . '" data-email="' . $email . '" data-role="' . $role . '" title="Assign Role" style="padding: 4px 8px; background: #fff;"><i class="ri-shield-user-line text-success"></i></button>';

                    // Suspend Button (Modal)
                    $suspendUrl = route('admin.users.suspend', $id);
                    $isSuspended = $row->is_suspended;
                    $suspendIcon = $isSuspended ? 'ri-user-follow-line text-success' : 'ri-user-unfollow-line text-warning';
                    $suspendTitle = $isSuspended ? 'Unsuspend User' : 'Suspend User';
                    $suspendConfirm = $isSuspended ? 'Are you sure you want to unsuspend this user?' : 'Are you sure you want to suspend this user?';
                    $btnClass = $isSuspended ? 'btn-success' : 'btn-warning';
                    $btnText = $isSuspended ? 'Unsuspend' : 'Suspend';

                    $btn .= '<button type="button" class="btn btn-sm btn-light border btn-confirm-modal" data-action="' . $suspendUrl . '" data-method="POST" data-title="' . $suspendTitle . '" data-desc="' . $suspendConfirm . '" data-btn-class="' . $btnClass . '" data-btn-text="' . $btnText . '" title="' . $suspendTitle . '" style="padding: 4px 8px; background: #fff;"><i class="' . $suspendIcon . '"></i></button>';

                    // Delete Button (Modal)
                    $btn .= '<button type="button" class="btn btn-sm btn-light border btn-confirm-modal" data-action="' . $deleteUrl . '" data-method="DELETE" data-title="Confirm Deletion" data-desc="Are you absolutely sure you want to permanently delete this user? This action cannot be undone." data-btn-class="btn-danger" data-btn-text="Delete" title="Delete User" style="padding: 4px 8px; background: #fff;"><i class="ri-delete-bin-line text-danger"></i></button>';

                    $btn .= '</div>';
                    return $btn;
                })
                ->addColumn('community_points', function ($row) {
                    return '<span class="fw-semibold text-dark">' . number_format($row->community_points) . '</span>';
                })
                ->rawColumns(['name', 'role', 'community_points', 'action-btn'])
                ->make(true);
        }

        return view('admin.users.index');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->loadCount(['listings', 'reviewsReceived', 'purchases', 'sales', 'companionshipRequests', 'companionshipAttendees', 'pointTransactions', 'reviewsGiven']);

        $listings = $user->listings()->with(['category', 'city'])->latest()->paginate(5, ['*'], 'listings_page')->fragment('listings');
        $meetupsHosted = $user->companionshipRequests()->with('cityRelation')->latest()->paginate(5, ['*'], 'meetups_hosted_page')->fragment('meetups');
        $meetupsJoined = $user->companionshipAttendees()->with('companionshipRequest.cityRelation', 'companionshipRequest.user')->latest()->paginate(5, ['*'], 'meetups_joined_page')->fragment('meetups');
        $pointTransactions = $user->pointTransactions()->latest()->paginate(10, ['*'], 'points_page')->fragment('points');
        $reviewsReceived = $user->reviewsReceived()->with('reviewer')->latest()->paginate(5, ['*'], 'reviews_received_page')->fragment('reviews');
        $reviewsGiven = $user->reviewsGiven()->with('reviewee')->latest()->paginate(5, ['*'], 'reviews_given_page')->fragment('reviews');

        return view('admin.users.show', compact(
            'user',
            'listings',
            'meetupsHosted',
            'meetupsJoined',
            'pointTransactions',
            'reviewsReceived',
            'reviewsGiven'
        ));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::enum(UserRole::class)],
            'is_verified' => 'boolean',
            'community_points' => 'integer|min:0',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:50',
        ]);

        // Prevent admin from removing their own admin role to avoid lockout
        if ($user->id === auth()->id() && $validated['role'] !== UserRole::ADMIN->value && $user->role === UserRole::ADMIN) {
            return back()->with('error', 'You cannot remove your own admin privileges.');
        }

        $user->update([
            'role' => $validated['role'],
            'is_verified' => $request->has('is_verified') ? true : false,
            'community_points' => $validated['community_points'] ?? $user->community_points,
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
        ]);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * Toggle the suspension status of the user.
     */
    public function toggleSuspend(User $user)
    {
        // Prevent suspending self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $action = $user->is_suspended ? 'suspended' : 'unsuspended';
        return back()->with('success', "User has been {$action} successfully.");
    }

    /**
     * Remove the specified user from storage (Soft Delete).
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Assign a role to a user via the modal form.
     */
    public function assignRole(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        // Prevent admin from removing their own admin role to avoid lockout
        if ($user->id === auth()->id() && $request->role !== UserRole::ADMIN->value && $user->role === UserRole::ADMIN) {
            return back()->with('error', 'You cannot remove your own admin privileges.');
        }

        $user->update([
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }
}
