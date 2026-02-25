<?php
use function Livewire\Volt\{state, mount};
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\ActivityLog;

state([
    'name' => '',
    'email' => '',
    'current_password' => '',
    'new_password' => '',
    'new_password_confirmation' => '',
]);

// Adatok betöltése belépéskor
mount(function () {
    $user = auth()->user();
    $this->name = $user->name;
    $this->email = $user->email;
});

// Profil adatok frissítése
$updateProfile = function () {
    $user = auth()->user();

    $this->validate([
        'name' => 'required|min:3',
        // Engedjük a saját emailt, de mástól tiltsuk el (unique kivétel)
        'email' => 'required|email|unique:sys_users,email,' . $user->id,
    ]);

    $user->update([
        'name' => $this->name,
        'email' => $this->email,
    ]);

    ActivityLog::create([
        'user_id' => $user->id,
        'action' => 'Profil módosítás',
        'description' => 'Saját név/email frissítve.',
    ]);

    $this->dispatch('swal:success', message: 'Sikeresen mentve!');
};

// Biztonságos jelszóváltás
$changePassword = function () {
    $user = auth()->user();

    $this->validate([
        'current_password' => 'required|current_password', // Validálja, hogy tényleg a régi jelszó-e
        'new_password' => ['required', 'confirmed', Password::min(8)],
    ], [
        'current_password' => 'A megadott jelenlegi jelszó helytelen!',
        'new_password.confirmed' => 'A két jelszó nem egyezik!',
    ]);

    $user->update([
        'password' => Hash::make($this->new_password)
    ]);

    ActivityLog::create([
        'user_id' => $user->id,
        'action' => 'Jelszóváltás',
        'description' => 'A felhasználó megváltoztatta a jelszavát.',
    ]);

    $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    $this->dispatch('swal:success', message: 'Jelszó sikeresen lecserélve!');
};
?>

<div class="row">
    {{-- Személyes adatok kártya --}}
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Adatok szerkesztése</h3></div>
            <form wire:submit.prevent="updateProfile">
                <div class="card-body">
                    <div class="form-group">
                        <label>Megjelenítési név</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>E-mail cím</label>
                        <input type="email" wire:model="email" class="form-control">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Mentés</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Jelszóváltás kártya --}}
    <div class="col-md-6">
        <div class="card card-warning card-outline">
            <div class="card-header"><h3 class="card-title">Jelszó cseréje</h3></div>
            <form wire:submit.prevent="changePassword">
                <div class="card-body">
                    <div class="form-group">
                        <label>Jelenlegi jelszó</label>
                        <input type="password" wire:model="current_password" class="form-control">
                        @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Új jelszó</label>
                        <input type="password" wire:model="new_password" class="form-control">
                        @error('new_password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Új jelszó újra</label>
                        <input type="password" wire:model="new_password_confirmation" class="form-control">
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-warning text-dark">Jelszó frissítése</button>
                </div>
            </form>
        </div>
    </div>
</div>
