<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictRiderAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role === 'rider') {
            // Redirect rider to skilled labor index if trying to access dashboard
            if ($request->is('dashboard') || $request->is('/')) {
                return redirect()->route('skilled-labour.index');
            }
        }
    
        return $next($request);
    }
}
