@extends('adminlte::page')

@section('title', 'Ütemezés szerkesztése')

@section('content_header')
    <h1>Ütemezés módosítása</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            {{-- Meghívjuk a formot és átadjuk az ID-t --}}
            <livewire:scheduling-form :id="$id" />
        </div>
    </div>
@stop
