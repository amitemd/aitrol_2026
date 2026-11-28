@extends('layouts.adminlte')

@php
    [$titulo, $icono] = config('inventarios.secciones.'.$seccion);
    $ruta = 'inventarios.'.$seccion;
    $catalogo = in_array($seccion, config('inventarios.catalogos', ['productos', 'bodegas']), true);
    $existencias = in_array($seccion, ['existencias', 'reportes'], true);
    $alta = ['productos' => 'Nuevo producto', 'bodegas' => 'Nueva bodega', 'movimientos' => 'Nuevo movimiento',
        'ajustes' => 'Nuevo ajuste', 'categorias' => 'Nueva categoría', 'plantillas-contables' => 'Nueva plantilla contable',
        'marcas' => 'Nueva marca', 'unidades' => 'Nueva unidad'];
    $columnas = match ($seccion) {
        'productos' => ['Código', 'Producto', 'Categoría', 'Unidad', 'Stock mínimo', 'Estado', 'Acciones'],
        'bodegas', 'categorias', 'plantillas-contables', 'marcas', 'unidades' => ['Código', 'Nombre', 'Descripción', 'Estado', 'Acciones'],
        'existencias' => ['Producto', 'Bodega', 'Existencia', 'Stock mínimo', 'Costo promedio', 'Estado'],
        'reportes' => ['Producto', 'Bodega', 'Existencia', 'Stock mínimo', 'Costo promedio', 'Valor de existencia'],
        'kardex' => ['Fecha', 'Producto', 'Bodega', 'Operación', 'Variación', 'Saldo anterior', 'Saldo', 'Costo promedio'],
        default => ['Fecha', 'Producto', 'Tipo', 'Bodega', 'Cantidad', 'Motivo', 'Acciones'],
    };
@endphp

