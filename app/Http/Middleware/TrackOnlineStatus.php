<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackOnlineStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $key  = 'user_online_' . $user->user_id;

            // Only update database every 5 minutes to reduce load
            // Use cache to avoid database hits on every request
            if (!Cache::has($key)) {
                // Queue the update instead of executing synchronously
                $user->update(['user_online' => 1]);
                Cache::put($key, true, now()->addMinutes(5));
            }
        }

        return $next($request);
    }
}

