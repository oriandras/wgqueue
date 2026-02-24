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
