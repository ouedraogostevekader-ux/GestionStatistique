<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CspMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        //$csp = "default-src 'self'; script-src 'self' 'nonce-".csp_nonce()."' 'strict-dynamic' https:; style-src 'self' 'nonce-".csp_nonce()."' https:; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self' https:; object-src 'none'; base-uri 'self'; frame-ancestors 'self';";

        $response = $next($request);
        //$response->header('Content-Security-Policy', $csp);

        return $response;
    }
}
