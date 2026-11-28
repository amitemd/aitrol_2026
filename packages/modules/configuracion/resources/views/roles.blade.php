@extends('layouts.adminlte')

@section('title', __('Roles y permisos'))

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Roles y permisos', 'subtitulo' => 'Configuración', 'icono' => 'bi-shield-lock',
        'crearUrl' => route('configuracion.roles.index', ['crear' => 1]), 'crearTexto' => 'Nuevo rol',
    ])
    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal')
            @if (session('estado')) <p class="alert alert-success" role="status">{{ session('estado') }}</p> @endif
            <section class="card clientes-directorio" data-ajax-list>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="clientes-tabla-cabecera">
                            <tr>
                                <th scope="col">Rol</th>
                                @include('partials.auditoria-cabeceras')
                                <th class="text-end" scope="col">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $item)
                                <tr>
                                    <th scope="row">{{ $item->nombre }}</th>
                                    @include('partials.auditoria-celdas', ['filaAuditoria' => $item])
                                    <td class="text-end">
                                        @component('partials.acciones-fila', ['editar' => route('configuracion.roles.index', ['editar' => $item->id]), 'tituloEditar' => 'Editar rol'])
                                            @include('partials.accion-fila-eliminar', ['href' => route('configuracion.roles.destroy', $item->id), 'confirmar' => '¿Eliminar el rol '.$item->nombre.'?'])
                                        @endcomponent
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center clientes-vacio">
                                        <span class="clientes-icono mx-auto mb-3" aria-hidden="true"><i class="bi bi-shield-lock"></i></span>
                                        <p class="fw-semibold mb-1">Todavía no hay roles registrados</p>
                                        <p class="text-body-secondary mb-0">Usa Nuevo rol para agrupar los permisos que asignarás a tus usuarios.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            @if ($rol || request()->boolean('crear') || $errors->any())
                <form method="POST" class="card form-accesos" data-ajax-form
                    action="{{ $rol ? route('configuracion.roles.update', $rol->id) : route('configuracion.roles.store') }}">
                    @csrf
                    @if ($rol) @method('PUT') @endif
                    <div class="card-body">
                        <label class="form-label" for="rol-nombre">Nombre del rol</label>
                        <input class="form-control mb-3" name="nombre" id="rol-nombre" required maxlength="100" value="{{ old('nombre', $rol?->nombre) }}">
                        @error('nombre') <p class="text-danger small">{{ $message }}</p> @enderror
                        @include('configuracion::matriz-permisos')
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2">
                        <a href="{{ route('configuracion.roles.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cancelar') }}</a>
                        <button type="submit" class="btn btn-primary">Guardar rol</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
