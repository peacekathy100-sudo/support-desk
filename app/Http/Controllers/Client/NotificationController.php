<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Portal notifications (placeholder until client-specific notification storage exists).
 */
class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.client');
    }

    public function index(): View
    {
        return view('client.notifications.index', [
            'notifications' => collect(),
        ]);
    }

    public function markAsRead(string $id): JsonResponse
    {
        Auth::guard('client')->user()?->updateLastActivity();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(): RedirectResponse
    {
        Auth::guard('client')->user()?->updateLastActivity();

        return back()->with('success', 'All notifications marked as read.');
    }
}
