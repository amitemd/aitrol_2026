@php
    $tiposDocumento = [
        'retenciones' => ['Retención', 'bi-percent', 'retencion'],
        'notas-credito' => ['Nota de crédito', 'bi-file-earmark-minus', 'credito'],
        'notas-debito' => ['Nota de débito', 'bi-file-earmark-plus', 'debito'],
    ];
    $puedeVerAsiento = Gate::allows('permiso', ['contabilidad.asientos.index', 'leer']);
    $puedeVerMovimiento = Gate::allows('permiso', ['inventarios.movimientos.index', 'leer']);
    $tieneTransacciones = filled($factura->numero_asiento) || filled($factura->numero_asiento_reversion)
        || $factura->lineas->contains(fn ($linea) => $linea->id_operacion || $linea->id_operacion_reversa);
@endphp

<details class="trazabilidad-diagrama" data-trazabilidad-diagrama>
    <summary>
        <i class="bi bi-diagram-3 trazabilidad-diagrama__icono" aria-hidden="true"></i>
        <strong>Diagrama de trazabilidad</strong>
        <i class="bi bi-chevron-down trazabilidad-diagrama__flecha" aria-hidden="true"></i>
    </summary>
    <div class="trazabilidad-diagrama__lienzo">
        <div class="trazabilidad-diagrama__nodo trazabilidad-diagrama__nodo--origen">
            <i class="bi bi-receipt-cutoff trazabilidad-diagrama__icono" aria-hidden="true"></i>
            <div class="trazabilidad-diagrama__datos">
                <span>{{ $modulo === 'compras' ? 'Factura de compra' : 'Factura de venta' }}</span>
                <strong>{{ $factura->numero }}</strong>
                <span>{{ $factura->tercero }} · {{ $factura->fecha }}</span>
            </div>
            <div class="trazabilidad-diagrama__pie-origen">
                <span class="trazabilidad-diagrama__estado">{{ $factura->anulado_at ? 'Anulada' : 'Vigente' }}</span>
                <strong>USD {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal((int) $factura->total, 2) }}</strong>
            </div>
        </div>
        @include('comercial::transacciones-trazabilidad', ['registro' => $factura])
        <div class="trazabilidad-diagrama__inferior trazabilidad-diagrama__rama">
        @if ($vinculados->isNotEmpty())
            <section class="trazabilidad-diagrama__rama trazabilidad-diagrama__documentos">
                <h3><i class="bi bi-link-45deg" aria-hidden="true"></i> Documentos vinculados <span>{{ $vinculados->count() }}</span></h3>
                <ul class="trazabilidad-diagrama__lista" role="list">
                    @foreach ($vinculados as $documento)
                        @php
                            [$tipo, $icono, $color] = $tiposDocumento[$documento->tipo] ?? ['Documento vinculado', 'bi-file-earmark-text', 'documento'];
                            $ruta = $modulo.'.'.$documento->tipo;
                            $enPeriodo = $periodo === null || (int) substr($documento->fecha, 0, 4) === $periodo;
                        @endphp
                        <li>
                            <div class="trazabilidad-diagrama__nodo trazabilidad-diagrama__nodo--{{ $color }}">
                                <i class="bi {{ $icono }} trazabilidad-diagrama__icono" aria-hidden="true"></i>
                                <div class="trazabilidad-diagrama__datos">
                                    <span>{{ $tipo }} · {{ $documento->fecha }}</span>
                                    @if ($enPeriodo && Gate::allows('permiso', [$ruta.'.index', 'leer']))
                                        <a href="{{ route($ruta.'.show', $documento->id) }}" target="_blank" rel="noopener">{{ $documento->numero }}</a>
                                    @else
                                        <strong>{{ $documento->numero }}</strong>
                                    @endif
                                </div>
                                <span class="trazabilidad-diagrama__estado">{{ $documento->anulado_at ? 'Anulado' : 'Vigente' }}</span>
                            </div>
                            <div class="trazabilidad-diagrama__derivaciones">
                                @include('comercial::transacciones-trazabilidad', ['registro' => $documento])
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @elseif (!$tieneTransacciones)
            <p class="trazabilidad-diagrama__vacio">Este documento todavía no tiene transacciones ni documentos vinculados.</p>
        @endif
        @if ($factura->lineas->isNotEmpty())
            <section class="trazabilidad-diagrama__rama trazabilidad-diagrama__conceptos">
                <h3><i class="bi bi-card-checklist" aria-hidden="true"></i> Productos y servicios facturados <span>{{ $factura->lineas->count() }}</span></h3>
                <ul class="trazabilidad-diagrama__lista" role="list">
                    @foreach ($factura->lineas as $linea)
                        @php
                            $producto = $productosTrazabilidad->get($linea->id_producto);
                            $tipoLinea = match ($producto?->tipo_producto) {
                                'servicio' => 'Servicio facturado',
                                'producto' => 'Producto inventariable',
                                'no_inventariable' => 'Producto no inventariable',
                                default => 'Concepto facturado',
                            };
                        @endphp
                        <li class="trazabilidad-diagrama__nodo">
                            <div class="trazabilidad-diagrama__datos">
                                <span>{{ $tipoLinea }}@if ($producto?->codigo) · {{ $producto->codigo }}@endif</span>
                                <strong>{{ $linea->descripcion }}</strong>
                                <span>{{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal((int) $linea->cantidad, 3) }} · USD {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal((int) $linea->total, 2) }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
        </div>
    </div>
</details>
