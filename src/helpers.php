<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Uiaciel\SuryaCms\Models\Setting;

if (! function_exists('register_admin_menupackage')) {
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

if (! function_exists('get_cms_setting')) {
    /**
     * Get CMS setting dengan caching yang aman
     */
    function get_cms_setting()
    {
        return cache()->remember('cms_settings', 3600, function () {
            try {
                if (Schema::hasTable('settings')) {
                    return Setting::first();
                }
            } catch (\Exception $e) {
                Log::error('Error fetching CMS settings: ' . $e->getMessage());
            }
            return null;
        });
    }
}

if (! function_exists('active_languages')) {
    /**
     * Get all active language codes
     */
    function active_languages()
    {
        return cache()->rememberForever('active_languages', function () {
            try {
                return \Uiaciel\SuryaCms\Models\Language::where('status', 'Publish')
                    ->pluck('code')
                    ->toArray();
            } catch (\Exception $e) {
                Log::error('Error fetching active languages: ' . $e->getMessage());
                return [];
            }
        });
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
     * @param string $name Route name
     * @param array|string $params Route parameters
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

        if (!is_array($params)) {
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
                } catch (\Exception $e) {
                    Log::warning("Route not found: {$name}");
                    return url('/');
                }
            } else {
                // Jika bukan default, tambahkan lang parameter
                $params['lang'] = $locale;
                try {
                    return route($name, $params);
                } catch (\Exception $e) {
                    Log::warning("Route not found: {$name}");
                    return url('/');
                }
            }
        }

        // Jika single language, gunakan route biasa
        try {
            return route($name, $params);
        } catch (\Exception $e) {
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
     * @param string $path Path tanpa language prefix (misal: '/about', 'contact')
     * @param string|null $lang Language code, default dari session/config
     * @return string Full URL dengan atau tanpa prefix
     *
     * @example
     * url_with_lang('/about')        // /about (id) atau /en/about (en)
     * url_with_lang('contact', 'en') // /en/contact
     */
    function url_with_lang($path = '', $lang = null)
    {
        if (!is_multilingual()) {
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
     * @param string $newLocale Language code untuk switch ke
     * @return string URL dengan locale baru
     *
     * @example
     * // Current: /en/about -> switch_locale_url('id') -> /about
     * // Current: /about -> switch_locale_url('en') -> /en/about
     */
    function switch_locale_url($newLocale)
    {
        if (!is_multilingual()) {
            return url()->current();
        }

        $currentUrl = request()->path();
        $currentLocale = current_locale();
        $defaultLocale = default_locale();

        // Extract path tanpa language prefix
        if ($currentLocale !== $defaultLocale && str_starts_with($currentUrl, $currentLocale . '/')) {
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

        return cache()->rememberForever('app_date_format', function () {
            return Setting::first()->date_format ?? 'd/m/Y';
        });
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
        } catch (\Exception $e) {
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

if (! function_exists('getActiveTheme')) {
    /**
     * Get the active frontend theme
     */
    function getActiveTheme(): string
    {
        return cache()->remember('active_frontend_theme', 3600, function () {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                    $theme = Setting::value('active_theme');
                    if ($theme) {
                        return $theme;
                    }
                }
            } catch (\Exception $e) {
                // Silently fail
            }

            return config('frontend.active', 'default');
        });
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
    function getFirstImage($item, string $class = 'img-thumbnail', string $alt = '' , string $style = ''): string
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            Log::error('Error getting first image URL: '.$e->getMessage());

            return null;
        }
    }
}

if (! function_exists('available_locales')) {
    /**
     * Get all available published locales
     *
     * @return \Illuminate\Support\Collection
     */
    function available_locales()
    {
        return \Uiaciel\SuryaCms\Models\Language::where('status', 'Publish')->get();
    }
}
