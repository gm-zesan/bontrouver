<?php

namespace Database\Seeders;

use App\Models\BannerAd;
use App\Models\PromotionPackage;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class MonetizationSeeder extends Seeder
{
    /**
     * Seed monetization packages, listing quota settings, and local banner ads.
     */
    public function run(): void
    {
        // 1. Seed Default Promotion Packages
        $packages = [
            [
                'name_en' => 'Sponsored Spotlight',
                'name_fr' => 'Pleins feux sponsorisés',
                'slug' => 'sponsored-spotlight',
                'type' => 'sponsored',
                'badge_text_en' => 'Sponsored',
                'badge_text_fr' => 'Sponsorisé',
                'badge_color' => '#f59e0b',
                'badge_icon' => 'bi-rocket-takeoff',
                'price' => 9.99,
                'point_cost' => 300,
                'duration_days' => 7,
                'description_en' => 'Maximum exposure on the homepage carousel and pinned at the very top of Canadian search results.',
                'description_fr' => 'Visibilité maximale sur le carrousel de la page d\'accueil et épinglé tout en haut des résultats de recherche au Canada.',
                'features_en' => [
                    'Pinned at top of category search results',
                    'Prominent placement on Homepage Hero Carousel',
                    'Highlighted gold accent styling',
                    '3x more buyer inquiries on average',
                    'Valid for 7 full days',
                ],
                'features_fr' => [
                    'Épinglé en tête des résultats de recherche par catégorie',
                    'Emplacement privilégié sur le carrousel d\'accueil',
                    'Mise en valeur élégante aux reflets dorés',
                    '3x plus de demandes d\'acheteurs en moyenne',
                    'Valide pendant 7 jours complets',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name_en' => 'Featured Highlight',
                'name_fr' => 'Mise en vedette',
                'slug' => 'featured-highlight',
                'type' => 'featured',
                'badge_text_en' => 'Featured',
                'badge_text_fr' => 'En vedette',
                'badge_color' => '#2563eb',
                'badge_icon' => 'bi-star-fill',
                'price' => 4.99,
                'point_cost' => 150,
                'duration_days' => 7,
                'description_en' => 'Stand out from standard listings with a distinguished blue verified badge and highlighted card border.',
                'description_fr' => 'Démarquez-vous des annonces standard avec un badge bleu distinctif et une bordure de carte mise en valeur.',
                'features_en' => [
                    'Featured Blue Badge on listing cards',
                    'Top search placement above standard ads',
                    'Included in weekly category digest',
                    'Valid for 7 full days',
                ],
                'features_fr' => [
                    'Badge bleu « En vedette » sur les cartes d\'annonces',
                    'Position supérieure au-dessus des annonces standard',
                    'Inclus dans la sélection hebdomadaire de la catégorie',
                    'Valide pendant 7 jours complets',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name_en' => 'Instant Bump-Up',
                'name_fr' => 'Remontée instantanée',
                'slug' => 'instant-bump-up',
                'type' => 'bump_up',
                'badge_text_en' => 'Bumped',
                'badge_text_fr' => 'Remonté',
                'badge_color' => '#059669',
                'badge_icon' => 'bi-arrow-up-circle-fill',
                'price' => 1.99,
                'point_cost' => 60,
                'duration_days' => 0,
                'description_en' => 'Instantly push your older listing back to the #1 top spot in search results as if it were just posted.',
                'description_fr' => 'Remontez instantanément votre annonce plus ancienne à la 1re place des résultats de recherche comme si elle venait d\'être publiée.',
                'features_en' => [
                    'Immediate refresh to the top of the search feed',
                    'Resets chronological posting freshness',
                    'Notification bump to saved users',
                    'Instant 1-click execution',
                ],
                'features_fr' => [
                    'Actualisation immédiate en tête du fil de recherche',
                    'Réinitialise la fraîcheur chronologique de publication',
                    'Notification de remontée aux utilisateurs ayant sauvegardé',
                    'Exécution instantanée en 1 clic',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $pkg) {
            PromotionPackage::updateOrCreate(
                ['slug' => $pkg['slug']],
                $pkg
            );
        }

        // 2. Marketplace Policy Site Settings
        SiteSetting::updateOrCreate(
            ['key' => 'enable_listing_promotions'],
            [
                'value' => '1',
                'group' => 'marketplace',
                'type' => 'boolean',
                'description' => 'Global master switch to enable optional paid promotions, featured badges, and point redemptions.',
            ]
        );

        // 3. Local Canadian Banner Ads
        $banners = [
            [
                'title_en' => 'Maple Leaf Moving & Storage — Canadian Interprovincial Relocations',
                'title_fr' => 'Déménagement & Entreposage Maple Leaf — Relocalisation interprovinciale au Canada',
                'position' => 'search_sidebar',
                'image_path' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=600&q=80',
                'target_url' => 'https://example.ca/movers',
                'city' => null, // Nationwide
                'province' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title_en' => 'Montreal Pro Auto Inspection & Warranty Services',
                'title_fr' => 'Inspection auto pro et services de garantie à Montréal',
                'position' => 'listing_detail_bottom',
                'image_path' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?auto=format&fit=crop&w=1200&q=80',
                'target_url' => 'https://example.ca/auto-inspection',
                'city' => 'Montréal',
                'province' => 'QC',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title_en' => 'Toronto Heritage Home Insurance & Escrow Safety',
                'title_fr' => 'Assurance habitation patrimoniale et sécurité séquestre à Toronto',
                'position' => 'community_sidebar',
                'image_path' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=600&q=80',
                'target_url' => 'https://example.ca/insurance',
                'city' => 'Toronto',
                'province' => 'ON',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title_en' => 'Vancouver Eco Green Energy & Solar Solutions Canada',
                'title_fr' => 'Énergie verte écologique et solutions solaires Vancouver Canada',
                'position' => 'homepage_top',
                'image_path' => 'https://images.unsplash.com/photo-1497440001374-f26997328c1b?auto=format&fit=crop&w=1200&q=80',
                'target_url' => 'https://example.ca/clean-energy',
                'city' => 'Vancouver',
                'province' => 'BC',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title_en' => 'Calgary & Edmonton Winter Tire & Auto Care Express',
                'title_fr' => 'Pneus d\'hiver et entretien automobile express à Calgary & Edmonton',
                'position' => 'homepage_middle',
                'image_path' => 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?auto=format&fit=crop&w=1200&q=80',
                'target_url' => 'https://example.ca/winter-tires',
                'city' => 'Calgary',
                'province' => 'AB',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title_en' => 'Ottawa & Gatineau Home Renovation & Certified Trades Network',
                'title_fr' => 'Rénovation domiciliaire et réseau de gens de métier certifiés à Ottawa & Gatineau',
                'position' => 'homepage_bottom',
                'image_path' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1200&q=80',
                'target_url' => 'https://example.ca/home-trades',
                'city' => 'Ottawa',
                'province' => 'ON',
                'is_active' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($banners as $b) {
            BannerAd::updateOrCreate(
                ['title_en' => $b['title_en']],
                $b
            );
        }

        $this->command->info('MonetizationSeeder: Packages, Quotas, and Canadian Banner Ads seeded successfully.');
    }
}
