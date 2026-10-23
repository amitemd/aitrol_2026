@extends('contable.nota_credito_cliente.base')

@section('action-content')
<?php
    $porcentajeIva = isset($porIva->iva) ? $porIva->iva * 100 : 0;
?>

<style>
    .nc-inv-box { margin: 12px 15px; }
    .nc-inv-box .box-header { padding: 12px 15px; }
    .nc-inv-actions { text-align: right; }
    .nc-inv-grid { display: flex; flex-wrap: wrap; margin: 0 -7px; }
    .nc-inv-field { padding: 0 7px; margin-bottom: 12px; }
    .nc-inv-field label { display: block; min-height: 20px; margin-bottom: 5px; }
    .nc-inv-field .form-control, .nc-inv-field .select2-container { width: 100% !important; }
    .nc-inv-section { background: #aaa; color: #fff; font-weight: bold; padding: 7px 10px; margin: 4px 0 12px; }
    .nc-inv-help { background: #e8f7fc; border-left: 4px solid #00a7d0; padding: 9px 12px; margin-bottom: 12px; }
    .nc-inv-table th { background: #9e9e9e; color: #fff; text-align: center; vertical-align: middle !important; }
    .nc-inv-table td { vertical-align: middle !important; }
    .nc-inv-table .cantidad-devolver { width: 100px; text-align: right; margin: auto; }
    .nc-inv-totales { max-width: 390px; margin-left: auto; }
    .nc-inv-total-row { display: flex; justify-content: space-between; padding: 3px 0; font-weight: bold; }
    .nc-inv-total-row input { width: 130px; border: 0; text-align: right; background: transparent; }
    .nc-inv-total-final { border-top: 1px solid #777; font-size: 16px; margin-top: 3px; padding-top: 7px; }
    .nc-inv-hidden { display: none; }
    @media (max-width: 991px) {
        .nc-inv-actions { text-align: left; margin-top: 10px; }
        .nc-inv-actions .btn { margin-bottom: 5px; }
    }
</style>

<section class="content">
    <ol class="breadcrumb">
        <li><a href="#">{{ trans('contableM.contable') }}</a></li>
        <li><a href="{{ route('nota_credito_cliente.index') }}">Nota Crédito Cliente</a></li>
        <li class="active">Crear con devolución de inventario</li>
    </ol>

    <form id="form-nota-inventario" method="post" action="{{ route('nota_credito_cliente.inventario.store') }}">
        {{ csrf_field() }}
        <div class="box nc-inv-box">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-7">
                        <h4 class="box-title"><b>CREAR NOTA DE CRÉDITO CLIENTE CON DEVOLUCIÓN DE INVENTARIO</b></h4>
                    </div>
                    <div class="col-md-5 nc-inv-actions">
                        <button type="submit" id="guardar-nota" class="btn btn-success">
                            <i class="glyphicon glyphicon-floppy-disk"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-info" id="nueva-nota">
                            <i class="glyphicon glyphicon-file"></i> Nuevo
                        </button>
                        <a href="{{ route('nota_credito_cliente.index') }}" class="btn btn-danger">
                            <i class="glyphicon glyphicon-arrow-left"></i> Regresar
                        </a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="nc-inv-section">DATOS DE LA NOTA DE CRÉDITO</div>
                <div class="nc-inv-grid">
                    <div class="nc-inv-field col-md-2 col-sm-4">
                        <label>Estado</label>
                        <input class="form-control" value="ACTIVO" readonly style="background:#158000;color:#fff;font-weight:bold;">
                    </div>
                    <div class="nc-inv-field col-md-2 col-sm-4">
                        <label>Tipo</label>
                        <input class="form-control" name="tipo" value="CLI-NC" readonly>
                    </div>
                    <div class="nc-inv-field col-md-2 col-sm-4">
                        <label>Fecha</label>
                        <input class="form-control" type="date" name="fecha_hoy" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="nc-inv-field col-md-3 col-sm-6">
                        <label>Sucursal</label>
                        <select class="form-control" name="sucursal" id="sucursal" required>
                            <option value="">Seleccione...</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->codigo_sucursal }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="nc-inv-field col-md-3 col-sm-6">
                        <label>Punto de emisión</label>
                        <select class="form-control" name="punto_emision" id="punto_emision" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="nc-inv-field col-md-8 col-sm-8">
                        <label>Concepto</label>
                        <input class="form-control" name="concepto" maxlength="50" required autocomplete="off"
                               placeholder="Ej.: Devolución de mercadería de la factura seleccionada">
                    </div>
                    <div class="nc-inv-field col-md-2 col-sm-2">
                        <label>Electrónica</label>
                        <label class="checkbox-inline"><input type="checkbox" name="electronica" value="1" checked> Sí</label>
                    </div>
                    <div class="nc-inv-field col-md-2 col-sm-2">
                        <label>Archivo SRI</label>
                        <label class="checkbox-inline"><input type="checkbox" name="check_archivo_sri" value="1" checked> Sí</label>
                    </div>
                </div>

                <div class="nc-inv-section">FACTURA Y SALIDA DE INVENTARIO</div>
                <div class="nc-inv-help">
                    Solo se muestran productos que tienen una salida activa de inventario vinculada a la factura. La bodega,
                    serie, lote y costo se toman del movimiento original y no pueden ser reemplazados desde esta pantalla.
                </div>
                <div class="nc-inv-grid">
                    <div class="nc-inv-field col-md-6 col-sm-12">
                        <label>Factura</label>
                        <select class="form-control" name="factura" id="factura" required></select>
                    </div>
                    <div class="nc-inv-field col-md-2 col-sm-4">
                        <label>Cliente</label>
                        <input class="form-control" id="cliente" readonly>
                    </div>
                    <div class="nc-inv-field col-md-2 col-sm-4">
                        <label>Total factura</label>
                        <input class="form-control text-right" id="total_factura" value="0.00" readonly>
                    </div>
                    <div class="nc-inv-field col-md-2 col-sm-4">
                        <label>Saldo contable</label>
                        <input class="form-control text-right" id="saldo_factura" value="0.00" readonly>
                    </div>
                </div>

                <div class="row" style="margin-bottom:10px;">
                    <div class="col-md-6"><b>PRODUCTOS QUE REALMENTE SALIERON EN LA VENTA</b></div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-default btn-sm" id="seleccionar-todos"><i class="fa fa-check-circle"></i> Seleccionar todos</button>
                        <button type="button" class="btn btn-default btn-sm" id="deseleccionar-todos"><i class="fa fa-times-circle"></i> Deseleccionar todos</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover nc-inv-table">
                        <thead>
                            <tr>
                                <th style="width:45px;">✓</th>
                                <th>Código / Producto facturado</th>
                                <th>Producto de inventario</th>
                                <th>Bodega</th>
                                <th>Serie</th>
                                <th>Lote</th>
                                <th>Disponible</th>
                                <th>A devolver</th>
                                <th>Precio venta</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="detalle-productos">
                            <tr><td colspan="10" class="text-center text-muted">Seleccione una factura.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <label>Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="4"></textarea>
                    </div>
                    <div class="col-md-5">
                        <div class="nc-inv-totales">
                            <div class="nc-inv-total-row"><span>Subtotal {{ $porcentajeIva }}%</span><input id="subtotal12" value="0.00" readonly></div>
                            <div class="nc-inv-total-row"><span>Subtotal 0%</span><input id="subtotal0" value="0.00" readonly></div>
                            <div class="nc-inv-total-row"><span>Descuento</span><input id="descuento" value="0.00" readonly></div>
                            <div class="nc-inv-total-row"><span>Impuesto</span><input id="impuesto" value="0.00" readonly></div>
                            <div class="nc-inv-total-row nc-inv-total-final"><span>Total nota</span><input id="total_nota" value="0.00" readonly></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
(function () {
    var lineas = [];
    var cargando = false;

    function moneda(valor) {
        return Number(valor || 0).toFixed(2);
    }

    function escapar(valor) {
        return $('<div>').text(valor == null ? '' : valor).html();
    }

    function aviso(tipo, titulo, mensaje) {
        if (window.Swal) {
            Swal.fire({ icon: tipo, title: titulo, html: mensaje });
        } else {
            alert(mensaje.replace(/<[^>]+>/g, ''));
        }
    }

    function cargarPuntos() {
        $('#punto_emision').html('<option value="">Cargando...</option>');
        $.ajax({
            type: 'post',
            url: '{{ route("caja.sucursal") }}',
            data: { id_sucur: $('#sucursal').val(), _token: $('input[name=_token]').val() },
            success: function (data) {
                var opciones = '<option value="">Seleccione...</option>';
                $.each(data, function (_, item) {
                    opciones += '<option value="' + item.id + '">' + escapar(item.codigo_sucursal + '-' + item.codigo_caja) + '</option>';
                });
                $('#punto_emision').html(opciones);
            },
            error: function () {
                $('#punto_emision').html('<option value="">Seleccione...</option>');
                aviso('error', 'Configuración', 'No se pudieron cargar los puntos de emisión.');
            }
        });
    }

    function fila(linea, indice) {
        return '<tr data-indice="' + indice + '">' +
            '<td class="text-center"><input type="hidden" name="items[' + indice + '][key]" value="' + escapar(linea.key) + '">' +
            '<input type="hidden" class="valor-seleccion" name="items[' + indice + '][seleccionado]" value="0">' +
            '<input type="checkbox" class="seleccionar-linea"></td>' +
            '<td><b>' + escapar(linea.codigo) + '</b><br>' + escapar(linea.nombre) + '</td>' +
            '<td>' + escapar(linea.producto_inventario) + '</td>' +
            '<td>' + escapar(linea.bodega) + '</td>' +
            '<td>' + escapar(linea.serie || '-') + '</td>' +
            '<td>' + escapar(linea.lote || '-') + '</td>' +
            '<td class="text-right">' + linea.cantidad_disponible + '</td>' +
            '<td><input type="number" class="form-control cantidad-devolver" name="items[' + indice + '][cantidad]" min="0.0001" step="0.0001" max="' + linea.cantidad_disponible + '" value="' + linea.cantidad_disponible + '" disabled></td>' +
            '<td class="text-right">' + moneda(linea.precio) + '</td>' +
            '<td class="text-right total-linea">0.00</td>' +
            '</tr>';
    }

    function cargarFactura(id) {
        lineas = [];
        $('#detalle-productos').html('<tr><td colspan="10" class="text-center"><i class="fa fa-spinner fa-spin"></i> Verificando factura y movimientos...</td></tr>');
        $.get('{{ url("contable/nota_credito_cliente/inventario/factura") }}/' + id)
            .done(function (data) {
                lineas = data.lineas || [];
                $('#cliente').val(data.factura.cliente);
                $('#total_factura').val(moneda(data.factura.total));
                $('#saldo_factura').val(moneda(data.factura.saldo));
                var html = '';
                $.each(lineas, function (i, linea) { html += fila(linea, i); });
                $('#detalle-productos').html(html || '<tr><td colspan="10" class="text-center text-muted">No hay productos disponibles para devolver.</td></tr>');
                recalcular();
            })
            .fail(function (xhr) {
                var mensaje = xhr.responseJSON && xhr.responseJSON.msj ? xhr.responseJSON.msj : 'No se pudo verificar la factura.';
                $('#detalle-productos').html('<tr><td colspan="10" class="text-center text-danger">' + escapar(mensaje) + '</td></tr>');
                aviso('error', 'Factura no disponible', escapar(mensaje));
            });
    }

    function recalcular() {
        var subtotal0 = 0, subtotal12 = 0, descuento = 0, impuesto = 0;
        $('#detalle-productos tr[data-indice]').each(function () {
            var tr = $(this), indice = Number(tr.data('indice')), linea = lineas[indice];
            var seleccionado = tr.find('.seleccionar-linea').prop('checked');
            var cantidad = Number(tr.find('.cantidad-devolver').val() || 0);
            var bruto = 0, desc = 0, tax = 0, total = 0;
            tr.find('.valor-seleccion').val(seleccionado ? 1 : 0);
            if (seleccionado && linea && cantidad > 0) {
                bruto = Number(linea.precio) * cantidad;
                desc = Math.min(bruto, Number(linea.descuento_unitario) * cantidad);
                tax = Math.max(0, bruto - desc) * Number(linea.iva_porcentaje);
                if (Number(linea.iva_porcentaje) > 0) subtotal12 += bruto; else subtotal0 += bruto;
                descuento += desc;
                impuesto += tax;
                total = bruto - desc + tax;
            }
            tr.find('.total-linea').text(moneda(total));
        });
        $('#subtotal0').val(moneda(subtotal0));
        $('#subtotal12').val(moneda(subtotal12));
        $('#descuento').val(moneda(descuento));
        $('#impuesto').val(moneda(impuesto));
        $('#total_nota').val(moneda(subtotal0 + subtotal12 - descuento + impuesto));
    }

    $(document).ready(function () {
        $('#factura').select2({
            placeholder: 'Busque por número, cliente o identificación',
            allowClear: true,
            ajax: {
                url: '{{ route("nota_credito_cliente.inventario.facturas") }}',
                dataType: 'json',
                delay: 300,
                data: function (params) { return { search: params.term || '' }; },
                processResults: function (data) { return { results: data }; }
            }
        });

        $('#sucursal').on('change', cargarPuntos);
        $('#factura').on('change', function () {
            if ($(this).val()) cargarFactura($(this).val());
        });
        $('#detalle-productos').on('change input', '.seleccionar-linea, .cantidad-devolver', function () {
            var tr = $(this).closest('tr');
            if ($(this).hasClass('seleccionar-linea')) {
                tr.find('.cantidad-devolver').prop('disabled', !$(this).prop('checked'));
            }
            recalcular();
        });
        $('#seleccionar-todos').on('click', function () {
            $('.seleccionar-linea').prop('checked', true);
            $('.cantidad-devolver').prop('disabled', false);
            recalcular();
        });
        $('#deseleccionar-todos').on('click', function () {
            $('.seleccionar-linea').prop('checked', false);
            $('.cantidad-devolver').prop('disabled', true);
            recalcular();
        });
        $('#nueva-nota').on('click', function () { window.location.reload(); });

        $('#form-nota-inventario').on('submit', function (event) {
            event.preventDefault();
            if (cargando) return;
            if (Number($('#total_nota').val()) <= 0) {
                aviso('warning', 'Falta selección', 'Seleccione los productos y cantidades que regresan al inventario.');
                return;
            }
            cargando = true;
            $('#guardar-nota').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
            $.ajax({
                type: 'post',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: 'json'
            }).done(function (data) {
                aviso('success', 'Proceso completado',
                    escapar(data.msj) + '<br><b>Nota:</b> ' + escapar(data.numero) +
                    '<br><b>Asiento comercial:</b> ' + data.id_asiento +
                    '<br><b>Asiento de costo:</b> ' + data.id_asiento_costo);
                $('#form-nota-inventario :input').prop('disabled', true);
            }).fail(function (xhr) {
                var mensaje = xhr.responseJSON && xhr.responseJSON.msj ? xhr.responseJSON.msj : 'No se pudo guardar la nota de crédito.';
                aviso('error', 'No se guardó', escapar(mensaje));
                cargando = false;
                $('#guardar-nota').prop('disabled', false).html('<i class="glyphicon glyphicon-floppy-disk"></i> Guardar');
            });
        });
    });
})();
</script>
@endsection
