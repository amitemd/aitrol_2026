@extends('layouts.adminlte')

@section('title', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <a href="{{ route('configuracion.usuarios.index') }}" class="small text-decoration-none">Usuarios</a>
            <h1 class="fs-3 mb-0">{{ $usuario->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-9 col-lg-11">
                    <form class="card shadow-sm" method="POST" data-ajax-form data-usuario-form enctype="multipart/form-data"
                        action="{{ $usuario->exists ? route('configuracion.usuarios.update', $usuario) : route('configuracion.usuarios.store') }}">
                        @csrf
                        @if ($usuario->exists) @method('PUT') @endif
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="mb-3">
                                    <ul class="alert alert-danger ps-4 mb-0" role="alert">
                                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="usuario-tabs nav nav-pills ficha-tabs" role="tablist" aria-label="Ficha del usuario" hidden>
                                <button type="button" class="nav-link active" id="usuario-tab-generales" role="tab"
                                    aria-controls="usuario-generales" aria-selected="true" data-usuario-tab="generales">
                                    <i class="bi bi-person-vcard me-1" aria-hidden="true"></i> Datos generales
                                </button>
                                <button type="button" class="nav-link" id="usuario-tab-ubicacion" role="tab"
                                    aria-controls="usuario-ubicacion" aria-selected="false" tabindex="-1" data-usuario-tab="ubicacion">
                                    <i class="bi bi-geo-alt me-1" aria-hidden="true"></i> Ubicación
                                </button>
                            </div>
                            <section id="usuario-generales" role="tabpanel" aria-labelledby="usuario-tab-generales"
                                class="ficha-perfil" data-usuario-panel="generales">
                                <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombres" class="form-label fw-semibold">Nombres</label>
                                    <input id="nombres" name="nombres" class="form-control @error('nombres') is-invalid @enderror"
                                        value="{{ old('nombres', $usuario->persona?->nombre ?? $usuario->name) }}" maxlength="255" autocomplete="given-name" required>
                                    @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="apellidos" class="form-label fw-semibold">Apellidos</label>
                                    <input id="apellidos" name="apellidos" class="form-control"
                                        value="{{ old('apellidos', $usuario->persona?->apellido) }}" maxlength="100" autocomplete="family-name">
                                </div>
                                @if ($usuario->persona?->identificacion === null)
                                    <div class="col-md-6">
                                        <label for="tipo_identificacion" class="form-label fw-semibold">Tipo de identificación</label>
                                        <select id="tipo_identificacion" name="tipo_identificacion" class="form-select">
                                            <option value="">Seleccionar</option>
                                            @foreach ($tipos as $tipo)
                                                <option value="{{ $tipo->value }}" @selected(old('tipo_identificacion') === $tipo->value)>{{ $tipo->etiqueta() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="identificacion" class="form-label fw-semibold">Identificación</label>
                                        <input id="identificacion" name="identificacion" class="form-control" maxlength="30"
                                            value="{{ old('identificacion') }}">
                                        <div class="form-text">Si el documento ya existe, se vincula a esa persona.</div>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <span class="small text-body-secondary">Identificación vinculada</span>
                                        <p class="mb-0">{{ $usuario->persona->identificacion }}</p>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $usuario->email) }}" maxlength="255" autocomplete="off" aria-describedby="correo-ingreso" required>
                                    <div id="correo-ingreso" class="form-text">Este correo se usa para iniciar sesión y recuperar la contraseña.</div>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @foreach ([
                                    'email_contacto' => ['Correo de contacto', 'email', 255, $usuario->persona?->email],
                                    'telefono' => ['Teléfono', 'tel', 100, $usuario->persona?->telefono],
                                    'celular' => ['Celular', 'tel', 100, $usuario->persona?->celular],
                                    'fecha_nacimiento' => ['Fecha de nacimiento', 'date', null, $usuario->persona?->fecha_nacimiento?->toDateString()],
                                ] as $campo => [$etiqueta, $tipo, $limite, $valor])
                                    <div class="col-md-6">
                                        <label for="{{ $campo }}" class="form-label fw-semibold">{{ $etiqueta }}</label>
                                        <input id="{{ $campo }}" name="{{ $campo }}" type="{{ $tipo }}" class="form-control"
                                            @if ($limite !== null) maxlength="{{ $limite }}" @endif value="{{ old($campo, $valor) }}">
                                    </div>
                                @endforeach
                                @unless ($usuario->exists)
                                    @foreach (['password' => 'Contraseña inicial', 'password_confirmation' => 'Confirmar contraseña'] as $campo => $etiqueta)
                                        <div class="col-md-6">
                                            <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                                            <input id="{{ $campo }}" name="{{ $campo }}" type="password" class="form-control" autocomplete="new-password" minlength="12" maxlength="72" required>
                                        </div>
                                    @endforeach
                                @endunless
                                    <div class="col-12">
                                        <label for="observacion" class="form-label fw-semibold">Observación</label>
                                        <textarea id="observacion" name="observacion" class="form-control" rows="2" maxlength="3000">{{ old('observacion', $usuario->persona?->observacion) }}</textarea>
                                    </div>
                                </div>
                                <aside class="ficha-perfil-foto">
                                    @include('partials.campo-adjunto', [
                                        'campo' => 'foto', 'nombre' => 'foto', 'titulo' => 'Foto del usuario',
                                        'iconoAdjunto' => 'bi-person', 'formatos' => 'image/jpeg,image/png,image/webp',
                                        'ayuda' => 'JPG, PNG o WebP · máximo 3 MB',
                                        'accion' => $usuario->foto ? 'Cambiar fotografía' : 'Seleccionar imagen',
                                        'vistaPrevia' => true, 'urlImagen' => $usuario->foto ? route('configuracion.usuarios.foto', $usuario) : null,
                                        'limiteOptimizado' => $limiteFoto, 'maximoOriginal' => $maximoFoto,
                                    ])
                                </aside>
                            </section>
                            <section id="usuario-ubicacion" role="tabpanel" aria-labelledby="usuario-tab-ubicacion"
                                class="row g-3" data-usuario-panel="ubicacion">
                                @include('partials.ubicacion-selects', ['idCiudad' => old('id_ciudad', $usuario->persona?->id_ciudad)])
                                @foreach ([
                                    'direccion' => ['Dirección', 255, 'col-12', 'street-address'],
                                    'urbanizacion' => ['Urbanización', 100, 'col-md-6', null],
                                    'etapa' => ['Etapa', 100, 'col-md-6', null],
                                    'manzana' => ['Manzana', 100, 'col-md-6', null],
                                    'villa' => ['Villa', 100, 'col-md-6', null],
                                ] as $campo => [$etiqueta, $limite, $clase, $autocomplete])
                                    <div class="{{ $clase }}">
                                        <label for="{{ $campo }}" class="form-label fw-semibold">{{ $etiqueta }}</label>
                                        <input id="{{ $campo }}" name="{{ $campo }}" class="form-control" maxlength="{{ $limite }}"
                                            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                                            value="{{ old($campo, $usuario->persona?->{$campo}) }}">
                                    </div>
                                @endforeach
                                <div class="col-12">
                                    <label for="referencia" class="form-label fw-semibold">Referencia</label>
                                    <textarea id="referencia" name="referencia" class="form-control" rows="2" maxlength="1000">{{ old('referencia', $usuario->persona?->referencia) }}</textarea>
                                </div>
                            </section>
                        </div>
                        <div class="card-footer d-flex flex-wrap align-items-center gap-2 ficha-acciones">
                            <div class="me-auto">
                                @include('partials.interruptor-activo', [
                                    'activo' => $usuario->activo, 'etiquetaEstado' => 'Cuenta activa',
                                    'estadoBloqueado' => auth()->user()->is($usuario) || ($usuario->is_admin_global && ! auth()->user()->is_admin_global),
                                ])
                                @error('activo') <div class="small text-danger">{{ $message }}</div> @enderror
                            </div>
                            <a href="{{ route('configuracion.usuarios.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
