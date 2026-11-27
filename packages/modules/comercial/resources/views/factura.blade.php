@extends('layouts.adminlte')

@section('title', 'Nueva factura')

@push('styles')
    <link rel="stylesheet" href="{{ asset('factura-venta.css') }}?v={{ filemtime(public_path('factura-venta.css')) }}">
@endpush

@section('content')
@php
    use App\Services\FacturaVenta;
    $hoy = now('America/Guayaquil')->toDateString();
    $cierre = min($periodo.'-12-31', $hoy);
@endphp
<main class="app-content factura-venta py-3 py-lg-4">
    <div class="container-fluid">
        <header class="factura-cabecera">
            <div>
                <a href="{{ route('ventas.documentos.index') }}" data-workspace-link="ventas.documentos.index" data-workspace-label="Lista de ventas" data-workspace-forward><i class="bi bi-arrow-left" aria-hidden="true"></i> Lista de ventas</a>
                <h1>Nueva factura</h1>
                <p>Período {{ $periodo }} · USD</p>
            </div>
            <a href="{{ route('ventas.facturas-recurrentes.index') }}" class="btn btn-outline-primary"
                data-workspace-link="ventas.facturas-recurrentes.index" data-workspace-label="Facturas recurrentes" data-workspace-forward><i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i> Facturas recurrentes</a>
        </header>
        @include('comercial::errores')
        @if ($borrador)
            <p class="alert alert-info">Borrador programado para {{ $borrador->fecha }}. Revisa los datos antes de registrarlo.</p>
        @endif
        <form method="POST" action="{{ route('ventas.documentos.store') }}" class="factura-distribucion" data-comercial-documento data-factura>
            @csrf
            @if ($borrador)<input type="hidden" name="borrador" value="{{ $borrador->id }}">@endif
            <div class="factura-cuerpo">
                <section class="factura-seccion" aria-labelledby="factura-encabezado-datos">
                    <h2 id="factura-encabezado-datos"><i class="bi bi-person-vcard" aria-hidden="true"></i> Cliente y comprobante</h2>
                    <div class="row g-3">
                        <div class="col-lg-5">
                            <label class="form-label" for="factura-cliente">Cliente</label>
                            <select id="factura-cliente" name="id_tercero" class="form-select" required
                                data-select2-url="{{ route('ventas.documentos.opciones', 'terceros') }}" data-select2-min="0" data-select2-placeholder="Busca por nombre o identificación">
                                <option value=""></option>
                                @if ($cliente)<option value="{{ $cliente->id }}" selected>{{ $cliente->identificacion }} · {{ $cliente->nombre }}</option>@endif
                            </select>
                        </div>
                        <div class="col-sm-7 col-lg-4">
                            <label class="form-label" for="factura-numero">Número de factura</label>
                            <input id="factura-numero" name="numero" class="form-control" value="{{ old('numero') }}" required
                                placeholder="001-001-000000001" maxlength="17" pattern="[0-9]{3}-[0-9]{3}-[0-9]{9}">
                        </div>
                        <div class="col-sm-5 col-lg-3">
                            <label class="form-label" for="factura-fecha">Fecha de emisión</label>
                            <input id="factura-fecha" type="date" name="fecha" class="form-control" required
                                min="{{ $periodo }}-01-01" max="{{ $cierre }}" value="{{ old('fecha', $fecha) }}">
                        </div>
                    </div>
                </section>
                <section class="factura-seccion factura-detalle" aria-labelledby="factura-encabezado-detalle">
                    <div class="factura-seccion-barra">
                        <h2 id="factura-encabezado-detalle"><i class="bi bi-box-seam" aria-hidden="true"></i> Productos y servicios</h2>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-comercial-agregar><i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Agregar línea</button>
                    </div>
                    <p class="small text-body-secondary">Elige la bodega de salida de cada producto. El IVA se toma de su ficha. Si ingresas un servicio manual, indica su tratamiento de IVA.</p>
                    <div class="d-grid gap-2" data-comercial-lineas>
                        @foreach ($lineas ?: [[]] as $indice => $linea)
                            @include('comercial::factura-linea', ['indice' => $indice, 'linea' => is_array($linea) ? $linea : []])
                        @endforeach
                    </div>
                    <template data-comercial-plantilla>
                        @include('comercial::factura-linea', ['indice' => '__INDICE__', 'linea' => []])
                    </template>
                </section>
                <section class="factura-seccion" aria-labelledby="factura-encabezado-pago">
                    <h2 id="factura-encabezado-pago"><i class="bi bi-credit-card" aria-hidden="true"></i> Pago e información adicional</h2>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="factura-pago">Forma de pago</label>
                            <select id="factura-pago" name="forma_pago" class="form-select" required>
                                <option value="">Selecciona una forma de pago</option>
                                @foreach (FacturaVenta::PAGOS as $codigo => $nombre)
                                    <option value="{{ $codigo }}" @selected((string) old('forma_pago', $datos['forma_pago'] ?? '') === (string) $codigo)>{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="factura-plazo">Días de crédito</label>
                            <input id="factura-plazo" type="number" name="plazo_dias" class="form-control" min="0" max="3650" value="{{ old('plazo_dias', $datos['plazo_dias'] ?? 0) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="factura-observacion">Observación <span class="text-body-secondary fw-normal">opcional</span></label>
                            <textarea id="factura-observacion" name="observacion" rows="2" maxlength="500" class="form-control">{{ old('observacion', $datos['observacion'] ?? '') }}</textarea>
                        </div>
                    </div>
                </section>
                @unless ($borrador)
                    <section class="factura-seccion factura-recurrencia" aria-labelledby="factura-encabezado-programacion">
                        <h2 id="factura-encabezado-programacion"><i class="bi bi-arrow-repeat" aria-hidden="true"></i> Facturación recurrente</h2>
                        <p class="small text-body-secondary">Cada vencimiento prepara un borrador para revisar y registrar.</p>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="factura-frecuencia">Frecuencia</label>
                                <select id="factura-frecuencia" name="frecuencia" class="form-select" data-select2-nativo data-factura-frecuencia>
                                    <option value="">Sin recurrencia</option>
                                    @foreach (FacturaVenta::FRECUENCIAS as $valor => $nombre)
                                        <option value="{{ $valor }}" @selected(old('frecuencia') === $valor)>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6" data-factura-programacion>
                                <label class="form-label" for="factura-primera">Primera fecha programada</label>
                                <input id="factura-primera" name="primera_fecha" type="date" class="form-control" value="{{ old('primera_fecha') }}" min="{{ $hoy }}">
                            </div>
                        </div>
                    </section>
                @endunless
            </div>
            <aside class="factura-resumen">
                <section class="factura-seccion" aria-labelledby="factura-encabezado-total">
                    <h2 id="factura-encabezado-total"><i class="bi bi-calculator" aria-hidden="true"></i> Resumen de factura</h2>
                    <dl class="factura-totales" aria-live="polite">
                        <div><dt>Subtotal bruto</dt><dd data-factura-total="bruto">$0.00</dd></div>
                        <div><dt>Descuento</dt><dd data-factura-total="descuento">$0.00</dd></div>
                        <div><dt>Base sin impuestos</dt><dd data-factura-total="base">$0.00</dd></div>
                        <div><dt>IVA</dt><dd data-factura-total="iva">$0.00</dd></div>
                        <div>
                            <dt><label for="factura-propina" class="d-flex align-items-center gap-2">
                                <input id="factura-propina" class="form-check-input mt-0" type="checkbox" name="aplicar_propina" value="1" @checked(old('aplicar_propina', $datos['aplicar_propina'] ?? false))>
                                Propina (10 %)
                            </label></dt>
                            <dd data-factura-total="propina">$0.00</dd>
                        </div>
                        <div class="factura-total"><dt>Total</dt><dd data-factura-total="total">$0.00</dd></div>
                    </dl>
                    <button class="btn btn-primary w-100"><i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Registrar factura</button>
                    <p class="small text-body-secondary mt-3 mb-0">Registro en Aitrol. La emisión electrónica requiere firma y autorización del SRI.</p>
                </section>
            </aside>
        </form>
    </div>
</main>
@endsection
