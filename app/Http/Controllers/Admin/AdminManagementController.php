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
        $admins = Admin::where('role', 'admin')
            ->where('id', '!=', auth('admin')->id())
            ->get();
        
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
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'created_by' => auth('admin')->id(),
            'must_change_password' => true,
        ]);

       return redirect()->route('admin.admins.index')->with('success', 'Admin baru berhasil ditambahkan!');
    }

    // Detail admin
    public function show($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);
        
       return view('admin.management.show', compact('admin'));
    }

    // Update admin
    public function update(Request $request, $id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('admins')->ignore($id)],
        ]);

        $admin->update($validated);

       return redirect()->route('admin.admins.index')->with('success', 'Data Admin berhasil diperbarui!');
    }

    // Hapus admin
    public function destroy($id)
    {
        $admin = Admin::where('role', 'admin')->findOrFail($id);
        
        // Pastikan tidak menghapus diri sendiri
        if ($admin->id === auth('admin')->id()) {
            return response()->json([
                'message' => 'You cannot delete yourself'
            ], 403);
        }
        
        $admin->delete();

       return redirect()->route('admin.admins.index')->with('success', 'Admin berhasil dihapus!');
    }
}