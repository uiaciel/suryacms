<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Uiaciel\SuryaCms\Support\SiteManagementAccess;

class ManageSites
{
    public function handle(Request $request, Closure $next): Response
    {
        app(SiteManagementAccess::class)->ensureAccess();

        return $next($request);
    }
}
