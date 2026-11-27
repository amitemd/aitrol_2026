@extends('layouts.adminlte')

@php
    [$titulo, $icono, $alta] = config('activos-fijos.secciones.'.$seccion);
    $ruta = 'activos-fijos.'.$seccion;
    $catalogo = in_array($seccion, config('activos-fijos.catalogos'), true);
    $esRegistro = in_array($seccion, ['activos', 'adquisiciones', 'reportes'], true);
    $campos = match ($seccion) {
        'activos' => ['codigo' => 'Código', 'nombre' => 'Activo', 'subtipo' => 'Subtipo', 'ubicacion' => 'Ubicación', 'responsable' => 'Responsable', 'estado' => 'Estado'],
        'adquisiciones' => ['codigo' => 'Código', 'nombre' => 'Activo', 'fecha_adquisicion' => 'Fecha de adquisición', 'factura' => 'Documento de compra', 'proveedor' => 'Proveedor', 'costo' => 'Costo'],
        'reportes' => ['codigo' => 'Código', 'nombre' => 'Activo', 'grupo' => 'Grupo', 'estado' => 'Estado', 'costo' => 'Costo', 'depreciado' => 'Depreciación acumulada', 'valor_libros' => 'Valor en libros'],
        'movimientos' => ['codigo_activo' => 'Código', 'activo' => 'Activo', 'tipo' => 'Movimiento', 'fecha' => 'Fecha', 'motivo' => 'Motivo'],
        'depreciaciones' => ['codigo_activo' => 'Código', 'activo' => 'Activo', 'periodo' => 'Período', 'importe' => 'Cuota', 'acumulado' => 'Acumulado', 'valor_libros' => 'Valor en libros'],
        'ordenes' => ['codigo_activo' => 'Código', 'activo' => 'Activo', 'tipo' => 'Mantenimiento', 'fecha_programada' => 'Fecha programada', 'estado' => 'Estado'],
        'tipos' => ['codigo' => 'Código', 'nombre' => 'Tipo', 'padre' => 'Grupo', 'activo' => 'Estado'],
        'subtipos' => ['codigo' => 'Código', 'nombre' => 'Subtipo', 'padre' => 'Tipo', 'vida_util_meses' => 'Vida útil sugerida', 'activo' => 'Estado'],
        default => ['codigo' => 'Código', 'nombre' => 'Nombre', 'descripcion' => 'Descripción', 'activo' => 'Estado'],
    };
