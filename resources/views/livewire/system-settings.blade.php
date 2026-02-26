<?php
/**
 * Rendszerbeállítások kezelése Livewire (Volt) komponens.
 * Lehetővé teszi az adminisztrátorok számára a globális kiküldési korlátok,
 * értesítési e-mail címek és a zárolt (karbantartási) időszakok kezelését.
 */
use function Livewire\Volt\{state, mount, action};
use App\Models\SystemSetting;
use App\Models\MaintenanceWindow;
use Carbon\Carbon;

// Komponens állapota
state([
    // Általános beállítások mezői
    'hourly_limit' => 5000,
    'server_team_email' => '',
    'maintainer_email' => '',

    // Új zárolt időszak (karbantartás) rögzítéséhez használt mezők
    'new_title' => '',
    'new_start' => '',
    'new_end' => '',

    // Aktuális karbantartási ablakok listája
    'windows' => []
]);

/**
 * Komponens inicializálása: beállítások és karbantartási ablakok betöltése.
 */
mount(function () {
    // Globális rendszerbeállítások lekérése az adatbázisból
    $this->hourly_limit = SystemSetting::get('hourly_limit', 5000);
    $this->server_team_email = SystemSetting::get('server_team_email', '');
    $this->maintainer_email = SystemSetting::get('maintainer_email', '');

    $this->loadWindows();
});

/**
 * Karbantartási időszakok betöltése időrendben csökkenő sorrendben.
 */
$loadWindows = function () {
    $this->windows = MaintenanceWindow::orderBy('start_time', 'desc')->get();
};

/**
 * Általános rendszerbeállítások mentése.
 */
$saveGeneral = function () {
    SystemSetting::set('hourly_limit', $this->hourly_limit);
    SystemSetting::set('server_team_email', $this->server_team_email);
    SystemSetting::set('maintainer_email', $this->maintainer_email);

    $this->dispatch('swal:success', message: 'Rendszerbeállítások elmentve.');
};

/**
 * Új karbantartási (zárolt) időszak hozzáadása.
 */
$addWindow = function () {
    // Bemeneti adatok validálása
    $this->validate([
        'new_title' => 'required|min:3',
        'new_start' => 'required|date',
        'new_end' => 'required|date|after:new_start',
    ]);

    // Új rekord létrehozása
    MaintenanceWindow::create([
        'title' => $this->new_title,
        'start_time' => $this->new_start,
        'end_time' => $this->new_end,
    ]);

    // Mezők ürítése és lista frissítése
    $this->new_title = '';
    $this->new_start = '';
    $this->new_end = '';
    $this->loadWindows();

    $this->dispatch('swal:success', message: 'Zárolt időszak rögzítve.');
};

/**
 * Karbantartási időszak törlése azonosító alapján.
 *
 * @param int $id A rekord azonosítója
 */
$deleteWindow = function ($id) {
    MaintenanceWindow::find($id)?->delete();
    $this->loadWindows();
};
?>

<div class="row">
    {{-- Általános beállítások szekció --}}
    <div class="col-md-5">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Kiküldési korlátok és Értesítések</h3></div>
            <div class="card-body">
                {{-- Óránkénti kiküldési limit --}}
                <div class="form-group">
                    <label>Óránkénti max. kiküldés (db)</label>
                    <input type="number" wire:model="hourly_limit" class="form-control">
                </div>
                <hr>
                {{-- Értesítendő e-mail címek --}}
                <div class="form-group">
                    <label>Szerveres kollégák email címe</label>
                    <input type="email" wire:model="server_team_email" class="form-control" placeholder="admin@szerver.hu">
                </div>
                <div class="form-group">
                    <label>Szoftver fenntartó email címe (Te)</label>
                    <input type="email" wire:model="maintainer_email" class="form-control">
                </div>
            </div>
            <div class="card-footer text-right">
                {{-- Általános beállítások mentése gomb --}}
                <button wire:click="saveGeneral" class="btn btn-primary">Mentés</button>
            </div>
        </div>
    </div>

    {{-- Zárolt időszakok (Maintenance Windows) kezelése szekció --}}
    <div class="col-md-7">
        <div class="card card-warning card-outline">
            <div class="card-header"><h3 class="card-title">Zárolt időszakok kezelése</h3></div>
            <div class="card-body">
                {{-- Új zárolt időszak rögzítése űrlap --}}
                <div class="row mb-4 bg-light p-2 rounded border">
                    <div class="col-md-4">
                        <label class="small">Megnevezés</label>
                        <input type="text" wire:model="new_title" class="form-control form-control-sm" placeholder="Karbantartás oka">
                    </div>
                    <div class="col-md-3">
                        <label class="small">Kezdet</label>
                        <input type="datetime-local" wire:model="new_start" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="small">Vége</label>
                        <input type="datetime-local" wire:model="new_end" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="small">&nbsp;</label>
                        <button wire:click="addWindow" class="btn btn-success btn-sm btn-block">Hozzáad</button>
                    </div>
                </div>

                {{-- Karbantartási időszakok táblázata --}}
                <table class="table table-sm table-hover">
                    <thead>
                    <tr>
                        <th>Megnevezés</th>
                        <th>Kezdet</th>
                        <th>Vége</th>
                        <th class="text-right">Művelet</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($windows as $win)
                        <tr>
                            <td>{{ $win->title }}</td>
                            <td>{{ Carbon::parse($win->start_time)->format('Y-m-d H:i') }}</td>
                            <td>{{ Carbon::parse($win->end_time)->format('Y-m-d H:i') }}</td>
                            <td class="text-right">
                                {{-- Törlés gomb --}}
                                <button wire:click="deleteWindow({{ $win->id }})" class="btn btn-danger btn-xs" title="Törlés">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    @if($windows->isEmpty())
                        <tr><td colspan="4" class="text-center text-muted p-3">Nincs rögzített zárolt időszak.</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
