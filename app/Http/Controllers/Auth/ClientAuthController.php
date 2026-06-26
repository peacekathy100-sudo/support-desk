<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\RedirectResponse;
use App\Models\ExternalClient;

class ClientAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest.client')->except('logout');
    }

    /**
     * Show the client login form
     */
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    /**
     * Handle client login
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $client = ExternalClient::where('username', $request->username)
            ->where('status', 'active')
            ->first();

        if (!$client) {
            return back()->withErrors([
                'username' => 'Username not found or account is not active.',
            ])->withInput();
        }

        if (!Hash::check($request->password, $client->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ])->withInput();
        }

        // Update last login
        $client->updateLastLogin();

        // Authenticate with client guard
        Auth::guard('client')->login($client, $request->boolean('remember'));

        return redirect()->route('client.dashboard');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('client.auth.forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:external_clients,email',
        ]);
        $status = Password::broker('clients')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Handle client logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        if ($client) {
            $client->updateLastActivity();
        }

        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login')->with('status', 'You have been logged out successfully.');
    }
}
