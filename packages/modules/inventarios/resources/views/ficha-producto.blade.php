@php
    $pestanas = [
        'generales' => ['General', 'bi-box-seam'],
        'contabilidad' => ['Cuentas contables', 'bi-journal-bookmark'],
        'precios' => ['Precios y costos', 'bi-tags'],
        'imagenes' => ['Imágenes', 'bi-images'],
        'documentos' => ['Documentación', 'bi-folder2-open'],
    ];
    $cuentasPorId = $ficha['cuentas']->keyBy('id');
    $marca = old('id_marca', $registro['id_marca'] ?? null);
    $unidad = old('id_unidad', $registro['id_unidad'] ?? ($registro ? null : $ficha['unidades']->firstWhere('codigo', 'UND')?->id));
    $sinInventario = old('tipo_producto', $registro['tipo_producto'] ?? 'producto') !== 'producto';
    $descripcionPlantillas = $ficha['plantillas']->map(fn ($plantilla) => [
        'nombre' => $plantilla->nombre,
        'cuentas' => collect(json_decode($plantilla->cuentas, true))->map(fn ($id) => $cuentasPorId->has($id)
            ? $cuentasPorId[$id]->codigo.' · '.$cuentasPorId[$id]->nombre : 'Cuenta #'.$id),
    ]);
