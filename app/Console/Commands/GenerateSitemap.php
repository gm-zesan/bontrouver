<?php

namespace App\Console\Commands;

use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a dynamic SEO XML sitemap containing static pages, categories, cities, and active listings with multilingual hreflang annotations.';

    /**
     * Maximum URLs per sitemap file according to Google specifications.
     */
    protected const MAX_URLS_PER_SITEMAP = 50000;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting sitemap generation for Bon Trouver...');

        // Ensure URLs use HTTPS scheme
        URL::forceScheme('https');
        $baseUrl = rtrim(config('app.url', 'https://bontrouver.ca'), '/');
        if (str_starts_with($baseUrl, 'http://')) {
            $baseUrl = 'https://' . substr($baseUrl, 7);
        }

        $items = [];
        $now = Carbon::now()->toW3cString();

        // 1. Static and Hub Pages
        $staticRoutes = [
            ['path' => '/',                   'changefreq' => 'daily',   'priority' => '1.0'],
            ['path' => '/listings',           'changefreq' => 'daily',   'priority' => '0.9'],
            ['path' => '/community',          'changefreq' => 'daily',   'priority' => '0.8'],
            ['path' => '/community-connect',  'changefreq' => 'daily',   'priority' => '0.8'],
            ['path' => '/how-it-works',       'changefreq' => 'weekly',  'priority' => '0.7'],
            ['path' => '/about',              'changefreq' => 'weekly',  'priority' => '0.7'],
            ['path' => '/post-ad',            'changefreq' => 'weekly',  'priority' => '0.7'],
            ['path' => '/register',           'changefreq' => 'monthly', 'priority' => '0.6'],
            ['path' => '/member-benefits',    'changefreq' => 'weekly',  'priority' => '0.6'],
            ['path' => '/advertise',          'changefreq' => 'weekly',  'priority' => '0.6'],
            ['path' => '/promote-tools',      'changefreq' => 'weekly',  'priority' => '0.6'],
            ['path' => '/verification',       'changefreq' => 'weekly',  'priority' => '0.6'],
            ['path' => '/terms',              'changefreq' => 'monthly', 'priority' => '0.4'],
            ['path' => '/privacy',            'changefreq' => 'monthly', 'priority' => '0.4'],
            ['path' => '/posting-policy',     'changefreq' => 'monthly', 'priority' => '0.4'],
            ['path' => '/security',           'changefreq' => 'monthly', 'priority' => '0.4'],
            ['path' => '/ad-choices',         'changefreq' => 'monthly', 'priority' => '0.4'],
            ['path' => '/accessibility',      'changefreq' => 'monthly', 'priority' => '0.4'],
        ];

        foreach ($staticRoutes as $r) {
            $url = $r['path'] === '/' ? $baseUrl . '/' : $baseUrl . $r['path'];
            $items[] = [
                'loc'        => $url,
                'lastmod'    => $now,
                'changefreq' => $r['changefreq'],
                'priority'   => $r['priority'],
            ];
        }

        // 2. Active Category Pages
        $categories = Category::where('is_active', true)->whereNotNull('slug')->get();
        foreach ($categories as $cat) {
            $items[] = [
                'loc'        => $baseUrl . '/category/' . $cat->slug,
                'lastmod'    => ($cat->updated_at ?? Carbon::now())->toW3cString(),
                'changefreq' => 'daily',
                'priority'   => '0.8',
            ];
        }
        $this->info("Included {$categories->count()} active category pages.");

        // 3. Active City Hub Pages
        $cities = City::where('is_active', true)->whereNotNull('slug')->get();
        foreach ($cities as $city) {
            $items[] = [
                'loc'        => $baseUrl . '/listings?city=' . urlencode($city->slug),
                'lastmod'    => ($city->updated_at ?? Carbon::now())->toW3cString(),
                'changefreq' => 'daily',
                'priority'   => '0.8',
            ];
        }
        $this->info("Included {$cities->count()} active city pages.");

        // 4. Active Classified Listings (Exclude Expired, Draft, Rejected, Paused, Sold)
        $listings = Listing::with(['primaryImage'])
            ->active()
            ->latest('updated_at')
            ->get();

        foreach ($listings as $listing) {
            $slugOrId = $listing->slug ?: $listing->id;
            $imageUrl = null;
            if ($listing->primaryImage && !empty($listing->primaryImage->image_path)) {
                $imageUrl = $listing->primary_image_url;
            }

            $items[] = [
                'loc'        => $baseUrl . '/listing/' . $slugOrId,
                'lastmod'    => ($listing->updated_at ?? Carbon::now())->toW3cString(),
                'changefreq' => 'weekly',
                'priority'   => '0.7',
                'image'      => $imageUrl,
                'title'      => $listing->title,
            ];
        }
        $this->info("Included {$listings->count()} active listings with lastmod timestamps.");

        $totalUrls = count($items);
        $this->info("Total URLs to include in sitemap: {$totalUrls}");

        // 5. Output to Sitemap XML (Handle Sitemap Index if > 50,000)
        if ($totalUrls <= self::MAX_URLS_PER_SITEMAP) {
            $xmlContent = $this->buildUrlsetXml($items);
            $this->saveSitemapFiles('sitemap.xml', $xmlContent);
        } else {
            $chunks = array_chunk($items, self::MAX_URLS_PER_SITEMAP);
            $sitemapIndexEntries = [];

            foreach ($chunks as $index => $chunk) {
                $chunkFilename = 'sitemap-' . ($index + 1) . '.xml';
                $chunkXml = $this->buildUrlsetXml($chunk);
                $this->saveSitemapFiles($chunkFilename, $chunkXml);

                $sitemapIndexEntries[] = [
                    'loc'     => $baseUrl . '/' . $chunkFilename,
                    'lastmod' => $now,
                ];
            }

            $indexXmlContent = $this->buildSitemapIndexXml($sitemapIndexEntries);
            $this->saveSitemapFiles('sitemap.xml', $indexXmlContent);
        }

        $this->info('Sitemap generated and saved successfully to public/sitemap.xml and root sitemap.xml.');
        return Command::SUCCESS;
    }

    /**
     * Build standard Google <urlset> XML.
     */
    protected function buildUrlsetXml(array $items): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml[] = '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

        foreach ($items as $item) {
            $loc = htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8');

            $xml[] = '  <url>';
            $xml[] = "    <loc>{$loc}</loc>";
            $xml[] = "    <lastmod>{$item['lastmod']}</lastmod>";
            $xml[] = "    <changefreq>{$item['changefreq']}</changefreq>";
            $xml[] = "    <priority>{$item['priority']}</priority>";

            if (!empty($item['image'])) {
                $imgLoc = htmlspecialchars($item['image'], ENT_XML1, 'UTF-8');
                $imgTitle = !empty($item['title']) ? htmlspecialchars(strip_tags($item['title']), ENT_XML1, 'UTF-8') : '';
                $xml[] = '    <image:image>';
                $xml[] = "      <image:loc>{$imgLoc}</image:loc>";
                if ($imgTitle) {
                    $xml[] = "      <image:title>{$imgTitle}</image:title>";
                }
                $xml[] = '    </image:image>';
            }

            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return implode("\n", $xml);
    }

    /**
     * Build standard Google <sitemapindex> XML.
     */
    protected function buildSitemapIndexXml(array $sitemaps): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($sitemaps as $sm) {
            $loc = htmlspecialchars($sm['loc'], ENT_XML1, 'UTF-8');
            $xml[] = '  <sitemap>';
            $xml[] = "    <loc>{$loc}</loc>";
            $xml[] = "    <lastmod>{$sm['lastmod']}</lastmod>";
            $xml[] = '  </sitemap>';
        }

        $xml[] = '</sitemapindex>';

        return implode("\n", $xml);
    }

    /**
     * Save the sitemap content to both public/ and root directory.
     */
    protected function saveSitemapFiles(string $filename, string $content): void
    {
        $publicPath = public_path($filename);
        $rootPath = base_path($filename);

        File::put($publicPath, $content);
        File::put($rootPath, $content);
    }
}

