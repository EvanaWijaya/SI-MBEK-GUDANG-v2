<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class OwnerForgotPasswordController extends Controller
{
    /**
     * Tampilkan form lupa password
     */
    public function showLinkRequestForm()
    {
        return view('auth.owner.forgot-password');
    }

    /**
     * Kirim email reset password
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:owners,email',
        ], [
            'email.exists' => 'Email tidak terdaftar sebagai owner.',
        ]);

        $status = Password::broker('owners')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password telah dikirim ke email Anda!');
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}