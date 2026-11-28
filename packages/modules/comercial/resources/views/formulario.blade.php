@extends('layouts.adminlte')

@php
    $config = config('comercial.'.$modulo);
    $ruta = $modulo.'.'.$seccion;
    $titulo = $config['secciones'][$seccion][2];
    $titulo = $creditoTotal ? 'Anular con nota de crédito' : $titulo;
    $vinculado = in_array($seccion, \Aitrol\Comercial\Application\DocumentosComerciales::VINCULADOS, true);
    $retencion = $seccion === 'retenciones';
    $retencionElectronica = $modulo === 'compras' && $retencion;
    $baseRenta = $retencion && $origen ? \Aitrol\Inventarios\Domain\CantidadInventario::decimal((int) $origen->subtotal, 2) : null;
    $baseIva = $retencion && $origen ? \Aitrol\Inventarios\Domain\CantidadInventario::decimal((int) $origen->impuesto, 2) : null;
    $fiscalSustento = $retencionElectronica && $origen ? json_decode($origen->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR) : [];
@endphp
@section('title', $titulo)
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <form method="POST" action="{{ route($ruta.'.store') }}" class="card" data-ajax-form data-comercial-documento
                @if ($retencion) data-formulario-retencion data-base-renta="{{ $baseRenta }}" data-base-iva="{{ $baseIva }}" @endif>
                @csrf
                <h1 class="card-header fs-5">{{ $titulo }}</h1>
                @include('comercial::errores')
                <div class="card-body">
                    @if ($retencion && $modulo === 'ventas')
                        <a class="btn btn-outline-primary btn-sm mb-3" data-ajax-modal-page
                            href="{{ route('ventas.retenciones.recibida', $origen ? ['origen' => $origen->id] : []) }}">
                            <i class="bi bi-file-earmark-code me-1" aria-hidden="true"></i> Importar por XML o clave de acceso
                        </a>
                    @endif
                    <div class="row g-3">
                        @unless ($retencionElectronica)
                        <div class="col-sm-6">
                            <label class="form-label" for="com-numero">Número de documento</label>
                            <input name="numero" class="form-control" id="com-numero" maxlength="60" value="{{ old('numero') }}" required autofocus>
                        </div>
                        @endunless
                        <div class="col-sm-6">
                            <label class="form-label" for="com-fecha">{{ __('Fecha') }}</label>
                            <input type="date" name="fecha" class="form-control" id="com-fecha" value="{{ old('fecha', $fecha) }}"
                                min="{{ $periodo }}-01-01" max="{{ $periodo }}-12-31" required>
                        </div>
                        <div @class(['col-sm-6' => $seccion === 'documentos', 'col-12' => $seccion !== 'documentos'])>
                            <label class="form-label" for="com-tercero">{{ $vinculado ? 'Documento de origen' : $config['tercero'] }}</label>
                            @if ($origen)
                                <input type="hidden" name="id_documento_origen" value="{{ $origen->id }}">
                                <input id="com-tercero" class="form-control" value="{{ $origen->numero }} · {{ $origen->tercero }}" readonly>
                            @else
                            <select id="com-tercero" class="form-select" name="{{ $vinculado ? 'id_documento_origen' : 'id_tercero' }}"
                                data-select2-url="{{ route($ruta.'.opciones', $vinculado ? 'origenes' : 'terceros') }}" data-select2-min="0"
                                @if ($retencion) data-retencion-origen @endif required>
                                <option value="">{{ __('Seleccionar') }}</option>
                            </select>
                            @endif
                        </div>
                        @if ($retencion)
                            <div class="col-12" data-retencion-bases @if (!$origen) hidden @endif>
                                <div class="d-flex flex-wrap gap-3 rounded border bg-body-tertiary px-3 py-2">
                                    <span>Subtotal sin IVA <strong class="ms-1" data-retencion-base-renta>USD {{ $baseRenta }}</strong></span>
                                    <span>IVA de la factura <strong class="ms-1" data-retencion-base-iva>USD {{ $baseIva }}</strong></span>
                                </div>
                            </div>
                        @endif
                        @if ($seccion === 'documentos')
                            <div class="col-sm-6">
                                <label class="form-label" for="com-bodega">Bodega</label>
                                <select id="com-bodega" class="form-select" name="id_bodega" data-select2-url="{{ route($ruta.'.opciones', 'bodegas') }}" data-select2-min="0" data-select2-placeholder="Sin movimiento de inventario">
                                    <option value="">Sin movimiento de inventario</option>
                                </select>
                            </div>
                        @endif
                        @if ($retencionElectronica)
                            <div class="col-md-7">
                                <label class="form-label" for="retencion-sustento">Sustento tributario</label>
                                <select class="form-select" id="retencion-sustento" name="codigo_sustento" required>
                                    <option value="">Seleccionar</option>
                                    @foreach (\App\Services\RetencionElectronica::SUSTENTOS as $codigo => $nombre)
                                        <option value="{{ $codigo }}" @selected(old('codigo_sustento') === $codigo)>{{ $codigo }} · {{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label" for="retencion-relacionada">¿Parte relacionada?</label>
                                <select class="form-select" id="retencion-relacionada" name="parte_relacionada" required>
                                    <option value="">Seleccionar</option>
                                    <option value="NO" @selected(old('parte_relacionada') === 'NO')>No</option>
                                    <option value="SI" @selected(old('parte_relacionada') === 'SI')>Sí</option>
                                </select>
                            </div>
                            @if (empty($fiscalSustento['xml_recibido']) && (!$origen || $origen->lineas->contains(fn ($linea) => (int) $linea->tasa === 0)))
                                <div class="col-md-6">
                                    <label class="form-label" for="retencion-iva-cero">Tratamiento de partidas sin IVA</label>
                                    <select class="form-select" id="retencion-iva-cero" name="iva_cero">
                                        <option value="">Seleccionar si aplica</option>
                                        @foreach (['0' => 'IVA 0 %', '6' => 'No objeto de IVA', '7' => 'Exento de IVA'] as $codigo => $nombre)
                                            <option value="{{ $codigo }}" @selected(old('iva_cero') == $codigo && old('iva_cero') !== null)>{{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            @if (empty($fiscalSustento['pagos']) && empty($fiscalSustento['forma_pago']))
                                <div class="col-md-6">
                                    <label class="form-label" for="retencion-pago">Forma de pago de la factura, si falta</label>
                                    <select class="form-select" id="retencion-pago" name="forma_pago">
                                        <option value="">Seleccionar si aplica</option>
                                        @foreach (\App\Services\FacturaVenta::PAGOS as $codigo => $pago)
                                            <option value="{{ $codigo }}" @selected(old('forma_pago') === $codigo)>{{ $codigo }} · {{ $pago }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        @else
                        <div class="col-12">
                            <label class="form-label" for="com-observacion">{{ $creditoTotal ? 'Motivo de la nota de crédito' : 'Observación' }}</label>
                            <textarea id="com-observacion" name="observacion" class="form-control" rows="2" maxlength="500" @required($creditoTotal)>{{ old('observacion') }}</textarea>
                        </div>
                        @endif
                    </div>
                    @if ($creditoTotal)
                        <input type="hidden" name="credito_total" value="1">
                        <p class="mb-0 mt-3">Total a acreditar: USD {{ \Aitrol\Inventarios\Domain\CantidadInventario::decimal((int) $origen->total, 2) }}</p>
                    @else
                    <div class="d-flex justify-content-between align-items-center gap-2 mt-4 mb-2">
                        <h2 class="h6 mb-0">{{ $retencion ? 'Detalle de retenciones' : 'Detalle del documento' }}</h2>
                        <button type="button" data-comercial-agregar class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Agregar línea</button>
                    </div>
                    @if ($seccion === 'documentos')
                        <p class="form-text">Selecciona una bodega si incluyes productos de inventario. Para servicios, escribe el concepto y deja el producto vacío.</p>
                    @endif
                    <div class="d-grid gap-2" data-comercial-lineas>
                        @foreach (array_values((array) old('lineas', [[]])) as $indice => $linea)
                            @php
                                $linea = is_array($linea) ? $linea : [];
                                if ($retencion) {
                                    $linea['base_retencion'] = ($linea['tipo_retencion'] ?? 'renta') === 'iva' ? $baseIva : $baseRenta;
                                }
                            @endphp
                            @include('comercial::linea', ['indice' => $indice, 'linea' => $linea])
                        @endforeach
                    </div>
                    <template data-comercial-plantilla>@include('comercial::linea', ['indice' => '__INDICE__', 'linea' => []])</template>
                    @if ($retencion)
                        <div class="d-flex justify-content-end gap-2 align-items-baseline mt-3 pt-3 border-top">
                            <span>Total retenido</span>
                            <output class="fw-bold fs-5" data-retencion-total>$0,00</output>
                        </div>
                    @endif
                    @endif
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route($ruta.'.index') }}" data-ajax-close>{{ __('Cancelar') }}</a>
                        <button class="btn btn-primary">{{ $retencionElectronica ? 'Generar retención' : 'Registrar documento' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
