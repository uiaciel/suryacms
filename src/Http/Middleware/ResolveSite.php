<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Uiaciel\SuryaCms\Models\Site;
use Uiaciel\SuryaCms\Models\Setting;
use Uiaciel\SuryaCms\Models\SiteDomain;

class ResolveSite
{
    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = config('app.locale', 'en');
        app()->forgetInstance('suryacms.current_site');
        app()->forgetInstance('suryacms.current_domain');
        app()->forgetInstance('current.setting');
        config(['frontend.active' => config('frontend.default_active', 'default')]);
        App::setLocale($defaultLocale);

        if (! config('suryacms.multisite.enabled', false)) {
            if (Schema::hasTable('sites') && Site::query()->count() > 1) {
                abort(503, 'Multiple SuryaCMS sites exist but multisite domain resolution is disabled.');
            }

            return $next($request);
        }

        if (! Schema::hasTable('site_domains')) {
            abort(503, 'SuryaCMS multisite is enabled but its database migrations have not been run.');
        }

        $domain = SiteDomain::query()
            ->with('site')
            ->where('domain', SiteDomain::normalize($request->getHost()))
            ->first();

        if (! $domain || ! $domain->site) {
            abort(404, 'This domain is not configured for a SuryaCMS site.');
        }
        app()->instance('suryacms.current_site', $domain->site);
        app()->instance('suryacms.current_domain', $domain);

        $setting = Setting::query()->first();
        if ($setting) {
            app()->instance('current.setting', $setting);
            App::setLocale($setting->language ?: $defaultLocale);
        }

        $theme = $setting?->active_theme ?: config('frontend.active', 'default');
        config(['frontend.active' => $theme]);
        $themeBasePath = 'views/'.trim(config('frontend.themes_path', 'frontend'), '/').'/';
        $themePath = resource_path($themeBasePath.$theme);

        if (! is_dir($themePath)) {
            $themePath = resource_path($themeBasePath.'default');
        }

        if (is_dir($themePath)) {
            View::prependNamespace('frontend', $themePath);
        }

        try {
            return $next($request);
        } finally {
            app()->forgetInstance('suryacms.current_site');
            app()->forgetInstance('suryacms.current_domain');
            app()->forgetInstance('current.setting');
            config(['frontend.active' => config('frontend.default_active', 'default')]);
            App::setLocale($defaultLocale);
        }
    }
}
