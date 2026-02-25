<?php
use function Livewire\Volt\{state, action, mount};
use App\Models\MailScheduling;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

state(['schedulingId' => null, 'start_time' => '', 'mail_count' => '', 'subject' => '', 'group_name' => '']);

mount(function ($id = null) {
    if ($id) {
        $item = MailScheduling::where('user_id', auth()->id())->find($id);
        if ($item) {
            $this->schedulingId = $item->id;
            $this->start_time = Carbon::parse($item->start_time)->format('Y-m-d\TH:i');
            $this->mail_count = $item->mail_count;
            $this->subject = $item->subject;
            $this->group_name = $item->group_name;
        }
    }
});

$save = function () {
    // 1. Alap validáció
    $this->validate([
        'start_time' => 'required|date',
        'mail_count' => 'required|integer|min:1',
        'subject'    => 'required|min:3',
        'group_name' => 'required',
    ]);

    $startTime = Carbon::parse($this->start_time);

    // 2. BACKEND: Múltbéli időpont tiltása
    if ($startTime->isPast()) {
        $this->addError('start_time', 'A múltba nem ütemezhetsz kiküldést!');
        return;
    }

    // Beállítások betöltése a számításhoz
    $settings = DB::table('sys_settings')->pluck('value', 'key');
    $limitPerMinute = (int)($settings['mails_per_minute'] ?? 100);
    $limitPerHour = 1000;
    $workStart = (int)($settings['work_start'] ?? 8);
    $workEnd = (int)($settings['work_end'] ?? 17);

    // Várható végidő kiszámítása (az ütközésvizsgálathoz)
    $durationMinutes = ceil($this->mail_count / $limitPerMinute);
    $endTime = $startTime->copy()->addMinutes($durationMinutes);

    // 3. BACKEND: Munkaidő óránkénti limit ellenőrzése (1000 levél/óra)
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

    // 4. BACKEND: Ütközésvizsgálat (Egymásra lapolás tiltása)
    // Logika: (új_kezdet < meglévő_vég) ÉS (új_vég > meglévő_kezdet)
    $overlap = MailScheduling::where(function ($query) use ($startTime, $endTime) {
        $query->where('start_time', '<', $endTime)
            ->where('calculated_end_time', '>', $startTime);
    })->exists();

    if ($overlap) {
        $this->addError('start_time', 'Ez az időpont átfedésben van egy másik kiküldéssel!');
        return;
    }

    if ($this->schedulingId) {
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
        // 5. Mentés
        MailScheduling::create([
            'user_id' => auth()->id(),
            'start_time' => $this->start_time,
            'mail_count' => $this->mail_count,
            'subject' => $this->subject,
            'group_name' => $this->group_name,
        ]);
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Ütemezés',
            'description' => 'Új kiküldés rögzítve: ' . $this->subject,
        ]);

        // Értesítés küldése a SweetAlert-nek
        $this->dispatch('swal:success', message: 'Sikeres foglalás!');
        $this->dispatch('calendar-updated'); // Naptár frissítése

        $this->reset();
    }
};
?>

<div>
    <form wire:submit.prevent="save" class="p-1">
        <div class="form-group">
            <label class="font-weight-bold">Kezdési időpont</label>
            <input type="datetime-local"
                   class="form-control"
                   wire:model="start_time"
                   min="{{ now()->format('Y-m-d\TH:i') }}">
            @error('start_time') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="font-weight-bold">E-mailek száma</label>
            <input type="number" class="form-control" wire:model="mail_count" placeholder="Pl. 500">
            @error('mail_count') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="font-weight-bold">Tárgy / Kampány neve</label>
            <input type="text" class="form-control" wire:model="subject" placeholder="Hírlevél tárgya...">
            @error('subject') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="font-weight-bold">Csoport neve</label>
            <input type="text" class="form-control" wire:model="group_name" placeholder="Célcsoport...">
            @error('group_name') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-save mr-1"></i> {{ $schedulingId ? 'Módosítások mentése' : 'Kiküldés ütemezése' }}
        </button>
    </form>
</div>
