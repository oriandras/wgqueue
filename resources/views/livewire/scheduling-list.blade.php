<?php
/**
 * Saját ütemezések listája Livewire (Volt) komponens.
 * Kilistázza a bejelentkezett felhasználó saját levélkiküldéseit,
 * lehetővé teszi a keresést, lapozást és a jövőbeli kiküldések törlését/szerkesztését.
 */
use function Livewire\Volt\{state, updated, computed, usesPagination};
use App\Models\MailScheduling;
use App\Models\ActivityLog;
use Carbon\Carbon;

// Bootstrap alapú lapozás használata
usesPagination(theme: 'bootstrap');

// Komponens állapota
state([
    'selectedRows' => [], // Kijelölt sorok azonosítói
    'selectAll' => false, // Összes kijelölése állapot
    'sortField' => 'start_time',
    'sortDirection' => 'desc',
    'perPage' => function() {
        // Alapértelmezett lapszám a felhasználói beállításokból
        return auth()->user()->settings->datatable_per_page ?? '10';
    },
    'f_start' => '',
    'f_subject' => '',
    'f_count' => '',
    'f_status' => '',
]);
    /**
     * Figyeljük a "Mindet kijelöl" checkbox változását.
     */
    updated(['selectAll' => function ($value) {
        if ($value) {
            // Aktuális oldal ID-k begyűjtése stringként
            $this->selectedRows = $this->mailings->getCollection()
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedRows = [];
        }
    }]);

    /**
     * Figyeljük az egyedi kijelöléseket.
     */
    updated(['selectedRows' => function ($value) {
        // Az aktuális oldalon lévő ID-k stringként
        $currentIds = $this->mailings->getCollection()
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        // Ellenőrizzük, hogy az összes aktuális ID benne van-e a kijelöltek között
        $allSelected = count(array_intersect($currentIds, $this->selectedRows)) === count($currentIds);

        $this->selectAll = (count($currentIds) > 0 && $allSelected);
    }]);
    /**
     * Kijelölt elemek tömeges törlése.
     * Csak a jövőbeli, még el nem kezdődött kiküldések törölhetők.
     */
    $bulkDelete = function () {
        if (empty($this->selectedRows)) return;

        // 1. Megnézzük, hányat akart törölni
        $totalSelected = count($this->selectedRows);

        // 2. Lekérjük azokat, amik VALÓBAN törölhetők (jövőbeliek)
        $toDelete = MailScheduling::whereIn('id', $this->selectedRows)
            ->where('user_id', auth()->id())
            ->where('start_time', '>', now())
            ->get();

        $deletedCount = $toDelete->count();

        // 3. Ha egyet sem találtunk, ami törölhető lenne
        if ($deletedCount === 0) {
            $this->dispatch('swal:error', message: 'Hiba: Csak a jövőbeli, még el nem kezdődött ütemezések törölhetők!');
            return;
        }

        // 4. Törlés végrehajtása
        foreach ($toDelete as $item) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Tömeges törlés',
                'description' => "Törölve: {$item->subject}",
            ]);
            $item->delete();
        }

        // Állapot frissítése
        $this->selectedRows = [];
        $this->selectAll = false;

        // 5. Differenciált visszajelzés
        if ($deletedCount < $totalSelected) {
            $this->dispatch('swal:warning', message: "Sikeresen törölve: $deletedCount ütemezés. A már lezárult/folyamatban lévő elemeket biztonsági okokból megőriztük.");
        } else {
            $this->dispatch('swal:success', message: 'A kijelölt ütemezések sikeresen törölve.');
        }
    };
/**
 * A bejelentkezett felhasználó kiküldéseinek lekérése a szűrők alapján.
 */
$mailings = computed(function () {
    return MailScheduling::where('user_id', auth()->id())
        ->when($this->f_subject, fn($q) => $q->where('subject', 'like', '%' . $this->f_subject . '%'))
        ->when($this->f_start, fn($q) => $q->whereDate('start_time', $this->f_start))
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage === 'all' ? 10000 : (int)$this->perPage);
});
?>

<div>
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title mr-auto">Saját ütemezett levelek</h3>

            <div class="card-tools d-flex">
                {{-- Tömeges műveletek gombjai, ha van kijelölt elem --}}
                @if(count($selectedRows) > 0)
                    <div class="btn-group mr-2">
                        @if(count($selectedRows) === 1)
                            {{-- Egy elem esetén közvetlen szerkesztés link --}}
                            <a href="{{ route('scheduling.edit', $selectedRows[0]) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-edit"></i> Szerkesztés
                            </a>
                        @endif
                        {{-- Törlés gomb megerősítéssel --}}
                        <button wire:click="bulkDelete" wire:confirm="Biztosan törlöd a kijelölt (jövőbeli) elemeket?" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Törlés ({{ count($selectedRows) }})
                        </button>
                    </div>
                @endif

                {{-- Oldalankénti elemszám választó --}}
                <select wire:model.live="perPage" class="form-control form-control-sm" style="width: 80px;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="all">Mind</option>
                </select>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- Kiküldések táblázata --}}
            <table class="table table-bordered table-hover m-0 table-sm">
                <thead>
                <tr class="bg-light">
                    <th style="width: 40px" class="text-center">
                        {{-- Összes kijelölése checkbox --}}
                        <input type="checkbox" wire:model.live="selectAll">
                    </th>
                    <th>Kezdés</th>
                    <th>Tárgy / Kampány</th>
                    <th>Mennyiség</th>
                    <th>Státusz</th>
                    <th style="width: 50px" class="text-center">Művelet</th>
                </tr>
                </thead>
                <tbody>
                @forelse($this->mailings as $mailing)
                    <tr wire:key="mailing-{{ $mailing->id }}" class="{{ in_array((string)$mailing->id, $selectedRows) ? 'table-warning' : '' }}">
                        <td class="text-center">
                            {{-- Egyedi kijelölés checkbox --}}
                            <input type="checkbox"
                                   id="checkbox-{{ $mailing->id }}"
                                   wire:key="checkbox-{{ $mailing->id }}"
                                   wire:model.live="selectedRows"
                                   value="{{ (string)$mailing->id }}"
                                   {{-- Kényszerített vizuális szinkron --}}
                                   :checked="in_array('{{ $mailing->id }}', selectedRows)">
                        </td>
                        <td>{{ Carbon::parse($mailing->start_time)->format('Y.m.d. H:i') }}</td>
                        <td>{{ $mailing->subject }}</td>
                        <td>{{ number_format($mailing->mail_count, 0, ',', ' ') }} db</td>
                        <td>
                            {{-- Állapot jelvény --}}
                            <span class="badge {{ Carbon::parse($mailing->start_time)->isPast() ? 'badge-secondary' : 'badge-success' }}">
                                {{ Carbon::parse($mailing->start_time)->isPast() ? 'Lezárult' : 'Ütemezve' }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{-- Szerkesztés ikon, csak jövőbeli kiküldéseknél --}}
                            @if(Carbon::parse($mailing->start_time)->isFuture())
                                <a href="{{ route('scheduling.edit', $mailing->id) }}" class="text-info" title="Szerkesztés">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    {{-- Nincs adat állapot --}}
                    <tr><td colspan="6" class="text-center p-4 text-muted">Nem találtunk a feltételeknek megfelelő ütemezést.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{-- Lapozó sáv --}}
            <div class="float-right">{{ $this->mailings->links() }}</div>
        </div>
    </div>
</div>