@endphp
<div class="card-body">
    @include('partials.pestanas-ficha', ['pestanas' => $pestanas, 'contexto' => 'producto'])

    <section id="producto-generales" role="tabpanel" aria-labelledby="producto-tab-generales" data-usuario-panel="generales" class="ficha-producto-panel">
        <div class="ficha-bloque">
            @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Identificación', 'iconoBloque' => 'bi-upc-scan'])
            <div class="row g-3">
                @foreach (['codigo' => ['Código', 60, 'col-sm-4'], 'nombre' => ['Nombre', 160, 'col-sm-8'],
                    'codigo_barras' => ['Código de barras', 80, 'col-sm-4'], 'id_marca' => ['Marca', 100, 'col-sm-4'], 'modelo' => ['Modelo', 100, 'col-sm-4']] as $campo => [$etiqueta, $maximo, $ancho])
                    <div class="{{ $ancho }}">
                        <label class="form-label" for="producto-{{ $campo }}">{{ __($etiqueta) }}</label>
                        @if ($campo === 'codigo')
                            <input id="producto-codigo" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
                                maxlength="{{ $maximo }}" value="{{ $registro ? $registro['codigo'] : old('codigo', $codigoSugerido) }}"
                                aria-describedby="producto-codigo-ayuda" autocomplete="off" @readonly($registro) @required(! $registro)>
                            <p id="producto-codigo-ayuda" class="form-text mb-0">{{ $registro ? 'Código fijo del producto.' : 'Puedes editar el código sugerido antes de guardar.' }}</p>
                        @elseif ($campo === 'id_marca')
                            <select id="producto-id_marca" name="id_marca" class="form-select">
                                <option value="">Sin marca</option>
                                @foreach ($ficha['marcas'] as $opcion)
                                    @continue(!$opcion->activo && (int) ($registro['id_marca'] ?? 0) !== (int) $opcion->id)
                                    <option value="{{ $opcion->id }}" @selected((int) $marca === (int) $opcion->id)>
                                        {{ $opcion->nombre }}{{ $opcion->activo ? '' : ' (inactiva)' }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input id="producto-{{ $campo }}" name="{{ $campo }}" class="form-control @error($campo) is-invalid @enderror"
                                maxlength="{{ $maximo }}" value="{{ old($campo, $registro[$campo] ?? '') }}" @required($campo === 'nombre')>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="ficha-bloque">
            @include('partials.ficha-bloque-titulo', ['tituloBloque' => 'Trazabilidad y referencias externas', 'iconoBloque' => 'bi-qr-code'])
            <div class="row g-3">
                @foreach (config('inventarios.trazabilidad_producto') as $campo => [$etiqueta, $longitud])
                    <div class="{{ $campo === 'codigo_qr' ? 'col-12' : 'col-sm-6' }}">
                        <label class="form-label" for="producto-{{ $campo }}">{{ $etiqueta }}</label>
                        @if ($campo === 'codigo_qr')
                            <textarea id="producto-{{ $campo }}" name="{{ $campo }}" rows="2" maxlength="{{ $longitud }}"
                                class="form-control @error($campo) is-invalid @enderror"
                                aria-describedby="producto-qr-ayuda">{{ old($campo, $registro[$campo] ?? '') }}</textarea>
                            <p id="producto-qr-ayuda" class="form-text mb-0">Pega el contenido leído al escanear el QR.</p>
                        @else
                            <input id="producto-{{ $campo }}" name="{{ $campo }}" class="form-control @error($campo) is-invalid @enderror"
                                type="{{ $longitud === null ? 'date' : 'text' }}" value="{{ old($campo, $registro[$campo] ?? '') }}"
                                @if ($longitud !== null) maxlength="{{ $longitud }}" @else min="1900-01-01" aria-describedby="producto-vencimiento-ayuda" @endif>
                            @if ($longitud === null)
                                <p id="producto-vencimiento-ayuda" class="form-text mb-0">Fecha de referencia del catálogo. Cada lote puede tener su propio vencimiento.</p>
                            @endif
                        @endif
                        @error($campo) <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endforeach
            </div>
        </div>
        <div class="ficha-bloque">
            <h2 class="h6"><i class="bi bi-diagram-3 me-2" aria-hidden="true"></i>Clasificación e inventario</h2>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label" for="producto-tipo">{{ __('Tipo') }}</label>
                    <select id="producto-tipo" name="tipo_producto" class="form-select" data-select2-nativo data-producto-tipo>
                        @foreach (config('inventarios.tipos_producto') as $valor => $nombre)
                            <option value="{{ $valor }}" @selected(old('tipo_producto', $registro['tipo_producto'] ?? 'producto') === $valor)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="producto-categoria">{{ __('Categoría') }}</label>
                    <select id="producto-categoria" name="id_categoria" class="form-select @error('id_categoria') is-invalid @enderror" data-producto-categoria>
                        <option value="">Sin categoría{{ !empty($registro['categoria']) && empty($registro['id_categoria']) ? ' · '.$registro['categoria'] : '' }}</option>
                        @foreach ($ficha['categorias'] as $categoria)
                            @continue(!$categoria->activo && (int) ($registro['id_categoria'] ?? 0) !== (int) $categoria->id)
                            <option value="{{ $categoria->id }}" data-plantilla="{{ $categoria->id_plantilla_contable }}"
                                @selected((int) old('id_categoria', $registro['id_categoria'] ?? 0) === (int) $categoria->id)>{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="form-text mb-0">La categoría define la plantilla contable cuando tiene una asignada.</p>
                </div>
                @if (!empty($registro['categoria']) && empty($registro['id_categoria']))
                    <input type="hidden" name="categoria" value="{{ $registro['categoria'] }}">
                @endif
                <div class="col-sm-6">
                    <label class="form-label" for="producto-unidad">Unidad de medida</label>
                    <select id="producto-unidad" name="id_unidad" class="form-select" required>
                        <option value="">Elige una unidad de medida</option>
                        @foreach ($ficha['unidades'] as $opcion)
                            @continue(!$opcion->activo && (int) ($registro['id_unidad'] ?? 0) !== (int) $opcion->id)
                            <option value="{{ $opcion->id }}" @selected((int) $unidad === (int) $opcion->id)>
                                {{ $opcion->nombre }}{{ $opcion->activo ? '' : ' (inactiva)' }}
                            </option>
                        @endforeach
                    </select>
                    @if ($registro && empty($registro['id_unidad']))
                        <p class="form-text mb-0">Unidad registrada: {{ $registro['unidad'] }}. Selecciona su equivalente del catálogo.</p>
                    @endif
                </div>
                <div class="col-sm-6" data-producto-stock @if($sinInventario) hidden @endif>
                    <label class="form-label" for="producto-minimo">Stock mínimo por bodega</label>
                    <input id="producto-minimo" name="stock_minimo" class="form-control" type="number" min="0" max="1000000"
                        step="0.001" value="{{ old('stock_minimo', $registro['stock_minimo'] ?? '0') }}" @disabled($sinInventario)>
                </div>
            </div>
        </div>
    </section>

    <section id="producto-contabilidad" role="tabpanel" aria-labelledby="producto-tab-contabilidad" data-usuario-panel="contabilidad" class="ficha-producto-panel">
        <div class="ficha-bloque" data-producto-herencia data-plantillas="{{ $descripcionPlantillas->toJson(JSON_UNESCAPED_UNICODE) }}" hidden>
            <h2 class="h6"><i class="bi bi-journal-check me-2" aria-hidden="true"></i><span data-producto-plantilla></span></h2>
            <p class="text-body-secondary small">Estas cuentas se heredan de la categoría.</p>
            <dl class="row mb-0">
                @foreach (config('inventarios.cuentas_producto') as $rol => [$etiqueta])
                    <dt class="col-sm-5">{{ __($etiqueta) }}</dt><dd class="col-sm-7" data-producto-cuenta="{{ $rol }}"></dd>
                @endforeach
            </dl>
        </div>
        <div class="ficha-bloque" data-producto-manual>
            <h2 class="h6"><i class="bi bi-sliders me-2" aria-hidden="true"></i>Asignación manual</h2>
            <p class="text-body-secondary small">Disponible cuando la categoría carece de plantilla.</p>
            <div class="row g-3">
                @include('inventarios::campos-cuentas')
            </div>
        </div>
    </section>

    <section id="producto-precios" role="tabpanel" aria-labelledby="producto-tab-precios" data-usuario-panel="precios" class="ficha-producto-panel">
        <div class="ficha-bloque">
            <h2 class="h6"><i class="bi bi-tags me-2" aria-hidden="true"></i>Precios y costos unitarios</h2>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label" for="producto-iva">Tratamiento de IVA</label>
                    <select id="producto-iva" name="iva_codigo" class="form-select" data-select2-nativo>
                        <option value="">Pendiente de configurar</option>
                        @foreach (array_keys(\App\Services\FacturaVenta::IVA) as $codigo)
                            <option value="{{ $codigo }}" @selected((string) old('iva_codigo', $registro['iva_codigo'] ?? '') === (string) $codigo)>
                                {{ \App\Services\FacturaVenta::etiquetaIva($codigo) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="form-text mb-0">La factura utiliza este tratamiento al seleccionar el producto o servicio.</p>
                </div>
                @foreach (config('inventarios.valores_producto') as $campo => [$etiqueta, $maximo, $decimales])
                    <div class="col-sm-6">
                        <label class="form-label" for="producto-{{ $campo }}">{{ __($etiqueta) }}{{ str_starts_with($campo, 'descuento_') ? '' : ' (USD)' }}</label>
                        <input id="producto-{{ $campo }}" name="{{ $campo }}" class="form-control" type="number" min="0"
                            max="{{ $maximo }}" step="{{ $decimales === 4 ? '0.0001' : '0.01' }}" value="{{ old($campo, $registro[$campo] ?? '') }}">
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="producto-imagenes" role="tabpanel" aria-labelledby="producto-tab-imagenes" data-usuario-panel="imagenes" class="ficha-producto-panel">
        @include('inventarios::galeria-producto')
    </section>

    <section id="producto-documentos" role="tabpanel" aria-labelledby="producto-tab-documentos" data-usuario-panel="documentos" class="ficha-producto-panel">
        <div class="ficha-bloque">
            <h2 class="h6"><i class="bi bi-file-earmark-text me-2" aria-hidden="true"></i>Ficha técnica</h2>
            <label class="form-label" for="producto-url">Enlace al documento (HTTPS)</label>
            <input id="producto-url" name="ficha_tecnica_url" class="form-control mb-3" type="url" maxlength="500" value="{{ old('ficha_tecnica_url', $registro['ficha_tecnica_url'] ?? '') }}">
            <label class="form-label" for="producto-descripcion">{{ __('Descripción') }}</label>
            <textarea id="producto-descripcion" name="descripcion" class="form-control" rows="3" maxlength="500">{{ old('descripcion', $registro['descripcion'] ?? '') }}</textarea>
        </div>
    </section>
</div>
