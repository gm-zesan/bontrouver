<?php

namespace App\Services\Translation\Drivers;

use App\Services\Translation\Contracts\TranslationDriverInterface;

class MockTranslationDriver implements TranslationDriverInterface
{
    /**
     * Custom mock response map for testing [text => translated].
     */
    protected static array $customResponses = [];

    /**
     * Whether the mock driver should simulate an external failure.
     */
    protected static bool $shouldFail = false;

    public function translate(string $text, string $targetLocale, string $sourceLocale = 'auto'): ?string
    {
        if (self::$shouldFail) {
            return null;
        }

        if (isset(self::$customResponses[$text])) {
            return self::$customResponses[$text];
        }

        // Deterministic mock translation output for testing
        $prefix = strtoupper($targetLocale);
        return "[$prefix] $text";
    }

    public function getName(): string
    {
        return 'mock_v1';
    }

    public static function setMockResponse(string $input, string $output): void
    {
        self::$customResponses[$input] = $output;
    }

    public static function simulateFailure(bool $fail = true): void
    {
        self::$shouldFail = $fail;
    }

    public static function reset(): void
    {
        self::$customResponses = [];
        self::$shouldFail = false;
    }
}
