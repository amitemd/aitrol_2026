@extends('layouts.adminlte')

@section('title', __('Proveedores'))

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Proveedores', 'subtitulo' => 'Directorio de proveedores', 'icono' => 'bi-truck',
        'crearUrl' => Gate::allows('permiso', ['proveedores.lista', 'escribir']) ? route('proveedores.create') : null,
        'crearTexto' => 'Nuevo proveedor',
    ])

    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal', ['iconoModal' => 'bi-truck'])
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            <div class="card clientes-directorio" data-ajax-list>
                @include('partials.listado-busqueda', ['listadoUrl' => route('proveedores.index'), 'placeholder' => 'Nombre, identificación o correo'])
                <div data-proveedores-listado>
                    <div class="px-3 py-2 small text-body-secondary border-bottom">
                        {{ number_format($proveedores->total(), 0, ',', '.') }}
                        {{ $proveedores->total() === 1 ? 'proveedor' : 'proveedores' }}
                        {{ request()->filled('q') ? 'encontrados' : 'en el directorio' }}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <caption class="visually-hidden">Listado de proveedores</caption>
                            <thead class="clientes-tabla-cabecera">
                                <tr>
                                    <th scope="col">{{ __('Nombre') }}</th>
                                    <th scope="col">{{ __('Identificación') }}</th>
                                    <th scope="col">Contacto</th>
                                    <th scope="col">{{ __('Estado') }}</th>
                                    <th scope="col"><span class="visually-hidden">{{ __('Acciones') }}</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($proveedores as $proveedor)
                                    <tr>
                                        <th scope="row">{{ $proveedor->nombre }}</th>
                                        <td>
                                            <small class="d-block text-body-secondary">{{ $proveedor->identificacion->tipo->etiqueta() }}</small>
                                            <span class="text-nowrap clientes-identificacion">{{ $proveedor->identificacion->numero }}</span>
                                        </td>
                                        <td>
                                            @if ($proveedor->email) <div>{{ $proveedor->email }}</div> @endif
                                            @if ($proveedor->telefono) <div>{{ $proveedor->telefono }}</div> @endif
                                            @if (!$proveedor->email && !$proveedor->telefono) <span class="text-body-secondary">—</span> @endif
                                        </td>
                                        <td><span @class(['badge rounded-pill', 'bg-success-subtle text-success-emphasis' => $proveedor->activo, 'bg-secondary-subtle text-secondary-emphasis' => !$proveedor->activo])>{{ $proveedor->activo ? 'Activo' : 'Inactivo' }}</span></td>
                                        <td class="text-end">
                                            @component('partials.acciones-fila', ['ver' => route('proveedores.show', $proveedor->id), 'tituloVer' => 'Detalle de proveedor'])
                                                @can('permiso', ['proveedores.lista', 'actualizar'])
                                                    @include('partials.accion-fila-enlace', ['href' => route('proveedores.edit', $proveedor->id), 'etiqueta' => 'Editar', 'titulo' => 'Editar proveedor'])
                                                @endcan
                                                @can('permiso', ['proveedores.lista', 'eliminar'])
                                                    @include('partials.accion-fila-eliminar', ['href' => route('proveedores.destroy', $proveedor->id), 'confirmar' => '¿Eliminar proveedor '.$proveedor->nombre.'?'])
                                                @endcan
                                            @endcomponent
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center clientes-vacio">
                                            <span class="clientes-icono mx-auto mb-3" aria-hidden="true"><i class="bi bi-truck"></i></span>
                                            <p class="fw-semibold mb-1">{{ request()->filled('q') ? 'No hay coincidencias' : 'Todavía no hay proveedores' }}</p>
                                            @if (request()->filled('q'))
                                                <p class="text-body-secondary mb-0">Prueba con otro término de búsqueda.</p>
                                            @elseif (Gate::allows('permiso', ['proveedores.lista', 'escribir']))
                                                <p class="text-body-secondary mb-0">Usa Nuevo proveedor para agregar el primer registro.</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($proveedores->hasPages())
                        <div class="card-footer">{{ $proveedores->links('pagination::bootstrap-5') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
