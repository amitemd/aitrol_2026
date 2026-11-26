@extends('layouts.adminlte')

@section('title', $cliente ? 'Editar cliente' : 'Nuevo cliente')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <h1 class="mb-0 fs-3">{{ $cliente ? 'Editar cliente' : 'Nuevo cliente' }}</h1>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <form method="POST" class="card" data-ajax-form
                action="{{ $cliente ? route('clientes.update', $cliente->id) : route('clientes.store') }}">
                @csrf
                @if ($cliente) @method('PUT') @endif
                <div class="card-body row g-3">
                    @include('clientes::campos')
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar cliente</button>
                </div>
            </form>
        </div>
    </div>
@endsection
