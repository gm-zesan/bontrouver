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

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
