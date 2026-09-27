<?php

namespace App\Services;

use App\Enums\ListingStatus;
use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AdminReportService
{
    /**
     * Build query for reports with filters and eager loading.
     */
    public function getReportsQuery(array $filters = []): Builder
    {
        $query = Report::with([
            'reporter',
            'reviewer',
            'reportable',
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['reason'])) {
            $query->where('reason', $filters['reason']);
        }

        if (!empty($filters['target_type'])) {
            $typeMap = [
                'listing' => Listing::class,
                'user' => User::class,
                'meetup' => CompanionshipRequest::class,
                'companionship' => CompanionshipRequest::class,
            ];
            if (isset($typeMap[$filters['target_type']])) {
                $query->where('reportable_type', $typeMap[$filters['target_type']]);
            }
        }

        return $query->latest('created_at');
    }

    /**
     * Resolve a single report with optional disciplinary action on the target entity.
     */
    public function resolveReport(Report $report, User $reviewer, ?string $notes = null, ?string $action = 'none'): array
    {
        return DB::transaction(function () use ($report, $reviewer, $notes, $action) {
            $report->update([
                'status' => 'resolved',
                'description' => $notes ? ($report->description ? $report->description . "\n\n[Moderator Resolution]: " . $notes : "[Moderator Resolution]: " . $notes) : $report->description,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $actionTaken = 'none';

            // Apply optional disciplinary action if requested
            if ($action && $action !== 'none' && $report->reportable) {
                if ($action === 'takedown_listing' && $report->reportable instanceof Listing) {
                    $report->reportable->update(['status' => ListingStatus::REJECTED]);
                    $actionTaken = 'Listing status set to Rejected/Taken Down.';
                } elseif ($action === 'suspend_user') {
                    $targetUser = null;
                    if ($report->reportable instanceof User) {
                        $targetUser = $report->reportable;
                    } elseif (isset($report->reportable->user)) {
                        $targetUser = $report->reportable->user;
                    }

                    if ($targetUser) {
                        $targetUser->update([
                            'is_suspended' => true,
                            'suspended_at' => now(),
                            'internal_notes' => ($targetUser->internal_notes ? $targetUser->internal_notes . "\n" : '') . "[Moderation Action]: Suspended following report #" . $report->id . " by " . $reviewer->name . " on " . now()->format('Y-m-d H:i') . ($notes ? " (Reason: {$notes})" : ""),
                        ]);
                        $actionTaken = 'User account has been suspended.';
                    }
                } elseif ($action === 'cancel_meetup' && $report->reportable instanceof CompanionshipRequest) {
                    $report->reportable->update(['status' => 'cancelled']);
                    $actionTaken = 'Meetup request has been cancelled.';
                }
            }

            return [
                'success' => true,
                'report' => $report,
                'action_taken' => $actionTaken,
            ];
        });
    }

    /**
     * Dismiss a report as invalid or unfounded.
     */
    public function dismissReport(Report $report, User $reviewer, ?string $notes = null): Report
    {
        $report->update([
            'status' => 'dismissed',
            'description' => $notes ? ($report->description ? $report->description . "\n\n[Moderator Dismissal]: " . $notes : "[Moderator Dismissal]: " . $notes) : $report->description,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        return $report;
    }

    /**
     * Perform bulk moderation actions on reports.
     */
    public function bulkAction(string $action, array $reportIds, User $reviewer, ?string $notes = null): array
    {
        $reports = Report::whereIn('id', $reportIds)->get();
        $count = $reports->count();

        DB::transaction(function () use ($reports, $action, $reviewer, $notes) {
            foreach ($reports as $report) {
                if ($action === 'resolve') {
                    $this->resolveReport($report, $reviewer, $notes, 'none');
                } elseif ($action === 'dismiss') {
                    $this->dismissReport($report, $reviewer, $notes);
                } elseif ($action === 'delete') {
                    $report->delete();
                }
            }
        });

        return [
            'success' => true,
            'count' => $count,
            'action' => $action,
        ];
    }

    /**
     * Get aggregate statistics for dashboard / moderation header cards.
     */
    public function getStats(): array
    {
        return [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'dismissed' => Report::where('status', 'dismissed')->count(),
            'listings' => Report::where('reportable_type', Listing::class)->count(),
            'users' => Report::where('reportable_type', User::class)->count(),
            'meetups' => Report::where('reportable_type', CompanionshipRequest::class)->count(),
        ];
    }
}
