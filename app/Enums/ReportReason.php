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

    public function label(): string
    {
        return match ($this) {
            self::SCAM => 'Fraudulent / Scam',
            self::PROHIBITED => 'Prohibited / Illegal Item',
            self::MISLEADING => 'Misleading Information',
            self::DUPLICATE => 'Duplicate / Spam',
            self::OFFENSIVE => 'Offensive Content',
            self::SPAM => 'Spam / Advertising',
            self::FRAUD => 'Fraudulent Behavior',
            self::HARASSMENT => 'Harassment / Abusive Language',
            self::IMPERSONATION => 'Impersonation / Fake Profile',
            self::INAPPROPRIATE => 'Inappropriate Content',
            self::OTHER => 'Other Policy Violation',
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
