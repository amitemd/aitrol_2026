<fieldset class="rounded border p-3" data-comercial-linea>
    <legend class="visually-hidden">Línea del documento</legend>
    <div class="row g-2">
        @if (!$vinculado)
            <div class="col-sm-6">
                <label class="form-label" for="com-{{ $indice }}-producto">Producto de inventario</label>
                <select id="com-{{ $indice }}-producto" name="lineas[{{ $indice }}][id_producto]" class="form-select"
                    data-select2-url="{{ route($ruta.'.opciones', 'productos') }}" data-select2-min="0" data-select2-placeholder="Seleccionar producto"
                    @if ($compra ?? false) data-compra-producto @endif>
                    <option value="">Seleccionar producto</option>
                    @isset($productoSeleccionado)
                        <option value="{{ $productoSeleccionado->id }}" @if ($compra ?? false) data-tipo-producto="{{ $productoSeleccionado->tipo_producto }}" @endif selected>{{ $productoSeleccionado->codigo }} · {{ $productoSeleccionado->nombre }}</option>
                    @endisset
                </select>
                @if (($importada ?? false) && ($puedeCrearProducto ?? false) && isset($linea['descripcion']))
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-compra-crear-producto
                        data-codigo="{{ mb_substr($linea['codigo_xml'] ?? '', 0, 60) }}" data-iva="{{ $linea['iva_codigo_xml'] ?? '' }}">
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Crear producto
                    </button>
                @endif
            </div>
        @endif
        @unless ($retencionElectronica ?? false)
        <div @class(['col-sm-6' => !$vinculado, 'col-12' => $vinculado])>
            <label class="form-label" for="com-{{ $indice }}-descripcion">Concepto</label>
            <input id="com-{{ $indice }}-descripcion" name="lineas[{{ $indice }}][descripcion]" class="form-control"
                value="{{ $linea['descripcion'] ?? '' }}" maxlength="255" @required($vinculado) @readonly($importada ?? false)>
        </div>
        @endunless
        @if ($retencion)
            <div class="col-sm-4 col-xl-2">
                <label class="form-label" for="com-{{ $indice }}-tipo">{{ __('Tipo') }}</label>
                <select class="form-select" id="com-{{ $indice }}-tipo" name="lineas[{{ $indice }}][tipo_retencion]" data-retencion-tipo required>
                    @foreach (['renta' => 'Renta', 'iva' => 'IVA'] as $tipo => $etiqueta)
                        <option value="{{ $tipo }}" @selected(($linea['tipo_retencion'] ?? '') === $tipo)>{{ __($etiqueta) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-8 col-xl-4">
                <label class="form-label" for="com-{{ $indice }}-codigo">Código de retención</label>
                <select id="com-{{ $indice }}-codigo" name="lineas[{{ $indice }}][codigo_retencion]" class="form-select"
                    data-retencion-catalogo data-select2-url="{{ route($ruta.'.catalogo') }}" data-select2-min="0"
                    @unless ($retencionElectronica ?? false) data-retencion-recibida @endunless
                    data-select2-placeholder="Código o descripción" required>
                    <option value=""></option>
                    @if (!empty($linea['codigo_retencion']))
                        <option value="{{ $linea['codigo_retencion'] }}" selected>{{ $linea['codigo_retencion'] }}</option>
                    @endif
                </select>
            </div>
        @endif
        @php
            $importes = $retencion
                ? ['base_retencion' => ['Base', 0.01, 100000000, 0.01, ''], 'porcentaje_retencion' => ['Retención (%)', ($retencionElectronica ?? false) ? 0 : 0.01, 100, 0.01, '']]
                : ['cantidad' => ['Cantidad', 0.001, 1000000, 0.001, 1], 'precio' => ['Precio unitario', 0.0001, 100000, 0.0001, ''], 'tasa' => ['Impuesto (%)', 0, 100, 0.01, 0]];
            if ($compra ?? false) {
                $importes += ['descuento' => ['Descuento USD', 0, 100000000000, 0.01, 0]];
            }
        @endphp
        @foreach ($importes as $campo => [$etiqueta, $min, $max, $paso, $predeterminado])
            <div class="col-6 col-sm">
                <label class="form-label" for="com-{{ $indice }}-{{ $campo }}">{{ __($etiqueta) }}</label>
                <input class="form-control" type="number" min="{{ $min }}" max="{{ $max }}" step="{{ $paso }}"
                    id="com-{{ $indice }}-{{ $campo }}" name="lineas[{{ $indice }}][{{ $campo }}]" value="{{ $linea[$campo] ?? $predeterminado }}"
                    @if ($compra ?? false) data-compra-campo="{{ $campo }}" @endif
                    @if ($retencion) data-retencion-campo="{{ $campo }}" @endif
                    @required(in_array($campo, ['cantidad', 'precio', 'porcentaje_retencion'], true)) @readonly($campo === 'base_retencion' || ($importada ?? false))>
            </div>
        @endforeach
        @if ($retencion)
            <div class="col-6 col-sm-auto align-self-end py-2">
                <span class="small text-body-secondary d-block">Valor retenido</span>
                <strong data-retencion-linea-total>$0,00</strong>
            </div>
        @endif
        @if ($compra ?? false)
            <div class="col-6 col-sm-auto align-self-end py-2">
                <span class="small text-body-secondary d-block">Total línea</span>
                <strong data-compra-linea-total>$0,00</strong>
            </div>
        @endif
        @unless ($importada ?? false)
            <div class="col-auto align-self-end">
                <button type="button" data-comercial-quitar class="btn btn-outline-danger" aria-label="Quitar línea"><i class="bi bi-trash3" aria-hidden="true"></i></button>
            </div>
        @endunless
    </div>
</fieldset>
