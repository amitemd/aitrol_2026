@extends('layouts.adminlte')

@section('title', 'Roles y permisos')

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Roles y permisos', 'subtitulo' => 'Configuraciones generales', 'icono' => 'bi-shield-lock',
        'crearUrl' => route('configuracion.roles.index', ['crear' => 1]), 'crearTexto' => 'Nuevo rol',
    ])
    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal')
            @if (session('estado')) <p class="alert alert-success" role="status">{{ session('estado') }}</p> @endif
            <section class="card clientes-directorio" data-ajax-list>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th scope="col">Rol</th><th class="text-end" scope="col">Acciones</th></tr></thead>
                        <tbody>
                            @forelse ($roles as $item)
                                <tr>
                                    <th scope="row">{{ $item->nombre }}</th>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('configuracion.roles.index', ['editar' => $item->id]) }}" data-ajax-open="Editar rol">Editar</a>
                                        <details class="d-inline-block">
                                            <summary class="btn btn-sm btn-outline-danger">Eliminar</summary>
                                            <form method="POST" data-ajax-action action="{{ route('configuracion.roles.destroy', $item->id) }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger mt-2">Confirmar</button>
                                            </form>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-body-secondary p-4">Todavía no hay roles registrados.</td></tr>
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
                        <a href="{{ route('configuracion.roles.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar rol</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection