<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Http\Requests\TranslateListingRequest;
use App\Models\Listing;
use App\Services\Translation\TranslationService;
use Illuminate\Http\JsonResponse;

class ListingTranslationController extends Controller
{
    protected TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Translate an active listing's content on-demand into English or French.
     */
    public function translate(Listing $listing, TranslateListingRequest $request): JsonResponse
    {
        // Visibility Check: Only active public listings may be translated
        $statusValue = $listing->status instanceof ListingStatus
            ? $listing->status->value
            : (string) $listing->status;

        if ($statusValue !== ListingStatus::ACTIVE->value) {
            return response()->json([
                'success' => false,
                'message' => 'Only active listings are eligible for translation.',
            ], 403);
        }

        $targetLocale = $request->input('target_locale');
        $scope = $request->input('scope', 'detail');

        $result = $this->translationService->translateListing($listing, $targetLocale, $scope);

        if (!$result['success']) {
            return response()->json($result, 503);
        }

        return response()->json($result, 200);
    }

    /**
     * Batch-translate multiple listing cards for fast, non-blocking rendering.
     */
    public function translateBatch(\Illuminate\Http\Request $request): JsonResponse
    {
        $ids = $request->input('listing_ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No listing IDs provided.',
            ], 422);
        }

        $targetLocale = (string) $request->input('target_locale', 'fr');
        if (!in_array(strtolower($targetLocale), ['en', 'fr'], true)) {
            $targetLocale = 'fr';
        }

        $scope = (string) $request->input('scope', 'card');
        if (!in_array($scope, ['card', 'hero', 'detail'], true)) {
            $scope = 'card';
        }

        // Limit to max 30 listings per batch to keep execution safe
        $sanitizedIds = array_slice(array_unique(array_filter(array_map('intval', $ids))), 0, 30);

        if (empty($sanitizedIds)) {
            return response()->json([
                'success' => true,
                'translations' => (object)[],
            ], 200);
        }

        $listings = Listing::whereIn('id', $sanitizedIds)
            ->where('status', ListingStatus::ACTIVE)
            ->get();

        $translations = $this->translationService->translateListingsBatch($listings, $targetLocale, $scope);

        return response()->json([
            'success' => true,
            'target_locale' => $targetLocale,
            'scope' => $scope,
            'translations' => $translations,
        ], 200);
    }
}
