@extends('layouts.adminlte')

@php
    [$titulo, $icono, $alta] = config('caja-bancos.secciones.'.$seccion);
    $ruta = 'caja-bancos.'.$seccion;
    $operacion = array_key_exists($seccion, config('caja-bancos.operaciones'));
    $campos = match ($seccion) {
        'cuentas' => ['codigo' => 'Código', 'nombre' => 'Cuenta', 'tipo' => 'Tipo', 'banco' => 'Banco', 'moneda' => 'Moneda', 'saldo' => 'Saldo en libros', 'activo' => 'Estado'],
        'cierres' => ['cuenta' => 'Caja', 'created_at' => 'Apertura', 'cerrado_at' => 'Cierre', 'saldo_apertura' => 'Saldo inicial',
            'saldo_cierre' => 'Saldo al cierre', 'efectivo_contado' => 'Efectivo contado', 'diferencia' => 'Diferencia', 'moneda' => 'Moneda'],
        'extractos' => ['id' => 'N.º', 'nombre' => 'Extracto', 'cuenta' => 'Banco', 'created_at' => 'Importado el',
            'importadas' => 'Importadas', 'omitidas' => 'Duplicadas'],
        'conciliacion' => ['fecha' => 'Fecha', 'cuenta' => 'Banco', 'referencia' => 'Referencia', 'descripcion' => 'Descripción',
            'importe' => 'Importe', 'moneda' => 'Moneda', 'id_asiento' => 'Estado'],
        'reportes' => ['fecha' => 'Fecha', 'cuenta' => 'Cuenta', 'tipo' => 'Tipo', 'concepto' => 'Concepto', 'moneda' => 'Moneda',
            'variacion' => 'Variación', 'saldo_anterior' => 'Saldo anterior', 'saldo' => 'Saldo'],
        default => ['fecha' => 'Fecha', 'tipo' => 'Tipo', 'concepto' => 'Concepto', 'origen' => 'Origen', 'destino' => 'Destino',
            'importe' => 'Importe', 'moneda' => 'Moneda', 'id_reversion' => 'Estado'],
    };
