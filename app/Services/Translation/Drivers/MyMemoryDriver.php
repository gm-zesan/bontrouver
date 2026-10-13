<?php

namespace App\Services\Translation\Drivers;

use App\Services\Translation\Contracts\TranslationDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class MyMemoryDriver implements TranslationDriverInterface
{
    public function translate(string $text, string $targetLocale, string $sourceLocale = 'auto'): ?string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return '';
        }

        $endpoint = config('translation.mymemory.endpoint', 'https://api.mymemory.translated.net/get');
        $timeout = config('translation.timeout', 5);
        $email = config('translation.mymemory.email');

        // MyMemory uses 'autodetect' for auto source language detection
        $source = ($sourceLocale === 'auto' || empty($sourceLocale)) ? 'autodetect' : $sourceLocale;
        $langpair = "{$source}|{$targetLocale}";

        try {
            $params = [
                'q' => $trimmed,
                'langpair' => $langpair,
            ];

            if (!empty($email)) {
                $params['de'] = $email;
            }

            $response = Http::withoutVerifying()
                ->timeout($timeout)
                ->retry(2, 200, throw: false)
                ->get($endpoint, $params);

            if (!$response->successful()) {
                Log::warning('MyMemory translation request unsuccessful', [
                    'status' => $response->status(),
                    'langpair' => $langpair,
                ]);
                return null;
            }

            $data = $response->json();
            $status = $data['responseStatus'] ?? null;
            $translated = $data['responseData']['translatedText'] ?? null;

            // MyMemory returns responseStatus = 200 on success
            if ($status === 200 && is_string($translated) && trim($translated) !== '') {
                // Check for quota warning string in response
                if (str_contains(strtoupper($translated), 'MYMEMORY WARNING:')) {
                    Log::warning('MyMemory translation quota reached');
                    return null;
                }

                return html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            Log::warning('MyMemory returned unexpected payload', [
                'responseStatus' => $status,
            ]);
            return null;
        } catch (Throwable $e) {
            Log::warning('MyMemory driver error: ' . $e->getMessage());
            return null;
        }
    }

    public function getName(): string
    {
        return 'mymemory_v1';
    }
}
