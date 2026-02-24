<?php
use function Livewire\Volt\{state, computed, action};
use App\Models\MailScheduling;

// Csak a bejelentkezett felhasználó kiküldéseit listázzuk
$mailings = computed(fn() => MailScheduling::where('user_id', auth()->id())
    ->orderBy('start_time', 'desc')
    ->get());

$delete = function ($id) {
    $item = MailScheduling::find($id);
    // Csak a jövőbeli kiküldés törölhető (biztonsági okból)
    if ($item && \Carbon\Carbon::parse($item->start_time)->isFuture()) {
        $item->delete();
    }
};
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Saját ütemezett levelek</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Kezdés</th>
                <th>Tárgy</th>
                <th>Mennyiség</th>
                <th>Becsült vég</th>
                <th>Állapot</th>
                <th style="width: 150px">Műveletek</th>
            </tr>
            </thead>
            <tbody>
            @foreach($this->mailings as $mailing)
                <tr>
                    <td>{{ $mailing->start_time }}</td>
                    <td>{{ $mailing->subject }}</td>
                    <td>{{ number_format($mailing->mail_count, 0, ',', ' ') }} db</td>
                    <td>{{ $mailing->calculated_end_time }}</td>
                    <td>
                        @if(\Carbon\Carbon::parse($mailing->start_time)->isPast())
                            <span class="badge badge-secondary">Lezárult</span>
                        @else
                            <span class="badge badge-success">Ütemezve</span>
                        @endif
                    </td>
                    <td>
                        @if(\Carbon\Carbon::parse($mailing->start_time)->isFuture())
                            {{-- Szerkesztés gomb (egyelőre csak placeholder) --}}
                            <button class="btn btn-info btn-xs"><i class="fas fa-edit"></i></button>

                            <button wire:click="delete({{ $mailing->id }})"
                                    wire:confirm="Biztosan törölni szeretnéd ezt az ütemezést?"
                                    class="btn btn-danger btn-xs">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
