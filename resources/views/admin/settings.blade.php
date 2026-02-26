{{--
    Rendszerbeállítások nézet.
    Ez az oldal az alkalmazás globális beállításait tartalmazza,
    amelyeket egy Livewire (Volt) komponens szolgál ki.
--}}
@extends('adminlte::page')

@section('title', 'Rendszerbeállítások')

@section('content_header')
    <h1>Rendszerbeállítások</h1>
@stop

@section('content')
    {{-- A globális rendszerbeállításokat kezelő Livewire komponens --}}
    <livewire:system-settings />
@stop
@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
