@extends('adminlte::page')
@section('title', 'Webgalamb kiküldések > Új kiküldés ütemezése')

@section('content_header')
    <h1>Új kiküldés ütemezése</h1>
@stop
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-primary mt-4">
                <div class="card-header"><h3 class="card-title">Új kiküldés</h3></div>
                <div class="card-body">
                    <livewire:scheduling-form />
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
