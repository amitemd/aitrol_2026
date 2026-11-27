<fieldset class="rounded border p-3" data-comercial-linea>
    <legend class="visually-hidden">Línea del documento</legend>
    <div class="row g-2">
        @if (!$vinculado)
            <div class="col-sm-6">
                <label class="form-label" for="com-{{ $indice }}-producto">Producto de inventario</label>
                <select id="com-{{ $indice }}-producto" name="lineas[{{ $indice }}][id_producto]" class="form-select"
                    data-select2-url="{{ route($ruta.'.opciones', 'productos') }}" data-select2-min="0" data-select2-placeholder="Seleccionar producto">
                    <option value="">Seleccionar producto</option>
                </select>
            </div>
        @endif
        <div @class(['col-sm-6' => !$vinculado, 'col-12' => $vinculado])>
            <label class="form-label" for="com-{{ $indice }}-descripcion">Concepto</label>
            <input id="com-{{ $indice }}-descripcion" name="lineas[{{ $indice }}][descripcion]" class="form-control"
                value="{{ $linea['descripcion'] ?? '' }}" maxlength="255" @required($vinculado)>
        </div>
        @if ($retencion)
            <div class="col-sm-6 col-xl-3">
                <label class="form-label" for="com-{{ $indice }}-tipo">Tipo</label>
                <select class="form-select" id="com-{{ $indice }}-tipo" name="lineas[{{ $indice }}][tipo_retencion]" required>
                    @foreach (['renta' => 'Renta', 'iva' => 'IVA', 'otra' => 'Otra'] as $tipo => $etiqueta)
                        <option value="{{ $tipo }}" @selected(($linea['tipo_retencion'] ?? '') === $tipo)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-xl-3">
                <label class="form-label" for="com-{{ $indice }}-codigo">Código</label>
                <input id="com-{{ $indice }}-codigo" name="lineas[{{ $indice }}][codigo_retencion]" value="{{ $linea['codigo_retencion'] ?? '' }}" class="form-control" maxlength="32" required>
            </div>
        @endif
        @php
            $importes = $retencion
                ? ['base_retencion' => ['Base retenida', 0.01, 100000000, 0.01, ''], 'porcentaje_retencion' => ['Retención (%)', 0.01, 100, 0.01, '']]
                : ['cantidad' => ['Cantidad', 0.001, 1000000, 0.001, 1], 'precio' => ['Precio unitario', 0.0001, 100000, 0.0001, ''], 'tasa' => ['Impuesto (%)', 0, 100, 0.01, 0]];
        @endphp
        @foreach ($importes as $campo => [$etiqueta, $min, $max, $paso, $predeterminado])
            <div class="col-6 col-sm">
                <label class="form-label" for="com-{{ $indice }}-{{ $campo }}">{{ $etiqueta }}</label>
                <input class="form-control" type="number" min="{{ $min }}" max="{{ $max }}" step="{{ $paso }}"
                    id="com-{{ $indice }}-{{ $campo }}" name="lineas[{{ $indice }}][{{ $campo }}]" value="{{ $linea[$campo] ?? $predeterminado }}" @required($campo !== 'tasa')>
            </div>
        @endforeach
        <div class="col-auto align-self-end">
            <button type="button" data-comercial-quitar class="btn btn-outline-danger" aria-label="Quitar línea"><i class="bi bi-trash3" aria-hidden="true"></i></button>
        </div>
    </div>
</fieldset>
