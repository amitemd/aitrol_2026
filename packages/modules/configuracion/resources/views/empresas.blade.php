@extends('layouts.adminlte')

@section('title', __('Empresas'))

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Empresas', 'subtitulo' => 'Configuración', 'icono' => 'bi-buildings',
    ])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <div class="alert alert-success" role="status">{{ session('estado') }}</div>
            @endif
            @include('partials.listado-modal', ['iconoModal' => 'bi-buildings'])
            <div class="row g-3 align-items-start">
                <div class="col-12">
                    <section class="card clientes-directorio" data-ajax-list>
                        @include('partials.listado-busqueda', ['listadoUrl' => route('configuracion.empresas.index'), 'placeholder' => 'Razón social, nombre comercial o RUC'])
                        <div class="small text-body-secondary px-3 py-2 border-bottom">{{ $empresas->total() }} empresas registradas</div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="clientes-tabla-cabecera"><tr><th scope="col">{{ __('Razón social') }}</th><th scope="col">RUC</th><th scope="col">Contacto</th><th scope="col">{{ __('Estado') }}</th><th scope="col" class="text-end">{{ __('Acciones') }}</th></tr></thead>
                                <tbody>
                                    @forelse ($empresas as $empresa)
                                        <tr>
                                            <th scope="row">
                                                {{ $empresa->razon_social }}
                                                @if ($empresa->nombre_comercial)
                                                    <small class="d-block fw-normal text-body-secondary">{{ $empresa->nombre_comercial }}</small>
                                                @endif
                                            </th>
                                            <td class="text-nowrap">{{ $empresa->ruc }}</td>
                                            <td>
                                                @if ($empresa->email) <div>{{ $empresa->email }}</div> @endif
                                                @if ($empresa->telefono) <div>{{ $empresa->telefono }}</div> @endif
                                                @if (!$empresa->email && !$empresa->telefono) <span class="text-body-secondary">Sin contacto</span> @endif
                                            </td>
                                            <td><span class="badge {{ $empresa->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $empresa->activo ? 'Activa' : 'Inactiva' }}</span></td>
                                            <td class="text-end">
                                                @component('partials.acciones-fila', ['ver' => route('configuracion.empresas.show', $empresa), 'tituloVer' => 'Detalle de empresa'])
                                                    @can('permiso', ['empresas', 'actualizar'])
                                                        @include('partials.accion-fila-enlace', ['href' => route('configuracion.empresas.edit', $empresa), 'etiqueta' => 'Editar', 'titulo' => 'Editar empresa'])
                                                    @endcan
                                                @endcomponent
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="p-4 text-center text-body-secondary">Todavía no hay empresas registradas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($empresas->hasPages())
                            <div class="card-footer">{{ $empresas->links('pagination::bootstrap-5') }}</div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
