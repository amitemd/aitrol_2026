@extends('layouts.adminlte')

@section('title', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <a href="{{ route('configuracion.usuarios.index') }}" class="small text-decoration-none">{{ __('Usuarios') }}</a>
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
                            @include('partials.pestanas-ficha', [
                                'pestanas' => ['generales' => ['Datos generales', 'bi-person-vcard'], 'ubicacion' => ['Ubicación', 'bi-geo-alt']],
                                'contexto' => 'usuario',
                                'etiquetaPestanas' => 'Ficha del usuario',
                            ])
                            <section id="usuario-generales" role="tabpanel" aria-labelledby="usuario-tab-generales"
                                class="ficha-bloque" data-usuario-panel="generales">
                                @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Identificación y acceso', 'iconoBloque' => 'bi-person-vcard'])
                                <div class="ficha-perfil">
                                <div class="row g-3">
                                @include('partials.nombre-persona', [
                                    'componentesNombre' => $usuario->persona?->perfil() ?? [],
                                    'nombresAnteriores' => $usuario->persona?->nombre ?? $usuario->name,
                                    'apellidosAnteriores' => $usuario->persona?->apellido,
                                    'limiteNombre' => 255, 'etiquetaNombreCompleto' => 'Nombre completo anterior',
                                ])
                                @if ($usuario->persona?->identificacion === null)
                                    <div class="col-md-6">
                                        <label for="tipo_identificacion" class="form-label fw-semibold">Tipo de identificación</label>
                                        <select id="tipo_identificacion" name="tipo_identificacion" class="form-select">
                                            <option value="">{{ __('Seleccionar') }}</option>
                                            @foreach ($tipos as $tipo)
                                                <option value="{{ $tipo->value }}" @selected(old('tipo_identificacion') === $tipo->value)>{{ $tipo->etiqueta() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="identificacion" class="form-label fw-semibold">{{ __('Identificación') }}</label>
                                        @include('partials.identificacion-persona', [
                                            'formularioPersona' => 'usuarios', 'idPersonaCampo' => 'identificacion', 'valorPersona' => old('identificacion'),
                                        ])
                                        <div class="form-text">Si el documento ya existe, se vincula a esa persona.</div>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <span class="small text-body-secondary">Identificación vinculada</span>
                                        <p class="mb-0">{{ $usuario->persona->identificacion }}</p>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">{{ __('Correo electrónico') }}</label>
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
                                        <label for="{{ $campo }}" class="form-label fw-semibold">{{ __($etiqueta) }}</label>
                                        <input id="{{ $campo }}" name="{{ $campo }}" type="{{ $tipo }}" class="form-control"
                                            @if ($limite !== null) maxlength="{{ $limite }}" @endif value="{{ old($campo, $valor) }}">
                                    </div>
                                @endforeach
                                @unless ($usuario->exists)
                                    @foreach (['password' => 'Contraseña inicial', 'password_confirmation' => 'Confirmar contraseña'] as $campo => $etiqueta)
                                        <div class="col-md-6">
                                            <label for="{{ $campo }}" class="form-label">{{ __($etiqueta) }}</label>
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
                                        'ayuda' => 'JPG, PNG o WebP · máximo '.rtrim(rtrim(number_format($limiteFotoMb, 2, ',', ''), '0'), ',').' MB',
                                        'accion' => $usuario->foto ? 'Cambiar fotografía' : 'Seleccionar imagen',
                                        'vistaPrevia' => true, 'urlImagen' => $usuario->foto ? route('configuracion.usuarios.foto', $usuario) : null,
                                    ])
                                </aside>
                                </div>
                            </section>
                            <section id="usuario-ubicacion" role="tabpanel" aria-labelledby="usuario-tab-ubicacion"
                                class="ficha-bloque" data-usuario-panel="ubicacion">
                                @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Ubicación', 'iconoBloque' => 'bi-geo-alt'])
                                <div class="row g-3">
                                @include('partials.ubicacion-selects', ['idCiudad' => old('id_ciudad', $usuario->persona?->id_ciudad)])
                                @foreach ([
                                    'direccion' => ['Dirección', 255, 'col-12', 'street-address'],
                                    'urbanizacion' => ['Urbanización', 100, 'col-md-6', null],
                                    'etapa' => ['Etapa', 100, 'col-md-6', null],
                                    'manzana' => ['Manzana', 100, 'col-md-6', null],
                                    'villa' => ['Villa', 100, 'col-md-6', null],
                                ] as $campo => [$etiqueta, $limite, $clase, $autocomplete])
                                    <div class="{{ $clase }}">
                                        <label for="{{ $campo }}" class="form-label fw-semibold">{{ __($etiqueta) }}</label>
                                        <input id="{{ $campo }}" name="{{ $campo }}" class="form-control" maxlength="{{ $limite }}"
                                            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                                            value="{{ old($campo, $usuario->persona?->{$campo}) }}">
                                    </div>
                                @endforeach
                                <div class="col-12">
                                    <label for="referencia" class="form-label fw-semibold">Referencia</label>
                                    <textarea id="referencia" name="referencia" class="form-control" rows="2" maxlength="1000">{{ old('referencia', $usuario->persona?->referencia) }}</textarea>
                                </div>
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
                            <a href="{{ route('configuracion.usuarios.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cancelar') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Guardar cambios') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
