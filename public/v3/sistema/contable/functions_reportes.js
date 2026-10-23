var sumaRenta = 0;
var totalDebe = 0;

$(document).ready(function () {


    $("#bodega option:eq(1)").prop("selected", true).trigger('change');

    $(".js-producto")
        .select2({
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/buscarxProducto",
                delay: 300,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.nombreappe,
                                id: item.id,
                            };
                        }),
                    };
                },
            },
            language: {
                inputTooShort: function () {
                    return "Por favor ingrese 3 caracteres....";
                },
            },
        })



    $(".js-producto-nombre")
        .select2({
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/buscarxNombreProducto",
                delay: 300,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.nombreappe,
                                id: item.id,
                            };
                        }),
                    };
                },
            },
            language: {
                inputTooShort: function () {
                    return "Por favor ingrese 3 caracteres....";
                },
            },
        })

    $(".js-nombre-producto")
        .select2({
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/buscarxNombreProducto",
                delay: 300,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.nombreappe,
                                id: item.id_Producto,
                            };
                        }),
                    };
                },
            },
            language: {
                inputTooShort: function () {
                    return "Por favor ingrese 3 caracteres....";
                },
            },
        })



    $('.oculto').css('display', 'none');
    if (($('#submodulo_').val() == 'ventas' && $('#extra_').val() == 'ventasnetas') ||
        ($('#submodulo_').val() == 'proveedores' && $('#extra_').val() == 'notadecredito') ||
        ($('#submodulo_').val() == 'proveedores' && $('#extra_').val() == 'retencionesproveedores') ||
        ($('#submodulo_').val() == 'proveedores' && $('#extra_').val() == 'deudasvspagos') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'librodiario') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'mayorizacion') ||
        ($('#submodulo_').val() == 'clientes' && $('#extra_').val() == 'deudasvspagos') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'balancedecomprobacion') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'flujodeefectivo') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'balancederesultados') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'balancefinanciero') ||
        ($('#submodulo_').val() == 'financieros' && $('#extra_').val() == 'indicesfinancieros')) {
        $(".botonMenu").trigger("click");
    }
    var tabla = '';
    var tableNotasCreditoProveedor = '';
    var tableNotasDebitoClientes = '';
    var tableNotasDebitoProveedores = '';
    var tablaRetencionesProveedor = '';
    var tablaDeudavsPagosClientes = '';
    var tablaDeudasvsPagosProveedores = '';
    var tablaInformeAnticiposProveedores = '';
    var tableClientesReportesRetenciones = '';
    var tableNotasCreditoClientes = '';
    var tablaDeudasPendientes = '';
    var tablaDeudasPendientesClientes = '';
    var tableBalanceComprobacion = '';
    var tableBalanceResultados = '';
    var tableLibroDiario = '';
    var tableActivosFijosSucursal = '';
    var tableProveedoresEgresoMasivo = '';
    var tableLibroMayor = '';
    var tableAnticiposProveedor = '';
    var tableChequesGirados = '';
    var tableInformeTiempo = '';
    var tableInformeSaldos = '';
    var tableCarteraPagar = '';
    try {

        var table = $("#tablaKardex").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            iDisplayLength: 8,
            dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>',
                footer: true,
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/reporteKardex",
                type: 'post',
                data: {
                    idBodega: $("#bodega").val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [{
                className: "text-center",
                data: 'codigo'
            },
            {
                className: "text-center",
                data: 'producto'
            },

            {
                className: "text-center",
                data: "saldo",
            },
            ],

        });

        $('#tablaKardex tbody').on('click', 'tr', function () {
            var row = table.row(this);
            var data = row.data();
            $('#modal .modal-body').html(detalleHTML(data));
            $("#modal").modal("show");
        });


        $('#tablaKardex tbody').on('mouseenter', 'tr', function () {
            $(this).css('cursor', 'pointer');
        });
        function detalleHTML(data) {

            let obj = {
                ...data,
                idCodigo: $("#bodega").val()
            };

            var tabla_body_html = '';

            var json = ajax("v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleKardex", obj, 'json');
            if (json.responseText != '') {
                let body = JSON.parse(json.responseText);
                let dato = 0;
                let saldo_final = 0;
                console.log(body, "asdasdas");
                body.forEach((element, index) => {
                    let valor_anterior = 0;
                    let precio_total_egreso = 0;
                    let precio_unitario_egreso = 0;
                    let cantidad_egreso = 0;
                    let precio_total_ingreso = 0;
                    let precio_unitario_ingreso = 0;
                    let cantidad_ingreso = 0;
                    let precio_unitario = 0;
                    let documento = '';

                    if (element.tipo == 'E') {
                        precio_total_egreso = element.cantidad;
                        precio_unitario_egreso = element.valor_unitario;
                        cantidad_egreso = precio_total_egreso * precio_unitario_egreso;

                        saldo_final = saldo_final - cantidad_egreso;

                        dato -= precio_total_egreso;
                        valor_anterior = parseFloat(saldo_final / dato);
                    }
                    if (element.tipo == 'I') {
                        precio_total_ingreso = element.cantidad;
                        precio_unitario_ingreso = element.valor_unitario;
                        cantidad_ingreso = precio_total_ingreso * precio_unitario_ingreso;
                        dato += precio_total_ingreso;
                        saldo_final += cantidad_ingreso;
                        valor_anterior = parseFloat(saldo_final / dato);
                    }
                    if (element.id_documento_bodega == 7) {
                        documento =
                            `<a href="${baseUrl}/contable/planilla/costo/detalle/pdf/${element.id_hc_procedimiento}/${element.id_hc_procedimiento}" target="_blank" > ${element.descripcion} </a>`;
                    } else if (element.id_documento_bodega == 4) {
                        documento =

                            `<a href="${baseUrl}/transito/index/pdf/${element.id_cab_mov}" target="_blank"> ${element.descripcion} </a>`;
                    } else if (element.id_documento_bodega == 16 || element
                        .id_documento_bodega == 17 || element.id_documento_bodega == 25 ||
                        element.id_documento_bodega == 55) {
                        documento =
                            `<a href="${baseUrl}/insumos/egreso/ingreso/pdf/${element.id_cab_mov}" target="_blank"> ${element.descripcion} </a>`;
                    } else {
                        documento = `${element.descripcion} `;
                    }

                    tabla_body_html += `
            <tr>
                <td> ${element.bodega} </td>
                <td> ${element.codigo} </td>
                <td> ${documento}</td>
                <td> ${element.documento} </td>
                <td> ${element.fecha} </td>
                <td> ${element.fecha_vence} </td>
                <td> 
                    <a href=${baseUrl}/contable/contabilidad/libro/edit/${element.id_asiento_cabecera != null ? element.id_asiento_cabecera : ""}" target="_blank">${element.id_asiento_cabecera != null ? element.id_asiento_cabecera : ""}</a> 
                </td>
                <td> ${element.iva} </td>
                <td> ${element.lote} </td>
                <td> ${element.nombre} </td>
                <td> ${element.pedido} </td>
                <td> ${element.producto} </td>
                <td> ${element.serie} </td>
                <td> ${element.subtotal} </td>
                <td style="background-color: #C8EFD4"> ${precio_total_ingreso} </td>
                <td style="background-color: #C8EFD4"> ${precio_unitario_ingreso} </td>
                <td style="background-color: #C8EFD4"> ${cantidad_ingreso.toFixed(4)} </td>
                <td style="background-color: #FBCFD0"> ${precio_total_egreso} </td>
                <td style="background-color: #FBCFD0"> ${precio_unitario_egreso}</td>
                <td style="background-color: #FBCFD0"> ${cantidad_egreso.toFixed(4)} </td>
                <td style="background-color: #C8EBFA"> ${dato} </td>
                <td style="background-color: #C8EBFA"> ${valor_anterior.toFixed(4)} </td>
                <td style="background-color: #C8EBFA"> ${saldo_final.toFixed(4)} </td>
            </tr>
        `;
                });


            }

            const titulosHtml = `
                <thead>
                    <tr class="well-dark">
                        <th>Bodega</th>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th>Fecha Vence</th>
                        <th>ID asiento cabecera</th>
                        <th>Iva</th>
                        <th>Lote</th>
                        <th>Nombre</th>
                        <th>Pedido</th>
                        <th>Producto</th>
                        <th>Serie</th>
                        <th>Subtotal</th>
                        <th>CANTIDAD</th>
                        <th>PRECIO UNITARIO</th>
                        <th>PRECIO TOTAL</th>
                        <th>CANTIDAD</th>
                        <th>PRECIO UNITARIO</th>
                        <th>PRECIO TOTAL</th>
                        <th>CANTIDAD</th>
                        <th>COSTO PROMEDIO</th>
                        <th>PRECIO TOTAL</th>
                    </tr>
                </thead>
            `;


            const tablaCompletaHtml = `
            <div class="tabla-contenedor-scroll">
                <table class="table table-bordered table-striped table-hover">
                    ${titulosHtml}
                    <tbody>
                        ${tabla_body_html}
                    </tbody>
                </table>
            </div>
        `;
            return tablaCompletaHtml;
        }


        $("#tableInventarioSerie").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            iDisplayLength: 8,
            dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>',
                footer: true,
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/inventarioReporteSerie",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [{
                className: "text-center",
                data: 'id'
            },
            {
                className: "text-center",
                data: 'codigo_producto'
            },
            {
                className: "text-center",
                data: 'nombre_producto'
            },

            {
                className: "text-right",
                data: 'nr_pedido'
            },
            {
                className: "text-right",
                data: 'bodega'
            },
            {
                className: "text-right",
                data: 'nombre_proveedor'
            },
            {
                className: "text-right",
                data: 'lote'
            },
            {
                className: "text-right",
                data: 'serie'
            },
            {
                className: "text-right",
                data: 'fecha_vence'
            },
            {
                className: "text-right",
                data: 'existencia'
            },
            {
                className: "text-right",
                data: 'existencia_min'
            },
            {
                className: "text-right",
                data: 'existencia_max'
            },
            {
                className: "text-right",
                data: 'documento'
            },
            {
                className: "text-right",
                data: 'costo_promedio'
            },

            ],

        });


        $("#tableInventario").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            iDisplayLength: 8,
            dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>',
                footer: true,
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/inventarioReporte",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [{
                className: "text-center",
                data: 'id'
            },
            {
                className: "text-center",
                data: 'codigo'
            },

            {
                className: "text-center",
                data: 'nombre'
            },
            {
                className: "text-center",
                data: 'nombreBodega'
            },
            {
                className: "text-right",
                data: 'existencia'
            },
            {
                className: "text-right",
                data: 'existencia_min'
            },
            {
                className: "text-right",
                data: 'costo_promedio_formateado'
            },
            {
                className: "text-right",
                data: 'costo_formateado'
            },
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                var existencia = api.column(4, { page: 'current' }).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                var stockminimo = api.column(5, { page: 'current' }).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                var valorUnitario = api.column(6, { page: 'current' }).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                var valorTotal = api.column(7, { page: 'current' }).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                $(api.column(4).footer()).html(existencia.toFixed(2));
                $(api.column(5).footer()).html(stockminimo.toFixed(2));
                $(api.column(6).footer()).html(valorUnitario.toFixed(2));
                $(api.column(7).footer()).html(valorTotal.toFixed(2));
            }
        });




        $('.toggle-details').on('click', function () {
            var $icon = $(this).find('i');
            var $detailsRow = $(this).closest('tr').next('.details-row');
            if ($detailsRow.is(':visible')) {
                $detailsRow.hide();
                $icon.removeClass('fa-minus').addClass('fa-plus');
            } else {
                $detailsRow.show();
                $icon.removeClass('fa-plus').addClass('fa-minus');
            }
        });
        if ($('#plan_desde').val() !== '' && $('#extra_').val() == 'libromayor') {
            $('#cta_desde').html('<option value="' + $('#plan_desde').val() + '">' + $('#plan_desde').val() + '</option>').trigger('change');
            buscarCuentasLibroMayor();
        }
        $("#tableInsumosCaducados").DataTable({
            language: {
                // tus traducciones
            },
            responsive: true,
            lengthChange: false,
            autoWidth: true,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [
                { extend: 'copy', text: '<i class="fa fa-copy" title="Copy"></i>' },
                { extend: 'excel', title: tituloExcel, text: '<i class="fas fa-file-excel" title="Excel"></i>' },
                { extend: 'pdf', title: tituloExcel, text: '<i class="fas fa-file-pdf" title="PDF"></i>' },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print" title="Print"></i>',
                    customize: function (win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }
            ],
            ajax: {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
                type: 'POST',
                headers: { "X-CSRF-TOKEN": $("#token").val() },
                error: function (error) {
                    console.log(error);
                }
            },
            columns: [
                {
                    data: "codigo",
                    className: "text-center",
                },
                {
                    data: "nombre",
                    className: "text-center",

                },
                {
                    data: "descripcion",
                    className: "text-center",

                },
                {
                    data: "serie",
                    className: "text-center",

                },
                {
                    data: "pedido",
                    className: "text-center",

                },
                {
                    data: "bodega",
                    className: "text-center",

                },
                {
                    data: "proveedor",
                    className: "text-center",

                },
                {
                    data: "marca",
                    className: "text-center",

                },
                {
                    data: "cantidad",
                    className: "text-center",

                },
                {
                    data: "fecha_vence",
                    className: "text-center",

                },
                {
                    data: "estado",
                    className: "text-center",

                }
            ],
            createdRow: function (row, data, dataIndex) {
                let $td = $('td', row).eq(10); // columna "estado"

                if (data.estado === "Producto Caducado") {
                    $td.css("background-color", "#e81a1aff"); // rojo
                    $td.css("color", "#000000"); // texto blanco
                    $td.css("font-weight", "bold");
                } else {
                    $td.css("background-color", "#f1de11ff"); // amarillo
                    $td.css("color", "#000000"); // texto negro
                    $td.css("font-weight", "bold");
                }
            }



        });
        $("#tablePedidos").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
            ajax: {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
                type: 'post',
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [
                {
                    data: "id",
                },
                {
                    data: "factura",
                },
                {
                    data: "numero_documento",
                },
                {
                    data: "nombrecomercial",
                },
                {
                    data: "fecha",
                },
                {
                    data: "nombre",
                },
                {
                    data: "cantidad",
                },
                {
                    data: "valor_unitario",
                },
                {
                    data: "subtotal",
                },
                {
                    data: "iva",
                },
                {
                    data: "descuento",
                },
                {
                    data: "total",
                },],
        });
        $("#tableStockMinimo").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
            ajax: {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
                type: 'post',
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [
                {
                    data: "codigo",
                },
                {
                    data: "nombre",
                },
                {
                    data: "descripcion",

                },
                {
                    data: "stock_minimo",
                    className: "text-center",
                },
                {
                    data: "existencia",
                    className: "text-center",
                },
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.existencia <= data.stock_minimo) {
                    $('td', row).eq(4).css({
                        'background-color': 'red',
                        'color': 'black',
                        'text-align': 'center'         // texto centrado
                    });
                }
            }
        });
        tableCarteraPagar = $("#tableCarteraPagar").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            searching: false,
            info: false,
            "responsive": true,
        });
        tableInformeSaldos = $("#tableInformeSaldos").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            searching: false,
            info: false,
            "responsive": true,
        });
        tableChequesGirados = $("#tableChequesGirados").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            iDisplayLength: 100,
            dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>',
                footer: true,
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/chequesgiradosjs",
                type: 'post',
                data: {
                    d: $("#desde").val(),
                    h: $("#hasta").val(),
                    id_proveedor: $("#id_proveedor").val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [{
                className: "text-center",
                data: 'fecha_comprobante'
            },
            {
                className: "text-center",
                data: 'fecha_cheque'
            },
            {
                className: "text-center",
                data: 'id_asiento_cabecera',
                render: function (data, type, row) {
                    return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\');">' + row.id_asiento_cabecera + '</a>';
                }
            },
            {
                className: "text-center",
                data: "cheque",
            },
            {
                data: 'razonsocial'
            },
            {
                data: 'banco'
            },
            {
                className: "text-right",
                data: 'valor'
            },
            {
                className: "text-center",
                data: 'fecha_comprobante',
                render: function (d, t, r) {
                    var estado = '<div class="bg-success color-palette"><span>Activo</span></div>';
                    if (r.estado_egreso == 0) {
                        estado = '<div class="bg-danger color-palette"><span>Anulado</span></div>';
                    }
                    $('#totalGirado').html(r.total_global);
                    return estado;
                }
            }
            ],
        });
        tableProveedoresEgresoMasivo = $("#tableProveedoresEgresoMasivo").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/egresomasivojs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    //window.open('v3/admin', '_self');
                    console.log(error);
                }
            },
            columns: [{
                data: 'id'
            },
            {
                data: 'descripcion'
            },
            {
                data: 'secuencia'
            },
            {
                className: "text-center",
                data: "id_asiento_cabecera",
                render: function (data, type, row) {
                    return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\')">' + row.id_asiento_cabecera + '</a>';
                }
            },
            {
                data: 'fecha_cheque'
            },
            {
                data: 'no_cheque'
            },
            {
                data: 'girado_a'
            },
            {
                data: 'valor_pago'
            },
            {
                data: 'nombreCrea'
            },
            {
                data: 'estado',
                render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                    return estado;
                }
            },
            ],
        });
        tabla = $("#tableVentasVentasNetas").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableAnticiposProveedor = $("#tableAnticiposProveedor").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: false,
            autoWidth: true,
            searching: false
        });
        tableLibroMayor = $("#tableLibroMayor").DataTable({
            language: {
                decimal: "",
                emptyTable: $("#emptyTable").val(),
                info: $("#mostrando").val() + " _START_ " + $("#a").val() + " _END_ " + $("#de").val() + " _TOTAL_ " + $("#entradas").val(),
                infoEmpty: $("#mostrando").val() + " 0 " + $("#a").val() + " 0 " + $("#de").val() + " 0 " + $("#entradas").val(),
                infoFiltered: "(" + $("#filtrado").val() + " " + $("#de").val() + " _MAX_ total " + $("#entradas").val() + ")",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: $("#mostrar").val() + " _MENU_ " + $("#entradas").val(),
                loadingRecords: $("#loadingRecords").val() + "...",
                processing: $("#processing").val() + "...",
                search: $("#search").val(),
                zeroRecords: $("#zeroRecords").val(),
                paginate: {
                    first: $("#first").val(),
                    last: $("#last").val(),
                    next: $("#next").val(),
                    previous: $("#previous").val(),
                },
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableNotasCreditoProveedor = $("#tableNotasCreditoProveedor").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableNotasDebitoClientes = $("#tableNotasDebitoClientes").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableNotasDebitoProveedores = $("#tableNotasDebitoProveedores").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaRetencionesProveedor = $("#tablaRetencionesProveedor").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaDeudavsPagosClientes = $("#tablaDeudavsPagosClientes").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaDeudasvsPagosProveedores = $("#tablaDeudasvsPagosProveedores").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaInformeAnticiposProveedores = $("#tablaInformeAnticiposProveedores").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableClientesReportesRetenciones = $("#tableClientesReportesRetenciones").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableNotasCreditoClientes = $("#tableNotasCreditoClientes").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaDeudasPendientesCliente = $("#tablaDeudasPendientesCliente").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: "excel",
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
                action: function (e, dt, button, config) {
                    $.ajax({
                        url: 'v3/' + $('#controlador').val() + '/excelDeudasClientes',
                        data: {
                            d: $("#desde").val(),
                            h: $("#hasta").val(),
                            id_cliente: $("#id_cliente").val(),
                            tipo: $("#tipo").val(),
                        },
                        method: 'GET',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function (data) {
                            var a = document.createElement('a');
                            var url = window.URL.createObjectURL(data);
                            a.href = url;
                            a.download = 'Deudas pendientes clientes.xlsx';
                            document.body.append(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            a.remove();
                        },
                        error: function () {
                            alert('Error al obtener los datos');
                        }
                    });
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaDeudasPendientes = $("#tablaDeudasPendientes").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: "excel",
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
                action: function (e, dt, button, config) {
                    $.ajax({
                        url: 'v3/' + $('#controlador').val() + '/excelDeudas',
                        data: {
                            d: $("#desde").val(),
                            h: $("#hasta").val(),
                            id_proveedor: $("#id_proveedor").val(),
                        },
                        method: 'GET',
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function (data) {
                            var a = document.createElement('a');
                            var url = window.URL.createObjectURL(data);
                            a.href = url;
                            a.download = 'Deudas Pendientes.xlsx';
                            document.body.append(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            a.remove();
                        },
                        error: function () {
                            alert('Error al obtener los datos');
                        }
                    });
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableLibroDiario = $("#tableLibroDiario").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleLibroDiario",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                },
            },
            columns: [{
                data: 'id',
                render: function (data, type, row) {
                    return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id + '\');">' + row.id + '</a>';
                }
            }, {
                className: 'text-center',
                data: 'fecha_asiento',
                render: function (data, type, row) {
                    return getfecha(row.fecha_asiento);
                }
            }, {
                className: 'text-center',
                data: 'plan',
            }, {
                data: 'nombre_cuenta'
            }, {
                className: 'text-right',
                data: 'debe',
                render: function (data, type, row) {
                    return row.debe;
                }
            }, {
                className: 'text-right',
                data: 'haber',
                render: function (data, type, row) {
                    $('#totalDebe').html(row.total_debe);
                    $('#totalHaber').html(row.total_haber);
                    return row.haber;
                }
            }, {
                data: 'observacion'
            }, {
                className: 'text-center',
                data: 'usuariocrea'
            }, {
                className: 'text-center',
                data: 'created_at'
            }, {
                className: 'text-center',
                data: 'usuariomod',
            }, {
                className: 'text-center',
                data: 'updated_at'
            }, {
                className: 'text-center',
                data: 'cuadrado'
            }, {
                className: 'text-center',
                data: 'aparece_sri',
                render: function (data, type, row) {
                    console.log(row.aparece_sri);
                    return row.aparece_sri !== 1 ? "NO" : "SI";
                }
            }],
        });
        tableBalanceComprobacion = $("#tableBalanceComprobacion").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            searching: false,
            info: false,
            paging: false,
            ordering: false,

        });
        tableBalanceResultados = $("#tableBalanceResultados").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableActivosFijosSucursal = $("#tableActivosFijosSucursal").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            pageLength: 100,
            order: [[3, "desc"]],
            dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
            ajax: {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/activosFijosSucursaljs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [{
                data: 'id'
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    return getfecha(row.fecha_compra, null, '-');
                }
            }, {
                data: 'codigo'
            }, {
                data: 'nombre'
            }, {
                data: 'numero'
            }, {
                data: 'color'
            }, {
                data: 'modelo'
            }, {
                data: 'serie'
            }, {
                className: 'text-right',
                data: 'costo'
            }, {
                className: 'text-center',
                data: 'sucursal'
            }]
        });
        tableInformeTiempo = $('#tableInformeTiempo').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            info: true,
            iDisplayLength: 100,
            order: [[0, 'desc']],
            dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
            buttons: [
                {
                    extend: 'excel',
                    title: tituloExcel,
                    text: '<i class="fas fa-file-excel" title="Excel"></i>',
                    footer: true,
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print" title="Print"></i>',
                    customize: function (win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }],
            "ajax": {
                url: "v3/" + $("#controlador").val() + "/" + $('#submodulo_').val() + "/InformeTiempojs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
            },
            columns: [
                {
                    className: 'text-right',
                    data: 'id_compra'
                }, {
                    className: 'text-center',
                    data: 'fecha'
                }, {
                    data: 'autorizacion',
                }, {
                    className: 'text-right',
                    data: 'comprobante'
                }, {
                    className: 'text-right',
                    data: 'id_proveedor'
                }, {
                    className: 'text-right',
                    data: 'nombre_proveedor'
                },
                {
                    className: 'text-right',
                    data: 'concepto'
                },
                {
                    className: 'text-right',
                    data: 'valor'
                }
            ],
        });

    } catch (error) { console.log(error); }
    $('#tipo').change(function () {
        if ($('#tipo').val() != '' && $('#tipo').val() == 4) {
            $('#divOculto').show();
        } else {
            $('#divOculto').hide();
        }
    });
    $('#id_cliente').select2({
        allowClear: true,
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/utilidades/clienteCombo",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            }, error(error) {

            },
            cache: true,
        }
    });
    $('#id_vendedor').select2({
        allowClear: true,
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/utilidades/vendedorCombo",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            }, error(error) {

            },
            cache: true,
        }
    });
    $('#estadoO').select2({
        allowClear: true,
        placeholder: "--Seleccione--",
    });
    $('#estado_').select2({
        allowClear: true,
        placeholder: "--Seleccione--",
    });
    $('#id_proveedor').select2({
        allowClear: true,
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/utilidades/proveedorCombo",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            }, error(error) {

            },
            cache: true,
        }
    });
    $("#cta_desde").select2({
        width: "100%",
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/proveedores/buscarCuenta",
            type: "get",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (response) {
                return {
                    results: $.map(response, function (categoria) {
                        return { id: categoria.plan, text: categoria.text };
                    }),
                };
            },
            error(error) {
                console.log(error);
            },
            cache: true,
        },
    });
    $("#cta_hasta").select2({
        width: "100%",
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/proveedores/buscarCuenta",
            type: "get",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (response) {
                return {
                    results: $.map(response, function (categoria) {
                        return { id: categoria.plan, text: categoria.text };
                    }),
                };
            },
            error(error) {
                console.log(error);
            },
            cache: true,
        },
    });
});
function buscarInformeSaldo() {
    let desde = $("#desde").val();
    let hasta = $("#hasta").val();
    let proveedor = $("#id_proveedor").val();
    $('#tableInformeSaldos').dataTable().fnClearTable();
    $('#tableInformeSaldos').dataTable().fnDestroy();
    $('#tableInformeSaldos').DataTable({
        language: {
            decimal: "",
            emptyTable: $("#emptyTable").val(),
            info: $("#mostrando").val() + " _START_ " + $("#a").val() + " _END_ " + $("#de").val() + " _TOTAL_ " + $("#entradas").val(),
            infoEmpty: $("#mostrando").val() + " 0 " + $("#a").val() + " 0 " + $("#de").val() + " 0 " + $("#entradas").val(),
            infoFiltered: "(" + $("#filtrado").val() + " " + $("#de").val() + " _MAX_ total " + $("#entradas").val() + ")",
            infoPostFix: "",
            thousands: ",",
            lengthMenu: $("#mostrar").val() + " _MENU_ " + $("#entradas").val(),
            loadingRecords: $("#loadingRecords").val() + "...",
            processing: $("#processing").val() + "...",
            search: $("#search").val(),
            zeroRecords: $("#zeroRecords").val(),
            paginate: {
                first: $("#first").val(),
                last: $("#last").val(),
                next: $("#next").val(),
                previous: $("#previous").val(),
            },
        },
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        info: true,
        iDisplayLength: 100,
        order: [[0, 'desc']],
        dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
        buttons: [
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        "ajax": {
            url: "v3/" + $("#controlador").val() + "/" + $('#submodulo_').val() + "/InformeSaldosjs",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            data: {
                desde: desde,
                hasta: hasta,
                proveedor: proveedor
            },
        },
        columns: [
            {
                className: 'text-center',
                data: 'proveedor_id'
            }, {
                data: 'razonsocial',
            }, {
                className: 'text-right',
                data: 'deuda'
            }, {
                className: 'text-right',
                data: 'anticipos'
            }, {
                className: 'text-right',
                data: 'proveedor_id',
                render: function (d, t, r) {
                    var saldo = parseFloat(r.deuda - r.anticipos).toFixed(2);
                    return saldo;
                }
            }
        ],
    });
    /* let desde = $("#desde").val();
     let hasta = $("#hasta").val();
     let proveedor = $("#id_proveedor").val();
     $.ajax({
         type: 'post',
         headers: {
             'X-CSRF-TOKEN': $('#token').val(),
         },
         url: "v3/" + $("#controlador").val() + "/" + $('#submodulo_').val() + "/InformeSaldosjs",
         data: {
             desde: desde,
             hasta: hasta,
             proveedor: proveedor
         },
         datatype: "json",
         success: function (data) {
             console.log(data.html);
             $("#contenidoHtml").html(data.html);
             $("#modalTotal").modal("show");
         },
         error: function (data) { },
    });*/
}
function buscarCarteraPagar() {
    $('#frm_cartera').submit();
    // $('#tableCarteraPagar').DataTable({
    //     destroy: true, 
    //     language: {
    //         decimal: "",
    //         emptyTable: $("#emptyTable").val(),
    //         info: $("#mostrando").val() + " _START_ " + $("#a").val() + " _END_ " + $("#de").val() + " _TOTAL_ " + $("#entradas").val(),
    //         infoEmpty: $("#mostrando").val() + " 0 " + $("#a").val() + " 0 " + $("#de").val() + " 0 " + $("#entradas").val(),
    //         infoFiltered: "(" + $("#filtrado").val() + " " + $("#de").val() + " _MAX_ total " + $("#entradas").val() + ")",
    //         infoPostFix: "",
    //         thousands: ",",
    //         lengthMenu: $("#mostrar").val() + " _MENU_ " + $("#entradas").val(),
    //         loadingRecords: $("#loadingRecords").val() + "...",
    //         processing: $("#processing").val() + "...",
    //         search: $("#search").val(),
    //         zeroRecords: $("#zeroRecords").val(),
    //         paginate: {
    //             first: $("#first").val(),
    //             last: $("#last").val(),
    //             next: $("#next").val(),
    //             previous: $("#previous").val(),
    //         },
    //     },
    //     responsive: true,
    //     lengthChange: true,
    //     autoWidth: true,
    //     info: true,
    //     iDisplayLength: 100,
    //     order: [[0, 'desc']],
    //     dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
    //     buttons: [
    //         {
    //             extend: 'excel',
    //             title: tituloExcel,
    //             text: '<i class="fas fa-file-excel" title="Excel"></i>',
    //             footer: true,
    //         },
    //         {
    //             extend: 'print',
    //             text: '<i class="fa fa-print" title="Print"></i>',
    //             customize: function (win) {
    //                 $(win.document.body).addClass('white-bg');
    //                 $(win.document.body).css('font-size', '10px');
    //                 $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
    //             }
    //         }],

    // });
}
function excelInformeSaldo() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        window.open('v3/' + $('#controlador').val() + '/excelInformeSaldo?d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&proveedor=' + $('#id_proveedor').val(), '_self');
    }
}
function excelCarteraPagar() {
    if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        window.open('v3/' + $('#controlador').val() + '/excelCarteraPagar?d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&proveedor=' + $('#id_proveedor').val(), '_self');
    }
}
function buscarBalanceSinCierre() {
    $('#c').val(1);
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    var data = {
        d: $('#desde').val(),
        h: $('#hasta').val(),
        c: 1
    };
    var html = ajax("v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleBalanceResultados", data, 'html');
    if (html.responseText != '') {
        html = html.responseText.split('|');
        $('#bodyIngresos').html(html[0]);
        $('#bodyCostos').html(html[1]);
        $('#bodyGastos').html(html[2]);
        $('#utilidadBruta').html(html[3]);
        $('#participacionTrabajadores').html(html[4]);
        $('#utilidadContable').html(html[5]);
        $('#utilidadGravable').html(html[6]);
        $('#impuestoGenerado').html(html[7]);
        $('#utilidadPerdida').html(html[8]);
        $('#desde_').html('Desde: ' + $('#desde').val());
        $('#hasta_').html('Hasta: ' + $('#hasta').val());
        $('.oculto').css('display', 'block');
    }
}
function buscarCuentasLibroMayor() {
    if ($('#cta_desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la la cuenta principal...');
    } else if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicial...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#v').html(0);
        $('#d').html(0);
        $('#vn').html(0);
        $('#nc').html(0);
        $('#vnf').html(0);
        $('#tableLibroMayor').dataTable().fnClearTable();
        $('#tableLibroMayor').dataTable().fnDestroy();
        $('#tableLibroMayor').DataTable({
            language: {
                decimal: "",
                emptyTable: $("#emptyTable").val(),
                info: $("#mostrando").val() + " _START_ " + $("#a").val() + " _END_ " + $("#de").val() + " _TOTAL_ " + $("#entradas").val(),
                infoEmpty: $("#mostrando").val() + " 0 " + $("#a").val() + " 0 " + $("#de").val() + " 0 " + $("#entradas").val(),
                infoFiltered: "(" + $("#filtrado").val() + " " + $("#de").val() + " _MAX_ total " + $("#entradas").val() + ")",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: $("#mostrar").val() + " _MENU_ " + $("#entradas").val(),
                loadingRecords: $("#loadingRecords").val() + "...",
                processing: $("#processing").val() + "...",
                search: $("#search").val(),
                zeroRecords: $("#zeroRecords").val(),
                paginate: {
                    first: $("#first").val(),
                    last: $("#last").val(),
                    next: $("#next").val(),
                    previous: $("#previous").val(),
                },
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            info: true,
            iDisplayLength: 100,
            dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
            buttons: [
                {
                    extend: 'excel',
                    title: tituloExcel,
                    text: '<i class="fas fa-file-excel" title="Excel"></i>',
                    footer: true,
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print" title="Print"></i>',
                    customize: function (win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');
                        $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                    }
                }],
            "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/libroMayorjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    cuentadesde: $('#cta_desde').val(),
                    cuentahasta: $('#cta_hasta').val(),
                },
            },
            columns: [
                {
                    className: 'text-center',
                    data: 'fecha_asiento',
                    render: function (data, type, row) {
                        return getfecha(row.fecha_asiento, null, '-');
                    }
                }, {
                    className: 'text-center',
                    data: 'id_asiento_cabecera'
                }, {
                    className: 'text-center',
                    data: 'fecha_asiento',
                    render: function (data, type, row) {
                        let fechaCadena = row.fecha_asiento;
                        let fecha = new Date(fechaCadena);
                        return fecha.getFullYear();
                    }
                }, {
                    className: 'text-center',
                    data: 'fecha_asiento',
                    render: function (data, type, row) {
                        let fechaCadena = row.fecha_asiento;
                        let fecha = new Date(fechaCadena);
                        const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                        return meses[fecha.getMonth()];
                    }
                }, {
                    data: 'informacion'
                }, {
                    className: 'text-center',
                    data: 'plan',
                }, {
                    data: 'nombre'
                }, {
                    data: 'observacion'
                }, {
                    data: 'identificacion'
                }, {
                    data: 'socio'
                }, {
                    className: 'text-right',
                    data: 'debe'
                }, {
                    className: 'text-right',
                    data: 'haber'
                }, {
                    className: 'text-right',
                    data: 'saldo',
                    render: function (data, type, row) {
                        $('#totalDebe').html(row.total_debe);
                        $('#totalHaber').html(row.total_haber);
                        return row.saldo;
                    }
                }
            ],
        });
    }
}
function verIndices(tipo) {
    if ($('#anio').val() == '') {
        return mensajes('warning', 'Seleccione el periodo a consultar...');
    }
    var data = {
        a: $('#anio').val(),
        t: tipo
    };
    var html = ajax("v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleRatiosFinanciero", data, 'html');
    if (html.responseText != '') {
        html = html.responseText;
        if (tipo == 'FI') {
            $('#estadofinanciero').html(html);
        } else if (tipo == 'RE') {
            $('#estadoresultado').html(html);
        } else {
            $('#ratios').html(html);
        }
    }
}
function buscarBalanceFinanciero() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    var data = {
        d: $('#desde').val(),
        h: $('#hasta').val(),
    };
    var html = ajax("v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleBalanceFinanciero", data, 'html');
    if (html.responseText != '') {
        html = html.responseText.split('|');
        $('.activos').html(html[0]);
        $('.pasivos').html(html[1]);
        $('.patrimonio').html(html[2]);
        $('#activos').html(html[3]);
        $('#pasivos').html(html[4]);
        $('#patrimonio').html(html[5]);
        $('#diferencia').html(html[6]);
        $('#desde_').html('Desde: ' + $('#desde').val());
        $('#hasta_').html('Hasta: ' + $('#hasta').val());
    }
}
function buscarBalanceResultados() {
    $('#c').val(0);
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    var data = {
        d: $('#desde').val(),
        h: $('#hasta').val(),
        c: 0
    };
    var html = ajax("v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleBalanceResultados", data, 'html');
    if (html.responseText != '') {
        html = html.responseText.split('|');
        $('#bodyIngresos').html(html[0]);
        $('#bodyCostos').html(html[1]);
        $('#bodyGastos').html(html[2]);
        $('#utilidadBruta').html(html[3]);
        $('#participacionTrabajadores').html(html[4]);
        $('#utilidadContable').html(html[5]);
        $('#utilidadGravable').html(html[6]);
        $('#impuestoGenerado').html(html[7]);
        $('#utilidadPerdida').html(html[8]);
        $('#desde_').html('Desde: ' + $('#desde').val());
        $('#hasta_').html('Hasta: ' + $('#hasta').val());
        $('.oculto').css('display', 'block');
    }
}
function buscarBalanceComprobacion() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    $('#tableBalanceComprobacion').dataTable().fnClearTable();
    $('#tableBalanceComprobacion').dataTable().fnDestroy();
    var data = {
        d: $('#desde').val(),
        h: $('#hasta').val(),
    };
    var html = ajax("v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleBalanceComprobacion", data, 'html');
    if (html.responseText != '') {

        html = html.responseText.split('|');
        $('#cuerpoBalanceComprobacion').html(html[0]);
        $('#totalDebe').html(parseFloat(html[1]).toFixed(2));
        $('#totalHaber').html(parseFloat(html[2]).toFixed(2));
        $('#totalDeudor').html(parseFloat(html[3]).toFixed(2));
        $('#totalAcreedor').html(parseFloat(html[4]).toFixed(2));
        $('#tableBalanceComprobacion').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: false,
            autoWidth: true,
            info: true,
            iDisplayLength: 1000,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            order: [[0, "asc"]],
            columnDefs: [
                { "orderable": false, "targets": [0, 1, 2, 3, 4, 5] },
            ],
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: 'Balance de comprobaasdasdción | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                action: function (e, dt, button, config) {


                    let arrayDatos = [];
                    let arrayDatosFooter = [];
                    $("#tableBalanceComprobacion #cuerpoBalanceComprobacion tr").each(function (i, obj) {
                        let elementos = $(obj).find("td");
                        arrayDatos.push({
                            'plan': $(elementos[0]).text(),
                            'descripcion': $(elementos[1]).text(),
                            'debe': $(elementos[2]).text(),
                            'haber': $(elementos[3]).text(),
                            'deudor': $(elementos[4]).text(),
                            'acreedor': $(elementos[5]).text(),
                        });
                    });

                    $("#tableBalanceComprobacion tfoot tr").each(function (i, obj) {
                        let elementos = $(obj).find("th");
                        arrayDatosFooter.push({
                            'nombre': $(elementos[1]).text(),
                            'total1': $(elementos[2]).text(),
                            'total2': $(elementos[3]).text(),
                            'total3': $(elementos[4]).text(),
                            'total4': $(elementos[5]).text(),
                        });
                    });
                    const data = [
                        [],
                        [$('#nombreEmpresa').val()],
                        ['Balance de Comprobación'],
                        ['Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val()],
                        [],
                        ['Plan', 'Descripción', 'Debe', 'Haber', 'Deudor', 'Acreedor'],
                    ];

                    const datosTabla = arrayDatos.map(item => [
                        item.plan,
                        item.descripcion,
                        parseFloat(item.debe),
                        parseFloat(item.haber),
                        parseFloat(item.deudor),
                        parseFloat(item.acreedor),
                    ]);

                    const footerTexto = [
                        ["", arrayDatosFooter[0]['nombre'], parseFloat(arrayDatosFooter[0]['total1']), parseFloat(arrayDatosFooter[0]['total2']), parseFloat(arrayDatosFooter[0]['total3']), parseFloat(arrayDatosFooter[0]['total4'])],
                    ];



                    const hojaDatos = [...data, ...datosTabla, ...footerTexto];
                    const worksheet = XLSX.utils.aoa_to_sheet(hojaDatos);
                    const workbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(workbook, worksheet, 'Balance de Comprobación');
                    const excelFileName = 'Balance de comprobación  Empresa' + $('#nombreEmpresa').val() + ' Desde ' + $('#desde').val() + ' al ' + $('#hasta').val() + '.xlsx';
                    XLSX.writeFile(workbook, excelFileName);
                },
                footer: true,
                action: function () {
                    // Aquí se llama a tu función personalizada antes de la exportación
                    miFuncionPersonalizada($('#desde').val(), $('#hasta').val());
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf" title="PDF"></i>',
                title: 'Balance de comprobación | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
                customize: function (doc) {
                    doc.content = doc.content.filter(function (item) {
                        return item.text !== 'Balance de comprobación | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val();
                    });
                    doc.styles.header = {
                        fontSize: 18,
                        bold: true,
                        alignment: 'center'
                    };
                    doc.content.unshift(
                        {
                            text: 'Balance de Comprobación',
                            style: 'header',
                            alignment: 'center'
                        },
                        {
                            text: $('#nombreEmpresa').val(),
                            style: 'header',
                            alignment: 'center'
                        },
                        {
                            text: 'Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
                            style: 'header',
                            alignment: 'center'
                        }
                    );
                    var columnsToRightAlign = [2, 3, 4, 5]; // Adjust this array to specify which columns to align right
                    doc.content[3].table.body.forEach(function (row, rowIndex) {
                        if (rowIndex > 0) { // Skip header row
                            columnsToRightAlign.forEach(function (colIndex) {
                                if (row[colIndex] && row[colIndex].text) {
                                    row[colIndex].alignment = 'right';
                                }
                            });
                        }
                    });
                },
                footer: true,
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
    }
    $('#fecha_balance').html('Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val());
}
function miFuncionPersonalizada(d, h) {
    window.open("v3/" + $('#controlador').val() + "/excelBalanceComprobacion?d=" + d + "&h=" + h + '&r=xls', "_self");
}
function buscarVentasNetas() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#v').html(0);
        $('#d').html(0);
        $('#vn').html(0);
        $('#nc').html(0);
        $('#vnf').html(0);
        $('#tableVentasVentasNetas').dataTable().fnClearTable();
        $('#tableVentasVentasNetas').dataTable().fnDestroy();
        $('#tableVentasVentasNetas').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 100,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/ventasnetasjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    cliente: $('#id_cliente').val(),
                    vendedor: $('#id_vendedor').val(),
                    estado: $('#estado_').val(),
                },
            },
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                className: 'text-center',
                data: 'nro_comprobante'
            }, {
                data: 'id_cliente'
            }, {
                data: 'nombre_cliente'
            }, {
                data: 'vendedor'
            }, {
                className: 'text-right',
                data: 'id',
                render: function (data, type, row) {
                    return parseFloat(row.subtotal).toFixed(2);
                }
            }, {
                className: 'text-right',
                data: 'descuento'
            }, {
                className: 'text-right',
                data: 'vn'
            }, {
                className: 'text-right',
                data: 'nc'
            }, {
                className: 'text-right',
                data: 'vnf'
            }, {
                data: 'estado',
                render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
                    return estado;
                }
            }, {
                data: 'usuariocrea'
            }, {
                data: 'usuariomod',
                render: function (data, type, row) {
                    $('#v').html(parseFloat(row.total_subtotal).toFixed(2));
                    $('#d').html(parseFloat(row.total_descuento).toFixed(2));
                    $('#vn').html(parseFloat(row.total_vn).toFixed(2));
                    $('#nc').html(parseFloat(row.total_nc).toFixed(2));
                    $('#vnf').html(parseFloat(parseFloat(row.total_vn) - parseFloat(row.total_nc)).toFixed(2));
                    return row.usuariomod;
                }
            }],
        });
    }
}
function agregarEgresoMasivo(id) {
    limpiarModal();
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nuevo Comprobante de egreso masivo');
    $('#myModal').modal('toggle');
}
function getPerdidasGanancias() {
    $('#frm_').submit();
}
function buscarNotasdeCreditoProveedor() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#subtotal_0').html('0.00');
        $('#subtotal_12').html('0.00');
        $('#subtotal').html('0.00');
        $('#impuesto').html('0.00');
        $('#total').html('0.00');
        $('#tableNotasCreditoProveedor').dataTable().fnClearTable();
        $('#tableNotasCreditoProveedor').dataTable().fnDestroy();
        $('#tableNotasCreditoProveedor').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            info: true,
            aLengthMenu: [[10, 25, 50, 100, - 1], [10, 25, 50, 100, "All"]],
            iDisplayLength: 100,
            dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/notasdeCreditoProveedorjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    proveedor: $('#id_proveedor').val(),
                }
            },
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                className: 'text-center',
                data: 'secuencia_factura'
            }, {
                data: 'nombrecomercial'
            }, {
                className: 'text-center',
                data: 'numero'
            }, {
                data: 'concepto'
            }, {
                className: 'text-right',
                data: 'subtotal_0'
            }, {
                className: 'text-right',
                data: 'subtotal_12'
            }, {
                className: 'text-right',
                data: 'subtotal'
            }, {
                className: 'text-right',
                data: 'impuesto'
            }, {
                className: 'text-right',
                data: 'valor_contable',
                render: function (data, type, row) {
                    $('#subtotal_0').html(row.sumSubtotal0);
                    $('#subtotal_12').html(row.sumSubtotal12);
                    $('#subtotal').html(row.sumSubtotal);
                    $('#impuesto').html(row.sumImpuesto);
                    $('#total').html(row.sumTotal);
                    return row.valor_contable;
                }
            },],
        });
    }
}
function buscarNotasdeCreditoClientes() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de fin...');
    } else {
        $('#total_credito').html('0.00');
        $('#total_deuda').html('0.00');
        $('#total_abono').html('0.00');
        $('#tableNotasCreditoClientes').dataTable().fnClearTable();
        $('#tableNotasCreditoClientes').dataTable().fnDestroy();
        $('#tableNotasCreditoClientes').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/notasdeCreditoClientesjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    id_cliente: $('#id_cliente').val(),
                }
            },

            columns: [
                {
                    className: "text-center",
                    data: "id",
                },
                {
                    className: "text-right",
                    data: "id_asiento_cabecera",
                },
                {
                    className: "text-right",
                    data: "nombre_cliente",
                },
                {
                    className: "text-center",
                    data: "fecha",
                },
                {
                    className: "text-center",
                    data: "tipo",
                },
                {
                    className: "text-center",
                    data: "nro_comprobante",
                },
                {
                    className: "text-center",
                    data: "concepto",
                },
                {
                    className: "text-center",
                    data: "total_credito",
                },
                {
                    className: "text-center",
                    data: "total_deudas",
                },
                {
                    className: "text-center",
                    data: "total_abonos",
                },

                {
                    className: "text-center",
                    data: 'nombre_crea'
                },
                {
                    className: 'text-center',
                    data: 'estado',
                    render: function (data, type, row) {
                        var estado = '<label class="text-success">' + $('#activo').val() + '</label>';
                        if (row.estado == 0) {
                            estado = '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                        }
                        return estado;
                    }
                },
                {
                    className: 'text-center',
                    data: 'electronica',
                    render: function (data, type, row) {
                        if (row.total_credito !== null) {
                            $('#total_credito').html(parseFloat(row.total_credito).toFixed(2));
                        }
                        if (row.total_deudas !== null) {
                            $('#total_deuda').html(parseFloat(row.total_deudas).toFixed(2));
                        }
                        if (row.total_abonos !== null) {
                            $('#total_abono').html(parseFloat(row.total_abonos).toFixed(2));
                        }
                        var electronica = '<label class="text-success">Electronica</label>';
                        if (row.electronica == 0) {
                            electronica = '<label class="text-danger">Normal</label>';
                        }
                        return electronica;
                    }
                },
            ],
        });
    }
}
function buscarClientesRetenciones() {

    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de fin...');
    } else {
        $('#tableClientesReportesRetenciones').dataTable().fnClearTable();
        $('#tableClientesReportesRetenciones').dataTable().fnDestroy();
        $('#tableClientesReportesRetenciones').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/retencionesFunctionjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    id_cliente: $('#id_cliente').val(),
                    estado: $("#estadoO").val()
                }
            },
            columns: [
                {
                    className: "text-center",
                    data: "fecha",
                },
                {
                    data: "nombre",
                },
                {
                    data: "descripcion",
                },
                {
                    className: "text-center",
                    data: "nro_comprobante",
                },
                {
                    className: "text-right",
                    data: "valor_fuente",
                },
                {
                    className: "text-right",
                    data: "valor_iva",
                },
                {
                    className: 'text-right',
                    data: 'total',
                },
                {
                    className: 'text-center',
                    data: 'estado',
                    render: function (data, type, row) {
                        $("#totalF").text("0.00");
                        $("#totalI").text("0.00");
                        $("#totalT").text("0.00");
                        var estado = '<label class="text-success">Activo</label>';
                        if (row.estado == 0) {
                            estado = '<label class="text-danger">Inactivo</label>';
                        }

                        $("#totalF").text(parseFloat(row.total_fuente).toFixed(2));
                        $("#totalI").text(parseFloat(row.total_iva).toFixed(2));
                        $("#totalT").text(parseFloat(row.total_t).toFixed(2));
                        return estado;
                    }
                },
                {
                    className: "text-center",
                    data: "nombre_crea",
                },
            ],
        });
    }

}
function buscarNotasdeDebitoClientes() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de fin...');
    }
    else {
        $('#total').html('0.00');
        $('#tableNotasDebitoClientes').dataTable().fnClearTable();
        $('#tableNotasDebitoClientes').dataTable().fnDestroy();
        $('#tableNotasDebitoClientes').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/notasdeDebitoClientesjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    id_cliente: $('#id_cliente').val(),
                }
            },
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                className: 'text-center',
                data: 'numero'
            }, {
                data: 'cliente'
            }, {
                data: 'concepto'
            }, {
                className: 'text-right',
                data: 'valor_contable',
                render: function (data, type, row) {
                    $('#total').html(row.total);
                    return row.valor_contable;
                },
            }, {
                className: 'text-center',
                data: 'usuariocrea',
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = '<label class="text-success">Activo</label>';
                    if (row.estado == 0) {
                        estado = '<label class="text-danger">Inactivo</label>';
                    }
                    return estado;
                }
            }],
        });
    }


}
function buscarNotasdeDebitoProveedores() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de fin...');
    }
    else {
        $('#total').html('0.00');
        $('#tableNotasDebitoProveedores').dataTable().fnClearTable();
        $('#tableNotasDebitoProveedores').dataTable().fnDestroy();
        $('#tableNotasDebitoProveedores').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/notasdeDebitoProveedoresjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    id_proveedor: $('#id_proveedor').val(),
                }
            },
            columns: [{
                className: 'text-center',
                data: 'secuencia'
            }, {
                className: 'text-right',
                data: 'id_asiento_cabecera'
            }, {
                data: 'concepto'
            }, {
                className: 'text-center',
                data: 'fecha_factura'
            }, {
                data: 'proveedor'
            }, {
                className: 'text-right',
                data: 'valor_contable',
                render: function (data, type, row) {
                    $('#total').html(row.total);
                    return row.valor_contable;
                },
            }, {
                className: 'text-center',
                data: 'ucnombre',
                render: function (data, type, row) {
                    var cadena = row.ucnombre;
                    var letra = cadena.charAt(0);
                    return letra + ". " + row.ucapellido;
                },
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = '<label class="text-success">Activo</label>';
                    if (row.estado == 0) {
                        estado = '<label class="text-danger">Inactivo</label>';
                    }
                    return estado;
                }
            }],
        });
    }

}
function buscarRetencionesProveedor() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#rfir').html('0.00');
        $('#rfiva').html('0.00');
        $('#tablaRetencionesProveedor').dataTable().fnClearTable();
        $('#tablaRetencionesProveedor').dataTable().fnDestroy();
        $('#tablaRetencionesProveedor').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                },
                footer: true,
            }], "ajax": {

                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/retencionesProveedorjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    proveedor: $('#id_proveedor').val(),
                    secuencia: $('#secuencia').val(),
                    retencion: $('#retencion').val(),
                }
            },
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                className: 'text-center',
                data: 'id_asiento_cabecera'
            }, {
                className: 'text-center',
                data: 'nro_comprobante'
            }, {
                className: 'text-center',
                data: 'numero'
            }, {
                data: 'razonsocial'
            }, {
                data: 'id'
            }, {
                data: 'descripcion'
            }, {
                className: 'text-right',
                data: 'valor_fuente',
            }, {
                className: 'text-right',
                data: 'porrenta',
            }, {
                className: 'text-right',
                data: 'valor_iva',
            }, {
                className: 'text-right',
                data: 'poriva',
                render: function (data, type, row) {
                    var poriva = row.poriva !== null ? row.poriva : 0;
                    return poriva;
                }
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
                    return estado;
                }
            }, {
                className: 'text-center',
                data: 'usuariocrea'
            }, {
                className: 'text-center',
                data: 'usuariomod',
                render: function (data, type, row) {
                    $('#rfir').html(parseFloat(row.sumarenta).toFixed(2));
                    $('#rfiva').html(parseFloat(row.sumaiva).toFixed(2));
                    return row.usuariomod;
                }
            }],
        });
    }
}
function excelBalanceResultado() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        window.open('v3/' + $('#controlador').val() + '/excelResultados?d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&c=' + $('#c').val(), '_self');
    }
}
function pdfBalanceResultdo() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    } else {
        window.open('v3/' + $('#controlador').val() + '/pdfResultados?d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&c=' + $('#c').val(), '_blank');
    }
}
function pdfBalanceFinanciero() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    } else {
        window.open('v3/' + $('#controlador').val() + '/pdfBalanceFinanciero?d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&c=' + $('#c').val(), '_blank');
    }
}
function excelBalanceFinanciero() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        window.open('v3/' + $('#controlador').val() + '/excelBalanceFinanciero?d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&c=' + $('#c').val(), '_self');
    }
}
function excelDeudasPendientes() {
    $('#oculto').css('display', 'block');
    window.open('v3/' + $('#controlador').val() + '/excelDeudasPendientes?d=' + $('#desde').val() + '&h=' + $('#hasta').val(), '_self');
}
function buscarDeudavsPagosClientes() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#total_deuda').html('0.00');
        $('#total_abonos').html('0.00');
        $('#sumsaldo').html('0.00');
        $('#tablaDeudavsPagosClientes').dataTable().fnClearTable();
        $('#tablaDeudavsPagosClientes').dataTable().fnDestroy();
        $('#tablaDeudavsPagosClientes').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: false,
            autoWidth: true,
            info: true,
            aLengthMenu: [[2, 4, 8, -1], [2, 4, 8, "All"]],
            iDisplayLength: 100,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                },
                footer: true,
            }], "ajax": {

                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/DeudasvsPagosClientesjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    cliente: $('#id_cliente').val(),
                }
            },
            columnDefs: [
                {
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],  // en la columna 0 el check
                    sortable: false

                }
            ],
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                data: 'detalle'
            }, {
                data: 'concepto'
            }, {
                className: 'text-center',
                data: 'id_asiento',
                render: function (data, type, row) {
                    return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + '\');">' + row.id_asiento + '</a>';
                }
            }, {
                data: 'nombre',
            }, {
                data: 'detalle_cuenta'
            }, {
                className: 'text-right',
                data: 'total_final',
                /*render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
                    return estado;
                }*/
            }, {
                className: 'text-right',
                data: 'deuda',

            }, {
                className: 'text-right',
                data: 'abono'
            }, {
                className: 'text-right',
                data: 'saldo',
                render: function (data, type, row) {
                    $('#total_deuda').html(parseFloat(row.total_deuda).toFixed(2));
                    $('#total_abonos').html(parseFloat(row.total_abonos).toFixed(2));
                    $("#sumsaldo").html(parseFloat(parseFloat(row.total_deuda).toFixed(2) - parseFloat(row.total_abonos).toFixed(2)).toFixed(2));
                    return row.saldo;
                }

            }], createdRow: function (row, data, dataIndex) {
                if (data.orden == 0) {
                    $(row).css('background-color', '#EAFAF1');
                } else {
                    $(row).css('background-color', '#F9EBEA');
                }
            },
        });
    }
}
function buscarInformeAnticipoProveedores() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#TotalValorPago').html('0.00');
        $('#tablaInformeAnticiposProveedores').dataTable().fnClearTable();
        $('#tablaInformeAnticiposProveedores').dataTable().fnDestroy();
        $('#tablaInformeAnticiposProveedores').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: false,
            autoWidth: true,
            info: true,
            aLengthMenu: [[2, 4, 8, -1], [2, 4, 8, "All"]],
            iDisplayLength: 100,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                },
                footer: true,
            }], "ajax": {

                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/InformeAnticiposProveedoresjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    proveedor: $('#id_proveedor').val(),
                }
            },
            columnDefs: [
                {
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7, 8],  // en la columna 0 el check
                    sortable: false

                }
            ],
            columns: [{
                className: 'text-center',
                data: 'id'
            }, {
                className: 'text-center',
                data: 'fecha_comprobante'
            }, {
                className: 'text-center',
                data: 'no_cheque'
            }, {
                className: "text-center",
                data: "id_asiento_cabecera",
                render: function (data, type, row) {
                    return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\')">' + row.id_asiento_cabecera + '</a>';
                }
            }, {
                className: 'text-center',
                data: 'id_proveedor',
            }, {
                data: 'nombrecomercial'
            }, {
                data: 'nombre',
            }, {
                data: 'beneficiario',

            }, {
                data: 'descripcion'
            }, {
                className: 'text-right',
                data: 'valor_pago',
                render: function (data, type, row) {
                    $('#TotalValorPago').html(row.TotalValorPago);
                    return row.valor_pago;
                }

            }], createdRow: function (row, data, dataIndex) {
                if (data.orden == 0) {
                    // $(row).css('background-color', '#EAFAF1');
                } else {
                    // $(row).css('background-color', '#F9EBEA');
                }
            },
        });
    }
}
function buscarEgresoMasivo() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    $('#tableProveedoresEgresoMasivo').dataTable().fnClearTable();
    $('#tableProveedoresEgresoMasivo').dataTable().fnDestroy();
    $('#tableProveedoresEgresoMasivo').DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
            "loadingRecords": $('#loadingRecords').val() + "...",
            "processing": $('#processing').val() + "...",
            "search": $('#search').val(),
            "zeroRecords": $('#zeroRecords').val(),
            "paginate": {
                "first": $('#first').val(),
                "last": $('#last').val(),
                "next": $('#next').val(),
                "previous": $('#previous').val()
            }
        },
        "responsive": true,
        "lengthChange": false,
        "autoWidth": true,
        dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
        buttons: [{
            extend: 'copy',
            text: '<i class="fa fa-copy" title="Copy"></i>'
        },
        {
            extend: 'excel',
            title: tituloExcel,
            text: '<i class="fas fa-file-excel" title="Excel"></i>'
        },
        {
            extend: 'pdf',
            title: tituloExcel,
            text: '<i class="fas fa-file-pdf" title="PDF"></i>'
        }, {
            extend: 'print',
            text: '<i class="fa fa-print" title="Print"></i>',
            customize: function (win) {
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
            }
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/buscarEgresoMasivo",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            data: {
                d: $('#desde').val(),
                h: $('#hasta').val(),
                p: $('#id_proveedor').val()
            },
        },
        columns: [{
            data: 'id'
        },
        {
            data: 'descripcion'
        },
        {
            data: 'secuencia'
        },
        {
            className: "text-center",
            data: "id_asiento_cabecera",
            render: function (data, type, row) {
                return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\')">' + row.id_asiento_cabecera + '</a>';
            }
        },
        {
            data: 'fecha_cheque'
        },
        {
            data: 'no_cheque'
        },
        {
            data: 'girado_a'
        },
        {
            data: 'valor_pago'
        },
        {
            data: 'nombreCrea'
        },
        {
            data: 'estado',
            render: function (data, type, row) {
                var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                return estado;
            }
        },
        ],
    });
}
function buscarDeudaPendientesClientes() {
    if ($("#desde").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de inicio...");
    } else if ($("#hasta").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de final...");
    } else {

        var data = {
            d: $('#desde').val(),
            h: $('#hasta').val(),
            c: $("#id_cliente").val(),
            t: $("#tipo").val(),
            co: $("#concepto").val(),
        };
        var json = ajax("v3/" + $("#controlador").val() + '/' + $("#submodulo").val() + "/DeudasPendientesClientejs", data, 'json');
        $('#reporteAjax').html(json.responseJSON.html);
        $('#totalDeuda').html(json.responseJSON.valor);
    }
}
function buscarDeudaPendientes_() {
    if ($("#desde").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de inicio...");
    } else if ($("#hasta").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de final...");
    } else {
        $("#sumadeuda").html(0);
        $("#sumabono").html(0);
        $("#sumsaldo").html(0);
        $("#tablaDeudasPendientes").dataTable().fnClearTable();
        $("#tablaDeudasPendientes").dataTable().fnDestroy();
        $("#tablaDeudasPendientes").DataTable({
            language: {
                decimal: "",
                emptyTable: $("#emptyTable").val(),
                info: $("#mostrando").val() + " _START_ " + $("#a").val() + " _END_ " + $("#de").val() + " _TOTAL_ " + $("#entradas").val(),
                infoEmpty: $("#mostrando").val() + " 0 " + $("#a").val() + " 0 " + $("#de").val() + " 0 " + $("#entradas").val(),
                infoFiltered: "(" + $("#filtrado").val() + " " + $("#de").val() + " _MAX_ total " + $("#entradas").val() + ")",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: $("#mostrar").val() + " _MENU_ " + $("#entradas").val(),
                loadingRecords: $("#loadingRecords").val() + "...",
                processing: $("#processing").val() + "...",
                search: $("#search").val(),
                zeroRecords: $("#zeroRecords").val(),
                paginate: {
                    first: $("#first").val(),
                    last: $("#last").val(),
                    next: $("#next").val(),
                    previous: $("#previous").val(),
                },
            },
            responsive: true,
            lengthChange: false,
            autoWidth: true,
            info: true,
            aLengthMenu: [
                [2, 4, 8, 100, -1],
                [2, 4, 8, 100, "All"],
            ],
            iDisplayLength: 100,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [
                {
                    extend: "copy",
                    text: '<i class="fa fa-copy" title="Copy"></i>',
                },
                {
                    extend: "excel",
                    title: tituloExcel,
                    text: '<i class="fas fa-file-excel" title="Excel"></i>',
                    footer: true,
                },
                {
                    extend: "print",
                    text: '<i class="fa fa-print" title="Print"></i>',
                    customize: function (win) {
                        $(win.document.body).addClass("white-bg");
                        $(win.document.body).css("font-size", "10px");
                        $(win.document.body).find("table").addClass("compact").css("font-size", "inherit");
                    },
                    footer: true,
                },
            ],
            ajax: {
                url: "v3/" + $("#controlador").val() + '/' + $("#submodulo").val() + "/DeudasPendientesjs",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    d: $("#desde").val(),
                    h: $("#hasta").val(),
                    p: $("#id_proveedor").val(),
                },
            },
            columnDefs: [
                {
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6],
                    sortable: false,
                },
            ],
            columns: [
                { className: "text-center", data: "fecha_compra" },
                { data: "comprobante" },
                { data: "cuenta" },
                { data: "detalle" },
                { data: "id_asiento" },
                { data: "razonsocial" },
                { className: "text-right", data: "valor_contable" },
                { className: "text-right", data: "deuda" },
                { className: "text-right", data: "abono" },
            ],
        });
    }
}
function buscarDeudaPendientes() {
    if ($("#hasta").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de final...");
    } else {
        var data = {
            d: $('#desde').val(),
            h: $('#hasta').val(),
            p: $("#id_proveedor").val(),
        };
        var json = ajax("v3/" + $('#controlador').val() + "/proveedores/DeudasPendientesjs", data, 'json');
        $('#reporteAjax').html(json.responseJSON.body);
    }
}
function buscarAsientos() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    $('#tableLibroDiario').dataTable().fnClearTable();
    $('#tableLibroDiario').dataTable().fnDestroy();
    $('#tableLibroDiario').DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
            "loadingRecords": $('#loadingRecords').val() + "...",
            "processing": $('#processing').val() + "...",
            "search": $('#search').val(),
            "zeroRecords": $('#zeroRecords').val(),
            "paginate": {
                "first": $('#first').val(),
                "last": $('#last').val(),
                "next": $('#next').val(),
                "previous": $('#previous').val()
            }
        },
        responsive: true,
        lengthChange: false,
        autoWidth: true,
        info: true,
        aLengthMenu: [[2, 4, 8, -1], [2, 4, 8, "All"]],
        iDisplayLength: 100,
        dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
        columnDefs: [
            { "orderable": false, "targets": [0, 1, 2, 3, 4, 5, 6] },
        ],
        buttons: [{
            extend: 'copy',
            text: '<i class="fa fa-copy" title="Copy"></i>'
        },
        {
            extend: 'excel',
            title: 'Libro diario | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
            text: '<i class="fas fa-file-excel" title="Excel"></i>',
            footer: true,
        },
        {
            extend: 'pdf',
            title: 'Libro diario | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
            text: '<i class="fas fa-file-pdf" title="PDF"></i>'
        }, {
            extend: 'print',
            text: '<i class="fa fa-print" title="Print"></i>',
            customize: function (win) {
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
            }
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleLibroDiario",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            data: {
                d: $('#desde').val(),
                h: $('#hasta').val(),
            },
        },
        columns: [{
            data: 'id',
            render: function (data, type, row) {
                return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id + '\');">' + row.id + '</a>';
            }
        }, {
            className: 'text-center',
            data: 'fecha_asiento',
            render: function (data, type, row) {
                return getfecha(row.fecha_asiento);
            }
        }, {
            className: 'text-center',
            data: 'plan',
        }, {
            data: 'nombre_cuenta'
        }, {
            className: 'text-right',
            data: 'debe',
            render: function (data, type, row) {
                return row.debe;
            }
        }, {
            className: 'text-right',
            data: 'haber',
            render: function (data, type, row) {
                $('#totalDebe').html(row.total_debe);
                $('#totalHaber').html(row.total_haber);
                return row.haber;
            }
        }, {
            data: 'observacion'
        }, {
            className: 'text-center',
            data: 'usuariocrea'
        }, {
            className: 'text-center',
            data: 'created_at',
            render: function (data, type, row) {
                return getfecha(row.created_at);
            }
        }, {
            className: 'text-center',
            data: 'usuariomod',
        }, {
            className: 'text-center',
            data: 'updated_at',
            render: function (data, type, row) {
                return getfecha(row.updated_at);
            }
        }, {
            className: 'text-center',
            data: 'cuadrado'
        }, {
            className: 'text-center',
            data: 'aparece_sri',
            render: function (data, type, row) {
                console.log(row.aparece_sri);
                return row.aparece_sri !== 1 ? "NO" : "SI";
            }
        }],
        createdRow: function (row, data, dataIndex) {
            console.log(data)
            if (data.cuadrado == 0) {
                $(row).css('background-color', '#FBE6D2');
            } else {
                $(row).css('background-color', '#C0FBCD');
            }
        }
    });
}
function buscarDeudasvsPagosProveedores() {
    if ($("#desde").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de inicio...");
    } else if ($("#hasta").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de fin...");
    } else {
        $('#t_deuda').html('0.00');
        $('#t_abono').html('0.00');
        $('#t_saldo').html('0.00');
        $("#tablaDeudasvsPagosProveedores").dataTable().fnClearTable();
        $("#tablaDeudasvsPagosProveedores").dataTable().fnDestroy();
        $("#tablaDeudasvsPagosProveedores").dataTable({
            language: {
                decimal: "",
                emptyTable: $("#emptyTable").val(),
                info: $("#mostrando").val() + " _START_ " + $("#a").val() + " _END_ " + $("#de").val() + " _TOTAL_ " + $("#entradas").val(),
                infoEmpty: $("#mostrando").val() + " 0 " + $("#a").val() + " 0 " + $("#de").val() + " 0 " + $("#entradas").val(),
                infoFiltered: "(" + $("#filtrado").val() + " " + $("#de").val() + " _MAX_ total " + $("#entradas").val() + ")",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: $("#mostrar").val() + " _MENU_ " + $("#entradas").val(),
                loadingRecords: $("#loadingRecords").val() + "...",
                processing: $("#processing").val() + "...",
                search: $("#search").val(),
                zeroRecords: $("#zeroRecords").val(),
                paginate: {
                    first: $("#first").val(),
                    last: $("#last").val(),
                    next: $("#next").val(),
                    previous: $("#previous").val(),
                },
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            info: true,
            aLengthMenu: [
                [2, 4, 8, -1],
                [2, 4, 8, "All"],
            ],
            iDisplayLength: 1000,
            dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
            buttons: [
                {
                    extend: "copy",
                    text: '<i class="fa fa-copy" title="Copy"></i>',
                },
                {
                    extend: "excel",
                    title: tituloExcel,
                    text: '<i class="fas fa-file-excel" title="Excel"></i>',
                    footer: true,
                },
                {
                    extend: "print",
                    text: '<i class="fa fa-print" title="Print"></i>',
                    customize: function (win) {
                        $(win.document.body).addClass("white-bg");
                        $(win.document.body).css("font-size", "10px");
                        $(win.document.body)
                            .find("table")
                            .addClass("compact")
                            .css("font-size", "inherit");
                    },
                },
            ],
            ajax: {
                url:
                    "v3/" +
                    $("#controlador").val() + "/" + $("#submodulo_").val() + "/deudasvspagosproveedoresjs",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    d: $("#desde").val(),
                    h: $("#hasta").val(),
                    id_proveedor: $("#id_proveedor").val(),
                },
            },
            columnDefs: [
                {
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],  // en la columna 0 el check
                    sortable: false
                }
            ],
            columns: [
                {
                    className: "text-center",
                    data: "fecha",
                    render: function (data, type, row) {
                        var fecha = row.fecha;
                        if (/COM-FA/.test(row.Comprobante)) {
                            fecha = row.fecha_documento;
                        }
                        return fecha;
                    }
                },
                {
                    className: "text-left",
                    data: "Comprobante",
                },
                {
                    data: "detalle",
                },
                {
                    className: 'text-center',
                    data: 'id_asiento',
                    render: function (data, type, row) {
                        return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + '\');">' + row.id_asiento + '</a>';
                    }
                },
                {
                    data: "razonsocial",
                },
                {
                    data: "cuenta",
                },
                {
                    className: "text-right",
                    data: "total_final",
                },
                {
                    className: "text-right",
                    data: "deuda",
                },
                {
                    className: "text-right",
                    data: "abono",
                },
                {
                    className: 'text-right',
                    data: 'saldo',
                    render: function (data, type, row) {
                        var saldo = row.deuda_total - row.abono_total;
                        $('#t_deuda').html(parseFloat(row.deuda_total).toFixed(2));
                        $('#t_abono').html(parseFloat(row.abono_total).toFixed(2));
                        $('#t_saldo').html(parseFloat(saldo).toFixed(2));
                        return row.saldo;
                    }
                }
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.orden == 0) {
                    $(row).css('background-color', '#EAFAF1');
                } else {
                    $(row).css('background-color', '#F9EBEA');
                }
            }
        });
    }
}
function buscarBalanceAnulados() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    $('#tableBalanceComprobacion').dataTable().fnClearTable();
    $('#tableBalanceComprobacion').dataTable().fnDestroy();
    var data = {
        d: $('#desde').val(),
        h: $('#hasta').val(),
    };
    var html = ajax("v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleBalanceComprobacionAnulacion", data, 'html');
    if (html.responseText != '') {
        html = html.responseText.split('|');
        $('#cuerpoBalanceComprobacion').html(html[0]);
        $('#totalDebe').html(parseFloat(html[1]).toFixed(2));
        $('#totalHaber').html(parseFloat(html[2]).toFixed(2));
        $('#totalDeudor').html(parseFloat(html[3]).toFixed(2));
        $('#totalAcreedor').html(parseFloat(html[4]).toFixed(2));
        $('#tableBalanceComprobacion').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: false,
            autoWidth: true,
            info: true,
            iDisplayLength: 1000,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            order: [[0, "asc"]],
            columnDefs: [
                { "orderable": false, "targets": [0, 1, 2, 3, 4, 5] },
            ],
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: 'Balance de comprobación | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf" title="PDF"></i>',
                title: 'Balance de comprobación | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
                customize: function (doc) {
                    doc.content = doc.content.filter(function (item) {
                        return item.text !== 'Balance de comprobación | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val();
                    });
                    doc.styles.header = {
                        fontSize: 18,
                        bold: true,
                        alignment: 'center'
                    };
                    doc.content.unshift(
                        {
                            text: 'Balance de Comprobación',
                            style: 'header',
                            alignment: 'center'
                        },
                        {
                            text: $('#nombreEmpresa').val(),
                            style: 'header',
                            alignment: 'center'
                        },
                        {
                            text: 'Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
                            style: 'header',
                            alignment: 'center'
                        }
                    );
                    var columnsToRightAlign = [2, 3, 4, 5]; // Adjust this array to specify which columns to align right
                    doc.content[3].table.body.forEach(function (row, rowIndex) {
                        if (rowIndex > 0) { // Skip header row
                            columnsToRightAlign.forEach(function (colIndex) {
                                if (row[colIndex] && row[colIndex].text) {
                                    row[colIndex].alignment = 'right';
                                }
                            });
                        }
                    });
                },
                footer: true,
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
    }
}
function exportarExcelFincnaciero(idTabla, nombreArchivo = '') {
    let linkDescarga;
    let tipoDatos = 'application/vnd.ms-excel';
    let tablaDatos = document.getElementById(idTabla);
    let tablaHTML = tablaDatos.outerHTML.replace(/ /g, '%20');
    let base64Image = document.getElementById('logoImage').src;
    console.log(base64Image)
    let imagenHTML = base64Image ? `< div class="img-container" >
                <img src="${base64Image}" alt="Logo" class="centered-img">
                </div>` : '';
    let contenidoHTML = tablaHTML;
    nombreArchivo = nombreArchivo ? nombreArchivo + '.xls' : 'Reporte_Puntos_Canjeados.xls';
    linkDescarga = document.createElement("a");
    document.body.appendChild(linkDescarga);
    if (navigator.msSaveOrOpenBlob) {
        let blob = new Blob(['\ufeff', contenidoHTML], {
            type: tipoDatos
        });
        navigator.msSaveOrOpenBlob(blob, nombreArchivo);
    } else {
        linkDescarga.href = 'data:' + tipoDatos + ', ' + contenidoHTML + imagenHTML;
        linkDescarga.download = nombreArchivo;
        linkDescarga.click();
    }
    document.body.removeChild(linkDescarga);
}
function excelResultadoMensual() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    } else {
        window.open("v3/" + $('#controlador').val() + "/excelResultadoMensual?d=" + $('#desde').val() + "&h=" + $('#hasta').val() + '&r=xls', "_self");
    }
}
function buscarChequesGirados() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    $('#tableChequesGirados').dataTable().fnClearTable();
    $('#tableChequesGirados').dataTable().fnDestroy();
    $("#tableChequesGirados").DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
            "loadingRecords": $('#loadingRecords').val() + "...",
            "processing": $('#processing').val() + "...",
            "search": $('#search').val(),
            "zeroRecords": $('#zeroRecords').val(),
            "paginate": {
                "first": $('#first').val(),
                "last": $('#last').val(),
                "next": $('#next').val(),
                "previous": $('#previous').val()
            }
        },
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        iDisplayLength: 100,
        dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
        buttons: [{
            extend: 'copy',
            text: '<i class="fa fa-copy" title="Copy"></i>'
        },
        {
            extend: 'excel',
            title: tituloExcel,
            text: '<i class="fas fa-file-excel" title="Excel"></i>',
            footer: true,
        },
        {
            extend: 'pdf',
            title: tituloExcel,
            text: '<i class="fas fa-file-pdf" title="PDF"></i>',
            footer: true,
        }, {
            extend: 'print',
            text: '<i class="fa fa-print" title="Print"></i>',
            customize: function (win) {
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
            }
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/chequesgiradosjs",
            type: 'post',
            data: {
                d: $("#desde").val(),
                h: $("#hasta").val(),
                id_proveedor: $("#id_proveedor").val(),
            },
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            }, error(error) {
                console.log(error);
            }
        },
        columns: [{
            className: "text-center",
            data: 'fecha_comprobante'
        },
        {
            className: "text-center",
            data: 'fecha_cheque'
        },
        {
            className: "text-center",
            data: 'id_asiento_cabecera',
            render: function (data, type, row) {
                return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\');">' + row.id_asiento_cabecera + '</a>';
            }
        },
        {
            className: "text-center",
            data: "cheque",
        },
        {
            data: 'razonsocial'
        },
        {
            data: 'banco'
        },
        {
            className: "text-right",
            data: 'valor'
        },
        {
            className: "text-center",
            data: 'fecha_comprobante',
            render: function (d, t, r) {
                var estado = '<div class="bg-success color-palette"><span>Activo</span></div>';
                if (r.estado_egreso == 0) {
                    estado = '<div class="bg-danger color-palette"><span>Anulado</span></div>';
                }
                $('#totalGirado').html(r.total_global);
                return estado;
            }
        }
        ],
    });
}

