@extends('layouts.adminlte')

@php $titulo = $modulo === 'compras' ? 'Trazabilidad de la compra' : 'Trazabilidad de la factura'; @endphp
@section('title', $titulo)
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <section class="card" data-ajax-form>
                <h1 class="card-header fs-5">{{ $titulo }}</h1>
                <div class="card-body">
                    <p class="fw-semibold">{{ $factura->numero }} · {{ $factura->tercero }}</p>
                    <dl class="row">
                        <dt class="col-sm-4">Registro</dt><dd class="col-sm-8">{{ $factura->created_at }}</dd>
                        <dt class="col-sm-4">Asiento</dt><dd class="col-sm-8">{{ $factura->numero_asiento ?? 'Pendiente de contabilizar' }}</dd>
                        @isset($saldoCobro)
                            <dt class="col-sm-4">{{ $saldoCobro < 0 ? 'Saldo a favor' : 'Saldo por cobrar' }}</dt>
                            <dd class="col-sm-8">USD {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal(abs($saldoCobro), 2) }}</dd>
                        @endisset
                        @if ($factura->anulado_at)
                            <dt class="col-sm-4">Anulación</dt><dd class="col-sm-8">{{ $factura->anulado_at }} · {{ $factura->motivo_anulacion }}</dd>
                            <dt class="col-sm-4">Asiento de reversión</dt><dd class="col-sm-8">{{ $factura->numero_asiento_reversion ?? '—' }}</dd>
                        @endif
                        @if ($factura->lineas->contains('id_operacion', '!=', null))
                            <dt class="col-sm-4">Movimientos de inventario</dt>
                            <dd class="col-sm-8">
                                {{ $factura->lineas->pluck('id_operacion')->filter()->unique()->implode(', ') }}
                                @if ($factura->lineas->pluck('id_operacion_reversa')->filter()->isNotEmpty())
                                    <span class="d-block">Reversiones: {{ $factura->lineas->pluck('id_operacion_reversa')->filter()->unique()->implode(', ') }}</span>
                                @endif
                            </dd>
                        @endif
                    </dl>
                    @isset($cotejo)
                        <h2 class="h6">Mercadería aplicada a esta factura</h2>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm">
                                <thead><tr>
                                    <th scope="col">Recepción</th><th scope="col">Producto</th>
                                    <th scope="col" class="text-end">Cantidad aplicada</th><th scope="col" class="text-end">Base aplicada (USD)</th>
                                </tr></thead>
                                <tbody>
                                    @foreach ($cotejo['aplicaciones'] as $aplicacion)
                                        <tr>
                                            <td>#{{ $aplicacion['id_recepcion'] }}</td><td>{{ $aplicacion['descripcion'] }}</td>
                                            <td class="text-end">{{ $aplicacion['cantidad'] }}</td><td class="text-end">{{ $aplicacion['subtotal'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @isset($cotejo['aprobacion'])
                            <section class="border rounded p-3 mb-3">
                                <h2 class="h6">Diferencias aprobadas</h2>
                                <p>{{ $cotejo['aprobacion']['motivo'] }}</p>
                                <p>Aprobó {{ $cotejo['aprobacion']['usuario'] ?? ('#'.$cotejo['aprobacion']['id_usuario']) }}
                                    el {{ \Illuminate\Support\Carbon::parse($cotejo['aprobacion']['fecha'])->setTimezone('America/Guayaquil')->format('d/m/Y H:i') }}.</p>
                                <ul class="mb-0">
                                    @foreach ($cotejo['aprobacion']['lineas'] as $diferencia)
                                        <li>{{ $factura->lineas->firstWhere('id_linea_orden', $diferencia['id_linea_orden'])?->descripcion }}
                                            · Precio esperado USD {{ $diferencia['precio_esperado'] }} y facturado USD {{ $diferencia['precio_facturado'] }}
                                            · Diferencia de base USD {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal($diferencia['diferencia_base'], 2) }}
                                            · Diferencia de IVA USD {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal($diferencia['diferencia_impuesto'], 2) }}</li>
                                    @endforeach
                                </ul>
                            </section>
                        @endisset
                    @endisset
                    <h2 class="h6">Documentos vinculados</h2>
                    <ul class="list-group list-group-flush">
                        @forelse ($vinculados as $documento)
                            <li class="list-group-item px-0">
                                <strong>{{ $documento->numero }}</strong> · {{ config('comercial.'.$modulo.'.secciones.'.$documento->tipo.'.0') }}
                                <small class="d-block text-body-secondary">{{ $documento->fecha }} · {{ $documento->anulado_at ? 'Anulado' : 'Vigente' }}
                                    @if ($documento->numero_asiento) · Asiento {{ $documento->numero_asiento }} @endif
                                </small>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-body-secondary">Sin notas ni retenciones vinculadas.</li>
                        @endforelse
                    </ul>
                    @if ($movimientosCobro->isNotEmpty())
                        <h2 class="h6 mt-3">Cobros registrados</h2>
                        <ul class="list-group list-group-flush">
                            @foreach ($movimientosCobro as $cobro)
                                <li class="list-group-item px-0">
                                    <strong>USD {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal((int) $cobro->importe, 2) }}</strong>
                                    · {{ $cobro->fecha }} · {{ $cobro->referencia }}
                                    @if (Gate::allows('permiso', ['caja-bancos.movimientos.index', 'leer']))
                                        · <a href="{{ route('caja-bancos.movimientos.show', $cobro->id) }}">Ver ingreso</a>
                                    @endif
                                    <small class="d-block text-body-secondary">
                                        {{ $cobro->cuenta }} · {{ $cobro->tipo === 'caja' ? 'Cobrado en caja' : ($cobro->id_conciliacion ? 'Conciliado' : 'Pendiente de conciliación') }}
                                        @if ($cobro->id_conciliacion && Gate::allows('permiso', ['caja-bancos.conciliacion.index', 'leer']))
                                            · <a href="{{ route('caja-bancos.conciliacion.show', $cobro->id_conciliacion) }}">Ver conciliación</a>
                                        @endif
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @include('comercial::diagrama-trazabilidad')
                </div>
                <div class="card-footer text-end">
                    @if ($modulo === 'compras' && $vinculados->contains('tipo', 'recepciones'))
                        <a href="{{ route('compras.documentos.devoluciones.index', $factura->id) }}" class="btn btn-outline-primary">Devoluciones de mercadería</a>
                    @endif
                    <a href="{{ route($modulo.'.documentos.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cerrar</a>
                </div>
            </section>
        </div>
    </div>
@endsection
