<?php

namespace App\Enums;

enum ReportReason: string
{
    case SCAM = 'scam';
    case PROHIBITED = 'prohibited';
    case MISLEADING = 'misleading';
    case DUPLICATE = 'duplicate';
    case OFFENSIVE = 'offensive';
    case SPAM = 'spam';
    case FRAUD = 'fraud';
    case HARASSMENT = 'harassment';
    case IMPERSONATION = 'impersonation';
    case INAPPROPRIATE = 'inappropriate';
    case OTHER = 'other';

    public function label(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($this) {
            self::SCAM          => $locale === 'fr' ? 'Frauduleux / Escroquerie' : 'Fraudulent / Scam',
            self::PROHIBITED    => $locale === 'fr' ? 'Article interdit / Illégal' : 'Prohibited / Illegal Item',
            self::MISLEADING    => $locale === 'fr' ? 'Informations trompeuses' : 'Misleading Information',
            self::DUPLICATE     => $locale === 'fr' ? 'Doublon / Pourriel' : 'Duplicate / Spam',
            self::OFFENSIVE     => $locale === 'fr' ? 'Contenu offensant' : 'Offensive Content',
            self::SPAM          => $locale === 'fr' ? 'Pourriel / Publicité' : 'Spam / Advertising',
            self::FRAUD         => $locale === 'fr' ? 'Comportement frauduleux' : 'Fraudulent Behavior',
            self::HARASSMENT    => $locale === 'fr' ? 'Harcèlement / Propos abusifs' : 'Harassment / Abusive Language',
            self::IMPERSONATION => $locale === 'fr' ? 'Usurpation d\'identité' : 'Impersonation / Fake Profile',
            self::INAPPROPRIATE => $locale === 'fr' ? 'Contenu inapproprié' : 'Inappropriate Content',
            self::OTHER         => $locale === 'fr' ? 'Autre violation des règles' : 'Other Policy Violation',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SCAM, self::FRAUD, self::PROHIBITED => 'bg-danger text-white',
            self::HARASSMENT, self::OFFENSIVE, self::IMPERSONATION => 'bg-danger-subtle text-danger',
            self::SPAM, self::DUPLICATE, self::MISLEADING => 'bg-warning text-dark',
            default => 'bg-secondary text-white',
        };
    }

    public static function listingReasons(): array
    {
        return [
            self::SCAM,
            self::PROHIBITED,
            self::MISLEADING,
            self::DUPLICATE,
            self::OFFENSIVE,
            self::OTHER,
        ];
    }

    public static function userReasons(): array
    {
        return [
            self::SPAM,
            self::FRAUD,
            self::HARASSMENT,
            self::IMPERSONATION,
            self::OTHER,
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
