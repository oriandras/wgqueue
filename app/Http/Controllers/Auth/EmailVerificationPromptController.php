<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Az e-mail cím megerősítésére figyelmeztető felületet kezelő vezérlő.
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * Megjeleníti az e-mail cím megerősítésére vonatkozó üzenetet.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        // Ha az e-mail már meg van erősítve, átirányítás a kezdőlapra,
        // különben a megerősítést kérő nézet megjelenítése.
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
