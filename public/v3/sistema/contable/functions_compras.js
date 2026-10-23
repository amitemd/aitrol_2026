var fila = 0;
var filaCompra = 0;
var filaGasto = 0;
var btnRuc = $('.ladda-button-demo-ruc').ladda();
var btnCed = $('.ladda-button-demo-ced').ladda();
var btnCer = $('.ladda-button-demo-cer').ladda();
var btnXMLCompra = $('.ladda-button-xml-compra').ladda();
$(document).ready(function () {
    $(".select_orden").select2({
        minimumInputLength: 3,
        cache: true,
        ajax: {
            url: "v3/" + $("#controlador").val() + "/getOrdenesActivoFijo",
            delay: 250,
            data: function (params) {
                return {
                    term: params.term,
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.data,
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
    });
    if (($('#submodulo_').val() == 'addcompra')) {
        $(".botonMenu").trigger("click");
    }
    var table_data_pedido = '';
    btnRuc.click(function () {
        btnRuc.ladda('start');
    });
    btnCed.click(function () {
        btnCed.ladda('start');
    });
    btnCer.click(function () {
        btnCer.ladda('start');
    });
    btnXMLCompra.click(function () {
        btnXMLCompra.ladda('start');
    });
    $("#elementTour").val(
        "hero-compra,hero-datosCompra,hero-proveedor,hero-agregarlinea,hero-agregarproveedor"
    );
    try {
        $("#tableIngresoActivos").DataTable({
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
            pageLength: 100,
            order: [[2, "desc"]],
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
                },
                {
                    extend: "pdf",
                    title: tituloExcel,
                    text: '<i class="fas fa-file-pdf" title="PDF"></i>',
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
                    "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                error(error) {
                    console.log(error);
                },
            },
            columns: [
                {
                    className: "text-center",
                    data: "id",
                },
                {
                    className: "text-center",
                    data: "secuencia_c",
                },
                {
                    className: "text-center",
                    data: "id_asiento",
                    render: function (data, type, row) {
                        return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + '\')">' + row.id_asiento + '</a>';
                    }
                },
                {
                    className: "text-center",
                    data: "fecha_asiento",
                    render: function (data, type, row) {
                        let fechap = formatDate(row.fecha_asiento)
                        return fechap;
                    }
                },
                {
                    className: "text-center",
                    data: "proveedor",
                },
                {
                    className: "text-right",
                    data: "razonsocial",
                },
                {
                    className: "text-right",
                    data: "nro_autorizacion",
                },
                {
                    className: "text-right",
                    data: "usuario_crea",
                },
                {
                    className: "text-center",
                    data: "estado",
                    render: function (data, type, row) {
                        var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                        return estado;
                    },
                },

                {
                    className: "text-center",
                    data: "id",
                    render: function (data, type, row) {

                        var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
                        var archivo = '<a href="javascript:;" style="margin: 0px;" class="dropdown-item"><label title="Archivo pdf del documento a ingresar" class="archivoActivo" for="archivoActivo_' + row.id + '" style="cursor:pointer;font-weight: normal;"><input type="file" accept="image/*" onchange="verificarCompraActivo(this)" name="archivoActivo_' + row.id + '" id="archivoActivo_' + row.id + '" style="display:none"><i class="fas fa-file-upload" style="color:green;"></i> Subir archivo</label></a>';

                        if (row.estado == 1) {
                            accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
                        }
                        if (row.id_retencion !== null) {

                            accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
                        }
                        if (row.nombre_archivo !== null) {
                            archivo = '<div class="dropdown-divider"></div><a href="javascript:;" class="dropdown-item" onclick="verArchivoActivo(\'' + row.nombre_archivo + '\')"><i class="fas fa-file-download" style="color:blue;"></i> Ver archivo</a><a href="javascript:;" class="dropdown-item" onclick="eliminarArchivoActivo(\'' + row.id + '\')"><i class="fas fa-trash-alt" style="color:red;"></i> Eliminar archivo</a><div class="dropdown-divider"></div>';
                        }
                        var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDocumentoActivo(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fas fa-file-invoice" style="color:#28a745"></i></i> Ver documento</a>' + archivo + '<div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="verificarActivo(\'' + row.id + "','" + row.estado + "','Factura');\">" + accion + "</a></div></div>";
                        return botones;
                    },
                },
            ],
        });
        $("#tablePreOrden").DataTable({
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
            pageLength: 100,
            order: [[2, "desc"]],
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
                },
                {
                    extend: "pdf",
                    title: tituloExcel,
                    text: '<i class="fas fa-file-pdf" title="PDF"></i>',
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
                    "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                error(error) {
                    console.log(error);
                },
            },
            columns: [
                {
                    className: "text-center",
                    data: "id",
                },
                {
                    className: "text-center",
                    data: "serie",
                    render: function (data, type, row) {
                        return row.serie + '-' + row.secuencia;
                    }
                },
                {
                    className: "text-center",
                    data: "fecha_asiento",
                    render: function (data, type, row) {
                        let fechap = formatDate(row.fecha_asiento)
                        return fechap;
                    }
                },
                {
                    className: "text-center",
                    data: "proveedor",
                },
                {
                    className: "text-right",
                    data: "nombrecomercial",
                },
                {
                    className: "text-right",
                    data: "observacion",
                },
                {
                    className: "text-right",
                    data: "usuario_crea",
                },
                {
                    className: "text-center",
                    data: "estado",
                    render: function (data, type, row) {
                        var estado = row.estadoL == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                        return estado;
                    },
                },

                {
                    className: "text-center",
                    data: "id",
                    render: function (data, type, row) {
                        var archivo = '<a href="javascript:;" style="margin: 0px;" class="dropdown-item"><label title="Archivo pdf del documento a ingresar" class="archivoActivo" for="archivoActivo_' + row.id + '" style="cursor:pointer;font-weight: normal;"><input type="file" accept="image/*" onchange="verificarCompraActivo(this)" name="archivoActivo_' + row.id + '" id="archivoActivo_' + row.id + '" style="display:none"><i class="fas fa-file-upload" style="color:green;"></i> Subir archivo</label></a>';

                        console.log(row.nombre_archivo);
                        if (row.nombre_archivo !== null) {
                            archivo = '<a href="javascript:;" class="dropdown-item" onclick="verArchivoActivo(\'' + row.nombre_archivo + '\')"><i class="fas fa-file-download" style="color:blue;"></i> Ver archivo</a><a href="javascript:;" class="dropdown-item" onclick="eliminarArchivoActivo(\'' + row.id + '\')"><i class="fas fa-trash-alt" style="color:red;"></i> Eliminar archivo</a>';
                        }
                        var botones = '<div class="btn-group"><button type="button" class="btn btn-' +
                            $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
                            $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDocumentoPreOrden(\'' + row.id + '\');"><i class="fas fa-edit" style="color:#F39C12"></i></i>Editar</a><a class="dropdown-item" href="javascript:;" onclick="verDocumentoActivo(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fa fa-upload" style="color:gray"></i></i> Fotos</a><a class="dropdown-item" href="javascript:;" onclick="verDocumentoActivo(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fas fa-trash-alt" style="color:#DD4B39"></i></i> Aprobar</a><a class="dropdown-item" href="javascript:;" onclick="verDocumentoActivo(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fa fa-check" style="color:#00A65A"></i></i> Eliminar</a>' + archivo + "</div></div></div>";
                        return botones;
                    },
                },
            ],
        });
        $("#tableCompras").DataTable({
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
            pageLength: 100,
            order: [[2, "desc"]],
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
                },
                {
                    extend: "pdf",
                    title: tituloExcel,
                    text: '<i class="fas fa-file-pdf" title="PDF"></i>',
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
                    "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                error(error) {
                    console.log(error);
                },
            },
            columns: [
                {
                    className: "text-center",
                    data: "numero",
                },
                {
                    data: "razonsocial",
                },
                {
                    className: "text-center",
                    data: "id",
                },
                {
                    className: "text-center",
                    data: "id_asiento_cabecera",
                    render: function (data, type, row) {
                        return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\')">' + row.id_asiento_cabecera + '</a>';
                    }
                },
                {
                    className: "text-center",
                    data: "tipo_comprobante",
                },
                {
                    className: "text-center",
                    data: "fecha",
                },
                {
                    className: "text-right",
                    data: "subtotal_0",
                },
                {
                    className: "text-right",
                    data: "subtotal_12",
                },
                {
                    className: "text-right",
                    data: "iva_total",
                },
                {
                    className: "text-right",
                    data: "total_final",
                },
                {
                    className: "text-center",
                    data: "estado",
                    render: function (data, type, row) {
                        var estado = '';
                        if (row.id_retencion !== null) {
                            estado += '<label class="text-info">' + $('#conretencion').val() + '</label>';
                        } else if (row.estado == 0) {
                            estado += '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                        } else if (row.estado == 1 || row.estado == 2 || row.estado == 3) {
                            estado += '<label class="text-success">' + $('#activo').val() + '</label>';
                        }
                        return estado;
                    },
                },
                {
                    className: "text-center",
                    data: "usuariocrea",
                },
                {
                    className: "text-center",
                    data: "usuariomod",
                },
                {
                    className: "text-center",
                    data: "id",
                    render: function (data, type, row) {
                        var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
                        var descargas = "";
                        var conretencion = '';
                        var archivo = '<a href="javascript:;" style="margin: 0px;" class="dropdown-item"><label title="Archivo pdf del documento a ingresar" class="archivoCompra" for="archivoCompra_' + row.id + '" style="cursor:pointer;font-weight: normal;"><input type="file" onchange="verificarArchivoCompra(this)" name="archivoCompra_' + row.id + '" id="archivoCompra_' + row.id + '" style="display:none"><i class="fas fa-file-upload" style="color:green;"></i> Subir archivo</label></a>';
                        if (row.estado == 1) {
                            accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
                        }
                        if (row.id_retencion !== null) {
                            conretencion = '<a class="dropdown-item" href="javascript:;" onclick="verRetencion(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fas fa-file-signature" style="color:#17a2b8"></i> Ver retención</a>';
                            accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
                        }
                        if (row.doc_electronico == 5) {

                            descargas = '<a href="' + $('#base_url').val() + '/api_doc_electronico?opcion=descargarXML&clave=' + row.nro_autorizacion + '" class="dropdown-item"><i class="fas fa-file-code" style="color:green;"></i> Descargar XML</a><a href="' + $('#base_url').val() + '/api_doc_electronico?opcion=generarPdf&clave=' + row.nro_autorizacion + '" class="dropdown-item"><i class="fas fa-file-pdf" style="color:red;"></i> Descargar PDF</a>';
                        }
                        if (row.rutapdf !== null) {
                            archivo = '<div class="dropdown-divider"></div><a href="javascript:;" class="dropdown-item" onclick="verArchivoCompra(\'' + row.rutapdf + '\')"><i class="fas fa-file-download" style="color:blue;"></i> Ver archivo</a><a href="javascript:;" class="dropdown-item" onclick="eliminarArchivoCompra(\'' + row.id + '\')"><i class="fas fa-trash-alt" style="color:red;"></i> Eliminar archivo</a><div class="dropdown-divider"></div>';
                        }
                        var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDocumento(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fas fa-file-invoice" style="color:#28a745"></i></i> Ver documento</a><a  target="_blank" href="' + $('#base_url').val() +'/v3/'+ $('#controlador').val()+ '/descargarComprobanteCompra?id='+ row.id + '" class="dropdown-item"><i class="fas fa-file-pdf" style="color:black;"></i> Comprobante de venta</a>' + conretencion + archivo + descargas + '<div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="verificarCompra(\'' + row.id + "','" + row.estado + "','Factura');\">" + accion + "</a></div></div>";
                        return botones;
                    },
                },
            ],
        });
        table_data_pedido = $('#table_data_pedido').DataTable({
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
            order: [[5, "desc"]],
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
                },
                {
                    extend: "pdf",
                    title: tituloExcel,
                    text: '<i class="fas fa-file-pdf" title="PDF"></i>',
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

        });
    } catch (error) {
        console.log(error);
    }
    $(".select2_bodega").select2({
        width: "element",
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/" + $("#controlador").val() + "/buscarBodega",
            type: "get",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (response) {
                return { results: response };
            },
            error(error) {
                console.log(error);
            },
            cache: true,
        },
    });
    $(".select2_proveedor").select2({
        width: "100%",
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
            url: "v3/" + $("#controlador").val() + "/buscarProveedorCombo",
            type: "get",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (response) {
                return { results: response };
            },
            error(error) {
                console.log(error);
            },
            cache: true,
        },
    });
    $(".select2_provi").select2({
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/" + "proveedores" + "/getProveedorCombo",
            type: "get",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (response) {
                return { results: response };
            },
            error(error) {
                console.log(error);
            },
            cache: true,
        },
    });
    $(".select2_bodega").select2({
        width: "100px",
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/" + $("#controlador").val() + "/buscarBodega",
            type: "get",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (response) {
                return { results: response };
            },
            error(error) {
                console.log(error);
            },
            cache: true,
        },
    });
    $('#secuencia_factura').change(function () {
        var originalString = $('#secuencia_factura').val();
        var paddedString = PadLeft(originalString, 9);
        $('#secuencia_factura').val(paddedString);
    });
    /*$("#f_autorizacion").change(function () {
        var dias = parseInt($("#termino").val());
        var fechaFacturacion = $("#f_autorizacion").val();
        if (!isNaN(dias) && fechaFacturacion) {
            var fechaObj = new Date(fechaFacturacion);
            fechaObj.setDate(fechaObj.getDate() + dias);
            var fechaFormateada = fechaObj.toISOString().split('T')[0];
            $("#f_caducidad").val(fechaFormateada);
        } else if (isNaN(dias)) {
            mensajes('info', 'Seleccione una opción válida.');
        }
    });*/
    $("#termino").change(function () {
        var data = {
            id: $("#termino").val()
        };
        var dias = ajax('v3/' + $('#controlador').val() + '/traerDias', data, 'json');
        try {
            if (dias.responseJSON != '') {
                dias = parseFloat(dias.responseJSON);
            }
        } catch (e) {
            dias = 0;
        }
        var fechaFacturacion = $("#f_autorizacion").val();
        if (!isNaN(dias) && fechaFacturacion) {
            var fechaObj = new Date(fechaFacturacion);
            fechaObj.setDate(fechaObj.getDate() + dias);
            var fechaFormateada = fechaObj.toISOString().split('T')[0];
            $("#f_caducidad").val(fechaFormateada);
        } else if (isNaN(dias)) {
            mensajes('info', 'Seleccione una opción válida.');
        }
    });
    $("#id_cuentas").select2({
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--" + $('#seleccione').val() + "--",
        minimumInputLength: 3,
        ajax: {
            url: 'v3/' + $('#controlador').val() + '/getAllCuentas',
            type: "get",
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
                console.log(error);
            },
            cache: true
        }
    });
    $("#id_configuracion").select2({
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--" + $('#seleccione').val() + "--",
        minimumInputLength: 3,
        ajax: {
            url: 'v3/' + $('#controlador').val() + '/getAllBancos',
            type: "get",
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
            }, error() {
                console.log('error');
            },
            cache: true
        }
    });
    $("#serie").on("keydown", function (event) {
        var serie = $('#serie').val();
        var keyCode = event.keyCode;
        if (keyCode != 8) {
            if (serie.length == 3) {
                $('#serie').val(serie + '-');
            }
        }
    });
    $("#agregarLinea").on("click", function () {
        var html = '<tr id="tr_' + filaCompra + '">';
        html += '<td style="width: 10%;"><input type="hidden" class="form-control" id="baseImponible_' + filaCompra + '" name="baseImponible[]" readonly /><input type="hidden" class="form-control" id="iva_porcentaje_' + filaCompra + '" name="iva_porcentaje[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_sin_' + filaCompra + '" name="iva_fila_sin[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_' + filaCompra + '" name="iva_fila[]" readonly />' + '<input type="text" class="form-control" id="codigo_' + filaCompra + '" name="codigo[]" readonly /></td>';
        html += '<td style="width: 10%;"><select class="form-control select2_producto" id="nombre_' + filaCompra + '" onchange="asignarProducto(\'' + filaCompra + '\')" name="nombre[]" style="width:100% !important;"><option value="">--Seleccione--</option></select><br/><textarea type="text" class="form-control text-right" id="detalle_' + filaCompra + '" name="detalle[]" /></textarea></td>';
        html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="cantidad_' + filaCompra + '" name="cantidad[]" value="1" onblur="calcular();" /><br/><select id="bodega_' + filaCompra + '" name="bodega[]" class="form-control  select2_bodega bodega" style="width: 100%;margin-top: 5px;" required>' + '<option value="">--Seleccione--</option>';
        $.ajax({
            url: 'v3/' + $("#controlador").val() + '/getAllBodegas',
            headers: {
                "X-CSRF-TOKEN": $("input[name=_token]").val(),
            },
            type: "post",
            data: { term: "" },
            dataType: "json",
            success: function (data) {
                for (let index = 0; index < data.length; index++) {
                    html += '<option value="' + data[index].id + '">' + data[index].nombre + '</option>';
                }
                html += '</select></td>';
                html += '<td style="width:10%;"><input type="text" class="form-control text-right" id="precio_' + filaCompra + '" name="precio[]" value="0.00" onblur="calcular();" /></td>';
                html += '<td style="width:10%;"><input type="text" class="form-control text-right" id="descuento_' + filaCompra + '" name="descuentod[]" value="0.00" onblur="getPorcent(' + filaCompra + ');" /></td>';
                html += '<td style="width:10%;"><input type="text" class="form-control text-right" id="pdescuento_' + filaCompra + '" name="pdescuento[]" value="0.00" onblur="getValue(' + filaCompra + ');" /></td>';
                html += '<td align="center" style="width:3%"><div class="icheck-primary d-inline"><input type="checkbox" id="cheMaterial_' + filaCompra + '" onchange="calcular();" name="cheMaterial[]" ><label for="cheMaterial_' + filaCompra + '"></label></div></td>';
                html += '<td style="width:10%;"><input type="text" class="form-control text-right" id="total_' + filaCompra + '" name="totald[]" value="0.00" onblur="calcular();" /></td>';
                html += '<td align="center" style="width:3%"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFilaCompra(\'' + filaCompra + "')\"></i></td>";
                html += '</tr>';
                $("#detalleCompra").before(html);
                $('.select2_bodega').select2({ width: "100%" });
                $(".select2_producto").select2({
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
                        url: "v3/" + $("#controlador").val() + "/buscarProducto",
                        type: "get",
                        dataType: "json",
                        delay: 250,
                        data: function (params) {
                            return { term: params.term };
                        },
                        processResults: function (response) {
                            return { results: response };
                        },
                        error(error) {
                            console.log(error);
                        },
                        cache: true,
                    },
                });
                filaCompra++;
                calcular();
            },
            error: function (xhr, status) {
                alert("Existió un problema");
            },
        });

    });
    $("#agregarLineaGasto").on("click", function () {
        var html = '<tr id="tr_' + filaGasto + '">';
        html += '<td style="width: 10%;"><input type="hidden" class="form-control" id="baseImponible_' + filaGasto + '" name="baseImponible[]" readonly /><input type="hidden" class="form-control" id="iva_porcentaje_' + filaGasto + '" name="iva_porcentaje[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_sin_' + filaGasto + '" name="iva_fila_sin[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_' + filaGasto + '" name="iva_fila[]" readonly />' + '<input type="text" class="form-control" id="codigo_' + filaGasto + '" name="codigo[]" readonly /></td>';
        html += '<td style="width: 10%;"><select class="form-control select2_producto" id="nombre_' + filaGasto + '" onchange="asignarProducto(\'' + filaGasto + '\')" name="nombre[]" style="width:100% !important;"><option value="">--Seleccione--</option></select><br/><textarea type="text" class="form-control text-right" id="detalle_' + filaGasto + '" name="detalle[]" /></textarea></td>';
        html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="cantidad_' + filaGasto + '" name="cantidad[]" value="1" onblur="calcular();" /></td>';
        html += '<td style="width:10%;"><input type="text" class="form-control text-right" id="precio_' + filaGasto + '" name="precio[]" value="0.00" onblur="calcular();" /></td>';
        html += '<td style="width:10%;"><input type="text" class="form-control text-right" id="descuento_' + filaGasto + '" name="descuentod[]" value="0.00" onblur="getPorcent(' + filaGasto + ');" /></td>';
        html += '<td style="width:10%;"><input type="text" class="form-control text-right" id="pdescuento_' + filaGasto + '" name="pdescuento[]" value="0.00" onblur="getValue(' + filaGasto + ');" /></td>';
        html += '<td align="center" style="width:3%"><div class="icheck-primary d-inline"><input type="checkbox" id="cheMaterial_' + filaGasto + '" onchange="checks_m(this);calcular();" name="cheMaterial[]" ><label for="cheMaterial_' + filaGasto + '"></label></div></td>';
        html += '<td align="center" style="width:3%"><input type="hidden" id="cheIvat_' + filaGasto + '"  name="cheIvat[]" value="0"/> <div class="icheck-primary d-inline"><input type="checkbox" id="cheIva_' + filaGasto + '" name="cheIva_[]"onchange="checks_i(this);calcular();actualizarHidden(this,\'' + filaGasto + '\')" value="0" ><label for="cheIva_' + filaGasto + '"></label></div></td>';
        html += '<td style="width:10%;"><input type="text" class="form-control text-right" id="total_' + filaGasto + '" name="totald[]" value="0.00" onblur="calcular();" /></td>';
        html += '<td align="center" style="width:3%"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFilaGasto(\'' + filaGasto + "')\"></i></td>";
        html += '</tr>';
        $("#detalleCompra").before(html);
        $(".select2_producto").select2({
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
                url: "v3/" + $("#controlador").val() + "/buscarGasto",
                type: "get",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return { term: params.term };
                },
                processResults: function (response) {
                    return { results: response };
                },
                error(error) {
                    console.log(error);
                },
                cache: true,
            },
        });
        filaGasto++;
        calcular();
    });
    $("#agregarProveedor").on("click", function () {
        $('#id').val('');
        $('#cheestado').attr('checked', true);
        $('#estado').val(1);
        $('#titulo_modal').html('<i class="fas fa-plus-square"></i> ' + $('#nuevo').val() + ' ' + $('#submodulo_').val());
        $('#myModal').modal('toggle');
        $("#modal_proveedores").modal('show');
        $("#modal_proveedores").modal('toggle');
    });
    $('#lblRuc').click(function (e) {
        if ($('#id').val() == '') {
            e.preventDefault();
            btnRuc.ladda('stop');
            return mensajes('warning', 'Debe ingresar el ruc del proveedor a crear...');
        }
    });
    $('#proveedorRuc').change(function (e) {
        if ($('#proveedorRuc').val().length = 0) {
            btnRuc.ladda('stop');
        } else {
            var inputFileImage = document.getElementById('proveedorRuc');
            var file = inputFileImage.files[0];
            var fileSize = $('#proveedorRuc')[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                btnRuc.ladda('stop');
                $('#proveedorRuc').val('');
                mensajes('error', 'Archivo muy grande o tiene una resolucion muy alta');
            } else {
                var fd = new FormData();
                fd.append('proveedorRuc', file);
                fd.append('tipo_archivo', 'proveedorRuc');
                fd.append('id_proveedor', $('#id').val());
                $.ajax({
                    url: "v3/utilidades/guardarArchivo",
                    headers: {
                        'X-CSRF-TOKEN': $('#token').val(),
                    },
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'post',
                    success: function (data) {
                        var imagen = data.split('|');
                        if (imagen[0] == 'ok') {
                            btnRuc.ladda('stop');
                            $('#proveedorRuc').val('');
                            $('#lblRuc').html('Ruc: <a href="' + $('#base_url').val() + '/v3/utilidades/viewPdf?id=' + $('#id').val() + '&t=proveedorRuc" target="new">ver/descargar</a>');
                        } else {
                            btnRuc.ladda('stop');
                            alert(imagen[1]);
                            $('#proveedorRuc').val('');
                        }
                    }
                });
            }
        }
    });
    $('#lblCedula').click(function (e) {
        if ($('#id').val() == '') {
            e.preventDefault();
            btnCed.ladda('stop');
            return mensajes('warning', 'Debe ingresar el ruc del proveedor a crear...');
        }
    });
    $('#proveedorCedula').change(function (e) {
        if ($('#proveedorCedula').val().length = 0) {
            btnCed.ladda('stop');
        } else {
            var inputFileImage = document.getElementById('proveedorCedula');
            var file = inputFileImage.files[0];
            var fileSize = $('#proveedorCedula')[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                btnCed.ladda('stop');
                $('#proveedorCedula').val('');
                mensajes('error', 'Archivo muy grande o tiene una resolucion muy alta');
            } else {
                var fd = new FormData();
                fd.append('proveedorCedula', file);
                fd.append('tipo_archivo', 'proveedorCedula');
                fd.append('id_proveedor', $('#id').val());
                $.ajax({
                    url: "v3/utilidades/guardarArchivo",
                    headers: {
                        'X-CSRF-TOKEN': $('#token').val(),
                    },
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'post',
                    success: function (data) {
                        var imagen = data.split('|');
                        if (imagen[0] == 'ok') {
                            btnCed.ladda('stop');
                            $('#proveedorCedula').val('');
                            $('#lblCedula').html('Cedula: <a href="' + $('#base_url').val() + '/v3/utilidades/viewPdf?id=' + $('#id').val() + '&t=proveedorCedula" target="new">ver/descargar</a>');
                        } else {
                            btnCed.ladda('stop');
                            $('#proveedorCedula').val('');
                        }
                    }
                });
            }
        }
    });
    $('#lblVotacion').click(function (e) {
        if ($('#id').val() == '') {
            e.preventDefault();
            btnCer.ladda('stop');
            return mensajes('warning', 'Debe ingresar el ruc del proveedor a crear...');
        }
    });
    $('#proveedorVotacion').change(function (e) {
        if ($('#proveedorVotacion').val().length = 0) {
            btnCer.ladda('stop');
        } else {
            var inputFileImage = document.getElementById('proveedorVotacion');
            var file = inputFileImage.files[0];
            var fileSize = $('#proveedorVotacion')[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                btnCer.ladda('stop');
                $('#proveedorVotacion').val('');
                alert('Error: imagen muy grande o tiene una resolucion muy alta');
                mensajes('error', 'Archivo muy grande o tiene una resolucion muy alta');
            } else {
                var fd = new FormData();
                fd.append('proveedorVotacion', file);
                fd.append('tipo_archivo', 'proveedorVotacion');
                fd.append('id_proveedor', $('#id').val());
                $.ajax({
                    url: "v3/utilidades/guardarArchivo",
                    headers: {
                        'X-CSRF-TOKEN': $('#token').val(),
                    },
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'post',
                    success: function (data) {
                        var imagen = data.split('|');
                        if (imagen[0] == 'ok') {
                            btnCer.ladda('stop');
                            $('#proveedorVotacion').val('');
                            $('#lblVotacion').html('Cartificado de votación: <a href="' + $('#base_url').val() + '/v3/utilidades/viewPdf?id=' + $('#id').val() + '&t=proveedorVotacion" target="new">ver/descargar</a>');
                        } else {
                            btnCer.ladda('stop');
                            $('#proveedorVotacion').val('');
                        }
                    }
                });
            }
        }
    });
    $("#btnguardarCompra").click(function () {
        if ($("#proveedor").val() == "") {
            return mensajes("warning", "Seleccione un proveedor para continuar...");
        } else if ($("#fecha").val() == "") {
            return mensajes("warning", "Seleccione la fecha de emisión para continuar...");
        } else if ($("#sucursal").val() == "") {
            return mensajes("warning", "Seleccione el establecimiento para continuar...");
        } else if ($("#p_emision").val() == "") {
            return mensajes("warning", "Seleccione la emisión para continuar...");
        } else if ($("#observacion").val() == "") {
            return mensajes("warning", "Debe ingresar el concepto de la compra para continuar...");
        } else if ($("#termino").val() == "") {
            return mensajes("warning", "Seleccione el termino de la compra para continuar...");
        } else if ($("#f_caducidad").val() == "") {
            return mensajes("warning", "Ingrese la fecha que cadula la compra para continuar...");
        } else if ($('#tipo_comprobante').val() == '') {
            return mensajes("warning", "Seleccione el tipo de comprobante...");
        } else if (filaCompra <= 0 && filaGasto <= 0) {
            return mensajes("warning", "Agregue productos para continuar...");
        } else {
            let validacionProducto = false;
            let validacionCantidad = false;

            $("select[name='producto[]']").each((i, v) => {
                if (v.value == "") {
                    validacionProducto = true;
                }
                if ($("#cantidad_" + i).val() === "") {
                    validacionCantidad = true;
                }
            });
            if (validacionProducto) {
                return mensajes("warning", "Debe ingresar un producto para continuar...");
            }
            if (validacionCantidad) {
                return mensajes("warning", "Debe ingresar la cantidad del producto para continuar...");
            }

            mensajes("question", $("#pregunta").val(), "frm_", "action");
        }
    });
    $("#btnGuardarIngresoActivos").on("click", function () {
        let tbody = document.getElementById('agregar_cuentas');
        let filas = tbody.rows.length;
        if (document.getElementById("id_proveedor").value == '') {
            return mensajes("warning", "Ingrese el proveedor");
        }
        if (document.getElementById("divisas").value == '') {
            return mensajes("warning", "Ingrese la divisa");
        }
        if (document.getElementById("termino").value == '') {
            return mensajes("warning", "Ingrese el termino");
        }
        if (document.getElementById("cred_tributario").value == '') {
            return mensajes("warning", "Ingrese el crédito tributario");
        }
        if (document.getElementById("nro_autorizacion").value == '') {
            return mensajes("warning", "Ingrese el número autorización");
        }
        if (document.getElementById("secuencia").value == '') {
            return mensajes("warning", "Ingrese la secuencia");
        }
        if (document.getElementById("tipo_comprobante").value == '') {
            return mensajes("warning", "Ingrese el tipo comprobante");
        }
        if (document.getElementById("concepto").value == '') {
            return mensajes("warning", "Ingrese el concepto");
        }
        if (filas < 0) {
            return mensajes("error", "Ingrese los detalles");
        }
        console.log($('#nro_autorizacion').val().length);
        if ($('#nro_autorizacion').val() != '') {
            if ($('#nro_autorizacion').val().length != 10 && $('#nro_autorizacion').val().length != 49) {
                return mensajes('warning', 'Por favor, El número de autorización debe contener 10 o 49 dígitos');
            }
        }
        if (!validarFechasPeriodo()) {
            return mensajes("warning", "La fecha del periodo con la fecha de facturacion no coinciden");
        }
        mensajes('question', $("#pregunta").val(), 'frm_', 'action', '', $('#base_url').val() + 'v3/compras/addActivos', '');
    });
    $('#btnGuardarProveedor').click(function () {
        if ($('#tipo').val() == '') {
            return mensajes('warning', $('#alertTipoIdentificacion').val() + '...');
        } else if ($('#id').val() == '') {
            return mensajes('warning', $('#alertNumeroIdentificacion').val() + '...');
        } else if ($('#razonsocial').val() == '') {
            return mensajes('warning', $('#alertRazonSoc').val() + '...');
        } else if ($('#nombrecomercial').val() == '') {
            return mensajes('warning', $('#alertNomComercial').val() + '...');
        } else if ($('#email').val() == '') {
            return mensajes('warning', $('#alertCorreo').val() + '...');
        } else if ($('#telefono1').val() == '') {
            return mensajes('warning', $('#alertConvencional').val() + '...');
        } else if ($('#telefono2').val() == '') {
            return mensajes('warning', $('#alertCelular').val() + '...');
        } else if ($('#direccion').val() == '') {
            return mensajes('warning', $('#alertDireccion').val() + '...');
        } else if ($('#id_ciudad').val() == '') {
            return mensajes('warning', $('#alertCiudad').val() + '...');
        } else {
            if ($('#tipo').val() == 4 && $('#id').val().length !== 13) {
                return mensajes('warning', $('#alertLongIdentificacion').val() + '...');
            }
            if ($('#tipo').val() == 5 && $('#id').val().length !== 10) {
                return mensajes('warning', $('#alertLongIdentificacion').val() + '...');
            }
            var seleccionado = $('#id_ciudad option:selected').text();
            $('#ciudad').val(seleccionado);
            mensajes("question", $("#pregunta").val(), "provee_frm", "actionSaveProveedor");
        }
    });
    $("#pedido").on("click", function () {
        $('#titulo_modal_ped').html('<i class="fas fa-plus-square"></i> ' + $('#listaPedido').val());
        $("#table_data_pedido").dataTable().fnClearTable();
        $("#table_data_pedido").dataTable().fnDestroy();
        $("#table_data_pedido").DataTable({
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
            response: true,
            ajax: {
                url: 'v3/' + $("#controlador").val() + '/modalPedido',
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
            },
            columns: [
                {
                    className: "text-center",
                    data: "created_at",
                },
                {
                    data: "nombrecomercial",
                },
                {
                    data: "pedido",
                },
                {
                    data: "factura",
                },
                {
                    data: "pedido",
                    render: function (data, type, row) {
                        return row.nombre1 + ' ' + row.apellido1;
                    }
                },
                {
                    data: "pedido",
                    render: function (data, type, row) {
                        return 'Conglomerada';
                    }
                },
                {
                    className: "text-right",
                    data: "pedido",
                    render: function (data, type, row) {
                        return '<button type="button" class="btn btn-primary" onclick="addPedido(\'' + row.pedido + '\')"><i class="fas fa-plus-circle" style="color:white;"></i> Agregar</button>';
                    }
                }
            ],
        });
        $('#modal_pedidos').modal('toggle');
    });
    $('#archivoXMLCompra').change(function () {
        var inputFileImage = document.getElementById('archivoXMLCompra');
        var file = inputFileImage.files[0];
        if (file.type === 'text/xml') {
            var fileSize = $('#archivoXMLCompra')[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                $('#archivoXMLCompra').val('');
                mensajes('error', 'Archivo muy grande o tiene una resolucion muy alta');
                btnXMLCompra.ladda('stop');
            } else {
                var fd = new FormData();
                fd.append('archivoXMLCompra', file);
                fd.append('tipo_archivo', 'archivoXMLCompra');
                $.ajax({
                    url: "v3/utilidades/leerArchivoXMLCompra",
                    headers: {
                        'X-CSRF-TOKEN': $('#token').val(),
                    },
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        $('#archivoXMLCompra').val('');
                        btnXMLCompra.ladda('stop');
                        var fechaString = json.infoFactura.fechaEmision;
                        var partesFecha = fechaString.split('/');
                        var fechaISO = partesFecha[2] + '-' + partesFecha[1] + '-' + partesFecha[0];
                        $('#proveedor').html('<option value="' + json.infoTributaria.ruc + '" selected>' + json.infoTributaria.ruc + ' | ' + json.infoTributaria.razonSocial + '</option>');
                        var dat = {
                            proveedor: json.infoTributaria.ruc
                        };
                        var verificarproveedor = ajax('v3/' + $('#controlador').val() + '/verificarproveedor', dat, 'json');
                        if (verificarproveedor.responseJSON !== null) {
                            verificarproveedor = verificarproveedor.responseJSON;
                            verificarErrores(verificarproveedor);
                        }
                        $('#f_autorizacion').val(fechaISO);
                        $('#tipo_comprobante').val(json.infoTributaria.codDoc).trigger('change');
                        $('#serie').val(json.infoTributaria.estab + '-' + json.infoTributaria.ptoEmi);
                        $('#secuencia_factura').val(json.infoTributaria.secuencial);
                        $('#autorizacion').val(json.infoTributaria.claveAcceso);
                        var html = '';
                        var ivaProducto = 0;
                        try {
                            $.each(json.detalles.detalle, function (i, item) {
                                ivaProducto = 0;
                                if (item.impuestos.impuesto.tarifa > 0) {
                                    ivaProducto = 1;
                                }
                                html += '<tr id="tr_' + filaGasto + '">';
                                html += '<td style="width: 10%;"><input type="hidden" class="form-control" id="baseImponible_' + filaGasto + '" name="baseImponible[]" readonly /><input type="hidden" class="form-control" id="iva_porcentaje_' + filaGasto + '" name="iva_porcentaje[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_sin_' + filaGasto + '" name="iva_fila_sin[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_' + filaGasto + '" name="iva_fila[]" readonly />' + '<input type="text" class="form-control" id="codigo_' + filaGasto + '" name="codigo[]" value="' + item.codigoPrincipal + ' ' + item.codigoAuxiliar + '" readonly /></td>';
                                html += '<td style="width: 12.5%;"><select class="form-control select2_producto" id="nombre_' + filaGasto + '" onchange="asignarProducto(\'' + filaGasto + '\')" name="nombre[]" width="100%">' + '<option value="">--Seleccione--</option><option value="' + item.codigoPrincipal + ' ' + item.codigoAuxiliar + '||0|0" selected>' + item.codigoPrincipal + ' ' + item.codigoAuxiliar + ' | ' + item.descripcion + '</option></select><br/><textarea type="text" class="form-control text-right" id="detalle_' + filaGasto + '" name="detalle[]" value="1" /></textarea></td>';
                                html += '<td style="width: 12.50%;"><input type="text" class="form-control text-right" id="cantidad_' + filaGasto + '" name="cantidad[]" value="' + item.cantidad + '" onblur="calcular();" /></td>';
                                html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="precio_' + filaGasto + '" name="precio[]" value="' + item.precioUnitario + '" onblur="calcular();" /></td>';
                                html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="descuento_' + filaGasto + '" name="descuentod[]" value="' + item.descuento + '" onblur="getPorcent(' + filaGasto + ');" /></td>';
                                html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="pdescuento_' + filaGasto + '" name="pdescuento[]" value="0.00" onblur="getValue(' + filaGasto + ');" /></td>';
                                html += '<td align="center" style="width:3%"><div class="icheck-primary d-inline"><input type="checkbox" id="cheMaterial_' + filaGasto + '" onchange="calcular();" name="cheMaterial[]" ><label for="cheMaterial_' + filaGasto + '"></label></div></td>';
                                html += '<td align="center" style="width:3%"><div class="icheck-primary d-inline"><label for="cheIva_' + filaGasto + '"><input type="checkbox" id="cheIva_' + filaGasto + '" onchange="calcular();" name="cheIva[]" value="1" ><label for="cheIva_' + filaGasto + '"></label></div></td>';
                                html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="total_' + filaGasto + '" name="totald[]" value="0.00" onblur="calcular();" /></td>';
                                html += '<td style="width: 5%;" align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFilaGasto(\'' + filaGasto + "')\"></i></td>";
                                html += '</tr>';
                                filaGasto++;
                            });
                        } catch (e) {
                            ivaProducto = 0;
                            if (json.detalles.detalle.impuestos.impuesto.tarifa > 0) {
                                ivaProducto = 1;
                            }
                            html += '<tr id="tr_' + filaGasto + '">';
                            html += '<td style="width: 10%;"><input type="hidden" class="form-control" id="baseImponible_' + filaGasto + '" name="baseImponible[]" readonly /><input type="hidden" class="form-control" id="iva_porcentaje_' + filaGasto + '" name="iva_porcentaje[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_sin_' + filaGasto + '" name="iva_fila_sin[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_' + filaGasto + '" name="iva_fila[]" readonly />' + '<input type="text" class="form-control" id="codigo_' + filaGasto + '" name="codigo[]" value="' + json.detalles.detalle.codigoPrincipal + ' ' + json.detalles.detalle.codigoAuxiliar + '" readonly /></td>';
                            html += '<td style="width: 12.5%;"><select class="form-control select2_producto" id="nombre_' + filaGasto + '" onchange="asignarProducto(\'' + filaGasto + '\')" name="nombre[]" width="100%">' + '<option value="">--Seleccione--</option><option value="' + json.detalles.detalle.codigoPrincipal + ' ' + json.detalles.detalle.codigoAuxiliar + '||0|0" selected>' + json.detalles.detalle.codigoPrincipal + ' ' + json.detalles.detalle.codigoAuxiliar + ' | ' + json.detalles.detalle.descripcion + '</option></select><br/><textarea type="text" class="form-control text-right" id="detalle_' + filaGasto + '" name="detalle[]" value="1" /></textarea></td>';
                            html += '<td style="width: 12.50%;"><input type="text" class="form-control text-right" id="cantidad_' + filaGasto + '" name="cantidad[]" value="' + json.detalles.detalle.cantidad + '" onblur="calcular();" /></td>';
                            html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="precio_' + filaGasto + '" name="precio[]" value="' + json.detalles.detalle.precioUnitario + '" onblur="calcular();" /></td>';
                            html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="descuento_' + filaGasto + '" name="descuentod[]" value="' + json.detalles.detalle.descuento + '" onblur="getPorcent(' + filaGasto + ');" /></td>';
                            html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="pdescuento_' + filaGasto + '" name="pdescuento[]" value="0.00" onblur="getValue(' + filaGasto + ');" /></td>';
                            html += '<td align="center" style="width:3%"><div class="icheck-primary d-inline"><input type="checkbox" id="cheMaterial_' + filaGasto + '" onchange="calcular();" name="cheMaterial[]" ><label for="cheMaterial_' + filaGasto + '"></label></div></td>';
                            html += '<td align="center" style="width:3%"><div class="icheck-primary d-inline"><input type="checkbox" id="cheIva_' + filaGasto + '" onchange="calcular();" value="1" name="cheIva[]" ><label for="cheIva_' + filaGasto + '"></label></div></td>';
                            html += '<td style="width: 10%;"><input type="text" class="form-control text-right" id="total_' + filaGasto + '" name="totald[]" value="0.00" onblur="calcular();" /></td>';
                            html += '<td style="width: 5%;" align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFilaGasto(\'' + filaGasto + "')\"></i></td>";
                            html += '</tr>';
                            filaGasto++;
                        }
                        $('#detalleCompra').html(html);
                        $(".select2_producto").select2({
                            width: "element",
                            noResults: function () {
                                return "No hay resultado";
                            },
                            searching: function () {
                                return "Buscando..";
                            },
                            placeholder: "--Seleccione--",
                            minimumInputLength: 3,
                            ajax: {
                                url: "v3/" + $("#controlador").val() + "/buscarGasto",
                                type: "get",
                                dataType: "json",
                                delay: 250,
                                data: function (params) {
                                    return { term: params.term };
                                },
                                processResults: function (response) {
                                    return { results: response };
                                },
                                error(error) {
                                    console.log(error);
                                },
                                cache: true,
                            },
                        });
                        calcular();
                    }
                });
            }
        } else {
            btnXMLCompra.ladda('stop');
            return mensajes('info', 'Solo se permiten archivos XML...');
        }
    });
    $("#sucursal_empresa").on("change", function () {
        let sucursal = $(this).val();
        $.ajax({
            type: "post",
            url: "v3/" + $("#controlador").val() + "/obtenerPuntoEmision",
            headers: {
                "X-CSRF-TOKEN": $("input[name=_token]").val(),
            },
            datatype: "json",
            data: {
                id: sucursal,
            },
            success: function (data) {
                if (sucursal != 0) {
                    $("#punto_emision").empty();

                    $.each(data, function (key, registro) {
                        var select = "";
                        if (registro.codigo_caja == "100") select = "selected";
                        $("#punto_emision").append("<option value=" + registro.id + " " + select + ">" + registro.codigo_sucursal + "-" + registro.codigo_caja + "</option>"
                        );
                    });
                } else {
                    $("#punto_emision").empty();
                }
            },
            error: function (data) { },
        });
    });
});
function crearGasto() {
    var id = document.getElementById('contador_items').value;
    let fila = `
        <tr >
            <td style="width:3%;text-align:center">
               
            </td>
            <td>
                <input type="hidden" name="gasto" value="GASTO"/>
                <input id="codigo${id}" class="form-control" type="text" name="codigo_gasto[]" value="" readonly>
            </td>
            <td>
                <select id="id_plan${id}" name="id_plan[]" class="form-control js-data_cuenta validar" style="width: 100%;">
                    <option value=""> Seleccione...</option>
                </select>
                <textarea  rows="3" name="observacion_gasto[]" id="observacion${id}" class="form-control px-1" style="margin-top:3px;" placeholder="Observacion"></textarea>
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control cant validar text-right" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad_gasto[]" >

            </td>
            <td>
                <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id});" id="precio${id}" value="0.00" type="text" class="pneto form-control text-right" name="precio_gasto[]" placeholder="0.00">
            </td>

            <td>
                <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0.00" name="descpor_gasto[]" >
                <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0.00" name="maxdesc_gasto[]" >
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="desc${id}" class="form-control text-right desc" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0.00" name="desc_gasto[]" >
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text"  onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto_gasto[]" >
            </td>
            <td style="width:3%;text-align:center">
                <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox" name="valoriva_gasto[]" id="valoriva${id}" value="1">
                <input type="hidden" name="val_iva_gasto[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" >
                <input type="hidden" name="total_valor_gasto[]" id="total_valor${id}" class="form-control px-1 text-right total_valor" value="0.00">
                <input class="check_iva" id="check_iva${id}" type="hidden" name="check_iva[]" value="0">

            </td>
            <td>
                <button type="button" class="btn btn-danger btn-gray delete">
                   <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </td>
        </tr>
    `;

    $('#agregar_cuentas').append(fila);
    callSelect2Cuenta();
    id++;
    document.getElementById('contador_items').value = id;
}
function crearTransporte() {
    var id = document.getElementById('contador_items').value;
    let fila = `
        <tr >
             <td style="width:3%;text-align:center">
            </td>
            <td>
                <input type="hidden" name="transporte" value="TRANS"/>
                <input id="codigo${id}" class="form-control validar" type="text"  name="codigo_trans[]" value="TRANS" readonly>
            </td>
            <td>
                <input id="descrip_prod${id}" class="form-control validar" type="text" name="descrip_prod_trans[]" value="TRANS | TRANSPORTE" readonly>
                <textarea  rows="3" name="observacion_trans[]" id="observacion${id}" class="form-control px-1 " placeholder="Observacion"></textarea>
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control cant validar text-right cneto" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="1" name="cantidad_trans[]" >

            </td>
            <td>
                <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id});" id="precio${id}" value="0.00" type="text" class="pneto form-control text-right" name="precio_trans[]"  placeholder="0.00">
            </td>

            <td>
                <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0.0" name="descpor_trans[]" >
                <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0.0" name="maxdesc_trans[]" >
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="desc${id}" class="form-control text-right desc" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0.0" name="desc_trans[]" >
            </td>
            <td>
                <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto" type="text" onkeypress="return isNumberKey(event)" value="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precioneto_trans[]" >
            </td>
            <td style="width:3%;text-align:center">
                <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox"  name="valoriva_trans[]" id="valoriva${id}" value="1">
                <input type="hidden" name="val_iva_trans[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" >
                <input type="hidden" name="total_valor_trans[]" id="total_valor${id}" class="form-control px-1 text-right total_valor" value="0.00">
                <input class="check_iva" id="check_iva${id}" type="hidden" name="check_iva[]" value="0">

            </td>

            <td>
                 <button type="button" class="btn btn-danger btn-gray delete">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </td>
        </tr>
    `

    $('#agregar_cuentas').append(fila);
    $('.select2_cuentas').select2({
        tags: false
    });

    id++;
    document.getElementById('contador_items').value = id;
}
function callSelect2Cuenta() {
    $(".js-data_cuenta")
        .select2({
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: "v3/" + $("#controlador").val() + "/buscarPlandeCuenta",
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    console.log(data);
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.nombre,
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
        });
}
function descuento(id) {
    let descpor = document.getElementById("descpor" + id);
    let cantidad = document.getElementById("cantidad" + id).value;
    let precio = document.getElementById("precio" + id).value;
    let desc = document.getElementById("desc" + id).value;
    let preXcantidad = (precio * cantidad);
    let preXcantidadV = 0;
    if (preXcantidad == 0) {
        preXcantidadV = 1;
    } else {
        preXcantidadV = preXcantidad;
    }
    let descuento = (desc * 100) / preXcantidadV;
    descpor.value = descuento.toFixed(4);
    totalProducto(id);
}
function select_iva(id) {
    let check = document.getElementById("valoriva" + id);
    let check_af = document.getElementById("check_iva" + id);
    if (check.checked) {
        check_af.value = 1;
    } else {
        check_af.value = 0;
    }
    totalProducto(id);
}
function sumarDias(fecha, dias) {
    fecha.setDate(fecha.getDate() + dias + 1);
    return fecha;
}
function fecha_vencimiento(e) {
    const d = new Date(document.getElementById("fecha_compra").value);
    let f_caducidad = document.getElementById("fecha_caduca");
    let day = e.options[e.selectedIndex].text;
    const dayArray = day.split(" ");
    let fechaNew = "";
    if (parseInt(dayArray[0]) >= 0) {
        fechaNew = sumarDias(d, +dayArray[0])
    } else if (dayArray[0].toUpperCase() == "CONTADO") {
        fechaNew = sumarDias(d, +0)
    } else {
        f_caducidad.readOnly = false;
        return;
    }
    var fechaImp = fechaNew.getFullYear() + '-' + ('0' + (fechaNew.getMonth() + 1)).slice(-2) + '-' + ('0' + fechaNew.getDate()).slice(-2);
    f_caducidad.value = fechaImp;
    f_caducidad.readOnly = true;
    return;
}
function validarFechasPeriodo() {
    let periodo_mes = document.getElementById("periodo_mes").value;
    let periodo_anio = document.getElementById("periodo_anio").value;
    let f_autorizacion = document.getElementById("fecha_compra").value;
    const fechArray = f_autorizacion.split("-");
    let fechAuto = `${fechArray[0]}-${fechArray[1]}`;
    let fechPeriodo = `${periodo_anio}-${periodo_mes}`;
    console.log(fechAuto, fechPeriodo, fechArray[0]);
    if (fechAuto == fechPeriodo) {
        return true;
    } else {
        return false;
    }
}
function eliminarArchivoCompra(id) {
    var data = {
        id: id
    };
    var json = ajax("v3/utilidades/eliminarArchivoCompra", data, 'json');
    if (json.responseJSON != '') {
        json = json.responseJSON;
        mensajes(json.status, json.message);
        $('.tablaDatos').DataTable().ajax.reload();
    }
}
function verificarArchivoCompra(archivo) {
    try {
        cancel = document.getElementsByClassName('archivoCompra');
        if (archivo.files[0] <= 0 || archivo.files[0] === null || archivo.files[0] === "" || archivo.files[0] === undefined || archivo.files[0] === "undefined" || archivo.files[0] === "null") {
            if (cancel.value == "" || cancel.value == null) {
            }
        }
        else {
            var inputFileImage = document.getElementById(archivo.id);
            var file = inputFileImage.files[0];
            if (file.type === 'application/pdf') {
                var fd = new FormData();
                fd.append(archivo.id, file);
                fd.append('tipo_archivo', 'archivoCompra');
                fd.append('id', archivo.id);
                $.ajax({
                    url: "v3/utilidades/guardarArchivoCompra",
                    headers: {
                        'X-CSRF-TOKEN': $('#token').val(),
                    },
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data) {
                        mensajes('info', 'Archivo subido correctamente');
                        $('.tablaDatos').DataTable().ajax.reload();
                    }
                });
            }
            else {
                mensajes('info', 'Solo se permiten archivos PDF...');
            }
        }
    } catch (error) {
        console.log(error);
    }
}
function verArchivoActivo(ruta) {
    $('#titulo_modal_archivo').html('');
    $('#visualizadorPdfActivo').html('');
    $('#titulo_modal_archivo').html('Visualizador de archivos');
    $('#visualizadorPdfActivo').html('<embed src="' + $('#rutadescarga').val() + '/' + ruta + '" type="application/pdf" width="100%" height="800px" />');
    $('#myModalArchivo').modal('toggle');
}

