{{--
    Adminisztrátori lista nézet: az összes ütemezett kiküldés megjelenítése és kezelése.
--}}
@extends('adminlte::page')
@section('title', 'Minden kiküldés')
@section('content_header')
    <h1>Minden kiküldés kezelése</h1>
@stop
@section('content')
    {{-- Minden felhasználó kiküldéseit listázó Livewire komponens --}}
    {{-- TODO: Jogosultság-ellenőrzés megjelenítés szinten is (pl. @can('admin')) --}}
    <livewire:admin-scheduling-list />
@stop
@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
