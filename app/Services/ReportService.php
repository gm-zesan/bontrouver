<?php

namespace App\Services;

use App\Models\CompanionshipRequest;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ReportService
{
    /**
     * Map friendly type string to Eloquent Model class.
     */
    protected array $typeMap = [
        'listing' => Listing::class,
        'user' => User::class,
        'companionship' => CompanionshipRequest::class,
        'companionship_request' => CompanionshipRequest::class,
    ];

    /**
     * Submit a new abuse / moderation report for a listing, user, or meetup.
     *
     * @throws ValidationException
     */
    public function submitReport(User $reporter, string $reportableType, int $reportableId, \App\Enums\ReportReason|string $reason, ?string $description = null): Report
    {
        $reasonEnum = $reason instanceof \App\Enums\ReportReason ? $reason : (\App\Enums\ReportReason::tryFrom($reason) ?? \App\Enums\ReportReason::OTHER);
        $normalizedType = strtolower($reportableType);
        $modelClass = $this->typeMap[$normalizedType] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            throw ValidationException::withMessages([
                'reportable_type' => ['Invalid reportable entity type.'],
            ]);
        }

        $reportable = $modelClass::find($reportableId);
        if (!$reportable) {
            throw ValidationException::withMessages([
                'reportable_id' => ['The item you are reporting does not exist or has been removed.'],
            ]);
        }

        // Prevent self-reporting
        if ($modelClass === User::class && $reportable->id === $reporter->id) {
            throw ValidationException::withMessages([
                'reportable_id' => ['You cannot report your own account.'],
            ]);
        }
        if (isset($reportable->user_id) && $reportable->user_id === $reporter->id) {
            throw ValidationException::withMessages([
                'reportable_id' => ['You cannot report your own listing or content.'],
            ]);
        }

        // Prevent duplicate pending reports from the same reporter
        $existingReport = Report::where('reporter_id', $reporter->id)
            ->where('reportable_type', $modelClass)
            ->where('reportable_id', $reportableId)
            ->where('status', 'pending')
            ->first();

        if ($existingReport) {
            throw ValidationException::withMessages([
                'reportable_id' => ['You have already submitted a pending report for this item. Our moderation team is reviewing it.'],
            ]);
        }

        return Report::create([
            'reporter_id' => $reporter->id,
            'reportable_type' => $modelClass,
            'reportable_id' => $reportableId,
            'reason' => $reasonEnum,
            'description' => $description,
            'status' => 'pending',
        ]);
    }
}
