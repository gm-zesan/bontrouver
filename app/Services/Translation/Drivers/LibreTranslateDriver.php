<?php

namespace App\Services\Translation\Drivers;

use App\Services\Translation\Contracts\TranslationDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LibreTranslateDriver implements TranslationDriverInterface
{
    public function translate(string $text, string $targetLocale, string $sourceLocale = 'auto'): ?string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return '';
        }

        $endpoint = config('translation.libretranslate.endpoint', 'https://libretranslate.com/translate');
        $timeout = config('translation.timeout', 5);
        $apiKey = config('translation.libretranslate.api_key');

        try {
            $payload = [
                'q' => $trimmed,
                'source' => $sourceLocale === 'auto' ? 'auto' : $sourceLocale,
                'target' => $targetLocale,
                'format' => 'text',
            ];

            if (!empty($apiKey)) {
                $payload['api_key'] = $apiKey;
            }

            $response = Http::timeout($timeout)
                ->asJson()
                ->post($endpoint, $payload);

            if (!$response->successful()) {
                Log::warning('LibreTranslate request unsuccessful', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();
            $translated = $data['translatedText'] ?? null;

            if (is_string($translated) && trim($translated) !== '') {
                return $translated;
            }

            return null;
        } catch (Throwable $e) {
            Log::warning('LibreTranslate driver error: ' . $e->getMessage());
            return null;
        }
    }

    public function getName(): string
    {
        return 'libretranslate_v1';
    }
}
