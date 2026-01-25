<?php

namespace Uiaciel\SuryaCms\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Uiaciel\SuryaCms\Models\Category;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Generating sitemap.xml...');

        $urls = [];
        $now = Carbon::now()->toAtomString();

        // Homepage
        $urls[] = [
            'loc' => URL::to('/'),
            'lastmod' => $now,
            'priority' => '1.0',
        ];

        // Pages
        foreach (Page::where('status', 'Publish')->get() as $page) {
            $urls[] = [
                'loc' => URL::to('/'.$page->slug),
                'lastmod' => Carbon::parse($page->updated_at)->toAtomString(),
                'priority' => '0.8',
            ];
        }

        // Posts
        foreach (Post::where('status', 'Publish')->get() as $post) {
            $urls[] = [
                'loc' => URL::to('/media/'.$post->slug),
                'lastmod' => Carbon::parse($post->updated_at)->toAtomString(),
                'priority' => '0.7',
            ];
        }

        // Categories
        foreach (Category::all() as $cat) {
            $urls[] = [
                'loc' => URL::to('/category/'.$cat->slug),
                'lastmod' => $now,
                'priority' => '0.6',
            ];
        }

        $xml = view('sitemap.xml', compact('urls'))->render();

        File::put(public_path('sitemap.xml'), $xml);

        $this->info('✅ sitemap.xml generated successfully.');
    }
}
