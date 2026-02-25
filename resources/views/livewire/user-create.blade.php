<?php
use function Livewire\Volt\{state, action};
use App\Models\User;
use App\Models\ActivityLog;
use App\Mail\SystemNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

state(['name' => '', 'email' => '']);

$store = function () {
    $this->validate([
        'name' => 'required|min:3',
        'email' => 'required|email|unique:sys_users,email',
    ], [
        'email.unique' => 'Ez az email cím már foglalt a rendszerben!',
    ]);

    $tempPassword = Str::random(12);

    $user = User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => Hash::make($tempPassword),
        'is_active' => true,
    ]);

    // Naplózás
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'User Létrehozás',
        'description' => "Új felhasználó: " . $user->email,
    ]);

    // Email küldése a szép Markdown sablonnal
    if ($user->email) {
        $mailable = app()->make(SystemNotification::class, [
            'title' => 'WGQueue - Belépési adatok',
            'message' => "Szia " . $user->name . "!\n\nAz adminisztrátor hozzáférést készített neked.\n\nEmail: " . $user->email . "\nIdeiglenes jelszó: " . $tempPassword . "\n\nKérjük, lépj be és változtasd meg a jelszavad!",
            'buttonUrl' => route('login'),
            'buttonText' => 'Bejelentkezés'
        ]);
        Mail::to($user->email)->send($mailable);
    }

    session()->flash('success', 'Felhasználó sikeresen létrehozva!');
    return redirect()->route('admin.users');
};
?>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <form wire:submit.prevent="store">
                <div class="card-body">
                    <div class="form-group">
                        <label>Felhasználó teljes neve</label>
                        <input type="text" wire:model="name" class="form-control" placeholder="Példa Béla">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>E-mail cím</label>
                        <input type="email" wire:model="email" class="form-control" placeholder="bela@tpf.hu">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="icon fas fa-info"></i>
                        A jelszót a rendszer automatikusan generálja és kiküldi a megadott e-mail címre.
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Felhasználó rögzítése
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
