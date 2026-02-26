<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * A jelszó megerősítéséért felelős vezérlő (biztonságos műveletek előtt).
 */
class ConfirmablePasswordController extends Controller
{
    /**
     * Megjeleníti a jelszó megerősítéséhez szükséges felületet.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Kezeli a felhasználó jelszavának megerősítését.
     */
    public function store(Request $request): RedirectResponse
    {
        // Jelszó ellenőrzése
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        // Megerősítés időpontjának tárolása a munkamenetben
        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
