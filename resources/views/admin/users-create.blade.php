@extends('adminlte::page')

@section('title', 'Új felhasználó felvétele')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Új felhasználó felvétele</h1>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Vissza a listához
        </a>
    </div>
@stop

@section('content')
    <livewire:user-create />
@stop
@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Oldalbetöltés:</b> {{ number_format(microtime(true) - LARAVEL_START, 3) }} mp
    </div>
    <strong>Webgalamb Queue &copy; 2026</strong>
@stop
