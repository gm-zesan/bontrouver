<?php

namespace Database\Seeders;

use App\Models\MemberTier;
use Illuminate\Database\Seeder;

class MemberTierSeeder extends Seeder
{
    /**
     * Run the database seeds with bilingual support.
     * Client Ranking: 🥉 New Member → 🥈 Active Member → 🥇 Trusted Member → ⭐ Highly Appreciated Member
     */
    public function run(): void
    {
        $tiers = [
            [
                'name_en'        => '🥉 New Member',
                'name_fr'        => '🥉 Nouveau membre',
                'icon'           => '🥉',
                'badge_color'    => '#cd7f32',
                'badge_class'    => 'tier-badge tier-bronze',
                'min_points'     => 0,
                'max_points'     => 99,
                'description_en' => 'Entry-level tier for newly registered community members.',
                'description_fr' => 'Niveau d\'entrée pour les membres nouvellement inscrits de la communauté.',
                'perks_en'       => [
                    'Standard ad posting and search browsing',
                    'Direct messaging with verified sellers and buyers',
                    'Basic profile showcase',
                ],
                'perks_fr'       => [
                    'Publication standard d\'annonces et recherche',
                    'Messagerie directe avec vendeurs et acheteurs vérifiés',
                    'Présentation de profil de base',
                ],
            ],
            [
                'name_en'        => '🥈 Active Member',
                'name_fr'        => '🥈 Membre actif',
                'icon'           => '🥈',
                'badge_color'    => '#94a3b8',
                'badge_class'    => 'tier-badge tier-silver',
                'min_points'     => 100,
                'max_points'     => 299,
                'description_en' => 'Active community contributor who has completed verification and initial deals.',
                'description_fr' => 'Contributeur communautaire actif ayant complété la vérification et ses premières transactions.',
                'perks_en'       => [
                    'Active Member badge displayed on public listings and profile',
                    'Access to Community Companionship Meetup hosting',
                    'Priority response queue in support',
                ],
                'perks_fr'       => [
                    'Badge de Membre Actif affiché sur les annonces et le profil',
                    'Accès à l\'organisation de rencontres communautaires',
                    'File de réponse prioritaire auprès du support',
                ],
            ],
            [
                'name_en'        => '🥇 Trusted Member',
                'name_fr'        => '🥇 Membre de confiance',
                'icon'           => '🥇',
                'badge_color'    => '#eab308',
                'badge_class'    => 'tier-badge tier-gold',
                'min_points'     => 300,
                'max_points'     => 699,
                'description_en' => 'High-trust community pillar with positive feedback and mutual aid history.',
                'description_fr' => 'Pilier de confiance de la communauté avec des avis positifs et un historique d\'entraide.',
                'perks_en'       => [
                    'Gold Trusted Member badge with instant buyer confidence',
                    '10% point discount on Featured Ad listings',
                    'Smart Alert priority matching speed',
                    'Verified Community Host status for events',
                ],
                'perks_fr'       => [
                    'Badge Membre de Confiance Or inspirant une confiance immédiate',
                    'Rabais de 10% en points sur les annonces En Vedette',
                    'Rapidité prioritaire d\'association pour les Alertes Intelligentes',
                    'Statut d\'Hôte Communautaire Vérifié pour les événements',
                ],
            ],
            [
                'name_en'        => '⭐ Highly Appreciated Member',
                'name_fr'        => '⭐ Membre hautement apprécié',
                'icon'           => '⭐',
                'badge_color'    => '#8b5cf6',
                'badge_class'    => 'tier-badge tier-platinum',
                'min_points'     => 700,
                'max_points'     => null,
                'description_en' => 'Elite community leader recognized for exceptional mutual aid, reliability, and service.',
                'description_fr' => 'Leader communautaire d\'élite reconnu pour son entraide, sa fiabilité et ses services exemplaires.',
                'perks_en'       => [
                    'Elite Platinum Star badge across all ads, profile, and messages',
                    '25% point discount on all promotional upgrades',
                    'Direct moderation priority line',
                    'Permanent verified badge and spotlight visibility',
                ],
                'perks_fr'       => [
                    'Badge Étoile Platine Élite sur toutes les annonces, profils et messages',
                    'Rabais de 25% en points sur toutes les promotions d\'annonces',
                    'Ligne directe prioritaire auprès de la modération',
                    'Badge vérifié permanent et visibilité en vedette',
                ],
            ],
        ];

        foreach ($tiers as $tier) {
            MemberTier::updateOrCreate(
                ['name_en' => $tier['name_en']],
                $tier
            );
        }
    }
}
