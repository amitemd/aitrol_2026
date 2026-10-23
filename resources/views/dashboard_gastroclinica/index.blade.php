<style>
    .gastro-dashboard {
        --gastro-blue: #1562ad;
        --gastro-blue-soft: #eaf3fb;
        --gastro-green: #159447;
        --gastro-red: #d64545;
        --gastro-border: #dce4ec;
        font-family: 'Poppins', sans-serif;
    }

    .gastro-dashboard .content-header h1 {
        color: #1f2937;
        font-weight: 600;
    }

    .gastro-dashboard .dashboard-intro {
        color: #64748b;
        margin-top: 6px;
    }

    .gastro-card {
        background: #fff;
        border: 1px solid var(--gastro-border);
        border-radius: 10px;
        box-shadow: 0 3px 12px rgba(15, 23, 42, .06);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .gastro-card__header {
        border-bottom: 1px solid var(--gastro-border);
        padding: 18px 20px 14px;
    }

    .gastro-card__header h2 {
        color: #1f2937;
        font-size: 19px;
        font-weight: 600;
        margin: 0 0 5px;
    }

    .gastro-card__header p {
        color: #64748b;
        margin: 0;
    }

    .gastro-card__body {
        padding: 20px;
    }

    .gastro-filter {
        align-items: flex-end;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .gastro-filter .form-group {
        margin-bottom: 0;
        min-width: 190px;
    }

    .gastro-filter .form-group--insurance {
        min-width: 250px;
    }

    .gastro-filter label {
        color: #334155;
        display: block;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .gastro-filter .form-control {
        border-color: #cbd5e1;
        border-radius: 6px;
        height: 36px;
    }

    .gastro-actions {
        display: flex;
        gap: 8px;
    }

    .gastro-actions .btn {
        border-radius: 6px;
        font-weight: 600;
        min-width: 105px;
    }

    .gastro-actions .btn-primary {
        background: var(--gastro-blue);
        border-color: var(--gastro-blue);
    }

    .gastro-actions .btn-success {
        background: #00a65a;
        border-color: #008d4c;
    }

    .gastro-results {
        border-top: 1px solid var(--gastro-border);
        margin-top: 20px;
        padding-top: 18px;
    }

    .gastro-results-grid {
        align-items: stretch;
        display: grid;
        gap: 18px;
        grid-template-columns: minmax(340px, 5fr) minmax(480px, 7fr);
    }

    .gastro-chart {
        border: 1px solid var(--gastro-border);
        border-radius: 8px;
        height: 310px;
        min-width: 0;
        overflow: hidden;
    }

    .gastro-chart-wrap {
        min-width: 0;
        position: relative;
    }

    .gastro-chart-menu {
        position: absolute;
        right: 9px;
        top: 8px;
        z-index: 4;
    }

    .gastro-chart-switch {
        align-items: center;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        color: var(--gastro-blue);
        display: flex;
        height: 32px;
        justify-content: center;
        padding: 0;
        transition: background .15s ease, color .15s ease;
        width: 32px;
    }

    .gastro-chart-switch:hover,
    .gastro-chart-switch:focus {
        background: var(--gastro-blue);
        color: #fff;
        outline: none;
    }

    .gastro-chart-menu .dropdown-menu {
        border-color: #cbd5e1;
        border-radius: 7px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .16);
        left: auto;
        min-width: 190px;
        padding: 5px;
        right: 0;
    }

    .gastro-chart-option {
        background: transparent;
        border: 0;
        border-radius: 5px;
        color: #334155;
        display: block;
        padding: 8px 10px;
        text-align: left;
        width: 100%;
    }

    .gastro-chart-option i {
        color: #64748b;
        margin-right: 9px;
        text-align: center;
        width: 15px;
    }

    .gastro-chart-menu .dropdown-menu > li > .gastro-chart-option:hover,
    .gastro-chart-menu .dropdown-menu > li > .gastro-chart-option:focus,
    .gastro-chart-menu .dropdown-menu > li.active > .gastro-chart-option {
        background: var(--gastro-blue-soft);
        color: var(--gastro-blue);
        outline: none;
    }

    .gastro-chart-menu .dropdown-menu > li.active > .gastro-chart-option {
        font-weight: 600;
    }

    .gastro-period {
        color: #64748b;
        font-size: 12px;
        margin-bottom: 8px;
    }

    .gastro-table {
        margin-bottom: 0;
    }

    .gastro-table > thead > tr > th,
    .gastro-table > tfoot > tr > th {
        background: #f5f8fb;
        border-color: var(--gastro-border);
        color: #334155;
        font-size: 12px;
        text-transform: uppercase;
        vertical-align: middle;
    }

    .gastro-table > tbody > tr > td {
        border-color: var(--gastro-border);
        vertical-align: middle;
    }

    .gastro-table .text-money {
        font-variant-numeric: tabular-nums;
        text-align: right;
        white-space: nowrap;
    }

    .gastro-table .text-count {
        font-variant-numeric: tabular-nums;
        text-align: center;
    }

    .gastro-service-link,
    .gastro-coverage-link {
        background: transparent;
        border: 0;
        color: var(--gastro-blue);
        font-weight: 600;
        padding: 0;
        text-align: left;
        text-decoration: underline;
    }

    .demand-alta td:first-child {
        border-left: 5px solid var(--gastro-green) !important;
    }

    .demand-alta {
        background: #f0faf4;
    }

    .demand-alta .gastro-service-link {
        color: #13783c;
    }

    .demand-baja td:first-child {
        border-left: 5px solid var(--gastro-red) !important;
    }

    .demand-baja {
        background: #fff2f2;
    }

    .demand-baja .gastro-service-link {
        color: #b42323;
    }

    .demand-neutral td:first-child {
        border-left: 5px solid #94a3b8 !important;
    }

    .demand-badge {
        border-radius: 20px;
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
        padding: 3px 8px;
    }

    .demand-badge--alta {
        background: #e7f7ed;
        color: #13783c;
    }

    .demand-badge--baja {
        background: #fdecec;
        color: #b42323;
    }

    .demand-badge--neutral {
        background: #eef2f6;
        color: #64748b;
    }

    .gastro-state {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        color: #64748b;
        padding: 24px;
        text-align: center;
    }

    .gastro-error {
        background: #fff1f1;
        border: 1px solid #f1b9b9;
        border-radius: 6px;
        color: #a52626;
        display: none;
        margin-top: 14px;
        padding: 10px 12px;
    }

    .gastro-modal-total {
        color: #334155;
        font-weight: 600;
    }

    @media (max-width: 767px) {
        .gastro-results-grid {
            display: block;
        }

        .gastro-chart {
            margin-bottom: 16px;
        }

        .gastro-filter,
        .gastro-actions {
            display: block;
        }

        .gastro-filter .form-group,
        .gastro-filter .form-group--insurance,
        .gastro-actions .btn {
            margin-bottom: 10px;
            width: 100%;
        }
    }
</style>

<div class="gastro-dashboard">
    <section class="content-header">
        <h1><i class="fa fa-line-chart"></i> Dashboard Gastroclínica</h1>
        <p class="dashboard-intro">
            Rotación y ventas de servicios facturados según su fecha de atención.
        </p>
    </section>

    <section class="content">
        <article class="gastro-card">
            <header class="gastro-card__header">
                <h2>Análisis de rotación de servicios</h2>
                <p>Compara la demanda facturada de procedimientos, imágenes y consultas.</p>
            </header>
            <div class="gastro-card__body">
                <form id="form-rotacion" class="gastro-filter">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="rotacion-fecha-desde">Atención desde</label>
                        <input id="rotacion-fecha-desde" class="form-control" type="date"
                               name="fecha_desde" value="{{ $fechaDesde }}" required>
                    </div>
                    <div class="form-group">
                        <label for="rotacion-fecha-hasta">Atención hasta</label>
                        <input id="rotacion-fecha-hasta" class="form-control" type="date"
                               name="fecha_hasta" value="{{ $fechaHasta }}" required>
                    </div>
                    <div class="form-group form-group--insurance">
                        <label for="rotacion-seguro">Seguro</label>
                        <select id="rotacion-seguro" class="form-control" name="id_seguro">
                            <option value="">Todos los seguros</option>
                            @foreach($seguros as $seguro)
                                <option value="{{ $seguro->id }}">{{ $seguro->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="gastro-actions">
                        <button type="submit" class="btn btn-primary" id="aplicar-rotacion">
                            <i class="fa fa-filter"></i> Aplicar
                        </button>
                        <button type="button" class="btn btn-success" id="excel-rotacion" disabled>
                            <i class="fa fa-file-excel-o"></i> Excel
                        </button>
                    </div>
                </form>

                <div id="error-rotacion" class="gastro-error" role="alert"></div>
                <div id="estado-rotacion" class="gastro-state" style="margin-top: 20px;">
                    <i class="fa fa-spinner fa-spin"></i> Cargando información de los últimos 12 meses...
                </div>
                <div id="resultado-rotacion" class="gastro-results" style="display: none;">
                    <div id="periodo-rotacion" class="gastro-period"></div>
                    <div class="gastro-results-grid">
                        <div class="gastro-chart-wrap">
                            <div id="menu-grafico-rotacion" class="dropdown gastro-chart-menu">
                                <button type="button" id="cambiar-grafico-rotacion" class="gastro-chart-switch"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        title="Seleccionar tipo de gráfico" aria-label="Seleccionar tipo de gráfico de rotación">
                                    <i class="fa fa-pie-chart" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="cambiar-grafico-rotacion">
                                    <li><button type="button" class="gastro-chart-option" data-tipo="combinado"><i class="fa fa-line-chart"></i> Combinado</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="columnas"><i class="fa fa-bar-chart"></i> Columnas</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="barras"><i class="fa fa-bars"></i> Barras</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="lineas"><i class="fa fa-line-chart"></i> Líneas</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="pastel"><i class="fa fa-pie-chart"></i> Pastel</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="dona"><i class="fa fa-circle-o-notch"></i> Dona</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="dispersion"><i class="fa fa-dot-circle-o"></i> Dispersión</button></li>
                                </ul>
                            </div>
                            <div id="grafico-rotacion" class="gastro-chart" aria-label="Gráfico de rotación de servicios"></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover gastro-table">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th class="text-count">Facturas</th>
                                        <th class="text-money">Valor total</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-rotacion"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <article class="gastro-card">
            <header class="gastro-card__header">
                <h2>Análisis de ventas por particular, aseguradoras y público</h2>
                <p>Compara el valor facturado de cada servicio por tipo de cobertura.</p>
            </header>
            <div class="gastro-card__body">
                <form id="form-ventas" class="gastro-filter">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="ventas-fecha-desde">Atención desde</label>
                        <input id="ventas-fecha-desde" class="form-control" type="date"
                               name="fecha_desde" value="{{ $fechaDesde }}" required>
                    </div>
                    <div class="form-group">
                        <label for="ventas-fecha-hasta">Atención hasta</label>
                        <input id="ventas-fecha-hasta" class="form-control" type="date"
                               name="fecha_hasta" value="{{ $fechaHasta }}" required>
                    </div>
                    <div class="gastro-actions">
                        <button type="submit" class="btn btn-primary" id="aplicar-ventas">
                            <i class="fa fa-filter"></i> Aplicar
                        </button>
                        <button type="button" class="btn btn-success" id="excel-ventas" disabled>
                            <i class="fa fa-file-excel-o"></i> Excel
                        </button>
                    </div>
                </form>

                <div id="error-ventas" class="gastro-error" role="alert"></div>
                <div id="estado-ventas" class="gastro-state" style="margin-top: 20px;">
                    <i class="fa fa-spinner fa-spin"></i> Cargando información de los últimos 12 meses...
                </div>
                <div id="resultado-ventas" class="gastro-results" style="display: none;">
                    <div id="periodo-ventas" class="gastro-period"></div>
                    <div class="gastro-results-grid">
                        <div class="gastro-chart-wrap">
                            <div id="menu-grafico-ventas" class="dropdown gastro-chart-menu">
                                <button type="button" id="cambiar-grafico-ventas" class="gastro-chart-switch"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        title="Seleccionar tipo de gráfico" aria-label="Seleccionar tipo de gráfico de ventas">
                                    <i class="fa fa-pie-chart" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="cambiar-grafico-ventas">
                                    <li><button type="button" class="gastro-chart-option" data-tipo="columnas"><i class="fa fa-bar-chart"></i> Columnas</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="barras"><i class="fa fa-bars"></i> Barras</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="lineas"><i class="fa fa-line-chart"></i> Líneas</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="area"><i class="fa fa-area-chart"></i> Área</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="pastel"><i class="fa fa-pie-chart"></i> Pastel</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="dona"><i class="fa fa-circle-o-notch"></i> Dona</button></li>
                                    <li><button type="button" class="gastro-chart-option" data-tipo="dispersion"><i class="fa fa-dot-circle-o"></i> Dispersión</button></li>
                                </ul>
                            </div>
                            <div id="grafico-ventas" class="gastro-chart" aria-label="Gráfico de ventas por cobertura"></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover gastro-table">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th class="text-money">Procedimientos</th>
                                        <th class="text-money">Imágenes</th>
                                        <th class="text-money">Consultas</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-ventas"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>
</div>

<div class="modal fade" id="modal-servicio" tabindex="-1" role="dialog" aria-labelledby="modal-servicio-titulo">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modal-servicio-titulo"></h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover gastro-table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th class="text-count">Facturas</th>
                                <th class="text-money">Valor</th>
                            </tr>
                        </thead>
                        <tbody id="modal-servicio-tabla"></tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th class="text-count" id="modal-servicio-facturas"></th>
                                <th class="text-money" id="modal-servicio-total"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-aseguradoras" tabindex="-1" role="dialog" aria-labelledby="modal-aseguradoras-titulo">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modal-aseguradoras-titulo"></h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover gastro-table">
                        <thead>
                            <tr>
                                <th>Aseguradora</th>
                                <th class="text-money">Procedimientos</th>
                                <th class="text-money">Imágenes</th>
                                <th class="text-money">Consultas</th>
                            </tr>
                        </thead>
                        <tbody id="modal-aseguradoras-tabla"></tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th class="text-money" id="modal-total-procedimientos"></th>
                                <th class="text-money" id="modal-total-imagenes"></th>
                                <th class="text-money" id="modal-total-consultas"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/highcharts.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    (function ($) {
        'use strict';

        var urls = {
            rotacion: '{{ route('dashboard.gastroclinica.rotacion') }}',
            ventas: '{{ route('dashboard.gastroclinica.ventas') }}',
            excelRotacion: '{{ route('dashboard.gastroclinica.exportar', ['analisis' => 'rotacion']) }}',
            excelVentas: '{{ route('dashboard.gastroclinica.exportar', ['analisis' => 'ventas']) }}'
        };

        var rotacionActual = null;
        var ventasActuales = null;
        var graficoRotacion = null;
        var graficoVentas = null;
        var tipoGraficoRotacion = 'combinado';
        var tipoGraficoVentas = 'columnas';
        var coloresGraficos = ['#1562ad', '#f59e0b', '#159447', '#8b5cf6', '#ef4444'];

        function escapar(valor) {
            return String(valor === null || valor === undefined ? '' : valor)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function moneda(valor) {
            var numero = parseFloat(valor || 0);
            return '$' + numero.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function entero(valor) {
            return parseInt(valor || 0, 10).toLocaleString('en-US');
        }

        function periodo(datos) {
            return 'Período de atención: ' + escapar(datos.periodo.desde) + ' al ' + escapar(datos.periodo.hasta);
        }

        function mensajeError(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.mensaje) {
                return xhr.responseJSON.mensaje;
            }
            return 'No fue posible cargar la información. Intenta nuevamente.';
        }

        function seleccionarTipoEnMenu(idMenu, idBoton, tipo, descripcion) {
            var $menu = $(idMenu);
            var $opcion = $menu.find('.gastro-chart-option[data-tipo="' + tipo + '"]');
            var nombre = $.trim($opcion.text()).toLowerCase();

            $menu.find('li').removeClass('active');
            $opcion.closest('li').addClass('active');
            $(idBoton).attr({
                title: 'Vista actual: ' + nombre + '. Seleccionar otro tipo de gráfico',
                'aria-label': 'Vista actual ' + nombre + '. Seleccionar tipo de gráfico de ' + descripcion
            });
        }

        function crearGraficoRotacion(datos) {
            var categorias = [];
            var facturas = [];
            var valores = [];
            var puntosPastel = [];
            var puntosDispersion = [];
            var configuraciones = {
                combinado: { facturas: 'column', valores: 'spline' },
                columnas: { facturas: 'column', valores: 'column' },
                barras: { facturas: 'bar', valores: 'bar' },
                lineas: { facturas: 'spline', valores: 'spline' }
            };

            $.each(datos.resumen, function (indice, fila) {
                var cantidad = parseInt(fila.facturas || 0, 10);
                var valor = parseFloat(fila.valor_total || 0);
                categorias.push(fila.servicio);
                facturas.push(cantidad);
                valores.push(valor);
                puntosPastel.push({ name: fila.servicio, y: valor, color: coloresGraficos[indice] });
                puntosDispersion.push({
                    name: fila.servicio,
                    x: cantidad,
                    y: valor,
                    color: coloresGraficos[indice]
                });
            });

            if (graficoRotacion) {
                graficoRotacion.destroy();
            }

            var opciones = {
                chart: { renderTo: 'grafico-rotacion', backgroundColor: 'transparent' },
                title: { text: 'Rotación de servicios', style: { fontSize: '15px' } },
                credits: { enabled: false }
            };

            if (tipoGraficoRotacion === 'pastel' || tipoGraficoRotacion === 'dona') {
                opciones.chart.type = 'pie';
                opciones.title.text = 'Participación del valor facturado';
                opciones.tooltip = {
                    formatter: function () {
                        return '<strong>' + escapar(this.point.name) + '</strong><br>' +
                            'Valor: <strong>' + moneda(this.y) + '</strong><br>' +
                            'Participación: ' + Highcharts.numberFormat(this.percentage || 0, 1) + '%';
                    }
                };
                opciones.plotOptions = {
                    pie: {
                        allowPointSelect: true,
                        dataLabels: {
                            enabled: true,
                            formatter: function () {
                                return escapar(this.point.name) + '<br>' +
                                    Highcharts.numberFormat(this.percentage || 0, 1) + '%';
                            }
                        }
                    }
                };
                opciones.series = [{
                    name: 'Valor facturado',
                    colorByPoint: true,
                    innerSize: tipoGraficoRotacion === 'dona' ? '55%' : '0%',
                    data: puntosPastel
                }];
            } else if (tipoGraficoRotacion === 'dispersion') {
                opciones.chart.type = 'scatter';
                opciones.title.text = 'Facturas vs. valor facturado';
                opciones.legend = { enabled: false };
                opciones.xAxis = {
                    min: 0,
                    allowDecimals: false,
                    title: { text: 'Facturas' }
                };
                opciones.yAxis = {
                    min: 0,
                    title: { text: 'Valor facturado' },
                    labels: { formatter: function () { return moneda(this.value); } }
                };
                opciones.tooltip = {
                    formatter: function () {
                        return '<strong>' + escapar(this.point.name) + '</strong><br>' +
                            'Facturas: <strong>' + entero(this.x) + '</strong><br>' +
                            'Valor: <strong>' + moneda(this.y) + '</strong>';
                    }
                };
                opciones.plotOptions = { scatter: { marker: { radius: 7 } } };
                opciones.series = [{ name: 'Servicios', data: puntosDispersion }];
            } else {
                var configuracion = configuraciones[tipoGraficoRotacion] || configuraciones.combinado;
                opciones.xAxis = { categories: categorias };
                opciones.yAxis = [{
                    min: 0,
                    allowDecimals: false,
                    title: { text: 'Facturas' }
                }, {
                    min: 0,
                    opposite: true,
                    title: { text: 'Valor facturado' },
                    labels: { formatter: function () { return moneda(this.value); } }
                }];
                opciones.tooltip = {
                    shared: true,
                    formatter: function () {
                        var contenido = '<strong>' + escapar(this.x) + '</strong>';
                        $.each(this.points || [], function (_, punto) {
                            var valor = punto.series.name === 'Valor facturado' ? moneda(punto.y) : entero(punto.y);
                            contenido += '<br><span style="color:' + punto.color + '">●</span> ' +
                                escapar(punto.series.name) + ': <strong>' + valor + '</strong>';
                        });
                        return contenido;
                    }
                };
                opciones.series = [{
                    name: 'Facturas',
                    type: configuracion.facturas,
                    color: '#1562ad',
                    data: facturas
                }, {
                    name: 'Valor facturado',
                    type: configuracion.valores,
                    color: '#159447',
                    yAxis: 1,
                    data: valores
                }];
            }

            graficoRotacion = new Highcharts.Chart(opciones);
            seleccionarTipoEnMenu(
                '#menu-grafico-rotacion',
                '#cambiar-grafico-rotacion',
                tipoGraficoRotacion,
                'rotación'
            );
        }

        function crearGraficoVentas(datos) {
            var categorias = [];
            var procedimientos = [];
            var imagenes = [];
            var consultas = [];
            var puntosPastel = [];
            var puntosDispersion = [];
            var tiposSeries = {
                columnas: 'column',
                barras: 'bar',
                lineas: 'spline',
                area: 'area'
            };

            $.each(datos.resumen, function (indice, fila) {
                var valorProcedimientos = parseFloat(fila.procedimientos || 0);
                var valorImagenes = parseFloat(fila.imagenes || 0);
                var valorConsultas = parseFloat(fila.consultas || 0);
                categorias.push(fila.cobertura);
                procedimientos.push(valorProcedimientos);
                imagenes.push(valorImagenes);
                consultas.push(valorConsultas);
                puntosPastel.push({
                    name: fila.cobertura,
                    y: valorProcedimientos + valorImagenes + valorConsultas,
                    color: coloresGraficos[indice]
                });
                puntosDispersion.push({
                    name: fila.cobertura,
                    x: valorProcedimientos,
                    y: valorImagenes + valorConsultas,
                    color: coloresGraficos[indice]
                });
            });

            if (graficoVentas) {
                graficoVentas.destroy();
            }

            var opciones = {
                chart: { renderTo: 'grafico-ventas', backgroundColor: 'transparent' },
                title: { text: 'Ventas por cobertura', style: { fontSize: '15px' } },
                credits: { enabled: false }
            };

            if (tipoGraficoVentas === 'pastel' || tipoGraficoVentas === 'dona') {
                opciones.chart.type = 'pie';
                opciones.title.text = 'Participación de ventas por cobertura';
                opciones.tooltip = {
                    formatter: function () {
                        return '<strong>' + escapar(this.point.name) + '</strong><br>' +
                            'Total: <strong>' + moneda(this.y) + '</strong><br>' +
                            'Participación: ' + Highcharts.numberFormat(this.percentage || 0, 1) + '%';
                    }
                };
                opciones.plotOptions = {
                    pie: {
                        allowPointSelect: true,
                        dataLabels: {
                            enabled: true,
                            formatter: function () {
                                return escapar(this.point.name) + '<br>' +
                                    Highcharts.numberFormat(this.percentage || 0, 1) + '%';
                            }
                        }
                    }
                };
                opciones.series = [{
                    name: 'Ventas',
                    colorByPoint: true,
                    innerSize: tipoGraficoVentas === 'dona' ? '55%' : '0%',
                    data: puntosPastel
                }];
            } else if (tipoGraficoVentas === 'dispersion') {
                opciones.chart.type = 'scatter';
                opciones.title.text = 'Procedimientos vs. otros servicios';
                opciones.legend = { enabled: false };
                opciones.xAxis = {
                    min: 0,
                    title: { text: 'Procedimientos' },
                    labels: { formatter: function () { return moneda(this.value); } }
                };
                opciones.yAxis = {
                    min: 0,
                    title: { text: 'Imágenes + consultas' },
                    labels: { formatter: function () { return moneda(this.value); } }
                };
                opciones.tooltip = {
                    formatter: function () {
                        return '<strong>' + escapar(this.point.name) + '</strong><br>' +
                            'Procedimientos: <strong>' + moneda(this.x) + '</strong><br>' +
                            'Imágenes + consultas: <strong>' + moneda(this.y) + '</strong>';
                    }
                };
                opciones.plotOptions = { scatter: { marker: { radius: 7 } } };
                opciones.series = [{ name: 'Coberturas', data: puntosDispersion }];
            } else {
                var tipoSerie = tiposSeries[tipoGraficoVentas] || tiposSeries.columnas;
                opciones.xAxis = { categories: categorias };
                opciones.yAxis = {
                    min: 0,
                    title: { text: 'Valor facturado' },
                    labels: { formatter: function () { return moneda(this.value); } }
                };
                opciones.tooltip = {
                    shared: true,
                    valuePrefix: '$',
                    valueDecimals: 2
                };
                opciones.plotOptions = {
                    column: { borderWidth: 0, groupPadding: 0.12 },
                    bar: { borderWidth: 0, groupPadding: 0.12 },
                    area: { fillOpacity: 0.2 }
                };
                opciones.series = [{
                    name: 'Procedimientos',
                    type: tipoSerie,
                    color: '#1562ad',
                    data: procedimientos
                }, {
                    name: 'Imágenes',
                    type: tipoSerie,
                    color: '#f59e0b',
                    data: imagenes
                }, {
                    name: 'Consultas',
                    type: tipoSerie,
                    color: '#159447',
                    data: consultas
                }];
            }

            graficoVentas = new Highcharts.Chart(opciones);
            seleccionarTipoEnMenu(
                '#menu-grafico-ventas',
                '#cambiar-grafico-ventas',
                tipoGraficoVentas,
                'ventas'
            );
        }

        function cargarRotacion() {
            var $boton = $('#aplicar-rotacion');
            $('#error-rotacion').hide().empty();
            $('#resultado-rotacion').hide();
            $('#estado-rotacion').show().html('<i class="fa fa-spinner fa-spin"></i> Consultando facturas...');
            $boton.prop('disabled', true);
            $('#excel-rotacion').prop('disabled', true);

            $.ajax({
                url: urls.rotacion,
                method: 'POST',
                data: $('#form-rotacion').serialize(),
                dataType: 'json'
            }).done(function (datos) {
                rotacionActual = datos;
                var html = '';

                $.each(datos.resumen, function (_, fila) {
                    var nivel = fila.nivel_demanda || 'neutral';
                    var etiqueta = nivel === 'alta' ? 'Mayor demanda' : (nivel === 'baja' ? 'Menor demanda' : 'Demanda intermedia');
                    html += '<tr class="demand-' + escapar(nivel) + '">' +
                        '<td><button type="button" class="gastro-service-link" data-servicio="' + escapar(fila.clave) + '">' +
                            escapar(fila.servicio) + '</button>' +
                            '<span class="demand-badge demand-badge--' + escapar(nivel) + '">' + escapar(etiqueta) + '</span></td>' +
                        '<td class="text-count">' + entero(fila.facturas) + '</td>' +
                        '<td class="text-money">' + moneda(fila.valor_total) + '</td>' +
                    '</tr>';
                });

                $('#tabla-rotacion').html(html);
                $('#periodo-rotacion').html(periodo(datos));
                $('#estado-rotacion').hide();
                $('#resultado-rotacion').show();
                crearGraficoRotacion(datos);
                $('#excel-rotacion').prop('disabled', false);
            }).fail(function (xhr) {
                $('#estado-rotacion').hide();
                $('#error-rotacion').text(mensajeError(xhr)).show();
            }).always(function () {
                $boton.prop('disabled', false);
            });
        }

        function cargarVentas() {
            var $boton = $('#aplicar-ventas');
            $('#error-ventas').hide().empty();
            $('#resultado-ventas').hide();
            $('#estado-ventas').show().html('<i class="fa fa-spinner fa-spin"></i> Consultando ventas...');
            $boton.prop('disabled', true);
            $('#excel-ventas').prop('disabled', true);

            $.ajax({
                url: urls.ventas,
                method: 'POST',
                data: $('#form-ventas').serialize(),
                dataType: 'json'
            }).done(function (datos) {
                ventasActuales = datos;
                var html = '';

                $.each(datos.resumen, function (_, fila) {
                    var nombre = escapar(fila.cobertura);
                    if (fila.clave === 'privado' || fila.clave === 'publico') {
                        nombre = '<button type="button" class="gastro-coverage-link" data-cobertura="' +
                            escapar(fila.clave) + '">' + nombre + '</button>';
                    }

                    html += '<tr>' +
                        '<td>' + nombre + '</td>' +
                        '<td class="text-money">' + moneda(fila.procedimientos) + '</td>' +
                        '<td class="text-money">' + moneda(fila.imagenes) + '</td>' +
                        '<td class="text-money">' + moneda(fila.consultas) + '</td>' +
                    '</tr>';
                });

                $('#tabla-ventas').html(html);
                $('#periodo-ventas').html(periodo(datos));
                $('#estado-ventas').hide();
                $('#resultado-ventas').show();
                crearGraficoVentas(datos);
                $('#excel-ventas').prop('disabled', false);
            }).fail(function (xhr) {
                $('#estado-ventas').hide();
                $('#error-ventas').text(mensajeError(xhr)).show();
            }).always(function () {
                $boton.prop('disabled', false);
            });
        }

        $('#form-rotacion').on('submit', function (evento) {
            evento.preventDefault();
            cargarRotacion();
        });

        $('#form-ventas').on('submit', function (evento) {
            evento.preventDefault();
            cargarVentas();
        });

        $('#form-rotacion :input').on('change', function () {
            $('#excel-rotacion').prop('disabled', true);
        });

        $('#form-ventas :input').on('change', function () {
            $('#excel-ventas').prop('disabled', true);
        });

        $('#excel-rotacion').on('click', function () {
            window.location.href = urls.excelRotacion + '?' + $('#form-rotacion').serialize();
        });

        $('#excel-ventas').on('click', function () {
            window.location.href = urls.excelVentas + '?' + $('#form-ventas').serialize();
        });

        $('#menu-grafico-rotacion').on('click', '.gastro-chart-option', function () {
            if (!rotacionActual) {
                return;
            }

            tipoGraficoRotacion = $(this).data('tipo');
            crearGraficoRotacion(rotacionActual);
        });

        $('#menu-grafico-ventas').on('click', '.gastro-chart-option', function () {
            if (!ventasActuales) {
                return;
            }

            tipoGraficoVentas = $(this).data('tipo');
            crearGraficoVentas(ventasActuales);
        });

        $('#tabla-rotacion').on('click', '.gastro-service-link', function () {
            if (!rotacionActual) {
                return;
            }

            var clave = $(this).data('servicio');
            var filas = rotacionActual.detalles[clave] || [];
            var titulo = $(this).text();
            var html = '';
            var facturas = 0;
            var total = 0;

            $.each(filas, function (_, fila) {
                facturas += parseInt(fila.facturas || 0, 10);
                total += parseFloat(fila.valor_total || 0);
                html += '<tr>' +
                    '<td>' + escapar(fila.servicio) + '</td>' +
                    '<td class="text-count">' + entero(fila.facturas) + '</td>' +
                    '<td class="text-money">' + moneda(fila.valor_total) + '</td>' +
                '</tr>';
            });

            if (!filas.length) {
                html = '<tr><td colspan="3" class="text-center text-muted">No existen facturas para este servicio.</td></tr>';
            }

            $('#modal-servicio-titulo').text('Servicio: ' + titulo);
            $('#modal-servicio-tabla').html(html);
            $('#modal-servicio-facturas').text(entero(facturas));
            $('#modal-servicio-total').text(moneda(total));
            $('#modal-servicio').modal('show');
        });

        $('#tabla-ventas').on('click', '.gastro-coverage-link', function () {
            if (!ventasActuales) {
                return;
            }

            var cobertura = $(this).data('cobertura');
            var filas = ventasActuales.detalles[cobertura] || [];
            var titulo = $(this).text();
            var html = '';
            var totales = { procedimientos: 0, imagenes: 0, consultas: 0 };

            $.each(filas, function (_, fila) {
                totales.procedimientos += parseFloat(fila.procedimientos || 0);
                totales.imagenes += parseFloat(fila.imagenes || 0);
                totales.consultas += parseFloat(fila.consultas || 0);
                html += '<tr>' +
                    '<td>' + escapar(fila.seguro) + '</td>' +
                    '<td class="text-money">' + moneda(fila.procedimientos) + '</td>' +
                    '<td class="text-money">' + moneda(fila.imagenes) + '</td>' +
                    '<td class="text-money">' + moneda(fila.consultas) + '</td>' +
                '</tr>';
            });

            if (!filas.length) {
                html = '<tr><td colspan="4" class="text-center text-muted">No existen ventas para esta cobertura.</td></tr>';
            }

            $('#modal-aseguradoras-titulo').text('Cobertura: ' + titulo);
            $('#modal-aseguradoras-tabla').html(html);
            $('#modal-total-procedimientos').text(moneda(totales.procedimientos));
            $('#modal-total-imagenes').text(moneda(totales.imagenes));
            $('#modal-total-consultas').text(moneda(totales.consultas));
            $('#modal-aseguradoras').modal('show');
        });

        cargarRotacion();
        cargarVentas();

        $('a[href="#dashboard-gastroclinica"]').on('shown.bs.tab', function () {
            window.setTimeout(function () {
                if (graficoRotacion && graficoRotacion.reflow) {
                    graficoRotacion.reflow();
                }
                if (graficoVentas && graficoVentas.reflow) {
                    graficoVentas.reflow();
                }
            }, 0);
        });
    })(window.jQuery);
    });
</script>
