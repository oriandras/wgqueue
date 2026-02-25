@extends('adminlte::page')

@section('title', 'Tevékenységnapló')

@section('content_header')
    <h1>Tevékenységnapló</h1>
@stop

@section('content')
    <livewire:activity-log-list />
@stop
@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
