@extends('adminlte::page')

@section('title', 'Rendszerbeállítások')

@section('content_header')
    <h1>Rendszerbeállítások</h1>
@stop

@section('content')
    {{-- Itt hívjuk meg a korábban megírt Volt komponenst --}}
    <livewire:system-settings />
@stop
