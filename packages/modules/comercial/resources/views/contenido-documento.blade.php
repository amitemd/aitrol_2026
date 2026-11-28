<dl class="row">
    <dt class="col-sm-4">Número</dt><dd class="col-sm-8">{{ $documento['numero'] }}</dd>
    @isset($documento['clave_acceso'])
        <dt class="col-sm-4">Clave de acceso</dt><dd class="col-sm-8 text-break font-monospace" data-factura-clave-acceso>{{ $documento['clave_acceso'] }}</dd>
        <dt class="col-sm-4">Ambiente SRI</dt><dd class="col-sm-8">{{ $documento['ambiente'] === '2' ? 'Producción' : 'Pruebas' }}</dd>
    @endisset
    @isset($documento['estado_retencion'])
        <dt class="col-sm-4">Estado electrónico</dt>
        <dd class="col-sm-8">{{ $documento['estado_retencion'] }} · <a href="{{ route($modulo.'.retenciones.xml', $documento['id']) }}">Descargar XML</a></dd>
        @if ($modulo === 'compras' && !$documento['anulado_at'] && $documento['estado_retencion'] !== 'Autorizada en el SRI' && Gate::allows('permiso', ['compras.retenciones.index', 'actualizar']))
            <dd class="col-sm-8 offset-sm-4">
                <form method="POST" action="{{ route('compras.retenciones.emitir', $documento['id']) }}" data-ajax-form>
                    @csrf
                    <button class="btn btn-sm btn-primary" type="submit">{{ empty($documento['recibida_sri']) ? 'Firmar y enviar al SRI' : 'Consultar autorización' }}</button>
                </form>
            </dd>
        @endif
    @endisset
    @isset($documento['fuente_compra'])
        <dt class="col-sm-4">Comprobante recibido</dt>
        <dd class="col-sm-8"><a href="{{ route('compras.documentos.xml', $documento['id']) }}">Descargar XML</a></dd>
    @endisset
    @isset($documento['forma_pago'])
        <dt class="col-sm-4">Forma de pago</dt><dd class="col-sm-8">{{ $documento['forma_pago'] }} - {{ \App\Services\FacturaVenta::PAGOS[$documento['forma_pago']] ?? 'Forma de pago registrada' }}</dd>
        @isset($documento['plazo_dias'])
            <dt class="col-sm-4">Días de crédito</dt><dd class="col-sm-8">{{ $documento['plazo_dias'] }}</dd>
        @endisset
    @endisset
    @if (!empty($documento['pagos']))
        <dt class="col-sm-4">Formas de pago</dt>
        <dd class="col-sm-8">
            @foreach ($documento['pagos'] as $pago)
                <div>{{ $pago['codigo'] }} · {{ \App\Services\FacturaVenta::PAGOS[$pago['codigo']] }} · USD {{ $pago['importe'] }}
                    @if (filled($pago['plazo'])) · {{ $pago['plazo'] }} {{ $pago['unidad_tiempo'] }} @endif
                </div>
            @endforeach
        </dd>
    @endif
    @if ($documento['numero_asiento'] || $documento['numero_asiento_reversion'])
        <dt class="col-sm-4">{{ __('Contabilidad') }}</dt><dd class="col-sm-8">@include('partials.referencia-asiento', ['registroAsiento' => $documento])</dd>
    @endif
    <dt class="col-sm-4">{{ __('Fecha') }}</dt><dd class="col-sm-8">{{ $documento['fecha'] }}</dd>
    <dt class="col-sm-4">{{ config('comercial.'.$modulo.'.tercero') }}</dt><dd class="col-sm-8">{{ $documento['tercero'] }}</dd>
    @if ($documento['numero_origen'])
        <dt class="col-sm-4">Documento de origen</dt><dd class="col-sm-8">{{ $documento['numero_origen'] }}</dd>
    @endif
    <dt class="col-sm-4">{{ __('Estado') }}</dt><dd class="col-sm-8">{{ $documento['anulado_at'] ? 'Anulado' : 'Vigente' }}</dd>
    @if ($documento['anulado_at'])
        <dt class="col-sm-4">Motivo de anulación</dt><dd class="col-sm-8">{{ $documento['motivo_anulacion'] }}</dd>
        <dt class="col-sm-4">Fecha de anulación</dt><dd class="col-sm-8">{{ $documento['anulado_at'] }}</dd>
    @endif
    @if ($documento['observacion'])
        <dt class="col-sm-4">Observación</dt><dd class="col-sm-8">{{ $documento['observacion'] }}</dd>
    @endif
</dl>
@if (!empty($documento['informacion_adicional']))
    <h2 class="h6">Información adicional</h2>
    <dl class="row">
        @foreach ($documento['informacion_adicional'] as $dato)
            <dt class="col-sm-4 text-break">{{ $dato['nombre'] }}</dt><dd class="col-sm-8 text-break">{{ $dato['valor'] }}</dd>
        @endforeach
    </dl>
@endif
@php $retencion = $documento['tipo'] === 'retenciones'; @endphp
<div class="table-responsive">
    <table class="table align-middle">
        <caption class="visually-hidden">Detalle del documento</caption>
        <thead><tr>
            <th>Concepto</th>
            <th class="text-end">{{ $retencion ? 'Base' : 'Cantidad' }}</th>
            <th class="text-end">{{ $retencion ? 'Retención (%)' : 'Precio unitario' }}</th>
            <th class="text-end">{{ __('Total') }}</th>
        </tr></thead>
        <tbody>
            @foreach ($documento['lineas'] as $linea)
                <tr>
                    <th scope="row">{{ $linea['descripcion'] }}
                        @if ($linea['nombre_bodega'])<small class="d-block text-body-secondary">Bodega: {{ $linea['nombre_bodega'] }}</small>@endif
                        @if ($retencion)<small class="d-block text-body-secondary">{{ Str::upper($linea['tipo_retencion']) }} · {{ $linea['codigo_retencion'] }}</small>@endif
                    </th>
                    <td class="text-end">{{ $retencion ? $linea['base_retencion'] : $linea['cantidad'] }}</td>
                    <td class="text-end">{{ $retencion ? $linea['porcentaje_retencion'] : $linea['precio'] }}</td>
                    <td class="text-end">{{ $linea['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if (!$retencion)
                <tr><th scope="row" colspan="3" class="text-end">Subtotal</th><td class="text-end">{{ $documento['subtotal'] }}</td></tr>
                <tr><th scope="row" colspan="3" class="text-end">{{ __('Impuesto') }}</th><td class="text-end">{{ $documento['impuesto'] }}</td></tr>
                @if ((float) $documento['propina'] > 0)
                    <tr><th scope="row" colspan="3" class="text-end">Propina</th><td class="text-end">{{ $documento['propina'] }}</td></tr>
                @endif
            @endif
            <tr><th scope="row" colspan="3" class="text-end">{{ $retencion ? 'Total retenido' : 'Total' }}</th><td class="text-end fw-semibold">{{ $documento['total'] }}</td></tr>
        </tfoot>
    </table>
</div>
