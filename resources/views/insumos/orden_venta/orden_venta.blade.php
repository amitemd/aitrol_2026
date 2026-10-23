@extends('contable.balance_general.base')
@section('action-content')

@php
    $esEdicion = !empty($orden);
@endphp

<style>
    .oc-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
    }

    .oc-head {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .oc-title {
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }

    .oc-body {
        padding: 16px;
    }

    .oc-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1.2fr;
        gap: 12px;
    }

    .oc-table th {
        background: #f8fafc;
        font-weight: 800;
    }

    .oc-table td,
    .oc-table th {
        vertical-align: middle;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #d2d6de !important;
        border-radius: 0 !important;
        padding: 4px;
        box-shadow: none !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        padding-left: 0 !important;
    }

    .sum-box {
        display: flex;
        gap: 14px;
        justify-content: flex-end;
        margin-top: 12px;
    }

    .sum-item {
        min-width: 220px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f8fafc;
        padding: 10px 12px;
    }

    .sum-label {
        font-size: 12px;
        color: #64748b;
    }

    .sum-value {
        font-size: 18px;
        font-weight: 900;
    }

    .w-120 {
        width: 120px;
    }

    .nowrap {
        white-space: nowrap;
    }

    .select2-error .select2-selection--single {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 .2rem rgba(220, 53, 69, .15) !important;
    }

    .select2-error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 4px;
    }

    .oc-footer-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
    }
</style>

