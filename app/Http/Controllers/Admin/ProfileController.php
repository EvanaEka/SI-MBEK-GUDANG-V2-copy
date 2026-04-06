<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminProfileUpdateRequest;
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,'.$admin->id],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maks 2MB
        ]);

        $admin->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($admin->isDirty('email')) {
            $admin->email_verified_at = null;
        }

        // Handle upload foto profil
        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama jika ada dan bukan null
            if ($admin->profile_picture && Storage::disk('public')->exists('admin_avatars/' . $admin->profile_picture)) {
                Storage::disk('public')->delete('admin_avatars/' . $admin->profile_picture);
            }

            $file = $request->file('profile_picture');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke storage/app/public/admin_avatars
            $file->storeAs('admin_avatars', $fileName, 'public');
            
            $admin->profile_picture = $fileName;
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
        $query = User::query()->withCount(['kambing', 'domba']);

        if ($type) {
            $relation = $type === 'kambing' ? 'kambing' : 'domba';
            $query->has($relation);
        }

        return view('admin.pengguna', [
            'users' => $query->paginate(10),
            'currentType' => $type
        ]);
    }
}
