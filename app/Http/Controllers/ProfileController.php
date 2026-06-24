<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // 1. Tarik data user yang sedang login SEBELUM blok IF
        $user = $request->user(); 

        // 2. Simpan semua input teks (Nama, Email, Alamat, No Telepon) dari validasi request
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 3. Handle upload foto menggunakan tabel media
        if ($request->hasFile('profile_picture')) {
            
            // Hapus foto lama di tabel media jika ada
            foreach ($user->media as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }

            // Upload file fisik baru
            $file = $request->file('profile_picture');
            $fileName = "user_" . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke direktori storage/app/public/profilImage
            $filePath = $file->storeAs('profilImage', $fileName, 'public');

            // Simpan data ke tabel media dengan relasi milik user
            $user->media()->create([
                'file_name'  => $file->getClientOriginalName(),
                'file_path'  => $filePath,
                'mime_type'  => $file->getMimeType(),
                'file_size'  => $file->getSize(),
                'type'       => 'image',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        // Simpan semua perubahan
        $user->save();

        return Redirect::route('dashboard')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'account-deleted');
    }

}
