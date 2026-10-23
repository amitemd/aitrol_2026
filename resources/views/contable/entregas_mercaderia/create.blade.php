@extends('contable.ventas.base')

@section('action-content')
<style>
    .delivery-form-page {
        --df-primary: #176aa5;
        --df-primary-dark: #105581;
        --df-accent: #2e9bd2;
        --df-border: #dce6ed;
        --df-text: #243c4d;
        --df-muted: #708493;
        padding-top: 18px;
        font-family: 'Poppins', sans-serif;
    }
    .delivery-form-shell {
        overflow: hidden;
        background: #fff;
        border: 1px solid #d8e3ea;
        border-radius: 12px;
        box-shadow: 0 8px 28px rgba(29, 63, 87, .10);
    }
    .delivery-form-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 22px 24px;
        color: #fff;
        background: linear-gradient(120deg, #155f94 0%, #1e7db7 58%, #2d9bd2 100%);
    }
    .delivery-form-heading { display: flex; align-items: center; gap: 14px; }
    .delivery-form-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 48px;
        width: 48px;
        height: 48px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 12px;
        font-size: 20px;
    }
    .delivery-form-title { margin: 0 0 3px; font-size: 19px; font-weight: 600; }
    .delivery-form-subtitle { margin: 0; color: rgba(255,255,255,.86); font-size: 12px; }
    .delivery-back-button {
        padding: 9px 15px;
        color: #155f94 !important;
        background: #fff;
        border: 0;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(20, 67, 98, .16);
    }
    .delivery-form-body { padding: 22px; background: #f8fafc; }
    .delivery-process-note {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 20px;
        padding: 14px 16px;
        color: #27536d;
        background: #edf7fc;
        border: 1px solid #cce7f5;
        border-left: 4px solid #2e9bd2;
        border-radius: 8px;
        font-size: 12px;
        line-height: 1.6;
    }
    .delivery-process-note i { margin-top: 2px; color: #258ac0; font-size: 18px; }
    .delivery-section {
        margin-bottom: 18px;
        padding: 20px;
        background: #fff;
        border: 1px solid var(--df-border);
        border-radius: 10px;
        box-shadow: 0 2px 7px rgba(29, 63, 87, .035);
    }
    .delivery-section:last-child { margin-bottom: 0; }
    .delivery-section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #edf2f5;
    }
    .delivery-section-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        color: #fff;
        background: var(--df-primary);
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
    }
    .delivery-section-heading h3 { margin: 0; color: var(--df-text); font-size: 15px; font-weight: 600; }
    .delivery-section-heading p { margin: 2px 0 0; color: var(--df-muted); font-size: 11px; }
    .delivery-emission-section { padding: 14px 18px; }
    .delivery-emission-section .delivery-section-heading {
        margin-bottom: 10px;
        padding-bottom: 8px;
    }
    .delivery-emission-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(160px, 230px));
        gap: 16px;
        width: 100%;
        max-width: 476px;
    }
    .delivery-emission-fields .form-group { margin-bottom: 0; }
    .delivery-form-page .form-group { margin-bottom: 16px; }
    .delivery-form-page label {
        margin-bottom: 7px;
        color: #3a5365;
        font-size: 11px;
        font-weight: 600;
    }
    .delivery-required { color: #d9534f; }
    .delivery-form-page .form-control {
        height: 38px;
        color: #354f60;
        background-color: #fff;
        border: 1px solid #cfdae2;
        border-radius: 7px;
        font-size: 12px;
        box-shadow: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .delivery-form-page textarea.form-control { height: auto; min-height: 76px; resize: vertical; }
    .delivery-form-page .form-control:focus {
        border-color: #60aedd;
        box-shadow: 0 0 0 3px rgba(46,155,210,.12);
    }
    .delivery-form-page .select2-container { width: 100% !important; }
    .delivery-form-page .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #cfdae2;
        border-radius: 7px;
    }
    .delivery-form-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 12px;
        padding-right: 34px;
        color: #354f60;
        line-height: 36px;
        font-size: 12px;
    }
    .delivery-form-page .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; right: 6px; }
    .delivery-form-page .select2-container--default.select2-container--focus .select2-selection--single,
    .delivery-form-page .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #60aedd;
        box-shadow: 0 0 0 3px rgba(46,155,210,.12);
    }
    .delivery-products-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }
    .delivery-products-help { margin: 0; color: var(--df-muted); font-size: 11px; }
    .delivery-add-line {
        color: #19704b !important;
        background: #eaf8f1;
        border: 1px solid #bce7d1;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 600;
    }
    .delivery-products-table-wrap {
        width: 100%;
        max-width: 100%;
        overflow: visible;
        border: 1px solid var(--df-border);
        border-radius: 8px;
    }
    .delivery-products-table {
        width: 100% !important;
        min-width: 0;
        max-width: 100%;
        table-layout: fixed;
        margin: 0;
    }
    .delivery-products-table thead th {
        padding: 11px 12px;
        color: #3a5669;
        background: #f0f5f8;
        border: 0 !important;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
    }
    .delivery-products-table tbody td {
        min-width: 0;
        padding: 11px;
        vertical-align: middle;
        border-color: #e7eef2;
        overflow: hidden;
    }
    .delivery-products-table .form-control,
    .delivery-products-table .select2-container {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }
    .delivery-products-table .select2-selection__rendered {
        display: block !important;
        max-width: 100%;
        overflow: hidden !important;
        white-space: nowrap !important;
        text-overflow: ellipsis;
    }
    .delivery-stock-field, .delivery-line-total {
        height: 38px;
        padding: 9px 10px;
        color: #35566b;
        background: #f3f7f9;
        border: 1px solid #d7e2e8;
        border-radius: 7px;
        font-size: 12px;
        white-space: nowrap;
    }
    .delivery-reservation-state { display: block; min-height: 16px; margin-top: 4px; color: #1f8659; font-size: 9px; }
    .delivery-value-summary {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 13px;
        padding: 12px 15px;
        color: #3b5668;
        background: #eef5f8;
        border: 1px solid #d9e7ed;
        border-radius: 8px;
        font-size: 12px;
    }
    .delivery-value-summary strong { color: #176aa5; font-size: 17px; }
    .delivery-remove-line {
        width: 34px;
        height: 34px;
        padding: 0;
        color: #c34b4b !important;
        background: #fff1f1;
        border: 1px solid #f0caca;
        border-radius: 7px;
    }
    .delivery-form-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 17px 22px;
        background: #fff;
        border-top: 1px solid var(--df-border);
    }
    .delivery-cancel-button { padding: 10px 17px; border-radius: 8px; font-size: 12px; }
    .delivery-submit-button {
        padding: 10px 19px;
        color: #fff;
        background: linear-gradient(135deg, var(--df-primary), #238fc9);
        border: 0;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(23,106,165,.24);
    }
    .delivery-submit-button:hover, .delivery-submit-button:focus { color: #fff; background: var(--df-primary-dark); }
    .delivery-error-alert { border-radius: 8px; font-size: 12px; }
    .delivery-error-alert ul { margin-bottom: 0; padding-left: 20px; }
    @media (max-width: 767px) {
        .delivery-form-hero { align-items: flex-start; flex-direction: column; padding: 18px; }
        .delivery-back-button { width: 100%; }
        .delivery-form-body, .delivery-section { padding: 15px; }
        .delivery-products-toolbar, .delivery-form-footer { align-items: stretch; flex-direction: column; }
        .delivery-add-line, .delivery-form-footer .btn { width: 100%; }
        .delivery-products-table-wrap { overflow-x: auto; }
        .delivery-products-table { min-width: 760px; table-layout: fixed; }
        .delivery-emission-fields { grid-template-columns: 1fr; max-width: none; }
    }
</style>

<section class="content delivery-form-page">
    <div class="delivery-form-shell">
        <div class="delivery-form-hero">
            <div class="delivery-form-heading">
                <span class="delivery-form-icon"><i class="fa fa-truck"></i></span>
                <div>
                    <h1 class="delivery-form-title">Nueva entrega de mercadería</h1>
                    <p class="delivery-form-subtitle">Registra la salida física y genera automáticamente su trazabilidad.</p>
                </div>
            </div>
            <a href="{{ route('entregas_mercaderia.index') }}" class="btn delivery-back-button"><i class="fa fa-arrow-left"></i> Regresar al listado</a>
        </div>

        <form method="POST" action="{{ route('entregas_mercaderia.store') }}" id="form-entrega">
            {{ csrf_field() }}
            <input type="hidden" name="idempotency_key" id="idempotency_key" value="{{ $idempotencyKey }}">
            <div class="delivery-form-body">
                <div class="delivery-process-note">
                    <i class="fa fa-info-circle"></i>
                    <div><strong>Confirmación de salida física.</strong> Al guardar se descontará el inventario, se generará el asiento de costo y se creará la guía para autorización del SRI. La factura posterior no volverá a mover inventario.</div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger delivery-error-alert">
                        <strong><i class="fa fa-exclamation-circle"></i> Revisa la información ingresada:</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <div class="delivery-section">
                    <div class="delivery-section-heading">
                        <span class="delivery-section-number">1</span>
                        <div><h3>Destinatario y fechas</h3><p>Selecciona a quién se entrega la mercadería y el período del traslado.</p></div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="id_cliente">Cliente destinatario <span class="delivery-required">*</span></label>
                            <select name="id_cliente" id="id_cliente" class="form-control" required style="width:100%">
                                <option value=""></option>
                                @if($clienteSeleccionado)
                                    <option value="{{ $clienteSeleccionado->identificacion }}" selected>{{ $clienteSeleccionado->identificacion }} · {{ $clienteSeleccionado->nombre }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fecha_entrega">Fecha de entrega <span class="delivery-required">*</span></label>
                            <input id="fecha_entrega" type="date" name="fecha_entrega" value="{{ old('fecha_entrega', date('Y-m-d')) }}" class="form-control" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fecha_fin_traslado">Fin del traslado <span class="delivery-required">*</span></label>
                            <input id="fecha_fin_traslado" type="date" name="fecha_fin_traslado" value="{{ old('fecha_fin_traslado', date('Y-m-d')) }}" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="delivery-section delivery-emission-section">
                    <div class="delivery-section-heading">
                        <span class="delivery-section-number">2</span>
                        <div><h3>Emisión</h3><p>Selecciona el establecimiento y punto que emitirán la guía.</p></div>
                    </div>
                    <div class="delivery-emission-fields">
                        <div class="form-group">
                            <label for="id_sucursal">Sucursal de emisión <span class="delivery-required">*</span></label>
                            <select name="id_sucursal" id="id_sucursal" class="form-control delivery-static-select" required style="width:100%">
                                <option value="">Sucursal</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}" {{ old('id_sucursal') == $sucursal->id ? 'selected' : '' }}>{{ str_pad($sucursal->codigo_sucursal, 3, '0', STR_PAD_LEFT) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_caja">Punto de emisión <span class="delivery-required">*</span></label>
                            <select name="id_caja" id="id_caja" class="form-control delivery-static-select" required style="width:100%">
                                <option value="">Punto</option>
                                @foreach($cajas as $caja)
                                    <option value="{{ $caja->id }}" data-sucursal="{{ $caja->id_sucursal }}" {{ old('id_caja') == $caja->id ? 'selected' : '' }}>{{ str_pad($caja->codigo_caja, 3, '0', STR_PAD_LEFT) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="delivery-section">
                    <div class="delivery-section-heading">
                        <span class="delivery-section-number">3</span>
                        <div><h3>Datos del traslado</h3><p>Completa la información logística que aparecerá en la guía de remisión.</p></div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label for="id_transportista">Transportista <span class="delivery-required">*</span></label>
                            <select name="id_transportista" id="id_transportista" class="form-control delivery-static-select" required style="width:100%">
                                <option value="">Seleccione un transportista</option>
                                @foreach($transportistas as $transportista)
                                    <option value="{{ $transportista->id }}" data-placa="{{ $transportista->placa }}" {{ old('id_transportista') == $transportista->id ? 'selected' : '' }}>{{ $transportista->ci_ruc }} · {{ $transportista->razon_social }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="placa">Placa <span class="delivery-required">*</span></label>
                            <input name="placa" id="placa" value="{{ old('placa') }}" class="form-control" maxlength="20" required placeholder="ABC-1234">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="motivo_traslado">Motivo del traslado <span class="delivery-required">*</span></label>
                            <input id="motivo_traslado" name="motivo_traslado" value="{{ old('motivo_traslado', 'ENTREGA DE MERCADERIA') }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4"><label for="direccion_partida">Dirección de partida <span class="delivery-required">*</span></label><input id="direccion_partida" name="direccion_partida" value="{{ old('direccion_partida') }}" class="form-control" required></div>
                        <div class="form-group col-md-4"><label for="direccion_destino">Dirección de destino <span class="delivery-required">*</span></label><input id="direccion_destino" name="direccion_destino" value="{{ old('direccion_destino') }}" class="form-control" required></div>
                        <div class="form-group col-md-4"><label for="ruta">Ruta</label><input id="ruta" name="ruta" value="{{ old('ruta') }}" class="form-control" placeholder="Detalle de la ruta de traslado"></div>
                    </div>
                    <div class="form-group"><label for="observacion">Observación</label><textarea id="observacion" name="observacion" class="form-control" rows="2" placeholder="Información adicional de la entrega">{{ old('observacion') }}</textarea></div>
                </div>

                <div class="delivery-section">
                    <div class="delivery-section-heading">
                        <span class="delivery-section-number">4</span>
                        <div><h3>Productos a entregar</h3><p>Elige producto, bodega con stock, serie/lote y cantidad. Repite el producto para completar desde otra existencia.</p></div>
                    </div>
                    <div class="delivery-products-toolbar">
                        <p class="delivery-products-help"><i class="fa fa-search"></i> Escribe el código o nombre para consultar el catálogo sin cargarlo completo.</p>
                        <button type="button" class="btn delivery-add-line" id="agregar-linea"><i class="fa fa-plus-circle"></i> Agregar otra serie o bodega</button>
                    </div>
                    <div class="table-responsive delivery-products-table-wrap">
                        <table class="table delivery-products-table" id="tabla-productos">
                            <colgroup>
                                <col style="width:24%">
                                <col style="width:14%">
                                <col style="width:20%">
                                <col style="width:6%">
                                <col style="width:6%">
                                <col style="width:8%">
                                <col style="width:7%">
                                <col style="width:12%">
                                <col style="width:3%">
                            </colgroup>
                            <thead><tr><th>Producto</th><th>Bodega con stock</th><th>Serie / lote</th><th>Disponible</th><th>Cantidad</th><th>Precio de venta</th><th>Total</th><th>Descripción para la guía</th><th></th></tr></thead>
                            <tbody>
                                <?php
                                    $productosOld = (array) old('producto', []);
                                    $cantidadesOld = (array) old('cantidad', []);
                                    $descripcionesOld = (array) old('descripcion', []);
                                    $seriesOld = (array) old('serie', []);
                                    $preciosVentaOld = (array) old('precio_venta', []);
                                    $bodegasOld = (array) old('bodega', []);
                                ?>
                                @foreach($productosOld as $indice => $idProductoOld)
                                    <?php $productoOld = $productosSeleccionados->get($idProductoOld); ?>
                                    <?php $serieOld = isset($seriesOld[$indice]) ? $seriesSeleccionadas->get($seriesOld[$indice]) : null; ?>
                                    <?php $bodegaOld = isset($bodegasOld[$indice]) ? $bodegasSeleccionadas->get($bodegasOld[$indice]) : null; ?>
                                    <tr>
                                        <td><select name="producto[]" class="form-control delivery-product-select" required style="width:100%"><option value=""></option>@if($productoOld)<option value="{{ $productoOld->id }}" selected>{{ $productoOld->codigo }} · {{ $productoOld->nombre }}</option>@endif</select></td>
                                        <td><select name="bodega[]" class="form-control delivery-warehouse-select" required style="width:100%"><option value=""></option>@if($bodegaOld)<option value="{{ $bodegaOld->id }}" selected>{{ $bodegaOld->nombre }}</option>@endif</select><small class="delivery-warehouse-state"></small></td>
                                        <td><select name="serie[]" class="form-control delivery-series-select" required style="width:100%"><option value=""></option>@if($serieOld)<option value="{{ $serieOld->id }}" data-serie="{{ $serieOld->serie }}" data-lote="{{ $serieOld->lote }}" data-fecha-vence="{{ $serieOld->fecha_vence }}" data-existencia="{{ $serieOld->existencia }}" selected>Serie: {{ $serieOld->serie ?: 'N/A' }} · Lote: {{ $serieOld->lote ?: 'N/A' }} · Disponible: {{ $serieOld->existencia }}</option>@endif</select><small class="delivery-reservation-state"></small></td>
                                        <td><div class="delivery-stock-field">{{ $serieOld ? $serieOld->existencia : '—' }}</div></td>
                                        <td><input type="number" name="cantidad[]" min="1" step="1" value="{{ isset($cantidadesOld[$indice]) ? $cantidadesOld[$indice] : 1 }}" class="form-control delivery-quantity" required></td>
                                        <td><input type="number" name="precio_venta[]" min="0" step="0.01" value="{{ isset($preciosVentaOld[$indice]) ? $preciosVentaOld[$indice] : 0 }}" class="form-control delivery-sale-price" required></td>
                                        <td><div class="delivery-line-total">$ 0.00</div></td>
                                        <td><input name="descripcion[]" value="{{ isset($descripcionesOld[$indice]) ? $descripcionesOld[$indice] : '' }}" class="form-control" maxlength="255" placeholder="Descripción opcional"></td>
                                        <td class="text-center"><button type="button" class="btn delivery-remove-line quitar-linea" title="Quitar producto"><i class="fa fa-trash"></i></button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="delivery-value-summary"><span>Total comercial estimado</span><strong>$ <span id="valor-total-entrega">0.00</span></strong></div>
                    <p class="delivery-products-help" style="margin-top:8px"><i class="fa fa-info-circle"></i> El precio de venta puede editarse y se conservará para generar posteriormente la factura. No se enviará ni aparecerá en la guía de remisión.</p>
                </div>
            </div>

            <div class="delivery-form-footer">
                <a href="{{ route('entregas_mercaderia.index') }}" class="btn btn-default delivery-cancel-button">Cancelar</a>
                <button type="submit" class="btn delivery-submit-button" id="guardar-entrega"><i class="fa fa-truck"></i> Confirmar entrega y generar guía</button>
            </div>
        </form>
    </div>
</section>

<script>
$(function () {
    var urls = {
        destinatarios: {!! json_encode(route('entregas_mercaderia.ajax_destinatarios')) !!},
        bodegas: {!! json_encode(route('entregas_mercaderia.ajax_bodegas')) !!},
        productos: {!! json_encode(route('entregas_mercaderia.ajax_productos')) !!},
        series: {!! json_encode(route('entregas_mercaderia.ajax_series')) !!},
        reservar: {!! json_encode(route('entregas_mercaderia.ajax_reservar_serie')) !!},
        liberar: {!! json_encode(route('entregas_mercaderia.ajax_liberar_serie')) !!}
    };
    var tokenReserva = $('#idempotency_key').val();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    function ajaxSelect2($elemento, url, placeholder, minimo, datosExtra) {
        $elemento.select2({
            width: '100%',
            placeholder: placeholder,
            allowClear: true,
            minimumInputLength: minimo,
            ajax: {
                url: url,
                dataType: 'json',
                delay: 300,
                cache: true,
                data: function (params) {
                    var datos = { q: params.term || '', page: params.page || 1 };
                    if (datosExtra) $.extend(datos, datosExtra($elemento));
                    return datos;
                },
                processResults: function (data) { return data; }
            },
            language: {
                inputTooShort: function () { return 'Escribe al menos ' + minimo + ' caracteres'; },
                noResults: function () { return 'No se encontraron resultados'; },
                searching: function () { return 'Buscando...'; },
                loadingMore: function () { return 'Cargando más resultados...'; },
                errorLoading: function () { return 'No fue posible cargar los resultados'; }
            }
        });
    }

    ajaxSelect2($('#id_cliente'), urls.destinatarios, 'Buscar por identificación o nombre', 2);
    $('.delivery-static-select').select2({ width: '100%', placeholder: 'Seleccione una opción', allowClear: true });

    function iniciarProducto($select) {
        ajaxSelect2($select, urls.productos, 'Escribe código o nombre del producto', 1);
    }

    function iniciarBodega($select) {
        var $fila = $select.closest('tr');
        ajaxSelect2($select, urls.bodegas, 'Seleccionar bodega con stock', 0, function () {
            return { id_producto: $fila.find('.delivery-product-select').val() };
        });
        $select.prop('disabled', !$fila.find('.delivery-product-select').val());
    }

    function iniciarSerie($select) {
        var $fila = $select.closest('tr');
        ajaxSelect2($select, urls.series, 'Seleccionar serie o lote disponible', 0, function () {
            return {
                id_bodega_inventario: $fila.find('.delivery-warehouse-select').val(),
                id_producto: $fila.find('.delivery-product-select').val(),
                token: tokenReserva
            };
        });
        $select.prop('disabled', !$fila.find('.delivery-product-select').val() || !$fila.find('.delivery-warehouse-select').val());
    }

    function numero(valor) {
        var resultado = parseFloat(valor);
        return isNaN(resultado) ? 0 : resultado;
    }

    function datosSerieFila($fila, datos) {
        if (datos && typeof datos.existencia !== 'undefined') return datos;
        var $opcion = $fila.find('.delivery-series-select option:selected');
        return {
            id: $opcion.val(),
            serie: $opcion.data('serie'),
            lote: $opcion.data('lote'),
            fecha_vence: $opcion.data('fecha-vence'),
            existencia: $opcion.data('existencia')
        };
    }

    function actualizarFila($fila, datos) {
        var disponible = numero(datos.existencia);
        $fila.data('disponible', disponible);
        $fila.find('.delivery-stock-field').text(disponible.toFixed(4).replace(/\.?0+$/, ''));
        $fila.find('.delivery-quantity').attr('max', disponible);
        actualizarTotales();
    }

    function actualizarTotales() {
        var total = 0;
        $('#tabla-productos tbody tr').each(function () {
            var $fila = $(this);
            var valor = numero($fila.find('.delivery-sale-price').val()) * numero($fila.find('.delivery-quantity').val());
            total += valor;
            $fila.find('.delivery-line-total').text('$ ' + valor.toFixed(2));
        });
        $('#valor-total-entrega').text(total.toFixed(2));
    }

    function liberarSerie(idSerie) {
        if (!idSerie) return;
        $.ajax({
            url: urls.liberar,
            method: 'POST',
            data: { _token: csrfToken, token: tokenReserva, id_inv_inventario_serie: idSerie }
        });
    }

    function reservarFila($fila, silencioso) {
        var idProducto = $fila.find('.delivery-product-select').val();
        var idSerie = $fila.find('.delivery-series-select').val();
        var cantidad = numero($fila.find('.delivery-quantity').val());
        if (!idProducto || !idSerie || cantidad <= 0) return;
        $fila.data('reserva-valida', false);
        $fila.find('.delivery-reservation-state').removeClass('text-danger').text('Reservando existencia...');
        $.ajax({
            url: urls.reservar,
            method: 'POST',
            dataType: 'json',
            data: {
                _token: csrfToken,
                token: tokenReserva,
                id_bodega_inventario: $fila.find('.delivery-warehouse-select').val(),
                id_producto: idProducto,
                id_inv_inventario_serie: idSerie,
                cantidad: cantidad
            }
        }).done(function (respuesta) {
            $fila.data('reserva-valida', true).data('disponible', numero(respuesta.disponible));
            $fila.find('.delivery-stock-field').text(numero(respuesta.disponible).toFixed(4).replace(/\.?0+$/, ''));
            $fila.find('.delivery-quantity').attr('max', respuesta.disponible);
            $fila.find('.delivery-reservation-state').removeClass('text-danger').text('Existencia reservada por 15 minutos');
        }).fail(function (xhr) {
            var respuestaError = xhr.responseJSON || {};
            var mensaje = respuestaError.message || '';
            if (!mensaje) {
                $.each(respuestaError, function (campo, errores) {
                    if (!mensaje && $.isArray(errores) && errores.length) mensaje = errores[0];
                });
            }
            if (!mensaje) mensaje = 'No fue posible reservar esta existencia.';
            $fila.data('reserva-valida', false);
            $fila.find('.delivery-reservation-state').addClass('text-danger').text(mensaje);
            if (!silencioso) window.alert(mensaje);
        });
    }

    function agregarLinea() {
        var fila = '' +
            '<tr>' +
                '<td><select name="producto[]" class="form-control delivery-product-select" required style="width:100%"><option value=""></option></select></td>' +
                '<td><select name="bodega[]" class="form-control delivery-warehouse-select" required style="width:100%"><option value=""></option></select><small class="delivery-warehouse-state"></small></td>' +
                '<td><select name="serie[]" class="form-control delivery-series-select" required style="width:100%"><option value=""></option></select><small class="delivery-reservation-state"></small></td>' +
                '<td><div class="delivery-stock-field">—</div></td>' +
                '<td><input type="number" name="cantidad[]" min="1" step="1" value="1" class="form-control delivery-quantity" required></td>' +
                '<td><input type="number" name="precio_venta[]" min="0" step="0.01" value="0.00" class="form-control delivery-sale-price" required></td>' +
                '<td><div class="delivery-line-total">$ 0.00</div></td>' +
                '<td><input name="descripcion[]" class="form-control" maxlength="255" placeholder="Descripción opcional"></td>' +
                '<td class="text-center"><button type="button" class="btn delivery-remove-line quitar-linea" title="Quitar producto"><i class="fa fa-trash"></i></button></td>' +
            '</tr>';
        var $fila = $(fila).appendTo('#tabla-productos tbody');
        iniciarProducto($fila.find('.delivery-product-select'));
        iniciarBodega($fila.find('.delivery-warehouse-select'));
        iniciarSerie($fila.find('.delivery-series-select'));
    }

    $('.delivery-product-select').each(function () {
        var $fila = $(this).closest('tr');
        iniciarProducto($(this));
        iniciarBodega($fila.find('.delivery-warehouse-select'));
        iniciarSerie($fila.find('.delivery-series-select'));
        if ($fila.find('.delivery-series-select').val()) {
            $fila.data('serie-reservada', $fila.find('.delivery-series-select').val());
            actualizarFila($fila, datosSerieFila($fila));
            reservarFila($fila, true);
        }
    });
    if (!$('#tabla-productos tbody tr').length) agregarLinea();
    $('#agregar-linea').on('click', agregarLinea);
    $(document).on('select2:select', '.delivery-product-select', function (evento) {
        var $fila = $(this).closest('tr');
        liberarSerie($fila.data('serie-reservada'));
        $fila.removeData('serie-reservada').removeData('reserva-valida');
        $fila.find('.delivery-warehouse-select').val('').prop('disabled', false).trigger('change');
        $fila.find('.delivery-series-select').val('').prop('disabled', true).trigger('change');
        $fila.find('.delivery-stock-field').text('—');
        $fila.find('.delivery-sale-price').val(numero(evento.params.data.precio_venta).toFixed(2));
        $fila.find('.delivery-line-total').text('$ 0.00');
        $fila.find('.delivery-reservation-state').text('');
        $fila.removeData('disponible');
        actualizarTotales();
        $fila.find('.delivery-warehouse-state').text('Selecciona una bodega que tenga stock');
        window.setTimeout(function () { $fila.find('.delivery-warehouse-select').select2('open'); }, 100);
    });
    $(document).on('select2:unselect', '.delivery-product-select', function () {
        var $fila = $(this).closest('tr');
        liberarSerie($fila.data('serie-reservada'));
        $fila.removeData('serie-reservada').removeData('reserva-valida').removeData('disponible');
        $fila.find('.delivery-warehouse-select, .delivery-series-select').val('').prop('disabled', true).trigger('change');
        $fila.find('.delivery-stock-field').text('—');
        $fila.find('.delivery-reservation-state, .delivery-warehouse-state').text('');
    });
    $(document).on('select2:select', '.delivery-warehouse-select', function () {
        var $fila = $(this).closest('tr');
        liberarSerie($fila.data('serie-reservada'));
        $fila.removeData('serie-reservada').removeData('reserva-valida').removeData('disponible');
        $fila.find('.delivery-series-select').val('').prop('disabled', false).trigger('change');
        $fila.find('.delivery-stock-field').text('—');
        $fila.find('.delivery-reservation-state').text('');
        $fila.find('.delivery-warehouse-state').text('Ahora selecciona la serie o lote');
        window.setTimeout(function () { $fila.find('.delivery-series-select').select2('open'); }, 100);
    });
    $(document).on('select2:unselect', '.delivery-warehouse-select', function () {
        var $fila = $(this).closest('tr');
        liberarSerie($fila.data('serie-reservada'));
        $fila.removeData('serie-reservada').removeData('reserva-valida').removeData('disponible');
        $fila.find('.delivery-series-select').val('').prop('disabled', true).trigger('change');
        $fila.find('.delivery-stock-field').text('—');
        $fila.find('.delivery-reservation-state, .delivery-warehouse-state').text('');
    });
    $(document).on('select2:select', '.delivery-series-select', function (evento) {
        var $fila = $(this).closest('tr');
        var idSerie = String(evento.params.data.id);
        var duplicada = false;
        $('.delivery-series-select').not(this).each(function () {
            if (String($(this).val()) === idSerie) duplicada = true;
        });
        if (duplicada) {
            $(this).val('').trigger('change');
            window.alert('Esta serie o lote ya fue agregado en otra línea.');
            return;
        }
        var anterior = $fila.data('serie-reservada');
        if (anterior && String(anterior) !== idSerie) liberarSerie(anterior);
        $fila.data('serie-reservada', idSerie);
        actualizarFila($fila, evento.params.data);
        reservarFila($fila, false);
    });
    $(document).on('change blur', '.delivery-quantity', function () {
        var $fila = $(this).closest('tr');
        actualizarTotales();
        reservarFila($fila, false);
    });
    $(document).on('input change blur', '.delivery-sale-price', actualizarTotales);
    $(document).on('click', '.quitar-linea', function () {
        if ($('#tabla-productos tbody tr').length > 1) {
            var $fila = $(this).closest('tr');
            liberarSerie($fila.data('serie-reservada'));
            $fila.find('.delivery-product-select, .delivery-warehouse-select, .delivery-series-select').select2('destroy');
            $fila.remove();
            actualizarTotales();
        }
    });

    $('#id_transportista').on('change', function () {
        var placa = $(this).find(':selected').data('placa') || '';
        $('#placa').val(placa);
    });

    function filtrarCajas(limpiar) {
        var sucursal = $('#id_sucursal').val();
        $('#id_caja option').each(function () {
            var pertenece = !$(this).val() || String($(this).data('sucursal')) === String(sucursal);
            $(this).prop('disabled', !pertenece);
        });
        if (limpiar) $('#id_caja').val('').trigger('change');
        else $('#id_caja').trigger('change.select2');
    }
    $('#id_sucursal').on('change', function () { filtrarCajas(true); });
    filtrarCajas(false);

    $(document).on('select2:open', function () {
        window.setTimeout(function () { $('.select2-container--open .select2-search__field').focus(); }, 0);
    });

    window.setInterval(function () {
        $('#tabla-productos tbody tr').each(function () { reservarFila($(this), true); });
    }, 240000);

    $('#form-entrega').on('submit', function (evento) {
        var reservaInvalida = false;
        $('#tabla-productos tbody tr').each(function () {
            if ($(this).find('.delivery-series-select').val() && $(this).data('reserva-valida') !== true) reservaInvalida = true;
        });
        if (reservaInvalida) {
            evento.preventDefault();
            window.alert('Espere a que todas las series y lotes confirmen su reserva antes de generar la entrega.');
            return;
        }
        $('#guardar-entrega').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Procesando entrega...');
    });
});
</script>
@endsection
