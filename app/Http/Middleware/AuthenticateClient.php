<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateClient
{
    public function handle(Request $request, Closure $next, string $guard = 'client'): Response
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('client.login');
        }

        // Check if client is active
        $client = Auth::guard($guard)->user();
        if (!$client->isActive()) {
            Auth::guard($guard)->logout();
            return redirect()->route('client.login')->withErrors([
                'status' => 'Your account is not active. Please contact your assigned representative.',
            ]);
        }

        return $next($request);
    }
}
