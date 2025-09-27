<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Accesso richiesto.');
        }

        if (!auth()->user()->is_admin) {
            return redirect('/')->with('error', 'Accesso non autorizzato.');
        }

        return $next($request);
    }
}
