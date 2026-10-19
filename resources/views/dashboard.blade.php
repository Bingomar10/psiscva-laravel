@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Panel de Control</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <x-adminlte-info-box title="Ventas" text="150" icon="bi bi-bag" theme="info"/>
        </div>
        <div class="col-lg-3 col-6">
            <x-adminlte-info-box title="Productos" text="53" icon="bi bi-box" theme="success"/>
        </div>
    </div>
    <p>Hola <strong>{{ Auth::user()->nombre_usuario }}</strong>, has iniciado sesión.</p>
@stop