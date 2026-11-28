@extends('layouts.adminlte')

@section('title', 'Comprobantes de pago a proveedores')

@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Comprobantes de pago', 'subtitulo' => 'Proveedores', 'icono' => 'bi-receipt-cutoff'])
    <div class="app-content">
        <div class="container-fluid">
            <section class="card">
                <div class="card-body">
                    <form method="GET" class="d-flex gap-2 align-items-center">
                        <label for="pagos-proveedores-corte" class="form-label mb-0">Corte</label>
                        <input id="pagos-proveedores-corte" class="form-control w-auto" name="corte" type="date"
                            value="{{ $corte->toDateString() }}" max="{{ now('America/Guayaquil')->toDateString() }}">
                        <button class="btn btn-outline-primary">Consultar</button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="clientes-tabla-cabecera">
                            <tr><th scope="col">Fecha</th><th scope="col">Proveedor</th><th scope="col">Factura</th>
                                <th scope="col">Referencia</th><th scope="col" class="text-end">Importe (USD)</th><th scope="col">Estado</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($filas as $pago)
                                <tr>
                                    <td>{{ $pago->fecha }}</td><th scope="row">{{ $pago->tercero }}</th><td>{{ $pago->numero }}</td>
                                    <td>{{ $pago->referencia }}</td><td class="text-end">{{ number_format($pago->importe / 100, 2) }}</td>
                                    <td>{{ $pago->id_reversa ? 'Revertido' : 'Registrado' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-body-secondary py-4">No hay comprobantes de pago para este corte.</td></tr>
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
