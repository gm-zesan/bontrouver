<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case SUPERADMIN = 'superadmin';
    
    public function label(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match($this) {
            self::USER       => $locale === 'fr' ? 'Utilisateur' : 'User',
            self::ADMIN      => $locale === 'fr' ? 'Administrateur' : 'Admin',
            self::SUPERADMIN => $locale === 'fr' ? 'Super Administrateur' : 'Super Admin',
        };
    }
}
