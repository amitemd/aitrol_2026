@extends('layouts.adminlte')

@php
    [$titulo, $icono, $alta] = $seccion === 'nomina' ? ['Datos de nómina', 'bi-people', 'Registrar trabajador'] : config('tributario.secciones.'.$seccion);
    $ruta = 'tributario.'.$seccion;
    $permiso = 'tributario.'.($seccion === 'nomina' ? 'formulario-107' : $seccion).'.index';
    $formato = in_array($seccion, [...config('tributario.formatos'), 'declaraciones'], true);
    $columnas = match ($seccion) {
        'nomina' => ['ejercicio' => 'Ejercicio', 'identificacion' => 'Identificación', 'nombre' => 'Trabajador', 'ingresos_gravados' => 'Ingresos gravados', 'retencion' => 'Retención'],
        'documentos' => ['nombre' => 'Archivo', 'tipo' => 'Tipo', 'formato' => 'Obligación', 'desde' => 'Período', 'created_at' => 'Cargado el'],
        'procesos' => ['formato' => 'Obligación', 'desde' => 'Período', 'accion' => 'Evento', 'detalle' => 'Detalle', 'created_at' => 'Fecha'],
        default => ['formato' => 'Obligación', 'desde' => 'Desde', 'hasta' => 'Hasta', 'estado' => 'Estado', 'referencia_externa' => 'Referencia externa'],
    };
