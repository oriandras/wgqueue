<?php

use function Livewire\Volt\{state, mount};
use App\Models\MailScheduling;
use App\Models\MaintenanceWindow;

state(['events' => []]);

mount(function () {
    $schedulings = MailScheduling::with('user')->get()->map(function ($item) {
        return [
            'title' => ($item->user->name ?? 'Ismeretlen') . ': ' . $item->subject,
            'start' => $item->start_time,
            'end'   => $item->calculated_end_time,
            'color' => '#17a2b8',
        ];
    });

    $maintenances = MaintenanceWindow::all()->map(function ($item) {
        return [
            'title' => '⚠️ ' . $item->title,
            'start' => $item->start_time,
            'end'   => $item->end_time,
            'color' => '#dc3545',
        ];
    });

    $this->events = $schedulings->concat($maintenances)->toArray();
});

?>

<div>
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar-alt mr-1"></i> Kiküldési Naptár</h3>
        </div>
        <div class="card-body">
            <div id='calendar' wire:ignore></div>
        </div>
    </div>

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/hu.global.min.js'></script>

    <script>
        document.addEventListener('livewire:initialized', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'hu',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotMinTime: '07:00:00',
                slotMaxTime: '19:00:00',
                firstDay: 1,
                events: @json($events),
                height: 'auto'
            });
            calendar.render();
        });
    </script>
</div>
