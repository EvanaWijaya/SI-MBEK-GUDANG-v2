<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Pastikan user sudah login dengan guard admin
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();

        // 2. Cek apakah role user ada dalam daftar role yang diizinkan
        // Kita gunakan in_array supaya bisa cek lebih dari satu role sekaligus
        if (!in_array($user->role, $roles)) {
            // Jika ini request AJAX/JSON, kirim response JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            // Jika akses web biasa, tampilkan halaman error 403
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}