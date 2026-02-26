<?php
/**
 * Kiküldés ütemezése Livewire (Volt) komponens.
 * Ez a komponens felelős az új levélkiküldések rögzítéséért és a meglévők módosításáért.
 * Tartalmazza a komplex validációs logikát: munkaidő korlátok, ütközésvizsgálat
 * és karbantartási időszakok ellenőrzése.
 */
use function Livewire\Volt\{state, action, mount};
use App\Models\MailScheduling;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use App\Models\MaintenanceWindow;
use App\Mail\SystemNotification;
use Illuminate\Support\Facades\Mail;

// Komponens állapota
state([
    'schedulingId' => null,
    'start_time' => '',
    'mail_count' => '',
    'subject' => '',
    'group_name' => ''
]);

/**
 * Komponens inicializálása: meglévő ütemezés betöltése szerkesztés esetén.
 *
 * @param int|null $id Az ütemezés azonosítója
 */
mount(function ($id = null) {
    if ($id) {
        // Jogosultság ellenőrzése: admin mindent, felhasználó csak a sajátját láthatja
        $query = auth()->user()->is_admin
            ? MailScheduling::query()
            : MailScheduling::where('user_id', auth()->id());

        $item = $query->find($id);

        if ($item) {
            $this->schedulingId = $item->id;
            $this->start_time = Carbon::parse($item->start_time)->format('Y-m-d\TH:i');
            $this->mail_count = $item->mail_count;
            $this->subject = $item->subject;
            $this->group_name = $item->group_name;
        }
    }
});

/**
 * Ütemezés mentése vagy frissítése.
 * Tartalmazza a szigorú üzleti szabályok ellenőrzését.
 */
