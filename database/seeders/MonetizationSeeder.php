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
                'name' => 'Sponsored Spotlight',
                'slug' => 'sponsored-spotlight',
                'type' => 'sponsored',
                'badge_text' => 'Sponsored',
                'badge_color' => '#f59e0b',
                'badge_icon' => 'bi-rocket-takeoff',
                'price' => 9.99,
                'point_cost' => 300,
                'duration_days' => 7,
                'description' => 'Maximum exposure on the homepage carousel and pinned at the very top of Canadian search results.',
                'features' => [
                    'Pinned at top of category search results',
                    'Prominent placement on Homepage Hero Carousel',
                    'Highlighted gold accent styling',
                    '3x more buyer inquiries on average',
                    'Valid for 7 full days',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Featured Highlight',
                'slug' => 'featured-highlight',
                'type' => 'featured',
                'badge_text' => 'Featured',
                'badge_color' => '#2563eb',
                'badge_icon' => 'bi-star-fill',
                'price' => 4.99,
                'point_cost' => 150,
                'duration_days' => 7,
                'description' => 'Stand out from standard listings with a distinguished blue verified badge and highlighted card border.',
                'features' => [
                    'Featured Blue Badge on listing cards',
                    'Top search placement above standard ads',
                    'Included in weekly category digest',
                    'Valid for 7 full days',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Instant Bump-Up',
                'slug' => 'instant-bump-up',
                'type' => 'bump_up',
                'badge_text' => 'Bumped',
                'badge_color' => '#059669',
                'badge_icon' => 'bi-arrow-up-circle-fill',
                'price' => 1.99,
                'point_cost' => 60,
                'duration_days' => 0,
                'description' => 'Instantly push your older listing back to the #1 top spot in search results as if it were just posted.',
                'features' => [
                    'Immediate refresh to the top of the search feed',
                    'Resets chronological posting freshness',
                    'Notification bump to saved users',
                    'Instant 1-click execution',
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

        // 2. Listing Quota Site Settings
        SiteSetting::updateOrCreate(
            ['key' => 'free_listing_limit_per_user'],
            [
                'value' => '5',
                'group' => 'marketplace',
                'type' => 'number',
                'description' => 'Maximum number of free active listings allowed per standard member before requiring paid promotion or listing upgrade.',
            ]
        );

        SiteSetting::updateOrCreate(
            ['key' => 'enable_listing_promotions'],
            [
                'value' => '1',
                'group' => 'marketplace',
                'type' => 'boolean',
                'description' => 'Global master switch to enable paid promotions, featured badges, and point redemptions.',
            ]
        );

        // 3. Local Canadian Banner Ads
        $banners = [
            [
                'title' => 'Maple Leaf Moving & Storage — Canadian Interprovincial Relocations',
                'position' => 'search_sidebar',
                'image_path' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=600&q=80',
                'target_url' => 'https://example.ca/movers',
                'city' => null, // Nationwide
                'province' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Montreal Pro Auto Inspection & Warranty Services',
                'position' => 'listing_detail_bottom',
                'image_path' => 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?auto=format&fit=crop&w=1200&q=80',
                'target_url' => 'https://example.ca/auto-inspection',
                'city' => 'Montréal',
                'province' => 'QC',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Toronto Heritage Home Insurance & Escrow Safety',
                'position' => 'community_sidebar',
                'image_path' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=600&q=80',
                'target_url' => 'https://example.ca/insurance',
                'city' => 'Toronto',
                'province' => 'ON',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Vancouver Eco Green Energy & Solar Solutions Canada',
                'position' => 'homepage_top',
                'image_path' => 'https://images.unsplash.com/photo-1497440001374-f26997328c1b?auto=format&fit=crop&w=1200&q=80',
                'target_url' => 'https://example.ca/clean-energy',
                'city' => 'Vancouver',
                'province' => 'BC',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Calgary & Edmonton Winter Tire & Auto Care Express',
                'position' => 'homepage_middle',
                'image_path' => 'https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?auto=format&fit=crop&w=1200&q=80',
                'target_url' => 'https://example.ca/winter-tires',
                'city' => 'Calgary',
                'province' => 'AB',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Ottawa & Gatineau Home Renovation & Certified Trades Network',
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
                ['title' => $b['title']],
                $b
            );
        }

        $this->command->info('MonetizationSeeder: Packages, Quotas, and Canadian Banner Ads seeded successfully.');
    }
}
