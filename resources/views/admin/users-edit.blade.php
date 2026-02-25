@extends('adminlte::page')

@section('title', 'Felhasználó szerkesztése')

@section('content_header')
    <h1>Felhasználó adatainak módosítása</h1>
@stop

@section('content')
    {{-- Átadjuk az URL-ből kapott ID-t a komponensnek --}}
    <livewire:user-edit :id="request()->route('id')" />
@stop
@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
