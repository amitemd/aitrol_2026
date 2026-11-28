@extends('layouts.adminlte')

@php $estados = \Aitrol\Comercial\Application\AprobacionesOrdenCompra::ESTADOS; @endphp
@section('title', 'Aprobaciones de compra')
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Aprobaciones de compra', 'subtitulo' => 'Órdenes de compra', 'icono' => 'bi-clipboard-check'])
    <div class="app-content">
        <div class="container-fluid">
            @include('comercial::errores')
            <section class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('compras.aprobaciones.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label for="aprobaciones-busqueda" class="form-label">Número de orden o proveedor</label>
                            <input id="aprobaciones-busqueda" name="q" value="{{ $busqueda }}" maxlength="120" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="aprobaciones-estado" class="form-label">Estado</label>
                            <select class="form-select" name="estado" id="aprobaciones-estado">
                                @foreach (['pendiente', 'aprobada', 'rechazada', 'anulada'] as $opcion)
                                    <option value="{{ $opcion }}" @selected($opcion === $estado)>{{ $estados[$opcion] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto"><button type="submit" class="btn btn-primary">Filtrar</button></div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th scope="col">Orden</th><th scope="col">Fecha</th><th scope="col">Proveedor</th>
                            <th scope="col" class="text-end">Total (USD)</th><th scope="col">Estado</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($ordenes as $orden)
                                <tr>
                                    <th scope="row"><a href="{{ route('compras.aprobaciones.show', $orden['id']) }}">{{ $orden['numero'] }}</a></th>
                                    <td>{{ $orden['fecha'] }}</td><td>{{ $orden['proveedor'] }}</td>
                                    <td class="text-end">{{ $orden['total'] }}</td><td>{{ $estados[$orden['estado']] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-body-secondary">No hay órdenes con estos filtros.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex flex-wrap gap-2 justify-content-between">
                    {{ $ordenes->links('pagination::bootstrap-5') }}
                    <div class="ms-auto d-flex gap-2 flex-wrap">
                        @can('administrar-configuracion')
                            <a class="btn btn-outline-primary" href="{{ route('compras.aprobaciones.politica') }}">Política de aprobación</a>
                        @endcan
                        <a class="btn btn-outline-secondary" href="{{ route('compras.ordenes-compra.index') }}">Volver a órdenes</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
