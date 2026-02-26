{{--
    Ütemezés szerkesztése nézet.
    A Livewire űrlap a meglévő ütemezési rekord adatait tölti be és menti vissza.
--}}
@extends('adminlte::page')

@section('title', 'Ütemezés szerkesztése')

@section('content_header')
    <h1>Ütemezés módosítása</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            {{-- A módosítandó ütemezés azonosítójának átadása a Livewire űrlapnak --}}
            {{-- TODO: Validáljuk az $id meglétét és típusát a kontrollerben, mielőtt a nézetbe kerül --}}
            <livewire:scheduling-form :id="$id" />
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
