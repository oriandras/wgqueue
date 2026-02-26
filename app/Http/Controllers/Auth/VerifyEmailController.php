<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * Az e-mail cím megerősítéséért felelős vezérlő.
 */
class VerifyEmailController extends Controller
{
    /**
     * A bejelentkezett felhasználó e-mail címének megerősítettként való megjelölése.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Ha az e-mail már meg van erősítve, átirányítás a kezdőlapra
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        // Az e-mail cím megerősítése és esemény kiváltása
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
