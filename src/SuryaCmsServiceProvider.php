<?php

namespace Uiaciel\SuryaCms;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Symfony\Component\Finder\Finder;
use Uiaciel\SuryaCms\Http\Middleware\CheckMaintenance;
use Uiaciel\SuryaCms\Http\Middleware\SetLocaleFromUrl; // ← TAMBAHKAN INI
use Uiaciel\SuryaCms\Models\Category;
use Uiaciel\SuryaCms\Models\Contact;
use Uiaciel\SuryaCms\Models\Gallery;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Menu;
use Uiaciel\SuryaCms\Models\Page;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Models\Setting;
use Uiaciel\SuryaCms\Models\YoutubeVideo;

class SuryaCmsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $helperPath = __DIR__.'/helpers.php';
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }

        $this->bootTheme();
        $this->registerMiddleware();

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'suryacms');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->registerCommands();
        $this->publishAssets();

        if (class_exists(Livewire::class)) {
            $this->registerLivewireComponents();
        }

        $this->shareViewData();

        // Register admin menu for Themes
        if (function_exists('register_admin_menupackage')) {
            register_admin_menupackage('Themes', [
                [
                    'label' => 'All Themes',
                    'icon' => 'bi bi-easel2-fill',
                    'route' => '/admin/themes',
                ],
                [
                    'label' => 'Editor',
                    'icon' => 'bi bi-code',
                    'route' => '/admin/themes/editor',
                ],
                [
                    'label' => 'Generate',
                    'icon' => 'bi bi-palette2',
                    'route' => '/admin/themes/generate',
                ],

            ]);
        }
    }

    public function register(): void {}

    private function registerLivewireComponents(): void
    {
        $packageNamespace = 'suryacms';
        $componentDir = __DIR__.'/Http/Livewire';
        $baseNamespace = 'Uiaciel\\SuryaCms\\Http\\Livewire\\';

        $filesystem = new Filesystem;

        if (! $filesystem->isDirectory($componentDir)) {
            return;
        }

        foreach ($filesystem->allFiles($componentDir) as $file) {
            $relativePath = str_replace('.php', '', $file->getRelativePathname());

            $class = $baseNamespace.str_replace(['/', '\\'], '\\', $relativePath);

            $parts = array_map(
                fn ($part) => \Illuminate\Support\Str::kebab($part),
                explode('\\', str_replace('/', '\\', $relativePath))
            );
            $alias = $packageNamespace.'::'.implode('.', $parts);

            Livewire::component($alias, $class);
        }
    }

    private function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);

        // Register maintenance middleware
        $router->aliasMiddleware('suryacms.maintenance', CheckMaintenance::class);

        // Register locale middleware
        $router->aliasMiddleware('suryacms.locale', SetLocaleFromUrl::class); // ← TAMBAHKAN INI
    }

    private function bootTheme(): void
    {

        $activeTheme = config('frontend.active', 'default');

        try {
            if (Schema::hasTable('settings')) {
                $dbTheme = Setting::value('active_theme');
                if ($dbTheme) {
                    $activeTheme = $dbTheme;
                }
            }
        } catch (\Exception $e) {

        }

        $themesPath = config('frontend.themes_path', 'frontend');

        $localThemePath = resource_path('views/'.$themesPath.'/'.$activeTheme);

        if (is_dir($localThemePath)) {
            $this->loadViewsFrom($localThemePath, 'frontend');
        }

        $publishedThemePath = resource_path($themesPath.'/'.$activeTheme);
        if (file_exists($publishedThemePath)) {
            View::addNamespace('frontend', $publishedThemePath);
        } else {
            $packageThemePath = __DIR__.'/../resources/views/frontend/'.$activeTheme;
            if (file_exists($packageThemePath)) {
                View::addNamespace('frontend', $packageThemePath);
            }
        }

        Blade::directive('theme_asset', function ($path) use ($themesPath) {
            return "<?php echo asset('{$themesPath}/' . {$path}); ?>";
        });

        if (! function_exists('getActiveTheme')) {
            function getActiveTheme()
            {
                return config('frontend.active');
            }
        }
    }

    private function registerCommands(): void
    {
        $commandPath = __DIR__.'/Console/Commands';
        $namespace = 'Uiaciel\\SuryaCms\\Console\\Commands\\';

        if (! is_dir($commandPath)) {
            return;
        }

        $commands = [];
        foreach ((new Finder)->in($commandPath)->files()->name('*.php') as $command) {
            $commandClass = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                $command->getRelativePathname()
            );

            $commands[] = $commandClass;
        }

        $this->commands($commands);
    }

    private function publishAssets(): void
    {

        $this->publishes([
            __DIR__.'/../config/frontend.php' => config_path('frontend.php'),
        ], 'suryacms-frontend-config');

        $this->publishes([
            __DIR__.'/../resources/views/frontend' => resource_path('views/frontend'),
        ], 'suryacms-frontend-views');

        $this->publishes([
            __DIR__.'/../public/frontend' => public_path('frontend'),
        ], 'suryacms-frontend-assets');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'suryacms-migrations');
    }

    private function shareViewData(): void
    {
        View::composer('*', function ($view) {
            try {
                $lang = Language::all();
                $categories = Category::all();
                $galleries = Gallery::where('status', 'Publish')
                    ->orderBy('created_at', 'desc')
                    ->where('category', 'gallery')
                    ->take(8)
                    ->get();

                $contact = Contact::orderBy('created_at', 'desc')->get();
                $posts = Post::with('category')->where('status', 'Publish')->get();
                $menus = Menu::whereNull('parent_id')
                    ->with(['children' => function ($query) {
                        $query->orderBy('order');
                    }])
                    ->orderBy('order')
                    ->get();

                $latestposts = Post::where('status', 'Publish')
                    ->orderBy('created_at', 'desc')
                    ->take(6)
                    ->get();

                $topposts = Post::where('status', 'Publish')
                    ->orderBy('view', 'desc')
                    ->take(5)
                    ->get();

                $youtubes = YoutubeVideo::where('status', 'published')
                    ->orderBy('created_at', 'desc')
                    ->take(6)
                    ->get();

                $sliders = Gallery::where('status', 'Publish')
                    ->orderBy('created_at', 'desc')
                    ->where('category', 'Slider')
                    ->take(8)
                    ->get();

                $allTags = Post::where('status', 'Publish')
                    ->pluck('tags')
                    ->filter()
                    ->toArray();

                $pages = Page::where('status', 'Publish')
                    ->get();

                $tags = [];
                foreach ($allTags as $tagString) {
                    $tags = array_merge($tags, array_map('trim', explode(',', $tagString)));
                }

                $tagCounts = array_count_values($tags);
                arsort($tagCounts);
                $tagcloud = collect($tagCounts)->take(20);

                $sharedData = [
                    'menus' => $menus,
                    'categories' => $categories,
                    'posts' => $posts,
                    'latestposts' => $latestposts,
                    'topposts' => $topposts,
                    'tagcloud' => $tagcloud,
                    'youtubes' => $youtubes,
                    'sliders' => $sliders,
                    'pages' => $pages,
                    'setting' => null,
                    'lang' => $lang,
                    'categories' => $categories,
                    'gallery' => $galleries,
                    'contacts' => $contact,
                ];

                // Get settings if table exists
                if (Schema::hasTable('settings')) {
                    try {
                        $sharedData['setting'] = Setting::first();
                    } catch (\Exception $e) {
                        // Silently fail if settings cannot be fetched
                    }
                }

                $view->with($sharedData);
            } catch (\Exception $e) {
                // Silently fail during service provider boot
            }
        });
    }
}