@endphp
@section('title', $titulo.' | Caja y bancos')
@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => $titulo, 'subtitulo' => 'Caja y bancos', 'icono' => $icono,
        'crearUrl' => $alta && Gate::allows('permiso', [$ruta.'.index', 'escribir']) ? route($ruta.'.create') : null,
        'crearTexto' => $alta,
    ])
    <div class="app-content"><div class="container-fluid">
        @include('partials.listado-modal', ['iconoModal' => $icono])
        @if (session('status')) <p class="alert alert-success" role="status">{{ session('status') }}</p> @endif
        <div class="card clientes-directorio" data-ajax-list>
            <form action="{{ route($ruta.'.index') }}" data-ajax-search method="GET" class="row g-2 align-items-end m-0 p-3 border-bottom">
                <div class="col-12 col-sm-4 col-lg-3">
                    <label for="cb-q" class="form-label">{{ __('Buscar') }}</label>
                    <input id="cb-q" name="q" class="form-control" maxlength="120" value="{{ $filtros['q'] ?? '' }}">
                </div>
                @if ($seccion === 'cuentas' || $seccion === 'conciliacion')
                    @php $filtroEstado = $seccion === 'cuentas' ? 'activo' : 'conciliado'; @endphp
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label for="cb-estado" class="form-label">{{ __('Estado') }}</label>
                        <select id="cb-estado" name="{{ $filtroEstado }}" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" @selected((string) ($filtros[$filtroEstado] ?? '') === '1')>{{ $seccion === 'cuentas' ? 'Activo' : 'Conciliado' }}</option>
                            <option value="0" @selected((string) ($filtros[$filtroEstado] ?? '') === '0')>{{ $seccion === 'cuentas' ? 'Inactivo' : 'Pendiente' }}</option>
                        </select>
                    </div>
                @endif
                @if ($seccion === 'conciliacion')
                    <div class="col-6 col-sm-3 col-lg-2">
                        <label class="form-label" for="cb-extracto">N.º de extracto</label>
                        <input type="number" id="cb-extracto" name="id_extracto" min="1" class="form-control" value="{{ $filtros['id_extracto'] ?? '' }}">
                    </div>
                @endif
                @if ($seccion !== 'cuentas')
                    <div class="col-6 col-sm-4 col-lg-3">
                        <label for="cb-filtro-cuenta" class="form-label">{{ __('Cuenta') }}</label>
                        <select id="cb-filtro-cuenta" name="id_cuenta" class="form-select" data-select2-url="{{ route($ruta.'.filtro-cuentas') }}"
                            data-select2-min="0" data-select2-placeholder="Todas las cuentas">
                            <option value="">Todas las cuentas</option>
                            @if ($cuentaFiltro)
                                <option value="{{ $cuentaFiltro->id }}" selected>{{ $cuentaFiltro->codigo }} · {{ $cuentaFiltro->nombre }} ({{ $cuentaFiltro->moneda }})</option>
                            @endif
                        </select>
                    </div>
                    @foreach (['desde' => 'Desde', 'hasta' => 'Hasta'] as $campo => $etiqueta)
                        <div class="col-6 col-sm-auto">
                            <label for="cb-{{ $campo }}" class="form-label">{{ __($etiqueta) }}</label>
                            <input type="date" id="cb-{{ $campo }}" name="{{ $campo }}" class="form-control" value="{{ $filtros[$campo] ?? '' }}">
                        </div>
                    @endforeach
                @endif
                <div class="col-auto d-flex flex-wrap gap-2">
                    <button class="btn btn-outline-primary">Filtrar</button>
                    @if ($filtros)<a href="{{ route($ruta.'.index') }}" data-ajax-list-link class="btn btn-outline-secondary">{{ __('Limpiar') }}</a>@endif
                    @if ($seccion === 'reportes')
                        <a href="{{ route($ruta.'.exportar', $filtros) }}" class="btn btn-outline-primary"><i class="bi bi-download me-1" aria-hidden="true"></i>Exportar CSV</a>
                    @endif
                </div>
            </form>
            @if ($resumen?->isNotEmpty())
                <div class="row g-2 m-0 p-3 border-bottom">
                    @foreach ($resumen as $totales)
                        <div class="col-sm-6 col-lg-4"><dl class="row rounded border bg-body-tertiary m-0 px-2 py-3">
                            <dt class="col-7">Entradas {{ $totales['moneda'] }}</dt><dd class="col-5 text-end">{{ $totales['ingresos'] }}</dd>
                            <dt class="col-7">Salidas {{ $totales['moneda'] }}</dt><dd class="col-5 text-end mb-0">{{ $totales['egresos'] }}</dd>
                        </dl></div>
                    @endforeach
                </div>
            @endif
            <div class="px-3 py-2 small text-body-secondary border-bottom">{{ $registros->total() }} {{ $registros->total() === 1 ? 'registro' : 'registros' }}</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">{{ $titulo }}</caption>
                    <thead class="clientes-tabla-cabecera"><tr>
                        @foreach ($campos as $etiqueta)<th scope="col">{{ __($etiqueta) }}</th>@endforeach
                        @unless ($seccion === 'reportes')<th scope="col" class="text-end">{{ __('Acciones') }}</th>@endunless
                    </tr></thead>
                    <tbody>
                        @forelse ($registros as $fila)
                            <tr>
                                @foreach ($campos as $campo => $etiqueta)
                                    <td>
                                        @include('caja-bancos::valor', ['valor' => $fila[$campo] ?? null])
                                        @if (in_array($campo, ['concepto', 'referencia'], true))
                                            @include('partials.referencia-asiento', ['registroAsiento' => $fila])
                                        @endif
                                    </td>
                                @endforeach
                                @unless ($seccion === 'reportes')
                                    <td class="text-end text-nowrap">
                                        @php
                                            $acciones = match ($seccion) {
                                                'cuentas' => ['editar' => 'Editar'],
                                                'cierres' => $fila['cerrado_at'] ? [] : ['cerrar' => 'Cerrar caja'],
                                                'conciliacion' => $fila['id_asiento'] ? ['desconciliar' => 'Desconciliar'] : ['conciliar' => 'Conciliar'],
                                                default => $operacion && !$fila['id_reversion'] && $fila['tipo'] !== 'reversa' ? ['revertir' => 'Revertir'] : [],
                                            };
                                        @endphp
                                        @component('partials.acciones-fila', ['ver' => route($ruta.'.show', $fila['id'])])
                                            @if ($seccion === 'extractos' && Gate::allows('permiso', ['caja-bancos.conciliacion.index', 'leer']))
                                                @include('partials.accion-fila-enlace', ['href' => route('caja-bancos.conciliacion.index', ['id_extracto' => $fila['id']]),
                                                    'etiqueta' => 'Ver líneas', 'modal' => false, 'workspace' => 'caja-bancos.conciliacion.index', 'workspaceLabel' => 'Conciliación bancaria', 'icono' => 'bi-list-ul'])
                                            @endif
                                            @can('permiso', [$ruta.'.index', 'actualizar'])
                                                @foreach ($acciones as $accion => $etiqueta)
                                                    @include('partials.accion-fila-enlace', ['href' => route($ruta.'.'.$accion.'.formulario', $fila['id']), 'etiqueta' => $etiqueta,
                                                        'icono' => ['editar' => 'bi-pencil', 'cerrar' => 'bi-lock', 'conciliar' => 'bi-check2-square', 'desconciliar' => 'bi-unlock', 'revertir' => 'bi-arrow-counterclockwise'][$accion]])
                                                @endforeach
                                            @endcan
                                        @endcomponent
                                    </td>
                                @endunless
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($campos) + ($seccion === 'reportes' ? 0 : 1) }}" class="text-center clientes-vacio">
                                <span class="clientes-icono mx-auto mb-3" aria-hidden="true"><i class="bi {{ $icono }}"></i></span>
                                <p class="fw-semibold mb-0">{{ $filtros ? 'No hay resultados con estos filtros' : 'Todavía no hay registros' }}</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($registros->hasPages())<div class="card-footer">{{ $registros->links('pagination::bootstrap-5') }}</div>@endif
        </div>
    </div></div>
@endsection
