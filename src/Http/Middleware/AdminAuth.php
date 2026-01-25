<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle($request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect('/login'); // atau route login khusus admin
        }

        return $next($request);
    }
}
