@extends('layouts.adminlte')

@section('title', 'Administrar menú')

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Administrar menú', 'subtitulo' => 'Configuraciones generales', 'icono' => 'bi-list-ul',
        'crearUrl' => route('configuracion.menu.index', ['crear' => 1]), 'crearTexto' => 'Nueva opción',
    ])

    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal')
            @if (session('estado'))
                <div class="alert alert-success" role="status" data-workspace-menu-changed>{{ session('estado') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $mensaje)
                            <li>{{ $mensaje }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-12">
                    <section class="card clientes-directorio" data-ajax-list>
                        @include('partials.listado-busqueda', ['listadoUrl' => route('configuracion.menu.index'), 'placeholder' => 'Nombre de la opción'])
                        <div class="small text-body-secondary px-3 py-2 border-bottom">{{ count($resultados) }} opciones registradas</div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="clientes-tabla-cabecera">
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Ubicación</th>
                                        <th scope="col">Orden</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col" class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($resultados as $item)
                                        <tr>
                                            <th scope="row">
                                                <i class="bi {{ $item->icono }} me-1" aria-hidden="true"></i>
                                                {{ $item->nombre }}
                                                @if ($item->soloAdministrador)
                                                    <small class="d-block fw-normal text-body-secondary">Solo administradores</small>
                                                @endif
                                            </th>
                                            <td>{{ $opciones[$item->padreId]->nombre ?? ($item->ruta === null ? 'Grupo principal' : 'Menú principal') }}</td>
                                            <td>{{ $item->orden }}</td>
                                            <td>
                                                <span @class(['badge', 'text-bg-success' => $item->activo, 'text-bg-secondary' => ! $item->activo])>
                                                    {{ $item->activo ? 'Activa' : 'Oculta' }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if ($item->protegido)
                                                    <span class="text-body-secondary">Protegida</span>
                                                @else
                                                    @component('partials.acciones-fila')
                                                        @include('partials.accion-fila-enlace', ['href' => route('configuracion.menu.index', ['editar' => $item->id]), 'etiqueta' => 'Editar', 'titulo' => 'Editar opción'])
                                                        @include('partials.accion-fila-eliminar', ['href' => route('configuracion.menu.destroy', $item->id), 'confirmar' => '¿Eliminar la opción '.$item->nombre.'?'])
                                                    @endcomponent
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="p-4 text-center text-body-secondary">No hay opciones que coincidan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
                @if ($opcion || request()->boolean('crear') || $errors->any())
                <div class="col-lg-8">
                    <section class="card h-100" aria-labelledby="formulario-menu">
                        <div class="card-header">
                            <h2 class="card-title" id="formulario-menu">{{ $opcion ? 'Editar opción' : 'Nueva opción' }}</h2>
                        </div>
                        <form method="POST" action="{{ $opcion ? route('configuracion.menu.update', $opcion->id) : route('configuracion.menu.store') }}" data-ajax-form>
                            @csrf
                            @if ($opcion)
                                @method('PUT')
                            @endif
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $opcion?->nombre) }}" required maxlength="80">
                                </div>
                                <div class="mb-3">
                                    <label for="ruta" class="form-label">Destino</label>
                                    <select class="form-select" id="ruta" name="ruta">
                                        <option value="">Sin destino · grupo de menús</option>
                                        @foreach ($destinos as $ruta => $etiqueta)
                                            <option value="{{ $ruta }}" @selected(old('ruta', $opcion?->ruta) === $ruta)>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="padre_id" class="form-label">Ubicación</label>
                                    <select class="form-select" id="padre_id" name="padre_id">
                                        <option value="">Menú principal</option>
                                        @foreach ($ubicaciones as $grupoId => $nombreCompleto)
                                            <option value="{{ $grupoId }}" @selected((string) old('padre_id', $opcion?->padreId) === (string) $grupoId)>{{ $nombreCompleto }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col">
                                        <label for="icono" class="form-label">Icono</label>
                                        <input class="form-control" id="icono" name="icono" value="{{ old('icono', $opcion?->icono ?? 'bi-circle') }}" required maxlength="50">
                                    </div>
                                    <div class="col">
                                        <label for="orden" class="form-label">Orden</label>
                                        <input class="form-control" id="orden" name="orden" type="number" value="{{ old('orden', $opcion?->orden ?? 50) }}" required min="0" max="65535">
                                    </div>
                                </div>
                                <div class="form-check mt-2">
                                    <input type="hidden" name="solo_administrador" value="0">
                                    <input class="form-check-input" type="checkbox" id="solo_administrador" name="solo_administrador" value="1" @checked(old('solo_administrador', $opcion?->soloAdministrador ?? false))>
                                    <label class="form-check-label" for="solo_administrador">Visible solo para administradores</label>
                                </div>
                            </div>
                            <div class="card-footer d-flex flex-wrap align-items-center gap-2 ficha-acciones">
                                <div class="me-auto">@include('partials.interruptor-activo', ['activo' => $opcion?->activo, 'etiquetaEstado' => 'Mostrar en la barra lateral'])</div>
                                <a class="btn btn-outline-secondary" href="{{ route('configuracion.menu.index') }}" data-ajax-close>Cancelar</a>
                                <button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1" aria-hidden="true"></i>{{ $opcion ? 'Guardar cambios' : 'Crear opción' }}</button>
                            </div>
                        </form>
                    </section>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