$save = function () {
    // 1. Alapvető mezők validálása
    $this->validate([
        'start_time' => 'required|date',
        'mail_count' => 'required|integer|min:1',
        'subject'    => 'required|min:3',
        'group_name' => 'required',
    ]);

    $startTime = Carbon::parse($this->start_time);

    // 2. Múltbéli időpont tiltása
    if ($startTime->isPast()) {
        $this->addError('start_time', 'A múltba nem ütemezhetsz kiküldést!');
        return;
    }

    // Rendszerbeállítások lekérése a számításokhoz
    $settings = DB::table('sys_settings')->pluck('value', 'key');
    $limitPerMinute = (int)($settings['mails_per_minute'] ?? 100);
    $limitPerHour = 1000; // Fix limit óránként
    $workStart = (int)($settings['work_start'] ?? 8);
    $workEnd = (int)($settings['work_end'] ?? 17);

    // Várható végidőpont kiszámítása (levélszám / percenkénti limit)
    $durationMinutes = ceil($this->mail_count / $limitPerMinute);
    $endTime = $startTime->copy()->addMinutes($durationMinutes);

    // 3. Munkaidő óránkénti limit ellenőrzése (pl. max 1000 levél/óra)
    if ($startTime->hour >= $workStart && $startTime->hour < $workEnd) {
        $alreadyScheduled = MailScheduling::whereBetween('start_time', [
            $startTime->copy()->startOfHour(),
            $startTime->copy()->endOfHour()
        ])->sum('mail_count');

        if (($alreadyScheduled + $this->mail_count) > $limitPerHour) {
            $this->addError('mail_count', "Munkaidőben óránként max $limitPerHour levél mehet ki! Jelenleg ebben az órában: $alreadyScheduled db van.");
            return;
        }
    }

    // 4. Zárolt időszak (Maintenance Window) ellenőrzése
    $maintenanceConflict = MaintenanceWindow::where(function ($query) use ($startTime, $endTime) {
        $query->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);
    })->first();

    if ($maintenanceConflict) {
        // Értesítés küldése az adminisztrátornak a tiltott kísérletről
        $serverEmail = \App\Models\SystemSetting::get('server_team_email');
        $maintainerEmail = \App\Models\SystemSetting::get('maintainer_email');
        $recipients = array_filter([$serverEmail, $maintainerEmail]);

        if (!empty($recipients)) {
            $warningMail = app()->make(SystemNotification::class, [
                'title' => 'RIASZTÁS: Tiltott ütemezési kísérlet',
                'message' => "Egy felhasználó (" . auth()->user()->name . ") megpróbált kiküldést ütemezni egy zárolt időszakra!\n\n" .
                    "Időszak neve: " . $maintenanceConflict->title . "\n" .
                    "Kampány tárgya: " . $this->subject . "\n" .
                    "Tervezett kezdés: " . $startTime->format('Y-m-d H:i'),
                'buttonUrl' => route('admin.logs.errors'),
                'buttonText' => 'Rendszernapló ellenőrzése'
            ]);

            Mail::to($recipients)->send($warningMail);
        }

        $this->addError('start_time', "Sajnos ez az időszak rendszerkarbantartás miatt foglalt.");
        return;
    }

    // 5. Ütközésvizsgálat más kiküldésekkel (időbeli átfedés tiltása)
    $overlap = MailScheduling::where(function ($query) use ($startTime, $endTime) {
        $query->where('start_time', '<', $endTime)
            ->where('calculated_end_time', '>', $startTime);
    })->when($this->schedulingId, fn($q) => $q->where('id', '!=', $this->schedulingId))
    ->exists();

    if ($overlap) {
        $this->addError('start_time', 'Ez az időpont átfedésben van egy másik kiküldéssel!');
        return;
    }

    // 6. Mentés vagy frissítés végrehajtása
    if ($this->schedulingId) {
        // Frissítés
        $item = MailScheduling::find($this->schedulingId);
        $item->update([
            'start_time' => $this->start_time,
            'mail_count' => $this->mail_count,
            'subject' => $this->subject,
            'group_name' => $this->group_name,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Módosítás',
            'description' => "Módosítva: {$this->subject}",
        ]);

        session()->flash('success', 'Sikeresen frissítve!');
        return redirect()->route('scheduling.list');
    } else {
        // Új rögzítése
        MailScheduling::create([
            'user_id' => auth()->id(),
            'start_time' => $this->start_time,
            'mail_count' => $this->mail_count,
            'subject' => $this->subject,
            'group_name' => $this->group_name,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Ütemezés',
            'description' => 'Új kiküldés rögzítve: ' . $this->subject,
        ]);

        // Visszajelzés és események kiváltása
        $this->dispatch('swal:success', message: 'Sikeres foglalás!');
        $this->dispatch('calendar-updated'); // Naptár komponens frissítése
        $this->reset();
    }
};
?>

<div>
    {{-- Ütemezési űrlap --}}
    <form wire:submit.prevent="save" class="p-1">
        {{-- Kezdési időpont választó --}}
        <div class="form-group">
            <label class="font-weight-bold">Kezdési időpont</label>
            <input type="datetime-local"
                   class="form-control"
                   wire:model="start_time"
                   min="{{ now()->format('Y-m-d\TH:i') }}">
            @error('start_time') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        {{-- Levélszám megadása --}}
        <div class="form-group">
            <label class="font-weight-bold">E-mailek száma</label>
            <input type="number" class="form-control" wire:model="mail_count" placeholder="Pl. 500">
            @error('mail_count') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        {{-- Tárgy / Kampány neve --}}
        <div class="form-group">
            <label class="font-weight-bold">Tárgy / Kampány neve</label>
            <input type="text" class="form-control" wire:model="subject" placeholder="Hírlevél tárgya...">
            @error('subject') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        {{-- Csoport neve mező --}}
        <div class="form-group">
            <label class="font-weight-bold">Csoport neve</label>
            <input type="text" class="form-control" wire:model="group_name" placeholder="Célcsoport...">
            @error('group_name') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        {{-- Mentés gomb dinamikus felirattal --}}
        <button type="submit" class="btn btn-primary btn-block shadow-sm">
            <i class="fas fa-save mr-1"></i> {{ $schedulingId ? 'Módosítások mentése' : 'Kiküldés ütemezése' }}
        </button>
    </form>
</div>
