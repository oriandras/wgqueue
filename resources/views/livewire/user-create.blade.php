<?php
/**
 * Új felhasználó létrehozása Livewire (Volt) komponens.
 * Lehetővé teszi az adminisztrátor számára új felhasználó rögzítését,
 * automatikus jelszógenerálással és e-mail értesítéssel.
 */
use function Livewire\Volt\{state, action};
use App\Models\User;
use App\Models\ActivityLog;
use App\Mail\SystemNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Komponens állapota
state(['name' => '', 'email' => '']);

/**
 * Az új felhasználó mentése és az értesítő levél kiküldése.
 */
$store = function () {
    // Bemeneti adatok validálása
    $this->validate([
        'name' => 'required|min:3',
        'email' => 'required|email|unique:sys_users,email',
    ], [
        'email.unique' => 'Ez az email cím már foglalt a rendszerben!',
    ]);

    // Ideiglenes jelszó generálása
    $tempPassword = Str::random(12);

    // Felhasználó létrehozása az adatbázisban
    $user = User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => Hash::make($tempPassword),
        'is_active' => true,
    ]);

    // Tevékenység naplózása
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'User Létrehozás',
        'description' => "Új felhasználó: " . $user->email,
    ]);

    // Értesítő e-mail küldése a belépési adatokkal
    if ($user->email) {
        $mailable = app()->make(SystemNotification::class, [
            'title' => 'WGQueue - Belépési adatok',
            'message' => "Szia " . $user->name . "!\n\nAz adminisztrátor hozzáférést készített neked.\n\nEmail: " . $user->email . "\nIdeiglenes jelszó: " . $tempPassword . "\n\nKérjük, lépj be és változtasd meg a jelszavad!",
            'buttonUrl' => route('login'),
            'buttonText' => 'Bejelentkezés'
        ]);
        Mail::to($user->email)->send($mailable);
    }

    // Visszajelzés és átirányítás a listához
    session()->flash('success', 'Felhasználó sikeresen létrehozva!');
    return redirect()->route('admin.users');
};
?>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            {{-- Felhasználó rögzítése űrlap --}}
            <form wire:submit.prevent="store">
                <div class="card-body">
                    {{-- Név mező --}}
                    <div class="form-group">
                        <label>Felhasználó teljes neve</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="Példa Béla">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    {{-- E-mail cím mező --}}
                    <div class="form-group">
                        <label>E-mail cím</label>
                        <input type="email" wire:model="email" class="form-control" placeholder="bela@tpf.hu">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tájékoztató üzenet a jelszóról --}}
                    <div class="alert alert-info mt-4">
                        <i class="icon fas fa-info"></i>
                        A jelszót a rendszer automatikusan generálja és kiküldi a megadott e-mail címre.
                    </div>
                </div>
                <div class="card-footer text-right">
                    {{-- Mentés gomb --}}
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Felhasználó rögzítése
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
