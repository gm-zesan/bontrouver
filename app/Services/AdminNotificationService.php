<?php

namespace App\Services;

use App\Models\Report;
use App\Models\UserVerification;

class AdminNotificationService
{
    /**
     * Get aggregated important notifications for the admin panel.
     * Restricted strictly to high-priority safety reports and identity verifications.
     */
    public function getImportantNotifications(): array
    {
        $notifications = collect();

        // 1. Pending Abuse & Safety Reports (Urgent Priority)
        $pendingReports = Report::with('reporter')
            ->whereNull('reviewed_at')
            ->latest()
            ->take(10)
            ->get();

        $unresolvedReportsCount = Report::whereNull('reviewed_at')->count();

        foreach ($pendingReports as $report) {
            $reasonText = $report->reason?->label() ?? 'Safety Concern';
            $reportableType = class_basename($report->reportable_type ?? 'Listing');

            $notifications->push([
                'id' => 'report-' . $report->id,
                'category' => 'report',
                'priority' => 'high',
                'icon' => 'ri-alarm-warning-fill text-danger',
                'bg_class' => 'bg-danger-subtle',
                'badge' => 'Safety Report',
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'title' => "Safety Report: {$reasonText}",
                'message' => "Reported {$reportableType} flagged for moderator review by " . ($report->reporter?->name ?? 'a user'),
                'url' => route('admin.reports.index'),
                'created_at' => $report->created_at,
                'time' => $report->created_at?->diffForHumans() ?? 'Just now',
            ]);
        }

        // 2. Pending ID Verification Submissions (High Priority)
        $pendingVerifications = UserVerification::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        $pendingVerificationsCount = UserVerification::where('status', 'pending')->count();

        foreach ($pendingVerifications as $verif) {
            $docType = ucfirst(str_replace('_', ' ', $verif->document_type ?? 'ID'));
            $userName = $verif->user?->name ?? 'Member';

            $notifications->push([
                'id' => 'verification-' . $verif->id,
                'category' => 'verification',
                'priority' => 'high',
                'icon' => 'ri-shield-user-fill text-primary',
                'bg_class' => 'bg-primary-subtle',
                'badge' => 'ID Verification',
                'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'title' => "ID Verification: {$userName}",
                'message' => "Canadian {$docType} document submitted for identity badge verification",
                'url' => route('admin.verifications.index'),
                'created_at' => $verif->created_at,
                'time' => $verif->created_at?->diffForHumans() ?? 'Just now',
            ]);
        }

        // Sort items by date descending
        $sortedItems = $notifications->sortByDesc('created_at')->values()->all();

        // Urgent action items count
        $urgentActionCount = $unresolvedReportsCount + $pendingVerificationsCount;

        return [
            'urgent_count' => $urgentActionCount,
            'total_count' => count($sortedItems),
            'items' => $sortedItems,
            'stats' => [
                'unresolved_reports' => $unresolvedReportsCount,
                'pending_verifications' => $pendingVerificationsCount,
            ],
        ];
    }
}
