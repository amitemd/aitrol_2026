@extends('layouts.adminlte')

@section('title', 'Compras pendientes')
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Compras pendientes', 'subtitulo' => 'Compras', 'icono' => 'bi-clipboard-data'])
    <div class="app-content">
        <div class="container-fluid">
            <section class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('compras.reportes.pendientes') }}" class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label for="compras-pendientes-q" class="form-label">Buscar por orden, proveedor o producto</label>
                            <input id="compras-pendientes-q" name="q" value="{{ $busqueda }}" maxlength="120" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="compras-pendientes-estado" class="form-label">Pendiente</label>
                            <select id="compras-pendientes-estado" name="estado" class="form-select">
                                <option value="todos" @selected($estado === 'todos')>Todos</option>
                                <option value="por-recibir" @selected($estado === 'por-recibir')>Por recibir</option>
                                <option value="por-facturar" @selected($estado === 'por-facturar')>Por facturar</option>
                            </select>
                        </div>
                        <div class="col-auto"><button class="btn btn-primary" type="submit">Filtrar</button></div>
                    </form>
                </div>
                <div class="card-body">
                    <p class="mb-0"><strong>{{ $totales['ordenes'] }}</strong> órdenes en el resultado · <strong>USD {{ $totales['base_por_facturar'] }}</strong> recibidos por facturar</p>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th scope="col">Orden</th><th scope="col">Proveedor</th><th scope="col">Producto</th>
                            <th scope="col" class="text-end">Ordenado</th><th scope="col" class="text-end">Recibido</th>
                            <th scope="col" class="text-end">Por recibir</th><th scope="col" class="text-end">Por facturar</th>
                            <th scope="col" class="text-end">Base por facturar (USD)</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($filas as $fila)
                                <tr>
                                    <th scope="row">
                                        @can('permiso', ['compras.ordenes-compra.index', 'leer'])
                                            <a href="{{ route('compras.ordenes-compra.recepciones.index', $fila->id_orden) }}">{{ $fila->numero }}</a>
                                        @else
                                            {{ $fila->numero }}
                                        @endcan
                                        <small class="d-block text-body-secondary">{{ $fila->fecha }}</small>
                                    </th>
                                    <td>{{ $fila->proveedor }}</td><td>{{ $fila->descripcion }}</td>
                                    @foreach (['ordenada', 'recibida', 'por_recibir', 'por_facturar', 'base_por_facturar'] as $campo)
                                        <td class="text-end">{{ $fila->$campo }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-body-secondary">No hay compras pendientes con estos filtros.</td></tr>
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
