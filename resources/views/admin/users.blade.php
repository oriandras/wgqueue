@extends('adminlte::page')

@section('title', 'Felhasználók kezelése')

@section('content_header')
    <h1>Felhasználók kezelése</h1>
@stop

@section('content')
    <livewire:user-management />
@stop
@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
