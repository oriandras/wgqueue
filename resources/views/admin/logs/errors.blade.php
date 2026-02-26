{{--
    Hibanapló nézet.
    Ez az oldal a rendszerben naplózott hibákat jeleníti meg,
    segítve a fejlesztést és a hibakeresést.
--}}
@extends('adminlte::page')

@section('title', 'Hibanapló')

@section('content_header')
    <h1>Rendszerhibák naplója</h1>
@stop

@section('content')
    {{-- A hibanaplót (sys_errors) megjelenítő Livewire komponens --}}
    <livewire:error-log-list />
@stop
@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
