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
@if ($seccion === 'cuentas')
    @push('scripts')
        <script src="{{ asset('plan-cuentas.js') }}?v={{ filemtime(public_path('plan-cuentas.js')) }}" nonce="{{ request()->attributes->get('csp_nonce') }}" defer></script>
    @endpush
@endif
@section('title', $titulo.' | Contabilidad')
@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => $titulo, 'subtitulo' => 'Contabilidad', 'icono' => $icono,
        'crearUrl' => Gate::allows('permiso', [$ruta.'.index', 'escribir']) ? route($ruta.'.create') : null,
        'crearTexto' => $seccion === 'cuentas' ? 'Nueva auxiliar' : $alta,
    ])
    <div class="app-content"><div class="container-fluid">
        @include('partials.listado-modal', ['iconoModal' => $icono])
        @if (session('status')) <p class="alert alert-success" role="status">{{ session('status') }}</p> @endif
        <div class="card clientes-directorio" data-ajax-list>
            @include('partials.listado-busqueda', ['listadoUrl' => route($ruta.'.index'), 'placeholder' => $seccion === 'asientos' ? 'Concepto o referencia' : 'Código o nombre'])
            <div class="px-3 py-2 small border-bottom text-body-secondary">{{ $seccion === 'cuentas' ? $registros->count() : $registros->total() }} registros</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">{{ $titulo }}</caption>
                    <thead class="clientes-tabla-cabecera"><tr>@foreach ($campos as $etiqueta)<th scope="col">{{ __($etiqueta) }}</th>@endforeach
                        @include('partials.auditoria-cabeceras')
                        <th scope="col" class="text-end">{{ __('Acciones') }}</th></tr></thead>
                    <tbody @if ($seccion === 'cuentas') data-plan-cuentas @endif>
                        @forelse ($registros as $fila)
                            <tr @if ($seccion === 'cuentas')
                                data-cuenta-nivel="{{ $fila['nivel_arbol'] }}"
                                @if (!$buscando && $fila['nivel_arbol'] > 0) hidden @endif
                            @endif>
                                @foreach ($campos as $campo => $etiqueta)
                                    @if ($seccion === 'cuentas' && $campo === 'nombre')
                                        <th scope="row" class="plan-cuenta-nombre {{ $fila['auxiliar'] ? 'fw-normal' : 'fw-semibold' }}">
                                            <div class="d-flex align-items-center gap-2">
                                                @for ($nivel = 0; $nivel < $fila['nivel_arbol']; $nivel++)
                                                    <span class="plan-cuenta-sangria" aria-hidden="true"></span>
                                                @endfor
                                                @if ($fila['tiene_hijas'])
                                                    <button type="button" class="btn btn-sm btn-outline-secondary accion-fila-icono plan-cuenta-expansion"
                                                        data-plan-expandir aria-expanded="{{ $buscando ? 'true' : 'false' }}" aria-label="Subcuentas de {{ $fila['nombre'] }}">
                                                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                                    </button>
                                                @else
                                                    <span class="plan-cuenta-indicador" aria-hidden="true">
                                                        <i class="bi {{ $fila['auxiliar'] ? 'bi-file-earmark-text' : 'bi-folder2' }}"></i>
                                                    </span>
                                                @endif
                                                <span>{{ $fila['nombre'] }}</span>
                                            </div>
                                        </th>
                                    @else
                                        <td>@include('contabilidad::valor', ['valor' => $fila[$campo] ?? null])</td>
                                    @endif
                                @endforeach
                                @include('partials.auditoria-celdas', ['filaAuditoria' => $fila])
                                <td class="text-end text-nowrap">
                                    @php
                                        $accion = match ($seccion) {
                                            'cuentas' => $fila['auxiliar'] ? ['editar', 'Editar'] : null,
                                            'periodos' => $fila['cerrado_at'] ? null : ['cerrar', 'Cerrar período'],
                                            'asientos' => $fila['id_reversion'] || $fila['id_reversa_de'] || $fila['origen'] ? null : ['revertir', 'Revertir'],
                                        };
                                    @endphp
                                    @component('partials.acciones-fila', ['ver' => route($ruta.'.show', $fila['id']), 'tituloVer' => 'Detalle contable'])
                                        @if ($seccion === 'cuentas' && !$fila['auxiliar'] && $fila['activo'] && Gate::allows('permiso', [$ruta.'.index', 'escribir']))
                                            @include('partials.accion-fila-enlace', ['href' => route($ruta.'.create', ['id_padre' => $fila['id']]),
                                                'etiqueta' => 'Agregar auxiliar', 'titulo' => 'Nueva cuenta auxiliar', 'icono' => 'bi-plus-lg'])
                                        @endif
                                        @if ($accion && Gate::allows('permiso', [$ruta.'.index', 'actualizar']))
                                            @include('partials.accion-fila-enlace', ['href' => route($ruta.'.'.$accion[0].'.formulario', $fila['id']),
                                                'etiqueta' => $accion[1], 'icono' => ['editar' => 'bi-pencil', 'cerrar' => 'bi-lock', 'revertir' => 'bi-arrow-counterclockwise'][$accion[0]]])
                                        @endif
                                    @endcomponent
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($campos) + 5 }}">@include('contabilidad::vacio', ['texto' => $filtros ? 'No hay resultados con estos filtros' : 'Todavía no hay registros'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($seccion !== 'cuentas' && $registros->hasPages())<div class="card-footer">{{ $registros->links('pagination::bootstrap-5') }}</div>@endif
        </div>
    </div></div>
@endsection
