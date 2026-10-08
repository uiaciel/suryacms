<?php

use Composer\InstalledVersions;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Setting;
use Uiaciel\SuryaCms\Models\Post;
use Uiaciel\SuryaCms\Models\Page;
use Illuminate\Support\Str;

if (! function_exists('register_admin_menupackage')) {
    /**
     * Check menu is enabled.
     */
    function register_admin_menupackage($packageName, array $menus)
    {
        // Ambil menu yang sudah ada
        $registered = app()->bound('suryacms.admin.menus')
            ? app('suryacms.admin.menus')
            : [];

        // Tambah menu baru
        $registered[$packageName] = $menus;

        // Simpan kembali
        app()->instance('suryacms.admin.menus', $registered);
    }
}

if (! function_exists('setting')) {

    /**
     * Helper untuk mengambil setting dengan caching yang aman
     */
    function setting()
    {
        // PRIORITAS CONTEXT AKTIF
        if (app()->bound('current.setting')) {
            return app('current.setting');
        }

        try {
            return Schema::hasTable('settings')
                ? Setting::first()
                : null;
        } catch (Exception $e) {
            return null;
        }
    }
}

if (! function_exists('get_cms_setting')) {
    /**
     * Get CMS setting dengan caching yang aman
     */
    function get_cms_setting()
    {
        try {
            if (Schema::hasTable('settings')) {
                return Setting::first();
            }
        } catch (Exception $e) {
            Log::error('Error fetching CMS settings: '.$e->getMessage());
        }

        return null;
    }
}

if (! function_exists('active_languages')) {
    /**
     * Get all active language codes
     */
    function active_languages()
    {
        try {
            return Language::where('status', 'Publish')
                ->pluck('code')
                ->toArray();
        } catch (Exception $e) {
            Log::error('Error fetching active languages: '.$e->getMessage());

            return [];
        }
    }
}

if (! function_exists('default_locale')) {
    /**
     * Get default locale from config or settings
     */
    function default_locale()
    {
        $setting = get_cms_setting();

        return $setting->language ?? config('app.locale', 'id');
    }
}

if (! function_exists('is_multilingual')) {
    /**
     * Check if multilingual mode is enabled
     */
    function is_multilingual()
    {
        $setting = get_cms_setting();

        return $setting && $setting->is_multilingual === 'Yes';
    }
}

if (! function_exists('current_locale')) {
    /**
     * Get current locale dari session atau app atau config
     */
    function current_locale()
    {
        return session('locale') ?? app()->getLocale() ?? default_locale();
    }
}

if (! function_exists('lang_route')) {
    /**
     * Generate route URL dengan SEO-friendly language handling
     *
     * Default language: tanpa prefix (eg: /page, /category/slug)
     * Non-default languages: dengan prefix (eg: /en/page, /en/category/slug)
     *
     * @param  string  $name  Route name
     * @param  array|string  $params  Route parameters
     * @return string Route URL
     *
     * @example
     * lang_route('frontend.post.show', 'my-article')      // /media/my-article (id) atau /en/media/my-article (en)
     * lang_route('frontend.category.show', ['slug' => 'tech'])
     * lang_route('frontend.contact')
     */
    function lang_route($name, $params = [])
    {
        // Jika parameter tunggal (string slug), ubah jadi array
        if (is_string($params)) {
            $params = ['slug' => $params];
        }

        if (! is_array($params)) {
            $params = [];
        }

        $locale = current_locale();
        $defaultLocale = default_locale();

        // Jika multilingual aktif
        if (is_multilingual()) {
            // Jika current locale == default locale, jangan tambah lang parameter (SEO-friendly)
            if ($locale === $defaultLocale) {
                try {
                    return route($name, $params);
                } catch (Exception $e) {
                    Log::warning("Route not found: {$name}");

                    return url('/');
                }
            } else {
                // Jika bukan default, tambahkan lang parameter
                $params['lang'] = $locale;
                try {
                    return route($name, $params);
                } catch (Exception $e) {
                    Log::warning("Route not found: {$name}");

                    return url('/');
                }
            }
        }

        // Jika single language, gunakan route biasa
        try {
            return route($name, $params);
        } catch (Exception $e) {
            Log::warning("Route not found: {$name}");

            return url('/');
        }
    }
}

