<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été désactivé. Contactez l’administrateur.']);
        } elseif (!Auth::user()->isAdmin()) {
            return redirect()->back()
                ->with(['success' => 'Vous n\'avez pas droit à effectuer cette situation. Contactez l’administrateur.']);
        }
        return $next($request);
    }
}
