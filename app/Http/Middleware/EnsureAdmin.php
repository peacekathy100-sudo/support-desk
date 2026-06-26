<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\SysUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user instanceof SysUser || !($user->isMainAdmin() || $user->isSuperUser())) {
            abort(403, 'Administrator access is required to view this page.');
        }

        return $next($request);
    }
}