if (! function_exists('url_with_lang')) {
    /**
     * Generate URL dengan SEO-friendly language handling
     *
     * Default language: tanpa prefix
     * Non-default: dengan prefix
     *
     * @param  string  $path  Path tanpa language prefix (misal: '/about', 'contact')
     * @param  string|null  $lang  Language code, default dari session/config
     * @return string Full URL dengan atau tanpa prefix
     *
     * @example
     * url_with_lang('/about')        // /about (id) atau /en/about (en)
     * url_with_lang('contact', 'en') // /en/contact
     */
    function url_with_lang($path = '', $lang = null)
    {
        if (! is_multilingual()) {
            return url($path);
        }

        $lang = $lang ?? current_locale();
        $defaultLocale = default_locale();
        $path = ltrim($path, '/');

        // Jika default locale, jangan tambah prefix (SEO-friendly)
        if ($lang === $defaultLocale) {
            return url("/{$path}");
        }

        // Jika bukan default, tambahkan prefix
        if (empty($path)) {
            return url("/{$lang}");
        }

        return url("/{$lang}/{$path}");
    }
}

if (! function_exists('switch_locale_url')) {
    /**
     * Generate URL untuk switching locale, maintain current path
     *
     * SEO-friendly: default language tanpa prefix, non-default dengan prefix
     *
     * @param  string  $newLocale  Language code untuk switch ke
     * @return string URL dengan locale baru
     *
     * @example
     * // Current: /en/about -> switch_locale_url('id') -> /about
     * // Current: /about -> switch_locale_url('en') -> /en/about
     */
    function switch_locale_url($newLocale)
    {
        if (! is_multilingual()) {
            return url()->current();
        }

        $currentUrl = request()->path();
        $currentLocale = current_locale();
        $defaultLocale = default_locale();

        // Extract path tanpa language prefix
        if ($currentLocale !== $defaultLocale && str_starts_with($currentUrl, $currentLocale.'/')) {
            // Current locale punya prefix: /en/about -> extract path /about
            $path = substr($currentUrl, strlen($currentLocale) + 1);
        } elseif ($currentLocale !== $defaultLocale && $currentUrl === $currentLocale) {
            // Current locale adalah prefix saja: /en -> path kosong
            $path = '';
        } else {
            // Default locale atau sudah tidak punya prefix
            $path = $currentUrl;
        }

        // Build new URL
        if ($newLocale === $defaultLocale) {
            // Switch ke default language: jangan tambah prefix
            if (empty($path)) {
                return url('/');
            }

            return url("/{$path}");
        } else {
            // Switch ke non-default language: tambah prefix
            if (empty($path)) {
                return url("/{$newLocale}");
            }

            return url("/{$newLocale}/{$path}");
        }
    }
}

if (! function_exists('get_date_format')) {

    function get_date_format()
    {
        return Setting::first()->date_format ?? 'd/m/Y';
    }
}

if (! function_exists('formatDate')) {
    /**
     * Memformat string tanggal atau objek Carbon sesuai dengan setting aplikasi.
     *
     * @param  string|\Carbon\Carbon|null  $date
     * @return string|null
     */
    function formatDate($date)
    {
        // Mengambil format dari helper di atas
        $dateFormat = get_date_format();

        if (is_null($date)) {
            return null;
        }

        try {
            if (is_string($date)) {
                $date = Carbon::parse($date);
            }

            // Menggunakan translatedFormat() untuk dukungan bahasa dan format
            return $date->translatedFormat($dateFormat);
        } catch (Exception $e) {
            // Log error jika parsing gagal
            Log::error('Error formatting date: '.$e->getMessage(), ['date' => $date]);

            return null;
        }
    }
}