function buscarPedidos() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    $('#tablePedidos').dataTable().fnClearTable();
    $('#tablePedidos').dataTable().fnDestroy();
    $('#tablePedidos').DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
            "loadingRecords": $('#loadingRecords').val() + "...",
            "processing": $('#processing').val() + "...",
            "search": $('#search').val(),
            "zeroRecords": $('#zeroRecords').val(),
            "paginate": {
                "first": $('#first').val(),
                "last": $('#last').val(),
                "next": $('#next').val(),
                "previous": $('#previous').val()
            }
        },
        responsive: true,
        lengthChange: false,
        autoWidth: true,
        info: true,
        aLengthMenu: [[2, 4, 8, -1], [2, 4, 8, "All"]],
        iDisplayLength: 100,
        dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
        buttons: [{
            extend: 'copy',
            text: '<i class="fa fa-copy" title="Copy"></i>'
        },
        {
            extend: 'excel',
            title: 'Libro diario | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
            text: '<i class="fas fa-file-excel" title="Excel"></i>',
            footer: true,
        },
        {
            extend: 'pdf',
            title: 'Libro diario | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
            text: '<i class="fas fa-file-pdf" title="PDF"></i>'
        }, {
            extend: 'print',
            text: '<i class="fa fa-print" title="Print"></i>',
            customize: function (win) {
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
            }
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/buscarPedidos",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            data: {
                d: $('#desde').val(),
                h: $('#hasta').val(),
                p: $('#id_proveedor').val()
            },
        },
        columns: [
            {
                data: "id",
            },
            {
                data: "factura",
            },
            {
                data: "numero_documento",
            },
            {
                data: "nombrecomercial",
            },
            {
                data: "fecha",
            },
            {
                data: "nombre",
            },
            {
                data: "cantidad",
            },
            {
                data: "valor_unitario",
            },
            {
                data: "subtotal",
            },
            {
                data: "iva",
            },
            {
                data: "descuento",
            },
            {
                data: "total",
            },
        ]
    });
}

