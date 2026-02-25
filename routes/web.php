<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/scheduling/calendar', function () {
        return view('scheduling.calendar');
    })->name('scheduling.calendar');

    Route::get('/scheduling/list', function () {
        return view('scheduling.list');
    })->name('scheduling.list');
});

Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/scheduling/admin-list', function () {
        return view('scheduling.admin-list');
    })->name('scheduling.admin-list');
});

Route::get('/scheduling/create', function () {
    return view('scheduling.create');
})->middleware(['auth']);

Route::get('/scheduling/edit/{id}', function ($id) {
    return view('scheduling.edit', ['id' => $id]);
})->name('scheduling.edit')->middleware(['auth']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'can:admin'])->prefix('admin')->group(function () {

    // Ez lesz a https://wgqueue.test/admin/settings
    Route::view('/settings', 'admin.settings')->name('admin.settings');
    Route::view('/users', 'admin.users')->name('admin.users');
    Route::view('/users/create', 'admin.users-create')->name('admin.users.create');

    // Itt kezdődik a logs alcsoport
    Route::prefix('logs')->name('admin.logs.')->group(function () {
        Route::view('/activity', 'admin.logs.activity')->name('activity');
        Route::view('/errors', 'admin.logs.errors')->name('errors');
    });
});

require __DIR__ . '/auth.php';
