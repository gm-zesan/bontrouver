<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Submit an abuse or moderation report.
     */
    public function store(StoreReportRequest $request): JsonResponse|RedirectResponse
    {
        $report = $this->reportService->submitReport(
            reporter: $request->user(),
            reportableType: $request->validated('reportable_type'),
            reportableId: (int) $request->validated('reportable_id'),
            reason: $request->validated('reason'),
            description: $request->validated('description')
        );

        $message = 'Thank you. Your report has been submitted to our moderation team for review.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'report_id' => $report->id,
            ]);
        }

        return redirect()->back()->with('status', $message);
    }
}
