<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Az új felhasználók regisztrációjáért felelős vezérlő.
 */
class RegisteredUserController extends Controller
{
    /**
     * Megjeleníti a regisztrációs felületet.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Kezeli a bejövő regisztrációs kérelmet.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Bemeneti adatok validálása
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Új felhasználó létrehozása az adatbázisban
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Regisztrációs esemény kiváltása
        event(new Registered($user));

        // Automatikus bejelentkeztetés a regisztráció után
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
