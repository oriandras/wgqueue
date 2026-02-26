<?php
/**
 * Felhasználói profil szerkesztése Livewire (Volt) komponens.
 * Lehetővé teszi a bejelentkezett felhasználó számára profiladatainak
 * (név, e-mail) és jelszavának módosítását.
 */
use function Livewire\Volt\{state, mount};
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\ActivityLog;

// Komponens állapota
state([
    'name' => '',
    'email' => '',
    'current_password' => '',
    'new_password' => '',
    'new_password_confirmation' => '',
]);

/**
 * Adatok betöltése a bejelentkezett felhasználó alapján.
 */
mount(function () {
    $user = auth()->user();
    $this->name = $user->name;
    $this->email = $user->email;
});

/**
 * Alapvető profil adatok (név, e-mail) frissítése.
 */
$updateProfile = function () {
    $user = auth()->user();

    // Bemeneti adatok validálása
    $this->validate([
        'name' => 'required|min:3',
        // A saját e-mail címet engedélyezzük, de másét nem (unique szabály kivétellel)
        'email' => 'required|email|unique:sys_users,email,' . $user->id,
    ]);

    // Felhasználó frissítése
    $user->update([
        'name' => $this->name,
        'email' => $this->email,
    ]);

    // Tevékenység naplózása
    ActivityLog::create([
        'user_id' => $user->id,
        'action' => 'Profil módosítás',
        'description' => 'Saját név/email frissítve.',
    ]);

    // Sikeres mentés visszajelzés (SweetAlert2)
    $this->dispatch('swal:success', message: 'Sikeresen mentve!');
};

/**
 * Jelszó módosítása a jelenlegi jelszó ellenőrzésével.
 */
$changePassword = function () {
    $user = auth()->user();

    // Jelszó adatok validálása
    $this->validate([
        'current_password' => 'required|current_password', // A jelenlegi jelszó helyességének ellenőrzése
        'new_password' => ['required', 'confirmed', Password::min(8)],
    ], [
        'current_password' => 'A megadott jelenlegi jelszó helytelen!',
        'new_password.confirmed' => 'A két jelszó nem egyezik!',
    ]);

    // Új jelszó titkosítása és mentése
    $user->update([
        'password' => Hash::make($this->new_password)
    ]);

    // Tevékenység naplózása
    ActivityLog::create([
        'user_id' => $user->id,
        'action' => 'Jelszóváltás',
        'description' => 'A felhasználó megváltoztatta a jelszavát.',
    ]);

    // Mezők alaphelyzetbe állítása és visszajelzés
    $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    $this->dispatch('swal:success', message: 'Jelszó sikeresen lecserélve!');
};
?>

<div class="row">
    {{-- Személyes adatok szerkesztése kártya --}}
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Adatok szerkesztése</h3></div>
            <form wire:submit.prevent="updateProfile">
                <div class="card-body">
                    {{-- Megjelenítési név mező --}}
                    <div class="form-group">
                        <label>Megjelenítési név</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    {{-- E-mail cím mező --}}
                    <div class="form-group">
                        <label>E-mail cím</label>
                        <input type="email" wire:model="email" class="form-control">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="card-footer text-right">
                    {{-- Mentés gomb --}}
                    <button type="submit" class="btn btn-primary">Mentés</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Jelszó módosítása kártya --}}
    <div class="col-md-6">
        <div class="card card-warning card-outline">
            <div class="card-header"><h3 class="card-title">Jelszó cseréje</h3></div>
            <form wire:submit.prevent="changePassword">
                <div class="card-body">
                    {{-- Jelenlegi jelszó a biztonság érdekében --}}
                    <div class="form-group">
                        <label>Jelenlegi jelszó</label>
                        <input type="password" wire:model="current_password" class="form-control">
                        @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <hr>
                    {{-- Új jelszó megadása --}}
                    <div class="form-group">
                        <label>Új jelszó</label>
                        <input type="password" wire:model="new_password" class="form-control">
                        @error('new_password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    {{-- Új jelszó megerősítése --}}
                    <div class="form-group">
                        <label>Új jelszó újra</label>
                        <input type="password" wire:model="new_password_confirmation" class="form-control">
                    </div>
                </div>
                <div class="card-footer text-right">
                    {{-- Jelszó frissítése gomb --}}
                    <button type="submit" class="btn btn-warning text-dark">Jelszó frissítése</button>
                </div>
            </form>
        </div>
    </div>
</div>