if (! function_exists('text')) {
    /**
     * Helper untuk menampilkan teks sesuai session locale.
     */
    function text(string $idText, string $enText): string
    {
        return session('locale', 'id') === 'id' ? $idText : $enText;
    }
}

// list helpers untuk tema

if (! function_exists('get_active_theme')) {
    function get_active_theme(): string
    {
        static $activeTheme = null;

        if ($activeTheme !== null) {
            return $activeTheme;
        }

        try {
            if (Schema::hasTable('settings')) {
                // Mengambil dari model Setting atau DB
                $activeTheme = \Uiaciel\SuryaCms\Models\Setting::value('active_theme')
                    ?? config('frontend.active', 'default');
            } else {
                $activeTheme = config('frontend.active', 'default');
            }
        } catch (\Exception $e) {
            $activeTheme = config('frontend.active', 'default');
        }

        return $activeTheme;
    }
}

if (! function_exists('theme_view')) {
    /**
     * Memanggil view berdasarkan tema yang sedang aktif.
     * Contoh penggunaan: theme_view('homepage') atau theme_view('page.show')
     *
     * @return View|Factory
     */
    function theme_view(string $view, array $data = [], array $mergeData = [])
    {
        $theme = get_active_theme();
        $themeViewPath = "frontend.{$theme}.{$view}";

        // Fallback ke tema default jika view di tema aktif tidak ditemukan
        if (! view()->exists($themeViewPath)) {
            return view("frontend.default.{$view}", $data, $mergeData);
        }

        return view($themeViewPath, $data, $mergeData);
    }
}

if (! function_exists('theme_vite_assets')) {
    /**
     * Menghasilkan tag Vite untuk CSS dan JavaScript tema aktif.
     */
    function theme_vite_assets(): \Illuminate\Support\HtmlString
    {
        $theme = get_active_theme();
        $hotFile = public_path("frontend/{$theme}/hot");

        if (
            app()->environment('local') &&
            \Illuminate\Support\Facades\File::exists($hotFile)
        ) {
            $entries = ["resources/themes/{$theme}/css/app.css"];
            $configPath = resource_path("themes/{$theme}/theme.json");
            $config = \Illuminate\Support\Facades\File::exists($configPath)
                ? json_decode(\Illuminate\Support\Facades\File::get($configPath), true)
                : [];
            $javascriptStack = data_get($config, 'stack.javascript', 'alpine');
            $javascriptSource = resource_path("themes/{$theme}/js/app.js");

            if (
                $javascriptStack !== 'none' &&
                \Illuminate\Support\Facades\File::exists($javascriptSource)
            ) {
                $entries[] = "resources/themes/{$theme}/js/app.js";
            }

            $vite = clone app(\Illuminate\Foundation\Vite::class);
            $vite->useHotFile($hotFile);

            return $vite($entries, "frontend/{$theme}");
        }

        $tags = '';
        $cssPath = "frontend/{$theme}/css/styles.css";
        $jsPath = "frontend/{$theme}/js/app.js";

        if (\Illuminate\Support\Facades\File::exists(public_path($cssPath))) {
            $tags .= '<link rel="stylesheet" href="'.e(asset($cssPath)).'">';
        }

        if (\Illuminate\Support\Facades\File::exists(public_path($jsPath))) {
            $tags .= '<script type="module" src="'.e(asset($jsPath)).'"></script>';
        }

        return new \Illuminate\Support\HtmlString($tags);
    }
}

if (! function_exists('theme_asset')) {
    /**
     * Menghasilkan URL untuk public asset milik tema yang aktif.
     * Contoh penggunaan: theme_asset('css/style.css')
     *
     * @param  bool|null  $secure
     */
    function theme_asset(string $path, $secure = null): string
    {
        $theme = get_active_theme();

        // Membersihkan slash di awal jika user sengaja/tidak sengaja menulisnya (misal: '/css/style.css')
        $cleanPath = ltrim($path, '/');

        return asset("frontend/{$theme}/{$cleanPath}", $secure);
    }
}

