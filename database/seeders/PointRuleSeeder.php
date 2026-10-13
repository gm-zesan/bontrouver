<?php

namespace Database\Seeders;

use App\Models\PointRule;
use Illuminate\Database\Seeder;

class PointRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // Earning Rules
            [
                'rule_key'       => 'identity_verification',
                'name_en'        => 'Identity Verification Approved',
                'name_fr'        => 'Vérification d\'identité approuvée',
                'type'           => 'earn',
                'points'         => 50,
                'category'       => 'Trust & Verification',
                'description_en' => 'Awarded when admin approves official government ID verification.',
                'description_fr' => 'Attribué lorsque l\'administrateur approuve la vérification de pièce d\'identité officielle.',
                'sort_order'     => 1,
            ],
            [
                'rule_key'       => 'verified_dealer',
                'name_en'        => 'Business / Dealer License Verified',
                'name_fr'        => 'Permis d\'entreprise / commerçant vérifié',
                'type'           => 'earn',
                'points'         => 100,
                'category'       => 'Trust & Verification',
                'description_en' => 'Awarded for licensed dealerships and verified business profiles.',
                'description_fr' => 'Attribué pour les concessionnaires agréés et les profils d\'entreprise vérifiés.',
                'sort_order'     => 2,
            ],
            [
                'rule_key'       => 'positive_review',
                'name_en'        => 'Receiving 5-Star Buyer/Seller Review',
                'name_fr'        => 'Réception d\'un avis 5 étoiles acheteur/vendeur',
                'type'           => 'earn',
                'points'         => 20,
                'category'       => 'Reputation & Feedback',
                'description_en' => 'Awarded for high-quality transaction ratings (4-5 stars).',
                'description_fr' => 'Attribué pour les évaluations de transactions de haute qualité (4-5 étoiles).',
                'sort_order'     => 3,
            ],
            [
                'rule_key'       => 'free_listing',
                'name_en'        => 'Giving Away a Free / Donated Item',
                'name_fr'        => 'Don d\'un article gratuit',
                'type'           => 'earn',
                'points'         => 25,
                'category'       => 'Mutual Aid & Giving',
                'description_en' => 'Rewarded for giving items to neighbors at $0 free of charge.',
                'description_fr' => 'Récompensé pour avoir offert des articles aux voisins à 0 $ gratuitement.',
                'sort_order'     => 4,
            ],
            [
                'rule_key'       => 'meetup_host',
                'name_en'        => 'Hosting a Companionship Meetup',
                'name_fr'        => 'Organisation d\'une rencontre amicale',
                'type'           => 'earn',
                'points'         => 30,
                'category'       => 'Community Activities',
                'description_en' => 'Awarded when hosting a social meetup with confirmed attendees.',
                'description_fr' => 'Attribué lors de l\'organisation d\'une rencontre sociale avec des participants confirmés.',
                'sort_order'     => 5,
            ],
            [
                'rule_key'       => 'meetup_attendee',
                'name_en'        => 'Attending a Community Meetup',
                'name_fr'        => 'Participation à une rencontre communautaire',
                'type'           => 'earn',
                'points'         => 15,
                'category'       => 'Community Activities',
                'description_en' => 'Rewarded for attending and participating in social gatherings.',
                'description_fr' => 'Récompensé pour la présence et la participation à des rassemblements sociaux.',
                'sort_order'     => 6,
            ],
            [
                'rule_key'       => 'first_deal',
                'name_en'        => 'First Verified Marketplace Deal',
                'name_fr'        => 'Première transaction vérifiée sur la plateforme',
                'type'           => 'earn',
                'points'         => 25,
                'category'       => 'Marketplace Activity',
                'description_en' => 'Bonus awarded upon successfully completing first transaction.',
                'description_fr' => 'Bonus accordé lors de la réalisation réussie de la première transaction.',
                'sort_order'     => 7,
            ],

            // Spending Rules
            [
                'rule_key'       => 'featured_promotion',
                'name_en'        => 'Featured Listing Placement (7 Days)',
                'name_fr'        => 'Placement d\'annonce en vedette (7 jours)',
                'type'           => 'spend',
                'points'         => 100,
                'category'       => 'Ad Visibility',
                'description_en' => 'Reduces point balance to feature ad at top of category results.',
                'description_fr' => 'Déduit des points pour afficher l\'annonce en haut des résultats de catégorie.',
                'sort_order'     => 8,
            ],
            [
                'rule_key'       => 'sponsored_promotion',
                'name_en'        => 'Hero Carousel Spotlight (7 Days)',
                'name_fr'        => 'Mise en avant carrousel d\'accueil (7 jours)',
                'type'           => 'spend',
                'points'         => 300,
                'category'       => 'Ad Visibility',
                'description_en' => 'Reduces point balance for homepage hero carousel banner exposure.',
                'description_fr' => 'Déduit des points pour une visibilité dans le carrousel principal de la page d\'accueil.',
                'sort_order'     => 9,
            ],
        ];

        foreach ($rules as $rule) {
            PointRule::updateOrCreate(
                ['rule_key' => $rule['rule_key']],
                $rule
            );
        }
    }
}
