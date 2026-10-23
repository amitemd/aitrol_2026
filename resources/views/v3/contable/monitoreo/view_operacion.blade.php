<style>
    .health-hero {
        background: linear-gradient(125deg, #153b5b 0%, #1b6788 58%, #2386a3 100%);
        border: 0;
        border-radius: 10px;
        color: #fff;
        overflow: hidden;
        position: relative;
    }
    .health-hero:after {
        border: 45px solid rgba(255, 255, 255, .06);
        border-radius: 50%;
        content: '';
        height: 260px;
        position: absolute;
        right: -70px;
        top: -105px;
        width: 260px;
    }
    .health-score {
        align-items: center;
        background: rgba(255, 255, 255, .14);
        border: 6px solid rgba(255, 255, 255, .28);
        border-radius: 50%;
        display: flex;
        height: 142px;
        justify-content: center;
        margin: auto;
        position: relative;
        width: 142px;
        z-index: 1;
    }
    .health-score strong { font-size: 2.2rem; line-height: 1; }
    .health-label { font-size: .72rem; letter-spacing: .04rem; text-transform: uppercase; }
    .health-status-badge { background: rgba(255, 255, 255, .18); border: 1px solid rgba(255, 255, 255, .35); border-radius: 20px; display: inline-block; padding: 5px 12px; }
    .health-progress { background: rgba(255, 255, 255, .18); border-radius: 4px; height: 7px; overflow: hidden; }
    .health-progress span { background: #fff; border-radius: 4px; display: block; height: 100%; }
    .environment-pill { background: rgba(255, 255, 255, .1); border-radius: 6px; margin-right: 8px; padding: 7px 10px; }
    .check-card { border: 0; border-left: 5px solid transparent; border-radius: 7px; height: 100%; }
    .check-card.ok { border-left-color: #20a857; }
    .check-card.fail { background: #fff8f8; border-left-color: #dc3545; }
    .check-icon {
        align-items: center;
        border-radius: 50%;
        display: flex;
        flex: 0 0 48px;
        height: 48px;
        justify-content: center;
        margin-right: 14px;
        width: 48px;
    }
    .check-state { font-size: .68rem; font-weight: 700; letter-spacing: .04rem; text-transform: uppercase; }
    .summary-card { border: 0; border-radius: 8px; height: 100%; overflow: hidden; }
    .summary-accent { height: 4px; }
    .summary-value { color: #1e4665; font-size: 1.75rem; font-weight: 700; line-height: 1; }
    .section-card { border: 0; border-radius: 8px; }
    .section-card .card-header { background: #fff; border-bottom: 1px solid #edf0f3; padding: 14px 18px; }
    .operation-metric { font-size: 1.8rem; font-weight: 700; line-height: 1.1; }
    .module-bar { height: 7px; }
    .activity-table td, .activity-table th { vertical-align: middle; }
    .activity-table thead th { background: #f2f5f7; border-bottom: 0; color: #51606d; font-size: .72rem; letter-spacing: .035rem; text-transform: uppercase; }
    .architecture-card { background: linear-gradient(180deg, #fff 0%, #f8fafc 100%); border: 0; border-radius: 8px; }
    .architecture-score { align-items: center; background: #fff3cd; border-radius: 50%; color: #856404; display: flex; flex: 0 0 92px; height: 92px; justify-content: center; width: 92px; }
    .architecture-score strong { font-size: 1.45rem; }
    .architecture-step { border-left: 3px solid #dbe4eb; padding: 0 0 18px 18px; position: relative; }
    .architecture-step:last-child { padding-bottom: 0; }
    .architecture-step:before { background: #fff; border: 3px solid #3c8dbc; border-radius: 50%; content: ''; height: 13px; left: -8px; position: absolute; top: 2px; width: 13px; }
    .risk-list { list-style: none; margin: 0; padding: 0; }
    .risk-list li { border-bottom: 1px solid #edf0f3; padding: 9px 0; }
    .risk-list li:last-child { border-bottom: 0; }
    .log-number { font-size: 1.25rem; font-weight: 700; }
    .log-detail-message { color: #263746; font-size: .85rem; line-height: 1.35; min-width: 280px; white-space: normal; word-break: break-word; }
    .log-detail-origin { color: #687782; font-family: Consolas, Monaco, monospace; font-size: .73rem; word-break: break-all; }
    .log-detail-table td { vertical-align: top; }
    .log-detail-table thead th { background: #f2f5f7; border-bottom: 0; color: #51606d; font-size: .7rem; letter-spacing: .035rem; text-transform: uppercase; }
    .log-help { background: #f7fafc; border-left: 4px solid #3c8dbc; }
    .error-suggestion { background: #f7fafc; border-left: 3px solid #20a857; border-radius: 3px; font-size: .78rem; margin-top: 8px; padding: 8px 10px; }
    .security-score { align-items: center; background: #eef7fb; border: 6px solid #3c8dbc; border-radius: 50%; color: #1e4665; display: flex; flex: 0 0 112px; height: 112px; justify-content: center; width: 112px; }
    .security-score strong { font-size: 1.8rem; line-height: 1; }
    .maintenance-score { background: #f1faf4; border-color: #20a857; color: #186b3a; }
    .security-table td { vertical-align: top; }
    .security-table thead th { background: #f2f5f7; border-bottom: 0; color: #51606d; font-size: .7rem; letter-spacing: .035rem; text-transform: uppercase; }
    .error-follow-up { min-width: 225px; }
    @media (max-width: 767.98px) {
        .environment-pill { display: block; margin: 5px 0; }
        .health-score { height: 120px; width: 120px; }
    }
</style>

<?php
    $verificacionesFallidas = array_filter($verificaciones, function ($verificacion) {
        return !$verificacion['correcto'];
    });
    $cantidadFallidas = count($verificacionesFallidas);
    $estadoSalud = $porcentajeSalud >= 90 ? 'Estable' : ($porcentajeSalud >= 75 ? 'Requiere atencion' : 'Critico');
    $colorSalud = $porcentajeSalud >= 90 ? 'success' : ($porcentajeSalud >= 75 ? 'warning' : 'danger');
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1>Operacion y monitoreo</h1>
            </div>
            <div class="col-sm-5">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('v3.matriz-madurez') }}">Matriz de madurez</a></li>
                    <li class="breadcrumb-item active">Monitoreo</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content pb-3">
    <div class="container-fluid">
        @if (session('operacion_exito'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <i class="fas fa-check-circle mr-2"></i>{{ session('operacion_exito') }}
            </div>
        @endif
        @if (session('operacion_error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('operacion_error') }}
            </div>
        @endif
        <div class="card health-hero shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-4 text-center mb-3 mb-md-0">
                        <div class="health-score">
                            <div>
                                <strong>{{ $porcentajeSalud }}%</strong>
                                <div class="health-label mt-2">salud operativa</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-8 position-relative" style="z-index: 1;">
                        <div class="d-flex flex-wrap align-items-center mb-2">
                            <h2 class="mb-0 mr-3">Estado operativo de v3</h2>
                            <span class="health-status-badge"><i class="fas fa-circle mr-2 text-{{ $colorSalud }}"></i>{{ $estadoSalud }}</span>
                        </div>
                        <p class="lead mb-2">{{ $verificacionesCorrectas }} controles correctos y {{ $cantidadFallidas }} que requieren revision.</p>
                        <div class="health-progress mb-3"><span style="width: {{ $porcentajeSalud }}%"></span></div>
                        <div class="d-flex flex-wrap small">
                            <span class="environment-pill"><i class="fas fa-layer-group mr-2"></i>Entorno: <strong>{{ $entorno }}</strong></span>
                            <span class="environment-pill"><i class="fab fa-php mr-2"></i>PHP <strong>{{ $versionPhp }}</strong></span>
                            <span class="environment-pill"><i class="fab fa-laravel mr-2"></i><strong>{{ $versionLaravel }}</strong></span>
                            <span class="environment-pill"><i class="far fa-clock mr-2"></i>{{ $fechaRevision }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card summary-card shadow-sm mb-0">
                    <div class="summary-accent bg-{{ $colorSalud }}"></div>
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Resultado general</div>
                        <div class="summary-value mt-2">{{ $estadoSalud }}</div>
                        <div class="small text-muted mt-2">Basado en {{ $totalVerificaciones }} controles de infraestructura.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card summary-card shadow-sm mb-0">
                    <div class="summary-accent {{ $cantidadFallidas > 0 ? 'bg-danger' : 'bg-success' }}"></div>
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Controles pendientes</div>
                        <div class="summary-value mt-2">{{ $cantidadFallidas }}</div>
                        <div class="small text-muted mt-2">{{ $cantidadFallidas > 0 ? 'Requieren accion antes de considerar estable el ambiente.' : 'No existen alertas de infraestructura.' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3 mb-sm-0">
                <div class="card summary-card shadow-sm mb-0">
                    <div class="summary-accent {{ $resumenLog['errores_pendientes'] > 0 ? 'bg-danger' : 'bg-success' }}"></div>
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Tipos de error pendientes</div>
                        <div class="summary-value mt-2">{{ $resumenLog['errores_pendientes'] }}</div>
                        <div class="small text-muted mt-2">
                            {{ $resumenLog['errores_recientes'] }} eventos en la muestra y {{ $resumenLog['errores_corregidos'] }} corregidos en seguimiento.
                            @if ($resumenLog['errores_pendientes'] > 0 || $resumenLog['errores_corregidos'] > 0)
                                <button type="button" class="btn btn-link btn-sm p-0 ml-1 align-baseline" data-toggle="modal" data-target="#modalErroresLog">Ver cuales son</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card summary-card shadow-sm mb-0">
                    <div class="summary-accent bg-info"></div>
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Actividad auditada</div>
                        <div class="summary-value mt-2">{{ $auditoriaOperacion['ultimos_siete_dias'] }}</div>
                        <div class="small text-muted mt-2">Operaciones registradas en {{ $umbralesOperacion['dias_auditoria'] }} dias.</div>
                    </div>
                </div>
            </div>
        </div>

        @if ($cantidadFallidas > 0)
            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger text-white border-0">
                    <strong><i class="fas fa-exclamation-triangle mr-2"></i>Acciones prioritarias</strong>
                </div>
                <div class="card-body py-2">
                    <ul class="risk-list">
                        @foreach ($verificacionesFallidas as $verificacion)
                            <li><i class="{{ $verificacion['icono'] }} text-danger mr-2"></i><strong>{{ $verificacion['nombre'] }}:</strong> {{ $verificacion['detalle'] }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="row">
            @foreach ($verificaciones as $verificacion)
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="card check-card {{ $verificacion['correcto'] ? 'ok' : 'fail' }} shadow-sm mb-0">
                        <div class="card-body d-flex align-items-center">
                            <span class="check-icon {{ $verificacion['correcto'] ? 'bg-success' : 'bg-danger' }} text-white">
                                <i class="{{ $verificacion['icono'] }}"></i>
                            </span>
                            <div class="flex-grow-1">
                                <div class="check-state {{ $verificacion['correcto'] ? 'text-success' : 'text-danger' }} mb-1">
                                    {{ $verificacion['correcto'] ? 'Correcto' : 'Revisar' }}
                                </div>
                                <strong>{{ $verificacion['nombre'] }}</strong>
                                <div class="small text-muted">{{ $verificacion['detalle'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card section-card shadow-sm mb-3">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1"><i class="fas fa-shield-alt text-primary mr-2"></i>Diagnostico de seguridad v3</h3>
                        <div class="small text-muted">Evaluacion de solo lectura; no cambia permisos, rutas ni configuracion.</div>
                    </div>
                    <span class="badge badge-light border p-2 mt-2 mt-md-0">{{ count($seguridadOperacion['controles']) }} controles evaluados</span>
                </div>
            </div>
            <div class="card-body pb-0">
                <div class="row align-items-center">
                    <div class="col-lg-3 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="security-score mr-3">
                                <div class="text-center"><strong>{{ $seguridadOperacion['porcentaje'] }}%</strong><div class="small">seguridad</div></div>
                            </div>
                            <div class="small">
                                <div class="mb-2"><span class="badge badge-success mr-1">{{ $seguridadOperacion['correctos'] }}</span> Correctos</div>
                                <div class="mb-2"><span class="badge badge-warning mr-1">{{ $seguridadOperacion['revisar'] }}</span> Por revisar</div>
                                <div><span class="badge badge-danger mr-1">{{ $seguridadOperacion['criticos'] }}</span> Criticos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 mb-3">
                        <div class="alert alert-info mb-0">
                            <strong>Lectura recomendada:</strong> “Revisar” identifica una superficie que necesita confirmacion antes de cambiarla. “Critico” representa una configuracion efectiva que debe corregirse primero en pruebas y luego mediante el despliegue autorizado.
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover security-table mb-0">
                    <thead>
                        <tr>
                            <th>Control</th>
                            <th>Estado</th>
                            <th>Resultado</th>
                            <th>Recomendacion</th>
                            <th>Prioridad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($seguridadOperacion['controles'] as $controlSeguridad)
                            <tr>
                                <td>
                                    <strong>{{ $controlSeguridad['nombre'] }}</strong>
                                    <div class="small text-muted mt-1">{{ $controlSeguridad['evidencia'] }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $controlSeguridad['estado'] === 'correcto' ? 'badge-success' : ($controlSeguridad['estado'] === 'critico' ? 'badge-danger' : 'badge-warning') }}">
                                        {{ $controlSeguridad['estado'] === 'correcto' ? 'Correcto' : ($controlSeguridad['estado'] === 'critico' ? 'Critico' : 'Revisar') }}
                                    </span>
                                </td>
                                <td class="small">{{ $controlSeguridad['detalle'] }}</td>
                                <td class="small">{{ $controlSeguridad['recomendacion'] }}</td>
                                <td><span class="badge badge-light border">{{ $controlSeguridad['prioridad'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card section-card shadow-sm mb-3">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1"><i class="fas fa-tools text-success mr-2"></i>Mantenimiento preventivo</h3>
                        <div class="small text-muted">Controles de continuidad y operacion; ninguna recomendacion se ejecuta desde esta pantalla.</div>
                    </div>
                    <span class="badge badge-light border p-2 mt-2 mt-md-0">{{ count($mantenimientoOperacion['controles']) }} controles evaluados</span>
                </div>
            </div>
            <div class="card-body pb-0">
                <div class="row align-items-center">
                    <div class="col-lg-3 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="security-score maintenance-score mr-3">
                                <div class="text-center"><strong>{{ $mantenimientoOperacion['porcentaje'] }}%</strong><div class="small">mantenimiento</div></div>
                            </div>
                            <div class="small">
                                <div class="mb-2"><span class="badge badge-success mr-1">{{ $mantenimientoOperacion['correctos'] }}</span> Correctos</div>
                                <div class="mb-2"><span class="badge badge-warning mr-1">{{ $mantenimientoOperacion['revisar'] }}</span> Por revisar</div>
                                <div><span class="badge badge-danger mr-1">{{ $mantenimientoOperacion['criticos'] }}</span> Criticos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 mb-3">
                        <div class="alert alert-success mb-0">
                            <strong>Regla segura:</strong> respaldar, verificar y documentar antes de limpiar logs, reconstruir cache, cambiar tareas programadas o modificar permisos.
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover security-table mb-0">
                    <thead>
                        <tr>
                            <th>Control</th>
                            <th>Estado</th>
                            <th>Resultado</th>
                            <th>Accion recomendada</th>
                            <th>Frecuencia</th>
                            <th>Prioridad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mantenimientoOperacion['controles'] as $controlMantenimiento)
                            <tr>
                                <td><strong>{{ $controlMantenimiento['nombre'] }}</strong></td>
                                <td>
                                    <span class="badge {{ $controlMantenimiento['estado'] === 'correcto' ? 'badge-success' : ($controlMantenimiento['estado'] === 'critico' ? 'badge-danger' : 'badge-warning') }}">
                                        {{ $controlMantenimiento['estado'] === 'correcto' ? 'Correcto' : ($controlMantenimiento['estado'] === 'critico' ? 'Critico' : 'Revisar') }}
                                    </span>
                                </td>
                                <td class="small">{{ $controlMantenimiento['detalle'] }}</td>
                                <td class="small">{{ $controlMantenimiento['recomendacion'] }}</td>
                                <td class="small text-nowrap">{{ $controlMantenimiento['frecuencia'] }}</td>
                                <td><span class="badge badge-light border">{{ $controlMantenimiento['prioridad'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card section-card shadow-sm">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-lg-5 mb-3 mb-lg-0">
                        <strong><i class="fas fa-terminal text-primary mr-2"></i>Verificacion automatizada</strong>
                        <div class="small text-muted mt-1">No modifica datos y devuelve codigo 0 si todo esta correcto o 1 si requiere atencion.</div>
                        <code>php artisan v3:health-check</code>
                        <span class="text-muted mx-1">|</span>
                        <code>php artisan v3:health-check --json</code>
                    </div>
                    <div class="col-lg-7">
                        <div class="row text-center small">
                            <div class="col"><strong>{{ $umbralesOperacion['peticion_lenta_ms'] }} ms</strong><br><span class="text-muted">Peticion lenta</span></div>
                            <div class="col"><strong>{{ $umbralesOperacion['consulta_lenta_ms'] }} ms</strong><br><span class="text-muted">Consulta lenta</span></div>
                            <div class="col"><strong>{{ $umbralesOperacion['disco_minimo'] }}%</strong><br><span class="text-muted">Disco minimo</span></div>
                            <div class="col"><strong>{{ $umbralesOperacion['log_maximo_mb'] }} MB</strong><br><span class="text-muted">Log maximo</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card section-card shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Actividad auditada</h3>
                    </div>
                    <div class="card-body">
                        @if ($auditoriaOperacion['disponible'])
                            <div class="row text-center">
                                <div class="col-6 col-md-3 border-right mb-3">
                                    <div class="operation-metric text-primary">{{ $auditoriaOperacion['hoy'] }}</div>
                                    <div class="text-muted small">Operaciones hoy</div>
                                </div>
                                <div class="col-6 col-md-3 border-right mb-3">
                                    <div class="operation-metric text-info">{{ $auditoriaOperacion['ultimos_siete_dias'] }}</div>
                                    <div class="text-muted small">Ultimos {{ $umbralesOperacion['dias_auditoria'] }} dias</div>
                                </div>
                                <div class="col-6 col-md-3 border-right mb-3">
                                    <div class="operation-metric text-success">{{ $auditoriaOperacion['usuarios_hoy'] }}</div>
                                    <div class="text-muted small">Usuarios hoy</div>
                                </div>
                                <div class="col-6 col-md-3 mb-3">
                                    <div class="operation-metric {{ $auditoriaOperacion['consultas_lentas'] > 0 ? 'text-danger' : 'text-success' }}">{{ $auditoriaOperacion['consultas_lentas'] }}</div>
                                    <div class="text-muted small">Consultas &gt;= {{ $umbralesOperacion['consulta_lenta_ms'] }} ms</div>
                                </div>
                            </div>
                            <div class="text-muted small border-top pt-3">
                                Ultima actividad: {{ $auditoriaOperacion['ultima_actividad'] ?: 'Sin actividad registrada' }}
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $auditoriaOperacion['error'] }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card section-card shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title"><i class="fas fa-file-medical-alt mr-2"></i>Registro Laravel</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <span>Archivo</span>
                            <strong>{{ $resumenLog['archivo_existe'] ? 'Disponible' : 'Sin crear' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <span>Tamano</span><strong>{{ $resumenLog['tamano'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <span>Errores en muestra</span><strong class="log-number text-danger">{{ $resumenLog['errores_recientes'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <span>Tipos pendientes</span><strong class="text-danger">{{ $resumenLog['errores_pendientes'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <span>Marcados corregidos</span><strong class="text-success">{{ $resumenLog['errores_corregidos'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between pb-2">
                            <span>Advertencias en muestra</span><strong class="text-warning">{{ $resumenLog['advertencias_recientes'] }}</strong>
                        </div>
                        <div class="small text-muted mt-2">
                            Ultima escritura: {{ $resumenLog['ultima_modificacion'] ?: 'No disponible' }}
                        </div>
                        <div class="small text-muted border-top pt-2 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>Los conteos corresponden al fragmento reciente del archivo, no a un periodo completo.
                        </div>
                        @if ($resumenLog['errores_pendientes'] > 0 || $resumenLog['errores_corregidos'] > 0)
                            <button type="button" class="btn btn-outline-danger btn-sm btn-block mt-3" data-toggle="modal" data-target="#modalErroresLog">
                                <i class="fas fa-search mr-1"></i>Revisar errores agrupados
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card section-card shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title"><i class="fas fa-th-list mr-2"></i>Modulos con mayor actividad ({{ $umbralesOperacion['dias_auditoria'] }} dias)</h3>
                    </div>
                    <div class="card-body">
                        @forelse ($auditoriaOperacion['modulos'] as $modulo)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>{{ $modulo->controlador_auditoria ?: 'No clasificado' }}</span>
                                    <strong>{{ $modulo->total }}</strong>
                                </div>
                                <div class="progress module-bar">
                                    <div class="progress-bar bg-info" style="width: {{ min(100, ($modulo->total / max(1, $auditoriaOperacion['ultimos_siete_dias'])) * 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">No existe actividad suficiente para mostrar.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card section-card shadow-sm">
                    <div class="card-header border-0">
                        <h3 class="card-title"><i class="fas fa-history mr-2"></i>Actividad reciente</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover activity-table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Modulo</th>
                                    <th>Operacion</th>
                                    <th class="text-right">Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($auditoriaOperacion['actividades'] as $actividad)
                                    <tr>
                                        <td class="text-nowrap">{{ $actividad->created_at_auditoria }}</td>
                                        <td>{{ trim($actividad->nombre1 . ' ' . $actividad->apellido1) ?: 'No identificado' }}</td>
                                        <td>{{ $actividad->controlador_auditoria ?: 'No clasificado' }}</td>
                                        <td>{{ $actividad->observacion_auditoria ?: ($actividad->submodulo_auditoria ?: 'Sin descripcion') }}</td>
                                        <td class="text-right">
                                            @if ($actividad->tiempo_consulta_auditoria !== null && $actividad->tiempo_consulta_auditoria !== '')
                                                <span class="badge {{ $actividad->tiempo_consulta_auditoria >= 1000 ? 'badge-danger' : 'badge-light' }}">
                                                    {{ number_format($actividad->tiempo_consulta_auditoria, 2, ',', '.') }} ms
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No hay actividad reciente.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card architecture-card shadow-sm mt-2">
            <div class="card-header bg-transparent border-bottom-0 pb-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-1"><i class="fas fa-project-diagram text-primary mr-2"></i>Evaluacion de arquitectura actual</h3>
                        <div class="small text-muted">Diagnostico informativo; esta pantalla no modifica controladores ni rutas.</div>
                    </div>
                    <span class="badge badge-warning p-2 mt-2 mt-md-0">Deuda tecnica controlada</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 border-right mb-4 mb-lg-0">
                        <div class="d-flex align-items-center mb-3">
                            <div class="architecture-score mr-3"><div class="text-center"><strong>Medio</strong><div class="small">nivel actual</div></div></div>
                            <div>
                                <div class="text-muted small text-uppercase">Patron identificado</div>
                                <h4 class="mb-1">index + switch</h4>
                                <p class="text-muted mb-0">Despachador de acciones por submodulo dentro de cada controlador.</p>
                            </div>
                        </div>
                        <div class="alert alert-info mb-0">
                            <strong>Conclusion:</strong> es valido en Laravel 5.4 y conviene conservarlo para no romper URLs, pero no deberia seguir creciendo indefinidamente.
                        </div>
                    </div>
                    <div class="col-lg-4 border-right mb-4 mb-lg-0">
                        <h5><i class="fas fa-balance-scale text-warning mr-2"></i>Lectura tecnica</h5>
                        <ul class="risk-list">
                            <li><i class="fas fa-check text-success mr-2"></i><strong>Ventaja:</strong> centraliza la entrada y conserva compatibilidad.</li>
                            <li><i class="fas fa-check text-success mr-2"></i><strong>Ventaja:</strong> facilita ubicar el modulo principal.</li>
                            <li><i class="fas fa-exclamation text-warning mr-2"></i><strong>Riesgo:</strong> el switch crece y mezcla consultas, vistas y escrituras.</li>
                            <li><i class="fas fa-exclamation text-warning mr-2"></i><strong>Riesgo:</strong> dificulta permisos, validacion y pruebas por accion.</li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <h5><i class="fas fa-route text-primary mr-2"></i>Evolucion recomendada</h5>
                        <div class="architecture-step">
                            <strong>1. Mantener rutas actuales</strong>
                            <div class="small text-muted">No cambiar URLs ni nombres usados por la interfaz.</div>
                        </div>
                        <div class="architecture-step">
                            <strong>2. Extraer nuevas acciones</strong>
                            <div class="small text-muted">Mover logica nueva a metodos privados o servicios v3 pequenos que puedan probarse.</div>
                        </div>
                        <div class="architecture-step">
                            <strong>3. Separar por riesgo</strong>
                            <div class="small text-muted">Priorizar ventas, compras, bancos, inventario y asientos.</div>
                        </div>
                        <div class="architecture-step">
                            <strong>4. Migracion gradual</strong>
                            <div class="small text-muted">Crear rutas especificas solo cuando existan pruebas que protejan el comportamiento.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalErroresLog" tabindex="-1" role="dialog" aria-labelledby="tituloErroresLog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <div>
                    <h4 class="modal-title" id="tituloErroresLog"><i class="fas fa-exclamation-circle mr-2"></i>Detalle de errores del registro Laravel</h4>
                    <div class="small mt-1">
                        {{ $resumenLog['errores_pendientes'] }} tipos pendientes y {{ $resumenLog['errores_corregidos'] }} marcados como corregidos.
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="log-help px-3 py-2 m-3 small">
                    <strong>Como usar este detalle:</strong> marca un error solo despues de aplicar y verificar la correccion. No se elimina del log; si vuelve a aparecer con fecha posterior, regresara automaticamente a Pendientes.
                </div>
                <ul class="nav nav-tabs px-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="errores-pendientes-tab" data-toggle="tab" href="#errores-pendientes" role="tab">
                            Pendientes <span class="badge badge-danger ml-1">{{ $resumenLog['errores_pendientes'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="errores-corregidos-tab" data-toggle="tab" href="#errores-corregidos" role="tab">
                            Corregidos <span class="badge badge-success ml-1">{{ $resumenLog['errores_corregidos'] }}</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="errores-pendientes" role="tabpanel">
                @if (count($resumenLog['errores_detalle']) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover log-detail-table mb-0">
                            <thead>
                                <tr>
                                    <th>Ultima fecha</th>
                                    <th>Nivel</th>
                                    <th class="text-center">Veces</th>
                                    <th>Error</th>
                                    <th>Origen probable</th>
                                    <th>Seguimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resumenLog['errores_detalle'] as $errorLog)
                                    <tr>
                                        <td class="text-nowrap small">{{ $errorLog['fecha'] }}</td>
                                        <td>
                                            <span class="badge {{ $errorLog['nivel'] === 'ERROR' ? 'badge-danger' : 'badge-dark' }}">{{ $errorLog['nivel'] }}</span>
                                            <div class="small text-muted mt-1">{{ $errorLog['entorno'] }}</div>
                                        </td>
                                        <td class="text-center"><span class="badge badge-pill badge-danger px-2">{{ $errorLog['ocurrencias'] }}</span></td>
                                        <td>
                                            <div class="d-flex flex-wrap align-items-center mb-1">
                                                <span class="badge badge-info mr-2">{{ $errorLog['categoria'] }}</span>
                                                <span class="small text-muted">Prioridad: {{ $errorLog['prioridad'] }}</span>
                                            </div>
                                            <div class="log-detail-message">{{ $errorLog['mensaje'] }}</div>
                                            <div class="error-suggestion">
                                                <div><strong>Causa probable:</strong> {{ $errorLog['causa_probable'] }}</div>
                                                <div class="mt-1"><strong>Como corregirlo:</strong> {{ $errorLog['sugerencia'] }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($errorLog['origen'])
                                                <div class="log-detail-origin">{{ $errorLog['origen'] }}{{ $errorLog['linea'] ? ':' . $errorLog['linea'] : '' }}</div>
                                            @else
                                                <span class="small text-muted">No identificado en la muestra</span>
                                            @endif
                                        </td>
                                        <td class="error-follow-up">
                                            @if (!empty($errorLog['reabierto']))
                                                <div class="alert alert-warning py-1 px-2 small mb-2"><i class="fas fa-redo mr-1"></i>Reabierto por una nueva aparicion.</div>
                                            @endif
                                            <form method="POST" action="{{ route('v3.operacion-monitoreo.errores.corregir', ['firma' => $errorLog['firma']]) }}">
                                                {{ csrf_field() }}
                                                <label class="small mb-1" for="nota-{{ $errorLog['firma'] }}">Nota de correccion (opcional)</label>
                                                <textarea id="nota-{{ $errorLog['firma'] }}" name="nota" class="form-control form-control-sm mb-2" rows="2" maxlength="500" placeholder="Ej.: se valido el resultado de first() antes de usarlo"></textarea>
                                                <button type="submit" class="btn btn-success btn-sm btn-block" onclick="return confirm('Confirma que este error fue corregido y verificado?');">
                                                    <i class="fas fa-check mr-1"></i>Marcar corregido
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning mx-3">
                        No existen errores pendientes en la muestra actual.
                    </div>
                @endif
                    </div>
                    <div class="tab-pane fade" id="errores-corregidos" role="tabpanel">
                        @if (count($resumenLog['errores_corregidos_detalle']) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover log-detail-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Corregido</th>
                                            <th>Error</th>
                                            <th>Origen</th>
                                            <th>Responsable y nota</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($resumenLog['errores_corregidos_detalle'] as $errorCorregido)
                                            <tr>
                                                <td class="text-nowrap small">
                                                    <strong class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ $errorCorregido['corregido_en'] }}</strong>
                                                    <div class="text-muted mt-1">Ultima aparicion: {{ $errorCorregido['fecha'] ?: 'No disponible' }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info mb-1">{{ $errorCorregido['categoria'] }}</span>
                                                    <div class="log-detail-message">{{ $errorCorregido['mensaje'] }}</div>
                                                </td>
                                                <td>
                                                    @if ($errorCorregido['origen'])
                                                        <div class="log-detail-origin">{{ $errorCorregido['origen'] }}{{ $errorCorregido['linea'] ? ':' . $errorCorregido['linea'] : '' }}</div>
                                                    @else
                                                        <span class="small text-muted">No identificado</span>
                                                    @endif
                                                </td>
                                                <td class="small">
                                                    <strong>{{ $errorCorregido['usuario'] }}</strong>
                                                    <div class="text-muted">{{ $errorCorregido['nota'] !== '' ? $errorCorregido['nota'] : 'Sin nota de correccion' }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-light border mx-3 mt-3">Todavia no se han marcado errores como corregidos.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="small text-muted">
                    Se muestran hasta 20 tipos recientes. El texto completo y la traza permanecen en <code>storage/logs/laravel.log</code> del servidor.
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
