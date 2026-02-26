<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * A felhasználói profil kezeléséért felelős vezérlő.
 */
class ProfileController extends Controller
{
    /**
     * Megjeleníti a felhasználó profil szerkesztő felületét.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Frissíti a felhasználó profiladatait.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // A validált adatokkal feltöltjük a felhasználói modellt
        $request->user()->fill($request->validated());

        // Ha megváltozott az e-mail cím, az ellenőrzést alaphelyzetbe állítjuk
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Törli a felhasználói fiókot.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Jelszó ellenőrzése a fiók törlése előtt
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Kijelentkeztetés
        Auth::logout();

        // Felhasználó törlése az adatbázisból
        $user->delete();

        // Munkamenet érvénytelenítése és token újragenerálása
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
