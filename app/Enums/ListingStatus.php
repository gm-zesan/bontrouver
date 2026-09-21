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
     * Get a human-readable display label.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_REVIEW => 'Pending Review',
            self::ACTIVE => 'Active',
            self::PAUSED => 'Paused',
            self::SOLD => 'Sold',
            self::EXPIRED => 'Expired',
            self::REJECTED => 'Rejected',
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
