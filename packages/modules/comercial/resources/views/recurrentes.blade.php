@extends('layouts.adminlte')

@section('title', 'Facturas recurrentes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('factura-venta.css') }}?v={{ filemtime(public_path('factura-venta.css')) }}">
@endpush

@section('content')
<div class="app-content factura-venta py-3 py-lg-4">
    <div class="container-fluid">
        <header class="factura-cabecera">
            <div>
                <a href="{{ route('ventas.documentos.index') }}" data-workspace-link="ventas.documentos.index" data-workspace-label="Lista de ventas" data-workspace-forward><i class="bi bi-arrow-left" aria-hidden="true"></i> Lista de ventas</a>
                <h1>Facturas recurrentes</h1>
            </div>
            <a href="{{ route('ventas.documentos.create') }}" class="btn btn-primary"
                data-workspace-link="ventas.documentos.create" data-workspace-label="Nueva factura" data-workspace-forward>Nueva factura</a>
        </header>
        @if (session('status'))<p class="alert alert-success" role="status">{{ session('status') }}</p>@endif
        <section class="factura-seccion mb-3">
            <h2><i class="bi bi-inbox" aria-hidden="true"></i> Borradores por revisar</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Fecha programada</th><th>Frecuencia</th><th class="text-end">Acción</th></tr></thead>
                    <tbody>
                        @forelse ($pendientes as $fila)
                            <tr>
                                <td>{{ $fila->fecha }}</td><td>{{ \App\Services\FacturaVenta::FRECUENCIAS[$fila->frecuencia] }}</td>
                                <td class="text-end"><a class="btn btn-outline-primary btn-sm" href="{{ route('ventas.documentos.create', ['borrador' => $fila->id]) }}"
                                    data-workspace-link="ventas.documentos.create.borrador-{{ $fila->id }}" data-workspace-label="Factura {{ $fila->fecha }}" data-workspace-forward>Revisar borrador</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-body-secondary">No hay borradores pendientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $pendientes->withQueryString()->links() }}
        </section>
        <section class="factura-seccion factura-recurrencia">
            <h2><i class="bi bi-arrow-repeat" aria-hidden="true"></i> Programaciones</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Factura de origen</th><th>Frecuencia</th><th>Próxima fecha</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                    <tbody>
                        @forelse ($recurrencias as $fila)
                            <tr>
                                <td>{{ $fila->numero }}</td><td>{{ \App\Services\FacturaVenta::FRECUENCIAS[$fila->frecuencia] }}</td>
                                <td>{{ $fila->proxima_fecha }}</td><td>{{ $fila->activa ? 'Activa' : 'Pausada' }}</td>
                                <td class="text-end">
                                    @can('permiso', ['ventas.documentos.index', 'actualizar'])
                                        <form method="POST" action="{{ route('ventas.facturas-recurrentes.estado', $fila->id) }}">
                                            @csrf
                                            <input type="hidden" name="activa" value="{{ $fila->activa ? 0 : 1 }}">
                                            <button class="btn btn-outline-secondary btn-sm">{{ $fila->activa ? 'Pausar' : 'Reanudar' }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-body-secondary">Todavía no hay programaciones.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $recurrencias->withQueryString()->links() }}
        </section>
    </div>
</div>
@endsection
