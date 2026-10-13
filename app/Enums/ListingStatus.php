<?php

namespace App\Enums;

enum ListingStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case SOLD = 'sold';
    case EXPIRED = 'expired';
    case REJECTED = 'rejected';

    /**
     * Get a human-readable localized display label.
     */
    public function label(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($this) {
            self::DRAFT          => $locale === 'fr' ? 'Brouillon' : 'Draft',
            self::PENDING_REVIEW => $locale === 'fr' ? 'En attente de révision' : 'Pending Review',
            self::ACTIVE         => $locale === 'fr' ? 'Actif' : 'Active',
            self::PAUSED         => $locale === 'fr' ? 'En pause' : 'Paused',
            self::SOLD           => $locale === 'fr' ? 'Vendu' : 'Sold',
            self::EXPIRED        => $locale === 'fr' ? 'Expiré' : 'Expired',
            self::REJECTED       => $locale === 'fr' ? 'Rejeté' : 'Rejected',
        };
    }

    /**
     * Get the Bootstrap badge CSS class for admin & front-end display.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-success-subtle text-success border border-success-subtle',
            self::PAUSED => 'bg-warning-subtle text-warning border border-warning-subtle',
            self::SOLD => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            self::PENDING_REVIEW => 'bg-info-subtle text-info border border-info-subtle',
            self::DRAFT => 'bg-light text-muted border',
            self::EXPIRED => 'bg-secondary-subtle text-muted border border-secondary-subtle',
            self::REJECTED => 'bg-danger-subtle text-danger border border-danger-subtle',
        };
    }

    /**
     * Whether this listing status is publicly searchable.
     */
    public function isPublic(): bool
    {
        return $this === self::ACTIVE;
    }
}
