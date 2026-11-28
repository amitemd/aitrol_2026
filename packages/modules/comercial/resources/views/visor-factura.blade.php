@php
    $esCompra = $modulo === 'compras';
    $proveedor = $esCompra ? $proveedoresFactura->get($documento['id_proveedor']) : null;
    $emisor = $documento['emisor'] ?? ($esCompra
        ? ['razon_social' => $documento['tercero'], 'ruc' => $proveedor?->identificacion, 'direccion' => $proveedor?->direccion]
        : (array) $empresaFactura);
    $sucursalEmisor = $esCompra ? ($emisor['sucursal'] ?? null) : $sucursal->direccion;
    $telefonoEmisor = $esCompra ? $proveedor?->telefono : $empresaFactura?->telefono;
    $cliente = $esCompra
        ? ['nombre' => $empresaFactura?->razon_social, 'identificacion' => $empresaFactura?->ruc, 'direccion' => $empresaFactura?->direccion]
        : ($documento['cliente'] ?? []);
    $fecha = \Illuminate\Support\Carbon::parse($documento['fecha']);
@endphp

<article class="archivo-documentos__hoja visor-factura" data-bs-theme="light" @if ($detalleModal ?? false) data-ajax-form @endif>
    <header class="visor-factura__encabezado">
        <div>
            <div class="visor-factura__emisor" data-archivo-emisor>
                @if (!empty($logoEnFicha))
                    <img class="visor-factura__logo" src="{{ $logoFactura }}" alt="">
                @endif
                <h3>{{ $emisor['razon_social'] ?? $empresaFactura?->razon_social }}</h3>
            </div>
            <dl class="visor-factura__ficha mt-2">
                @if (filled($emisor['ruc'] ?? null))
                    <div><dt>RUC</dt><dd>{{ $emisor['ruc'] }}</dd></div>
                @endif
                @if (filled($emisor['direccion'] ?? null))
                    <div><dt>Matriz</dt><dd>{{ $emisor['direccion'] }}</dd></div>
                @endif
                @if (filled($sucursalEmisor))
                    <div><dt>Sucursal</dt><dd>{{ $sucursalEmisor }}</dd></div>
                @endif
                @if (filled($telefonoEmisor))
                    <div><dt>Teléfono</dt><dd>{{ $telefonoEmisor }}</dd></div>
                @endif
            </dl>
        </div>
        <div>
            <span class="visor-factura__tipo">{{ $esCompra ? 'Factura de compra' : 'Factura' }}</span>
            <p class="visor-factura__numero">{{ $documento['numero'] }}</p>
            <dl class="visor-factura__ficha">
                <div><dt>Fecha de emisión</dt><dd><time datetime="{{ $fecha->toDateString() }}">{{ $fecha->format('d/m/Y') }}</time></dd></div>
                <div><dt>Estado</dt><dd>{{ $documento['anulado_at'] ? 'Anulado' : 'Vigente' }}</dd></div>
                @if (isset($documento['saldo_cobro']) && !$documento['anulado_at'])
                    <div>
                        <dt>{{ $documento['saldo_cobro'] < 0 ? 'Saldo a favor' : 'Saldo por cobrar' }}</dt>
                        <dd>USD {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal(abs($documento['saldo_cobro']), 2) }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </header>

    <div class="visor-factura__datos">
        <section>
            <h3>{{ $esCompra ? 'Comprador' : 'Facturar a' }}</h3>
            <p class="fw-semibold mb-1">{{ $cliente['nombre'] ?? $documento['tercero'] }}</p>
            @foreach (['identificacion', 'direccion', 'telefono', 'email'] as $campo)
                @if (filled($cliente[$campo] ?? null))
                    <div>{{ $cliente[$campo] }}</div>
                @endif
            @endforeach
            @if (filled($documento['clave_acceso'] ?? null))
                <dl class="visor-factura__clave">
                    <dt>Clave de acceso</dt>
                    <dd class="font-monospace mb-0" data-factura-clave-acceso>{{ $documento['clave_acceso'] }}</dd>
                </dl>
            @endif
        </section>
        <section>
            <h3>Datos del comprobante</h3>
            <dl class="visor-factura__ficha">
                @isset($documento['forma_pago'])
                    <div><dt>Forma de pago</dt><dd>{{ $documento['forma_pago'] }} · {{ \App\Services\FacturaVenta::PAGOS[$documento['forma_pago']] ?? 'Forma de pago registrada' }}</dd></div>
                @endisset
                @foreach ($documento['pagos'] ?? [] as $pago)
                    <div>
                        <dt>Forma de pago</dt>
                        <dd>{{ $pago['codigo'] }} · {{ \App\Services\FacturaVenta::PAGOS[$pago['codigo']] ?? 'Forma de pago registrada' }} · USD {{ $pago['importe'] }}
                            @if (filled($pago['plazo'])) · {{ $pago['plazo'] }} {{ $pago['unidad_tiempo'] }} @endif
                        </dd>
                    </div>
                @endforeach
                @isset($documento['plazo_dias'])
                    <div><dt>Días de crédito</dt><dd>{{ $documento['plazo_dias'] }}</dd></div>
                @endisset
                @isset($documento['ambiente'])
                    <div><dt>Ambiente SRI</dt><dd>{{ $documento['ambiente'] === '2' ? 'Producción' : 'Pruebas' }}</dd></div>
                @endisset
                @if ($documento['numero_asiento'] || $documento['numero_asiento_reversion'])
                    <div><dt>Contabilidad</dt><dd>@include('partials.referencia-asiento', ['registroAsiento' => $documento])</dd></div>
                @endif
                @if ($documento['anulado_at'])
                    <div><dt>Fecha de anulación</dt><dd>{{ $documento['anulado_at'] }}</dd></div>
                    <div><dt>Motivo de anulación</dt><dd>{{ $documento['motivo_anulacion'] }}</dd></div>
                @endif
            </dl>
            @if ($esCompra)
                @if ($documento['tiene_xml'])
                    <a class="btn btn-sm btn-outline-primary mt-2" href="{{ route('compras.documentos.xml', $documento['id']) }}">Descargar XML</a>
                @endif
            @else
                @include('comercial::estado-sri', ['documentoSri' => $documento])
            @endif
        </section>
    </div>

    <div class="table-responsive visor-factura__detalle">
        <table class="table align-middle mb-0">
            <caption class="visually-hidden">Productos y servicios facturados</caption>
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Descripción</th>
                    <th scope="col" class="text-end">Cantidad</th>
                    <th scope="col" class="text-end">Precio unitario</th>
                    <th scope="col" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documento['lineas'] as $linea)
                    <tr>
                        <td>{{ $codigosProductos->get($linea['id_producto']) ?? '—' }}</td>
                        <th scope="row">
                            {{ $linea['descripcion'] }}
                            @if ($linea['nombre_bodega'])
                                <small class="d-block text-body-secondary">Bodega: {{ $linea['nombre_bodega'] }}</small>
                            @endif
                        </th>
                        <td class="text-end">{{ $linea['cantidad'] }}</td>
                        <td class="text-end">{{ $linea['precio'] }}</td>
                        <td class="text-end">{{ $linea['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <footer class="visor-factura__pie">
        @if (!empty($documento['informacion_adicional']) || filled($documento['observacion']))
            <section class="visor-factura__adicional">
                <h3>Información adicional</h3>
                <dl class="mb-0">
                    @foreach ($documento['informacion_adicional'] ?? [] as $dato)
                        <div><dt>{{ $dato['nombre'] }}</dt><dd>{{ $dato['valor'] }}</dd></div>
                    @endforeach
                    @if (filled($documento['observacion']))
                        <div><dt>Observación</dt><dd>{{ $documento['observacion'] }}</dd></div>
                    @endif
                </dl>
            </section>
        @endif
        <dl class="visor-factura__totales">
            <div><dt>Subtotal</dt><dd>{{ $documento['subtotal'] }}</dd></div>
            <div><dt>IVA</dt><dd>{{ $documento['impuesto'] }}</dd></div>
            @if ((float) $documento['propina'] > 0)
                <div><dt>Propina</dt><dd>{{ $documento['propina'] }}</dd></div>
            @endif
            <div class="visor-factura__gran-total"><dt>Total</dt><dd>USD {{ $documento['total'] }}</dd></div>
        </dl>
    </footer>
    @if (($verRespaldo ?? false) && $documento['mime_respaldo'])
        <section class="visor-factura__respaldo">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h3 class="h6 mb-0">Archivo de respaldo</h3>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('compras.documentos.respaldo.archivo', ['registro' => $documento['id'], 'descargar' => 1]) }}">Descargar archivo</a>
            </div>
            @php $urlRespaldo = route('compras.documentos.respaldo.archivo', $documento['id']); @endphp
            @if ($documento['mime_respaldo'] === 'application/pdf')
                <iframe class="visor-factura__pdf" src="{{ $urlRespaldo }}" title="PDF de respaldo de la compra" loading="lazy"></iframe>
            @else
                <img class="img-fluid d-block mx-auto" src="{{ $urlRespaldo }}" alt="Archivo de respaldo de la compra" loading="lazy">
            @endif
        </section>
    @endif
</article>
