@extends('adminlte::page')

@section('title', 'Műszerfal')

@section('content_header')
    <h1>WGQueue Műszerfal</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @livewire('calendar-view')
        </div>
    </div>
@stop
