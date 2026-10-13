<?php

namespace App\Enums;

enum CompanionshipType: string
{
    case COFFEE = 'Coffee & Chat';
    case WALK = 'Walk & Nature';
    case DINING = 'Dining & Foodies';
    case CINEMA = 'Cinema & Arts';
    case MATCH = 'Watch a Match';
    case SPORTS = 'Sports & Active';
    case GAMING = 'Board Games & Gaming';
    case OUTING = 'Outings & Nightlife';
    case MEET_PEOPLE = 'Meet New People';
    case ENTREPRENEUR = 'Business & Entrepreneurs';
    case PASSENGER_PARCEL = 'Passenger & Parcel Service';

    public function label(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($this) {
            self::COFFEE           => $locale === 'fr' ? 'Café et discussion' : 'Coffee & Chat',
            self::WALK             => $locale === 'fr' ? 'Promenade et nature' : 'Walk & Nature',
            self::DINING           => $locale === 'fr' ? 'Repas et gastronomie' : 'Dining & Foodies',
            self::CINEMA           => $locale === 'fr' ? 'Cinéma et arts' : 'Cinema & Arts',
            self::MATCH            => $locale === 'fr' ? 'Regarder un match' : 'Watch a Match',
            self::SPORTS           => $locale === 'fr' ? 'Sports et activités' : 'Sports & Active',
            self::GAMING           => $locale === 'fr' ? 'Jeux de société et jeux vidéo' : 'Board Games & Gaming',
            self::OUTING           => $locale === 'fr' ? 'Sorties et vie nocturne' : 'Outings & Nightlife',
            self::MEET_PEOPLE      => $locale === 'fr' ? 'Faire de nouvelles rencontres' : 'Meet New People',
            self::ENTREPRENEUR     => $locale === 'fr' ? 'Affaires et entrepreneurs' : 'Business & Entrepreneurs',
            self::PASSENGER_PARCEL => $locale === 'fr' ? 'Service passagers et colis' : 'Passenger & Parcel Service',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
