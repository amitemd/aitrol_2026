@extends('dashboard.labs.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset('/css/dashboard-labs.css') }}?v=20260805-6">

@php
    $labsEmbebido = request()->ajax();
    $etiquetasSeguro = [];
    $idsSeguro = [];
    $facturadoSeguro = [];
    $emitidoSeguro = [];
    $cotizadoSeguro = [];
    $sumaSeguro = 0;
    foreach ($porSeguro as $filaSeguro) {
        $etiquetasSeguro[] = $filaSeguro->seguro;
        $idsSeguro[] = $filaSeguro->id_seguro;
        $facturadoSeguro[] = round((float) $filaSeguro->facturado, 2);
        $emitidoSeguro[] = round((float) $filaSeguro->emitido, 2);
        $cotizadoSeguro[] = round((float) $filaSeguro->cotizado, 2);
        $sumaSeguro += (float) $filaSeguro->total;
    }

    $etiquetasTendencia = [];
    $facturadoTendencia = [];
    $emitidoTendencia = [];
    $cotizadoTendencia = [];
    $cantidadFacturadaTendencia = [];
    $cantidadEmitidaTendencia = [];
    $cantidadCotizadaTendencia = [];
    $rangosTendencia = [];
    foreach ($tendencia['filas'] as $filaTendencia) {
        $etiquetasTendencia[] = $filaTendencia->etiqueta;
        $facturadoTendencia[] = round((float) $filaTendencia->facturado, 2);
        $emitidoTendencia[] = round((float) $filaTendencia->emitido, 2);
        $cotizadoTendencia[] = round((float) $filaTendencia->cotizado, 2);
        $cantidadFacturadaTendencia[] = (int) $filaTendencia->facturadas;
        $cantidadEmitidaTendencia[] = (int) $filaTendencia->emitidas;
        $cantidadCotizadaTendencia[] = (int) $filaTendencia->cotizaciones;
        $rangosTendencia[] = ['desde' => $filaTendencia->desde, 'hasta' => $filaTendencia->hasta];
    }
@endphp

<section class="content dashboard-labs">
    <div class="labs-panel labs-intro">
        <div>
            <h2>Desempeño de órdenes de laboratorio</h2>
            <p>Facturación, cotización y conversión por seguro y período.</p>
        </div>
        <span class="labs-rule"><i class="fa fa-check-circle"></i> Regla: facturadas, emitidas y cotizaciones</span>
    </div>

    <div class="labs-panel labs-filter-panel">
        <div class="labs-panel-heading">
            <div>
                <h3>Filtros del análisis</h3>
                <p>Todos los indicadores, gráficos y detalles conservan esta selección.</p>
            </div>
        </div>
        <form method="GET" action="{{ $labsEmbebido ? route('dashboard') : route('dashboard.labs.index') }}" id="labs-filter-form">
            @if($labsEmbebido)
                <input type="hidden" name="tab" value="labs">
            @endif
            <div class="row labs-filter-row">
                <div class="col-md-2 col-sm-4">
                    <label for="fecha_desde">Atención desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ $filtros['fecha_desde'] }}">
                </div>
                <div class="col-md-2 col-sm-4">
                    <label for="fecha_hasta">Atención hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] }}">
                </div>
                <div class="col-md-2 col-sm-4">
                    <label for="seguro">Seguro</label>
                    <select class="form-control" id="seguro" name="seguro">
                        <option value="">Todos los seguros</option>
                        @foreach($seguros as $seguro)
                            <option value="{{ $seguro->id }}" {{ (string) $filtros['seguro'] === (string) $seguro->id ? 'selected' : '' }}>{{ $seguro->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-4">
                    <label for="doctor">Doctor</label>
                    <select class="form-control" id="doctor" name="doctor">
                        <option value="">Todos los doctores</option>
                        @foreach($doctores as $doctor)
                            <option value="{{ $doctor->id }}" {{ (string) $filtros['doctor'] === (string) $doctor->id ? 'selected' : '' }}>
                                {{ trim($doctor->apellido1 . ' ' . $doctor->apellido2 . ' ' . $doctor->nombre1 . ' ' . $doctor->nombre2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-4">
                    <label for="creador">Creador</label>
                    <select class="form-control" id="creador" name="creador">
                        <option value="">Todos los creadores</option>
                        @foreach($creadores as $creador)
                            <option value="{{ $creador->id }}" {{ (string) $filtros['creador'] === (string) $creador->id ? 'selected' : '' }}>
                                {{ trim($creador->apellido1 . ' ' . $creador->apellido2 . ' ' . $creador->nombre1 . ' ' . $creador->nombre2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-4">
                    <label for="estado">Estado</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="" {{ $filtros['estado'] === '' ? 'selected' : '' }}>Todos los estados</option>
                        <option value="facturado" {{ $filtros['estado'] === 'facturado' ? 'selected' : '' }}>Facturadas</option>
                        <option value="emitido" {{ $filtros['estado'] === 'emitido' ? 'selected' : '' }}>Emitidas pendientes de facturar</option>
                        <option value="cotizacion" {{ $filtros['estado'] === 'cotizacion' ? 'selected' : '' }}>Cotizaciones</option>
                    </select>
                </div>
            </div>
            <div class="row labs-filter-actions">
                <div class="col-md-12 labs-actions">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Aplicar</button>
                    <button type="button" class="btn btn-success js-labs-export" data-detail="todos"><i class="fa fa-file-excel-o"></i> Excel/CSV</button>
                    <a href="{{ $labsEmbebido ? route('dashboard', ['tab' => 'labs']) : route('dashboard.labs.index') }}" class="btn btn-default"><i class="fa fa-eraser"></i> Limpiar</a>
                </div>
            </div>
        </form>
        <div class="labs-period">Período de atención: <strong>{{ date('d/m/Y', strtotime($filtros['fecha_desde'])) }}</strong> al <strong>{{ date('d/m/Y', strtotime($filtros['fecha_hasta'])) }}</strong></div>
    </div>

    <div class="row labs-kpi-grid">
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="facturado"><span class="labs-kpi-icon blue"><i class="fa fa-money"></i></span><span class="labs-kpi-label">Total facturado</span><strong>${{ number_format($metricas['total_facturado'], 2) }}</strong><small>Órdenes con número de factura</small></button></div>
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="emitido"><span class="labs-kpi-icon teal"><i class="fa fa-clock-o"></i></span><span class="labs-kpi-label">Total emitido</span><strong>${{ number_format($metricas['total_emitido'], 2) }}</strong><small>Realizadas pendientes de facturar</small></button></div>
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="cotizacion"><span class="labs-kpi-icon orange"><i class="fa fa-file-text-o"></i></span><span class="labs-kpi-label">En cotización</span><strong>${{ number_format($metricas['total_cotizacion'], 2) }}</strong><small>Valor aún no convertido</small></button></div>
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="facturado"><span class="labs-kpi-icon blue"><i class="fa fa-percent"></i></span><span class="labs-kpi-label">% facturado</span><strong>{{ number_format($metricas['porcentaje_facturado'], 2) }}%</strong><small>Sobre el valor potencial</small></button></div>
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="cotizacion"><span class="labs-kpi-icon orange"><i class="fa fa-percent"></i></span><span class="labs-kpi-label">% cotización</span><strong>{{ number_format($metricas['porcentaje_cotizacion'], 2) }}%</strong><small>Complemento de conversión</small></button></div>
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="facturado"><span class="labs-kpi-icon green"><i class="fa fa-flask"></i></span><span class="labs-kpi-label">N.º facturadas</span><strong>{{ number_format($metricas['numero_facturadas']) }}</strong><small>Con número de factura</small></button></div>
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="emitido"><span class="labs-kpi-icon teal"><i class="fa fa-files-o"></i></span><span class="labs-kpi-label">N.º emitidas</span><strong>{{ number_format($metricas['numero_emitidas']) }}</strong><small>Pendientes de facturar</small></button></div>
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="cotizacion"><span class="labs-kpi-icon orange"><i class="fa fa-files-o"></i></span><span class="labs-kpi-label">N.º cotizaciones</span><strong>{{ number_format($metricas['numero_cotizaciones']) }}</strong><small>Órdenes distintas</small></button></div>
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="todos"><span class="labs-kpi-icon navy"><i class="fa fa-line-chart"></i></span><span class="labs-kpi-label">Valor potencial</span><strong>${{ number_format($metricas['total_general'], 2) }}</strong><small>Facturado + cotizado</small></button></div>
        <div class="col-lg-3 col-md-4 col-sm-6"><button class="labs-kpi js-labs-detail" data-detail="facturado"><span class="labs-kpi-icon green"><i class="fa fa-calculator"></i></span><span class="labs-kpi-label">Ticket promedio</span><strong>${{ number_format($metricas['ticket_promedio'], 2) }}</strong><small>Por orden facturada</small></button></div>
    </div>

    <button type="button" class="labs-quality {{ $inconsistencias > 0 ? 'has-issues' : 'is-ok' }} js-labs-detail" data-detail="inconsistencias">
        <span><i class="fa {{ $inconsistencias > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle' }}"></i> Control de calidad</span>
        <strong>{{ number_format($inconsistencias) }} {{ $inconsistencias === 1 ? 'orden requiere' : 'órdenes requieren' }} revisión</strong>
        <small>Factura, estado, valores, duplicidad y campos obligatorios <i class="fa fa-chevron-right"></i></small>
    </button>

    <div class="row labs-visual-row">
        <div class="col-md-5">
            <div class="labs-panel labs-chart-panel">
                <div class="labs-panel-heading"><div><h3>Facturado, emitido y cotización</h3><p>Participación sobre el total monitoreado.</p></div></div>
                <div class="labs-donut-wrap"><canvas id="labs-status-chart" role="img" aria-label="Gráfico de participación entre valor facturado, emitido y cotizado"></canvas></div>
                <div class="labs-chart-legend">
                    <button class="js-labs-detail" data-detail="facturado"><span class="dot blue"></span> Facturado <strong>${{ number_format($metricas['total_facturado'], 2) }} ({{ number_format($metricas['porcentaje_visual_facturado'], 2) }}%)</strong></button>
                    <button class="js-labs-detail" data-detail="emitido"><span class="dot teal"></span> Emitido <strong>${{ number_format($metricas['total_emitido'], 2) }} ({{ number_format($metricas['porcentaje_visual_emitido'], 2) }}%)</strong></button>
                    <button class="js-labs-detail" data-detail="cotizacion"><span class="dot orange"></span> Cotización <strong>${{ number_format($metricas['total_cotizacion'], 2) }} ({{ number_format($metricas['porcentaje_visual_cotizacion'], 2) }}%)</strong></button>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="labs-panel labs-chart-panel">
                <div class="labs-panel-heading"><div><h3>Conciliación del período</h3><p>Verificación entre KPI, estados y agrupación por seguro.</p></div></div>
                <div class="table-responsive">
                    <table class="table labs-reconcile-table"><tbody>
                        <tr><td>Total facturado</td><td class="text-right">${{ number_format($metricas['total_facturado'], 2) }}</td></tr>
                        <tr><td>Emitido pendiente de facturar</td><td class="text-right">${{ number_format($metricas['total_emitido'], 2) }}</td></tr>
                        <tr><td>En cotización</td><td class="text-right">${{ number_format($metricas['total_cotizacion'], 2) }}</td></tr>
                        <tr class="total"><td>Total monitoreado</td><td class="text-right">${{ number_format($metricas['total_monitoreado'], 2) }}</td></tr>
                        <tr><td>Suma del gráfico por seguro</td><td class="text-right">${{ number_format($sumaSeguro, 2) }}</td></tr>
                    </tbody></table>
                </div>
                <div class="labs-reconcile-status {{ abs($metricas['total_monitoreado'] - $sumaSeguro) < 0.02 ? 'ok' : 'warning' }}">
                    <i class="fa {{ abs($metricas['total_monitoreado'] - $sumaSeguro) < 0.02 ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                    {{ abs($metricas['total_monitoreado'] - $sumaSeguro) < 0.02 ? 'Los valores concilian correctamente.' : 'Existe una diferencia que debe revisarse.' }}
                    <span>Participación gráfica: {{ number_format($metricas['porcentaje_visual_facturado'] + $metricas['porcentaje_visual_emitido'] + $metricas['porcentaje_visual_cotizacion'], 2) }}%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="labs-panel labs-chart-panel">
        <div class="labs-panel-heading"><div><h3>Valores por seguro</h3><p>Facturado, emitido y cotizado, ordenado por valor total descendente. Seleccione una barra para ver sus órdenes.</p></div></div>
        <div class="labs-wide-chart"><canvas id="labs-insurance-chart" role="img" aria-label="Gráfico de valores facturados, emitidos y cotizados por seguro"></canvas></div>
    </div>

    <div class="labs-panel labs-chart-panel">
        <div class="labs-panel-heading"><div><h3>{{ count($tendencia['filas']) > 1 ? 'Tendencia ' . $tendencia['escala'] : 'Resumen del período' }}</h3><p>Valores y número de órdenes según la amplitud del período.</p></div></div>
        <div class="labs-wide-chart"><canvas id="labs-trend-chart" role="img" aria-label="Gráfico de tendencia de valores y cantidades de órdenes"></canvas></div>
    </div>

    <div class="labs-panel">
        <div class="labs-panel-heading"><div><h3>Desempeño por seguro</h3><p>Todos los seguros con órdenes facturadas, emitidas o cotizadas en el período.</p></div></div>
        <div class="table-responsive">
            <table class="table table-hover labs-opportunity-table">
                <thead><tr><th>Seguro</th><th class="text-right">Facturado</th><th class="text-right">Emitido</th><th class="text-right">Cotizado</th><th class="text-right">Conversión</th><th class="text-right">Volumen</th><th></th></tr></thead>
                <tbody>
                @forelse($oportunidades as $oportunidad)
                    <tr>
                        <td>{{ $oportunidad->seguro }}</td>
                        <td class="text-right">${{ number_format($oportunidad->facturado, 2) }}</td>
                        <td class="text-right">${{ number_format($oportunidad->emitido, 2) }}</td>
                        <td class="text-right">${{ number_format($oportunidad->cotizado, 2) }}</td>
                        <td class="text-right"><span class="labs-conversion {{ $oportunidad->conversion < 50 ? 'low' : '' }}">{{ number_format($oportunidad->conversion, 2) }}%</span></td>
                        <td class="text-right">{{ number_format($oportunidad->volumen) }}</td>
                        <td class="text-right"><button class="btn btn-xs btn-default js-labs-detail" data-detail="todos" data-insurance="{{ $oportunidad->id_seguro }}" data-no-insurance="{{ is_null($oportunidad->id_seguro) ? '1' : '0' }}">Ver órdenes</button></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="labs-empty">No hay seguros con actividad en el período.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="modal fade fullscreen-modal" id="labs-detail-modal" tabindex="-1" role="dialog" aria-labelledby="labs-detail-title">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="labs-detail-title">Detalle de órdenes</h4></div>
            <div class="modal-body" id="labs-detail-content"><div class="labs-loading"><i class="fa fa-spinner fa-spin"></i> Cargando detalle...</div></div>
        </div>
    </div>
</div>

@if(!request()->ajax())
<script src="{{ asset('/hc4/js/chart.min.js') }}"></script>
@endif
<script>
window.dashboardLabsConfig = {
    detailUrl: {!! json_encode(route('dashboard.labs.detalle')) !!},
    exportUrl: {!! json_encode(route('dashboard.labs.exportar')) !!},
    status: { values: {!! json_encode([$metricas['total_facturado'], $metricas['total_emitido'], $metricas['total_cotizacion']]) !!} },
    insurance: {
        labels: {!! json_encode($etiquetasSeguro) !!}, ids: {!! json_encode($idsSeguro) !!},
        billed: {!! json_encode($facturadoSeguro) !!}, issued: {!! json_encode($emitidoSeguro) !!}, quoted: {!! json_encode($cotizadoSeguro) !!}
    },
    trend: {
        labels: {!! json_encode($etiquetasTendencia) !!}, billed: {!! json_encode($facturadoTendencia) !!},
        issued: {!! json_encode($emitidoTendencia) !!}, quoted: {!! json_encode($cotizadoTendencia) !!},
        billedCount: {!! json_encode($cantidadFacturadaTendencia) !!}, issuedCount: {!! json_encode($cantidadEmitidaTendencia) !!},
        quotedCount: {!! json_encode($cantidadCotizadaTendencia) !!}, ranges: {!! json_encode($rangosTendencia) !!}
    }
};
</script>
<script src="{{ asset('/js/dashboard-labs.js') }}?v=20260805-6"></script>

@endsection
