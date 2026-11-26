@extends('layouts.adminlte')

@section('title', 'Clientes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('clientes.css') }}">
@endpush

@section('content')
    <div class="app-content-header clientes-cabecera">
        <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="clientes-icono" aria-hidden="true"><i class="bi bi-people"></i></span>
                <div>
                    <h1 class="mb-0 fs-3 fw-semibold">Clientes</h1>
                    <p class="mb-0 text-body-secondary">Directorio de clientes</p>
                </div>
            </div>
            <a href="{{ route('clientes.create') }}" class="btn btn-primary" data-cliente-dialog-open>
                <i class="bi bi-person-plus me-1" aria-hidden="true"></i> Nuevo cliente
            </a>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <p class="alert alert-success" role="status" data-cliente-estado hidden></p>
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            <div class="card clientes-directorio">
                <div class="card-header bg-body p-3">
                    <form method="GET" action="{{ route('clientes.index') }}" class="d-flex flex-wrap gap-2" role="search">
                        <label class="visually-hidden" for="q">Buscar cliente</label>
                        <div class="input-group clientes-busqueda">
                            <span class="input-group-text bg-body-tertiary"><i class="bi bi-search" aria-hidden="true"></i></span>
                            <input class="form-control" id="q" type="search" name="q" maxlength="160"
                                value="{{ request('q') }}" placeholder="Nombre, identificación o correo">
                            <button type="submit" class="btn btn-outline-primary">Buscar</button>
                        </div>
                        @if (request()->filled('q'))
                            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                        @endif
                    </form>
                    @error('q') <p class="text-danger mb-0 mt-2">{{ $message }}</p> @enderror
                </div>
                <div data-clientes-listado>
                    <div class="px-3 py-2 small text-body-secondary border-bottom">
                        {{ number_format($clientes->total(), 0, ',', '.') }}
                        {{ $clientes->total() === 1 ? 'cliente' : 'clientes' }}
                        {{ request()->filled('q') ? 'encontrados' : 'en el directorio' }}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <caption class="visually-hidden">Listado de clientes</caption>
                            <thead class="clientes-tabla-cabecera">
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
                                        <td>
                                            <small class="d-block text-body-secondary">{{ $cliente->identificacion->tipo->etiqueta() }}</small>
                                            <span class="text-nowrap clientes-identificacion">{{ $cliente->identificacion->numero }}</span>
                                        </td>
                                        <td>
                                            @if ($cliente->email) <div>{{ $cliente->email }}</div> @endif
                                            @if ($cliente->telefono) <div>{{ $cliente->telefono }}</div> @endif
                                            @if (!$cliente->email && !$cliente->telefono) <span class="text-body-secondary">—</span> @endif
                                        </td>
                                        <td><span @class(['badge rounded-pill', 'bg-success-subtle text-success-emphasis' => $cliente->activo, 'bg-secondary-subtle text-secondary-emphasis' => !$cliente->activo])>{{ $cliente->activo ? 'Activo' : 'Inactivo' }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-outline-secondary"
                                                aria-label="Editar {{ $cliente->nombre }}"><i class="bi bi-pencil me-1" aria-hidden="true"></i> Editar</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center clientes-vacio">
                                            <span class="clientes-icono mx-auto mb-3" aria-hidden="true"><i class="bi bi-person-lines-fill"></i></span>
                                            <p class="fw-semibold mb-1">{{ request()->filled('q') ? 'No hay coincidencias' : 'Todavía no hay clientes' }}</p>
                                            <p class="text-body-secondary mb-0">{{ request()->filled('q') ? 'Prueba con otro término de búsqueda.' : 'Usa Nuevo cliente para agregar el primer registro.' }}</p>
                                        </td>
                                    </tr>
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
    </div>

    <dialog class="cliente-dialog cliente-dialog-redisenado" aria-labelledby="cliente-dialog-titulo" data-cliente-dialog>
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            <div class="d-flex align-items-start justify-content-between gap-3 cliente-dialog-cabecera">
                <div class="d-flex align-items-center gap-3">
                    <span class="clientes-icono" aria-hidden="true"><i class="bi bi-person-plus"></i></span>
                    <div>
                        <h2 class="fs-5 fw-semibold mb-1" id="cliente-dialog-titulo">Nuevo cliente</h2>
                        <p class="small text-body-secondary mb-0">Completa los datos de identificación y contacto.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" aria-label="Cerrar" data-cliente-dialog-close></button>
            </div>
            <div class="cliente-dialog-cuerpo">
                <div class="alert alert-danger" role="alert" data-cliente-errores hidden></div>
                <p class="small text-body-secondary mb-3">* Campos obligatorios</p>
                <div class="row g-3">
                    @include('clientes::campos', ['cliente' => null])
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 cliente-dialog-pie">
                <button type="button" class="btn btn-outline-secondary" data-cliente-dialog-close>Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cliente</button>
            </div>
        </form>
    </dialog>
@endsection

@push('scripts')
    <script src="{{ asset('clientes-dialog.js') }}" defer></script>
@endpush
