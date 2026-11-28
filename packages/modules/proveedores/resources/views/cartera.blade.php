@extends('layouts.adminlte')

@section('title', $titulo)

@section('content')
    @include('partials.listado-cabecera', ['titulo' => $titulo, 'subtitulo' => 'Proveedores', 'icono' => 'bi-receipt'])
    <div class="app-content">
        <div class="container-fluid">
            @unless ($agrupado)
                @include('partials.listado-modal', ['iconoModal' => 'bi-cash-stack'])
            @endunless
            @if (session('status'))
                <p class="alert alert-success" role="status">{{ session('status') }}</p>
            @endif
            <section class="card" data-ajax-list>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end mb-3">
                        <div class="col-sm">
                            <label for="proveedor-cartera-busqueda" class="form-label">Proveedor o factura</label>
                            <input id="proveedor-cartera-busqueda" class="form-control" name="q" type="search" value="{{ request('q') }}" maxlength="160">
                        </div>
                        <div class="col-sm-auto">
                            <label for="proveedor-cartera-corte" class="form-label">Corte</label>
                            <input id="proveedor-cartera-corte" class="form-control" name="corte" type="date"
                                value="{{ $corte->toDateString() }}" max="{{ now('America/Guayaquil')->toDateString() }}">
                        </div>
                        <div class="col-sm-auto"><button class="btn btn-outline-primary">Consultar</button></div>
                    </form>
                    <p class="mb-0">Pendiente <strong>USD {{ number_format($totales->pendiente / 100, 2) }}</strong>
                        <span class="ms-3">Vencido <strong>USD {{ number_format($totales->vencido / 100, 2) }}</strong></span>
                        @if ($totales->a_favor > 0)
                            <span class="ms-3">A favor de la empresa <strong>USD {{ number_format($totales->a_favor / 100, 2) }}</strong></span>
                        @endif
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="clientes-tabla-cabecera">
                            <tr>
                                <th scope="col">Proveedor</th>
                                @unless ($agrupado)
                                    <th scope="col">Factura</th><th scope="col">Vencimiento</th>
                                @endunless
                                @if ($porEdad)
                                    <th scope="col" class="text-end">Por vencer</th><th scope="col" class="text-end">1–30 días</th>
                                    <th scope="col" class="text-end">31–60 días</th><th scope="col" class="text-end">61–90 días</th>
                                    <th scope="col" class="text-end">Más de 90 días</th>
                                @endif
                                <th scope="col" class="text-end">Pendiente (USD)</th>
                                @unless ($agrupado)
                                    <th scope="col"><span class="visually-hidden">Acciones</span></th>
                                @endunless
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($filas as $fila)
                                <tr>
                                    <th scope="row">{{ $fila->tercero }}</th>
                                    @unless ($agrupado)
                                        <td>{{ $fila->numero }}</td><td>{{ $fila->vencimiento }}</td>
                                    @endunless
                                    @if ($porEdad)
                                        @foreach (['vigente', 'dias_1_30', 'dias_31_60', 'dias_61_90', 'dias_91'] as $tramo)
                                            <td class="text-end">{{ number_format($fila->$tramo / 100, 2) }}</td>
                                        @endforeach
                                    @endif
                                    <td class="text-end">{{ number_format(($agrupado ? $fila->pendiente : $fila->saldo) / 100, 2) }}</td>
                                    @unless ($agrupado)
                                        <td class="text-end">
                                            @if ($corte->isToday())
                                                @can('permiso', ['proveedores.reportes.cuentas-por-pagar', 'leer'])
                                                    @can('permiso', ['caja-bancos.movimientos.index', 'escribir'])
                                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('proveedores.pagos.create', $fila->id) }}" data-ajax-open="Registrar pago">Registrar pago</a>
                                                    @endcan
                                                @endcan
                                            @endif
                                        </td>
                                    @endunless
                                </tr>
                            @empty
                                <tr><td colspan="{{ $porEdad ? 7 : ($agrupado ? 2 : 5) }}" class="text-center text-body-secondary py-4">No hay facturas pendientes para esta consulta.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($filas->hasPages())
                    <div class="card-footer">{{ $filas->links('pagination::bootstrap-5') }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
