<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key'         => 'site_name',
                'value'       => 'Bon Trouver',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Platform name displayed across title tags, emails, and header.',
            ],
            [
                'key'         => 'site_tagline',
                'value'       => "Canada's Trusted Local Classifieds & Community Hub",
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Short brand slogan for hero headers and meta descriptions.',
            ],
            [
                'key'         => 'contact_email',
                'value'       => 'support@bontrouver.ca',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Public customer care and support email.',
            ],
            [
                'key'         => 'contact_phone',
                'value'       => '+1 (800) 555-0199',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Public toll-free or customer contact phone number.',
            ],
            [
                'key'         => 'contact_address',
                'value'       => '1000 Rue de la Gauchetière O, Montréal, QC H3B 4W5, Canada',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Physical headquarters or corporate address.',
            ],
            [
                'key'         => 'footer_copyright',
                'value'       => '© 2026 Bon Trouver Inc. All rights reserved.',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Copyright notice displayed in platform footer.',
            ],

            // Branding Assets
            [
                'key'         => 'site_logo_light',
                'value'       => null,
                'group'       => 'branding',
                'type'        => 'file',
                'description' => 'Primary platform logo for light backgrounds.',
            ],
            [
                'key'         => 'site_logo_dark',
                'value'       => null,
                'group'       => 'branding',
                'type'        => 'file',
                'description' => 'Secondary platform logo for dark navigation bars.',
            ],
            [
                'key'         => 'site_favicon',
                'value'       => null,
                'group'       => 'branding',
                'type'        => 'file',
                'description' => 'Browser tab favicon icon (PNG, ICO, SVG).',
            ],
            [
                'key'         => 'og_default_image',
                'value'       => null,
                'group'       => 'branding',
                'type'        => 'file',
                'description' => 'Default social media sharing OpenGraph preview banner.',
            ],

            // SEO & Social Metadata (Strictly Tailored for Canada)
            [
                'key'         => 'meta_title',
                'value'       => 'Bon Trouver — Canadian Classifieds & Local Community Hub',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'Default global browser title tag for Canadian marketplace.',
            ],
            [
                'key'         => 'meta_description',
                'value'       => 'Buy, sell, rent, and discover deals, jobs, services, and mutual aid meetups across Canadian cities on Bon Trouver.',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'Default meta description for Canadian search engine ranking.',
            ],
            [
                'key'         => 'meta_keywords',
                'value'       => 'classifieds canada, canadian marketplace, buy sell montreal, toronto rentals, vancouver cars, calgary jobs, canadian community',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'Canadian marketplace meta keywords separated by comma.',
            ],
            [
                'key'         => 'geo_region',
                'value'       => 'CA',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'ISO 3166-2 regional code for Canada (e.g., CA, CA-QC, CA-ON, CA-BC).',
            ],
            [
                'key'         => 'geo_placename',
                'value'       => 'Canada',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'Target Canadian geographical placename.',
            ],
            [
                'key'         => 'geo_position',
                'value'       => '45.5017;-73.5673',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'Default geographic latitude;longitude coordinates for Canadian indexing.',
            ],
            [
                'key'         => 'social_twitter',
                'value'       => 'https://twitter.com/bontrouver',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'Official X / Twitter profile URL.',
            ],
            [
                'key'         => 'social_facebook',
                'value'       => 'https://facebook.com/bontrouver',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'Official Facebook page URL.',
            ],
            [
                'key'         => 'social_instagram',
                'value'       => 'https://instagram.com/bontrouver',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'Official Instagram handle URL.',
            ],
            [
                'key'         => 'social_linkedin',
                'value'       => 'https://linkedin.com/company/bontrouver',
                'group'       => 'seo',
                'type'        => 'string',
                'description' => 'Official LinkedIn organization URL.',
            ],

            // Marketplace & Moderation Rules
            [
                'key'         => 'auto_approve_listings',
                'value'       => '1',
                'group'       => 'marketplace',
                'type'        => 'boolean',
                'description' => 'Automatically publish new listings without manual admin review queue.',
            ],
            [
                'key'         => 'max_images_per_listing',
                'value'       => '10',
                'group'       => 'marketplace',
                'type'        => 'integer',
                'description' => 'Maximum allowed photos per classified ad.',
            ],
            [
                'key'         => 'max_upload_size_mb',
                'value'       => '10',
                'group'       => 'marketplace',
                'type'        => 'integer',
                'description' => 'Maximum upload file size limit in megabytes.',
            ],
            [
                'key'         => 'listing_expiry_days',
                'value'       => '60',
                'group'       => 'marketplace',
                'type'        => 'integer',
                'description' => 'Number of days before an active listing expires.',
            ],
            [
                'key'         => 'free_listings_limit',
                'value'       => '50',
                'group'       => 'marketplace',
                'type'        => 'integer',
                'description' => 'Free active listings allowed simultaneously per standard member.',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        SiteSetting::flushCache();
    }
}
