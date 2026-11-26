@extends('layouts.adminlte')

@section('title', 'Clientes')

@push('scripts')
    <script src="{{ asset('clientes-ficha.js') }}" defer></script>
@endpush

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Clientes', 'subtitulo' => 'Directorio de clientes', 'icono' => 'bi-people',
        'crearUrl' => route('clientes.create'), 'crearTexto' => 'Nuevo cliente',
    ])

    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal')
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            <div class="card clientes-directorio" data-ajax-list>
                @include('partials.listado-busqueda', ['listadoUrl' => route('clientes.index'), 'placeholder' => 'Nombre, identificación o correo'])
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
                                                data-ajax-open="Editar cliente" aria-label="Editar {{ $cliente->nombre }}"><i class="bi bi-pencil me-1" aria-hidden="true"></i> Editar</a>
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
@endsection
