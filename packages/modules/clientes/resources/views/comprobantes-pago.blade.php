@extends('layouts.adminlte')

@section('title', 'Comprobantes de pago')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Comprobantes de pago', 'subtitulo' => 'Clientes / Documentos', 'icono' => 'bi-credit-card'])
    <div class="app-content"><div class="container-fluid">
        @include('partials.listado-modal', ['iconoModal' => 'bi-credit-card'])
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <p class="text-body-secondary mb-0">Sucursal {{ $sucursal }}</p>
            @can('permiso', ['clientes.reportes.cuentas-por-cobrar', 'leer'])
                <a class="btn btn-outline-primary btn-sm" href="{{ route('clientes.reportes.cuentas-por-cobrar') }}"
                    data-workspace-link="clientes.reportes.cuentas-por-cobrar" data-workspace-label="Cuentas por cobrar">Ver facturas pendientes</a>
            @endcan
        </div>
        <div class="card clientes-directorio" data-ajax-list>
            @include('partials.listado-busqueda', ['listadoUrl' => route('clientes.documentos.comprobantes-de-pago'), 'placeholder' => 'Cliente, factura o referencia'])
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="clientes-tabla-cabecera"><tr>
                        <th>Fecha</th><th>Cliente</th><th>Factura</th><th>Referencia</th><th>Caja o banco</th>
                        <th class="text-end">Importe</th><th>Estado</th>
                    </tr></thead>
                    <tbody data-ajax-filas>
                        @forelse ($filas as $fila)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($fila->fecha)->format('d/m/Y') }}</td>
                                <th scope="row">
                                    @if ($fila->id_cliente && Gate::allows('permiso', ['clientes.reportes.cuentas-por-cobrar', 'leer']))
                                        <a href="{{ route('clientes.estado-cuenta', $fila->id_cliente) }}" data-workspace-link="clientes.estado-cuenta.{{ $fila->id_cliente }}" data-workspace-label="Estado de cuenta">{{ $fila->tercero }}</a>
                                    @else
                                        {{ $fila->tercero }}
                                    @endif
                                </th>
                                <td>{{ $fila->numero }}</td>
                                <td>
                                    @can('permiso', ['caja-bancos.movimientos.index', 'leer'])
                                        <a href="{{ route('caja-bancos.movimientos.show', $fila->id) }}" data-ajax-open="Detalle del cobro">{{ $fila->referencia ?: 'Ver cobro' }}</a>
                                    @else
                                        {{ $fila->referencia }}
                                    @endcan
                                </td>
                                <td>{{ $fila->destino }}</td>
                                <td class="text-end text-nowrap">{{ $fila->moneda }} {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal((int) $fila->importe, 2) }}</td>
                                <td>{{ $fila->id_reversa ? 'Revertido' : 'Registrado' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center p-4 text-body-secondary">No hay cobros de facturas para esta consulta.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($filas->hasMorePages())
                <div class="card-footer text-center" aria-live="polite">
                    <a class="btn btn-outline-secondary" href="{{ $filas->nextPageUrl() }}" data-ajax-mas>Cargar más comprobantes</a>
                </div>
            @endif
        </div>
    </div></div>
@endsection
