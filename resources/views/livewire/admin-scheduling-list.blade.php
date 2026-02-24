<?php
use function Livewire\Volt\{state, updated, computed, usesPagination};
use App\Models\MailScheduling;
use App\Models\User;
use Carbon\Carbon;

usesPagination(theme: 'bootstrap');

state([
    'sortField' => 'start_time',
    'sortDirection' => 'desc',
    'perPage' => function() {
        // Ha nincs még beállítása, adjunk vissza egy default értéket
        return auth()->user()->settings->datatable_per_page ?? '10';
    },
    // Oszloponkénti szűrők
    'f_user' => '',
    'f_start' => '',
    'f_subject' => '',
    'f_count' => '',
    'f_status' => '',
]);

updated(['perPage' => function ($value) {
    // Mentés az új táblába
    \App\Models\UserSetting::updateOrCreate(
        ['user_id' => auth()->id()],
        ['datatable_per_page' => $value]
    );

    // Oldalszám alaphelyzetbe állítása
    $this->resetPage();
}]);

// Rendezés váltó függvény
$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
};

$allMailings = computed(function () {
    return MailScheduling::with('user')
        // Oszloponkénti keresések
        ->when($this->f_user, function($q) {
            $q->whereHas('user', fn($sq) => $sq->where('name', 'like', '%' . $this->f_user . '%'));
        })
        ->when($this->f_subject, fn($q) => $q->where('subject', 'like', '%' . $this->f_subject . '%'))
        ->when($this->f_start, fn($q) => $q->where('start_time', 'like', '%' . $this->f_start . '%'))
        ->when($this->f_count, fn($q) => $q->where('mail_count', '>=', $this->f_count))
        // Státusz szűrő logika
        ->when($this->f_status, function($q) {
            if ($this->f_status === 'past') return $q->where('start_time', '<=', Carbon::now());
            if ($this->f_status === 'future') return $q->where('start_time', '>', Carbon::now());
        })
        // DateTime logika
        ->when($this->f_start, function($q) {
            // A dátumválasztó YYYY-MM-DD formátumot küld, a whereDate ezt kezeli
            return $q->whereDate('start_time', $this->f_start);
        })
        // Rendezés alkalmazása
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage === 'all' ? 10000 : (int)$this->perPage);
});
?>

<div class="card card-danger card-outline">
    <div class="card-header">
        <h3 class="card-title">Rendszerszintű kiküldések</h3>
        <div class="card-tools d-flex">
            <div class="input-group input-group-sm mr-2" style="width: 150px;">
                <div class="input-group-prepend">
                    <span class="input-group-text">Sorok:</span>
                </div>
                <select wire:model.live="perPage" class="form-control">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">Összes</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-hover m-0">
            <thead class="thead-light">
            <tr>
                <th wire:click="sortBy('user_id')" style="cursor:pointer">
                    Felhasználó @if($sortField === 'user_id') <i class="fas fa-sort-{{$sortDirection === 'asc' ? 'up' : 'down'}}"></i> @endif
                </th>
                <th wire:click="sortBy('start_time')" style="cursor:pointer">
                    Kezdés @if($sortField === 'start_time') <i class="fas fa-sort-{{$sortDirection === 'asc' ? 'up' : 'down'}}"></i> @endif
                </th>
                <th wire:click="sortBy('subject')" style="cursor:pointer">
                    Tárgy @if($sortField === 'subject') <i class="fas fa-sort-{{$sortDirection === 'asc' ? 'up' : 'down'}}"></i> @endif
                </th>
                <th wire:click="sortBy('mail_count')" style="cursor:pointer">
                    Mennyiség @if($sortField === 'mail_count') <i class="fas fa-sort-{{$sortDirection === 'asc' ? 'up' : 'down'}}"></i> @endif
                </th>
                <th>Státusz</th>
                <th style="width: 80px">Műv.</th>
            </tr>
            <tr class="bg-light">
                <td><input type="text" wire:model.live="f_user" class="form-control form-control-sm" placeholder="Név..."></td>
                <td style="min-width: 150px;">
                    <div class="input-group input-group-sm">
                        <input type="date"
                               wire:model.live="f_start"
                               class="form-control form-control-sm">
                        @if($f_start)
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary"
                                        type="button"
                                        wire:click="$set('f_start', '')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </td>
                <td><input type="text" wire:model.live="f_subject" class="form-control form-control-sm" placeholder="Tárgy..."></td>
                <td><input type="number" wire:model.live="f_count" class="form-control form-control-sm" placeholder="Min. db"></td>
                <td>
                    <select wire:model.live="f_status" class="form-control form-control-sm">
                        <option value="">Mind</option>
                        <option value="future">Ütemezve</option>
                        <option value="past">Lezárult</option>
                    </select>
                </td>
                <td></td>
            </tr>
            </thead>
            <tbody>
            @forelse($this->allMailings as $mailing)
                <tr>
                    <td><span class="badge badge-info">{{ $mailing->user->name ?? 'Ismeretlen' }}</span></td>
                    <td>{{ $mailing->start_time }}</td>
                    <td>{{ $mailing->subject }}</td>
                    <td>{{ number_format($mailing->mail_count, 0, ',', ' ') }}</td>
                    <td>
                        @if(\Carbon\Carbon::parse($mailing->start_time)->isPast())
                            <span class="badge badge-secondary">Lezárult</span>
                        @else
                            <span class="badge badge-success">Ütemezve</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(\Carbon\Carbon::parse($mailing->start_time)->isFuture())
                            <button wire:click="deleteAny({{ $mailing->id }})" wire:confirm="Törlöd?" class="btn btn-danger btn-xs">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Nincs a szűrésnek megfelelő találat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        <div class="float-right">
            {{ $this->allMailings->links() }}
        </div>
        <div class="float-left text-muted">
            Összesen {{ $this->allMailings->total() }} találat
        </div>
    </div>
</div>
