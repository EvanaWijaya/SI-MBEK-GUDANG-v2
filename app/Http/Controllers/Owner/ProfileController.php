<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the owner's profile (read-only)
     */
    public function show()
    {
        $owner = auth('owner')->user();

        return view('owner.profile.show', compact('owner'));
    }

    /**
     * Show the form for changing password
     */
    public function showChangePasswordForm()
    {
        $owner = auth('owner')->user();

        return view('owner.profile.change-password', compact('owner'));
    }

    /**
     * Handle password change request
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:owner'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $owner = auth('owner')->user();

        // Update password dan ubah flag must_change_password
        $owner->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        return redirect()->route('owner.dashboard')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Update password from profile page (not first time)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:owner'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $owner = auth('owner')->user();

        $owner->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }
}