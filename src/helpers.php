<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
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
    function getFirstImage($item, string $class = 'img-thumbnail', string $alt = ''): string
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
                '<img src="%s" class="%s" alt="%s">',
                htmlspecialchars($firstImage, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($class, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($altText, ENT_QUOTES, 'UTF-8')
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
