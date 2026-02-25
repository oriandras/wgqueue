@extends('adminlte::page')

@section('title', 'Profilom')

@section('content_header')
    <h1>Profil beállítások</h1>
@stop

@section('content')
    {{-- Ide hívjuk be a Volt komponenst --}}
    <livewire:profile-form />
@stop
@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
