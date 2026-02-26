{{--
    Saját levélkiküldések listája.
    A lista megjelenítését és a műveleteket egy Livewire komponens végzi.
--}}
@extends('adminlte::page')
@section('title', 'Levélkiküldéseim')
@section('content_header')
    <h1>Levélkiküldéseim kezelése</h1>
@stop
@section('content')
    {{-- A felhasználóhoz tartozó kiküldések listáját megjelenítő Livewire komponens --}}
    <livewire:scheduling-list />
@stop
@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
