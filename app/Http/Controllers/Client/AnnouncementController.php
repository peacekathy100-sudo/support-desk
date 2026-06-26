<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Models\Announcement;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.client');
    }

    /**
     * Display announcements
     */
    public function index(): View
    {
        $announcements = Announcement::where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('client.announcements.index', [
            'announcements' => $announcements,
        ]);
    }
}
