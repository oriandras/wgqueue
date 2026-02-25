<?php

use function Livewire\Volt\{state, mount, on};
use App\Models\MailScheduling;
use App\Models\MaintenanceWindow;

state(['events' => []]);

// Segédfüggvény, ami nem függ a $this-től
$fetchData = function () {
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

mount(function () use ($fetchData) {
    // Csak itt, a mount-ban használjuk a $this-t
    $this->events = $fetchData();
});

on(['calendar-updated' => function () use ($fetchData) {
    // Itt is kimentjük az adatot, majd dispatch-eljük
    $newEvents = $fetchData();
    $this->events = $newEvents;
    $this->dispatch('refresh-calendar-js', events: $newEvents);
}]);

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
            <div id="calendar" wire:ignore style="min-height: 600px;"></div>
        </div>
    </div>

    <div class="modal fade" id="modal-scheduling" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Új kiküldés rögzítése</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <livewire:scheduling-form />
                </div>
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
            if (calendar) calendar.destroy();
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'hu',
                slotDuration: '00:20:00',
                slotMinTime: '00:00:00',
                slotMaxTime: '23:59:59',
                scrollTime: '08:00:00',
                events: @json($events),
                contentHeight: 600,
                scrollTime: '17:00:00'
            });
            calendar.render();
        }
        document.addEventListener('livewire:initialized', () => {
            renderCalendar();
            Livewire.on('refresh-calendar-js', (data) => {
                $('#modal-scheduling').modal('hide');
                calendar.removeAllEvents();
                calendar.addEventSource(data.events);
            });
        });
        document.addEventListener('livewire:navigated', renderCalendar);
    </script>
</div>
