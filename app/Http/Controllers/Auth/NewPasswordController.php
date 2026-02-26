<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Az új jelszó beállításáért felelős vezérlő.
 */
class NewPasswordController extends Controller
{
    /**
     * Megjeleníti a jelszó-visszaállítási űrlapot.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Kezeli az új jelszó beállítására vonatkozó kérelmet.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Bemeneti adatok validálása
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Megkíséreljük a jelszó visszaállítását. Ha sikeres, frissítjük a
        // felhasználói modellt és elmentjük az adatbázisba.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Jelszó-visszaállítási esemény kiváltása
                event(new PasswordReset($user));
            }
        );

        // Ha a jelszó sikeresen vissza lett állítva, átirányítjuk a felhasználót
        // a bejelentkező oldalra. Hiba esetén visszaküldjük a hibaüzenettel.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
