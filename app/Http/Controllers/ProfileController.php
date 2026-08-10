<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil user.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'church' => $request->user()->church,
        ]);
    }

    /**
     * Memperbarui informasi profil user.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Aturan validasi dasar untuk User
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];

        // Jika user adalah admin gereja, tambahkan validasi untuk field gereja
        if ($user->hasRole('church_admin') && $user->church) {
            $rules = array_merge($rules, [
                'church_name' => ['required', 'string', 'max:255', 'unique:churches,name,' . $user->church_id],
                'church_address' => ['required', 'string', 'min:10', 'max:500'],
                'pastor_name' => ['nullable', 'string', 'max:255'],
                'church_latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'church_longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'church_phone' => ['required', 'string', 'regex:/^(\+62|0)[0-9]{9,12}$/', 'unique:churches,phone,' . $user->church_id],
                'church_email' => ['required', 'email:rfc,dns', 'unique:churches,email,' . $user->church_id],
                'church_description' => ['required', 'string', 'min:20', 'max:1000'],
                'church_founded_year' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
                'social_media' => ['nullable', 'url', 'max:255'],
                'church_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'church_cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:3072'],
            ]);
        }

        $request->validate($rules);

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Update Data Gereja jika user adalah admin gereja
        if ($user->hasRole('church_admin') && $user->church) {
            $church = $user->church;
            $church->update([
                'name' => $request->church_name,
                'address' => $request->church_address,
                'pastor_name' => $request->pastor_name,
                'latitude' => $request->church_latitude,
                'longitude' => $request->church_longitude,
                'phone' => $request->church_phone,
                'email' => $request->church_email,
                'description' => $request->church_description,
                'founded_year' => $request->church_founded_year,
                'social_media' => $request->social_media,
            ]);

            if ($request->hasFile('church_logo')) {
                if ($church->logo_path) Storage::disk('public')->delete($church->logo_path);
                $church->update(['logo_path' => $request->file('church_logo')->store('churches/logos', 'public')]);
            }

            if ($request->hasFile('church_cover_image')) {
                if ($church->cover_image_path) Storage::disk('public')->delete($church->cover_image_path);
                $church->update(['cover_image_path' => $request->file('church_cover_image')->store('churches/covers', 'public')]);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun user.
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

        return Redirect::to('/');
    }

    /**
     * Menampilkan daftar user untuk Super Admin (Jika dibutuhkan oleh route admin.users.index)
     */
    public function index(): View
    {
        $users = \App\Models\User::paginate(10);
        return view('admin.users.index', compact('users'));
    }
}