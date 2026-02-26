<?php
/**
 * Webes útvonalak (Routes) definíciója.
 * Ebben a fájlban találhatók az alkalmazás publikus és hitelesítést igénylő útvonalai.
 * A legtöbb útvonal közvetlenül Blade nézeteket vagy Livewire komponenseket szolgál ki.
 */

use Illuminate\Support\Facades\Route;

// TODO: A ProfileController importálva van, de a fájlban jelenleg sehol nincs használva közvetlenül.
// Ha a profil kezelést teljesen átvette a Livewire, ez az import törölhető.
// use App\Http\Controllers\ProfileController;

/**
 * Nyitóoldal átirányítása a bejelentkezéshez.
 */
Route::redirect('/', '/login');

/**
 * Hitelesített és e-mail megerősítéssel rendelkező felhasználók alapértelmezett oldala.
 */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Felhasználói kiküldések (Scheduling) kezelése.
 * Csak bejelentkezett felhasználók számára elérhető funkciók.
 */
Route::middleware(['auth'])->group(function () {
    // Naptár nézet az összes (saját + karbantartás) esemény megjelenítésére
    Route::get('/scheduling/calendar', function () {
        return view('scheduling.calendar');
    })->name('scheduling.calendar');

    // Saját ütemezések listája
    Route::get('/scheduling/list', function () {
        return view('scheduling.list');
    })->name('scheduling.list');

    /**
     * Új ütemezés rögzítése.
     */
    Route::get('/scheduling/create', function () {
        return view('scheduling.create');
    })->name('scheduling.create');

    /**
     * Meglévő ütemezés szerkesztése.
     */
    Route::get('/scheduling/edit/{id}', function ($id) {
        return view('scheduling.edit', ['id' => $id]);
    })->name('scheduling.edit');
});

/**
 * Adminisztrátori kiküldés kezelés.
 * Csak admin jogkörrel rendelkező hitelesített felhasználók számára.
 */
Route::middleware(['auth', 'can:admin'])->group(function () {
    // Az összes felhasználó összes ütemezésének listája
    Route::get('/scheduling/admin-list', function () {
        return view('scheduling.admin-list');
    })->name('scheduling.admin-list');
});

/**
 * Felhasználói profil kezelése.
 */
Route::middleware('auth')->group(function () {
    // Profil szerkesztő felület
    Route::view('/profile', 'profile')->name('profile');
});

/**
 * Adminisztrációs felület (Admin Panel).
 * Globális beállítások, felhasználókezelés és naplózás.
 */
Route::middleware(['auth', 'can:admin'])->prefix('admin')->group(function () {

    // Rendszer szintű globális beállítások
    Route::view('/settings', 'admin.settings')->name('admin.settings');

    // Felhasználók kezelése (lista, létrehozás, szerkesztés)
    Route::view('/users', 'admin.users')->name('admin.users');
    Route::view('/users/create', 'admin.users-create')->name('admin.users.create');
    Route::view('/users/{id}/edit', 'admin.users-edit')->name('admin.users.edit');

    /**
     * Rendszernaplók alcsoportja.
     */
    Route::prefix('logs')->name('admin.logs.')->group(function () {
        // Felhasználói tevékenységek naplója
        Route::view('/activity', 'admin.logs.activity')->name('activity');
        // Rendszerhibák naplója
        Route::view('/errors', 'admin.logs.errors')->name('errors');
    });
});

/**
 * Hitelesítéssel kapcsolatos útvonalak betöltése (login, register, password reset, stb.).
 */
require __DIR__ . '/auth.php';
