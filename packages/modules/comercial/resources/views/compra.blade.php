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
        <section class="factura-seccion mb-3" aria-labelledby="compra-importar">
            <details class="compra-importador" @if ($errors->hasAny(['xml_compra', 'clave_compra', 'compra_duplicada'])) open @endif>
                <summary class="compra-importador-resumen">
                    <span class="compra-importador-icono"><i class="bi bi-file-earmark-code" aria-hidden="true"></i></span>
                    <span>
                        <strong id="compra-importar" class="d-block">Importar factura electrónica</strong>
                        <span class="small text-body-secondary">Archivo XML o clave de acceso del SRI</span>
                    </span>
                    <i class="bi bi-chevron-down compra-importador-flecha" aria-hidden="true"></i>
                </summary>
                <div class="compra-importador-opciones">
                    <form method="POST" action="{{ route('compras.documentos.importar') }}" enctype="multipart/form-data" class="compra-importador-opcion">
                        @csrf
                        <label for="compra-xml" class="form-label fw-semibold">Archivo XML</label>
                        <input id="compra-xml" name="xml_compra" type="file" accept=".xml,text/xml,application/xml"
                            class="form-control compra-importador-archivo" required>
                        <button type="submit" class="btn btn-primary align-self-start">
                            <i class="bi bi-upload me-1" aria-hidden="true"></i> Importar XML
                        </button>
                    </form>
                    <form method="POST" action="{{ route('compras.documentos.importar') }}" class="compra-importador-opcion">
                        @csrf
                        <label for="compra-clave" class="form-label fw-semibold">Clave de acceso del SRI</label>
                        <input id="compra-clave" name="clave_compra" type="text" inputmode="numeric" pattern="[0-9]{49}"
                            maxlength="49" value="{{ old('clave_compra') }}" placeholder="49 dígitos" class="form-control" required>
                        <button type="submit" class="btn btn-outline-primary align-self-start">
                            <i class="bi bi-search me-1" aria-hidden="true"></i> Consultar SRI
                        </button>
                    </form>
                </div>
            </details>
            @if ($importacion)
                <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                    <span>{{ $importacion['fiscal']['fuente_compra'] === 'sri' ? 'XML autorizado recibido del SRI' : 'XML del proveedor cargado' }}</span>
                    <a href="{{ route('compras.documentos.create') }}" class="btn btn-sm btn-outline-secondary">Empezar otra compra</a>
                </div>
                @unless ($proveedor)
                    <p class="alert alert-warning mt-3 mb-0">Registra o activa al proveedor {{ $importacion['proveedor'] }} con RUC {{ $importacion['ruc_emisor'] }} antes de guardar esta compra.</p>
                @endunless
            @elseif (filled($token))
                <p class="alert alert-warning mt-3 mb-0">La importación ya no está disponible. Carga nuevamente el XML o consulta al SRI.</p>
            @endif
        </section>
        <form method="POST" action="{{ route('compras.documentos.store') }}" class="factura-distribucion" data-comercial-documento data-compra>
            @csrf
            @if ($importacion)
                <input type="hidden" name="importacion_compra" value="{{ $token }}">
            @endif
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
                            @if ($importacion && $proveedor)
                                <div class="form-text">
                                    @if ($proveedor->direccion)<div>{{ $proveedor->direccion }}</div>@endif
                                    @if ($proveedor->email)<div>{{ $proveedor->email }}</div>@endif
                                    @if ($proveedor->telefono || $proveedor->celular)
                                        <div>{{ implode(' · ', array_filter([$proveedor->telefono, $proveedor->celular])) }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-6 col-md-5">
                            <label class="form-label" for="compra-fecha">Fecha de emisión</label>
                            <input id="compra-fecha" name="fecha" type="date" class="form-control" value="{{ $importacion['fecha'] ?? old('fecha', $fecha) }}"
                                min="{{ $periodo }}-01-01" max="{{ $periodo }}-12-31" required @readonly($importacion !== null)>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label" for="compra-numero">Número del comprobante</label>
                            <input id="compra-numero" name="numero" class="form-control" maxlength="60" value="{{ $importacion['numero'] ?? old('numero') }}" required @readonly($importacion !== null)>
                        </div>
                        <div class="col-md-7">
                            @if (!empty($importacion['fiscal']['pagos']))
                                <span class="form-label d-block">Formas de pago del comprobante</span>
                                <ul class="mb-0">
                                    @foreach ($importacion['fiscal']['pagos'] as $pago)
                                        <li>{{ $pago['codigo'] }} · {{ \App\Services\FacturaVenta::PAGOS[$pago['codigo']] }} · USD {{ $pago['importe'] }}
                                            @if (filled($pago['plazo'])) · {{ $pago['plazo'] }} {{ $pago['unidad_tiempo'] }} @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <label class="form-label" for="compra-pago">Forma de pago</label>
                                <select id="compra-pago" name="forma_pago" class="form-select" required>
                                    <option value="">Seleccionar</option>
                                    @foreach (\App\Services\FacturaVenta::PAGOS as $codigo => $nombre)
                                        <option value="{{ $codigo }}" @selected((string) old('forma_pago') === (string) $codigo)>{{ $codigo }} · {{ $nombre }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="compra-recepcion">Bodega de recepción</label>
                            <input id="compra-recepcion" class="form-control" data-compra-bodega
                                data-bodega-principal="{{ $bodegaPrincipal ? $bodegaPrincipal->codigo.' · '.$bodegaPrincipal->nombre : 'Sin bodega principal configurada' }}"
                                value="{{ $bodegaPrincipal ? $bodegaPrincipal->codigo.' · '.$bodegaPrincipal->nombre : 'Sin bodega principal configurada' }}" readonly>
                        </div>
                    </div>
                </section>
                <section class="factura-seccion factura-detalle" aria-labelledby="compra-detalle">
                    <div class="factura-seccion-barra">
                        <h2 id="compra-detalle"><i class="bi bi-box-seam" aria-hidden="true"></i> Productos y servicios</h2>
                        @unless ($importacion)
                            <button type="button" class="btn btn-outline-primary btn-sm" data-comercial-agregar><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Agregar línea</button>
                        @endunless
                    </div>
                    <p class="form-text">Solo los productos inventariables ingresan a bodega. Puedes seleccionar productos no inventariables para compras de consumo.</p>
                    @php
                        $opcionesLinea = ['ruta' => 'compras.documentos', 'retencion' => false, 'vinculado' => false, 'compra' => true, 'importada' => $importacion !== null];
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
        @if ($puedeCrearProducto)
            @include('comercial::producto-compra')
        @endif
    </div>
</main>
@endsection
