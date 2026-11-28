@extends('layouts.adminlte')

@section('title', $titulo)

@section('content')
    @php
        $usd = fn ($centavos) => 'USD '.number_format((int) $centavos / 100, 2, '.', ',');
        $puedeVerEstado = Gate::allows('permiso', ['clientes.reportes.cuentas-por-cobrar', 'leer']);
        $importes = match ($reporte) {
            'edad-de-cartera' => ['vigente' => 'Por vencer', 'dias_1_30' => '1–30 días', 'dias_31_60' => '31–60 días', 'dias_61_90' => '61–90 días', 'dias_91' => 'Más de 90 días', 'pendiente' => 'Total pendiente'],
            'saldos-de-cartera' => ['pendiente' => 'Pendiente', 'vencido' => 'Vencido', 'a_favor' => 'A favor', 'saldo' => 'Saldo neto'],
            default => ['total' => 'Factura', 'debitos' => 'Notas de débito', 'creditos' => 'Notas de crédito', 'retenido' => 'Retenciones', 'cobrado' => 'Cobros', 'saldo' => 'Saldo'],
        };
        $etiquetasMovimientos = ['notas-debito' => 'Nota de débito', 'notas-credito' => 'Nota de crédito', 'retenciones' => 'Retención', 'cobro' => 'Cobro'];
        $nombre = $cliente ? ($cliente->persona?->nombreCompleto() ?: $cliente->nombre) : null;
        $resumen = ['pendiente' => ['Total pendiente', $totales->pendiente], 'vencido' => ['Saldo vencido', $totales->vencido], 'a_favor' => ['Saldo a favor', $totales->a_favor]];
        if ($cliente) {
            $resumen['neto'] = ['Saldo neto', $totales->pendiente - $totales->a_favor];
        }
    @endphp
    @include('partials.listado-cabecera', ['titulo' => $titulo, 'subtitulo' => $nombre ?? 'Clientes / Reportes', 'icono' => 'bi-wallet2'])

    <div class="app-content"><div class="container-fluid">
        @include('partials.listado-modal', ['iconoModal' => 'bi-cash-coin'])
        @if ($cliente)
            <p class="text-body-secondary mb-2">{{ $cliente->persona?->identificacion ?? $cliente->identificacion }} · {{ $sucursal }}</p>
        @else
            <p class="text-body-secondary mb-2">Sucursal {{ $sucursal }}</p>
        @endif
        <div data-ajax-list>
        <div class="row g-3 mb-3">
            @foreach ($resumen as $campo => [$etiqueta, $valor])
                <div class="{{ $cliente ? 'col-sm-6 col-lg-3' : 'col-sm-4' }}"><div class="card h-100 mb-0"><div class="card-body">
                    <div class="small text-body-secondary">{{ $etiqueta }}</div>
                    <strong class="fs-5 {{ $campo === 'vencido' && $valor > 0 ? 'text-danger-emphasis' : '' }}">{{ $usd($valor) }}</strong>
                </div></div></div>
            @endforeach
        </div>

        <div class="card clientes-directorio">
            <form class="row g-2 align-items-end m-0 p-3 border-bottom" method="GET" action="{{ url()->current() }}" data-ajax-search>
                <div class="col-sm-3">
                    <label for="cartera-corte" class="form-label">Fecha de corte</label>
                    <input class="form-control @error('corte') is-invalid @enderror" type="date" id="cartera-corte" name="corte" value="{{ old('corte', $corte->toDateString()) }}" max="{{ $limite }}" required>
                    @error('corte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-5">
                    <label for="cartera-q" class="form-label">Buscar</label>
                    <input class="form-control @error('q') is-invalid @enderror" id="cartera-q" name="q" value="{{ old('q', $filtros['q'] ?? '') }}" placeholder="{{ $cliente ? 'Número de factura' : 'Cliente, identificación o factura' }}" maxlength="120">
                    @error('q')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-auto"><button class="btn btn-outline-primary" type="submit">Consultar</button></div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">{{ $titulo }} al {{ $corte->format('d/m/Y') }}</caption>
                    <thead class="clientes-tabla-cabecera"><tr>
                        @unless ($cliente)<th>Cliente</th>@endunless
                        @unless ($porCliente)
                            <th>Documento</th><th>Emisión</th><th>Vencimiento</th>
                        @endunless
                        @foreach ($importes as $etiqueta)<th class="text-end">{{ $etiqueta }}</th>@endforeach
                        @if ($puedeCobrar)<th class="text-end">Acción</th>@endif
                    </tr></thead>
                    <tbody>
                        @forelse ($filas as $fila)
                            <tr>
                                @unless ($cliente)
                                    <th scope="row">
                                        @if ($puedeVerEstado && $fila->id_cliente)
                                            <a href="{{ route('clientes.estado-cuenta', ['cliente' => $fila->id_cliente, 'corte' => $corte->toDateString()]) }}"
                                                data-workspace-link="clientes.estado-cuenta.{{ $fila->id_cliente }}" data-workspace-label="Estado de cuenta">{{ $fila->tercero }}</a>
                                        @else
                                            {{ $fila->tercero }}
                                        @endif
                                        @if ($fila->identificacion)<small class="d-block text-body-secondary">{{ $fila->identificacion }}</small>@endif
                                    </th>
                                @endunless
                                @unless ($porCliente)
                                    <td class="text-nowrap">
                                        @if ($cliente && $movimientos->has($fila->id))
                                            <details>
                                                <summary>{{ $fila->numero }}</summary>
                                                <ul class="list-unstyled small my-2 text-wrap">
                                                    @foreach ($movimientos->get($fila->id) as $movimiento)
                                                        <li class="mb-1"><span class="text-body-secondary">{{ \Illuminate\Support\Carbon::parse($movimiento->fecha)->format('d/m/Y') }}</span>
                                                            {{ $etiquetasMovimientos[$movimiento->tipo] }} · {{ $movimiento->referencia }} · {{ $usd($movimiento->importe) }}</li>
                                                    @endforeach
                                                </ul>
                                            </details>
                                        @else
                                            {{ $fila->numero }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ \Illuminate\Support\Carbon::parse($fila->fecha)->format('d/m/Y') }}</td>
                                    <td class="text-nowrap {{ $fila->saldo > 0 && $fila->vencimiento < $corte->toDateString() ? 'text-danger-emphasis' : '' }}">{{ \Illuminate\Support\Carbon::parse($fila->vencimiento)->format('d/m/Y') }}</td>
                                @endunless
                                @foreach ($importes as $campo => $etiqueta)
                                    <td class="text-end text-nowrap {{ $loop->last ? 'fw-semibold' : '' }}">{{ $usd($fila->{$campo}) }}</td>
                                @endforeach
                                @if ($puedeCobrar)
                                    <td class="text-end text-nowrap">
                                        @if ($fila->saldo > 0)
                                            <a class="btn btn-sm btn-outline-primary" data-ajax-open="Registrar cobro"
                                                href="{{ route('ventas.documentos.cobro.create', $fila->id) }}">Registrar cobro</a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($importes) + ($cliente ? 0 : 1) + ($porCliente ? 0 : 3) + (int) $puedeCobrar }}" class="text-center p-4 text-body-secondary">No hay registros para esta consulta.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($filas->hasPages())<div class="card-footer">{{ $filas->links('pagination::bootstrap-5') }}</div>@endif
        </div>
        </div>
    </div></div>
@endsection