if (! function_exists('get_theme_config')) {
    /**
     * Mengambil file konfigurasi theme.php dari tema aktif atau data spesifik di dalamnya.
     * Contoh: get_theme_config('assets.styles')
     *
     * @return mixed
     */
    function get_theme_config(?string $key = null)
    {
        $theme = get_active_theme();
        $configPath = resource_path("views/frontend/{$theme}/theme.php");

        if (! file_exists($configPath)) {
            // Fallback ke config default jika file tidak ada di tema aktif
            $configPath = resource_path('views/frontend/default/theme.php');
        }

        if (file_exists($configPath)) {
            $config = include $configPath;

            if ($key) {
                return data_get($config, $key);
            }

            return $config;
        }

        return null;
    }
}

if (! function_exists('get_theme_style')) {
    /**
     * Mengambil jenis CSS framework dari info.style theme aktif.
     */
    function get_theme_style(): string
    {
        $style = get_theme_config('info.style');

        return is_string($style) && trim($style) !== ''
            ? trim($style)
            : 'bootstrap';
    }
}

if (! function_exists('getActiveTheme')) {

    function getActiveTheme(): string
    {
        // PRIORITAS DEMO CONTEXT
        if (app()->bound('currentTheme')) {
            return app('currentTheme');
        }

        try {
            if (Schema::hasTable('settings')) {
                $theme = Setting::value('active_theme');
                if ($theme) {
                    return $theme;
                }
            }
        } catch (Exception $e) {
            //
        }

        return config('frontend.active', 'default');
    }
}

if (! function_exists('themeAsset')) {
    /**
     * Get URL to a theme asset
     */
    function themeAsset(string $path): string
    {
        $theme = getActiveTheme();

        return asset(config('frontend.themes_path').'/'.$theme.'/'.$path);
    }
}

if (! function_exists('themePath')) {
    /**
     * Get path to theme directory
     */
    function themePath(): string
    {
        $theme = getActiveTheme();

        return resource_path(config('frontend.themes_path').'/'.$theme);
    }
}

if (! function_exists('theme_css')) {
    /**
     * Generate HTML link tag for theme CSS files
     *
     * @param  string  $path  Path to CSS file relative to theme's css directory
     * @param  array  $attributes  Additional HTML attributes
     */
    function theme_css(string $path, array $attributes = []): string
    {
        $url = themeAsset('css/'.ltrim($path, '/'));

        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= " {$key}=\"{$value}\"";
        }

        return '<link rel="stylesheet" href="'.$url.'"'.$attrs.'>';
    }
}

if (! function_exists('theme_js')) {
    /**
     * Generate HTML script tag for theme JS files
     *
     * @param  string  $path  Path to JS file relative to theme's js directory
     * @param  array  $attributes  Additional HTML attributes
     */
    function theme_js(string $path, array $attributes = []): string
    {
        $url = themeAsset('js/'.ltrim($path, '/'));

        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= " {$key}=\"{$value}\"";
        }

        return '<script src="'.$url.'"'.$attrs.'></script>';
    }
}

if (! function_exists('getAvailableThemes')) {
    /**
     * Get list of available themes from the themes directory
     */
    function getAvailableThemes(): array
    {
        $themesPath = resource_path('views/'.config('frontend.themes_path', 'frontend'));
        $themes = [];

        if (is_dir($themesPath)) {
            $directories = array_filter(glob($themesPath.'/*'), 'is_dir');
            foreach ($directories as $dir) {
                $themeName = basename($dir);
                $themes[$themeName] = ucfirst($themeName);
            }
        }

        return $themes ?: ['default' => 'Default'];
    }
}

