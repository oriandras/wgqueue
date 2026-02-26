<?php
/**
 * Hibanapló lista Livewire (Volt) komponens.
 * Kilistázza a rendszerben keletkezett hibákat (sys_errors tábla),
 * lehetővé teszi a keresést és a részletes stack trace megtekintését.
 */
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\ErrorLog;

// Bootstrap alapú lapozás használata
usesPagination(theme: 'bootstrap');

// Komponens állapota
state([
    'search' => '',
    'perPage' => 10,
    'selectedTrace' => '' // Ebben tároljuk a megjelenítendő stack trace szöveget
]);

/**
 * Stack trace betöltése és a modal megnyitása.
 *
 * @param int $id A hiba rekord azonosítója
 */
$showTrace = function ($id) {
    $error = ErrorLog::find($id);
    if ($error) {
        $this->selectedTrace = $error->stack_trace;
        // JS esemény küldése a modal megnyitásához
        $this->dispatch('open-modal', id: 'modal-error-trace');
    }
};

/**
 * A teljes hibanapló törlése.
 */
$deleteLogs = fn() => [
    ErrorLog::truncate(),
    $this->dispatch('swal:success', message: 'Hibanapló ürítve.')
];

/**
 * Hibák lekérése a keresési feltétel alapján, időrendben csökkenő sorrendben.
 */
$allErrors = computed(fn() => ErrorLog::with('user')
    ->where('message', 'like', "%{$this->search}%")
    ->orderBy('created_at', 'desc')
    ->paginate($this->perPage)
);
?>

<div>
    <div class="card card-outline card-danger">
        <div class="card-header">
            {{-- Napló ürítése gomb megerősítéssel --}}
            <button wire:click="deleteLogs" wire:confirm="Biztosan törlöd az összes hibanaplót?" class="btn btn-danger btn-xs float-right">
                <i class="fas fa-trash"></i> Napló ürítése
            </button>
            {{-- Kereső mező az üzenetekre --}}
            <input wire:model.live="search" class="form-control form-control-sm w-25" placeholder="Keresés...">
        </div>
        <div class="card-body p-0 text-sm">
            {{-- Hibák táblázata --}}
            <table class="table table-hover table-sm m-0">
                <thead>
                <tr>
                    <th>Időpont</th>
                    <th>Hibaüzenet</th>
                    <th class="text-center">Trace</th>
                </tr>
                </thead>
                <tbody>
                @foreach($this->allErrors as $error)
                    <tr>
                        <td nowrap>{{ $error->created_at }}</td>
                        <td class="text-danger">
                            <b>{{ Str::limit($error->message, 120) }}</b>
                            <br><small class="text-muted">{{ $error->url }}</small>
                        </td>
                        <td class="text-center">
                            {{-- Részletek (stack trace) megtekintése gomb --}}
                            <button wire:click="showTrace({{ $error->id }})" class="btn btn-default btn-xs">
                                <i class="fas fa-search text-primary"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{-- Lapozó sáv --}}
            {{ $this->allErrors->links() }}
        </div>
    </div>

    {{-- Stack Trace megjelenítésére szolgáló modal --}}
    <div class="modal fade" id="modal-error-trace" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title">Stack Trace részletei</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body bg-dark">
                    {{-- Formázott hibakód megjelenítése --}}
                    <pre class="text-white p-3" style="white-space: pre-wrap; word-wrap: break-word; max-height: 70vh; overflow-y: auto;">{{ $selectedTrace }}</pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Bezárás</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Livewire eseménykezelők regisztrálása.
     */
    document.addEventListener('livewire:initialized', () => {
        // Modal megnyitása eseményre
        Livewire.on('open-modal', (event) => {
            $('#' + event.id).modal('show');
        });
    });
</script>
