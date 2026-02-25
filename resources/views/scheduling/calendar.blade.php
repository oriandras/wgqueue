@extends('adminlte::page')

@section('title', 'Webgalamb kiküldések > Naptár nézet')

@section('content_header')
    <h1>Naptár nézet</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @livewire('calendar-view')
        </div>
    </div>
@stop
@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
