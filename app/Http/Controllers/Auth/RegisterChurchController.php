<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChurchRequest;
use App\Models\Church;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisterChurchController extends Controller
{
    /**
     * Display the church registration view.
     */
    public function showForm(): View
    {
        return view('auth.register-church');
    }

    /**
     * Handle church registration.
     */
    public function store(StoreChurchRequest $request)
    {
        // Validate input
        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $request) {
            // Create church record tanpa submitted_by terlebih dahulu
            // CATATAN: Pastikan kolom submitted_by di migration sudah ->nullable()
            $church = Church::create([
                'name' => $validated['name'],
                'pastor_name' => $validated['pastor_name'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'description' => $validated['description'],
                'founded_year' => $validated['founded_year'],
                'status' => 'pending',
            ]);

            // Handle uploads
            if ($request->hasFile('logo')) {
                $church->logo_path = $request->file('logo')->store('churches/logos', 'public');
            }
            if ($request->hasFile('cover_image')) {
                $church->cover_image_path = $request->file('cover_image')->store('churches/covers', 'public');
            }
            $church->save();

            if (Auth::check()) {
                $user = Auth::user();
                $user->update([
                    'church_id' => $church->id,
                ]);
            } else {
                // Check if email already exists
                $existingUser = User::where('email', $validated['email'])->first();
                if ($existingUser) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'email' => 'Email ini sudah terdaftar. Silakan "Masuk" (Login) ke akun Anda terlebih dahulu, kemudian lakukan Pendaftaran Gereja dari dalam dasbor.',
                    ]);
                }

                // Create user admin gereja
                $user = User::create([
                    'name' => 'Admin - ' . $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make(str()->random(32)),
                    'phone' => $validated['phone'],
                    'church_id' => $church->id,
                    'is_active' => false,
                ]);

                // Trigger event
                event(new Registered($user));
            }

            // Update gereja dengan ID user yang baru dibuat/disubmit
            $church->update(['submitted_by' => $user->id]);

            // Assign role
            if (!$user->hasRole('church_admin')) {
                $user->assignRole('church_admin');
            }

            if (Auth::check()) {
                return redirect()->route('dashboard')->with('status', 'Pendaftaran gereja berhasil disubmit! Silakan tunggu persetujuan dari Super Admin.');
            }

            return redirect()->route('login')->with('status', 
                'Pendaftaran gereja berhasil! Silakan tunggu persetujuan dari Super Admin. 
                Email konfirmasi telah dikirim ke ' . $validated['email']);
        });
    }
}
