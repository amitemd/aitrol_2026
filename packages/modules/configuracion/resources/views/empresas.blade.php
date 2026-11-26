@extends('layouts.adminlte')

@section('title', 'Empresas')

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Empresas', 'subtitulo' => 'Configuraciones generales', 'icono' => 'bi-buildings',
        'crearUrl' => Gate::allows('permiso', ['empresas', 'escribir']) ? route('configuracion.empresas.index', ['crear' => 1]) : null,
        'crearTexto' => 'Nueva empresa',
    ])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <div class="alert alert-success" role="status">{{ session('estado') }}</div>
            @endif
            @include('partials.listado-modal')
            <div class="row g-3 align-items-start">
                <div class="col-12">
                    <section class="card clientes-directorio" data-ajax-list>
                        @include('partials.listado-busqueda', ['listadoUrl' => route('configuracion.empresas.index'), 'placeholder' => 'Razón social, nombre comercial o RUC'])
                        <div class="small text-body-secondary px-3 py-2 border-bottom">{{ $empresas->total() }} empresas registradas</div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="clientes-tabla-cabecera"><tr><th scope="col">Razón social</th><th scope="col">RUC</th><th scope="col">Contacto</th><th scope="col" class="text-end">Acciones</th></tr></thead>
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
                                                                                    <td class="text-end">
                                                @can('permiso', ['empresas', 'actualizar'])
                                                    <a href="{{ route('configuracion.empresas.edit', $empresa) }}" class="btn btn-sm btn-outline-primary" data-ajax-open="Editar empresa">Editar</a>
                                                @endcan
                                                @can('permiso', ['empresas', 'eliminar'])
                                                    <details class="d-inline-block">
                                                        <summary class="btn btn-sm btn-outline-danger">Eliminar</summary>
                                                        <form method="POST" data-ajax-action action="{{ route('configuracion.empresas.destroy', $empresa) }}">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-danger mt-2">Confirmar</button>
                                                        </form>
                                                    </details>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="p-4 text-center text-body-secondary">Todavía no hay empresas registradas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($empresas->hasPages())
                            <div class="card-footer">{{ $empresas->links('pagination::bootstrap-5') }}</div>
                        @endif
                    </section>
                </div>
                @if (request()->boolean('crear') || $errors->any())
                <div class="col-lg-8">
                    @include('configuracion::formulario-empresa', ['seleccionada' => null])
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
