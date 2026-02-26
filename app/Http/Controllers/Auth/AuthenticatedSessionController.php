<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * A bejelentkezett munkamenetért felelős vezérlő.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Megjeleníti a bejelentkező felületet.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Kezeli a bejövő hitelesítési kérelmet.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Hitelesítési adatok ellenőrzése
        $request->authenticate();

        // Munkamenet újragenerálása a fixáció elleni védelem érdekében
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Lezárja a hitelesített munkamenetet (kijelentkezés).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Kijelentkeztetés
        Auth::guard('web')->logout();

        // Munkamenet érvénytelenítése és CSRF token újragenerálása
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
