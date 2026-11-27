@extends('layouts.adminlte')

@section('title', 'Editar empresa')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Editar empresa', 'subtitulo' => 'Configuración', 'icono' => 'bi-buildings'])
    <div class="app-content">
        <div class="container-fluid">
            @include('configuracion::formulario-empresa')
        </div>
    </div>
@endsection