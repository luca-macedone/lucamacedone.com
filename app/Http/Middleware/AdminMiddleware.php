<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->Auth::check() || !auth()->Auth::user()()->is_admin) {
            return redirect('/')->with('error', 'Access forbidden.');
        }

        return $next($request);
    }
}
