<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictLocalStaffAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local') && ! in_array($request->ip(), ['127.0.0.1', '::1'], true)) {
            abort(404);
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');

        return $response;
    }
}
