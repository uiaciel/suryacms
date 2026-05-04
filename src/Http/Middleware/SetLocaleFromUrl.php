<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * SetLocaleFromUrl Middleware
 *
 * Mengatur locale aplikasi berdasarkan language parameter dari URL
 * atau menggunakan default language dari settings.
 *
 * URL Structure:
 * - Default language: / /page /category/slug (NO PREFIX)
 * - Non-default language: /en /en/page /en/category/slug (WITH PREFIX)
 */
class SetLocaleFromUrl
{
    /**
     * Handle middleware
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Ambil language parameter dari URL
            $locale = $request->route('lang');

            // Ambil list bahasa yang aktif
            $activeLangs = active_languages();

            // Jika ada lang parameter di URL dan aktif, gunakan itu
            if ($locale && in_array($locale, $activeLangs)) {
                app()->setLocale($locale);
                session(['locale' => $locale]);
            } else {
                // Jika tidak ada lang parameter (default language route), gunakan default
                $default = default_locale();
                app()->setLocale($default);
                session(['locale' => $default]);
            }
        } catch (\Exception $e) {
            Log::error('SetLocaleFromUrl Middleware Error: ' . $e->getMessage());
        }

        return $next($request);
    }
}
