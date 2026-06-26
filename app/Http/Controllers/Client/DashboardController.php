<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ClientTicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Client Dashboard Controller
 * Displays client portal dashboard with overview
 */
class DashboardController extends Controller
{
    protected $ticketService;

    public function __construct(ClientTicketService $ticketService)
    {
        $this->middleware('auth.client');
        $this->ticketService = $ticketService;
    }

    /**
     * Display client dashboard
     */
    public function index(): View
    {
        $client = Auth::guard('client')->user();

        // Get statistics
        $statistics = $this->ticketService->getTicketStatistics($client);
        $recentTickets = $this->ticketService->getRecentTickets($client, 5);
        $unreadMessages = $client->unreadNotificationsCount();

        // Get representative info
        $representative = $client->getRepresentativeInfo();

        return view('client.dashboard', [
            'client' => $client,
            'statistics' => $statistics,
            'recentTickets' => $recentTickets,
            'unreadMessages' => $unreadMessages,
            'representative' => $representative,
        ]);
    }

    /**
     * View client profile
     */
    public function profile(): View
    {
        $client = Auth::guard('client')->user();

        return view('client.profile', [
            'client' => $client,
            'representative' => $client->assignedRepresentative,
        ]);
    }

    /**
     * Update client profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:external_clients,email,' . $client->id,
        ]);

        $client->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Show change password form
     */
    public function showChangePassword(): View
    {
        return view('client.profile.change-password');
    }

    /**
     * Update client password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'current_password' => 'required|current_password:client',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $client->update([
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('client.profile')->with('success', 'Password changed successfully.');
    }
}
