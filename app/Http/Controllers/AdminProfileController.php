<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', ['adminUser' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160', Rule::unique('users', 'email')->ignore($user)],
        ]);

        $user->update($validated);

        return back()->with('status', 'Profile details updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()],
        ]);

        $request->user()->update(['password' => Hash::make($validated['password'])]);

        return back()->with('status', 'Password changed successfully.');
    }

    public function settings(Request $request): View
    {
        return view('admin.settings.edit', [
            'adminUser' => $request->user(),
            'preferences' => $request->user()->admin_preferences ?? [],
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_screen' => ['required', Rule::in(['dashboard', 'pages', 'media'])],
            'sidebar_collapsed' => ['nullable', 'boolean'],
            'email_notifications' => ['nullable', 'boolean'],
        ]);

        $request->user()->update([
            'admin_preferences' => [
                'default_screen' => $validated['default_screen'],
                'sidebar_collapsed' => $request->boolean('sidebar_collapsed'),
                'email_notifications' => $request->boolean('email_notifications'),
            ],
        ]);

        return back()->with('status', 'Dashboard preferences saved.');
    }
}
