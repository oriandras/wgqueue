<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * A jelszó-visszaállítási link igényléséért felelős vezérlő.
 */
class PasswordResetLinkController extends Controller
{
    /**
     * Megjeleníti a jelszó-visszaállítási link igényléséhez szükséges felületet.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Kezeli a jelszó-visszaállítási linkre vonatkozó bejövő kérelmet.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Az e-mail cím validálása
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Jelszó-visszaállítási link küldése. A válasz alapján
        // értesítjük a felhasználót a művelet sikerességéről vagy hibájáról.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
