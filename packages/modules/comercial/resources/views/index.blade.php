@extends('layouts.adminlte')

@php
    $config = config('comercial.'.$modulo);
    [$titulo, $icono, $alta] = $config['secciones'][$seccion];
    $ruta = $modulo.'.'.$seccion;
    $reportes = $seccion === 'reportes';
@endphp
@section('title', $titulo.' | '.$config['titulo'])
@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => $titulo, 'subtitulo' => $config['titulo'], 'icono' => $icono,
        'crearUrl' => $alta && Gate::allows('permiso', [$ruta.'.index', 'escribir']) ? route($ruta.'.create') : null,
        'crearTexto' => $alta,
    ])
    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal', ['iconoModal' => $icono])
            @if (session('status')) <p class="alert alert-success" role="status">{{ session('status') }}</p> @endif
            <div class="card clientes-directorio" data-ajax-list>
                <form method="GET" action="{{ route($ruta.'.index') }}" class="row g-2 align-items-end m-0 p-3 border-bottom" data-ajax-search>
                    <div class="col-12 col-sm-5 col-lg-4">
                        <label class="form-label" for="com-q">{{ __('Buscar') }}</label>
                        <input class="form-control" id="com-q" name="q" value="{{ $filtros['q'] ?? '' }}" maxlength="120" placeholder="Número o {{ Str::lower($config['tercero']) }}">
                    </div>
                    @foreach (['desde' => 'Desde', 'hasta' => 'Hasta'] as $campo => $etiqueta)
                        <div class="col-6 col-sm-3 col-lg-auto">
                            <label class="form-label" for="com-{{ $campo }}">{{ __($etiqueta) }}</label>
                            <input type="date" id="com-{{ $campo }}" name="{{ $campo }}" class="form-control" value="{{ $filtros[$campo] ?? '' }}">
                        </div>
                    @endforeach
                    <div class="col-auto d-flex gap-2">
                        <button class="btn btn-outline-primary">Filtrar</button>
                        @if ($filtros)<a class="btn btn-outline-secondary" href="{{ route($ruta.'.index') }}" data-ajax-list-link>{{ __('Limpiar') }}</a>@endif
                        @if ($reportes)
                            <a class="btn btn-outline-primary" href="{{ route($ruta.'.exportar', $filtros) }}">
                                <i class="bi bi-download me-1" aria-hidden="true"></i> Exportar CSV
                            </a>
                        @endif
                    </div>
                </form>
                @if ($reportes)
                    <div class="row g-2 m-0 p-3 border-bottom">
                        @foreach (['neto' => 'Importe neto', 'retenciones' => 'Retenciones vigentes', 'neto_menos_retenciones' => 'Neto menos retenciones'] as $campo => $etiqueta)
                            <div class="col-sm-4">
                                <div class="rounded border bg-body-tertiary p-3 h-100">
                                    <div class="small text-body-secondary">{{ __($etiqueta) }}</div>
                                    <strong class="fs-5">{{ $reporte[$campo] }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <caption class="visually-hidden">Totales por tipo de documento</caption>
                            <thead class="clientes-tabla-cabecera"><tr><th>{{ __('Tipo') }}</th><th>{{ __('Estado') }}</th><th class="text-end">{{ __('Cantidad') }}</th><th class="text-end">Subtotal</th><th class="text-end">{{ __('Impuesto') }}</th><th class="text-end">{{ __('Total') }}</th></tr></thead>
                            <tbody>
                                @forelse ($reporte['filas'] as $fila)
                                    <tr>
                                        <th scope="row">{{ $config['secciones'][$fila['tipo']][0] }}</th>
                                        <td>{{ $fila['anulado'] ? 'Anulado' : 'Vigente' }}</td>
                                        @foreach (['cantidad', 'subtotal', 'impuesto', 'total'] as $campo)<td class="text-end">{{ $fila[$campo] }}</td>@endforeach
                                    </tr>
                                @empty
                                    <tr><td colspan="6">@include('comercial::vacio')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-3 py-2 small text-body-secondary border-bottom">{{ $registros->total() }} {{ $registros->total() === 1 ? 'documento' : 'documentos' }}</div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <caption class="visually-hidden">{{ $titulo }}</caption>
                            <thead class="clientes-tabla-cabecera">
                                <tr><th>Número</th><th>{{ __('Fecha') }}</th><th>{{ $config['tercero'] }}</th><th class="text-end">{{ __('Total') }}</th><th>{{ __('Estado') }}</th><th class="text-end">{{ __('Acciones') }}</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($registros as $fila)
                                    <tr>
                                        <th scope="row">{{ $fila['numero'] }}
                                            @include('partials.referencia-asiento', ['registroAsiento' => $fila])
                                            @if ($seccion === 'anulaciones')
                                                <small class="d-block text-body-secondary">{{ $config['secciones'][$fila['tipo']][0] }}</small>
                                            @endif
                                        </th>
                                        <td class="text-nowrap">{{ $fila['fecha'] }}</td>
                                        <td>{{ $fila['tercero'] }}</td>
                                        <td class="text-end">{{ $fila['total'] }}</td>
                                        <td>
                                            <span @class(['badge rounded-pill', 'text-bg-secondary' => $fila['anulado_at'], 'text-bg-success' => !$fila['anulado_at']])>{{ $fila['anulado_at'] ? 'Anulado' : 'Vigente' }}</span>
                                            @if ($seccion === 'anulaciones') <small class="d-block text-body-secondary">{{ $fila['anulado_at'] }}</small> @endif
                                        </td>
                                        <td class="text-end">
                                            @component('partials.acciones-fila', ['ver' => route($ruta.'.show', $fila['id']), 'tituloVer' => 'Documento '.$fila['numero']])
                                                @if (!$fila['anulado_at'] && Gate::allows('permiso', [$ruta.'.index', 'actualizar']))
                                                    @include('partials.accion-fila-enlace', ['href' => route($ruta.'.confirmar', $fila['id']), 'etiqueta' => 'Anular', 'titulo' => 'Anular documento', 'icono' => 'bi-x-circle'])
                                                @endif
                                            @endcomponent
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">@include('comercial::vacio')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($registros->hasPages())
                        <div class="card-footer">{{ $registros->links('pagination::bootstrap-5') }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
