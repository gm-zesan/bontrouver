<?php

namespace App\Services\Translation\Drivers;

use App\Services\Translation\Contracts\TranslationDriverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * GoogleWebDriver
 *
 * Pluggable driver utilizing Google's unofficial web client translation endpoint.
 *
 * CRITICAL ARCHITECTURAL & LEGAL NOTICE:
 * 1. UNOFFICIAL ENDPOINT: This driver accesses `https://translate.googleapis.com/translate_a/single?client=gtx`.
 *    This is an undocumented/unofficial web endpoint, NOT the official Google Cloud Translation API (Cloud Translation v2/v3).
 * 2. NO SLA OR GUARANTEE: This endpoint is NOT guaranteed to remain free, is NOT approved by Google for commercial
 *    production use, and is NOT backed by any Service Level Agreement (SLA), uptime commitment, or enterprise contract.
 * 3. RATE LIMITING & BREAKING RISK: Google may return HTTP 429 (Too Many Requests), require CAPTCHA challenges, or
 *    modify its response schema without prior warning.
 * 4. PRIVACY IMPLICATIONS: Uncached listing titles and descriptions sent through this endpoint transit Google's public
 *    infrastructure. No confidentiality or GDPR/PIPEDA enterprise compliance is contractually guaranteed for this endpoint.
 * 5. PRODUCTION RESILIENCE: All requests are guarded by short timeouts, graceful null fallback, and 30-day SHA-256
 *    caching to minimize external network requests. Provider failures must never break user-facing pages.
 */
class GoogleWebDriver implements TranslationDriverInterface
{
    public function translate(string $text, string $targetLocale, string $sourceLocale = 'auto'): ?string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return '';
        }

        // Unofficial Google web translation client endpoint (NOT Google Cloud Translation API)
        $endpoint = 'https://translate.googleapis.com/translate_a/single';
        $timeout = (int) config('translation.timeout', 8);

        $headers = [
            'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            'Accept'          => '*/*',
            'Accept-Language' => 'en-US,en;q=0.9,fr;q=0.8',
            'Referer'         => 'https://translate.google.com/',
        ];

        $payload = [
            'client' => 'gtx',
            'sl'     => ($sourceLocale === 'auto' || empty($sourceLocale)) ? 'auto' : $sourceLocale,
            'tl'     => $targetLocale,
            'dt'     => 't',
            'q'      => $trimmed,
        ];

        try {
            // First attempt: POST form payload with desktop browser headers (prevents datacenter IP blocks)
            $response = Http::withoutVerifying()
                ->withHeaders($headers)
                ->timeout($timeout)
                ->asForm()
                ->post($endpoint, $payload);

            // Fallback to GET if POST was not successful
            if (!$response->successful()) {
                $response = Http::withoutVerifying()
                    ->withHeaders($headers)
                    ->timeout($timeout)
                    ->get($endpoint, $payload);
            }

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
