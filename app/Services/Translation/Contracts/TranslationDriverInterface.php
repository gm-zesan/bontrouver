<?php

namespace App\Services\Translation\Contracts;

interface TranslationDriverInterface
{
    /**
     * Translate text from a source locale to a target locale.
     *
     * @param string $text Raw text to translate
     * @param string $targetLocale Target ISO code (e.g., 'fr', 'en')
     * @param string $sourceLocale Source ISO code or 'auto' for automatic detection
     * @return string|null Translated string on success, or null on provider failure
     */
    public function translate(string $text, string $targetLocale, string $sourceLocale = 'auto'): ?string;

    /**
     * Return unique identifier name and version of the driver.
     */
    public function getName(): string;
}
