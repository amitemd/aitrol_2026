@extends('layouts.adminlte')

@section('title', 'Historial de movimientos')
@section('content')
    @include('partials.listado-cabecera', ['titulo' => 'Historial de movimientos', 'subtitulo' => $ficha->codigo.' · '.$ficha->nombre, 'icono' => 'bi-clock-history'])
    <div class="app-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <a href="{{ route('inventarios.productos.index') }}" class="btn btn-outline-secondary">Volver a productos</a>
                @if ($kardex?->isNotEmpty() || ($kardex && $ficha->tipo_producto === 'producto'))
                    <a href="{{ route('inventarios.kardex.index', ['id_producto' => $ficha->id]) }}" class="btn btn-outline-primary">Ver Kardex del producto</a>
                @endif
            </div>
            <section class="card clientes-directorio">
                <h2 class="card-header fs-6 mb-0">Ventas y compras · {{ $periodo }}</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <caption class="visually-hidden">Documentos comerciales del producto</caption>
                        <thead class="clientes-tabla-cabecera">
                            <tr><th scope="col">Fecha</th><th scope="col">Movimiento</th><th scope="col">Documento</th><th scope="col" class="text-end">Cantidad</th><th scope="col">Estado</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($movimientos as $movimiento)
                                <tr>
                                    <td><time datetime="{{ $movimiento->fecha }}">{{ $movimiento->fecha }}</time></td>
                                    <td>{{ $movimiento->modulo === 'ventas' ? 'Venta' : 'Compra' }}</td>
                                    <td>{{ $movimiento->numero }}</td>
                                    <td class="text-end">{{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal((int) $movimiento->cantidad, 3) }}</td>
                                    <td><span @class(['badge rounded-pill', 'text-bg-secondary' => $movimiento->anulado_at, 'text-bg-success' => !$movimiento->anulado_at])>{{ $movimiento->anulado_at ? 'Anulado' : 'Vigente' }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-body-secondary">Sin ventas ni compras registradas en esta sucursal y período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($movimientos->hasPages()) <div class="card-footer">{{ $movimientos->links() }}</div> @endif
            </section>
            @if ($kardex?->isNotEmpty() || ($kardex && $ficha->tipo_producto === 'producto'))
                <section class="card clientes-directorio mt-3">
                    <h2 class="card-header fs-6 mb-0">Últimos movimientos de existencias</h2>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <caption class="visually-hidden">Movimientos físicos del producto</caption>
                            <thead class="clientes-tabla-cabecera">
                                <tr><th scope="col">Fecha</th><th scope="col">Movimiento</th><th scope="col">Bodega</th><th scope="col" class="text-end">Variación</th><th scope="col" class="text-end">Saldo</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($kardex as $fila)
                                    <tr>
                                        <td>{{ $fila['created_at'] }}</td>
                                        <td>{{ config('inventarios.tipos.'.$fila['tipo']) }}</td>
                                        <td>{{ $fila['bodega'] }}</td>
                                        <td class="text-end">{{ $fila['variacion'] }}</td>
                                        <td class="text-end">{{ $fila['saldo'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Sin movimientos de existencias en esta sucursal y período.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
