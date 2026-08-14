<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user exists by google_id
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // If user exists, log them in
                Auth::login($user);
                return redirect()->intended(route('dashboard', absolute: false));
            } else {
                // Check if user exists by email but without google_id
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // Update existing user with google_id
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        'email_verified_at' => $existingUser->email_verified_at ?? now(), // verify if not yet
                    ]);
                    Auth::login($existingUser);
                    return redirect()->intended(route('dashboard', absolute: false));
                }

                // If user doesn't exist at all, create a new one
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(24)), // Random password since they login via Google
                    'is_active' => true,
                    'email_verified_at' => now(), // Auto verify since it's from Google
                ]);

                // Assign default role 'member'
                $newUser->assignRole('member');

                event(new Registered($newUser));

                Auth::login($newUser);

                return redirect()->route('dashboard');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('status', 'Gagal masuk menggunakan Google: ' . $e->getMessage());
        }
    }
}
