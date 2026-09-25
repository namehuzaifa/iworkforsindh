<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;

class ScraperApiKeyMiddleware
{
    /**
     * Let a request through only when it carries the scraper's API key.
     *
     * With no key configured the endpoint stays shut, so a server that has
     * not been set up yet cannot be posted to by anyone.
     */
    public function handle(Request $request, Closure $next)
    {
        $key = (string) config('services.scraper.key');
        $sent = (string) $request->header('X-API-KEY');

        if ($key === '' || $sent === '' || ! hash_equals($key, $sent)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or missing API key.',
            ], 401);
        }

        return $next($request);
    }
}
