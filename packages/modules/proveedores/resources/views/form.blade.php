@extends('layouts.adminlte')

@section('title', $proveedor ? 'Editar proveedor' : 'Nuevo proveedor')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <h1 class="mb-0 fs-3">{{ $proveedor ? 'Editar proveedor' : 'Nuevo proveedor' }}</h1>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <form method="POST" class="card ficha-cliente" data-ajax-form data-ficha-cliente enctype="multipart/form-data"
                action="{{ $proveedor ? route('proveedores.update', $proveedor->id) : route('proveedores.store') }}">
                @csrf
                @if ($proveedor) @method('PUT') @endif
                <div class="card-body">
                    @if ($errors->any())
                        <ul class="alert alert-danger ps-4" role="alert">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    @endif
                    @include('clientes::campos', ['cliente' => $proveedor, 'modulo' => 'proveedores'])
                </div>
                <div class="card-footer d-flex flex-wrap align-items-center gap-2 ficha-acciones">
                    <div class="me-auto">@include('partials.interruptor-activo', ['activo' => $proveedor?->activo])</div>
                    <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cancelar') }}</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1" aria-hidden="true"></i>Guardar proveedor</button>
                </div>
            </form>
        </div>
    </div>
@endsection
