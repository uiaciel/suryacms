<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Setting;

class SetLocaleFromUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if multilingual is enabled
        $setting = Setting::first();
        $isMultilingual = $setting && isset($setting->is_multilingual) && $setting->is_multilingual === 'Yes';

        if (! $isMultilingual) {
            // If not multilingual, use default language
            $defaultLocale = $setting->language ?? config('app.locale', 'id');
            App::setLocale($defaultLocale);
            Session::put('locale', $defaultLocale);

            return $next($request);
        }

        // Get locale from URL parameter
        $locale = $request->route('lang');

        // If locale is in URL, validate and set it
        if ($locale) {
            // Check if locale is valid in database
            $language = Language::where('code', $locale)
                ->where('status', 'Publish')
                ->first();

            if ($language) {
                // Valid locale found, set it
                App::setLocale($locale);
                Session::put('locale', $locale);
            } else {
                // Invalid locale, use default
                $defaultLocale = $setting->language ?? config('app.locale', 'id');
                App::setLocale($defaultLocale);
                Session::put('locale', $defaultLocale);
            }
        } else {
            // No locale in URL, check session
            $sessionLocale = Session::get('locale');

            if ($sessionLocale) {
                // Use session locale
                App::setLocale($sessionLocale);
            } else {
                // Use default locale
                $defaultLocale = $setting->language ?? config('app.locale', 'id');
                App::setLocale($defaultLocale);
                Session::put('locale', $defaultLocale);
            }
        }

        return $next($request);
    }
}