function buscarInformeTiempo() {
    let desde = $("#desde").val();
    let hasta = $("#hasta").val();
    let proveedor = $("#id_proveedor").val();
    $('#tableInformeTiempo').dataTable().fnClearTable();
    $('#tableInformeTiempo').dataTable().fnDestroy();
    $('#tableInformeTiempo').DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
            "loadingRecords": $('#loadingRecords').val() + "...",
            "processing": $('#processing').val() + "...",
            "search": $('#search').val(),
            "zeroRecords": $('#zeroRecords').val(),
            "paginate": {
                "first": $('#first').val(),
                "last": $('#last').val(),
                "next": $('#next').val(),
                "previous": $('#previous').val()
            }
        },
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        iDisplayLength: 100,
        dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
        buttons: [
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        "ajax": {
            url: "v3/" + $("#controlador").val() + "/" + $('#submodulo_').val() + "/InformeTiempojs",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            data: {
                desde: desde,
                hasta: hasta,
                proveedor: proveedor
            },
        },
        columns: [
            {
                className: 'text-right',
                data: 'id_compra'
            }, {
                className: 'text-center',
                data: 'fecha'
            }, {
                data: 'autorizacion',
            }, {
                className: 'text-right',
                data: 'comprobante'
            }, {
                className: 'text-right',
                data: 'id_proveedor'
            }, {
                className: 'text-right',
                data: 'nombre_proveedor'
            },
            {
                className: 'text-right',
                data: 'concepto'
            },
            {
                className: 'text-right',
                data: 'valor'
            }
        ],
    });
}


