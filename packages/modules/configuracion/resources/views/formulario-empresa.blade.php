<form class="card shadow-sm" method="POST" enctype="multipart/form-data" data-ajax-form data-usuario-form action="{{ route('configuracion.empresas.update', $seleccionada) }}">
        @csrf
    @method('PUT')
        <div class="card-header"><h2 class="card-title mb-0">Editar empresa</h2></div>
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
                    <label for="{{ $campo }}" class="form-label fw-semibold">{{ $etiqueta }}</label>
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
                    <label for="direccion" class="form-label fw-semibold">Dirección</label>
                    <input id="direccion" name="direccion" maxlength="255" autocomplete="street-address"
                        class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $seleccionada->direccion) }}">
                    @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </section>
            <section id="empresa-identidad" role="tabpanel" aria-labelledby="empresa-tab-identidad" class="ficha-bloque" data-usuario-panel="identidad">
                @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Identidad y certificado', 'iconoBloque' => 'bi-patch-check'])
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="empresa-logo" class="form-label fw-semibold">Logo de la empresa</label>
                        @if ($identidad?->logo_mime)
                            <img src="{{ route('configuracion.empresas.logo', $seleccionada) }}" alt="Logo actual de la empresa"
                                class="d-block mb-2 rounded object-fit-contain" width="120" height="72">
                        @endif
                        <input type="file" name="logo" id="empresa-logo" class="form-control" accept="image/png,image/jpeg,image/webp">
                        <div class="form-text">PNG, JPG o WebP, hasta 1 MB.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="empresa-firma" class="form-label fw-semibold">Certificado de firma electrónica</label>
                        <input type="file" name="firma" id="empresa-firma" class="form-control" accept=".p12,.pfx">
                        @if ($identidad?->firma_vence)
                            <div class="form-text">Vencimiento {{ \Carbon\CarbonImmutable::parse($identidad->firma_vence, 'UTC')->setTimezone('America/Guayaquil')->format('d/m/Y') }}</div>
                        @else
                            <div class="form-text">Archivo P12 o PFX, hasta 512 KB.</div>
                        @endif
                        <label for="empresa-firma-clave" class="form-label fw-semibold mt-2">Clave del certificado</label>
                        <input type="password" name="clave_firma" id="empresa-firma-clave" class="form-control" maxlength="255" autocomplete="new-password">
                    </div>
                </div>
                @foreach (['logo', 'firma', 'clave_firma'] as $archivo)
                    @error($archivo)<div class="small text-danger mt-2">{{ $message }}</div>@enderror
                @endforeach
            </section>
            <section id="empresa-operacion" role="tabpanel" aria-labelledby="empresa-tab-operacion" class="ficha-bloque" data-usuario-panel="operacion">
                @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Configuración operativa y fiscal', 'iconoBloque' => 'bi-sliders'])
                <div class="list-group">
                    @can('administrar-configuracion')
                        <a class="list-group-item list-group-item-action" href="{{ route('configuracion.sucursales.index') }}" data-workspace-link="configuracion.sucursales.index"><i class="bi bi-diagram-3 me-2" aria-hidden="true"></i>Sucursales</a>
                    @endcan
                    @can('permiso', ['contabilidad.periodos.index', 'leer'])
                        <a class="list-group-item list-group-item-action" href="{{ route('contabilidad.periodos.index') }}" data-workspace-link="contabilidad.periodos.index"><i class="bi bi-calendar-range me-2" aria-hidden="true"></i>Períodos contables</a>
                    @endcan
                    <a class="list-group-item list-group-item-action" href="{{ route('configuracion.empresas.parametros') }}" data-workspace-link="configuracion.empresas.parametros" data-workspace-label="Parámetros fiscales"><i class="bi bi-percent me-2" aria-hidden="true"></i>Parámetros fiscales y participación de trabajadores</a>
                </div>
            </section>
        </div>
        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            @include('partials.interruptor-activo', ['activo' => $seleccionada->activo, 'etiquetaEstado' => 'Empresa activa'])
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('configuracion.empresas.index') }}" data-ajax-close>Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1" aria-hidden="true"></i>Guardar cambios</button>
            </div>
        </div>
    </form>
