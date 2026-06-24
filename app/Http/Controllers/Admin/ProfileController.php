<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the admin profile page
     */
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => auth('admin')->user(),
        ]);
    }

    /**
     * Update profile information (name, email, profile picture)
     */
    public function update(Request $request)
    {
        $admin = auth('admin')->user();

        // Validasi input manual karena kita menambahkan profile_picture
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maks 2MB
        ]);

        $admin->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($admin->isDirty('email')) {
            $admin->email_verified_at = null;
        }

        // Handle upload foto profil menggunakan tabel media
        if ($request->hasFile('profile_picture')) {
            
            // 1. Hapus foto lama di tabel media jika ada (menggunakan variabel $admin)
            foreach ($admin->media as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }

            // 2. Upload file fisik ke folder admin_avatars
            $file = $request->file('profile_picture');
            $fileName = "admin_" . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('admin_avatars', $fileName, 'public');

            // 3. Simpan data ke tabel media dengan relasi milik admin
            $admin->media()->create([
                'file_name'  => $file->getClientOriginalName(),
                'file_path'  => $filePath,
                'mime_type'  => $file->getMimeType(),
                'file_size'  => $file->getSize(),
                'type'       => 'image',
                'is_primary' => true, 
                'sort_order' => 0,
            ]);
        }

        $admin->save();

        return Redirect::route('admin.profile.edit')
            ->with('status', 'profile-updated');
    }
    
    /**
     * Delete admin account
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password:admin'],
        ]);

        $admin = auth('admin')->user();

        Auth::guard('admin')->logout();

        $admin->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/admin/login');
    }

    /**
     * 🔐 CHANGE PASSWORD
     * - First login: tanpa current password
     * - Normal: wajib current password
     */
    public function changePassword(Request $request)
    {
        $admin = auth('admin')->user();

        if ($admin->must_change_password) {
            // FIRST LOGIN
            $request->validate([
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);
        } else {
            // NORMAL CHANGE PASSWORD
            $request->validate([
                'current_password' => ['required', 'current_password:admin'],
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);
        }

        $admin->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false, // 🔥 INI KUNCI SUPAYA TIDAK LOOP
            'password_changed_at' => now(),
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Password berhasil diperbarui');
    }

    /**
     * Delete penitip user (super admin only)
     */
    public function destroyuser(User $user)
    {
        if (auth('admin')->user()->role !== 'super_admin') {
            return redirect()->back()
                ->with('error', 'Tidak memiliki akses.');
        }

        $imagePath = public_path('upload/profilImage/' . $user->profile_picture);

        if ($user->profile_picture && file_exists($imagePath)) {
            @unlink($imagePath);
        }

        $user->delete();

        return redirect()->back()
            ->with('success', 'Data user berhasil dihapus');
    }

    /**
     * List penitip users
     */
    public function penitip(Request $request, $type = null)
    {
        $query = User::query()->withCount(['kambings', 'dombas']);

        if ($type) {
            $relation = $type === 'kambings' ? 'kambings' : 'dombas';
            $query->has($relation);
        }

        return view('admin.pengguna', [
            'users' => $query->paginate(10),
            'currentType' => $type
        ]);
    }
}
