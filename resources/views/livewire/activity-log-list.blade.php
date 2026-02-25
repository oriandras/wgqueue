<?php
use function Livewire\Volt\{state, computed, usesPagination};
use App\Models\ActivityLog;

usesPagination(theme: 'bootstrap');

state([
    'f_user' => '', 'f_action' => '', 'f_date' => '',
    'sortField' => 'created_at', 'sortDirection' => 'desc',
    'perPage' => 15
]);

$allLogs = computed(fn() => ActivityLog::with('user')
    ->when($this->f_user, fn($q) => $q->whereHas('user', fn($sq) => $sq->where('name', 'like', "%{$this->f_user}%")))
    ->when($this->f_action, fn($q) => $q->where('action', 'like', "%{$this->f_action}%"))
    ->when($this->f_date, fn($q) => $q->whereDate('created_at', $this->f_date))
    ->orderBy($this->sortField, $this->sortDirection)
    ->paginate($this->perPage)
);
?>

<div class="card card-outline card-info">
    <div class="card-body p-0">
        <table class="table table-bordered table-sm m-0">
            <thead class="thead-light">
            <tr>
                <th>Felhasználó</th>
                <th>Művelet</th>
                <th>Leírás</th>
                <th>Időpont</th>
            </tr>
            <tr class="bg-light">
                <td><input wire:model.live="f_user" class="form-control form-control-sm" placeholder="Szűrés..."></td>
                <td><input wire:model.live="f_action" class="form-control form-control-sm" placeholder="Művelet..."></td>
                <td></td>
                <td><input type="date" wire:model.live="f_date" class="form-control form-control-sm"></td>
            </tr>
            </thead>
            <tbody>
            @foreach($this->allLogs as $log)
                <tr>
                    <td><span class="badge badge-secondary">{{ $log->user->name ?? 'Rendszer' }}</span></td>
                    <td><b>{{ $log->action }}</b></td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $this->allLogs->links() }}</div>
</div>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-modal', (event) => {
            $('#' + event.id).modal('show');
        });
    });
</script>
