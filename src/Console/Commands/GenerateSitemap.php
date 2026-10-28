<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Uiaciel\SuryaCms\Events\SitemapGenerating;
use Uiaciel\SuryaCms\Models\Category;
use Uiaciel\SuryaCms\Models\Menu;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Models\Setting;
use Uiaciel\SuryaCms\Models\SiteDomain;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap files for the configured site or domains';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info(config('suryacms.multisite.enabled', false)
            ? 'Generating domain-specific sitemap files...'
            : 'Generating sitemap.xml...');

        if (config('suryacms.multisite.enabled', false)) {
            return $this->generateMultisiteSitemaps();
        }

        $this->writeSitemap(URL::to('/'), public_path('sitemap.xml'));

        $this->info('✅ sitemap.xml generated successfully.');

        return self::SUCCESS;
    }

    private function generateMultisiteSitemaps(): int
    {
        if (! Schema::hasTable('site_domains') || ! Schema::hasTable('sites')) {
            $this->error('Multisite is enabled but site domain mappings are not available. Run the package migrations first.');

            return self::FAILURE;
        }

        $domains = SiteDomain::query()->with('site')->orderBy('domain')->get();
        if ($domains->isEmpty()) {
            $this->error('No multisite domains are configured; no sitemap was generated.');

            return self::FAILURE;
        }

        $generated = 0;

        foreach ($domains as $domain) {
            if (! $domain->site) {
                continue;
            }

            $previousBindings = [];
            foreach (['suryacms.current_site', 'suryacms.current_domain', 'current.setting'] as $binding) {
                if (app()->bound($binding)) {
                    $previousBindings[$binding] = app($binding);
                }
            }

            app()->instance('suryacms.current_site', $domain->site);
            app()->instance('suryacms.current_domain', $domain);

            try {
                $setting = Setting::query()->first();
                $scheme = parse_url((string) $setting?->url, PHP_URL_SCHEME)
                    ?: parse_url((string) config('app.url'), PHP_URL_SCHEME)
                    ?: 'https';
                $baseUrl = $scheme.'://'.$domain->domain;
                $filenameDomain = preg_replace('/[^a-z0-9.-]/i', '-', $domain->domain);
                $path = public_path($filenameDomain.'.xml');

                $this->writeSitemap($baseUrl, $path, true);
                $this->info("✅ {$filenameDomain}.xml generated successfully.");
                $generated++;
            } finally {
                foreach (['suryacms.current_site', 'suryacms.current_domain', 'current.setting'] as $binding) {
                    if (isset($previousBindings[$binding])) {
                        app()->instance($binding, $previousBindings[$binding]);
                    } else {
                        app()->forgetInstance($binding);
                    }
                }
            }
        }

        if ($generated === 0) {
            $this->error('No sitemap was generated because no domain has a valid site mapping.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function writeSitemap(string $baseUrl, string $path, bool $multisite = false): void
    {
        $urls = [];
        $now = Carbon::now()->toAtomString();

        // Homepage
        $urls[] = [
            'loc' => rtrim($baseUrl, '/').'/',
            'lastmod' => $now,
            'priority' => '1.0',
        ];

        // Pages
        foreach (Page::where('status', 'Publish')->get() as $page) {
            $urls[] = [
                'loc' => rtrim($baseUrl, '/').'/'.$page->slug,
                'lastmod' => Carbon::parse($page->updated_at)->toAtomString(),
                'priority' => '0.8',
            ];
        }

        // Posts
        foreach (Post::where('status', 'Publish')->get() as $post) {
            $urls[] = [
                'loc' => rtrim($baseUrl, '/').'/media/'.$post->slug,
                'lastmod' => Carbon::parse($post->updated_at)->toAtomString(),
                'priority' => '0.7',
            ];
        }

        // Categories
        foreach (Category::all() as $cat) {
            $urls[] = [
                'loc' => rtrim($baseUrl, '/').'/category/'.$cat->slug,
                'lastmod' => $now,
                'priority' => '0.6',
            ];
        }

        if ($multisite) {
            foreach (Menu::all() as $menu) {
                $scheme = parse_url($menu->link, PHP_URL_SCHEME);
                $menuUrl = $scheme
                    ? $menu->link
                    : rtrim($baseUrl, '/').'/'.ltrim($menu->link, '/');

                $urls[] = [
                    'loc' => $menuUrl,
                    'lastmod' => Carbon::parse($menu->updated_at)->toAtomString(),
                    'priority' => '0.5',
                ];
            }

            $sitemapUrls = collect($urls);
            event(new SitemapGenerating($sitemapUrls));
            $xml = view('suryacms::livewire.admin.sitemap', ['urls' => $sitemapUrls->all()])->render();
        } else {
            $xml = view('sitemap.xml', compact('urls'))->render();
        }

        if (! File::put($path, $xml)) {
            throw new \RuntimeException("Unable to write sitemap file: {$path}");
        }
    }
}
