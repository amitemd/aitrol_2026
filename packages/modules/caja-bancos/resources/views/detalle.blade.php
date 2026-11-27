@extends('layouts.adminlte')

@php
    $campos = match ($seccion) {
        'cuentas' => ['codigo' => 'Código', 'nombre' => 'Cuenta', 'tipo' => 'Tipo', 'moneda' => 'Moneda', 'banco' => 'Banco',
            'ultimos_digitos' => 'Últimos dígitos', 'saldo' => 'Saldo en libros', 'activo' => 'Estado'],
        'cierres' => ['cuenta' => 'Caja', 'created_at' => 'Apertura', 'cerrado_at' => 'Cierre', 'saldo_apertura' => 'Saldo inicial',
            'saldo_cierre' => 'Saldo al cierre', 'efectivo_contado' => 'Efectivo contado', 'diferencia' => 'Diferencia', 'observacion' => 'Observación', 'moneda' => 'Moneda'],
        'extractos' => ['id' => 'N.º de extracto', 'nombre' => 'Nombre', 'cuenta' => 'Banco', 'moneda' => 'Moneda', 'created_at' => 'Importado el',
            'importadas' => 'Líneas importadas', 'omitidas' => 'Líneas duplicadas'],
        'conciliacion' => ['fecha' => 'Fecha del banco', 'cuenta' => 'Banco', 'id_extracto' => 'N.º de extracto', 'referencia' => 'Referencia',
            'descripcion' => 'Descripción', 'importe' => 'Importe', 'moneda' => 'Moneda', 'id_asiento' => 'Estado'],
        default => ['id' => 'N.º de operación', 'fecha' => 'Fecha', 'tipo' => 'Tipo', 'concepto' => 'Concepto', 'referencia' => 'Referencia',
            'importe' => 'Importe', 'moneda' => 'Moneda', 'id_reversion' => 'Estado', 'id_reversa_de' => 'Revierte la operación'],
    };
@endphp
@section('title', 'Detalle de Caja y bancos')
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <section class="card" data-ajax-form>
            <h1 class="card-header fs-5">Detalle del registro</h1>
            <div class="card-body">
                <dl class="row">
                    @if (!empty($registro['numero_asiento']))
                        <dt class="col-sm-5">Contabilidad</dt><dd class="col-sm-7">@include('partials.referencia-asiento', ['registroAsiento' => $registro])</dd>
                    @endif
                    @foreach ($campos as $campo => $etiqueta)
                        <dt class="col-sm-5">{{ $etiqueta }}</dt>
                        <dd class="col-sm-7">@include('caja-bancos::valor', ['valor' => $registro[$campo] ?? null])</dd>
                    @endforeach
                </dl>
                @isset($registro['asientos'])
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <caption class="visually-hidden">Movimientos en el libro de cuentas</caption>
                            <thead><tr><th scope="col">Cuenta</th><th scope="col">Variación</th><th scope="col">Saldo anterior</th><th scope="col">Saldo</th></tr></thead>
                            <tbody>
                                @foreach ($registro['asientos'] as $asiento)
                                    <tr><th scope="row">{{ $asiento['cuenta'] }}</th><td>{{ $asiento['variacion'] }}</td><td>{{ $asiento['saldo_anterior'] }}</td><td>{{ $asiento['saldo'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endisset
                @if (!empty($registro['historial']) && $registro['historial']->isNotEmpty())
                    <h2 class="h6">Historial de conciliación</h2>
                    <ul class="list-group">
                        @foreach ($registro['historial'] as $evento)
                            <li class="list-group-item d-flex flex-wrap justify-content-between">
                                <span>{{ $evento->accion === 'conciliar' ? 'Conciliado' : 'Desconciliado' }} · asiento {{ $evento->id_asiento }}
                                    @if ($evento->motivo)<small class="d-block text-body-secondary">{{ $evento->motivo }}</small>@endif
                                </span>
                                <small class="text-body-secondary">{{ $evento->created_at }}</small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="card-footer text-end"><a class="btn btn-outline-secondary" href="{{ route('caja-bancos.'.$seccion.'.index') }}" data-ajax-close>Cerrar</a></div>
        </section>
    </div></div>
@endsection