function buscadorReporteInventario() {


    $('#tableInventario').dataTable().fnClearTable();
    $('#tableInventario').dataTable().fnDestroy();
    var data = {
        nombre_producto: $('#nombre_producto').val(),
        producto: $('#producto').val(),
        bodega: $('#bodega').val(),
        tipo: $('#tipo').val(),
    };

    $("#tableInventario").DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
            "loadingRecords": $('#loadingRecords').val() + "...",
            "processing": $('#processing').val() + "...",
            "search": $('#search').val(),
            "zeroRecords": $('#zeroRecords').val(),
            "paginate": {
                "first": $('#first').val(),
                "last": $('#last').val(),
                "next": $('#next').val(),
                "previous": $('#previous').val()
            }
        },
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        iDisplayLength: 8,
        dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
        buttons: [{
            extend: 'copy',
            text: '<i class="fa fa-copy" title="Copy"></i>'
        },
        {
            extend: 'excel',
            title: tituloExcel,
            text: '<i class="fas fa-file-excel" title="Excel"></i>',
            footer: true,
        },
        {
            extend: 'pdf',
            title: tituloExcel,
            text: '<i class="fas fa-file-pdf" title="PDF"></i>',
            footer: true,
        }, {
            extend: 'print',
            text: '<i class="fa fa-print" title="Print"></i>',
            customize: function (win) {
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
            }
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/inventarioReporte",
            type: 'post',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            }, error(error) {
                console.log(error);
            }
        },
        columns: [{
            className: "text-center",
            data: 'id'
        },
        {
            className: "text-center",
            data: 'codigo'
        },
        {
            className: "text-center",
            data: 'nombre'
        },
        {
            className: "text-center",
            data: 'nombreBodega'
        },
        {
            className: "text-right",
            data: 'existencia'
        },
        {
            className: "text-right",
            data: 'existencia_min'
        },
        {
            className: "text-right",
            data: 'costo_promedio_formateado'
        },
        {
            className: "text-right",
            data: 'costo_formateado'
        },
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            var existencia = api
                .column(5, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
            var stockminimo = api
                .column(6, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            var valorUnitario = api
                .column(7, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            var valorTotal = api
                .column(8, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            $(api.column(5).footer()).html(existencia.toFixed(2));
            $(api.column(6).footer()).html(stockminimo.toFixed(2));
            $(api.column(7).footer()).html(valorUnitario.toFixed(2));
            $(api.column(8).footer()).html(valorTotal.toFixed(2));
        }
    });





}


function resetFields() {

    $('#nombre_producto').val("").trigger("change");
    $('#producto').val("").trigger("change");
    $('#bodega').val("").trigger("change");
    $('#tipo').val("").trigger("change");
}

function buscadorXSerie() {
    $('#tableInventarioSerie').dataTable().fnClearTable();
    $('#tableInventarioSerie').dataTable().fnDestroy();
    let data = {
        nombre_producto: $("#nombre_producto").val(),
        descripcion: $("#descripcion").val(),
        marca: $("#marca").val(),
        bodega: $("#bodega").val(),
        serie: $("#serie").val(),
    };
    console.log(data);
    $("#tableInventarioSerie").DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
            "loadingRecords": $('#loadingRecords').val() + "...",
            "processing": $('#processing').val() + "...",
            "search": $('#search').val(),
            "zeroRecords": $('#zeroRecords').val(),
            "paginate": {
                "first": $('#first').val(),
                "last": $('#last').val(),
                "next": $('#next').val(),
                "previous": $('#previous').val()
            }
        },
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        iDisplayLength: 8,
        dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
        buttons: [{
            extend: 'copy',
            text: '<i class="fa fa-copy" title="Copy"></i>'
        },
        {
            extend: 'excel',
            title: tituloExcel,
            text: '<i class="fas fa-file-excel" title="Excel"></i>',
            footer: true,
        },
        {
            extend: 'pdf',
            title: tituloExcel,
            text: '<i class="fas fa-file-pdf" title="PDF"></i>',
            footer: true,
        }, {
            extend: 'print',
            text: '<i class="fa fa-print" title="Print"></i>',
            customize: function (win) {
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
            }
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/inventarioReporteSerie",
            type: 'post',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            }, error(error) {
                console.log(error);
            }
        },
        columns: [{
            className: "text-center",
            data: 'id'
        },
        {
            className: "text-center",
            data: 'codigo_producto'
        },

        {
            className: "text-center",
            data: "marca",
        },
        {
            className: "text-center",
            data: 'nombre_producto'
        },

        {
            className: "text-right",
            data: 'nr_pedido'
        },
        {
            className: "text-right",
            data: 'bodega'
        },
        {
            className: "text-right",
            data: 'nombre_proveedor'
        },
        {
            className: "text-right",
            data: 'lote'
        },
        {
            className: "text-right",
            data: 'serie'
        },
        {
            className: "text-right",
            data: 'fecha_vence'
        },
        {
            className: "text-right",
            data: 'existencia'
        },
        {
            className: "text-right",
            data: 'existencia_min'
        },
        {
            className: "text-right",
            data: 'existencia_max'
        },
        {
            className: "text-right",
            data: 'documento'
        },
        {
            className: "text-right",
            data: 'costo_promedio'
        },

        ],

    });

}




function resetFieldsXSerie() {
    $('#nombre_producto').val("").trigger("change");
    $('#descripcion').val("");
    $('#marca').val("").trigger("change");
    $('#bodega').val("").trigger("change");
    $('#serie').val("");
}
