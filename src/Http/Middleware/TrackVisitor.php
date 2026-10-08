<?php

namespace Uiaciel\SuryaCms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Uiaciel\SuryaCMS\Models\Visitor;

class TrackVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya catat request GET biasa (bukan AJAX, API, atau asset)
        if ($request->isMethod('get') && ! $request->expectsJson() && ! $request->routeIs('api.*')) {
            $today = now()->toDateString();
            $ip = $request->ip();

            // Cek apakah IP ini sudah berkunjung hari ini (Unique Visitor)
            $exists = Visitor::where('ip_address', $ip)
                ->where('visited_date', $today)
                ->exists();

            if (! $exists) {
                // Gunakan try-catch agar jika gagal menulis log, website klien tidak ikutan crash
                try {
                    Visitor::create([
                        'ip_address' => $ip,
                        'user_agent' => substr($request->userAgent(), 0, 255),
                        'visited_date' => $today,
                    ]);
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }

        return $response;
    }
}
