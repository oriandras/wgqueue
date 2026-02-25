<?php
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\ErrorLog;

usesPagination(theme: 'bootstrap');

state([
    'search' => '',
    'perPage' => 10,
    'selectedTrace' => '' // Ebben tároljuk a megjelenítendő trace-t
]);

// Trace betöltése a modalba
$showTrace = function ($id) {
    $error = ErrorLog::find($id);
    if ($error) {
        $this->selectedTrace = $error->stack_trace;
        $this->dispatch('open-modal', id: 'modal-error-trace');
    }
};

$deleteLogs = fn() => [ErrorLog::truncate(), $this->dispatch('swal:success', message: 'Hibanapló ürítve.')];

$allErrors = computed(fn() => ErrorLog::with('user')
    ->where('message', 'like', "%{$this->search}%")
    ->orderBy('created_at', 'desc')
    ->paginate($this->perPage)
);
?>

<div> {{-- Root element a hiba elkerülésére --}}
    <div class="card card-outline card-danger">
        <div class="card-header">
            <button wire:click="deleteLogs" wire:confirm="Biztosan törlöd az összes hibanaplót?" class="btn btn-danger btn-xs float-right">
                <i class="fas fa-trash"></i> Napló ürítése
            </button>
            <input wire:model.live="search" class="form-control form-control-sm w-25" placeholder="Keresés...">
        </div>
        <div class="card-body p-0 text-sm">
            <table class="table table-hover table-sm m-0">
                <thead>
                <tr>
                    <th>Időpont</th>
                    <th>Hibaüzenet</th>
                    <th>Trace</th>
                </tr>
                </thead>
                <tbody>
                @foreach($this->allErrors as $error)
                    <tr>
                        <td nowrap>{{ $error->created_at }}</td>
                        <td class="text-danger"><b>{{ Str::limit($error->message, 120) }}</b><br><small>{{ $error->url }}</small></td>
                        <td class="text-center">
                            {{-- Most már a Livewire függvényt hívjuk meg --}}
                            <button wire:click="showTrace({{ $error->id }})" class="btn btn-default btn-xs">
                                <i class="fas fa-search text-primary"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $this->allErrors->links() }}</div>
    </div>

    <div class="modal fade" id="modal-error-trace" wire:ignore.self>
        <div class="modal-dialog modal-xl"> {{-- Extra large méret a sok szövegnek --}}
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title">Stack Trace részletei</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body bg-dark">
                    {{-- A 'pre' tag megőrzi a sortöréseket és a formázást --}}
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
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-modal', (event) => {
            $('#' + event.id).modal('show');
        });
    });
</script>
