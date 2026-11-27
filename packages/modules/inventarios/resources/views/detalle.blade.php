@extends('layouts.adminlte')

@section('title', 'Detalle de inventario')
@section('content')
    <div class="app-content py-3"><div class="container-fluid">
        <section class="card" data-ajax-form>
            <h1 class="card-header fs-5">Detalle de inventario</h1>
            <div class="card-body">
                @include('partials.referencia-asiento', ['registroAsiento' => $fila])
                <dl class="row mb-0 mt-3">
                    @foreach (['codigo' => 'Código', 'nombre' => 'Nombre', 'descripcion' => 'Descripción', 'codigo_barras' => 'Código de barras',
                        'categoria' => 'Categoría', 'unidad' => 'Unidad', 'stock_minimo' => 'Stock mínimo', 'tipo' => 'Tipo', 'cantidad' => 'Cantidad',
                        'costo_unitario' => 'Costo unitario', 'motivo' => 'Motivo', 'referencia' => 'Referencia', 'created_at' => 'Fecha de registro'] as $campo => $etiqueta)
                        @isset($fila[$campo])
                            <dt class="col-sm-5">{{ $etiqueta }}</dt><dd class="col-sm-7">{{ $campo === 'tipo' ? config('inventarios.tipos.'.$fila[$campo]) : $fila[$campo] }}</dd>
                        @endisset
                    @endforeach
                </dl>
                @isset($fila['lineas'])
                    <div class="table-responsive"><table class="table table-sm">
                        <caption class="visually-hidden">Movimientos de existencias</caption>
                        <thead><tr><th>Bodega</th><th>Variación</th><th>Saldo anterior</th><th>Saldo</th></tr></thead>
                        <tbody>@foreach ($fila['lineas'] as $linea)
                            <tr><td>{{ $linea['id_bodega'] }}</td><td>{{ $linea['variacion'] }}</td><td>{{ $linea['saldo_anterior'] }}</td><td>{{ $linea['saldo'] }}</td></tr>
                        @endforeach</tbody>
                    </table></div>
                @endisset
            </div>
            <div class="card-footer text-end"><a href="{{ route('inventarios.'.$seccion.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>Cerrar</a></div>
        </section>
    </div></div>
@endsection
