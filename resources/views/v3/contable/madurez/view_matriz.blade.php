<style>
    .maturity-hero {
        background: linear-gradient(135deg, #1f4e78 0%, #2878a8 100%);
        border-radius: 8px;
        color: #fff;
        overflow: hidden;
    }
    .maturity-score {
        align-items: center;
        background: rgba(255, 255, 255, .14);
        border: 2px solid rgba(255, 255, 255, .65);
        border-radius: 50%;
        display: flex;
        height: 140px;
        justify-content: center;
        margin: 0 auto;
        width: 140px;
    }
    .maturity-score strong { font-size: 2.3rem; line-height: 1; }
    .metric-card { border-left: 4px solid #2878a8; }
    .metric-number { color: #1f4e78; font-size: 1.9rem; font-weight: 700; }
    .maturity-table td, .maturity-table th { vertical-align: middle; }
    .maturity-table .progress { min-width: 120px; }
    .module-table td, .module-table th { font-size: .86rem; vertical-align: top; }
    .module-table .module-score { min-width: 95px; }
    .audit-note { border-left: 4px solid #17a2b8; }
    .start-module { border-left: 5px solid #dc3545; }
    .priority-number {
        align-items: center;
        background: #e8f2f8;
        border-radius: 50%;
        color: #1f4e78;
        display: inline-flex;
        flex: 0 0 32px;
        font-weight: 700;
        height: 32px;
        justify-content: center;
        margin-right: 12px;
        width: 32px;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1>Matriz de madurez del sistema v3</h1>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Matriz de madurez</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content pb-3">
    <div class="container-fluid">
        <div class="card maturity-hero shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="maturity-score">
                            <div>
                                <strong>{{ $madurezGlobal }}%</strong>
                                <div>madurez global</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h2 class="mb-2">{{ $nivelMadurez }}</h2>
                        <p class="lead mb-2">La version v3 posee una cobertura funcional importante, pero requiere fortalecer seguridad, pruebas, despliegue y mantenibilidad.</p>
                        <p class="mb-0"><i class="far fa-calendar-alt mr-2"></i>Linea base evaluada el {{ $fechaEvaluacion }}</p>
                        @if ($puedeVerPanelOperacion)
                            <a href="{{ $panelOperacionUrl }}" class="btn btn-light mt-3">
                                <i class="fas fa-heartbeat mr-2"></i>Ver operacion y monitoreo
                            </a>
                            <a href="{{ $documentacionUrl }}" class="btn btn-outline-light mt-3 ml-2">
                                <i class="fas fa-book mr-2"></i>Ver documentacion
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card metric-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase small">Avance funcional visible</div>
                        <div class="metric-number">{{ $avanceFuncional }}%</div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-success" style="width: {{ $avanceFuncional }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card metric-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase small">Madurez integral</div>
                        <div class="metric-number">{{ $madurezGlobal }}%</div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-warning" style="width: {{ $madurezGlobal }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card metric-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted text-uppercase small">Preparacion para produccion</div>
                        <div class="metric-number">{{ $preparacionProduccion }}%</div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-danger" style="width: {{ $preparacionProduccion }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card start-module shadow-sm mt-3">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-play-circle text-danger mr-2"></i>Orden recomendado de mejora</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <strong>APIs v3 en certificacion.</strong> El contrato y el aislamiento ya tienen controles; falta ejecutar la prueba de dos empresas en el servidor antes de aprobar produccion.
                </div>
                <div class="row">
                    @foreach ($planMejora as $paso)
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge badge-dark p-2">Paso {{ $paso['orden'] }}</span>
                                    <span class="badge badge-{{ $paso['prioridad'] === 'P0' ? 'danger' : 'warning' }} p-2">{{ $paso['prioridad'] }}</span>
                                </div>
                                <strong>{{ $paso['modulo'] }}</strong>
                                <p class="text-muted small mb-0 mt-2">{{ $paso['objetivo'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Evaluacion ponderada</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover maturity-table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Dimension</th>
                            <th class="text-center">Peso</th>
                            <th>Madurez</th>
                            <th class="text-center">Aporte</th>
                            <th>Estado</th>
                            <th>Hallazgo principal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dimensionesMadurez as $dimension)
                            <tr>
                                <td><strong>{{ $dimension['dimension'] }}</strong></td>
                                <td class="text-center">{{ $dimension['peso'] }}%</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress progress-sm flex-grow-1 mr-2">
                                            <div class="progress-bar bg-{{ $dimension['color'] }}" style="width: {{ $dimension['madurez'] }}%"></div>
                                        </div>
                                        <strong>{{ $dimension['madurez'] }}%</strong>
                                    </div>
                                </td>
                                <td class="text-center">{{ number_format($dimension['aporte'], 2, ',', '.') }}%</td>
                                <td><span class="badge badge-{{ $dimension['color'] }} p-2">{{ $dimension['estado'] }}</span></td>
                                <td>{{ $dimension['hallazgo'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th>Total</th>
                            <th class="text-center">100%</th>
                            <th colspan="2" class="text-center">{{ $madurezGlobal }}%</th>
                            <th colspan="2">{{ $nivelMadurez }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header border-0">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title"><i class="fas fa-clipboard-check mr-2"></i>Auditoria de modulos existentes en v3</h3>
                        <div class="text-muted small mt-1">Inventario estatico del codigo al {{ $fechaEvaluacion }}. No afirma una prueba completa con datos reales.</div>
                    </div>
                    <span class="badge badge-info p-2">{{ $resumenModulos['total'] }} modulos auditados</span>
                </div>
            </div>
            <div class="card-body border-top">
                <div class="alert alert-light audit-note">
                    <strong>Como leer el dictamen:</strong> "Sirve" confirma una implementacion identificable y controles visibles;
                    "Sirve con riesgo" requiere correcciones antes de considerarlo estable; "Parcial" necesita completar o certificar su alcance;
                    "No apto para produccion" debe cerrarse o corregirse primero.
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 col-6 mb-2">
                        <div class="border rounded p-2 h-100"><span class="text-muted small">Promedio por modulo</span><div class="h4 mb-0">{{ $resumenModulos['promedio'] }}%</div></div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="border border-danger rounded p-2 h-100"><span class="text-muted small">Atencion inmediata P0</span><div class="h4 mb-0 text-danger">{{ $resumenModulos['prioridades']['P0'] }}</div></div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="border border-warning rounded p-2 h-100"><span class="text-muted small">Correccion prioritaria P1</span><div class="h4 mb-0 text-warning">{{ $resumenModulos['prioridades']['P1'] }}</div></div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <div class="border border-success rounded p-2 h-100"><span class="text-muted small">Dictamen: sirve</span><div class="h4 mb-0 text-success">{{ isset($resumenModulos['dictamenes']['Sirve']) ? $resumenModulos['dictamenes']['Sirve'] : 0 }}</div></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <input type="search" id="filtroModuloMadurez" class="form-control" placeholder="Buscar modulo, hallazgo o pendiente...">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select id="filtroPrioridadMadurez" class="form-control">
                            <option value="">Todas las prioridades</option>
                            <option value="P0">P0 - inmediata</option>
                            <option value="P1">P1 - alta</option>
                            <option value="P2">P2 - planificada</option>
                            <option value="P3">P3 - mantener</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <select id="filtroDictamenMadurez" class="form-control">
                            <option value="">Todos los dictamenes</option>
                            <option value="Sirve">Sirve</option>
                            <option value="Sirve con controles">Sirve con controles</option>
                            <option value="Sirve con riesgo">Sirve con riesgo</option>
                            <option value="Parcial">Parcial</option>
                            <option value="No apto para produccion">No apto para produccion</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover module-table mb-0" id="tablaModulosMadurez">
                    <thead class="thead-light">
                        <tr>
                            <th>Modulo / ruta</th>
                            <th>Madurez</th>
                            <th>Dictamen</th>
                            <th>Prioridad</th>
                            <th>Ultima revision</th>
                            <th>Evidencia encontrada</th>
                            <th>Que falta</th>
                            <th>Por donde iniciar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($modulosMadurez as $modulo)
                            <?php
                                $colorPrioridad = $modulo['prioridad'] === 'P0' ? 'danger' : ($modulo['prioridad'] === 'P1' ? 'warning' : ($modulo['prioridad'] === 'P2' ? 'info' : 'success'));
                            ?>
                            <tr data-prioridad="{{ $modulo['prioridad'] }}" data-dictamen="{{ $modulo['dictamen'] }}">
                                <td>
                                    <strong>{{ $modulo['modulo'] }}</strong>
                                    <div><code>{{ $modulo['ruta'] }}</code></div>
                                    <small class="text-muted">{{ $modulo['grupo'] }}</small>
                                </td>
                                <td class="module-score">
                                    <strong>{{ $modulo['madurez'] }}%</strong>
                                    <div class="progress progress-xs mt-1">
                                        <div class="progress-bar bg-{{ $modulo['color'] }}" style="width: {{ $modulo['madurez'] }}%"></div>
                                    </div>
                                    <small>{{ $modulo['estado'] }}</small>
                                </td>
                                <td><span class="badge badge-{{ $modulo['color'] }} p-2">{{ $modulo['dictamen'] }}</span></td>
                                <td><span class="badge badge-{{ $colorPrioridad }} p-2">{{ $modulo['prioridad'] }}</span></td>
                                <td>
                                    <strong>{{ $modulo['ultima_revision'] }}</strong>
                                    <div class="text-muted small mt-1">{{ $modulo['ultimo_resultado'] }}</div>
                                </td>
                                <td>{{ $modulo['evidencia'] }}</td>
                                <td>{{ $modulo['falta'] }}</td>
                                <td>{{ $modulo['inicio'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="sinModulosMadurez" class="alert alert-info m-3 d-none">No hay modulos que coincidan con los filtros.</div>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-history mr-2"></i>Historial de revisiones y modificaciones</h3>
                <div class="card-tools"><span class="badge badge-success p-2">Actualizacion obligatoria por cambio v3</span></div>
            </div>
            <div class="card-body border-top">
                <p class="text-muted mb-0">Una modificacion puede mantener el porcentaje si no agrega evidencia suficiente. El registro igualmente debe indicar que se reviso, que cambio y cual fue el resultado.</p>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Modulo</th>
                            <th>Tipo</th>
                            <th>Cambio revisado</th>
                            <th>Evidencia</th>
                            <th>Resultado</th>
                            <th>Variacion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($revisionesMadurez as $revision)
                            <tr>
                                <td>{{ $revision['fecha'] }}</td>
                                <td><strong>{{ $revision['modulo'] }}</strong></td>
                                <td>{{ $revision['tipo'] }}</td>
                                <td>{{ $revision['cambio'] }}</td>
                                <td>{{ $revision['evidencia'] }}</td>
                                <td>{{ $revision['resultado'] }}</td>
                                <td class="text-center">
                                    @if ($revision['madurez_anterior'] === null)
                                        <span class="badge badge-secondary">Linea base {{ $revision['madurez_nueva'] }}%</span>
                                    @elseif ($revision['madurez_nueva'] > $revision['madurez_anterior'])
                                        <span class="badge badge-success">{{ $revision['madurez_anterior'] }}% &rarr; {{ $revision['madurez_nueva'] }}%</span>
                                    @else
                                        <span class="badge badge-info">Se mantiene en {{ $revision['madurez_nueva'] }}%</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header border-0">
                <h3 class="card-title"><i class="fas fa-tasks mr-2"></i>Prioridades para elevar la madurez</h3>
            </div>
            <div class="card-body">
                @foreach ($prioridadesMadurez as $indice => $prioridad)
                    <div class="d-flex align-items-center {{ !$loop->last ? 'mb-3' : '' }}">
                        <span class="priority-number">{{ $indice + 1 }}</span>
                        <span>{{ $prioridad }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        function filtrarModulosMadurez() {
            var texto = ($('#filtroModuloMadurez').val() || '').toLowerCase();
            var prioridad = $('#filtroPrioridadMadurez').val() || '';
            var dictamen = $('#filtroDictamenMadurez').val() || '';
            var visibles = 0;

            $('#tablaModulosMadurez tbody tr').each(function () {
                var fila = $(this);
                var coincideTexto = texto === '' || fila.text().toLowerCase().indexOf(texto) !== -1;
                var coincidePrioridad = prioridad === '' || fila.data('prioridad') === prioridad;
                var coincideDictamen = dictamen === '' || fila.data('dictamen') === dictamen;
                var visible = coincideTexto && coincidePrioridad && coincideDictamen;
                fila.toggle(visible);
                if (visible) visibles++;
            });

            $('#sinModulosMadurez').toggleClass('d-none', visibles !== 0);
        }

        $(document).off('input.madurez change.madurez', '#filtroModuloMadurez, #filtroPrioridadMadurez, #filtroDictamenMadurez')
            .on('input.madurez change.madurez', '#filtroModuloMadurez, #filtroPrioridadMadurez, #filtroDictamenMadurez', filtrarModulosMadurez);
    })();
</script>
