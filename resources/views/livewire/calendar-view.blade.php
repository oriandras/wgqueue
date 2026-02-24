<?php

use function Livewire\Volt\{state, mount, rules};
use App\Models\MailScheduling;
use App\Models\MaintenanceWindow;

state([
    'events' => [],
    'start_time' => '',
    'mail_count' => '',
    'subject' => '',
    'group_name' => ''
]);

rules([
    'start_time' => 'required|date|after:now',
    'mail_count' => 'required|integer|min:1',
    'subject'    => 'required|min:3',
    'group_name' => 'required',
]);

/**
 * Ez a függvény most már csak ÖSSZEGYŰJTI az adatokat és VISSZAADJA őket.
 * Így elkerüljük a $this használatát a closure-ön belül.
 */
$fetchEvents = function () {
    $schedulings = MailScheduling::with('user')->get()->map(fn($item) => [
        'title' => ($item->user->name ?? 'Ismeretlen') . ': ' . $item->subject,
        'start' => $item->start_time,
        'end'   => $item->calculated_end_time,
        'color' => '#17a2b8',
    ]);

    $maintenances = MaintenanceWindow::all()->map(fn($item) => [
        'title' => '⚠️ ' . $item->title,
        'start' => $item->start_time,
        'end'   => $item->end_time,
        'color' => '#dc3545',
    ]);

    return $schedulings->concat($maintenances)->toArray();
};

// A mount-ban már van objektum kontextus, itt beállíthatjuk a state-et
mount(function () use ($fetchEvents) {
    $this->events = $fetchEvents();
});

$save = function () use ($fetchEvents) {
    $this->validate();

    $startTime = \Carbon\Carbon::parse($this->start_time);

    // 1. ÚJ ELLENŐRZÉS: Ne lehessen a múltba ütemezni
    // A Carbon isPast() metódusa megnézi, hogy az időpont korábbi-e, mint a mostani pillanat
    if ($startTime->isPast()) {
        $this->addError('start_time', 'A múltba nem ütemezhetsz kiküldést! Kérlek, válassz jövőbeli időpontot.');
        return;
    }

    // 2. Kiszámoljuk a tervezett végidőpontot az ütközésvizsgálathoz
    $limitPerMinute = (int) (\Illuminate\Support\Facades\DB::table('sys_settings')
        ->where('key', 'mails_per_minute')
        ->value('value') ?? 100);

    $durationMinutes = ceil($this->mail_count / $limitPerMinute);
    $endTime = (clone $startTime)->addMinutes($durationMinutes);

    // 3. Ütközésvizsgálat (Overlap check)
    $overlap = MailScheduling::where(function ($query) use ($startTime, $endTime) {
        $query->where('start_time', '<', $endTime)
            ->where('calculated_end_time', '>', $startTime);
    })->exists();

    if ($overlap) {
        $this->addError('start_time', 'Ez az időpont már foglalt vagy átfedésben van egy másik kiküldéssel!');
        return;
    }

    // 4. Mentés
    MailScheduling::create([
        'user_id'    => auth()->id(),
        'start_time' => $this->start_time,
        'mail_count' => $this->mail_count,
        'subject'    => $this->subject,
        'group_name' => $this->group_name,
    ]);

    $this->reset(['start_time', 'mail_count', 'subject', 'group_name']);
    $this->events = $fetchEvents();
    $this->dispatch('calendar-updated', events: $this->events);
};

?>

<div>
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-calendar-alt mr-1"></i> Kiküldési Naptár</h3>
            <button type="button" class="btn btn-success btn-sm ml-auto" data-toggle="modal" data-target="#modal-scheduling">
                <i class="fas fa-plus mr-1"></i> Új ütemezés
            </button>
        </div>
        <div class="card-body">
            <div id="calendar" wire:ignore style="min-height: 200px;"></div>
        </div>
    </div>

    <div class="modal fade" id="modal-scheduling" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title">Új kiküldés rögzítése</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kezdési időpont</label>
                            <input type="datetime-local"
                                   id="start_time_input"
                                   class="form-control"
                                   wire:model="start_time"
                                   min="{{ now()->format('Y-m-d\TH:i') }}">
                            @error('start_time') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>E-mailek száma</label>
                            <input type="number" class="form-control" wire:model="mail_count" placeholder="Pl. 5000">
                            @error('mail_count') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Tárgy / Kampány neve</label>
                            <input type="text" class="form-control" wire:model="subject" placeholder="Hírlevél...">
                            @error('subject') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Csoport neve</label>
                            <input type="text" class="form-control" wire:model="group_name" placeholder="Vásárlók...">
                            @error('group_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Mégse</button>
                        <button type="submit" class="btn btn-primary">Mentés és foglalás</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/hu.global.min.js'></script>

    <script>
        var calendar;

        function renderCalendar() {
            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            if (calendar) { calendar.destroy(); }

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'hu',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                // 1. BEÁLLÍTÁS: Növeljük a slotok idejét (kevesebb vízszintes vonal)
                slotDuration: '00:20:00', // 30 perc helyett 1 órás sávok
                slotMinTime: '00:00:00',
                slotMaxTime: '23:59:59',
                firstDay: 1,
                // 2. BEÁLLÍTÁS: Fix magasság belső görgetővel (height: 'auto' helyett)
                contentHeight: 600,
                // Alapértelmezetten a munkaidő végére ugorjon a görgető,
                // de felfelé görgetve ott lesz az előzmény is
                scrollTime: '17:00:00',
                allDaySlot: false,
                events: @json($events),
                //height: 'auto'
            });

            calendar.render();
        }

        document.addEventListener('livewire:initialized', function() {
            renderCalendar();

            Livewire.on('calendar-updated', (data) => {
                $('#modal-scheduling').modal('hide');
                calendar.removeAllEvents();
                calendar.addEventSource(data.events);
            });
        });

        // Biztosítjuk, hogy navigáció után is újraépüljön
        document.addEventListener('livewire:navigated', renderCalendar);

        // Amikor a modális ablak megnyitása elindul
        $('#modal-scheduling').on('show.bs.modal', function () {
            var now = new Date();

            // Formázzuk: YYYY-MM-DDTHH:mm
            var year = now.getFullYear();
            var month = (now.getMonth() + 1).toString().padStart(2, '0');
            var day = now.getDate().toString().padStart(2, '0');
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');

            var minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

            // Beállítjuk az input mező minimumát az aktuális pillanatra
            document.getElementById('start_time_input').setAttribute('min', minDateTime);
        });
    </script>
</div>
