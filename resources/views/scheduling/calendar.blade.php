{{--
    Ütemezések naptár nézete.
    A teljes képernyős naptár a Livewire komponenssel renderelődik.
--}}
@extends('adminlte::page')

@section('title', 'Webgalamb kiküldések > Naptár nézet')

@section('content_header')
    <h1>Naptár nézet</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            {{-- Naptár megjelenítése Livewire komponenssel --}}
            {{-- TODO: Szűrés felhasználóra és státuszra a naptárban --}}
            @livewire('calendar-view')
        </div>
    </div>
@stop
@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