@endphp
@section('title', $titulo.' | Activos fijos')
@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => $titulo, 'subtitulo' => 'Activos fijos', 'icono' => $icono,
        'crearUrl' => $alta && Gate::allows('permiso', [$ruta.'.index', 'escribir']) ? route($ruta.'.create') : null,
        'crearTexto' => $alta,
    ])
    <div class="app-content">
        <div class="container-fluid">
            @include('partials.listado-modal', ['iconoModal' => $icono])
            @if (session('status')) <p class="alert alert-success" role="status">{{ session('status') }}</p> @endif
            <div class="card clientes-directorio" data-ajax-list>
                <form action="{{ route($ruta.'.index') }}" method="GET" data-ajax-search class="row g-2 align-items-end m-0 p-3 border-bottom">
                    <div class="col-12 col-sm-5 col-lg-4">
                        <label for="af-buscar" class="form-label">Buscar</label>
                        <input id="af-buscar" class="form-control" name="q" maxlength="120" value="{{ $filtros['q'] ?? '' }}"
                            placeholder="{{ $esRegistro ? 'Código, nombre, serie o compra' : 'Código o nombre' }}">
                    </div>
                    @if ($catalogo || $esRegistro)
                        <div class="col-6 col-sm-3 col-lg-2">
                            <label for="af-estado" class="form-label">Estado</label>
                            <select id="af-estado" class="form-select" name="activo">
                                <option value="">Todos</option>
                                <option value="1" @selected((string) ($filtros['activo'] ?? '') === '1')>Activos</option>
                                <option value="0" @selected((string) ($filtros['activo'] ?? '') === '0')>{{ $catalogo ? 'Inactivos' : 'Dados de baja' }}</option>
                            </select>
                        </div>
                    @elseif ($seccion === 'ordenes')
                        <div class="col-6 col-sm-3 col-lg-2">
                            <label for="af-estado" class="form-label">Estado</label>
                            <select id="af-estado" name="estado" class="form-select">
                                <option value="">Todos</option>
                                @foreach (config('activos-fijos.estados_orden') as $valor => $texto)
                                    <option value="{{ $valor }}" @selected(($filtros['estado'] ?? null) === $valor)>{{ $texto }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    @if ($esRegistro)
                        @foreach (['desde' => 'Adquirido desde', 'hasta' => 'Adquirido hasta'] as $campo => $etiqueta)
                            <div class="col-6 col-sm-auto">
                                <label for="af-{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                                <input type="date" id="af-{{ $campo }}" class="form-control" name="{{ $campo }}" value="{{ $filtros[$campo] ?? '' }}">
                            </div>
                        @endforeach
                    @endif
                    <div class="col-auto d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary">Filtrar</button>
                        @if ($filtros)<a href="{{ route($ruta.'.index') }}" class="btn btn-outline-secondary" data-ajax-list-link>Limpiar</a>@endif
                        @if ($resumen)<a href="{{ route('activos-fijos.reportes.exportar', $filtros) }}" class="btn btn-outline-primary"><i class="bi bi-download me-1" aria-hidden="true"></i>Exportar CSV</a>@endif
                    </div>
                </form>
                @if ($resumen)
                    <div class="row g-2 m-0 p-3 border-bottom">
                        @foreach (['cantidad' => 'Bienes', 'costo' => 'Costo de adquisición', 'depreciado' => 'Depreciación acumulada', 'valor_libros' => 'Valor en libros'] as $campo => $etiqueta)
                            <div class="col-6 col-lg-3"><div class="rounded border bg-body-tertiary p-3 h-100">
                                <span class="d-block small text-body-secondary">{{ $etiqueta }}</span><strong class="fs-5">{{ $resumen[$campo] }}</strong>
                            </div></div>
                        @endforeach
                    </div>
                @endif
                <div class="px-3 py-2 small text-body-secondary border-bottom">{{ $registros->total() }} {{ $registros->total() === 1 ? 'registro' : 'registros' }}</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <caption class="visually-hidden">{{ $titulo }}</caption>
                        <thead class="clientes-tabla-cabecera"><tr>
                            @foreach ($campos as $etiqueta)<th scope="col">{{ $etiqueta }}</th>@endforeach
                            @unless ($seccion === 'reportes')<th scope="col" class="text-end">Acciones</th>@endunless
                        </tr></thead>
                        <tbody>
                            @forelse ($registros as $fila)
                                <tr>
                                    @foreach ($campos as $campo => $etiqueta)
                                        <td>
                                            @include('activos-fijos::valor', ['valor' => $fila[$campo] ?? null])
                                            @if (($esRegistro && $campo === 'codigo') || (!$esRegistro && $campo === 'codigo_activo'))
                                                @include('partials.referencia-asiento', ['registroAsiento' => $fila])
                                            @endif
                                        </td>
                                    @endforeach
                                    @unless ($seccion === 'reportes')
                                        <td class="text-end text-nowrap">
                                            @component('partials.acciones-fila', ['ver' => route($ruta.'.show', $fila['id'])])
                                                @if (($catalogo || ($seccion === 'activos' && !$fila['fecha_baja']) || ($seccion === 'ordenes' && in_array($fila['estado'], ['pendiente', 'en_proceso'], true))) && Gate::allows('permiso', [$ruta.'.index', 'actualizar']))
                                                    @include('partials.accion-fila-enlace', ['href' => route($ruta.'.edit', $fila['id']),
                                                        'etiqueta' => $seccion === 'ordenes' ? 'Actualizar' : 'Editar'])
                                                @endif
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
        </div>
    </div>
@endsection
