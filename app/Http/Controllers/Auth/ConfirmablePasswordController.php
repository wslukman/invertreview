<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! hash_equals((string) $request->session()->token(), (string) $request->token)) {
            return back()->withErrors(['token' => 'Token mismatch.']);
        }

        $request->session()->passwordConfirmed();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
