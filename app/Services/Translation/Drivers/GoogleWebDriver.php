<?php

namespace App\Services\Translation\Drivers;

use App\Services\Translation\Contracts\TranslationDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleWebDriver implements TranslationDriverInterface
{
    public function translate(string $text, string $targetLocale, string $sourceLocale = 'auto'): ?string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return '';
        }

        $endpoint = 'https://translate.googleapis.com/translate_a/single';
        $timeout = (int) config('translation.timeout', 5);

        try {
            $response = Http::withoutVerifying()
                ->timeout($timeout)
                ->retry(1, 50, when: fn ($exception) => !($exception instanceof \Illuminate\Http\Client\RequestException && $exception->response?->status() === 429), throw: false)
                ->get($endpoint, [
                    'client' => 'gtx',
                    'sl'     => ($sourceLocale === 'auto' || empty($sourceLocale)) ? 'auto' : $sourceLocale,
                    'tl'     => $targetLocale,
                    'dt'     => 't',
                    'q'      => $trimmed,
                ]);

            if (!$response->successful()) {
                Log::warning('GoogleWeb translation request failed with status ' . $response->status());
                return null;
            }

            $data = $response->json();
            if (!is_array($data) || !isset($data[0]) || !is_array($data[0])) {
                return null;
            }

            $translated = '';
            foreach ($data[0] as $segment) {
                if (is_array($segment) && isset($segment[0]) && is_string($segment[0])) {
                    $translated .= $segment[0];
                }
            }

            return $translated !== '' ? trim($translated) : null;
        } catch (Throwable $e) {
            Log::warning('GoogleWebDriver error: ' . $e->getMessage());
            return null;
        }
    }

    public function getName(): string
    {
        return 'google_web_v1';
    }
}
