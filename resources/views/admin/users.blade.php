{{--
    Felhasználók kezelése (admin) nézet.
    Ez az oldal listázza a rendszer összes felhasználóját
    és lehetőséget ad a kezelésükre (Livewire).
--}}
@extends('adminlte::page')

@section('title', 'Felhasználók kezelése')

@section('content_header')
    <h1>Felhasználók kezelése</h1>
@stop

@section('content')
    {{-- A felhasználói listát és műveleteket kezelő Livewire komponens --}}
    <livewire:user-management />
@stop
@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
