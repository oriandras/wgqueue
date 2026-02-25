<?php
use function Livewire\Volt\{state, mount, action};
use App\Models\SystemSetting; // Feltételezve, hogy van ilyen táblád
use App\Models\MaintenanceWindow; // A sch_maintenance_windows táblához
use Carbon\Carbon;

state([
    // Általános beállítások
    'hourly_limit' => 5000,
    'server_team_email' => '',
    'maintainer_email' => '',

    // Új zárolt időszak mezői
    'new_title' => '',
    'new_start' => '',
    'new_end' => '',

    'windows' => []
]);

mount(function () {
    // Beállítások betöltése (példa logika)
    $this->hourly_limit = SystemSetting::get('hourly_limit', 5000);
    $this->server_team_email = SystemSetting::get('server_team_email', '');
    $this->maintainer_email = SystemSetting::get('maintainer_email', '');

    $this->loadWindows();
});

$loadWindows = function () {
    $this->windows = MaintenanceWindow::orderBy('start_time', 'desc')->get();
};

$saveGeneral = function () {
    SystemSetting::set('hourly_limit', $this->hourly_limit);
    SystemSetting::set('server_team_email', $this->server_team_email);
    SystemSetting::set('maintainer_email', $this->maintainer_email);

    $this->dispatch('swal:success', message: 'Rendszerbeállítások elmentve.');
};

$addWindow = function () {
    $this->validate([
        'new_title' => 'required|min:3',
        'new_start' => 'required|date',
        'new_end' => 'required|date|after:new_start',
    ]);

    MaintenanceWindow::create([
        'title' => $this->new_title,
        'start_time' => $this->new_start,
        'end_time' => $this->new_end,
    ]);

    $this->new_title = ''; $this->new_start = ''; $this->new_end = '';
    $this->loadWindows();
    $this->dispatch('swal:success', message: 'Zárolt időszak rögzítve.');
};

$deleteWindow = function ($id) {
    MaintenanceWindow::find($id)?->delete();
    $this->loadWindows();
};
?>

<div class="row">
    {{-- ÁLTALÁNOS BEÁLLÍTÁSOK --}}
    <div class="col-md-5">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Kiküldési korlátok és Értesítések</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Óránkénti max. kiküldés (db)</label>
                    <input type="number" wire:model="hourly_limit" class="form-control">
                </div>
                <hr>
                <div class="form-group">
                    <label>Szerveres kollégák email címe</label>
                    <input type="email" wire:model="server_team_email" class="form-control" placeholder="admin@szerver.hu">
                </div>
                <div class="form-group">
                    <label>Szoftver fenntartó email címe (Te)</label>
                    <input type="email" wire:model="maintainer_email" class="form-control">
                </div>
            </div>
            <div class="card-footer">
                <button wire:click="saveGeneral" class="btn btn-primary float-right">Mentés</button>
            </div>
        </div>
    </div>

    {{-- ZÁROLT IDŐSZAKOK (MAINTENANCE WINDOWS) --}}
    <div class="col-md-7">
        <div class="card card-warning card-outline">
            <div class="card-header"><h3 class="card-title">Zárolt időszakok kezelése</h3></div>
            <div class="card-body">
                {{-- Új felvétele --}}
                <div class="row mb-4 bg-light p-2 rounded">
                    <div class="col-md-4">
                        <input type="text" wire:model="new_title" class="form-control form-control-sm" placeholder="Megnevezés">
                    </div>
                    <div class="col-md-3">
                        <input type="datetime-local" wire:model="new_start" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <input type="datetime-local" wire:model="new_end" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <button wire:click="addWindow" class="btn btn-success btn-sm btn-block">Hozzáad</button>
                    </div>
                </div>

                {{-- Lista --}}
                <table class="table table-sm table-hover">
                    <thead>
                    <tr>
                        <th>Megnevezés</th>
                        <th>Kezdet</th>
                        <th>Vége</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($windows as $win)
                        <tr>
                            <td>{{ $win->title }}</td>
                            <td>{{ Carbon::parse($win->start_time)->format('Y-m-d H:i') }}</td>
                            <td>{{ Carbon::parse($win->end_time)->format('Y-m-d H:i') }}</td>
                            <td class="text-right">
                                <button wire:click="deleteWindow({{ $win->id }})" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
