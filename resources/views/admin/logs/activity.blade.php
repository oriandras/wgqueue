{{--
    Tevékenységnapló nézet.
    Ez az oldal a rendszerben történt felhasználói tevékenységeket
    jeleníti meg egy szűrhető listában.
--}}
@extends('adminlte::page')

@section('title', 'Tevékenységnapló')

@section('content_header')
    <h1>Tevékenységnapló</h1>
@stop

@section('content')
    {{-- A tevékenységnaplót megjelenítő Livewire komponens --}}
    <livewire:activity-log-list />
@stop
@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
