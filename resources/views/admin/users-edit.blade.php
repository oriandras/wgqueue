{{--
    Felhasználó szerkesztése admin felület.
    Az URL-ből kapott ID alapján betölti a Livewire komponenst a módosításhoz.
--}}
@extends('adminlte::page')

@section('title', 'Felhasználó szerkesztése')

@section('content_header')
    <h1>Felhasználó adatainak módosítása</h1>
@stop

@section('content')
    {{-- A felhasználó adatait kezelő Livewire komponens --}}
    {{-- TODO: Ellenőrizni, hogy az adott ID-jú felhasználó létezik-e, mielőtt a komponensnek átadjuk --}}
    <livewire:user-edit :id="request()->route('id')" />
@stop

@section('footer')
    {{-- Oldalbetöltési idő megjelenítése --}}
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
