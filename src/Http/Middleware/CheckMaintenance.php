<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Uiaciel\SuryaCms\Models\Setting;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        $setting = Setting::first();

        // 1. Jika user sudah login (admin atau user biasa), bypass maintenance
        if (Auth::guard('web')->check()) {
            return $next($request);
        }

        // 2. Kalau setting tidak ada atau maintenance off → lanjut
        if (! $setting || ! $setting->site_maintenance) {
            return $next($request);
        }

        // 3. Daftar route yang boleh diakses meskipun maintenance aktif
        $allowedRoutes = [
            'login*',           // halaman login
            'logout*',          // halaman logout
            'password/*',       // reset password
            'livewire/*',       // request Livewire
            '_ignition/*',      // error Laravel (Ignition)
            'admin/*',          // semua admin routes
            'api/*',            // API routes
        ];

        // Check if request matches any allowed route
        foreach ($allowedRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        // 4. Izinkan request Livewire AJAX via header
        if ($request->header('X-Livewire') || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $next($request);
        }

        // 5. Kalau guest → tampilkan halaman maintenance
        $theme = $setting->active_theme ?? 'default';
        $maintenanceView = "frontend.{$theme}.page.maintenance";

        // Fallback jika view tidak ditemukan
        if (! view()->exists($maintenanceView)) {
            return response()
                ->json([
                    'message' => 'Website is currently under maintenance. Please try again later.',
                ], 503);
        }

        return response()
            ->view($maintenanceView, [], 503);
    }
}
