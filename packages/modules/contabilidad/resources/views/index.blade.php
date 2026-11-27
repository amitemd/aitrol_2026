@extends('layouts.adminlte')

@php
    [$titulo, $icono, $alta] = config('contabilidad.secciones.'.$seccion);
    $ruta = 'contabilidad.'.$seccion;
    $campos = match ($seccion) {
        'asientos' => ['numero' => 'Asiento', 'fecha' => 'Fecha', 'concepto' => 'Concepto', 'origen' => 'Origen', 'total' => 'Importe USD', 'id_reversion' => 'Estado'],
        'cuentas' => ['codigo' => 'Código', 'nombre' => 'Cuenta', 'clase' => 'Clase', 'auxiliar' => 'Nivel', 'efectivo' => 'Efectivo', 'activo' => 'Estado'],
        'periodos' => ['nombre' => 'Período', 'desde' => 'Desde', 'hasta' => 'Hasta', 'cerrado_at' => 'Estado'],
    };
@endphp
@section('title', $titulo.' | Contabilidad')
@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => $titulo, 'subtitulo' => 'Contabilidad', 'icono' => $icono,
        'crearUrl' => Gate::allows('permiso', [$ruta.'.index', 'escribir']) ? route($ruta.'.create') : null, 'crearTexto' => $alta,
    ])
    <div class="app-content"><div class="container-fluid">
        @include('partials.listado-modal', ['iconoModal' => $icono])
        @if (session('status')) <p class="alert alert-success" role="status">{{ session('status') }}</p> @endif
        <div class="card clientes-directorio" data-ajax-list>
            @include('partials.listado-busqueda', ['listadoUrl' => route($ruta.'.index'), 'placeholder' => $seccion === 'asientos' ? 'Concepto o referencia' : 'Código o nombre'])
            <div class="px-3 py-2 small border-bottom text-body-secondary">{{ $registros->total() }} registros</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">{{ $titulo }}</caption>
                    <thead class="clientes-tabla-cabecera"><tr>@foreach ($campos as $etiqueta)<th scope="col">{{ $etiqueta }}</th>@endforeach<th scope="col" class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        @forelse ($registros as $fila)
                            <tr>
                                @foreach ($campos as $campo => $etiqueta)
                                    <td>@include('contabilidad::valor', ['valor' => $fila[$campo] ?? null])</td>
                                @endforeach
                                <td class="text-end text-nowrap">
                                    <a href="{{ route($ruta.'.show', $fila['id']) }}" data-ajax-open="Detalle contable" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    @php
                                        $accion = match ($seccion) {
                                            'cuentas' => ['editar', 'Editar'],
                                            'periodos' => $fila['cerrado_at'] ? null : ['cerrar', 'Cerrar período'],
                                            'asientos' => $fila['id_reversion'] || $fila['id_reversa_de'] || $fila['origen'] ? null : ['revertir', 'Revertir'],
                                        };
                                    @endphp
                                    @if ($accion && Gate::allows('permiso', [$ruta.'.index', 'actualizar']))
                                        <a href="{{ route($ruta.'.'.$accion[0].'.formulario', $fila['id']) }}" data-ajax-open="{{ $accion[1] }}" class="btn btn-sm btn-outline-primary">{{ $accion[1] }}</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($campos) + 1 }}">@include('contabilidad::vacio', ['texto' => $filtros ? 'No hay resultados con estos filtros' : 'Todavía no hay registros'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($registros->hasPages())<div class="card-footer">{{ $registros->links('pagination::bootstrap-5') }}</div>@endif
        </div>
    </div></div>
@endsection
