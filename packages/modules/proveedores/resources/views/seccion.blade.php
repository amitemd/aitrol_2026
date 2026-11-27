@extends('layouts.adminlte')

@section('title', $titulo)

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <p class="text-body-secondary mb-1">Proveedores / {{ $grupo }}</p>
            <h1 class="fs-3 mb-0">{{ $titulo }}</h1>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <p class="mb-0 text-body-secondary">Esta sección aún no está conectada a una fuente de datos.</p>
                </div>
            </div>
        </div>
    </div>
@endsection