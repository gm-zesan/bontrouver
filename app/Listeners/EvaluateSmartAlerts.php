<?php

namespace App\Listeners;

use App\Events\ListingCreated;
use App\Services\SmartAlertService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EvaluateSmartAlerts implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        private SmartAlertService $smartAlertService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(ListingCreated $event): void
    {
        $this->smartAlertService->evaluateNewListing($event->listing);
    }
}
