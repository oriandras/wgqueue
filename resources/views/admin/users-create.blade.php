{{--
    Új felhasználó létrehozása nézet.
    Az adminisztrátorok ezen az oldalon rögzíthetnek új felhasználókat
    egy Livewire űrlapon keresztül.
--}}
@extends('adminlte::page')

@section('title', 'Új felhasználó felvétele')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Új felhasználó felvétele</h1>
        {{-- Visszalépés a felhasználók listájához --}}
        <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Vissza a listához
        </a>
    </div>
@stop

@section('content')
    {{-- Az új felhasználó adatait kezelő Livewire komponens --}}
    <livewire:user-create />
@stop
@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
