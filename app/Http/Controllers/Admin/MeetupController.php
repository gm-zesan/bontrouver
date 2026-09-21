<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanionshipRequest;
use App\Models\CompanionshipAttendee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MeetupController extends Controller
{
    /**
     * Display a listing of meetups.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CompanionshipRequest::with(['user', 'cityRelation']);

            if ($request->filled('type')) {
                $query->where('type', $request->get('type'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check m-0"><input class="form-check-input meetup-checkbox border-secondary" type="checkbox" value="' . $row->id . '"></div>';
                })
                ->editColumn('title', function ($row) {
                    $title = e($row->title);
                    $showUrl = route('admin.meetups.show', $row->id);
                    $typeBadge = '<span class="badge bg-light text-secondary border fw-medium me-2" style="font-size: 11px;">' . e($row->type) . '</span>';
                    
                    return '<div class="d-flex flex-column">' .
                           '<div class="d-flex align-items-center mb-1">' . $typeBadge . '<a href="' . $showUrl . '" class="fw-semibold text-dark text-decoration-none" style="font-size: 13.5px;">' . $title . '</a></div>' .
                           '<span class="text-muted small"><i class="ri-map-pin-line me-1"></i>' . e($row->city ?? 'Canada') . ($row->province ? ', ' . e($row->province) : '') . '</span>' .
                           '</div>';
                })
                ->addColumn('host', function ($row) {
                    if (!$row->user) {
                        return '<span class="text-muted small">N/A</span>';
                    }
                    $name = e($row->user->name);
                    $profileUrl = route('admin.users.show', $row->user->id);
                    $verifiedBadge = $row->user->is_verified ? '<i class="ri-verified-badge-fill text-primary ms-1" style="font-size: 14px;" title="Verified User"></i>' : '';
                    return '<a href="' . $profileUrl . '" class="text-decoration-none fw-medium text-dark" style="font-size: 13px;">' . $name . '</a>' . $verifiedBadge;
                })
                ->editColumn('meetup_date_time', function ($row) {
                    return '<span class="text-dark fw-medium" style="font-size: 13px;">' . $row->meetup_date_time->format('M d, Y') . '</span><br><span class="text-muted small">' . $row->meetup_date_time->format('g:i A') . '</span>';
                })
                ->addColumn('capacity', function ($row) {
                    $approved = $row->attendees()->where('status', 'approved')->count();
                    $limit = $row->headcount_limit ?? '∞';
                    $isFull = $row->status === 'full' || ($limit !== '∞' && $approved >= $limit);
                    $badgeClass = $isFull ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success';
                    return '<span class="badge ' . $badgeClass . ' fw-medium" style="font-size: 12px;">' . $approved . ' / ' . $limit . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $status = strtolower($row->status);
                    $badgeClass = match($status) {
                        'open' => 'bg-success text-white',
                        'full' => 'bg-warning text-dark',
                        'completed' => 'bg-info text-dark',
                        'cancelled' => 'bg-secondary text-white',
                        default => 'bg-light text-secondary border'
                    };
                    return '<span class="badge ' . $badgeClass . '" style="font-size: 11px; padding: 4px 8px; text-transform: uppercase;">' . e($status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $id = $row->id;
                    $showUrl = route('admin.meetups.show', $id);
                    $deleteUrl = route('admin.meetups.destroy', $id);
                    $statusUrl = route('admin.meetups.updateStatus', $id);

                    $isCompletedOrCancelled = in_array(strtolower($row->status), ['completed', 'cancelled']);
                    
                    $actions = '<div class="d-flex align-items-center justify-content-end gap-1">';
                    
                    // View
                    $actions .= '<a href="' . $showUrl . '" class="btn btn-sm btn-light border text-primary" title="View Details" style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;"><i class="ri-eye-line"></i></a>';
                    
                    // Cancel Toggle (only if not completed/cancelled)
                    if (!$isCompletedOrCancelled) {
                        $actions .= '<button type="button" class="btn btn-sm btn-light border text-warning btn-confirm-modal" title="Cancel Meetup" style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;" data-action="' . $statusUrl . '" data-method="POST" data-title="Cancel Meetup?" data-desc="Are you sure you want to cancel this meetup? Attendees will be notified." data-btn-class="btn-warning" data-btn-text="Yes, Cancel" data-extra-payload=\'{"status": "cancelled"}\'><i class="ri-close-circle-line"></i></button>';
                    }

                    // Delete
                    $actions .= '<button type="button" class="btn btn-sm btn-light border text-danger btn-confirm-modal" title="Delete Meetup" style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;" data-action="' . $deleteUrl . '" data-method="DELETE" data-title="Delete Meetup" data-desc="Are you sure you want to permanently delete this meetup? This action cannot be undone." data-btn-class="btn-danger" data-btn-text="Delete"><i class="ri-delete-bin-line"></i></button>';
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['checkbox', 'title', 'host', 'meetup_date_time', 'capacity', 'status', 'action'])
                ->make(true);
        }

        return view('admin.meetups.index');
    }

    /**
     * Display the specified meetup.
     */
    public function show(CompanionshipRequest $meetup)
    {
        $meetup->load(['user', 'cityRelation', 'attendees.user', 'reports.reporter']);
        return view('admin.meetups.show', compact('meetup'));
    }

    /**
     * Update the status of the specified meetup.
     */
    public function updateStatus(Request $request, CompanionshipRequest $meetup)
    {
        $request->validate([
            'status' => 'required|in:open,full,cancelled,completed'
        ]);

        $meetup->update(['status' => $request->status]);

        // Note: In a real app, we might dispatch an event here to notify attendees if cancelled/completed.
        // For admin panel, we just update the status.

        return response()->json([
            'success' => true,
            'message' => 'Meetup status updated successfully.'
        ]);
    }

    /**
     * Remove the specified meetup from storage.
     */
    public function destroy(CompanionshipRequest $meetup)
    {
        $meetup->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Meetup deleted successfully.'
            ]);
        }

        return redirect()->route('admin.meetups.index')->with('success', 'Meetup deleted successfully.');
    }

    /**
     * Handle bulk actions for meetups.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:cancel,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:companionship_requests,id'
        ]);

        $ids = $request->ids;
        $action = $request->action;

        if ($action === 'delete') {
            CompanionshipRequest::whereIn('id', $ids)->delete();
            $message = 'Selected meetups have been deleted.';
        } elseif ($action === 'cancel') {
            CompanionshipRequest::whereIn('id', $ids)->update(['status' => 'cancelled']);
            $message = 'Selected meetups have been cancelled.';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Remove an attendee from the meetup.
     */
    public function removeAttendee(CompanionshipRequest $meetup, CompanionshipAttendee $attendee)
    {
        // Ensure attendee belongs to this meetup
        if ($attendee->companionship_request_id !== $meetup->id) {
            return response()->json(['success' => false, 'message' => 'Attendee does not belong to this meetup.'], 403);
        }

        $attendee->delete();
        
        // Recalculate if it was full and now has space
        if ($meetup->status === 'full' && $meetup->headcount_limit) {
            $approved = $meetup->attendees()->where('status', 'approved')->count();
            if ($approved < $meetup->headcount_limit) {
                $meetup->update(['status' => 'open']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendee removed successfully.'
        ]);
    }
}