function verArchivoCompra(ruta) {
    $('#titulo_modal_archivo').html('');
    $('#visualizadorPdfCompras').html('');
    $('#titulo_modal_archivo').html('Visualizador de archivos ');
    var data = {
        ruta: ruta
    };
    let path = $("#rutaImagen").val();
    $('#visualizadorPdfCompras').html('<embed src="' + $('#pilozopendejo').val() + '/' + ruta + '" type="application/pdf" width="100%" height="800px" />');
    $('#myModalArchivo').modal('toggle');
}
function verDocumento(id, num) {
    $("#titulo_modal_compra").html('<i class="fas fa-edit"></i> No. documento: ' + num);
    $.ajax({
        url: "v3/" + $("#controlador").val() + "/getCompra",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
            id: id,
        },
        dataType: "html",
        success: function (html) {
            $("#contenidoHtml").html(html);
            $('#myModalDocumento').modal("toggle");
        },
        error(error) {
            console.log(error);
            //window.open("v3/admin", "_self");
        },
    });
}
function verRetencion(id, num) {
    $("#titulo_modal_compra").html('<i class="fas fa-edit"></i> No. documento: ' + num);
    var data = {
        id: id,
        todo: 'todo'
    };
    var html = ajax('v3/proveedores/getRetencionPrint', data, 'html');
    $('#contenidoHtmlRetencion').html(html.responseText);
    $('#myModalRetencion').modal("toggle");
}
function agregarCompra() {
    window.open("v3/" + $("#controlador").val() + "/addcompra", "_self");
}
function obtener_caja() {
    $.ajax({
        type: "post",
        url: "v3/" + $("#controlador").val() + "/obtenerPuntoEmision",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        datatype: "json",
        data: {
            id: $("#sucursal").val(),
        },
        success: function (data) {
            if ($("#sucursal").val() != "") {
                $("#punto_emision").empty();
                $("#punto_emision").append('<option value="">--Seleccione--</option>');
                $.each(data, function (key, registro) {
                    $("#punto_emision").append(
                        "<option value=" +
                        registro.id +
                        ">" +
                        registro.codigo_sucursal +
                        "-" +
                        registro.codigo_caja +
                        "</option>"
                    );
                });
            } else {
                $("#punto_emision").empty();
                $("#punto_emision").append('<option value="">--Seleccione--</option>');
            }
        },
        error: function (data) {
            console.log(data);
        },
    });
}
function str_pad(input, length, padChar, padType) {
    var result = String(input);
    if (typeof input !== undefined) {
        if (typeof padChar === undefined) {
            padChar = '0';
        }
        if (typeof padType === undefined) {
            padType = 'L';
        }
        else {
            padType = padType.toUpperCase();
        }
        if (padType === 'L') {
            while (result.length < length) {
                result = padChar + result;
            }
        } else {
            while (result.length < length) {
                result = result + padChar;
            }
        }
    }
    return result;
}
function eliminarFila(fila) {
    $("#tr_" + fila).remove();
    fila--;
    calcular();
}
function eliminarFilaCompra(fila) {
    $("#tr_" + fila).remove();
    filaCompra--;
    calcular();
}
function eliminarFilaGasto(fila) {
    $("#tr_" + fila).remove();
    filaGasto--;
    calcular();
}
function asignarProducto(id) {
    var datos = $("#nombre_" + id).val().split("|");
    $("#codigo_" + id).val(datos[1]);
    $("#cantidad_" + id).focus();
    calcular();
}
function actualizarHidden(e, filaGasto_) {

    let idF = '';

    if (filaGasto_ == undefined) {
        let indice = e.id.indexOf("_");
        idF = e.id.substring(indice + 1);
    }else{
        idF = filaGasto_;
    }
    if ($("#" + e.id).is(":checked")) {
        $("#" + e.id).val(1);
        $("#cheIvat_" + idF).val(1);
        $("#cheIva_" + idF).val(1);

    } else {
        $("#" + e.id).val(0);  
        $("#cheIva_" + idF).val(0);
        $("#cheIvat_" + idF).val(0);
    }
}
function checks_m(e) {
    var idIva = e.id.replaceAll('cheMaterial_', '');

    if ($('#cheMaterial_' + idIva).is(':checked')) {
        $('#cheIva_' + idIva).prop('checked', true);
    } else {
        $('#cheIva_' + idIva).prop('checked', false);
        $('#cheMaterial_' + idIva).prop('checked', false);

    }
    actualizarHidden(e);
}
function checks_i(e) {
    var idIva = e.id.replaceAll('cheIva_', '');

    if (!$('#cheIva_' + idIva).is(':checked')) {

        $('#cheIva_' + idIva).prop('checked', false);
        $('#cheMaterial_' + idIva).prop('checked', false);
    }

    actualizarHidden(e);
}
function calcular() {
    var descuento = 0;
    var subtotal0 = 0;
    var subtotal12 = 0;
    var iva = 0;
    let baseImponibleTotal = 0;
    let baseImponible = 0;
    let ivasin = 0;
    let sumaivasin = 0;
    let count_mc = 0;
    let iva_mc = (5) / 100;
    try {
        let cantidad = 0;
        let precio = 0;
        $("input[name='cantidad[]']").each((i, v) => {
            cantidad += Number(v.value);
        });
        $("input[name='precio[]']").each((i, v) => {
            if ($('#cheMaterial_' + i).is(':checked')) {
                count_mc++;
            }
            let precioUni = Number(v.value) * Number($("#cantidad_" + i).val());
            let is_iva = $("#nombre_" + i).val().split("|");
            descuento += Number($("#descuento_" + i).val());
            if (is_iva.length > 3) {
                // con iva de base
                if (is_iva[3] == 1) {
                    ivasin = Number(v.value) * Number($("#cantidad_" + i).val());
                    sumaivasin += ivasin;
                    subtotal12 += precioUni;;
                    if ($('#cheMaterial_' + i).is(':checked')) {
                        $("#iva_fila_" + i).val(parseFloat(precioUni) * iva_mc);
                        $("#iva_fila_sin_" + i).val(parseFloat(ivasin) * iva_mc);
                        $("#iva_porcentaje_" + i).val(iva_mc);
                    } else {
                        $("#iva_fila_" + i).val(parseFloat(precioUni) * $('#impuestoIva').val());
                        $("#iva_fila_sin_" + i).val(parseFloat(ivasin) * $('#impuestoIva').val());
                        $("#iva_porcentaje_" + i).val($('#impuestoIva').val());
                    }
                    $("#baseImponible_" + i).val(Number(v.value) * Number($("#cantidad_" + i).val()));
                } else if ($('#cheIva_' + i).is(':checked')) {
                    ivasin = Number(v.value) * Number($("#cantidad_" + i).val());
                    sumaivasin += ivasin;
                    subtotal12 += precioUni;
                    if ($('#cheMaterial_' + i).is(':checked')) {
                        $("#iva_fila_" + i).val(parseFloat(precioUni) * iva_mc);
                        $("#iva_fila_sin_" + i).val(parseFloat(ivasin) * iva_mc);
                        $("#iva_porcentaje_" + i).val(iva_mc);
                    } else {
                        $("#iva_fila_" + i).val(parseFloat(precioUni) * $('#impuestoIva').val());
                        $("#iva_fila_sin_" + i).val(parseFloat(ivasin) * $('#impuestoIva').val());
                        $("#iva_porcentaje_" + i).val($('#impuestoIva').val());
                    }
                    $("#baseImponible_" + i).val(Number(v.value) * Number($("#cantidad_" + i).val()));
                } else if ($('#cheMaterial_' + i).is(':checked')) {
                    ivasin = Number(v.value) * Number($("#cantidad_" + i).val());
                    sumaivasin += ivasin;
                    subtotal12 += precioUni;
                    if ($('#cheMaterial_' + i).is(':checked')) {
                        $("#iva_fila_" + i).val(parseFloat(precioUni) * iva_mc);
                        $("#iva_fila_sin_" + i).val(parseFloat(ivasin) * iva_mc);
                        $("#iva_porcentaje_" + i).val(iva_mc);
                    } else {
                        $("#iva_fila_" + i).val(parseFloat(precioUni) * $('#impuestoIva').val());
                        $("#iva_fila_sin_" + i).val(parseFloat(ivasin) * $('#impuestoIva').val());
                        $("#iva_porcentaje_" + i).val($('#impuestoIva').val());
                    }
                    $("#baseImponible_" + i).val(Number(v.value) * Number($("#cantidad_" + i).val()));

                } else {
                    subtotal0 += precioUni;
                    $("#iva_porcentaje_" + i).val(0.00);
                }


                // console.log($('#cheMaterial_' + i).is(':checked'),$("#cheMaterial_" + i).is(":checked"));
                // if ($("#cheMaterial_" + i).is(":checked") && ($("#cheIva_" + i).is(":checked")== false)) {
                //     $("#cheIva_" + i).prop("checked", true);

                // } else if ($("#cheMaterial_" + i).is(":checked") ) {
                //     $("#cheIva_" + i).prop("checked", false);
                //     $("#cheMaterial_" + i).prop("checked", false);
                // }

            }
            baseImponible += Number(v.value) * Number($("#cantidad_" + i).val());
            $("#total_" + i).val(precioUni.toFixed(2));
            precio += Number(v.value);
        });
    } catch (error) {
        console.log(error);
    }
    if (count_mc > 0) {
        $('.m_c').css('display', 'table-row');
    }
    else {
        $('.m_c').css('display', 'none');
    }
    var total = subtotal0 + subtotal12 - descuento;
    var subtotal0 = parseFloat(subtotal0);
    var subtotal12 = parseFloat(subtotal12);
    let ivaM = 0;
    let ivaP = 0;
    let precioSub15 = 0;
    let precioSub0 = 0;
    $("input[name='precio[]']").each((i, v) => {
        let is_iva = $("#nombre_" + i).val().split("|");
        let subTot = Number(v.value) * Number($("#cantidad_" + i).val());
        if ($('#cheMaterial_' + i).is(':checked')) {
            if (is_iva.length > 3) {
                if ($("#compraV").val() == 1) {
                    if (is_iva[3] == 1) {
                        ivaP += parseFloat(subTot) * iva_mc;
                        precioSub0 += subTot;
                    } else if ($('#cheIva_' + i).is(':checked')) {
                        ivaP += parseFloat(subTot) * iva_mc;
                        precioSub0 += subTot;
                    }
                } else {
                    ivaP += parseFloat(subTot) * iva_mc;
                    precioSub0 += subTot;
                }
            }
            baseImponibleTotal = parseFloat(baseImponible) + parseFloat(sumaivasin * iva_mc);
            $("#iva").val(ivaM);
            $("#iva_mc").val(ivaP.toFixed(2));
            $("#subtotal_12").val(precioSub15.toFixed(2));
            $("#subtotal_mc").val(precioSub0.toFixed(2));
        } else {
            if (is_iva.length > 3) {
                if (is_iva[3] == 1) {
                    ivaM += parseFloat(subTot) * $('#impuestoIva').val();
                    precioSub15 += subTot;
                } else if ($('#cheIva_' + i).is(':checked')) {
                    ivaM += parseFloat(subTot) * $('#impuestoIva').val();
                    precioSub15 += subTot;
                }
            }
            baseImponibleTotal = parseFloat(baseImponible) + parseFloat(sumaivasin * $('#impuestoIva').val());
            $("#iva").val(ivaM.toFixed(2));
            $("#iva_mc").val(ivaP);
            $("#subtotal_12").val(precioSub15.toFixed(2));
            $("#subtotal_mc").val(precioSub0.toFixed(2));
        }
    });
    var total_pagar = parseFloat(subtotal0) + parseFloat(subtotal12) + parseFloat(ivaP + ivaM) - parseFloat(descuento);
    $("#subtotal_0").val(subtotal0.toFixed(2));
    $("#descuento").val(descuento.toFixed(2));
    $("#total").val(total.toFixed(2));
    $("#total_pagar").val(Math.trunc(total_pagar * 100) / 100);

}
function getPorcent(row) {
    let descuento = $('#descuento_' + row).val();
    let punitario = $('#precio_' + row).val();
    let cantidad = $('#cantidad_' + row).val();
    if (punitario > 0) {
        total = punitario * cantidad;
        let convert = (descuento * 100) / total;
        $('#pdescuento_' + row).val(convert);
        calcular();
    }
}
function getValue(row) {
    let pdescuento = $('#pdescuento_' + row).val();
    let punitario = $('#precio_' + row).val();
    let cantidad = $('#cantidad_' + row).val();
    if (punitario > 0) {
        total = punitario * cantidad;
        let convert = pdescuento * total / 100;
        $('#descuento_' + row).val(convert);
        calcular();
    }
}
function addPedido(pedido) {
    $.ajax({
        type: "post",
        url: "v3/" + $("#controlador").val() + "/agregarPedido",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        datatype: "json",
        data: {
            id: pedido,
        },
        success: function (data) {
            $('#fecha').val(data.cabecera.fecha);
            $('.select2_provi').val(data.cabecera.id_proveedor).trigger('change');
            $('#tipo_comprobante').val(data.cabecera.tipo);
            $('#pedido').val(data.cabecera.pedido);
            $('#subtotal_12').val(data.cabecera.subtotal12.toFixed(2));
            $('#subtotal_0').val(data.cabecera.subtotal0.toFixed(2));
            $('#descuento').val(data.cabecera.descuento.toFixed(2));
            $('#total').val(data.cabecera.subtotal.toFixed(2));
            $('#iva').val(data.cabecera.iva.toFixed(2));
            $('#total_pagar').val(data.cabecera.total.toFixed(2));
            $('#detalleCompra').before(data.html);
            fila = data.cabecera.fila;
            $('#myModalPe').modal('toggle');
            $('#modal_pedidos').modal('show');
            $('#modal_pedidos').modal('toggle');
            //calcular();
        },
        error: function (data) {
            console.log(data);
        },
    });
}
function verificarCompra(idCompra, estado, tipo = 'Factura') {
    Swal.fire({
        title: `¿Desea Anular esta compra?`,
        text: $("#informa").val(),
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            var acumulate = "";
            $.ajax({
                type: 'get',
                url: "v3/" + $("#controlador").val() + "/verificarCompra",
                datatype: 'json',
                data: {
                    'verificar': '4',
                    'id_compra': idCompra
                },
                success: function (data) {

                    if (!data.status) {
                        return mensajes("error", data.msj);
                    }
                    if (data.msj == "si") {
                        let id_com_egreso = data.id_egreso;
                        let enlace = `<a target="_blank" href="{{ url('contable/acreedores/documentos/comprobante/egreso/comprobante/buscar?id=${data.ids[0]}') }}"><b>${data.tablas[0]}</b></a>`;
                        let enlace2 = `<a target="_blank" href="{{ url('contable/Banco/debito/acreedores/buscar?id=${data.ids[1]}') }}"> <b>${data.tablas[1]}</b> </a>`;
                        let enlace3 = `<a target="_blank" href="{{ url('contable/cruce/valores?id=${data.ids[2]}') }}"><b>${data.tablas[2]}</b></a>`;
                        let enlace4 = `<a target="_blank" href="{{ url('contable/acreedores/documentos/retenciones/buscar?id=${data.ids[3]}') }}"><b>${data.tablas[3]}</b></a>`;
                        let enlace5 = `<a target="_blank" href="{{ url('contable/comp_egreso_masivo/buscar?id=${data.ids[4]}') }}"><b>${data.tablas[4]}</b></a>`;
                        let enlace6 = `<a target="_blank" href="{{ url('contable/cruce_cuentas/valores/buscar?id=${data.ids[5]}') }}"><b>${data.tablas[5]}</b></a>`;
                        let texto = `Existen algunos ${enlace} ${enlace2} ${enlace3} ${enlace4} ${enlace5} ${enlace6} generados con esta factura`;
                        return mensajes("error", texto);
                    } else {
                        eliminarCompra(idCompra);
                    }

                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    })
}
async function eliminarCompra(idCompra) {
    try {
        const {
            value: text
        } = await Swal.fire({
            input: 'textarea',
            inputPlaceholder: 'Ingrese motivo de anulación...',
            inputAttributes: {
                'aria-label': 'Ingrese motivo de anulación'
            },
            showCancelButton: true
        })
        if (text) {
            $.ajax({
                type: 'get',
                url: "v3/" + $("#controlador").val() + "/anularCompra",
                datatype: 'json',
                data: {
                    'id_compra': idCompra,
                    'observacion': text
                },
                success: function (data) {
                    if (data.status) {
                        mensajes("success", data.msj);
                        $('#tableCompras').DataTable().ajax.reload();
                    } else {
                        mensajes("error", data.msj);
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    } catch (err) {
        console.log(err);
    }
}
function buscarCompras() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    $('#tableCompras').dataTable().fnClearTable();
    $('#tableCompras').dataTable().fnDestroy();
    $('#tableCompras').DataTable({
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
            url: "v3/" + $('#controlador').val() + "/buscarCompras",
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
            className: "text-center",
            data: "numero",
        },
        {
            data: "razonsocial",
        },
        {
            className: "text-center",
            data: "id",
        },
        {
            className: "text-center",
            data: "id_asiento_cabecera",
            render: function (data, type, row) {
                return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\')">' + row.id_asiento_cabecera + '</a>';
            }
        },
        {
            className: "text-center",
            data: "tipo_comprobante",
        },
        {
            className: "text-center",
            data: "fecha",
        },
        {
            className: "text-right",
            data: "subtotal_0",
        },
        {
            className: "text-right",
            data: "subtotal_12",
        },
        {
            className: "text-right",
            data: "iva_total",
        },
        {
            className: "text-right",
            data: "total_final",
        },
        {
            className: "text-center",
            data: "estado",
            render: function (data, type, row) {
                var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : (row.estado == 0 ? '<label class="text-danger">' + $('#inactivo').val() + '</label>' : (row.id_retencion !== null ? '<label class="text-info">' + $('#conretencion').val() + '</label>' : '<label class="text-success">' + $('#activo').val() + '</label>'));
                return estado;
            },
        },
        {
            className: "text-center",
            data: "usuariocrea",
        },
        {
            className: "text-center",
            data: "usuariomod",
        },
        {
            className: "text-center",
            data: "id",
            render: function (data, type, row) {
                var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
                var descargas = "";
                var conretencion = '';
                var archivo = '<a href="javascript:;" style="margin: 0px;" class="dropdown-item"><label title="Archivo pdf del documento a ingresar" class="archivoCompra" for="archivoCompra_' + row.id + '" style="cursor:pointer;font-weight: normal;"><input type="file" onchange="verificarArchivoCompra(this)" name="archivoCompra_' + row.id + '" id="archivoCompra_' + row.id + '" style="display:none"><i class="fas fa-file-upload" style="color:green;"></i> Subir archivo</label></a>';
                if (row.estado == 1) {
                    accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
                }
                if (row.id_retencion !== null) {
                    conretencion = '<a class="dropdown-item" href="javascript:;" onclick="verRetencion(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fas fa-file-signature" style="color:#17a2b8"></i> Ver retención</a>';
                    accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
                }
                if (row.doc_electronico == 5) {

                    descargas = '<a href="' + $('#base_url').val() + '/api_doc_electronico?opcion=descargarXML&clave=' + row.nro_autorizacion + '" class="dropdown-item"><i class="fas fa-file-code" style="color:green;"></i> Descargar XML</a><a href="' + $('#base_url').val() + '/api_doc_electronico?opcion=generarPdf&clave=' + row.nro_autorizacion + '" class="dropdown-item"><i class="fas fa-file-pdf" style="color:red;"></i> Descargar PDF</a>';
                }
                if (row.rutapdf !== null) {
                    archivo = '<div class="dropdown-divider"></div><a href="javascript:;" class="dropdown-item" onclick="verArchivoCompra(\'' + row.rutapdf + '\')"><i class="fas fa-file-download" style="color:blue;"></i> Ver archivo</a><a href="javascript:;" class="dropdown-item" onclick="eliminarArchivoCompra(\'' + row.id + '\')"><i class="fas fa-trash-alt" style="color:red;"></i> Eliminar archivo</a><div class="dropdown-divider"></div>';
                }
                var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDocumento(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fas fa-file-invoice" style="color:#28a745"></i></i> Ver documento</a>' + conretencion + archivo + descargas + '<div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="verificarCompra(\'' + row.id + "','" + row.estado + "','Factura');\">" + accion + "</a></div></div>";
                return botones;
            },
        },]
    });
}
function formatDate(dateString) {
    let date = new Date(dateString);
    let day = date.getDate();
    let month = date.getMonth() + 1;
    let year = date.getFullYear();
    day = day < 10 ? '0' + day : day;
    month = month < 10 ? '0' + month : month;
    return day + '/' + month + '/' + year;
}
function validarPeriodo(e, type) {
    const fecha = new Date();
    const anioActual = fecha.getFullYear();
    const mesActual = fecha.getMonth() + 1;
    if (type == "mes") {
        if (parseInt(e.value) > 12) {
            e.value = mesActual;
            if (parseInt(mesActual) >= 1 && parseInt(mesActual) <= 9) {
                e.value = "0" + parseInt(e.value);
            }
            return mensajes("error", "El mes del periodo no existe");

        } else if (parseFloat(e.value) <= 0) {
            e.value = mesActual;
            if (parseInt(mesActual) >= 1 && parseInt(mesActual) <= 9) {
                e.value = "0" + parseInt(e.value);
            }
            return mensajes("error", "El mes del periodo no puede ser menor o igual a 0")

        } else if (parseInt(e.value) >= 1 && parseInt(e.value) <= 9) {
            e.value = "0" + parseInt(e.value);
            return;
        }
    }

    if (type == "anio") {
        if (parseInt(e.value) > 2200) {
            e.value = anioActual;
            alertas("error", "Error en año", "El año del periodo no esta permitido")
            return;
        } else if (parseInt(e.value) < 2015) {
            e.value = anioActual;
            alertas("error", "Error en año", "El año del periodo no esta permitido")
            return;
        }
    }
}
function buscar_orden() {
    let traerOrden = ajax('v3/' + $('#controlador').val() + '/documentoFacturaBuscarOrden', { "orden": $("#select_orden").val() }, 'json');
    if (traerOrden.responseJSON !== null) {
        traerOrden = traerOrden.responseJSON;
        llenarInputs(traerOrden.cab);
    }
}
function ingresar_cero(ids, longitud) {
    let id = document.getElementById(ids);
    let cero = "";
    let concat = "";
    if (parseInt(id.value) > 0) {
        if (id.value.length < longitud) {
            while (concat.length != longitud) {
                cero = "0" + cero;
                concat = cero + id.value;
            }
            id.value = concat;
        } else {
            id.value = "";
            return mensajes("error", "Valor incorrecto");

        }
    } else {
        id.value = "";
        return mensajes("error", "Valor incorrecto");
    }
}
function llenarCampo() {
    let proveedor = document.getElementById("proveedor");
    let serie = document.getElementById("serie_factura").value;
    let secuencia = document.getElementById("secuencia").value;
    let value = proveedor.options[proveedor.selectedIndex].text;
    if (value != '') {
        document.getElementById("concepto").value = `ACT-FA # ${value} ${serie}-${secuencia}`
    }
}
function agregar_serie() {

    var serie = $('#serie_factura').val();
    if ((serie.length) == 3) {
        $('#serie_factura').val(serie + '-');
    } else if ((serie.length) > 7) {
        $('#serie_factura').val('');
        return mensajes("error", "Ingrese la serie de la factura correctamente");
    }
}
function llenarInputs(data) {
    document.getElementById("concepto").value = data.observacion;
    document.getElementById("fecha_compra").value = data.fecha_compra;
    document.getElementById("fecha_asiento").value = data.fecha_compra;
    document.getElementById("serie_factura").value = data.serie;
    document.getElementById("secuencia").value = data.secuencia;
    document.getElementById("divisas").value = data.divisas;
    $("#divisas").val(data.divisas).trigger("change");
    document.getElementById("subtotal_121").value = data.subtotal12;
    document.getElementById("subtotal_01").value = data.subtotal0;
    document.getElementById("descuento1").value = data.descuento;
    document.getElementById("base1").value = data.subtotal;
    document.getElementById("tarifa_iva1").value = data.impuesto;
    document.getElementById("total1").value = data.total;
    document.getElementById("subtotal_12").innerHTML = parseFloat(data.subtotal12).toFixed(2);
    document.getElementById("subtotal_0").innerHTML = parseFloat(data.subtotal0).toFixed(2);
    document.getElementById('base').innerHTML = parseFloat(data.subtotal).toFixed(2);
    document.getElementById('descuento').innerHTML = parseFloat(data.descuento).toFixed(2);
    document.getElementById('tarifa_iva').innerHTML = parseFloat(data.impuesto).toFixed(2);
    document.getElementById('total').innerHTML = parseFloat(data.total).toFixed(2);


    $('#id_proveedor').append(`<option selected value="${data.id_proveedor}" >${data.nombre_proveedor}</option>`).trigger("change");

    $('#agregar_cuentas').empty();
    document.getElementById('contador_items').value = 1;

    let details = data.detalles.map(detalle => {
        crearFila(1, detalle);
    });
}
function cambiarFecha(e, type) {
    if (type == "fecha_compra") {
        document.getElementById("fecha_asiento").value = e.value;
    } else if (type == "fecha_asiento") {
        document.getElementById("fecha_compra").value = e.value;
    }
}
function crearFila(req = 0, data = {}) {
    var id = document.getElementById('contador_items').value;
    let fila = `
    <tr >
        <td style="width:3%;text-align:center">
            <button type="button" id="btn_ac${id}" class="btn btn-xs btn-info" onclick='modal_activo(${id}, event)'><i class="fa fa-edit" aria-hidden="true"></i></button>  
        </td>
        <td>
            <input id="codigo${id}" class="form-control validar" type="text" name="codigo[]" readonly value="${req === 1 ? data.codigo : ''}" >
        </td>
        <td>
            <input id="descrip_prod${id}" class="form-control validar" type="text" name="descrip_prod[]" readonly value="${req === 1 ? data.nombre : ''}">
            <textarea  rows="3" name="observacion[]" id="observacion${id}" class="form-control" style="margin-top:2px;" placeholder="Observacion"></textarea>
        </td>
        <td>
            <input onchange="totalProducto(${id})" id="cantidad${id}" class="form-control text-right cant validar" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="${req === 1 ? data.cantidad : '0'}" name="cantidad[]" >

        </td>
        <td>
            <input onblur="this.value=parseFloat(this.value).toFixed(2);" onchange="totalProducto(${id})" id="precio${id}" value="${req === 1 ? data.precio : '0.00'}"  type="text" class="pneto form-control text-right validar" name="precio[]" placeholder="0.00">
        </td>

        <td>
            <input onchange="totalProducto(${id})" id="descpor${id}" class="form-control text-right pdesc" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="${req === 1 ? data.porct_descuento : '0.00'}" name="descpor[]" >
            <input onchange="totalProducto(${id})" id="maxdesc${id}" class="form-control text-right maxdesc" type="hidden" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0.00" name="maxdesc[]" >
        </td>
        <td>
            <input onchange="descuento(${id});"  id="desc${id}" class="form-control text-right desc" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="${req === 1 ? data.descuento : '0.00'}" name="desc[]" >
        </td>
        <td>
            <input onchange="totalProducto(${id})" id="precioneto${id}" class="form-control px-1 text-right precioneto validar" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="${req === 1 ? data.precio_neto : '0.00'}"  name="precioneto[]" >
        </td>
        <td style="width:3%;text-align:center">
            <input onchange="totalProducto(${id}); select_iva(${id});" class="form" type="checkbox" name="valoriva[]" id="valoriva${id}" ${req === 1 ? data.iva === 1 ? 'checked' : '' : ''}>
            <input type="hidden" name="val_iva[]" id="val_iva${id}" class="form-control px-1 text-right val_iva" value="${req === 1 ? data.val_iva : '0.00'}">
            <input type="hidden" name="total_valor[]" id="total_valor${id}" class="form-control px-1 text-right total_valor" value="${req === 1 ? data.total_valor : '0.00'}">
            <input class="check_iva" id="check_iva${id}" type="hidden" name="check_iva[]" value="${req === 1 ? data.iva : '0'}">
            

        </td>

        <td>
            <button type="button" class="btn btn-danger btn-gray delete" onclick="eliminarModalUni(${id})">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
        </td>
            </tr>
        `;

    $('#agregar_cuentas').append(fila);
    $('.select2_cuentas').select2({
        tags: false
    });

    if (req === 1) {

        $("#datos_activo").append(modal_crear(id, req, data.activo_fijo));

        let modal_datos_a = document.getElementById("modal_datos_a" + id);
        modal_datos_a.classList.remove("block");
        modal_datos_a.classList.add("none");
        $(`#mdtipo${id} option[value="${data.activo_fijo.tipo_id}"]`).attr("selected", true);
        $(`#mdtipo${id}`).change();
        $(`#mdmarca${id} option[value="${data.activo_fijo.marca}"]`).attr("selected", true);
        $(`#mdcolor${id} option[value="${data.activo_fijo.color}"]`).attr("selected", true);
        $(`#mdserie${id} option[value="${data.activo_fijo.serie}"]`).attr("selected", true);
        $(`#mdresponsable${id} option[value="${data.activo_fijo.responsable}"]`).attr("selected", true);
        $(`#mdubicacion${id} option[value="${data.activo_fijo.ubicacion}"]`).attr("selected", true);

        setTimeout(() => {
            $(`#mdgrupo${id} option[value="${data.activo_fijo.subtipo_id}"]`).attr("selected", true);
        }, 1000);


        if (data.activo_fijo.accesorios.length > 0) {
            const accesorio = data.activo_fijo.accesorios.map(access => {
                agregar_items(id, req, access);

            });
        }
    }
    id++;
    document.getElementById('contador_items').value = id;
}
function totalProducto(id) {
    let cantidad = document.getElementById("cantidad" + id).value;
    let precio = document.getElementById("precio" + id).value;
    let descpor = document.getElementById("descpor" + id).value;
    let desc = document.getElementById("desc" + id);
    let precioneto = document.getElementById("precioneto" + id);
    let val_iva = document.getElementById("val_iva" + id);
    let total_valor = document.getElementById("total_valor" + id);
    let subtotal0 = document.getElementById("subtotal_12");
    let subtotal12 = document.getElementById("subtotal_0");
    let iva_global = document.getElementById("iva_global").value;
    let preXcantidad = (precio * cantidad);
    let porcDescuento = (descpor / 100) * preXcantidad;
    let precioTotal = (preXcantidad) - porcDescuento;
    let valoriva = 0;
    if ($('#valoriva' + id).prop('checked')) {
        valoriva = Math.round(precioTotal * (1000 * parseFloat(iva_global)));
        valoriva = valoriva / 1000;
    }
    let total = precioTotal + valoriva;
    val_iva.value = valoriva;
    desc.value = porcDescuento.toFixed(2);
    precioneto.value = precioTotal.toFixed(2);
    total_valor.value = total;
    totalGlobal();
}
function totalGlobal() {
    let precioneto = document.querySelectorAll(".precioneto");
    let desc = document.querySelectorAll(".desc");
    let vliva = document.querySelectorAll(".val_iva");
    let tot_valor = document.querySelectorAll(".total_valor");
    let cantidad = document.querySelectorAll(".cant");
    let precio = document.querySelectorAll(".pneto");
    let check_iva = document.querySelectorAll(".check_iva");
    let total = 0;
    let descuento = 0;
    let iva = 0;
    let totv = 0;
    let subtotal0 = 0;
    let subtotal12 = 0;
    for (let i = 0; i < precioneto.length; i++) {
        total += parseFloat(precioneto[i].value);
        descuento += parseFloat(desc[i].value);
        iva += parseFloat(vliva[i].value);
        totv += parseFloat(tot_valor[i].value);
        console.log(tot_valor[i]);
        if (parseFloat(check_iva[i].value) == 1) {
            subtotal12 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
        } else {
            subtotal0 += parseFloat(cantidad[i].value) * parseFloat(precio[i].value);
        }
    }

    document.getElementById("subtotal_12").innerHTML = parseFloat(subtotal12).toFixed(2);
    document.getElementById("subtotal_121").value = parseFloat(subtotal12).toFixed(2);

    document.getElementById("subtotal_0").innerHTML = parseFloat(subtotal0).toFixed(2);
    document.getElementById("subtotal_01").value = parseFloat(subtotal0).toFixed(2);

    document.getElementById('base').innerHTML = parseFloat(total).toFixed(2);
    document.getElementById('base1').value = parseFloat(total).toFixed(2);

    document.getElementById('descuento').innerHTML = parseFloat(descuento).toFixed(2);
    document.getElementById('descuento1').value = parseFloat(descuento).toFixed(2);
    document.getElementById('tarifa_iva').innerHTML = parseFloat(isNaN(iva) ? 0 : iva).toFixed(2);
    document.getElementById('tarifa_iva1').value = parseFloat(isNaN(iva) ? 0 : iva).toFixed(2);

    document.getElementById('total').innerHTML = parseFloat(totv).toFixed(2);
    document.getElementById('total1').value = parseFloat(totv).toFixed(2);
}
$('body').on('click', '.delete', function () {
    $(this).parent().parent().remove();
    totalGlobal();
});
function eliminarModalUni(id) {
    let divCargando = document.getElementById("modal_padre" + id);
    if (divCargando !== null) {
        let hijo = document.getElementById("modal_datos_a" + id);
        divCargando.removeChild(hijo);
    }
}
function activarSelect2Categoria() {
    $(".js-data-categoria")
        .select2({
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: "v3/" + $("#controlador").val() + "/getDataCategoria",
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {

                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.data,
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
        });
}
function modal_activo(id, e) {
    e.preventDefault();
    let modal = document.getElementById("modal_datos");
    modal.style.display = "block";
    modal.classList.add("in");
    let modal_hecha = document.getElementById("modal_datos_a" + id);

    if (modal_hecha == null) {
        let modal_creada = modal_crear(id);
        $("#datos_activo").append(modal_creada);

    } else {
        modal_hecha.classList.remove("none");
        modal_hecha.classList.add("block");
    }
    $('.select2_cuentas2').select2({
        tags: false
    });

    $('.select2_color').select2({
        tags: true
    });

}
function modal_crear(id, req = 0, data = {}) {
    let mod = `
    <div id="modal_padre${id}">
    <div class="block" id="modal_datos_a${id}" class="modal-content" >
        <input type="hidden" name="mdtiposid[]" value="${id}">
        <div class="modal-header">
        <h3 style="margin:0;">Activos Fijos</h3>
        </div>
        <div class="modal-body">
        <div class="row">
        <label for="mdcodigo${id}" class="col-sm-2 control-label">Código*</label>
        <div class="col-md-4">
        <input class="form-control" type="text" id="mdcodigo${id}" name="mdcodigo${id}" value="${req === 1 ? data.codigo_text : ''}"  placeholder="Codigo" maxlength="16" readonly>
        <input type="hidden" id="mdid${id}" name="mdid${id}[]">
        </div>
        <div class="col-md-2 text-center">-</div>
        <div class="col-md-4">
        <input class="form-control" type="text" id="mdcodigo_num${id}" name="mdcodigo_num${id}" placeholder="Codigo" maxlength="16" value="${req === 1 ? data.codigo_num : ''}" onchange="ingresar_cero('mdcodigo_num${id}', 6);" readonly>
        </div>
        </div> <br>
        <div class="row">
        <label for="mdnombre${id}" class="col-md-2 control-label">Nombre*</label>
        <div class="col-md-10">
        <input class="form-control" type="text" id="mdnombre${id}" value="${req === 1 ? data.nombre : ''}" ${req === 1 ? 'readonly' : ''} name="mdnombre${id}" placeholder="Nombre">
        </div>
        </div> <br>
        <div class="row">
        <label for="mddescripcion${id}" class="col-md-2 control-label">Descripción</label>
        <div class="col-md-10">
        <input class="form-control" type="text" id="mddescripcion${id}" name="mddescripcion${id}" value="${req === 1 ? data.descripcion : ''}" ${req === 1 ? 'readonly' : ''} placeholder="Descripción">
        </div>
        </div> <br>
        <div class="row">
        <label for="mdtipo${id}" class="col-md-2 control-label">Tipo*</label>
        <div class="col-md-10">
        <select id="mdtipo${id}" name="mdtipo${id}" class="form-control select2_cuentas2" style="width: 100%;" onchange="buscar_categoria(${id}); codigo_activo(${id});" >
        <option value="">Seleccione...</option>`;
    let textInJavascript = document.getElementById("tiposModal").value;
    mod += textInJavascript;
    mod += `</select>
        </div >
        </div >
        <br/>
        <div class="row">
        <label for="" class="col-md-2 control-label">Categoria*</label>
        <div class="col-md-10">
        <select id="mdgrupo${id}" name="mdgrupo${id}" class="select2 form-control" style="width: 100%;" >
        <option value="">Seleccione...</option>
        </select>
        </div>
        </div> <br>
        <div class="row">
        <label for="mdresponsable${id}" class="col-md-2 control-label">Responsable</label>
        <div class="col-md-10">
        <select id="mdresponsable${id}" name="mdresponsable${id}" class="form-control select2_color" style="width: 100%;" onchange="guardar_responsable(${id});">
        <option value="">Seleccione...</option>`;
    let textInJavascript1 = document.getElementById("tiposModal1").value;
    mod += textInJavascript1;
    mod += `</select>
        </div>
        </div> <br>
        <div class="row">
        <label for="mdubicacion${id}" class="col-md-2 control-label">Ubicación</label>
        <div class="col-md-10">
        <input type="text" name="mdubicacion${id}" id="mdubicacion${id}" class="form-control" value="${req === 1 ? data.ubicacion : ''}" placeholder="Ubicación">
        </div>
        </div> <br>
        <div class="row">
        <label for="mdmarca${id}" class="col-md-2 control-label">Marca</label>
        <div class="col-md-10">
        <select id="mdmarca${id}" name="mdmarca${id}" class="form-control select2_color" style="width: 100%;" onchange="guardar_marca(${id});">
        <option value="">Seleccione...</option>`;
    let textInJavascript2 = document.getElementById("tiposModal2").value;
    mod += textInJavascript2;
    mod += `</select>
        </div>
        </div> <br>
        <div class="row">
        <label for="mdcolor${id}" class="col-md-2 control-label">Color</label>
        <div class="col-md-10">
        <select id="mdcolor${id}" name="mdcolor${id}" class="form-control select2_color" style="width:100%;" onchange="guardar_color(${id});">
        <option value="">Seleccione...</option>`;
    let textInJavascript3 = document.getElementById("tiposModal3").value;
    mod += textInJavascript3;
    mod += `</select>
        </select>
        </div>
        </div> <br>
        <div class="row">
        <label for="mdmodelo${id}" class="col-md-2 control-label">Modelo</label>
        <div class="col-md-10">
        <input class="form-control" type="text" id="mdmodelo${id}" value="${req === 1 ? data.modelo : ''}" name="mdmodelo${id}" placeholder="Modelo">
        </div>
        </div> <br>
        <div class="row">
        <label for="mdserie${id}" class="col-md-2 control-label">Serie</label>
        <div class="col-md-10">
        <select id="mdserie${id}" name="mdserie${id}" class="form-control select2_color" style="width:100%;" onchange="guardar_serie(${id});">
        <option value="">Seleccione...</option>`;
    let textInJavascript4 = document.getElementById("tiposModal4").value;
    mod += textInJavascript4;
    mod += `</select>
        </select>
        </div>
        </div> <br>
        <div class="row">
        <label for="mdprocedencia${id}" class="col-md-2 control-label">Procedencia</label>
        <div class="col-md-10">
        <input class="form-control" type="text" id="mdprocedencia${id}" name="mdprocedencia${id}" value="${req === 1 ? data.procedencia : ''}" placeholder="Procedencia">
        </div>
        </div> <br>
        <input type="hidden" name="fotoactivo${id}" id="fotoactivo${id}" class="form-control px-1 text-right fotoactivo" value="${req === 1 ? data.foto_activo : ''}">
        <input type="hidden" name="fotoacc${id}" id="fotoacc${id}" class="form-control px-1 text-right fotoacc" value="${req === 1 ? data.foto_acc : ''}">

            <div class="row">
                <input name='contador_items_accesorios${id}' id='contador_items_accesorios${id}' type='hidden' value="1">
                    <div class="col-md-12 table-responsive">
                        <table id="items${id}" class="table table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                            <thead>
                                <tr class='well-darks'>
                                    <th width="30%" tabindex="0">Nombre</th>
                                    <th width="20%" tabindex="0">Marca</th>
                                    <th width="20%" tabindex="0">Modelo</th>
                                    <th width="20%" tabindex="0">Serie</th>
                                    <th width="10%" tabindex="0">
                                        <button type="button" class="btn btn-success btn-gray" onclick="agregar_items(${id})">
                                            <i class="fas fa-plus" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="fila-fija">
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div> <br>
            </div>
            <br>
                <div class="modal-footer">
                    <button onclick="guardar_moda(${id})" type="button" class="btn btn-success pull-right" >Guardar</button>
                    <button onclick="eliminarModal(${id})" type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>`;
    return mod;
}
function guardar_serie(id) {
    let formData = $("#frm_").serializeArray();
    let formObject = {};
    $.each(formData, function (index, field) {
        formObject[field.name] = field.value;
    });
    formObject['id'] = id;
    let verificar = ajax('v3/' + $('#controlador').val() + '/documentofactura_saveSerie', formObject, 'json');
    if (verificar.responseJSON !== null) {
        verificar = verificar.responseJSON;
        if (verificar.status) {
            return mensajes(verificar.icon, verificar.msj);
        } else {
            return mensajes(verificar.icon, verificar.msj + ' ' + verificar.line);
        }
    }
}
function guardar_color(id) {
    let formData = $("#frm_").serializeArray();
    let formObject = {};
    $.each(formData, function (index, field) {
        formObject[field.name] = field.value;
    });
    formObject['id'] = id;
    let verificar = ajax('v3/' + $('#controlador').val() + '/guardar_color', formObject, 'json');
    if (verificar.responseJSON !== null) {
        verificar = verificar.responseJSON;
        if (verificar.status) {
            return mensajes(verificar.icon, verificar.msj);
        } else {
            return mensajes(verificar.icon, verificar.msj + ' ' + verificar.line);
        }
    }
}
function guardar_responsable(id) {

    let formData = $("#frm_").serializeArray();
    let formObject = {};
    $.each(formData, function (index, field) {
        formObject[field.name] = field.value;
    });
    formObject['id'] = id;
    let verificar = ajax('v3/' + $('#controlador').val() + '/guardar_responsable', formObject, 'json');
    if (verificar.responseJSON !== null) {
        verificar = verificar.responseJSON;
        if (verificar.status) {
            return mensajes(verificar.icon, verificar.msj);
        } else {
            return mensajes(verificar.icon, verificar.msj + ' ' + verificar.line);
        }
    }
}
function codigo_activo(id) {
    let id_tipo = $("#mdtipo" + id).val();
    $.ajax({
        type: 'post',
        url: "v3/" + $("#controlador").val() + "/buscar_codigo",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: {
            'id_tipo': id_tipo
        },
        success: function (data) {
            $("#mdcodigo" + id).val(data.codigo)
            $("#mdcodigo_num" + id).val(data.secuencia)


        },
        error: function (data) {
        }
    })
}
function buscar_categoria(id) {
    let valor = $("#mdtipo" + id).val();
    $.ajax({
        type: 'post',
        url: "v3/" + $("#controlador").val() + "/buscar_categoria",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: {
            'opcion': valor
        },
        success: function (data) {
            $("#mdgrupo" + id).empty();
            $.each(data, function (key, registro) {
                $("#mdgrupo" + id).append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });

        },
        error: function (data) {
        }
    })
}
function guardar_marca(id) {
    let formData = $("#frm_").serializeArray();
    let formObject = {};
    $.each(formData, function (index, field) {
        formObject[field.name] = field.value;
    });
    formObject['id'] = id;
    $.ajax({
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        url: "v3/" + $("#controlador").val() + "/guardar_marca",
        data: formData,
        datatype: 'json',
        success: function (data) {
            if (data.status) {
                return mensajes(data.icon, data.msj);
            } else {
                return mensajes(data.icon, data.msj + ' ' + data.line);
            }
        },
        error: function (data) {
            //console.log(data);

        }
    });
}
function agregar_items(id) {
    var id_accesorio = document.getElementById('contador_items_accesorios' + id).value;
    var tr = `<tr class="columnas"> 
                        <td>
                            <input required name="nombre_ac${id}[]" id="nombre_ac${id}" class="form-control"  autocomplete="off">
                        </td>

                        <td>
                            <select id="marca_ac${id}_${id_accesorio}" name="marca_ac${id}[]"  class="form-control select2_color" style="width:100%" onchange="guardar_marca(${id});" >
                                <option value="">Seleccione..</option>`;
    let textInJavascript2 = document.getElementById("tiposModal2").value;
    tr += textInJavascript2;
    tr += `</select>
                        </td>

                        <td> 
                            <input type="text" name="modelo_ac${id}[]" id="modelo_ac${id}_${id_accesorio}" class="form-control cant"  autocomplete="off">
                        </td>

                        <td>
                            <select id="serie_ac${id}_${id_accesorio}" name="serie_ac${id}[]" class="form-control select2_color" style="width:100%" onchange="guardar_serie(${id});" >
                                <option value="">Seleccione</option>`;
    let textInJavascript4 = document.getElementById("tiposModal4").value;
    tr += textInJavascript4;
    tr += `</select>
                        </td>

                        <td>
                            <button  type="button" onclick="deleteRow(this)" class="btn btn-danger btn-gray" ><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </td>
                        <input type="hidden" name="accesorio${id}[]" value="${id_accesorio}" />               
                    </tr> `;
    $('#items' + id).append(tr);
    $('.select2_color').select2({
        tags: true
    });
    let ids = id_accesorio;
    ids++;
    document.getElementById('contador_items_accesorios' + id).value = ids;
}
function deleteRow(btn) {
    var row = btn.parentNode.parentNode;
    row.parentNode.removeChild(row);
}
function guardar_moda(id) {

    let codigo = document.getElementById("codigo" + id);
    let mdcodigo = document.getElementById("mdcodigo" + id).value;
    let mdcodigo_num = document.getElementById("mdcodigo_num" + id).value;
    let mdtipo = document.getElementById("mdtipo" + id).value;
    let mdgrupo = document.getElementById("mdgrupo" + id).value;
    if (mdcodigo.trim() == "") {
        mensajes('warning', 'Ingrese el código');
    } else if (mdcodigo_num.trim() == "") {
        mensajes('warning', 'Ingrese el número de código');
    } else if (mdtipo.trim() == '') {
        mensajes('warning', 'Ingrese el tipo');
    } else if (mdgrupo.trim() == '') {
        mensajes('warning', 'Ingrese la categoría');
    } else {
        codigo.value = mdcodigo + "-" + mdcodigo_num;

        let mdnombre = document.getElementById("mdnombre" + id).value
        document.getElementById("descrip_prod" + id).value = mdnombre;
        let modal_datos_a = document.getElementById("modal_datos_a" + id);
        modal_datos_a.classList.remove("block");
        modal_datos_a.classList.add("none");
        let modal = document.getElementById("modal_datos");
        modal.style.display = "none";
        modal.classList.remove("in");
        let nombre_ac = document.querySelectorAll("#nombre_ac" + id);
        let desc = "";
        for (let i = 0; i < nombre_ac.length; i++) {
            desc = desc + nombre_ac[i].value + "\n"
        }
        document.getElementById("observacion" + id).value = desc
    }
}
function eliminarModal(id) {
    let modal_datos_a = document.getElementById("modal_datos_a" + id);
    modal_datos_a.classList.remove("block");
    modal_datos_a.classList.add("none");
    let modal = document.getElementById("modal_datos");
    modal.style.display = "none";
    modal.classList.remove("in");
    let check = document.getElementById("af" + id);
    let check_af = document.getElementById("check_af" + id);
    if (check != null) {
        check.checked = false;
    }
    if (check_af != null) {
        check_af.value = 0;
    }
}
function verificarCompraActivo(archivo) {
    try {
        cancel = document.getElementsByClassName('archivoActivo');
        if (archivo.files[0] <= 0 || archivo.files[0] === null || archivo.files[0] === "" || archivo.files[0] === undefined || archivo.files[0] === "undefined" || archivo.files[0] === "null") {
            if (cancel.value == "" || cancel.value == null) {
                mensajes('info', 'La imagen no puede estar vacia...');
            }
        }
        else {
            var inputFileImage = document.getElementById(archivo.id);
            var file = inputFileImage.files[0];
            var fd = new FormData();
            fd.append(archivo.id, file);
            fd.append('tipo_archivo', 'archivoActivo');
            fd.append('id', archivo.id);
            $.ajax({
                url: "v3/utilidades/guardarArchivoActivo",
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (data) {
                    mensajes('info', 'Archivo subido correctamente');
                    $('#tableIngresoActivos').DataTable().ajax.reload();
                }
            });
        }
    } catch (error) {
        console.log(error);
    }
}

function eliminarArchivoActivo(id) {
    var data = {
        id: id
    };
    var json = ajax("v3/utilidades/eliminarArchivoActivo", data, 'json');
    if (json.responseJSON != '') {
        json = json.responseJSON;
        mensajes(json.status, json.message);
        $('#tableIngresoActivos').DataTable().ajax.reload();
    }
}
function verDocumentoActivo(id) {
    window.open($("#base_url").val() + "/v3/compras/nuevaFacturaActivos?id=" + id, "_self");
}
function md_activo(id) {

    $.ajax({
        url: 'v3/' + $("#controlador").val() + '/getOnlyItemActive',
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: {

            'id': id
        },
        success: function (data) {
            for (let index = 0; index < data.detalles.length; index++) {
                if (contView < data.detalles.length) {
                    contView++;
                    var tr = `<tr class="columnas"> 
                        <td>
                            <input required name="nombre_ac${index}[]" value="${data.detalles[index]['nombre']}" id="nombre_ac${index}" class="form-control" style="height:25px;width:90%;" autocomplete="off" disabled>
                        </td>
                        <td>
                        <input required id="marca_ac${index}" name="marca_ac${index}[]" value="${data.detalles[index]['marca']}"  class="form-control" style="height:25px;width:90%;" autocomplete="off" disabled >
                        </td>
                        <td> 
                            <input type="text" name="modelo_ac${id}[]" id="modelo_ac${index}"  value="${data.detalles[index]['modelo']}" class="form-control cant" style="height:25px;width:75%;" autocomplete="off" disabled >
                        </td>
                        <td>
                        <input type="text" name="serie_ac${id}[]" id="serie_ac${index}"  value="${data.detalles[index]['serie']}" class="form-control cant" style="height:25px;width:75%;" autocomplete="off" disabled >
                        </td>
                    </tr> `;
                    $("#agregar_accesorios").append(tr);
                }
            }
            let datos = data.res;
            document.getElementById("mdcodigo").value = datos.codigo_text;
            document.getElementById("mdcodigo_num").value = datos.codigo_num;
            document.getElementById("mdnombre").value = datos.nombre;
            document.getElementById("mddescripcion").value = datos.descripcion;
            document.getElementById("mdtipo").value = data.tipo;
            document.getElementById("mdgrupo").value = data.subtipo;
            document.getElementById("mdresponsable").value = datos.responsable;
            document.getElementById("mdubicacion").value = datos.ubicacion;
            document.getElementById("mdmarca").value = datos.marca;
            document.getElementById("mdcolor").value = datos.color;
            document.getElementById("mdmodelo").value = datos.modelo;
            document.getElementById("mdserie").value = datos.serie;
            document.getElementById("mdprocedencia").value = datos.procedencia;
            $('#md-activo-fijo').modal('show');
        },
        error: function (data) {
        }
    });
}

function verificarActivo(id, estado, tipo) {
    Swal.fire({
        title: `¿Desea Anular esta factura?`,
        text: $("#informa").val(),
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'get',
                url: "v3/" + $("#controlador").val() + "/verificarActivo",
                datatype: 'json',
                data: {
                    "id": id
                },
                success: function (data) {
                    if (!data.status) {

                        return mensajes(data.status, data.msj);
                    }

                    if (data.respuesta == "si") {
                        let enlace = `<a target="_blank" href="{{ url('contable/acreedores/documentos/comprobante/egreso/comprobante/buscar?id="${data.ids[0]}')}}"><b>${data.tablas[0]}</b></a>`;
                        let enlace2 = `<a target="_blank" href="{{ url('contable/Banco/debito/acreedores/buscar?id=${data.ids[1]}')}}"> <b>${data.tablas[1]}</b> </a>`;
                        let enlace3 = `<a target="_blank" href="{{ url('contable/cruce/valores?id=${data.ids[2]}')}}"><b>${data.tablas[2]}</b></a>`;
                        let enlace4 = `<a target="_blank" href="{{ url('contable/acreedores/documentos/retenciones/buscar?id=${data.ids[3]}')}}"><b>${data.tablas[3]}</b></a>`;
                        let enlace5 = `<a target="_blank" href="{{ url('contable/comp_egreso_masivo/buscar?id=${data.ids[4]}')}}"><b>${data.tablas[4]}</b></a>`;
                        let texto = `Existen algunos ${enlace} ${enlace2} ${enlace3} ${enlace4} ${enlace5} generados con esta factura`;

                        return mensajes("error", texto);

                    } else {
                        eliminarActivoRegistro(id, estado, tipo);
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    })
}
async function eliminarActivoRegistro(id, estado, tipo) {

    try {
        const {
            value: text
        } = await Swal.fire({
            input: 'textarea',
            inputPlaceholder: 'Ingrese motivo de anulación...',
            inputAttributes: {
                'aria-label': 'Ingrese motivo de anulación'
            },
            showCancelButton: true
        })

        if (text) {
            $.ajax({
                type: 'get',
                url: "v3/" + $("#controlador").val() + "/anularActivo",
                datatype: 'json',
                data: {
                    'id': id,
                    'estado': estado,
                    'tipo': tipo
                },
                success: function (data) {
                    if (data.status) {
                        mensajes("success", "Factura anulada");
                        $('#tableIngresoActivos').DataTable().ajax.reload();
                    } else {
                        mensajes("error", data.msj);
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });

        }
    } catch (err) {
        console.log(err);
    }
}

function cambioCargarAutorizacion() {
    let id = document.getElementById("id_proveedor").value;
    var json = ajax("v3/" + $("#controlador").val() + "/traerDatosProveedor", { id: id }, 'json');
    if (json.responseJSON != '') {
        json = json.responseJSON;
        $("#serie_factura").val(json.serie);
        $('#nro_autorizacion').val(json.autorizacion);
    }
}

function llenarCampoOrden() {
    let proveedor = document.getElementById("id_proveedor");
    let serie = document.getElementById("serie_factura").value;
    let secuencia = document.getElementById("secuencia").value;
    let value = proveedor.options[proveedor.selectedIndex].text;
    if (value != '') {
        document.getElementById("concepto").value = `ACT-FA # ${value} ${serie}-${secuencia}`
    }
}


$("#btnGuardarOrden").on("click", function () {
    let tbody = document.getElementById('agregar_cuentas');
    let filas = tbody.rows.length;

    if (document.getElementById("id_proveedor").value == '') {
        return mensajes("warning", "Ingrese el proveedor");
    }
    if (document.getElementById("serie_factura").value == '') {
        return mensajes("warning", "Ingrese la serie");
    }
    if (document.getElementById("secuencia").value == '') {
        return mensajes("warning", "Ingrese la secuencia");
    }
    if (document.getElementById("concepto").value == '') {
        return mensajes("warning", "Ingrese el concepto");
    }
    if (filas < 0) {
        return mensajes("error", "Ingrese los detalles");
    }


    mensajes('question', $("#pregunta").val(), 'frm_', 'action', '', $('#base_url').val() + 'v3/compras/addActivos', '');
});

function verDocumentoPreOrden(id) {
    window.open($("#base_url").val() + "/v3/compras/nuevaFacturaOrden?id=" + id, "_self");

}
