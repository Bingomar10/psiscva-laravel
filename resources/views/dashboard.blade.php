@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Panel de Control</h1>
@stop

@section('content')
    <div class="row">
       
    </div>
    <p>Hola <strong>{{ Auth::user()->nombre_usuario }}</strong>, has iniciado sesión.</p>
@stop