@section('title', $titulo.' | Inventarios')

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => $titulo, 'subtitulo' => 'Inventarios', 'icono' => $icono,
        'crearUrl' => isset($alta[$seccion]) && Gate::allows('permiso', [$ruta.'.index', 'escribir']) ? route($ruta.'.create') : null,
        'crearTexto' => $alta[$seccion] ?? null,
        'abrirAlta' => $seccion === 'categorias' && request()->boolean('nueva'),
    ])
    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal', ['iconoModal' => $icono])
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            <div class="card clientes-directorio" data-ajax-list>
                <form method="GET" action="{{ route($ruta.'.index') }}" class="row g-2 align-items-end p-3 m-0 border-bottom" data-ajax-search>
                    <div class="col-12 col-md-3">
                        <label class="form-label" for="inventario-q">{{ __('Buscar') }}</label>
                        <input id="inventario-q" name="q" class="form-control" value="{{ $filtros['q'] ?? '' }}"
                            placeholder="Código o nombre" maxlength="120">
                    </div>
                    @if ($catalogo)
                        <div class="col-12 col-sm-5 col-md-2">
                            <label class="form-label" for="inventario-activo">{{ __('Estado') }}</label>
                            <select class="form-select" id="inventario-activo" name="activo">
                                <option value="">Todos</option>
                                <option value="1" @selected((string) ($filtros['activo'] ?? '') === '1')>Activos</option>
                                <option value="0" @selected((string) ($filtros['activo'] ?? '') === '0')>Inactivos</option>
                            </select>
                        </div>
                    @else
                        @foreach (['id_producto' => ['Producto', 'productos'], 'id_bodega' => ['Bodega', 'bodegas']] as $campo => [$etiqueta, $origen])
                            <div class="col-12 col-sm-6 col-md-3 col-xl-2">
                                <label class="form-label" for="filtro-{{ $campo }}">{{ __($etiqueta) }}</label>
                                <select class="form-select" id="filtro-{{ $campo }}" name="{{ $campo }}"
                                    data-select2-url="{{ route($ruta.'.opciones', $origen) }}" data-select2-min="0">
                                    <option value="">Todos</option>
                                    @isset($opciones[$campo])
                                        <option value="{{ $opciones[$campo]->id }}" selected>{{ $opciones[$campo]->codigo }} · {{ $opciones[$campo]->nombre }}</option>
                                    @endisset
                                </select>
                            </div>
                        @endforeach
                        @if ($existencias)
                            <div class="col-12 col-sm-6 col-md-auto pb-2">
                                <label class="form-check m-0"><input type="checkbox" class="form-check-input" name="bajo_minimo" value="1" @checked($filtros['bajo_minimo'] ?? false)> Stock al mínimo o por debajo</label>
                            </div>
                        @else
                            @foreach (['desde' => 'Desde', 'hasta' => 'Hasta'] as $campo => $etiqueta)
                                <div class="col-6 col-md-auto">
                                    <label class="form-label" for="filtro-{{ $campo }}">{{ __($etiqueta) }}</label>
                                    <input type="date" id="filtro-{{ $campo }}" name="{{ $campo }}" class="form-control" value="{{ $filtros[$campo] ?? '' }}">
                                </div>
                            @endforeach
                        @endif
                    @endif
                    <div class="col-auto d-flex gap-2">
                        <button class="btn btn-outline-primary">Filtrar</button>
                        @if (array_diff_key($filtros, ['page' => true, 'per_page' => true]))
                            <a href="{{ route($ruta.'.index') }}" class="btn btn-outline-secondary" data-ajax-list-link>{{ __('Limpiar') }}</a>
                        @endif
                    </div>
                </form>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom px-3 py-2 small text-body-secondary">
                    <span>{{ $registros->total() }} {{ $registros->total() === 1 ? 'registro' : 'registros' }}</span>
                    @if ($seccion === 'reportes')
                        <a class="btn btn-sm btn-outline-primary" href="{{ route($ruta.'.exportar', array_diff_key($filtros, ['page' => true, 'per_page' => true])) }}">
                            <i class="bi bi-download me-1" aria-hidden="true"></i> Exportar CSV
                        </a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <caption class="visually-hidden">{{ $titulo }} de inventario</caption>
                        <thead class="clientes-tabla-cabecera"><tr>
                            @foreach ($columnas as $columna)
                                @if ($columna === 'Acciones') @include('partials.auditoria-cabeceras') @endif
                                <th scope="col">{{ $columna }}</th>
                            @endforeach
                        </tr></thead>
                        <tbody>
                            @forelse ($registros as $fila)
                                <tr>
                                    @if ($catalogo)
                                        <th scope="row" class="text-nowrap">{{ $fila['codigo'] }}</th>
                                        <td>{{ $fila['nombre'] }}
                                            @if (!empty($fila['codigo_barras']))
                                                <small class="d-block text-body-secondary">{{ $fila['codigo_barras'] }}</small>
                                            @endif
                                        </td>
                                        @if ($seccion === 'productos')
                                            <td>{{ $fila['categoria'] ?? '—' }}</td>
                                            <td>{{ $fila['unidad'] }}</td>
                                            <td>{{ $fila['stock_minimo'] }}</td>
                                        @else
                                            <td>{{ $fila['descripcion'] ?? '—' }}</td>
                                        @endif
                                        <td><span @class(['badge rounded-pill', 'text-bg-success' => $fila['activo'], 'text-bg-secondary' => !$fila['activo']])>{{ $fila['activo'] ? 'Activo' : 'Inactivo' }}</span></td>
                                        @include('partials.auditoria-celdas', ['filaAuditoria' => $fila])
                                        <td>
                                            @component('partials.acciones-fila', [
                                                'ver' => route($ruta.'.show', $fila['id']),
                                                'editar' => Gate::allows('permiso', [$ruta.'.index', 'actualizar']) ? route($ruta.'.edit', $fila['id']) : null,
                                                'tituloEditar' => str_replace(['Nuevo ', 'Nueva '], 'Editar ', $alta[$seccion]),
                                            ])
                                                @if ($seccion === 'productos')
                                                    <a class="dropdown-item d-flex gap-2 align-items-center" href="{{ route('inventarios.productos.historial', $fila['id']) }}">
                                                        <i class="bi bi-clock-history" aria-hidden="true"></i> Historial de movimientos
                                                    </a>
                                                @endif
                                                @can('permiso', [$ruta.'.index', 'actualizar'])
                                                    <form method="POST" action="{{ route($ruta.'.estado', $fila['id']) }}" data-ajax-action>
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="activo" value="{{ $fila['activo'] ? 0 : 1 }}">
                                                        <button type="submit" @class(['dropdown-item d-flex gap-2 align-items-center', 'accion-fila-desactivar' => $fila['activo'], 'accion-fila-activar' => !$fila['activo']])>
                                                            <i class="bi {{ $fila['activo'] ? 'bi-toggle-off' : 'bi-toggle-on' }}" aria-hidden="true"></i>
                                                            {{ $fila['activo'] ? 'Desactivar' : 'Activar' }}
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endcomponent
                                        </td>
                                    @elseif ($existencias)
                                        <th scope="row">{{ $fila['producto'] }} <small class="d-block text-body-secondary">{{ $fila['codigo_producto'] }}</small></th>
                                        <td>{{ $fila['bodega'] }}</td>
                                        <td class="text-nowrap">{{ $fila['cantidad'] }} {{ $fila['unidad'] }}</td>
                                        <td>{{ $fila['stock_minimo'] }}</td>
                                        <td>{{ $fila['costo_promedio'] }}</td>
                                        @if ($seccion === 'reportes')
                                            <td>{{ $fila['valor_existencia'] }}</td>
                                        @else
                                            <td><span @class(['badge rounded-pill', 'text-bg-warning' => $fila['bajo_minimo'], 'text-bg-success' => !$fila['bajo_minimo']])>{{ $fila['bajo_minimo'] ? 'Al mínimo o por debajo' : 'Disponible' }}</span></td>
                                        @endif
                                    @else
                                        <td class="text-nowrap">{{ $fila['created_at'] }}</td>
                                        <th scope="row">{{ $fila['producto'] }} <small class="d-block text-body-secondary">{{ $fila['codigo_producto'] }}</small></th>
                                        @if ($seccion === 'kardex')
                                            <td>{{ $fila['bodega'] }}</td>
                                            <td>{{ config('inventarios.tipos.'.$fila['tipo']) }} <small class="d-block text-body-secondary">{{ $fila['motivo'] }}</small>
                                                @include('partials.referencia-asiento', ['registroAsiento' => $fila])
                                            </td>
                                            <td @class(['text-nowrap', 'text-success-emphasis' => $fila['variacion'] > 0, 'text-danger-emphasis' => $fila['variacion'] < 0])>{{ $fila['variacion'] > 0 ? '+' : '' }}{{ $fila['variacion'] }}</td>
                                            <td>{{ $fila['saldo_anterior'] }}</td>
                                            <td>{{ $fila['saldo'] }}</td>
                                            <td>{{ $fila['costo_promedio'] }}</td>
                                        @else
                                            <td>{{ config('inventarios.tipos.'.$fila['tipo']) }}</td>
                                            <td>{{ $fila['bodega'] }}@if ($fila['bodega_destino']) <i class="bi bi-arrow-right" aria-label="a"></i> {{ $fila['bodega_destino'] }}@endif</td>
                                            <td>{{ $fila['cantidad'] }}</td>
                                            <td>{{ $fila['motivo'] }} @if ($fila['referencia']) <small class="d-block text-body-secondary">{{ $fila['referencia'] }}</small>@endif
                                                @include('partials.referencia-asiento', ['registroAsiento' => $fila])
                                            </td>
                                            @include('partials.auditoria-celdas', ['filaAuditoria' => $fila])
                                            <td>@component('partials.acciones-fila', ['ver' => route($ruta.'.show', $fila['id'])])@endcomponent</td>
                                        @endif
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="{{ count($columnas) + (in_array('Acciones', $columnas, true) ? 4 : 0) }}" class="text-center clientes-vacio">
                                    <span class="clientes-icono mx-auto mb-3" aria-hidden="true"><i class="bi {{ $icono }}"></i></span>
                                    <p class="fw-semibold mb-1">{{ array_diff_key($filtros, ['page' => true, 'per_page' => true]) ? 'No hay resultados con estos filtros' : 'Todavía no hay registros' }}</p>
                                    @if (!$filtros && $existencias)
                                        <p class="text-body-secondary mb-0">Las existencias aparecen al registrar movimientos de inventario.</p>
                                    @endif
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($registros->hasPages())
                    <div class="card-footer">{{ $registros->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
