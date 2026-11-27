@extends('layouts.adminlte')

@section('title', 'Sucursales')
@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Sucursales', 'subtitulo' => 'Configuración', 'icono' => 'bi-diagram-3',
        'crearUrl' => route('configuracion.sucursales.create'), 'crearTexto' => 'Nueva sucursal',
    ])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <p class="alert alert-success" role="status">{{ session('estado') }}</p>
            @endif
            @include('partials.listado-modal', ['iconoModal' => 'bi-diagram-3'])
            <section class="card clientes-directorio" data-ajax-list>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th scope="col">Código</th><th scope="col">Nombre</th><th scope="col">Dirección</th><th scope="col" class="text-end">Acciones</th></tr></thead>
                        <tbody>
                            @forelse ($sucursales as $sucursal)
                                <tr>
                                    <td>{{ $sucursal->codigo }}</td>
                                    <th scope="row">{{ $sucursal->nombre }}</th>
                                    <td>{{ $sucursal->direccion ?: 'Sin dirección' }}</td>
                                    <td class="text-end">
                                        @component('partials.acciones-fila', ['ver' => route('configuracion.sucursales.show', $sucursal->id), 'tituloVer' => 'Detalle de sucursal'])
                                            @include('partials.accion-fila-enlace', ['href' => route('configuracion.sucursales.edit', $sucursal->id), 'etiqueta' => 'Editar', 'titulo' => 'Editar sucursal'])
                                        @endcomponent
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="text-center py-5 text-body-secondary" colspan="4">Aún no hay sucursales registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
