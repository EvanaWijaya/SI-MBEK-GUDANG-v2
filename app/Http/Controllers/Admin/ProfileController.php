<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => auth('admin')->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(AdminProfileUpdateRequest $request): RedirectResponse
    {
        $admin = auth('admin')->user(); // ✅ Explicit guard

        $admin->fill($request->validated());

        if ($admin->isDirty('email')) {
            $admin->email_verified_at = null;
        }

        $admin->save();

        return Redirect::route('admin.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password:admin'],
        ]);

        $user = $request->user();

        Auth::guard('admin')->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/admin/login');
    }

    /**
     * Delete penitip user (super_admin only)
     */
    public function destroyuser(User $user)
    {
        // ✅ Tambah validasi role
        if (auth('admin')->user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Tidak memiliki akses untuk menghapus user.');
        }

        $imagePath = public_path('upload/profilImage/' . $user->profile_picture);

        // Cek dulu apakah user punya foto dan file-nya ada
        if ($user->profile_picture && file_exists($imagePath)) {
            @chmod($imagePath, 0755);
            @unlink($imagePath);
        }

        // Hapus user dari database
        $user->delete();

        return redirect()->back()->with('success', 'Data user berhasil dihapus');
    }

    /**
     * Update password from profile page
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $admin = auth('admin')->user();

        $admin->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    public function penitip(Request $request, $type = null)
    {
        $query = User::query()->withCount(['kambing', 'domba']);

        if ($type) {
            $relation = $type === 'kambing' ? 'kambing' : 'domba';
            $query->has($relation);
        }

        $users = $query->paginate(10);

        return view('admin.pengguna', [
            'users' => $users,
            'currentType' => $type
        ]);
    }
}
