<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfClientAuthenticated
{
    public function handle(Request $request, Closure $next, string $guard = 'client'): Response
    {
        if (Auth::guard($guard)->check()) {
            return redirect()->route('client.dashboard');
        }

        return $next($request);
    }
}
