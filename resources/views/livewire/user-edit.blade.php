<?php
use function Livewire\Volt\{state, mount};
use App\Models\User;
use App\Models\ActivityLog;

state(['userId' => null, 'name' => '', 'email' => '', 'is_active' => true]);

mount(function ($id) {
    $user = User::findOrFail($id);
    $this->userId = $user->id;
    $this->name = $user->name;
    $this->email = $user->email;
    $this->is_active = $user->is_active ?? true;
});

$save = function () {
    $user = User::findOrFail($this->userId);

    $this->validate([
        'name' => 'required|min:3',
        'email' => 'required|email|unique:sys_users,email,' . $user->id,
    ]);

    $user->update([
        'name' => $this->name,
        'email' => $this->email,
        'is_active' => $this->is_active,
    ]);

    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'User Szerkesztés',
        'description' => "Módosítva: {$user->email} (Admin által)",
    ]);

    session()->flash('success', 'Felhasználó adatai frissítve!');
    return redirect()->route('admin.users');
};
?>

<div class="row">
    <div class="col-md-6">
        <div class="card card-info card-outline">
            <form wire:submit.prevent="save">
                <div class="card-body">
                    <div class="form-group">
                        <label>Név</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" wire:model="email" class="form-control">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Státusz</label>
                        <select wire:model="is_active" class="form-control">
                            <option value="1">Aktív</option>
                            <option value="0">Felfüggesztve</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.users') }}" class="btn btn-default">Mégse</a>
                    <button type="submit" class="btn btn-info">Módosítások mentése</button>
                </div>
            </form>
        </div>
    </div>
</div>
