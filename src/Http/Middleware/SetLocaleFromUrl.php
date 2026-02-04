<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Uiaciel\SuryaCms\Models\Language;
use Uiaciel\SuryaCms\Models\Setting;

class SetLocaleFromUrl
{
    public function handle(Request $request, Closure $next)
    {
        $setting = Setting::first();
        $isMultilingual = ($setting->is_multilingual ?? 'No') === 'Yes';
        $defaultLocale = $setting->language ?? config('app.locale', 'id');

        if (!$isMultilingual) {
            $this->setLocale($defaultLocale);
            return $next($request);
        }

        // Ambil locale dari parameter route 'lang'
        $locale = $request->route('lang');

        if ($locale && $this->isValidLanguage($locale)) {
            $this->setLocale($locale);
        } else {
            // Jika tidak ada di URL, pakai yang ada di session atau default
            $this->setLocale(Session::get('locale', $defaultLocale));
        }

        return $next($request);
    }

    private function isValidLanguage($code)
    {
        return Language::where('code', $code)->where('status', 'Publish')->exists();
    }

    private function setLocale($code)
    {
        App::setLocale($code);
        Session::put('locale', $code);
    }
}
