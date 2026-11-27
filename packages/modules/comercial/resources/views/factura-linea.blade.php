@php
    $iva = \App\Services\FacturaVenta::IVA;
    $prefijo = 'factura-linea-'.$indice;
    $producto = $linea['id_producto'] ?? null;
@endphp
<div data-comercial-linea class="factura-linea">
    <div class="factura-linea-conceptos">
        <div>
            <label for="{{ $prefijo }}-producto" class="form-label">Producto</label>
            <select id="{{ $prefijo }}-producto" name="lineas[{{ $indice }}][id_producto]" class="form-select" data-factura-producto
                data-select2-url="{{ route('ventas.documentos.opciones', ['catalogo' => 'productos', 'factura' => 1]) }}" data-select2-min="0" data-select2-placeholder="Buscar producto">
                <option value=""></option>
                @if (isset($productos[$producto]))
                    <option value="{{ $producto }}" selected>{{ $productos[$producto] }}</option>
                @endif
            </select>
        </div>
        <div>
            <label for="{{ $prefijo }}-concepto" class="form-label">Concepto</label>
            <input id="{{ $prefijo }}-concepto" class="form-control" name="lineas[{{ $indice }}][descripcion]" maxlength="255"
                placeholder="Descripción del servicio o del producto" value="{{ $linea['descripcion'] ?? '' }}">
        </div>
    </div>
    <div class="factura-linea-importes">
        @foreach (['cantidad' => ['Cantidad', '0.001', 1, '0.001'], 'precio' => ['Precio unitario', '0.0001', '', '0'], 'descuento' => ['Descuento USD', '0.01', 0, '0']] as $campo => [$etiqueta, $paso, $inicial, $minimo])
            <div>
                <label for="{{ $prefijo }}-{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                <input id="{{ $prefijo }}-{{ $campo }}" class="form-control" type="number" inputmode="decimal" min="{{ $minimo }}" step="{{ $paso }}"
                    name="lineas[{{ $indice }}][{{ $campo }}]" value="{{ $linea[$campo] ?? $inicial }}" data-factura-campo="{{ $campo }}" @required($campo !== 'descuento')>
            </div>
        @endforeach
        <div>
            <label for="{{ $prefijo }}-iva" class="form-label">IVA</label>
            <select id="{{ $prefijo }}-iva" name="lineas[{{ $indice }}][iva_codigo]" class="form-select" data-select2-nativo data-factura-iva required>
                <option value="">Elegir</option>
                @foreach ($iva as $codigo => $tasa)
                    <option value="{{ $codigo }}" data-tasa="{{ $tasa }}" @selected((string) ($linea['iva_codigo'] ?? '') === (string) $codigo)>
                        {{ match ($codigo) { 6 => 'No objeto', 7 => 'Exento', default => $tasa.' %' } }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="factura-linea-total">
            <span class="form-label">Total línea</span>
            <strong data-factura-linea-total>$0.00</strong>
        </div>
        <button type="button" class="btn btn-outline-danger" data-comercial-quitar aria-label="Quitar línea" title="Quitar línea"><i class="bi bi-trash" aria-hidden="true"></i></button>
    </div>
</div>
