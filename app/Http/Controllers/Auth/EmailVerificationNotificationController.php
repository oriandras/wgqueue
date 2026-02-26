<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Az e-mail cím megerősítéséhez szükséges értesítések újraküldéséért felelős vezérlő.
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Új e-mail megerősítési értesítés küldése.
     */
    public function store(Request $request): RedirectResponse
    {
        // Ha az e-mail már meg van erősítve, átirányítás a kezdőlapra
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Megerősítő e-mail kiküldése
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
