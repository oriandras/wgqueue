<?php
use function Livewire\Volt\{state, updated, computed, usesPagination};
use App\Models\MailScheduling;
use App\Models\ActivityLog;
use Carbon\Carbon;

usesPagination(theme: 'bootstrap');

state([
    'selectedRows' => [],
    'selectAll' => false,
    'sortField' => 'start_time',
    'sortDirection' => 'desc',
    'perPage' => function() {
        return auth()->user()->settings->datatable_per_page ?? '10';
    },
    'f_start' => '', 'f_subject' => '', 'f_count' => '', 'f_status' => '',
]);

// ... (A bulkDelete, updated, sortBy és mailings logika marad a régiben) ...
// Megjegyzés: Az editSelected és editSingle függvényekre itt már nincs szükség,
// mert sima <a> linkeket fogunk használni.

$bulkDelete = function () {
    if (empty($this->selectedRows)) return;

    $toDelete = MailScheduling::whereIn('id', $this->selectedRows)
        ->where('user_id', auth()->id())
        ->where('start_time', '>', now())
        ->get();

    foreach ($toDelete as $item) {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Tömeges törlés',
            'description' => "Törölve: {$item->subject}",
        ]);
        $item->delete();
    }

    $this->selectedRows = [];
    $this->selectAll = false;
    $this->dispatch('swal:success', message: 'Törlés sikeres.');
};

$mailings = computed(function () {
    return MailScheduling::where('user_id', auth()->id())
        ->when($this->f_subject, fn($q) => $q->where('subject', 'like', '%' . $this->f_subject . '%'))
        ->when($this->f_start, fn($q) => $q->whereDate('start_time', $this->f_start))
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage === 'all' ? 10000 : (int)$this->perPage);
});
?>

{{-- FONTOS: Ez az egyetlen közös gyökér elem, ami körbeveszi a komponenst --}}
<div>
    <div class="card card-outline card-primary">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title mr-auto">Saját ütemezett levelek</h3>

            <div class="card-tools d-flex">
                @if(count($selectedRows) > 0)
                    <div class="btn-group mr-2">
                        {{-- HA 1 ELEM VAN KIJELÖLVE: Sima link a szerkesztő oldalra --}}
                        @if(count($selectedRows) === 1)
                            <a href="{{ route('scheduling.edit', $selectedRows[0]) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-edit"></i> Szerkesztés
                            </a>
                        @endif
                        <button wire:click="bulkDelete" wire:confirm="Törlöd?" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Törlés ({{ count($selectedRows) }})
                        </button>
                    </div>
                @endif

                <select wire:model.live="perPage" class="form-control form-control-sm" style="width: 80px;">
                    <option value="10">10</option>
                    <option value="all">Mind</option>
                </select>
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered table-hover m-0">
                <thead>
                <tr class="thead-light">
                    <th style="width: 40px" class="text-center">
                        <input type="checkbox" wire:model.live="selectAll">
                    </th>
                    <th>Kezdés</th>
                    <th>Tárgy</th>
                    <th>Mennyiség</th>
                    <th>Státusz</th>
                    <th style="width: 50px"></th>
                </tr>
                </thead>
                <tbody>
                @forelse($this->mailings as $mailing)
                    <tr class="{{ in_array($mailing->id, $selectedRows) ? 'table-warning' : '' }}">
                        <td class="text-center">
                            <input type="checkbox" wire:model.live="selectedRows" value="{{ (string)$mailing->id }}">
                        </td>
                        <td>{{ $mailing->start_time }}</td>
                        <td>{{ $mailing->subject }}</td>
                        <td>{{ number_format($mailing->mail_count, 0, ',', ' ') }}</td>
                        <td>
                            <span class="badge {{ Carbon::parse($mailing->start_time)->isPast() ? 'badge-secondary' : 'badge-success' }}">
                                {{ Carbon::parse($mailing->start_time)->isPast() ? 'Lezárult' : 'Ütemezve' }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{-- SOR VÉGI LINK: Átirányít a szerkesztő oldalra --}}
                            @if(Carbon::parse($mailing->start_time)->isFuture())
                                <a href="{{ route('scheduling.edit', $mailing->id) }}" class="text-info" title="Szerkesztés">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Nincs találat.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">{{ $this->mailings->links() }}</div>
        </div>
    </div>
</div>