<div class="container-fluid">
    <div class="oc-card">
        <form id="formOrdenCompra" action="{{ $esEdicion ? route('ordenes_compra.update', $orden->id) : route('ordenes_compra.store') }}" method="POST" novalidate>
            
            <div class="oc-head">
                <h3 class="oc-title">
                    {{ $esEdicion ? 'EDITAR ORDEN DE COMPRA' : 'ORDEN DE COMPRA' }}
                </h3>

                <div style="display:flex; gap:8px;">
                    <a href="{{ route('ordenes_compra.create') }}" class="btn btn-default">
                        <i class="fa fa-plus"></i> Nuevo
                    </a>

                    @if($esEdicion)
                        <a href="{{ route('ordenes_compra.pdf', $orden->id) }}" target="_blank" class="btn btn-danger">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </a>
                    @endif

                    <button type="button" class="btn btn-primary" id="btnAddRow">
                        + Agregar producto
                    </button>
                </div>
            </div>

            <div class="oc-body">
                <div id="ajaxMessage"></div>

               <div class="oc-row">

                    @if($esEdicion)
                     <div>
                       <label>N° Orden</label>
                        <input type="text" class="form-control" value="{{ $orden->numero }}" readonly> </div>
                    @endif

                        <div>
                            <label>Fecha</label>
                            <input type="date"
                                class="form-control"
                                name="fecha"
                                id="fecha"
                                value="{{ old('fecha', $esEdicion ? $orden->fecha : date('Y-m-d')) }}"
                                required>
                        </div>

                        <!-- <div>
                            <label for="entrega_a">Entregar a</label>
                                <select class="form-control" name="entrega_a" id="entrega_a" required>
                                    <option value="">Seleccione...</option>

                                    <option value="TORRE 2 SUBSUELO"
                                    {{ old('entrega_a', $esEdicion ? $orden->entrega_a : '') == 'TORRE 2 SUBSUELO' ? 'selected' : '' }}>
                                            TORRE 2 SUBSUELO
                                    </option>

                                    <option value="TORRE 1 6TO PISO 618"
                                            {{ old('entrega_a', $esEdicion ? $orden->entrega_a : '') == 'TORRE 1 6TO PISO 618' ? 'selected' : '' }}>
                                            TORRE 1 6TO PISO 618
                                    </option>

                                </select>
                        </div> -->

                    <div class="form-group">
                            <label for="proveedor_id">Proveedor</label>
                            <select id="proveedor_id" name="proveedor_id" class="form-control select2_proveedor" required>
                                <option value=""></option>
                                @if($esEdicion && !empty($orden->proveedor_data))
                                    <option value="{{ $orden->proveedor_data->id }}" selected>
                                        {{ $orden->proveedor_data->nombrecomercial }}
                                    </option>
                                @endif
                            </select>
                    </div>
            </div>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered oc-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width:280px">CÓDIGO</th>
                                <th class="w-120">CANTIDAD EN UNIDADES</th>
                                <th>ORDEN DE COMPRA</th>
                                <th class="w-120">DIAMETER (FR)</th>
                                <th class="w-120">PRECIO UNITARIO</th>
                                <th class="w-120">TOTAL</th>
                                <th style="width:260px">OBSERVACIÓN</th>
                                <th class="nowrap">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="sum-box">
                    <div class="sum-item">
                        <div class="sum-label">TOTAL EN CANTIDADES</div>
                        <div class="sum-value" id="sumQty">0</div>
                        <input type="hidden"
                               name="total_cantidades"
                               id="inputSumQty"
                               value="{{ old('total_cantidades', $esEdicion ? $orden->total_cantidades : 0) }}">
                    </div>

                    <div class="sum-item">
                        <div class="sum-label">SUBTOTAL</div>
                        <div class="sum-value" id="sumSub">$0.00</div>
                        <input type="hidden"
                               name="total_subtotal"
                               id="inputSumSub"
                               value="{{ old('total_subtotal', $esEdicion ? $orden->subtotal : 0) }}">
                    </div>
                </div>

                <div class="oc-footer-actions">
                    <a href="{{ route('ordenes_compra.index') }}" class="btn btn-default">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-success" id="btnGuardar">
                        <i class="fa fa-save"></i> {{ $esEdicion ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<template id="rowTpl">
    <tr>
        <td>
            <select class="form-control product-select" required>
                <option value=""></option>
            </select>
            <input type="hidden" class="product-id" name="">
        </td>

        <td>
            <input type="number" min="0" step="1" class="form-control qty" name="" value="0">
        </td>

        <td>
            <input type="text" class="form-control desc" name="" readonly>
        </td>

        <td>
            <input type="text" class="form-control fr" name="" readonly>
        </td>

        <td>
            <input type="number" min="0" step="0.0001" class="form-control price" name="" value="0">
        </td>

        <td>
            <input type="text" class="form-control total" name="" value="$0.00" readonly>
        </td>

        <td>
            <input type="text" class="form-control obs" name="" placeholder="Observación...">
        </td>

        <td class="nowrap">
            <button type="button" class="btn btn-danger btnRemove">Eliminar</button>
        </td>
    </tr>
</template>

<script>
    let rowIndex = 0;
    let detalleInicial = {!! json_encode(old('items', $esEdicion ? $orden->detalle : [])) !!};

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        $('.select2_proveedor').select2({
            allowClear: true,
            width: '100%',
            minimumInputLength: 3,
            placeholder: 'Seleccione proveedor',
            language: {
                inputTooShort: function() {
                    return 'Introduzca al menos 3 caracteres';
                },
                noResults: function() {
                    return 'No se encontraron resultados';
                },
                searching: function() {
                    return 'Buscando...';
                }
            },
            ajax: {
                url: '{{ route("importaciones.proveedores") }}',
                dataType: 'json',
                delay: 250,
                cache: true,
                data: function(params) {
                    return { search: params.term || '' };
                },
                processResults: function(data) {
                    return { results: data };
                }
            }
        });

        $('#proveedor_id').on('change', function() {
            limpiarErrorSelect2($(this));
        });

        if (detalleInicial && detalleInicial.length > 0) {
            detalleInicial.forEach(function(item) {
                addRow(item);
            });
        } else {
            addRow();
        }

        recalcTotals();
    });

    function money(n) {
        n = Number(n || 0);
        return '$' + n.toFixed(2);
    }

    function toNum(v) {
        if (v === null || v === undefined || v === '') return 0;
        v = String(v).replace('$', '').replace(/,/g, '').trim();
        let n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }

    function recalcRow($tr) {
        const qty = toNum($tr.find('.qty').val());
        const price = toNum($tr.find('.price').val());
        const total = qty * price;

        $tr.find('.total').val(money(total));
    }

    function recalcTotals() {
        let sumQty = 0;
        let sumSub = 0;

        $('#itemsTable tbody tr').each(function() {
            const $tr = $(this);
            const productId = $tr.find('.product-id').val();

            if (productId) {
                sumQty += toNum($tr.find('.qty').val());
                sumSub += toNum($tr.find('.total').val());
            }
        });

        $('#sumQty').text(sumQty);
        $('#sumSub').text(money(sumSub));
        $('#inputSumQty').val(sumQty);
        $('#inputSumSub').val(sumSub);
    }

    function limpiarErrorSelect2($select) {
        $select.next('.select2').removeClass('select2-error');
        $select.closest('td, .form-group').find('.select2-error-message').remove();
    }

    function marcarErrorSelect2($select, mensaje) {
        limpiarErrorSelect2($select);
        $select.next('.select2').addClass('select2-error');
        $select.closest('td, .form-group').append('<div class="select2-error-message">' + mensaje + '</div>');
    }

    function initProductoSelect2($select) {
        $select.select2({
            placeholder: 'Escriba código...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route("orden_buscar.productos") }}',
                dataType: 'json',
                delay: 250,
                cache: true,
                data: function(params) {
                    return { search: params.term || '' };
                },
                processResults: function(data) {
                    return { results: data };
                }
            }
        });

        $select.on('select2:select', function(e) {
            const productId = e.params.data.id;
            const $tr = $(this).closest('tr');

            $tr.find('.product-id').val(productId);
            limpiarErrorSelect2($(this));

            let urlBase = '{{ route("importaciones.producto_detalle", ":id") }}';
            let finalUrl = urlBase.replace(':id', productId);

            $.get(finalUrl, function(p) {
                $tr.find('.desc').val(p.descripcion || '');
                $tr.find('.fr').val(p.diameter_fr || '');
                $tr.find('.qty').val(p.cantidad ?? 1);
                $tr.find('.price').val(p.precio ?? 0);

                recalcRow($tr);
                recalcTotals();
            });
        });

        $select.on('select2:clear', function() {
            const $tr = $(this).closest('tr');
            $tr.find('.product-id').val('');
            $tr.find('.desc').val('');
            $tr.find('.fr').val('');
            $tr.find('.qty').val(0);
            $tr.find('.price').val(0);
            $tr.find('.total').val('$0.00');
            recalcTotals();
        });

        $select.on('change', function() {
            if ($(this).val()) {
                limpiarErrorSelect2($(this));
            }
        });
    }

   function addRow(item = null) {
    const tpl = document.getElementById('rowTpl').content.cloneNode(true);
    const $tr = $(tpl).find('tr');

    $tr.find('.product-select').attr('name', `items[${rowIndex}][producto_select]`);
    $tr.find('.product-id').attr('name', `items[${rowIndex}][product_id]`);
    $tr.find('.qty').attr('name', `items[${rowIndex}][cantidad]`);
    $tr.find('.desc').attr('name', `items[${rowIndex}][descripcion]`);
    $tr.find('.fr').attr('name', `items[${rowIndex}][diameter_fr]`);
    $tr.find('.price').attr('name', `items[${rowIndex}][precio]`);
    $tr.find('.total').attr('name', `items[${rowIndex}][total]`);
    $tr.find('.obs').attr('name', `items[${rowIndex}][observacion]`);

    $('#itemsTable tbody').append($tr);
    initProductoSelect2($tr.find('.product-select'));

    if (item) {
        let productoId = item.producto ?? item.product_id ?? '';
        let productoTexto = item.producto_texto ?? item.codigo ?? item.product_id ?? productoId;

        if (productoId) {
            const option = new Option(productoTexto, productoId, true, true);
            $tr.find('.product-select').append(option).trigger('change');
        }

        $tr.find('.product-id').val(productoId);
        $tr.find('.qty').val(item.cantidad ?? 0);
        $tr.find('.desc').val(item.descripcion ?? '');
        $tr.find('.fr').val(item.diameter_fr ?? '');

        $tr.find('.price').val(item.precio_unitario ?? item.precio ?? 0);

        recalcRow($tr);

        $tr.find('.obs').val(item.observacion ?? '');
    }

    $tr.on('input', '.qty, .price', function() {
        recalcRow($tr);
        recalcTotals();
    });

    $tr.find('.btnRemove').on('click', function() {
        $tr.remove();
        recalcTotals();
    });

    rowIndex++;
    recalcTotals();
}

    function validarFormularioConSelect2() {
        $('#ajaxMessage').html('');
        limpiarErrorSelect2($('#proveedor_id'));

        $('.product-select').each(function() {
            limpiarErrorSelect2($(this));
        });

        if (!$('#fecha').val()) {
            document.getElementById('fecha').reportValidity();
            return false;
        }


        if (!$('#proveedor_id').val()) {
            marcarErrorSelect2($('#proveedor_id'), 'Seleccione un proveedor.');
            return false;
        }

        if ($('#itemsTable tbody tr').length === 0) {
            addRow();
            marcarErrorSelect2($('#itemsTable tbody tr:first').find('.product-select'), 'Seleccione un producto.');
            return false;
        }

        let filasValidas = 0;
        let primerProductoInvalido = null;
        let hayFilasVacias = false;

        $('#itemsTable tbody tr').each(function() {
            const $select = $(this).find('.product-select');
            const valor = $select.val();

            if (valor) {
                filasValidas++;
            } else {
                hayFilasVacias = true;
                marcarErrorSelect2($select, 'Seleccione un producto.');
                if (!primerProductoInvalido) {
                    primerProductoInvalido = $select;
                }
            }
        });

        if (filasValidas === 0 || hayFilasVacias) {
            if (primerProductoInvalido) {
                primerProductoInvalido.select2('open');
            }
            return false;
        }

        recalcTotals();
        return true;
    }

    $('#btnAddRow').on('click', function() {
        addRow();
    });

    $('#formOrdenCompra').on('submit', function(e) {
        e.preventDefault();

        if (!validarFormularioConSelect2()) {
            return false;
        }

        recalcTotals();

        const form = document.getElementById('formOrdenCompra');
        const formData = new FormData(form);

        $('#btnGuardar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Actualizando...');

        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                window.location.href = '{{ route("ordenes_compra.index") }}';
            },
            error: function(xhr) {
                $('#btnGuardar').prop('disabled', false).html('<i class="fa fa-save"></i> {{ $esEdicion ? "Actualizar" : "Guardar" }}');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let html = '<div class="alert alert-danger"><ul style="margin-bottom:0;">';

                    for (let campo in errors) {
                        html += '<li>' + errors[campo][0] + '</li>';
                    }

                    html += '</ul></div>';
                    $('#ajaxMessage').html(html);
                } else {
                    $('#ajaxMessage').html('<div class="alert alert-danger">Error al procesar la orden.</div>');
                    console.log(xhr.responseText);
                }
            }
        });
    });
</script>
@endsection