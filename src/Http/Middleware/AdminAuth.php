<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AdminAuth
{
    public function handle($request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if ($request->is('dashboard') || $request->is('*/dashboard')) {
            $dashboardRoute = Route::has('admin.dashboard')
                ? 'admin.dashboard'
                : 'admin.admin';

            return redirect()->route($dashboardRoute);
        }

        return $next($request);
    }
}
