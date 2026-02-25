<?php
use function Livewire\Volt\{state, computed};
use App\Models\User;

$onlineUsers = computed(function () {
    // Aki az utolsó 5 percben aktív volt
    return User::where('last_seen_at', '>=', now()->subMinutes(5))
        ->where('is_active', true)
        ->get();
});
?>

<div class="card card-outline card-success" wire:poll.10s> {{-- 10 másodpercenként frissül --}}
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users text-success mr-2"></i> Jelenleg online</h3>
    </div>
    <div class="card-body p-0">
        <ul class="nav nav-pills flex-column">
            @forelse($this->onlineUsers as $user)
                <li class="nav-item">
                    <span class="nav-link">
                        <i class="fas fa-circle text-success btn-xs mr-2" style="font-size: 0.6rem;"></i>
                        {{ $user->name }}
                        <span class="float-right text-muted small">{{ $user->email }}</span>
                    </span>
                </li>
            @empty
                <li class="nav-item p-3 text-muted text-center">Nincs aktív felhasználó.</li>
            @endforelse
        </ul>
    </div>
</div>
