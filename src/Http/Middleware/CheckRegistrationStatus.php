<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRegistrationStatus
{
    public function handle(Request $request, Closure $next)
    {

        if ($request->is('dashboard*')) {
                return redirect('/admin')->with('success', 'Selamat datang di Dashboard admin.');
        }

        if (! config('frontend.registration_enabled')) {

            if ($request->is('register*')) {
                return redirect('/login')->with('error', 'Registrasi saat ini ditutup.');
            }

        }

        return $next($request);
    }
}
