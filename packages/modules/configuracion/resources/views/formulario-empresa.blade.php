<form class="card shadow-sm" method="POST" enctype="multipart/form-data" data-ajax-form data-usuario-form action="{{ route('configuracion.empresas.update', $seleccionada) }}">
        @csrf
    @method('PUT')
        <div class="card-header"><h2 class="card-title mb-0">{{ __('Editar empresa') }}</h2></div>
        <div class="card-body">
            @include('partials.pestanas-ficha', ['pestanas' => [
                'generales' => ['General', 'bi-buildings'], 'ubicacion' => ['Ubicación', 'bi-geo-alt'],
                'identidad' => ['Logo y firma', 'bi-patch-check'], 'operacion' => ['Configuración', 'bi-sliders'],
            ], 'contexto' => 'empresa'])
            <section id="empresa-generales" role="tabpanel" aria-labelledby="empresa-tab-generales" class="ficha-bloque" data-usuario-panel="generales">
            @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Identificación de la empresa', 'iconoBloque' => 'bi-buildings'])
            <div class="row g-3">
            @foreach ([
                'razon_social' => ['Razón social', 'text', 160, true],
                'ruc' => ['RUC', 'text', 13, true],
                'nombre_comercial' => ['Nombre comercial', 'text', 160, false],
                'email' => ['Correo electrónico', 'email', 254, false],
                'telefono' => ['Teléfono', 'tel', 30, false],
            ] as $campo => [$etiqueta, $tipo, $limite, $obligatorio])
                <div @class(['col-12', 'col-md-6' => in_array($campo, ['email', 'telefono'], true)])>
                    <label for="{{ $campo }}" class="form-label fw-semibold">{{ __($etiqueta) }}</label>
                    <input id="{{ $campo }}" name="{{ $campo }}" type="{{ $tipo }}" maxlength="{{ $limite }}"
                        class="form-control @error($campo) is-invalid @enderror" value="{{ old($campo, $seleccionada?->{$campo}) }}"
                        @required($obligatorio) @if ($campo === 'ruc') inputmode="numeric" pattern="[0-9]{13}" @endif>
                    @error($campo) <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endforeach
            </div>
            </section>
            <section id="empresa-ubicacion" role="tabpanel" aria-labelledby="empresa-tab-ubicacion" class="ficha-bloque" data-usuario-panel="ubicacion">
                @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Dirección registrada', 'iconoBloque' => 'bi-geo-alt'])
                <div>
                    <label for="direccion" class="form-label fw-semibold">{{ __('Dirección') }}</label>
                    <input id="direccion" name="direccion" maxlength="255" autocomplete="street-address"
                        class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $seleccionada->direccion) }}">
                    @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </section>
            <section id="empresa-identidad" role="tabpanel" aria-labelledby="empresa-tab-identidad" class="ficha-bloque" data-usuario-panel="identidad">
                @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Identidad y certificado', 'iconoBloque' => 'bi-patch-check'])
                @php
                    $tieneLogo = filled($identidad?->logo_mime);
                    $firmaVence = $identidad?->firma_vence
                        ? \Carbon\CarbonImmutable::parse($identidad->firma_vence, 'UTC')->setTimezone('America/Guayaquil')
                        : null;
                @endphp
                <div class="row g-3 align-items-start">
                    <div class="col-md-6 empresa-logo-adjunto">
                        @include('partials.campo-adjunto', [
                            'campo' => 'logo', 'nombre' => 'logo', 'titulo' => 'Logo de la empresa',
                            'iconoAdjunto' => 'bi-image', 'formatos' => 'image/png,image/jpeg,image/webp',
                            'ayuda' => 'PNG, JPG o WebP · máximo 1 MB',
                            'accion' => $tieneLogo ? 'Cambiar logo' : 'Seleccionar logo',
                            'vistaPrevia' => true,
                            'urlImagen' => $tieneLogo ? route('configuracion.empresas.logo', $seleccionada) : null,
                        ])
                    </div>
                    <div class="col-md-6">
                        @include('partials.campo-adjunto', [
                            'campo' => 'firma', 'nombre' => 'firma', 'titulo' => 'Certificado de firma electrónica',
                            'iconoAdjunto' => 'bi-file-earmark-lock', 'formatos' => '.p12,.pfx',
                            'ayuda' => 'P12 o PFX · máximo 512 KB',
                            'accion' => $firmaVence ? 'Reemplazar certificado' : 'Seleccionar certificado',
                        ])
                        <div class="ficha-adjunto mt-3 p-3">
                            @if ($firmaVence)
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                    <span @class([
                                        'badge rounded-pill',
                                        'text-bg-success' => ! $firmaVence->isPast(),
                                        'text-bg-danger' => $firmaVence->isPast(),
                                    ])>{{ $firmaVence->isPast() ? 'Certificado vencido' : 'Certificado vigente' }}</span>
                                    <small class="text-body-secondary">Vencimiento {{ $firmaVence->format('d/m/Y') }}</small>
                                </div>
                            @endif
                            <label for="empresa-firma-clave" class="form-label fw-semibold">{{ __('Clave del certificado') }}</label>
                            <input type="password" name="clave_firma" id="empresa-firma-clave"
                                class="form-control @error('clave_firma') is-invalid @enderror" maxlength="255" autocomplete="new-password"
                                aria-describedby="empresa-firma-clave-ayuda">
                            <div id="empresa-firma-clave-ayuda" class="form-text">Se requiere al seleccionar un certificado.</div>
                            @error('clave_firma') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </section>
            <section id="empresa-operacion" role="tabpanel" aria-labelledby="empresa-tab-operacion" class="ficha-bloque" data-usuario-panel="operacion">
                @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Configuración operativa y fiscal', 'iconoBloque' => 'bi-sliders'])
                <div class="list-group">
                    @can('administrar-configuracion')
                        <a class="list-group-item list-group-item-action" href="{{ route('configuracion.sucursales.index') }}" data-workspace-link="configuracion.sucursales.index"><i class="bi bi-diagram-3 me-2" aria-hidden="true"></i>{{ __('Sucursales') }}</a>
                    @endcan
                    @can('permiso', ['contabilidad.periodos.index', 'leer'])
                        <a class="list-group-item list-group-item-action" href="{{ route('contabilidad.periodos.index') }}" data-workspace-link="contabilidad.periodos.index"><i class="bi bi-calendar-range me-2" aria-hidden="true"></i>{{ __('Períodos contables') }}</a>
                    @endcan
                    <a class="list-group-item list-group-item-action" href="{{ route('configuracion.empresas.parametros') }}" data-workspace-link="configuracion.empresas.parametros" data-workspace-label="Parámetros fiscales"><i class="bi bi-percent me-2" aria-hidden="true"></i>Parámetros fiscales y participación de trabajadores</a>
                </div>
            </section>
        </div>
        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            @include('partials.interruptor-activo', ['activo' => $seleccionada->activo, 'etiquetaEstado' => 'Empresa activa'])
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('configuracion.empresas.index') }}" data-ajax-close>{{ __('Cancelar') }}</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1" aria-hidden="true"></i>{{ __('Guardar cambios') }}</button>
            </div>
        </div>
    </form>
