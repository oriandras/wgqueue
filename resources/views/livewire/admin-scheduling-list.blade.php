<?php
/**
 * Admin ütemezés lista Livewire (Volt) komponens.
 * Lehetővé teszi az összes kiküldés listázását, szűrését, rendezését,
 * kijelölés kezelését, valamint tömeges törlést értesítéssel és naplózással.
 */
use function Livewire\Volt\{state, updated, computed, usesPagination};
use App\Models\MailScheduling;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\UserSetting;
use App\Mail\SystemNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

usesPagination(theme: 'bootstrap');

state([
    // Kijelölés állapota
    'selectedRows' => [],
    'selectAll' => false,

    // Rendezés és lapozás
    'sortField' => 'start_time',
    'sortDirection' => 'desc',
    'perPage' => function() {
        return auth()->user()->settings->datatable_per_page ?? '10';
    },

    // A MEGLÉVŐ ÉRTÉKES SZŰRŐID
    'f_user' => '',
    'f_start' => '',
    'f_subject' => '',
    'f_count' => '',
    'f_status' => '',
]);

// Kijelölés logika: Csak az aktuális oldalon lévőket jelöljük ki
updated(['selectAll' => function ($value) {
    if ($value) {
        $this->selectedRows = $this->allMailings->pluck('id')->map(fn($id) => (string)$id)->toArray();
    } else {
        $this->selectedRows = [];
    }
}]);

// Beállítások mentése
updated(['perPage' => function ($value) {
    UserSetting::updateOrCreate(
        ['user_id' => auth()->id()],
        ['datatable_per_page' => $value]
    );
    $this->resetPage();
}]);

$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
};

// ADMIN TÖMEGES TÖRLÉS LOGOLÁSSAL ÉS ÉRTESÍTÉSSEL
$bulkDeleteAny = function () {
    if (empty($this->selectedRows)) return;

    $toDelete = MailScheduling::whereIn('id', $this->selectedRows)
        ->where('start_time', '>', now())
        ->with('user')
        ->get();

    $count = $toDelete->count();
    foreach ($toDelete as $item) {
        // 1. Logolás
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Admin Tömeges Törlés',
            'description' => "Admin törölte: " . $item->subject . " (Tulajdonos: " . ($item->user->name ?? 'Ismeretlen') . ")",
        ]);

        // 2. Értesítés küldése (app()->make-el a szintaktikai hiba elkerülésére)
        if ($item->user && $item->user->email) {
            $mailable = app()->make(SystemNotification::class, [
                'title' => 'Ütemezett kiküldés törölve',
                'message' => "Értesítünk, hogy a(z) '" . $item->subject . "' tárgyú, " . $item->start_time . " időpontra ütemezett kiküldésedet egy adminisztrátor eltávolította a rendszerből.",
                'buttonUrl' => route('scheduling.list'),
                'buttonText' => 'Saját listám megnyitása'
            ]);
            Mail::to($item->user->email)->send($mailable);
        }

        // 3. Törlés
        $item->delete();
    }

    $this->selectedRows = [];
    $this->selectAll = false;
    $this->dispatch('swal:success', message: $count . " elem sikeresen törölve a rendszerből.");
};

// A KOMPLEX LEKÉRDEZÉSED
$allMailings = computed(function () {
    return MailScheduling::with('user')
        ->when($this->f_user, function($q) {
            $q->whereHas('user', fn($sq) => $sq->where('name', 'like', '%' . $this->f_user . '%'));
        })
        ->when($this->f_subject, fn($q) => $q->where('subject', 'like', '%' . $this->f_subject . '%'))
        ->when($this->f_count, fn($q) => $q->where('mail_count', '>=', $this->f_count))
        ->when($this->f_status, function($q) {
            if ($this->f_status === 'past') return $q->where('start_time', '<=', Carbon::now());
            if ($this->f_status === 'future') return $q->where('start_time', '>', Carbon::now());
        })
        ->when($this->f_start, function($q) {
            return $q->whereDate('start_time', $this->f_start);
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage === 'all' ? 10000 : (int)$this->perPage);
});
?>

<div>
    <div class="card card-danger card-outline">
        <div class="card-header d-flex align-items-center">
            <h3 class="card-title mr-auto">Rendszerszintű kiküldések</h3>

            <div class="card-tools d-flex">
                @if(count($selectedRows) > 0)
                    <div class="btn-group mr-2">
                        @if(count($selectedRows) === 1)
                            <a href="{{ route('scheduling.edit', $selectedRows[0]) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-edit"></i> Szerkesztés
                            </a>
                        @endif
                        <button wire:click="bulkDeleteAny" wire:confirm="Adminisztrátorként törlöd a kijelölt jövőbeli ütemezéseket?" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Törlés ({{ count($selectedRows) }})
                        </button>
                    </div>
                @endif

                <div class="input-group input-group-sm" style="width: 100px;">
                    <select wire:model.live="perPage" class="form-control">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">Mind</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered table-hover m-0">
                <thead class="thead-light text-nowrap">
                <tr>
                    <th style="width: 40px" class="text-center">
                        <input type="checkbox" wire:model.live="selectAll">
                    </th>
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
                        db @if($sortField === 'mail_count') <i class="fas fa-sort-{{$sortDirection === 'asc' ? 'up' : 'down'}}"></i> @endif
                    </th>
                    <th>Státusz</th>
                    <th style="width: 50px"></th>
                </tr>
                <tr class="bg-light">
                    <td></td>
                    <td><input type="text" wire:model.live="f_user" class="form-control form-control-sm" placeholder="Név..."></td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="date" wire:model.live="f_start" class="form-control form-control-sm">
                            @if($f_start)
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" wire:click="$set('f_start', '')"><i class="fas fa-times"></i></button>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td><input type="text" wire:model.live="f_subject" class="form-control form-control-sm" placeholder="Tárgy..."></td>
                    <td><input type="number" wire:model.live="f_count" class="form-control form-control-sm" placeholder="Min."></td>
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
                    <tr class="{{ in_array($mailing->id, $selectedRows) ? 'table-warning' : '' }}">
                        <td class="text-center">
                            <input type="checkbox" wire:model.live="selectedRows" value="{{ (string)$mailing->id }}">
                        </td>
                        <td><span class="badge badge-info">{{ $mailing->user->name ?? 'Ismeretlen' }}</span></td>
                        <td>{{ $mailing->start_time }}</td>
                        <td>{{ $mailing->subject }}</td>
                        <td>{{ number_format($mailing->mail_count, 0, ',', ' ') }}</td>
                        <td>
                            @if(Carbon::parse($mailing->start_time)->isPast())
                                <span class="badge badge-secondary">Lezárult</span>
                            @else
                                <span class="badge badge-success">Ütemezve</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(Carbon::parse($mailing->start_time)->isFuture())
                                <a href="{{ route('scheduling.edit', $mailing->id) }}" class="text-info" title="Szerkesztés">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center p-4">Nincs a szűrésnek megfelelő találat.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">{{ $this->allMailings->links() }}</div>
            <div class="float-left text-muted">Összesen {{ $this->allMailings->total() }} találat</div>
        </div>
    </div>
</div>
