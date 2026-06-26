<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\AuditService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    protected string $redirectTo = '/dashboard';

    public function __construct(private AuditService $auditor)
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'user_name' => 'required',
            'password'  => 'required',
        ]);

        $user = \App\Models\SysUser::where('user_name', $request->user_name)
            ->where('user_status', 'active')
            ->first();

        if (!$user) {
            return back()->withErrors(['user_name' => 'User not found']);
        }

        if (!Hash::check($request->password, $user->user_password)) {
            return back()->withErrors(['password' => 'Incorrect password']);
        }

        Auth::login($user);

        $user->update([
            'user_last_logged_in' => now(),
            'user_online' => 1,
        ]);

        $this->auditor->log('login', 'SysUser', $user->user_id);

        return redirect()->route('dashboard');
    }

    public function showLoginForm(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function username(): string
    {
        return 'user_name';
    }

    protected function authenticated(Request $request, $user): void
    {
        $user->update([
            'user_last_logged_in' => now(),
            'user_online'         => 1,
        ]);

        $this->auditor->log('login', 'SysUser', $user->user_id);
    }

    protected function loggedOut(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->update(['user_online' => 0]);
            $this->auditor->log('logout', 'SysUser', $user->user_id);
        }

        return redirect()->route('login');
    }

    protected function validateLogin(Request $request): void
    {
        $request->validate([
            'user_name' => 'required|string',
            'password'  => 'required|string',
        ], [
            'user_name.required' => 'Username is required.',
            'password.required'  => 'Password is required.',
        ]);
    }

    protected function credentials(Request $request): array
    {
        return [
            'user_name'     => $request->user_name,
            'user_password' => $request->password,
            'user_status'   => 'active',
        ];
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->update(['user_online' => 0]);
            $this->auditor->log('logout', 'SysUser', $user->user_id);
        }

        Auth::logout();

        return redirect('/');
    }
}
