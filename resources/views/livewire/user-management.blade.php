<?php
/**
 * Felhasználó kezelés Livewire (Volt) komponens.
 * Kilistázza a regisztrált felhasználókat, lehetővé teszi a keresést,
 * rendezést, státusz váltást és jelszó visszaállítást.
 */
use function Livewire\Volt\{state, computed, usesPagination, updated};
use App\Models\User;
use App\Models\ActivityLog;
use App\Mail\SystemNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Bootstrap alapú lapozás használata
usesPagination(theme: 'bootstrap');

// Komponens állapota
state([
    'search' => '',
    'sortField' => 'name',
    'sortDirection' => 'asc',
]);

// Keresés esetén a lapozás visszaállítása az első oldalra
updated(['search' => fn() => $this->resetPage()]);

/**
 * Táblázat rendezése mező szerint.
 */
$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
};

/**
 * Felhasználó státuszának (aktív/tiltott) váltása.
 */
$toggleStatus = function ($id) {
    // Saját magunk letiltásának megakadályozása
    if ($id == auth()->id()) {
        $this->dispatch('swal:error', message: 'Saját magadat nem tilthatod le!');
        return;
    }

    $user = User::findOrFail($id);
    $user->is_active = !($user->is_active ?? true);
    $user->save();

    // Naplózás
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'User Státusz Váltás',
        'description' => "Felhasználó: " . $user->email . " új állapota: " . ($user->is_active ? 'Aktív' : 'Tiltott'),
    ]);

    $this->dispatch('swal:success', message: 'Státusz sikeresen frissítve.');
};

/**
 * Felhasználó jelszavának alaphelyzetbe állítása és új jelszó küldése e-mailben.
 */
$resetPassword = function ($id) {
    $user = User::findOrFail($id);
    $newPassword = Str::random(12);

    $user->password = Hash::make($newPassword);
    $user->save();

    // Naplózás
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Jelszó Reset',
        'description' => "Új ideiglenes jelszó generálva a következőnek: " . $user->email,
    ]);

    // Értesítő levél küldése
    if ($user->email) {
        $mailable = app()->make(SystemNotification::class, [
            'title' => 'Új ideiglenes jelszó generálva',
            'message' => "Szia " . $user->name . "!\n\nAz adminisztrátor alaphelyzetbe állította a jelszavadat.\n\nAz új, ideiglenes jelszavad: " . $newPassword . "\n\nKérjük, az első bejelentkezés után haladéktalanul változtasd meg a profilodban!",
            'buttonUrl' => route('login'),
            'buttonText' => 'Bejelentkezés a rendszerbe'
        ]);

        Mail::to($user->email)->send($mailable);
    }

    $this->dispatch('swal:success', message: 'Az új jelszót elküldtük a felhasználónak!');
};

/**
 * Felhasználók lekérése keresési és rendezési feltételek alapján.
 */
$users = computed(function () {
    return User::query()
        ->where(function($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%');
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate(10);
});
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Regisztrált felhasználók</h3>
        <div class="card-tools d-flex">
            {{-- TODO: Az "Új felhasználó" gomb is mutathatna egy modalra --}}
            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm mr-3">
                <i class="fas fa-plus"></i> Új felhasználó
            </a>
            <input type="text" wire:model.live="search" class="form-control form-control-sm" style="width: 200px;" placeholder="Keresés...">
        </div>
    </div>
    <div class="card-body p-0">
        {{-- Felhasználók táblázata --}}
        <table class="table table-hover">
            <thead>
            <tr>
                <th wire:click="sortBy('id')" style="cursor:pointer">ID</th>
                <th wire:click="sortBy('name')" style="cursor:pointer">Név</th>
                <th wire:click="sortBy('email')" style="cursor:pointer">Email</th>
                <th>Státusz</th>
                <th class="text-right">Műveletek</th>
            </tr>
            </thead>
            <tbody>
            @forelse($this->users as $user)
                <tr wire:key="user-row-{{ $user->id }}">
                    <td>{{ $user->id }}</td>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        {{-- Felhasználó státusza --}}
                        <span class="badge {{ ($user->is_active ?? true) ? 'badge-success' : 'badge-danger' }}">
                            {{ ($user->is_active ?? true) ? 'Aktív' : 'Tiltott' }}
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="btn-group">
                            {{-- Státusz váltó gomb --}}
                            <button wire:click="toggleStatus({{ $user->id }})"
                                    wire:loading.attr="disabled"
                                    class="btn btn-default btn-sm"
                                    title="Státusz váltása">
                                <i class="fas {{ ($user->is_active ?? true) ? 'fa-user-slash text-danger' : 'fa-user-check text-success' }}"></i>
                            </button>

                            {{-- Jelszó reset gomb --}}
                            <button wire:click="resetPassword({{ $user->id }})"
                                    wire:confirm="Biztosan új jelszót generálsz?"
                                    wire:loading.attr="disabled"
                                    class="btn btn-default btn-sm"
                                    title="Jelszó visszaállítása">
                                <i class="fas fa-key text-warning"></i>
                            </button>

                            {{-- Szerkesztés gomb --}}
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-default btn-sm" title="Szerkesztés">
                                <i class="fas fa-edit text-info"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center p-4">Nincs találat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{-- Lapozó sáv --}}
        {{ $this->users->links() }}
    </div>
</div>