if (! function_exists('getFirstImage')) {
    /**
     * Ambil gambar pertama dari artikel menggunakan method gambar() dengan custom class
     *
     * @param  object|Model  $item  - Object artikel dengan method gambar()
     * @param  string  $class  - Custom CSS class untuk img tag (default: 'img-thumbnail')
     * @param  string  $alt  - Alt text untuk gambar (default: judul artikel)
     * @return string - HTML img tag atau string kosong jika tidak ada gambar
     *
     * @example
     * // Dalam blade template
     * {{ getFirstImage($post) }}
     * {{ getFirstImage($post, 'img-fluid rounded', 'Post: ' . $post->title) }}
     */
    function getFirstImage($item, string $class = 'img-thumbnail', string $alt = '', string $style = ''): string
    {
        try {
            // Pastikan item memiliki method gambar()
            if (! method_exists($item, 'gambar')) {
                return '';
            }

            $images = $item->gambar();

            // Jika tidak ada gambar, return empty string
            if (empty($images)) {
                return '';
            }

            // Ambil gambar pertama
            $firstImage = $images[0] ?? null;

            if (! $firstImage) {
                return '';
            }

            // Gunakan title atau alt parameter untuk alt text
            $altText = $alt ?: ($item->title ?? 'Image');

            // Return HTML img tag
            return sprintf(
                '<img src="%s" class="%s" alt="%s" style="%s">',
                htmlspecialchars($firstImage, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($class, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($altText, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($style, ENT_QUOTES, 'UTF-8')
            );
        } catch (Exception $e) {
            Log::error('Error getting first image: '.$e->getMessage());

            return '';
        }
    }
}

if (! function_exists('getFirstImageUrl')) {
    /**
     * Ambil URL gambar pertama dari artikel
     *
     * @param  object|Model  $item  - Object artikel dengan method gambar()
     * @return string|null - URL gambar atau null jika tidak ada
     *
     * @example
     * // Dalam blade template
     * <img src="{{ getFirstImageUrl($post) }}" alt="{{ $post->title }}">
     */
    function getFirstImageUrl($item): ?string
    {
        try {
            // Pastikan item memiliki method gambar()
            if (! method_exists($item, 'gambar')) {
                return null;
            }

            $images = $item->gambar();

            return $images[0] ?? null;
        } catch (Exception $e) {
            Log::error('Error getting first image URL: '.$e->getMessage());

            return null;
        }
    }
}

if (! function_exists('get_content_excerpt')) {
    /**
     * Get a clean excerpt from post or page content (strip HTML tags).
     *
     * @param  object  $item
     * @param  int  $length
     * @return string
     */
    function get_content_excerpt($item, int $length = 150): string
    {
        if (! $item || empty($item->content)) {
            return '';
        }

        $text = strip_tags($item->content);
        $text = preg_replace('/\s+/', ' ', $text);

        return Str::limit(trim($text), $length, '...');
    }
}

if (! function_exists('available_locales')) {
    /**
     * Get all available published locales
     *
     * @return Collection
     */
    function available_locales()
    {
        return Language::where('status', 'Publish')->get();
    }
}

if (! function_exists('seo_meta')) {
    function seo_meta($seo = null)
    {
        $setting = setting();

        $isEnglish = $setting->language === 'en';

        $siteName = $isEnglish
            ? $setting->sitename_translation
            : $setting->sitename;

        $keywords = $isEnglish
            ? $setting->keywords_translation
            : $setting->keywords;

        $description = $isEnglish
            ? $setting->description_translation
            : $setting->description;

        $data = [
            'title' => $seo->title ?? $siteName,
            'keywords' => $seo->keyword ?? $keywords,
            'description' => $seo->description ?? $description,
            'image' => $seo->image ?? $setting->images,
            'url' => $seo->url ?? $setting->url,
            'site_name' => $siteName,
        ];

        return view('suryacms::layouts.seo-meta', compact('data'))->render();
    }
}

if (! function_exists('suryacms_version')) {
    /**
     * Mengambil versi dari paket uiaciel/suryacms.
     *
     * @return string
     */
    function suryacms_version()
    {
        try {
            return InstalledVersions::getPrettyVersion('uiaciel/suryacms');
        } catch (OutOfBoundsException $e) {
            return 'Unknown'; // Mengembalikan teks jika paket tidak ditemukan
        }
    }
}
