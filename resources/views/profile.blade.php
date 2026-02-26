{{--
    Profil beállítások nézet.
    Ez az oldal megjeleníti a felhasználói profil szerkesztéséhez szükséges felületet,
    amelyet egy Livewire (Volt) komponens szolgál ki.
--}}
@extends('adminlte::page')

@section('title', 'Profilom')

@section('content_header')
    <h1>Profil beállítások</h1>
@stop

@section('content')
    {{-- A profil adatokat kezelő Livewire komponens beágyazása --}}
    <livewire:profile-form />
@stop

@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
