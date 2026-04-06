<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\OwnerProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the owner profile page
     */
    public function edit(Request $request): View
    {
        return view('owner.profile.edit', [
            'user' => auth('owner')->user(),
        ]);
    }

    /**
     * Update profile information (name, email, profile picture)
     */
    public function update(Request $request)
    {
        $owner = auth('owner')->user();

        // Validasi input manual karena kita menambahkan profile_picture
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:owners,email,'.$owner->id],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maks 2MB
        ]);

        $owner->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($owner->isDirty('email')) {
            $owner->email_verified_at = null;
        }

        // Handle upload foto profil
        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama jika ada dan bukan null
            if ($owner->profile_picture && Storage::disk('public')->exists('owner_avatars/' . $owner->profile_picture)) {
                Storage::disk('public')->delete('owner_avatars/' . $owner->profile_picture);
            }

            $file = $request->file('profile_picture');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke storage/app/public/owner_avatars
            $file->storeAs('owner_avatars', $fileName, 'public');
            
            $owner->profile_picture = $fileName;
        }

        $owner->save();

        return Redirect::route('owner.profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete owner account
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password:owner'],
        ]);

        $owner = auth('owner')->user();

        Auth::guard('owner')->logout();

        $owner->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/owner/login');
    }

    /**
     * 🔐 CHANGE PASSWORD
     * - First login: tanpa current password
     * - Normal: wajib current password
     */
    public function changePassword(Request $request)
    {
        $owner = auth('owner')->user();

        if ($owner->must_change_password) {
            // FIRST LOGIN
            $request->validate([
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);
        } else {
            // NORMAL CHANGE PASSWORD
            $request->validate([
                'current_password' => ['required', 'current_password:owner'],
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);
        }

        $owner->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false, // 🔥 INI KUNCI SUPAYA TIDAK LOOP
            'password_changed_at' => now(),
        ]);

        return redirect()->route('owner.dashboard')
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
    $query = User::with(['kambings', 'domba']) 
                ->withCount(['kambings', 'domba']);

    if ($type) {
        $relation = $type === 'kambing' ? 'kambings' : 'domba';
        $query->has($relation);
    }

    return view('owner.pengguna', [
        'users' => $query->paginate(10),
        'currentType' => $type
    ]);
}

}
