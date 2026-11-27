@extends('layouts.adminlte')

@section('title', 'Bitácora | Auditoría')

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Bitácora', 'subtitulo' => 'Actividad de usuarios y registros de aplicación', 'icono' => 'bi-shield-check',
    ])

    <div class="app-content">
        <div class="container-fluid">
            <div class="card clientes-directorio" data-ajax-list>
                <form method="GET" action="{{ route('auditoria.logs.index') }}" data-ajax-search
                    class="row g-2 align-items-end m-0 p-3 border-bottom">
                    <div class="col-6 col-lg">
                        <label class="form-label" for="auditoria-nivel">Nivel</label>
                        <select id="auditoria-nivel" name="nivel" class="form-select">
                            <option value="">Todos los niveles</option>
                            @foreach ($niveles as $valor => $texto)
                                <option value="{{ $valor }}" @selected($valor === ($filtros['nivel'] ?? null))>{{ $texto }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (auth()->user()->is_admin_global)
                        <div class="col-6 col-lg">
                            <label class="form-label" for="auditoria-ambito">Ámbito</label>
                            <select id="auditoria-ambito" name="ambito" class="form-select">
                                <option value="empresa" @selected($ambito === 'empresa')>Mi empresa</option>
                                <option value="todas" @selected($ambito === 'todas')>Todas las empresas</option>
                                <option value="sistema" @selected($ambito === 'sistema')>Sistema sin empresa</option>
                            </select>
                        </div>
                    @endif
                    <div class="col-6 col-lg">
                        <label class="form-label" for="auditoria-modulo">Módulo</label>
                        <select id="auditoria-modulo" name="modulo" class="form-select">
                            <option value="">Todos los módulos</option>
                            @foreach ($modulos as $valor => $texto)
                                <option value="{{ $valor }}" @selected($valor === ($filtros['modulo'] ?? null))>{{ $texto }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg">
                        <label class="form-label" for="auditoria-usuario">{{ __('Usuario') }}</label>
                        <input id="auditoria-usuario" class="form-control" name="usuario" maxlength="80"
                            placeholder="Nombre o ID" value="{{ $filtros['usuario'] ?? '' }}">
                    </div>
                    <div class="col-6 col-lg">
                        <label class="form-label" for="auditoria-desde">{{ __('Desde') }}</label>
                        <input id="auditoria-desde" class="form-control" name="desde" type="date" value="{{ $filtros['desde'] ?? '' }}">
                    </div>
                    <div class="col-6 col-lg">
                        <label class="form-label" for="auditoria-hasta">{{ __('Hasta') }}</label>
                        <input id="auditoria-hasta" class="form-control" name="hasta" type="date" value="{{ $filtros['hasta'] ?? '' }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-primary"><i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtrar</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-active">
                            <tr>
                                <th scope="col">Fecha y hora</th>
                                <th scope="col">{{ __('Usuario') }}</th>
                                <th scope="col">Nivel</th>
                                <th scope="col">Módulo</th>
                                <th scope="col">Actividad</th>
                                <th scope="col">{{ __('Detalle') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($eventos as $evento)
                                @php
                                    [$clase, $icono] = match ($evento->nivel) {
                                        'error' => ['danger', 'bi-exclamation-octagon'],
                                        'warning' => ['warning', 'bi-exclamation-triangle'],
                                        default => ['info', 'bi-info-circle'],
                                    };
                                    $traza = $evento->detalle_traza;
                                    $fecha = $evento->fecha_local;
                                @endphp
                                <tr>
                                    <td class="text-nowrap">
                                        <time datetime="{{ $fecha->toIso8601String() }}">{{ $fecha->format('d/m/Y H:i:s') }}</time>
                                    </td>
                                    <td>
                                        <div>{{ $evento->usuario_nombre ?? ($evento->id_usuario ? '#'.$evento->id_usuario : 'Sistema') }}</div>
                                        @if ($evento->usuario_nombre && $evento->id_usuario)
                                            <div class="small text-body-secondary">#{{ $evento->id_usuario }}</div>
                                        @endif
                                        @if ($ambito === 'todas')
                                            <div class="small text-body-secondary">{{ $evento->empresa ?? '#'.$evento->id_empresa }}</div>
                                        @endif
                                    </td>
                                    <td><span class="badge text-{{ $clase }}-emphasis bg-{{ $clase }}-subtle">
                                        <i class="bi {{ $icono }} me-1" aria-hidden="true"></i>{{ $niveles[$evento->nivel] }}
                                    </span></td>
                                    <td><span class="fw-semibold">{{ $evento->nombre_modulo }}</span></td>
                                    <td class="text-break">
                                        <div class="fw-semibold">{{ $evento->evento }}</div>
                                        <div class="small text-body-secondary">
                                            {{ $evento->origen === 'http' ? $evento->metodo.' · HTTP '.$evento->estado_http : 'Registro de aplicación' }}
                                            @if ($evento->canal) · {{ $evento->canal }} @endif
                                        </div>
                                        @if ($evento->destino)
                                            <div class="font-monospace small text-break">{{ $evento->destino }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <details class="small">
                                            <summary class="link-primary py-1">Ver detalle</summary>
                                            <dl class="mb-2 mt-2">
                                                @if ($evento->ruta)
                                                    <dt>Ruta</dt><dd class="text-break">{{ $evento->ruta }}</dd>
                                                @endif
                                                @if ($evento->controlador)
                                                    <dt>Controlador de la ruta</dt><dd class="font-monospace text-break">{{ $evento->controlador }}</dd>
                                                @endif
                                                @if (! empty($traza['referencias']))
                                                    <dt>Identificadores de la ruta</dt>
                                                    <dd class="text-break">
                                                        @foreach ($traza['referencias'] as $campo => $id)
                                                            <span class="badge text-body bg-body-secondary">{{ ucfirst($campo) }} #{{ $id }}</span>
                                                        @endforeach
                                                    </dd>
                                                @endif
                                                <dt>Solicitud</dt><dd class="font-monospace text-break">{{ $evento->solicitud }}</dd>
                                                @if ($evento->duracion_ms !== null)
                                                    <dt>Duración</dt><dd>{{ $evento->duracion_ms }} ms · {{ $traza['consultas'] ?? 0 }} consultas</dd>
                                                @endif
                                            </dl>
                                            @if (! empty($traza['pasos']))
                                                <p class="fw-semibold mb-1">Capas observadas durante la ejecución</p>
                                                <ol class="ps-3 mb-2">
                                                    @foreach ($traza['pasos'] as $paso)
                                                        <li class="mb-1 text-break">
                                                            <span class="text-body-secondary">{{ $paso['tipo'] }}</span>
                                                            <span class="d-block font-monospace">{{ $paso['nombre'] }}</span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                <p class="text-body-secondary mb-2">Esta entrada solo tiene datos de la solicitud.</p>
                                            @endif
                                            @if ($traza['limitada'] ?? false)
                                                <p class="text-body-secondary mb-2">La traza alcanzó el límite de captura.</p>
                                            @endif
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-body-secondary py-5">No hay eventos para este filtro.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($eventos->hasPages())
                    <div class="px-3 pt-3 border-top">{{ $eventos->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
