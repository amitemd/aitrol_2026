@extends('layouts.adminlte')

@section('title', 'Recepciones de la orden '.$orden['numero'])
@php $bloqueada = !\Aitrol\Comercial\Application\AprobacionesOrdenCompra::permiteOperar($orden['estado_aprobacion']); @endphp

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Recepciones de la orden '.$orden['numero'], 'subtitulo' => $orden['proveedor'], 'icono' => 'bi-box-arrow-in-down'])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            @include('comercial::errores')
            @if ($orden['anulada'])
                <p class="alert alert-warning">Orden anulada</p>
            @elseif ($bloqueada)
                <div class="alert alert-warning">
                    {{ \Aitrol\Comercial\Application\AprobacionesOrdenCompra::ESTADOS[$orden['estado_aprobacion']] }}.
                    La orden debe estar aprobada para recibir mercadería o cotejar su factura.
                    <a class="alert-link" href="{{ route('compras.aprobaciones.show', $orden['id']) }}">Ver aprobación</a>
                </div>
            @endif
            <section class="card mb-3">
                <div class="card-header"><h2 class="card-title">Avance de la orden</h2></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th scope="col">Concepto</th><th scope="col" class="text-end">Ordenado</th>
                            <th scope="col" class="text-end">Recibido</th><th scope="col" class="text-end">Pendiente</th></tr></thead>
                        <tbody>
                            @foreach ($lineas as $linea)
                                <tr>
                                    <th scope="row">{{ $linea['descripcion'] }}</th>
                                    <td class="text-end">{{ $linea['cantidad'] }}</td>
                                    @if ($linea['inventariable'])
                                        <td class="text-end">{{ $linea['recibida'] }}</td><td class="text-end">{{ $linea['pendiente'] }}</td>
                                    @else
                                        <td class="text-end text-body-secondary" colspan="2">Servicio · {{ $linea['servicio_por_facturar'] }} por facturar</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            @php
                $pendientes = $lineas->filter(fn ($linea) => $linea['inventariable'] && $linea['pendiente'] !== '0.000');
                $lineasAnteriores = collect(old('lineas', []))->filter(fn ($linea) => is_array($linea) && is_numeric($linea['id_linea_orden'] ?? null))
                    ->keyBy('id_linea_orden');
            @endphp
            @if (!$orden['anulada'] && !$bloqueada && $puedeRecibir && $pendientes->isNotEmpty())
                <form method="POST" action="{{ route('compras.ordenes-compra.recepciones.store', $orden['id']) }}" class="card mb-3">
                    @csrf
                    <input type="hidden" name="clave_operacion" value="{{ old('formulario') === 'recepcion' ? old('clave_operacion', Str::uuid()) : Str::uuid() }}">
                    <input type="hidden" name="formulario" value="recepcion">
                    <div class="card-header"><h2 class="card-title">Registrar recepción</h2></div>
                    <div class="card-body">
                        <label for="recepcion-bodega" class="form-label">Bodega de destino</label>
                        <select name="id_bodega" id="recepcion-bodega" class="form-select mb-3" required>
                            <option value="">Seleccionar bodega de la sucursal</option>
                            @foreach ($bodegas as $bodega)
                                <option value="{{ $bodega->id }}" @selected((int) old('id_bodega') === (int) $bodega->id)>{{ $bodega->codigo }} · {{ $bodega->nombre }}</option>
                            @endforeach
                        </select>
                        <p class="text-body-secondary">Ingresa solo las cantidades entregadas hoy.</p>
                        <div class="row g-3">
                            @foreach ($pendientes as $linea)
                                <div class="col-md-6 col-xl-4">
                                    <label for="recepcion-linea-{{ $linea['id'] }}" class="form-label">{{ $linea['descripcion'] }} · máx. {{ $linea['pendiente'] }}</label>
                                    <input type="hidden" name="lineas[{{ $linea['id'] }}][id_linea_orden]" value="{{ $linea['id'] }}">
                                    <input id="recepcion-linea-{{ $linea['id'] }}" class="form-control" type="number" min="0.001" step="0.001" max="{{ $linea['pendiente'] }}"
                                        name="lineas[{{ $linea['id'] }}][cantidad]"
                                        value="{{ old('formulario') === 'recepcion' ? data_get($lineasAnteriores->get($linea['id']), 'cantidad') : '' }}">
                                </div>
                            @endforeach
                            <div class="col-12">
                                <label for="recepcion-observacion" class="form-label">Observación</label>
                                <textarea id="recepcion-observacion" class="form-control" name="observacion" maxlength="500" rows="2">{{ old('formulario') === 'recepcion' ? old('observacion') : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end"><button class="btn btn-primary" type="submit">Confirmar recepción</button></div>
                </form>
            @endif

            <section class="card mb-3">
                <div class="card-header"><h2 class="card-title">Historial de recepciones</h2></div>
                <div class="card-body">
                    @forelse ($recepciones as $recepcion)
                        <details class="border rounded p-3 mb-2" id="recepcion-{{ $recepcion['id'] }}">
                            <summary>
                                <strong>Recepción #{{ $recepcion['id'] }}</strong> · {{ $recepcion['fecha'] }} · {{ $recepcion['bodega'] }}
                                · USD {{ $recepcion['total'] }}
                                · {{ $recepcion['anulado_at'] ? 'Anulada' : ($recepcion['facturable'] ? ($recepcion['id_factura'] ? 'Facturada parcialmente' : 'Por facturar') : 'Facturada') }}
                            </summary>
                            <ul class="mt-2 mb-2">
                                @foreach ($recepcion['lineas'] as $linea)
                                    <li>{{ $linea['descripcion'] }} · Recibido {{ $linea['cantidad'] }} · Por facturar {{ $linea['cantidad_por_facturar'] }}</li>
                                @endforeach
                            </ul>
                            <p class="mb-2">Asiento de recepción {{ $recepcion['numero_asiento'] }}</p>
                            @if ($recepcion['anulado_at'])
                                <p class="mb-0">Motivo: {{ $recepcion['motivo_anulacion'] }}</p>
                            @elseif ($recepcion['id_factura'])
                                @can('permiso', ['compras.documentos.index', 'leer'])
                                    <ul class="mb-0">
                                        @foreach ($recepcion['facturas'] as $factura)
                                            <li><a href="{{ route('compras.documentos.trazabilidad', $factura['id']) }}">Factura {{ $factura['numero'] }}</a></li>
                                        @endforeach
                                    </ul>
                                @endcan
                            @elseif ($puedeRecibir)
                                <form method="POST" action="{{ route('compras.ordenes-compra.recepciones.anular', [$orden['id'], $recepcion['id']]) }}" class="row g-2 align-items-end">
                                    @csrf
                                    <div class="col-sm">
                                        <label for="motivo-{{ $recepcion['id'] }}" class="form-label">Motivo de reversión</label>
                                        <input id="motivo-{{ $recepcion['id'] }}" name="motivo" class="form-control" maxlength="250" required>
                                    </div>
                                    <div class="col-sm-auto"><button class="btn btn-outline-danger" type="submit">Revertir recepción</button></div>
                                </form>
                            @endif
                        </details>
                    @empty
                        <p class="text-body-secondary mb-0">Todavía no se registran recepciones.</p>
                    @endforelse
                </div>
                @if ($recepciones->hasPages())
                    <div class="card-footer">{{ $recepciones->links('pagination::bootstrap-5') }}</div>
                @endif
            </section>

            @php
                $facturables = $recepciones->getCollection()->where('facturable', true);
                $lineasFacturables = $facturables->flatMap(fn ($recepcion) => $recepcion['lineas']->where('cantidad_por_facturar', '!=', '0.000'))
                    ->groupBy('id_linea_orden');
            @endphp
            @if (!$orden['anulada'] && !$bloqueada && $puedeFacturar && $facturables->isNotEmpty())
                <form method="POST" action="{{ route('compras.ordenes-compra.recepciones.facturar', $orden['id']) }}" class="card mb-3">
                    @csrf
                    <input type="hidden" name="formulario" value="factura">
                    <input type="hidden" name="modo_cotejo" value="parcial">
                    <div class="card-header"><h2 class="card-title">Cotejar y registrar factura de compra</h2></div>
                    <div class="card-body">
                        <fieldset class="mb-3">
                            <legend class="h6">Recepciones incluidas en el comprobante</legend>
                            @foreach ($facturables as $recepcion)
                                <div class="form-check">
                                    <input class="form-check-input" id="facturar-recepcion-{{ $recepcion['id'] }}" type="checkbox" name="recepciones[]" value="{{ $recepcion['id'] }}"
                                        @checked(in_array($recepcion['id'], (array) old('recepciones', [])))>
                                    <label class="form-check-label" for="facturar-recepcion-{{ $recepcion['id'] }}">Recepción #{{ $recepcion['id'] }} · {{ $recepcion['fecha'] }}
                                        · Base por facturar USD {{ $recepcion['base_por_facturar'] }}</label>
                                </div>
                            @endforeach
                        </fieldset>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="factura-recepcion-numero" class="form-label">Número de factura del proveedor</label>
                                <input id="factura-recepcion-numero" name="numero" class="form-control" maxlength="60" value="{{ old('numero') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="factura-recepcion-fecha" class="form-label">Fecha de factura</label>
                                <input id="factura-recepcion-fecha" name="fecha" type="date" class="form-control" value="{{ old('fecha', now('America/Guayaquil')->toDateString()) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="factura-recepcion-pago" class="form-label">Forma de pago</label>
                                <select id="factura-recepcion-pago" name="forma_pago" class="form-select" required>
                                    <option value="">Seleccionar</option>
                                    @foreach (\App\Services\FacturaVenta::PAGOS as $codigo => $nombre)
                                        <option value="{{ $codigo }}" @selected((string) old('forma_pago') === (string) $codigo)>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="factura-recepcion-plazo" class="form-label">Plazo de pago (días)</label>
                                <input id="factura-recepcion-plazo" name="plazo_dias" type="number" min="0" max="3650" class="form-control" value="{{ old('plazo_dias') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="factura-recepcion-subtotal" class="form-label">Subtotal del comprobante (USD)</label>
                                <input id="factura-recepcion-subtotal" name="subtotal" type="number" min="0.01" step="0.01" class="form-control" value="{{ old('subtotal') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="factura-recepcion-impuesto" class="form-label">Impuesto del comprobante (USD)</label>
                                <input id="factura-recepcion-impuesto" name="impuesto" type="number" min="0" step="0.01" class="form-control" value="{{ old('impuesto') }}" required>
                            </div>
                        </div>
                        <h3 class="h6">Cantidades y precios según la factura</h3>
                        <p class="text-body-secondary">Indica las cantidades del comprobante. Se aplicarán primero a la recepción seleccionada más antigua. Deja en blanco las líneas que no figuran en la factura.</p>
                        @foreach ($lineas->filter(fn ($linea) => $lineasFacturables->has($linea['id']) || ($linea['servicio_por_facturar'] !== null && $linea['servicio_por_facturar'] !== '0.000')) as $linea)
                            @php
                                $saldo = $linea['servicio_por_facturar'] ?? $lineasFacturables->get($linea['id'])->sum('cantidad_por_facturar');
                            @endphp
                            <fieldset class="row g-2 border-top pt-2 mb-3">
                                <legend class="h6">{{ $linea['descripcion'] }} · {{ $linea['servicio_por_facturar'] !== null ? 'Servicio' : 'Mercadería' }}
                                    · {{ $saldo }} por facturar · USD {{ $linea['precio'] }} por unidad</legend>
                                <input type="hidden" name="lineas[{{ $linea['id'] }}][id_linea_orden]" value="{{ $linea['id'] }}">
                                <div class="col-sm-6 col-xl-3">
                                    <label for="factura-cantidad-{{ $linea['id'] }}" class="form-label">Cantidad facturada</label>
                                    <input id="factura-cantidad-{{ $linea['id'] }}" name="lineas[{{ $linea['id'] }}][cantidad]" type="number" min="0.001" max="{{ number_format((float) $saldo, 3, '.', '') }}" step="0.001" class="form-control"
                                        value="{{ old('formulario') === 'factura' ? data_get($lineasAnteriores->get($linea['id']), 'cantidad') : '' }}">
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <label for="factura-precio-{{ $linea['id'] }}" class="form-label">Precio unitario (USD)</label>
                                    <input id="factura-precio-{{ $linea['id'] }}" name="lineas[{{ $linea['id'] }}][precio]" type="number" min="0.0001" step="0.0001" class="form-control"
                                        value="{{ old('formulario') === 'factura' ? data_get($lineasAnteriores->get($linea['id']), 'precio') : '' }}">
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <label for="factura-tasa-{{ $linea['id'] }}" class="form-label">IVA (%)</label>
                                    <input id="factura-tasa-{{ $linea['id'] }}" name="lineas[{{ $linea['id'] }}][tasa]" type="number" min="0" max="100" step="0.01" class="form-control"
                                        placeholder="{{ $linea['tasa'] }} (orden)"
                                        value="{{ old('formulario') === 'factura' ? data_get($lineasAnteriores->get($linea['id']), 'tasa') : '' }}">
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <label for="factura-descuento-{{ $linea['id'] }}" class="form-label">Descuento de la línea (USD)</label>
                                    <input id="factura-descuento-{{ $linea['id'] }}" name="lineas[{{ $linea['id'] }}][descuento]" type="number" min="0" step="0.01" class="form-control"
                                        value="{{ old('formulario') === 'factura' ? data_get($lineasAnteriores->get($linea['id']), 'descuento') : '' }}">
                                </div>
                            </fieldset>
                        @endforeach
                        @if ($puedeAprobarDiferencias)
                            <label for="factura-motivo-diferencia" class="form-label">Motivo de la aprobación</label>
                            <textarea id="factura-motivo-diferencia" name="motivo_diferencia" class="form-control" maxlength="500" rows="3"
                                aria-describedby="factura-motivo-ayuda">{{ old('motivo_diferencia') }}</textarea>
                            <div id="factura-motivo-ayuda" class="form-text">Obligatorio si el precio, el subtotal o el IVA difieren de lo esperado.</div>
                        @endif
                    </div>
                    <div class="card-footer text-end"><button class="btn btn-primary" type="submit">Registrar factura cotejada</button></div>
                </form>
            @endif
        </div>
    </div>
@endsection
