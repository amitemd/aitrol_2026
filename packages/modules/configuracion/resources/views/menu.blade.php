@extends('layouts.adminlte')

@section('title', 'Administrar menú')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <p class="text-body-secondary mb-1">Configuraciones generales</p>
            <h1 class="fs-3 mb-0">Administrar menú</h1>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <div class="alert alert-success" role="status">{{ session('estado') }}</div>
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
                <div class="col-xl-8">
                    <section class="card h-100" aria-labelledby="opciones-menu">
                        <div class="card-header">
                            <h2 class="card-title" id="opciones-menu">Opciones registradas</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Ubicación</th>
                                        <th scope="col">Orden</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col" class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($opciones as $item)
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
                                                    <div class="d-inline-flex gap-2">
                                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('configuracion.menu.index', ['editar' => $item->id]) }}" aria-label="Editar {{ $item->nombre }}">Editar</a>
                                                        <details class="text-start">
                                                            <summary class="btn btn-sm btn-outline-danger">Eliminar</summary>
                                                            <form method="POST" action="{{ route('configuracion.menu.destroy', $item->id) }}" class="mt-2">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" aria-label="Confirmar eliminación de {{ $item->nombre }}">Confirmar</button>
                                                            </form>
                                                        </details>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
                <div class="col-xl-4">
                    <section class="card h-100" aria-labelledby="formulario-menu">
                        <div class="card-header">
                            <h2 class="card-title" id="formulario-menu">{{ $opcion ? 'Editar opción' : 'Nueva opción' }}</h2>
                        </div>
                        <form method="POST" action="{{ $opcion ? route('configuracion.menu.update', $opcion->id) : route('configuracion.menu.store') }}">
                            @csrf
                            @if ($opcion)
                                @method('PUT')
                            @endif
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $opcion?->nombre) }}" required maxlength="80">
                                </div>
                                @if ($opcion?->ruta !== null || $opcion === null)
                                    <div class="mb-3">
                                        <label for="ruta" class="form-label">Destino</label>
                                        <select class="form-select" id="ruta" name="ruta" @required($opcion !== null)>
                                            @if ($opcion === null)
                                                <option value="">Sin destino · crear un grupo</option>
                                            @endif
                                            @foreach ($destinos as $ruta => $etiqueta)
                                                <option value="{{ $ruta }}" @selected(old('ruta', $opcion?->ruta) === $ruta)>{{ $etiqueta }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="padre_id" class="form-label">Ubicación del enlace</label>
                                        <select class="form-select" id="padre_id" name="padre_id">
                                            <option value="">Menú principal</option>
                                            @foreach ($grupos as $grupo)
                                                <option value="{{ $grupo->id }}" @selected((string) old('padre_id', $opcion?->padreId) === (string) $grupo->id)>{{ $grupo->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
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
                                <div class="form-check">
                                    <input type="hidden" name="activo" value="0">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" @checked(old('activo', $opcion?->activo ?? true))>
                                    <label class="form-check-label" for="activo">Mostrar en la barra lateral</label>
                                </div>
                                <div class="form-check mt-2">
                                    <input type="hidden" name="solo_administrador" value="0">
                                    <input class="form-check-input" type="checkbox" id="solo_administrador" name="solo_administrador" value="1" @checked(old('solo_administrador', $opcion?->soloAdministrador ?? false))>
                                    <label class="form-check-label" for="solo_administrador">Visible solo para administradores</label>
                                </div>
                            </div>
                            <div class="card-footer d-flex gap-2">
                                <button class="btn btn-primary" type="submit">{{ $opcion ? 'Guardar cambios' : 'Crear opción' }}</button>
                                @if ($opcion)
                                    <a class="btn btn-outline-secondary" href="{{ route('configuracion.menu.index') }}">Cancelar</a>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
