@extends('layouts.adminlte')

@php $estados = \Aitrol\Comercial\Application\SolicitudesCompra::ESTADOS; @endphp
@section('title', 'Solicitudes de compra')
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Solicitudes de compra', 'subtitulo' => 'Sucursal activa', 'icono' => 'bi-clipboard-plus'])
    <div class="app-content">
        <div class="container-fluid">
            @include('comercial::errores')
            <section class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('compras.solicitudes.index') }}" class="d-flex flex-wrap align-items-end gap-2">
                        <div>
                            <label for="solicitudes-estado" class="form-label">Estado</label>
                            <select name="estado" id="solicitudes-estado" class="form-select">
                                <option value="">Todos</option>
                                @foreach ($estados as $valor => $nombre)
                                    <option value="{{ $valor }}" @selected($estado === $valor)>{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a class="btn btn-outline-secondary ms-auto" href="{{ route('compras.presupuestos.index') }}">Presupuestos de compra</a>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th scope="col">Solicitud</th><th scope="col">Fecha</th><th scope="col">Centro de costo</th>
                            <th scope="col" class="text-end">Total (USD)</th><th scope="col">Estado</th></tr></thead>
                        <tbody>
                            @forelse ($solicitudes as $solicitud)
                                <tr>
                                    <th scope="row"><a href="{{ route('compras.solicitudes.show', $solicitud['id']) }}">{{ $solicitud['numero'] }}</a></th>
                                    <td>{{ $solicitud['fecha'] }}</td><td>{{ $solicitud['centro'] }}</td>
                                    <td class="text-end">{{ $solicitud['total'] }}</td><td>{{ $estados[$solicitud['estado']] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-body-secondary">No hay solicitudes con este filtro.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($solicitudes->hasPages()) <div class="card-footer">{{ $solicitudes->links('pagination::bootstrap-5') }}</div> @endif
            </section>
        </div>
    </div>
@endsection
