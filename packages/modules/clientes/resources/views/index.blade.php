@extends('layouts.adminlte')

@section('title', 'Clientes')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h1 class="mb-0 fs-3">Clientes</h1>
            <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1" aria-hidden="true"></i> Nuevo cliente
            </a>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('clientes.index') }}" class="d-flex flex-wrap gap-2" role="search">
                        <label class="visually-hidden" for="q">Buscar cliente</label>
                        <input class="form-control w-auto flex-grow-1" id="q" type="search" name="q" maxlength="160"
                            value="{{ request('q') }}" placeholder="Nombre, identificación o correo">
                        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search me-1" aria-hidden="true"></i> Buscar</button>
                        @if (request()->filled('q'))
                            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                        @endif
                    </form>
                    @error('q') <p class="text-danger mb-0 mt-2">{{ $message }}</p> @enderror
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <caption class="visually-hidden">Listado de clientes</caption>
                        <thead>
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Identificación</th>
                                <th scope="col">Contacto</th>
                                <th scope="col">Estado</th>
                                <th scope="col"><span class="visually-hidden">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clientes as $cliente)
                                <tr>
                                    <th scope="row">{{ $cliente->nombre }}</th>
                                    <td>{{ $cliente->identificacion->tipo->etiqueta() }} {{ $cliente->identificacion->numero }}</td>
                                    <td>
                                        @if ($cliente->email) <div>{{ $cliente->email }}</div> @endif
                                        @if ($cliente->telefono) <div>{{ $cliente->telefono }}</div> @endif
                                        @if (!$cliente->email && !$cliente->telefono) <span class="text-body-secondary">—</span> @endif
                                    </td>
                                    <td><span @class(['badge', 'text-bg-success' => $cliente->activo, 'text-bg-secondary' => !$cliente->activo])>{{ $cliente->activo ? 'Activo' : 'Inactivo' }}</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-outline-secondary"
                                            aria-label="Editar {{ $cliente->nombre }}">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-4 text-center text-body-secondary">No hay clientes para mostrar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($clientes->hasPages())
                    <div class="card-footer">{{ $clientes->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
