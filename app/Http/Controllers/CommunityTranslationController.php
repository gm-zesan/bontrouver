<?php

namespace App\Http\Controllers;

use App\Models\CompanionshipRequest;
use App\Services\Translation\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityTranslationController extends Controller
{
    public function __construct(
        protected TranslationService $translationService
    ) {}

    /**
     * Translate a single community meetup (title & description).
     */
    public function translate(Request $request, CompanionshipRequest $meetup): JsonResponse
    {
        $targetLocale = (string) $request->input('target_locale', 'fr');
        if (!in_array(strtolower($targetLocale), ['en', 'fr'], true)) {
            $targetLocale = 'fr';
        }

        $scope = (string) $request->input('scope', 'detail');
        if (!in_array($scope, ['detail', 'card'], true)) {
            $scope = 'detail';
        }

        $result = $this->translationService->translateMeetup($meetup, $targetLocale, $scope);

        if (empty($result['success'])) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? __('Translation currently unavailable'),
            ], 503);
        }

        return response()->json($result, 200);
    }

    /**
     * Batch translate multiple community meetups for cards.
     */
    public function translateBatch(Request $request): JsonResponse
    {
        $ids = $request->input('meetup_ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => true,
                'translations' => (object)[],
            ], 200);
        }

        $targetLocale = (string) $request->input('target_locale', 'fr');
        if (!in_array(strtolower($targetLocale), ['en', 'fr'], true)) {
            $targetLocale = 'fr';
        }

        $sanitizedIds = array_slice(array_unique(array_filter(array_map('intval', $ids))), 0, 30);

        if (empty($sanitizedIds)) {
            return response()->json([
                'success' => true,
                'translations' => (object)[],
            ], 200);
        }

        $meetups = CompanionshipRequest::whereIn('id', $sanitizedIds)->get();

        $translations = $this->translationService->translateMeetupsBatch($meetups, $targetLocale, 'card');

        return response()->json([
            'success' => true,
            'target_locale' => $targetLocale,
            'translations' => $translations,
        ], 200);
    }
}
