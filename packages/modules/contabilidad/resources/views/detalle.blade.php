@extends('layouts.adminlte')

@php
    $campos = match ($seccion) {
        'asientos' => ['numero' => 'Asiento', 'fecha' => 'Fecha', 'concepto' => 'Concepto', 'referencia' => 'Referencia', 'periodo' => 'Período',
            'total' => 'Importe USD', 'flujo' => 'Flujo de efectivo', 'origen' => 'Origen', 'id_origen' => 'N.º de operación de origen', 'numero_reversion' => 'Asiento de reversión', 'created_at' => 'Registrado el'],
        'cuentas' => ['codigo' => 'Código', 'nombre' => 'Cuenta', 'clase' => 'Clase', 'padre' => 'Cuenta superior', 'auxiliar' => 'Nivel', 'efectivo' => 'Efectivo', 'activo' => 'Estado'],
        'periodos' => ['nombre' => 'Período', 'desde' => 'Desde', 'hasta' => 'Hasta', 'cerrado_at' => 'Estado', 'motivo_cierre' => 'Motivo de cierre'],
    };
@endphp
@section('title', 'Detalle contable')
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <section class="card" data-ajax-form>
            <h1 class="card-header fs-5">Detalle contable</h1>
            <div class="card-body">
                <dl class="row mb-2">
                    @foreach ($campos as $campo => $etiqueta)
                        <dt class="col-sm-5">{{ __($etiqueta) }}</dt><dd class="col-sm-7">@include('contabilidad::valor', ['valor' => $registro[$campo] ?? null])</dd>
                    @endforeach
                </dl>
                @if ($seccion === 'cuentas')
                    <h2 class="fs-6 mt-3">Auditoría</h2>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" aria-label="Auditoría de la cuenta">
                            <thead><tr>@include('partials.auditoria-cabeceras')</tr></thead>
                            <tbody><tr>@include('partials.auditoria-celdas', ['filaAuditoria' => $registro])</tr></tbody>
                        </table>
                    </div>
                @endif
                @isset($registro['lineas'])
                    <div class="table-responsive"><table class="table table-sm align-middle">
                        <caption class="visually-hidden">Detalle de partidas</caption>
                        <thead><tr><th>{{ __('Cuenta') }}</th><th>{{ __('Detalle') }}</th><th class="text-end">Debe</th><th class="text-end">Haber</th></tr></thead>
                        <tbody>@foreach ($registro['lineas'] as $fila)
                            <tr><th scope="row">{{ $fila['codigo'] }} · {{ $fila['cuenta'] }}</th><td>{{ $fila['detalle'] ?? '—' }}</td>
                                <td class="text-end">{{ $fila['debe'] }}</td><td class="text-end">{{ $fila['haber'] }}</td></tr>
                        @endforeach</tbody>
                        <tfoot><tr><th scope="row" colspan="2" class="text-end">{{ __('Total') }}</th><td class="text-end">{{ $registro['total'] }}</td><td class="text-end">{{ $registro['total'] }}</td></tr></tfoot>
                    </table></div>
                @endisset
            </div>
            <div class="card-footer text-end"><a href="{{ route('contabilidad.'.$seccion.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cerrar') }}</a></div>
        </section>
    </div></div>
@endsection
