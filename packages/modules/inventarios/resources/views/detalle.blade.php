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
                        'categoria' => 'Categoría', 'plantilla_contable' => 'Plantilla contable', 'tipo_producto' => 'Tipo de producto', 'marca' => 'Marca', 'modelo' => 'Modelo',
                        'unidad' => 'Unidad', 'stock_minimo' => 'Stock mínimo', 'tipo' => 'Tipo', 'cantidad' => 'Cantidad',
                        'origen_movimiento' => 'Bodega de origen', 'destino_movimiento' => 'Bodega de destino',
                        'costo_unitario' => 'Costo unitario', 'motivo' => 'Motivo', 'numero_documento' => 'Documento comercial',
                        'referencia' => 'Referencia', 'created_at' => 'Fecha de registro'] as $campo => $etiqueta)
                        @isset($fila[$campo])
                            <dt class="col-sm-5">{{ __($etiqueta) }}</dt><dd class="col-sm-7">{{ match ($campo) {
                                'tipo' => config('inventarios.tipos.'.$fila[$campo]),
                                'tipo_producto' => config('inventarios.tipos_producto.'.$fila[$campo]),
                                default => $fila[$campo],
                            } }}</dd>
                        @endisset
                    @endforeach
                </dl>
                @if ($seccion === 'productos' && array_intersect_key(array_filter($fila, fn ($valor) => $valor !== null), config('inventarios.valores_producto')))
                    <h2 class="h6 mt-3">Precios y costos unitarios</h2>
                    <dl class="row mb-0">
                        @foreach (config('inventarios.valores_producto') as $campo => [$etiqueta, $maximo, $decimales])
                            @isset($fila[$campo])
                                <dt class="col-sm-5">{{ $etiqueta }}</dt>
                                <dd class="col-sm-7">{{ str_starts_with($campo, 'descuento_') ? '' : 'USD ' }}{{ number_format((float) $fila[$campo], $decimales) }}</dd>
                            @endisset
                        @endforeach
                    </dl>
                @endif
                @if ($seccion === 'plantillas-contables')
                    <h2 class="h6 mt-3">Categorías asignadas</h2>
                    @if ($categoriasPlantilla->isEmpty())
                        <p class="text-body-secondary mb-0">Sin categorías asignadas.</p>
                    @else
                        <ul class="mb-0">
                            @foreach ($categoriasPlantilla as $categoria)
                                <li>{{ $categoria->nombre }}{{ $categoria->activo ? '' : ' (inactiva)' }}</li>
                            @endforeach
                        </ul>
                    @endif
                @endif
                @if ((array) ($fila['cuentas_efectivas'] ?? $fila['cuentas'] ?? []))
                    <h2 class="h6 mt-3">Cuentas contables</h2>
                    <dl class="row mb-0">
                        @foreach (($fila['cuentas_efectivas'] ?? $fila['cuentas']) as $rol => $id)
                            <dt class="col-sm-5">{{ config('inventarios.cuentas_producto.'.$rol.'.0') }}</dt>
                            <dd class="col-sm-7">{{ $cuentas->has($id) ? $cuentas[$id]->codigo.' · '.$cuentas[$id]->nombre : '#'.$id }}</dd>
                        @endforeach
                    </dl>
                @endif
                @if (!empty($fila['ficha_tecnica_url']))
                    <a href="{{ $fila['ficha_tecnica_url'] }}" target="_blank" rel="noopener noreferrer">Abrir ficha técnica</a>
                @endif
                @if ($galeria && $galeria['imagenes']->isNotEmpty())
                    <h2 class="h6 mt-3">Imágenes</h2>
                    <div class="producto-galeria">
                        @foreach ($galeria['imagenes'] as $imagen)
                            <img class="producto-galeria-foto" src="{{ $imagen['url'] }}" alt="Imagen {{ $loop->iteration }} del producto" loading="lazy">
                        @endforeach
                    </div>
                @endif
                @isset($fila['lineas'])
                    <div class="table-responsive"><table class="table table-sm">
                        <caption class="visually-hidden">Movimientos de existencias</caption>
                        <thead><tr><th>{{ __('Bodega') }}</th><th>Variación</th><th>Saldo anterior</th><th>Saldo</th></tr></thead>
                        <tbody>@foreach ($fila['lineas'] as $linea)
                            <tr><td>{{ $linea['bodega'] }}</td><td>{{ $linea['variacion'] }}</td><td>{{ $linea['saldo_anterior'] }}</td><td>{{ $linea['saldo'] }}</td></tr>
                        @endforeach</tbody>
                    </table></div>
                @endisset
            </div>
            <div class="card-footer text-end"><a href="{{ route('inventarios.'.$seccion.'.index') }}" class="btn btn-outline-secondary" data-ajax-close>{{ __('Cerrar') }}</a></div>
        </section>
    </div></div>
@endsection
