@extends('layouts.adminlte')

@php
    [$titulo, $icono] = config('contabilidad.reportes.'.$reporte);
    $ruta = 'contabilidad.reportes.'.$reporte;
    $etiquetas = $columnas + [
        'ingresos' => 'Ingresos', 'gastos' => 'Gastos', 'resultado' => 'Resultado',
        'activos' => 'Activos', 'pasivos' => 'Pasivos', 'patrimonio' => 'Patrimonio total', 'patrimonio_registrado' => 'Patrimonio registrado',
        'resultado_acumulado' => 'Resultado acumulado pendiente de traspaso', 'pasivo_patrimonio' => 'Pasivo más patrimonio',
        'diferencia' => 'Diferencia', 'apertura' => 'Saldo inicial', 'cierre' => 'Saldo final', 'neto' => 'Variación del efectivo',
    ];
@endphp
@section('title', $titulo.' | Contabilidad')
@section('content')
    @include('partials.listado-cabecera', ['titulo' => $titulo, 'subtitulo' => 'Reportes contables · USD', 'icono' => $icono])
    <div class="app-content"><div class="container-fluid">
        @include('partials.listado-modal', ['iconoModal' => $icono])
        <div class="card clientes-directorio" data-ajax-list>
            <form method="GET" action="{{ route($ruta) }}" data-ajax-search class="row g-2 align-items-end m-0 p-3 border-bottom">
                @if ($reporte === 'mayorizacion')
                    <div class="col-sm-6 col-xl-3">
                        <label class="form-label" for="co-filtro-cuenta">{{ __('Cuenta') }}</label>
                        <select id="co-filtro-cuenta" name="id_cuenta" data-select2-min="0" data-select2-placeholder="Todas las cuentas"
                            data-select2-url="{{ route('contabilidad.reportes.opciones') }}" class="form-select">
                            <option value="">Todas las cuentas</option>
                            @if ($cuenta)<option value="{{ $cuenta->id }}" selected>{{ $cuenta->codigo }} · {{ $cuenta->nombre }}</option>@endif
                        </select>
                    </div>
                @endif
                @if ($filtraCentro)
                    <div class="col-sm-6 col-xl-3">
                        <label class="form-label" for="co-filtro-centro">Centro de costos</label>
                        <select id="co-filtro-centro" name="id_centro_costo" class="form-select">
                            <option value="">Todos los centros</option>
                            <option value="0" @selected((string) ($filtros['id_centro_costo'] ?? '') === '0')>Sin centro asignado</option>
                            @foreach ($centros as $centro)
                                <option value="{{ $centro->id }}" @selected((int) ($filtros['id_centro_costo'] ?? 0) === (int) $centro->id)>
                                    {{ $centro->codigo }} · {{ $centro->nombre }}{{ $centro->activo ? '' : ' (inactivo)' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @foreach ($reporte === 'balance-financiero' ? ['hasta' => 'Fecha de corte'] : ['desde' => 'Desde', 'hasta' => 'Hasta'] as $campo => $etiqueta)
                    <div class="col-sm-auto">
                        <label class="form-label" for="co-{{ $campo }}">{{ __($etiqueta) }}</label>
                        <input type="date" id="co-{{ $campo }}" name="{{ $campo }}" class="form-control" value="{{ $filtros[$campo] }}" required>
                    </div>
                @endforeach
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-outline-primary">Consultar</button>
                    <a href="{{ route($ruta.'.exportar', $filtros) }}" class="btn btn-outline-secondary"><i class="bi bi-download me-1" aria-hidden="true"></i>CSV</a>
                </div>
            </form>
            @if ($totales)
                <dl class="row g-2 m-0 px-3 py-2 border-bottom">
                    @foreach ($totales as $campo => $valor)
                        <div class="col-6 col-md-4 col-xl-3">
                            <div class="bg-body-tertiary rounded border p-2 h-100">
                                <dt class="small text-body-secondary">{{ $etiquetas[$campo] ?? $campo }}</dt><dd class="mb-0 fw-semibold">{{ $valor }}</dd>
                            </div>
                        </div>
                    @endforeach
                </dl>
            @endif
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">{{ $titulo }}</caption>
                    <thead class="clientes-tabla-cabecera"><tr>@foreach ($columnas as $etiqueta)<th scope="col">{{ __($etiqueta) }}</th>@endforeach</tr></thead>
                    <tbody>
                        @forelse ($registros as $fila)
                            <tr>@foreach ($columnas as $campo => $etiqueta)
                                <td>@include('contabilidad::valor', ['valor' => $fila[$campo] ?? ($campo === 'centro_costo' ? 'Sin centro asignado' : null)])</td>
                            @endforeach</tr>
                        @empty
                            <tr><td colspan="{{ count($columnas) }}">@include('contabilidad::vacio', ['texto' => 'No hay movimientos para el período seleccionado'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($registros instanceof \Illuminate\Pagination\LengthAwarePaginator && $registros->hasPages())
                <div class="card-footer">{{ $registros->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div></div>
@endsection
