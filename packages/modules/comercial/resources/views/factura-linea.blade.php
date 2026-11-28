@php
    $iva = \App\Services\FacturaVenta::IVA;
    $prefijo = 'factura-linea-'.$indice;
    $producto = $linea['id_producto'] ?? null;
    $ficha = $productos->get($producto);
    $bodegaLinea = $linea['id_bodega'] ?? ($ficha && $ficha->tipo_producto !== 'servicio' ? $datos['id_bodega'] ?? null : null);
    $ivaLinea = $ficha ? $ficha->iva_codigo : ($linea['iva_codigo'] ?? null);
@endphp
<div data-comercial-linea class="factura-linea">
    <div class="factura-linea-conceptos">
        <div>
            <label for="{{ $prefijo }}-producto" class="form-label">Producto</label>
            <select id="{{ $prefijo }}-producto" name="lineas[{{ $indice }}][id_producto]" class="form-select" data-factura-producto
                data-select2-url="{{ route('ventas.documentos.opciones', ['catalogo' => 'productos', 'factura' => 1]) }}" data-select2-min="0" data-select2-placeholder="Buscar producto">
                <option value=""></option>
                @if ($ficha)
                    <option value="{{ $producto }}" data-tipo-producto="{{ $ficha->tipo_producto }}" data-iva-codigo="{{ $ficha->iva_codigo }}" selected>{{ $ficha->nombre }}</option>
                @endif
            </select>
        </div>
        <div data-factura-bodega-bloque @if (! $ficha || $ficha->tipo_producto === 'servicio') hidden @endif>
            <label for="{{ $prefijo }}-bodega" class="form-label">Bodega de salida</label>
            <select id="{{ $prefijo }}-bodega" name="lineas[{{ $indice }}][id_bodega]" class="form-select" data-select2-nativo data-factura-bodega>
                <option value="">Elegir bodega</option>
                @foreach ($bodegas as $id => $nombre)
                    <option value="{{ $id }}" @selected((string) $bodegaLinea === (string) $id)>{{ $nombre }}</option>
                @endforeach
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
            <select id="{{ $prefijo }}-iva" name="lineas[{{ $indice }}][iva_codigo]" class="form-select" data-select2-nativo data-factura-iva @disabled($ficha) @required(! $ficha)>
                <option value="">Elegir</option>
                @foreach ($iva as $codigo => $tasa)
                    <option value="{{ $codigo }}" data-tasa="{{ $tasa }}" @selected((string) $ivaLinea === (string) $codigo)>
                        {{ \App\Services\FacturaVenta::etiquetaIva($codigo) }}
                    </option>
                @endforeach
            </select>
            <small class="text-body-secondary" data-factura-iva-origen @unless($ficha) hidden @endunless>{{ $ficha && $ficha->iva_codigo === null ? 'Configura el IVA en la ficha.' : 'IVA de la ficha' }}</small>
        </div>
        <div class="factura-linea-total">
            <span class="form-label">Total línea</span>
            <strong data-factura-linea-total>$0.00</strong>
        </div>
        <div class="factura-linea-accion">
            <button type="button" class="btn btn-outline-danger" data-comercial-quitar aria-label="Quitar línea" title="Quitar línea"><i class="bi bi-trash" aria-hidden="true"></i></button>
        </div>
    </div>
</div>