@endphp
@section('title', $titulo.' | Tributario')
@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => $titulo, 'subtitulo' => 'Tributario', 'icono' => $icono,
        'crearUrl' => $alta && Gate::allows('permiso', [$permiso, 'escribir']) ? route($ruta.'.create') : null, 'crearTexto' => $alta,
    ])
    <div class="app-content"><div class="container-fluid">
        @include('partials.listado-modal', ['iconoModal' => $icono])
        @if (session('status'))<p class="alert alert-success" role="status">{{ session('status') }}</p>@endif
        <div class="card clientes-directorio" data-ajax-list>
            <form method="GET" action="{{ route($ruta.'.index') }}" data-ajax-search class="row g-2 align-items-end m-0 p-3 border-bottom">
                <div class="col-6 col-sm-3"><label for="tr-filtro-anio" class="form-label">Ejercicio</label>
                    <input id="tr-filtro-anio" type="number" min="1900" max="2100" name="ejercicio" class="form-control" value="{{ $filtros['ejercicio'] ?? '' }}">
                </div>
                @if ($formato)
                    <div class="col-6 col-sm-4">
                        <label for="tr-filtro-estado" class="form-label">{{ __('Estado') }}</label>
                        <select id="tr-filtro-estado" name="estado" class="form-select"><option value="">Todos</option>
                            @foreach (config('tributario.estados') as $estado => $texto)<option value="{{ $estado }}" @selected(($filtros['estado'] ?? '') === $estado)>{{ $texto }}</option>@endforeach
                        </select>
                    </div>
                @endif
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-outline-primary">Filtrar</button>
                    @if ($filtros)<a class="btn btn-outline-secondary" href="{{ route($ruta.'.index') }}" data-ajax-list-link>{{ __('Limpiar') }}</a>@endif
                    @if ($seccion === 'formulario-107')<a class="btn btn-outline-secondary" href="{{ route('tributario.nomina.index') }}" data-workspace-link="tributario.nomina.index"><i class="bi bi-people me-1" aria-hidden="true"></i>{{ __('Nómina') }}</a>@endif
                    @if ($seccion === 'nomina')<a class="btn btn-outline-secondary" href="{{ route('tributario.formulario-107.index') }}" data-workspace-link="tributario.formulario-107.index">{{ __('Formulario 107') }}</a>@endif
                </div>
            </form>
            @if ($seccion === 'procesos')
                <p class="small mb-0 border-bottom px-3 py-2 text-body-secondary">Historial de preparación, revisión, respaldos y constancias registradas.</p>
            @endif
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">{{ $titulo }}</caption>
                    <thead class="clientes-tabla-cabecera"><tr>@foreach ($columnas as $etiqueta)<th scope="col">{{ __($etiqueta) }}</th>@endforeach
                        @include('partials.auditoria-cabeceras')
                        @if ($formato || $seccion === 'documentos')<th scope="col" class="text-end">{{ __('Acciones') }}</th>@endif
                    </tr></thead>
                    <tbody>
                        @forelse ($registros as $fila)
                            <tr>
                                @foreach ($columnas as $campo => $etiqueta)
                                    <td>
                                        @switch($campo)
                                            @case('formato'){{ config('tributario.secciones.'.$fila[$campo].'.0') }}@break
                                            @case('tipo'){{ config('tributario.archivos.'.$fila[$campo]) }}@break
                                            @case('estado')
                                                <span @class(['badge rounded-pill', 'text-bg-secondary' => $fila[$campo] === 'borrador', 'text-bg-info' => $fila[$campo] === 'revisado',
                                                    'text-bg-success' => in_array($fila[$campo], ['presentado', 'entregado'], true)])>{{ config('tributario.estados.'.$fila[$campo]) }}</span>
                                                @break
                                            @case('accion'){{ ['preparado' => 'Preparado', 'revisado' => 'Revisado', 'borrador' => 'Reabierto', 'respaldo_adjunto' => 'Respaldo adjunto',
                                                'presentado' => 'Presentación registrada', 'entregado' => 'Entrega registrada'][$fila[$campo]] ?? $fila[$campo] }}@break
                                            @default{{ $fila[$campo] ?? '—' }}
                                        @endswitch
                                    </td>
                                @endforeach
                                @include('partials.auditoria-celdas', ['filaAuditoria' => $fila])
                                @if ($formato)
                                    <td class="text-end">
                                        @component('partials.acciones-fila', ['ver' => route($ruta.'.show', $fila['id']), 'tituloVer' => 'Papel de trabajo'])
                                            @include('partials.accion-fila-enlace', ['href' => route($ruta.'.exportar', $fila['id']), 'etiqueta' => 'Exportar CSV', 'icono' => 'bi-download', 'modal' => false])
                                            @if (Gate::allows('permiso', [$permiso, 'actualizar']) && Gate::allows('permiso', ['tributario.'.$fila['formato'].'.index', 'actualizar']))
                                                @if ($fila['estado'] === 'borrador')
                                                    @include('partials.accion-fila-enlace', ['href' => route($ruta.'.revisar.formulario', $fila['id']), 'etiqueta' => 'Marcar revisado', 'icono' => 'bi-check2'])
                                                @elseif ($fila['estado'] === 'revisado')
                                                    @include('partials.accion-fila-enlace', ['href' => route($ruta.'.reabrir.formulario', $fila['id']), 'etiqueta' => 'Reabrir borrador', 'icono' => 'bi-arrow-counterclockwise'])
                                                    @include('partials.accion-fila-enlace', ['href' => route($ruta.'.registrar-entrega.formulario', $fila['id']),
                                                        'etiqueta' => $fila['formato'] === 'formulario-107' ? 'Registrar entrega' : 'Registrar presentación', 'icono' => 'bi-receipt'])
                                                @endif
                                            @endif
                                        @endcomponent
                                    </td>
                                @elseif ($seccion === 'documentos')
                                    <td class="text-end"><a href="{{ route('tributario.documentos.archivo', $fila['id']) }}" class="btn btn-sm btn-outline-secondary accion-fila-icono"
                                        aria-label="Descargar respaldo" title="Descargar respaldo"><i class="bi bi-download" aria-hidden="true"></i></a></td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($columnas) + 4 + (($formato || $seccion === 'documentos') ? 1 : 0) }}">
                                @include('contabilidad::vacio', ['texto' => 'Todavía no hay registros para la selección'])
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($registros->hasPages())<div class="card-footer">{{ $registros->links('pagination::bootstrap-5') }}</div>@endif
        </div>
    </div></div>
@endsection
