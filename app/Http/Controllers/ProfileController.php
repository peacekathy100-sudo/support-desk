<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function index(): View
    {
        return view('profile');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var \App\Models\SysUser $user */
        $user = Auth::user();

        $data = $request->validate([
            'user_surname' => ['required', 'string', 'max:100'],
            'user_othername' => ['nullable', 'string', 'max:100'],
            'user_email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('sysuser', 'user_email')->ignore($user->user_id, 'user_id'),
            ],
            'user_telephone' => ['nullable', 'string', 'max:30'],
            'user_gender' => ['nullable', 'in:Male,Female,Other'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $oldPhoto = $user->profile_photo;
            $extension = $file->getClientOriginalExtension() ?: 'png';
            $data['profile_photo'] = 'profile-' . $user->user_id . '-' . time() . '.' . $extension;

            if ($oldPhoto && file_exists(public_path('images/' . $oldPhoto))) {
                @unlink(public_path('images/' . $oldPhoto));
            }

            $file->move(public_path('images'), $data['profile_photo']);
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        /** @var \App\Models\SysUser $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->user_password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect'
            ]);
        }

        $user->update([
            'user_password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
