<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    // List semua admin (kecuali super_admin yang sedang login)
    public function index()
    {
        $admins = Admin::latest()->get();

        return view('admin.management.index', compact('admins'));
    }

    // Form Tambah Admin
    public function create()
    {
        return view('admin.management.create');
    }

    // Tambah admin baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'admin',
            'must_change_password' => true,
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'Admin baru berhasil ditambahkan!');
    }

    // Admin detail
    public function show($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);

        return view('admin.management.show', compact('admin'));
    }

    // Admin update
    public function update(Request $request, $id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('admins')->ignore($id)],
            'phone' => 'nullable|string|max:20',
        ]);

        $admin->update($validated);

        return redirect()->route('admin.admins.index')->with('success', 'Data Admin berhasil diperbarui!');
    }

    // Admin Delete
    public function destroy($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);

        // Cannot delete yourself
        if ($admin->id === auth('admin')->id()) {
            return response()->json([
                'message' => 'You cannot delete yourself'
            ], 403);
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'Admin berhasil dihapus!');
    }
}

