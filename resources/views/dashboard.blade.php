@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Üdvözlünk, {{ auth()->user()->name }}!</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock mr-1"></i> Közelgő ütemezett kiküldéseid</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Időpont</th>
                            <th>Tárgy</th>
                            <th style="width: 40px">db</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $upcoming = \App\Models\MailScheduling::where('user_id', auth()->id())
                                ->where('start_time', '>', now())
                                ->orderBy('start_time', 'asc')
                                ->take(5)
                                ->get();
                        @endphp
                        @forelse($upcoming as $mail)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($mail->start_time)->format('m.d. H:i') }}</td>
                                <td>{{ $mail->subject }}</td>
                                <td><span class="badge bg-primary">{{ $mail->mail_count }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Nincs közelgő kiküldésed.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ url('scheduling/calendar') }}" class="small-box-footer">Összes megtekintése naptárban <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <livewire:online-users />
        </div>

        <div class="col-md-5">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history mr-1"></i> Tevékenységnapló (Log)</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @php
                            $logs = \App\Models\ActivityLog::where('user_id', auth()->id())
                                ->orderBy('created_at', 'desc')
                                ->take(10)
                                ->get();
                        @endphp
                        @forelse($logs as $log)
                            <li class="list-group-item">
                                <small class="text-muted float-right">{{ $log->created_at->diffForHumans() }}</small>
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                <b>{{ $log->action }}:</b> {{ $log->description }}
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Még nincs rögzített tevékenységed.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
     </div>
@stop

@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
