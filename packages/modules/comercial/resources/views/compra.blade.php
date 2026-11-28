@extends('layouts.adminlte')

@section('title', 'Nueva compra')

@push('styles')
    <link rel="stylesheet" href="{{ asset('factura-venta.css') }}?v={{ filemtime(public_path('factura-venta.css')) }}">
@endpush

@section('content')
<main class="app-content factura-venta py-3 py-lg-4">
    <div class="container-fluid">
        <header class="factura-cabecera">
            <div>
                <a href="{{ route('compras.documentos.index') }}" data-workspace-link="compras.documentos.index"
                    data-workspace-label="Lista de compras" data-workspace-forward><i class="bi bi-arrow-left" aria-hidden="true"></i> Lista de compras</a>
                <h1>Nueva compra</h1>
                <p>Período {{ $periodo }} · USD</p>
            </div>
        </header>
        @include('comercial::errores')
        <form method="POST" action="{{ route('compras.documentos.store') }}" class="factura-distribucion" data-comercial-documento data-compra>
            @csrf
            <div class="factura-cuerpo">
                <section class="factura-seccion" aria-labelledby="compra-datos">
                    <h2 id="compra-datos"><i class="bi bi-receipt" aria-hidden="true"></i> Proveedor y comprobante recibido</h2>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label" for="compra-proveedor">Proveedor</label>
                            <select id="compra-proveedor" name="id_tercero" class="form-select" data-select2-min="0"
                                data-select2-url="{{ route('compras.documentos.opciones', 'terceros') }}" data-select2-placeholder="Busca por nombre o identificación" required>
                                <option value=""></option>
                                @if ($proveedor)
                                    <option value="{{ $proveedor->id }}" selected>{{ $proveedor->identificacion }} · {{ $proveedor->nombre }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-5">
                            <label class="form-label" for="compra-fecha">Fecha de emisión</label>
                            <input id="compra-fecha" name="fecha" type="date" class="form-control" value="{{ old('fecha', $fecha) }}"
                                min="{{ $periodo }}-01-01" max="{{ $periodo }}-12-31" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label" for="compra-numero">Número del comprobante</label>
                            <input id="compra-numero" name="numero" class="form-control" maxlength="60" value="{{ old('numero') }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="compra-recepcion">Bodega de recepción</label>
                            <input id="compra-recepcion" class="form-control"
                                value="{{ $bodegaPrincipal ? $bodegaPrincipal->codigo.' · '.$bodegaPrincipal->nombre : 'Sin bodega principal configurada' }}" readonly>
                        </div>
                    </div>
                </section>
                <section class="factura-seccion factura-detalle" aria-labelledby="compra-detalle">
                    <div class="factura-seccion-barra">
                        <h2 id="compra-detalle"><i class="bi bi-box-seam" aria-hidden="true"></i> Productos y servicios</h2>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-comercial-agregar><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Agregar línea</button>
                    </div>
                    <p class="form-text">La mercadería se recibe en la bodega principal de la empresa. Para servicios, escribe el concepto y deja el producto vacío.</p>
                    @php
                        $opcionesLinea = ['ruta' => 'compras.documentos', 'retencion' => false, 'vinculado' => false, 'compra' => true];
                    @endphp
                    <div class="d-grid gap-2" data-comercial-lineas>
                        @foreach ($lineas as $indice => $linea)
                            @php $linea = is_array($linea) ? $linea : []; @endphp
                            @include('comercial::linea', $opcionesLinea + [
                                'indice' => $indice, 'linea' => $linea,
                                'productoSeleccionado' => $productos->get((int) ($linea['id_producto'] ?? 0)),
                            ])
                        @endforeach
                    </div>
                    <template data-comercial-plantilla>
                        @include('comercial::linea', $opcionesLinea + ['indice' => '__INDICE__', 'linea' => []])
                    </template>
                </section>
                <section class="factura-seccion">
                    <label class="form-label" for="compra-observacion">Observación</label>
                    <textarea id="compra-observacion" name="observacion" class="form-control" rows="2" maxlength="500">{{ old('observacion') }}</textarea>
                </section>
            </div>
            <aside class="factura-resumen" aria-labelledby="compra-resumen">
                <section class="factura-seccion">
                    <h2 id="compra-resumen"><i class="bi bi-calculator" aria-hidden="true"></i> Resumen de compra</h2>
                    <dl class="factura-totales" aria-live="polite">
                        <div><dt>Subtotal sin impuestos</dt><dd data-compra-total="subtotal">$0,00</dd></div>
                        <div><dt>Impuesto</dt><dd data-compra-total="impuesto">$0,00</dd></div>
                        <div class="factura-total"><dt>Total</dt><dd data-compra-total="total">$0,00</dd></div>
                    </dl>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Registrar compra</button>
                </section>
            </aside>
        </form>
    </div>
</main>
@endsection
