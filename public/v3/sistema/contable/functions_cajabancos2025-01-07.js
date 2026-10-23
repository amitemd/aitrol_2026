var fila = 0;
var fila_deuda = 0;
var conciliado = 0;
var FilaNotaDebitoCajaBanco = 0;
$(document).ready(function () {
    if ($("#submodulo_").val() == "agregarCociliacion") {
        $(".botonMenu").trigger("click");
    } else if ($("#submodulo_").val() == "crearDepositoBancario") {
        $('#id_cuenta_origen option').filter(function () {
            return normalizeText($(this).text()) === 'cajageneral';
        }).prop('selected', true);
        $('#id_cuenta_origen').change(function () {
            const selectedText = $("#id_cuenta_origen option:selected").text();
            $('#message').text('Has seleccionado: ' + selectedText);
        });
        $('#id_cuenta_origen').change();
    }
    $('#elementTour').val('hero-contenido,hero-accion,hero-agregar,hero-agregarlinea');
    try {
        $("#tableConciliacion").DataTable({
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
            order: [[1, "desc"]],
            iDisplayLength: 50,
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
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [{
                data: 'nombre'
            }, {
                className: 'text-center',
                data: 'anio'
            }, {
                className: 'text-center',
                data: 'mes'
            }, {
                className: 'text-right',
                data: 'id',
                render: function (data, type, row) {
                    var libros = row.valores.split('|');
                    return libros[0];
                }
            }, {
                className: 'text-right',
                data: 'id',
                render: function (data, type, row) {
                    var libros = row.valores.split('|');
                    if (libros[1] !== undefined) {
                        return libros[1];
                    }
                    else {
                        return 0;
                    }
                }
            }, {
                className: 'text-center',
                data: 'usuariocrea'
            }, {
                className: 'text-center',
                data: 'created_at',
                render: function (d, t, r) {
                    return getfecha(r.created_at, null, '-');
                }
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#anular").val();
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verConciliacion(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><a class="dropdown-item" href="javascript:;" onclick="pdfDatosConciliacion(\'' + row.id_caja_banco + '\',\'' + row.anio + '\',\'' + row.mes + '\');"><i class="fa fa-file-pdf" style="color:red;"></i> PDF</a><a class="dropdown-item" href="javascript:;" onclick="excelDatosConciliacion(\'' + row.id_caja_banco + '\',\'' + row.anio + '\',\'' + row.mes + '\');"><i class="fas fa-file-excel" style="color:green;"></i> Excel</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','ConciliacionBancaria');\">" + accion + "</a></div></div>";
                    return botones;
                },
            }],
        });
        $("#tableTransferenciasBancarias").DataTable({
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
            order: [[1, "desc"]],
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
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
                type: 'post',
                data: {
                    desde: $("#desde").val(),
                    hasta: $("#hasta").val(),
                },
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
                    return '<a href="javascript:void(0)" onclick="visualizarAsiento(' + row.id_asiento + ')">' + row.id_asiento + '</a>';
                }
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var numcheque = row.numcheque === null ? '' : row.numcheque;
                    return numcheque;
                }
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    return getfecha(row.fecha_cheque, null, '-');
                }
            }, {
                data: 'concepto',
            }, {
                className: 'text-right',
                data: 'valor_destino',
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var estado = '<label class="text-success">' + $('#activo').val() + '</label>';
                    if (row.estado == 0) {
                        estado = '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                    }
                    return estado;
                }
            }, {
                className: 'text-center',
                data: 'usuariocrea',
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verTransferenciaBancaria(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><a class="dropdown-item" href="javascript:;" onclick="pdfTransferencia(\'' + row.id + '\');"><i class="fa fa-file-pdf" style="color:red;"></i> PDF</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoTrasferenciaB(\'' + row.id + "','" + row.estado + "','TransferenciaBancaria');\">" + accion + "</a></div></div>";
                    return botones;
                },
            }],
        });
        $("#tableCajaBancos").DataTable({
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
            order: [[1, "asc"]],
            iDisplayLength: 50,
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
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
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
                data: 'nombre'
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var cadena = row.ucnombre;
                    var letra = cadena.charAt(0);
                    return letra + ". " + row.ucapellido;
                },
            }, {
                className: 'text-center',
                data: 'created_at',
            }, {
                className: 'text-center',
                data: 'updated_at'
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = '<label class="text-success">' + $('#activo').val() + '</label>';
                    if (row.estado == 0) {
                        estado = '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                    }
                    return estado;
                }
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $('#activar').val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verBanco(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + '\',\'' + row.estado + '\',\'ConfigNomina\');">' + accion + '</a></div></div>';
                    return botones;
                }
            }],
        });
        $("#tableCrearCajaBancos").DataTable({
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
            iDisplayLength: 50,
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
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [{
                data: 'codigo'
            }, {
                data: 'nombre'
            }, {
                className: 'text-center',
                data: 'clase',
                render: function (data, type, row) {
                    var clase = row.clase == 1 ? 'Cuenta Bancaria' : 'Cuenta de Caja';
                    return clase;
                }
            }, {
                className: 'text-center',
                data: 'grupo',

            }, {
                data: 'nombre_mayor',

            }, {
                className: 'text-center',
                data: 'nombre_sucursal',

            }, {
                data: 'formas_pago',
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = '<label class="text-success">' + $('#activo').val() + '</label>';
                    if (row.estado == 0) {
                        estado = '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                    }
                    return estado;
                }
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $('#activar').val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verCajaBanco(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + '\',\'' + row.estado + '\',\'ConfigNomina2\');">' + accion + '</a></div></div>';
                    return botones;
                }

            }],
        });
        $("#tableDepositoBancario").DataTable({
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
                url: "v3/" + $('#controlador').val() + "/allDataDepositoBacancario" + "js",
                type: 'post',
                data: {
                    desde: $("#desde").val(),
                    hasta: $("#hasta").val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [{
                className: 'text-center',
                data: 'id'
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    return '<a href="javascript:void(0)" onclick="visualizarAsiento(' + row.id_asiento + ')">' + row.id_asiento + '</a>';
                }
            }, {
                className: 'text-center',
                data: 'fecha_asiento',
                render: function (data, type, row) {
                    return getfecha(row.fecha_asiento, null, '-')
                }

            }, {
                data: 'concepto',

            }, {
                className: 'text-right',
                data: 'total_deposito',

            }, {
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
                data: 'id',
                render: function (data, type, row) {
                    var accion = '';
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDepositoBancario(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><a class="dropdown-item" href="javascript:;" onclick="descargarPdfDeposito(\'' + row.id + '\');"><i class="fas fa-file-pdf" style="color:red;"></i> PDF</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoNotaCredito(\'' + row.id + '\',\'' + row.estado + '\');">' + accion + '</a></div></div>';
                    return botones;
                }

            },
            ],
        });
        $("#tableNotaCredito").DataTable({
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
                url: "v3/" + $('#controlador').val() + "/allNotaCredito" + "js",
                type: 'post',
                data: {
                    desde: $("#desde").val(),
                    hasta: $("#hasta").val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);
                }
            },
            columns: [{
                data: 'fecha'
            }, {
                className: "text-center",
                data: "id_asiento",
                render: function (data, type, row) {
                    return ('<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + "');\">" + row.id_asiento + "</a>");
                },
            }, {
                className: "text-center",
                data: 'id',

            }, {
                data: 'BAN-NC',
            }, {
                data: 'concepto',
            }, {
                data: 'valor'
            }, {
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
                data: 'id',
            },
            {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var accion = '';
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDepositoBancario(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoNotaCredito(\'' + row.id + '\',\'' + row.estado + ');">' + accion + '</a></div></div>';
                    return botones;
                }
            },
            ],
        });
        $("#tableNotadedebitoCajaBanco").DataTable({
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
            pageLength: 50,
            autoWidth: true,
            order: [[0, "desc"]],
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
                url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
                type: "post",
                data: {
                    desde: $("#desde").val(),
                    hasta: $("#hasta").val(),
                },
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
                    data: "fecha",
                },
                {
                    className: "text-center",
                    data: "id_asiento",
                    render: function (data, type, row) {
                        return ('<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + "');\">" + row.id_asiento + "</a>");
                    },
                },
                {
                    data: "tipo",
                },
                {
                    data: "concepto",
                },
                {
                    className: "text-right",
                    data: "valor",
                },
                {
                    className: "text-center",
                    data: "estado",
                    render: function (data, type, row) {
                        var estado =
                            row.estado == 1 ? '<label class="text-success">' + $("#activo").val() + "</label>" : '<label class="text-danger">' + $("#inactivo").val() + "</label>";
                        return estado;
                    },
                },
                {
                    className: "text-center",
                    data: "usuariocrea",
                },
                {
                    data: "id",
                    render: function (data, type, row) {
                        var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
                        if (row.estado == 1) {
                            accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
                        }
                        var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verNotadeDebitoCajaBanco(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '<a class="dropdown-item" href="javascript:;" onclick="pdf_nota_debito_cj(\'' + row.id + '\');"><i class="fa fa-file-pdf" style="color:red;"></i> PDF</a>' + '<div class="dropdown-divider"></div>';
                        if (row.estado == 1) {
                            botones += '<a class="dropdown-item" href="javascript:;" onclick="anularNotaDebitoCajaBanco(\'' + row.id + "');\">" + accion + "</a>";
                        } else {
                            botones += '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' + accion + "</a>";
                        }
                        botones += "</div></div>";
                        return botones;
                    },
                },
            ],
        });
        document.getElementById('cambio').addEventListener('input', validateNumberInput);
        document.getElementById('valor').addEventListener('input', validateNumberInput);
    } catch (error) { console.log(error); }
    $('#btnGuardarBancos').click(function () {
        if ($('#nombre').val() == '') {
            mensajes('info', 'Ingrese el nombre del banco');
        } else {
            validarbanco();
        }
    });
    $('#btnGuardarCajaBanco').click(function () {
        if ($('#codigo').val() == '') {
            mensajes('info', 'Ingrese el codigo');
        } else if ($('#nombre').val() == '') {
            mensajes('info', 'Ingrese el nombre');
        } else if ($('#numero_cuenta').val() == '') {
            mensajes('info', 'Ingrese el numero de cuenta');
        } else if ($('#clase').val() == '') {
            mensajes('info', 'Seleccione la clase');
        } else if ($('#grupo').val() == '') {
            mensajes('info', 'Seleccione el grupo');
        } else if ($('#cuenta_mayor').val() == '') {
            mensajes('info', 'Seleccione la cuenta mayor');
        } else if ($('#sucursal').val() == '') {
            mensajes('info', 'Seleccione la sucursal');
        } else if ($('#divisa').val() == '') {
            mensajes('info', 'Seleccione la divisa');
        } else if ($('#formas_pago').val() == '') {
            mensajes('info', 'Seleccione la forma de pago');
        } else {
            var codigoInput = document.querySelector("input[name='codigo']");
            var nombreInput = document.querySelector("input[name='nombre']");
            if (codigoInput.readOnly || nombreInput.readOnly) {
                mensajes("question", $("#pregunta").val());
            } else {
                validarCaja_banco();
            }
        }
    });
    $('.grupo').change(function () {
        if ($('.grupo').val() != '') {
            //$('.cuenta_mayor').html('<option value="">--Seleccionar--</option>');
            $.ajax({
                url: 'v3/' + $('#controlador').val() + '/getCuentaMayor',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    id_grupo: $('.grupo').val()
                },
                async: false,
                dataType: 'html',
                success: function (html) {
                    $('#cuenta_mayor').html(html).trigger('change');
                },
                error: function (error) {
                    alert(error);
                }
            });
        }
    });
    $('#instituciones').select2({
        width: "element",
        allowClear: true,
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccionar--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/" + $("#controlador").val() + "/buscarBancos",
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
    $('.editInfor').click(function () {
        if ($('#estado_modal').val() == 0) {
            $('#modalEditInfo').css({
                display: 'initial',
                width: '600px',
                height: '350px',
                position: 'absolute',
                top: '50px',
                right: '0px',
            });
            $('#saldo_inicial').focus();
            $('#estado_modal').val(1);
        }
        else {
            $('#modalEditInfo').css({
                display: 'none',
            });
            $('#estado_modal').val(0);
        }
    });
    $("#agregarLinea").on("click", function () {
        var html = '<tr id="tr_' + fila + '">';
        html += '<td style="width: 60%;"><select class="form-control select2_cuentas" id="codigo_' + fila + '" name="codigo[]">' + '<option value="">--Seleccione--</option></select></td>';
        html += '<td style="width: 30%;"><input type="text" class="form-control text-right" id="debe_' + fila + '" name="debe[]" onblur="calcular();" value="0.00" /></td>';
        html += '<td style="width: 10%;"align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFila(\'' + fila + "')\"></i></td>";
        html += '</tr>';
        fila++;
        calcular();
        $("#detalleNotaDebito").before(html);
        $(".select2_cuentas").select2({
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
                url: "v3/" + $("#controlador").val() + "/getAllCuentas",
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
    });
    $("#btnguardarNotaDebito").click(function () {
        if ($("#id_banco").val() == "") {
            return mensajes("warning", "Seleccione un banco para continuar...");
        } else if ($("#fecha").val() == "") {
            return mensajes("warning", "Seleccione la fecha de emisión para continuar...");
        } else if ($("#concepto_c").val() == "") {
            return mensajes("warning", "Debe ingresar el concepto de la nota de debito para continuar...");
        } else if ($("#id_acreedor").val() == "") {
            return mensajes("warning", "Seleccione el proveedor de la nota de debito para continuar...");
        } else if ($("#id_divisa").val() == "") {
            return mensajes("warning", "Seleccione la divisa de la nota de debito para continuar...");
        } else if ($("#valor").val() == "") {
            return mensajes("warning", "Ingrese el valor de la nota de debito para continuar...");
        } else if (fila_deuda <= 0) {
            return mensajes("warning", "Agregue deudas para continuar...");
        } else {
            mensajes("question", $("#pregunta").val(), "frm_", "action", $("#base_url").val() + "/v3/" + $("#controlador").val() + "/notadedebito");
        }
    });
    $("#btnguardarNotaDebitoCajaBanco").click(function () {
        if ($("#id_banco").val() == "") {
            return mensajes("warning", "Seleccione un banco para continuar...");
        } else if ($("#fecha").val() == "") {
            return mensajes("warning", "Seleccione la fecha de emisión para continuar...");
        } else if ($("#concepto").val() == "") {
            return mensajes("warning", "Debe ingresar el concepto de la nota de debito para continuar...");
        } else if ($("#id_divisa").val() == "") {
            return mensajes("warning", "Seleccione la divisa de la nota de debito para continuar...");
        } else if (FilaNotaDebitoCajaBanco <= 0) {
            return mensajes("warning", "Agregue detalles para continuar...");
        } else {
            mensajes("question", $("#pregunta").val(), "frm_", "action", $("#base_url").val() + "/v3/" + $("#controlador").val() + "/notadedebito");
        }
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
                $('.select2_provi').val(params.id).trigger('change');
                return { term: params.term };
            },
            processResults: function (response) {
                return { results: response };
            },
            error(error) {
                console.log(error);
            },
        },
    });
    $('.js-example-basic-multiple').select2({
        placeholder: "Buscar tipo pago"
    });
    if ($('#idMes').val() !== undefined && $('#idMes').val() != '') {
        if ($('#id_caja_banco').val() != '') {
            var datosBanco = $('#id_caja_banco').val().split('|');
            $('#instituciones').html('<option value="' + datosBanco[0] + '">' + datosBanco[2] + ' | ' + datosBanco[1] + '</option>').toggle('change');
            $('#fecha_periodo').val(datosBanco[3] + '-' + datosBanco[4]);
            buscarDatosConciliacion();
            $('.cheDepositos').prop('disabled', true);
            $('.cheTransferenciasAcreditadas').prop('disabled', true);
            $('.cheTransferenciasDebitadas').prop('disabled', true);
            $('.cheNotaDebito').prop('disabled', true);
            $('.cheNotasCredito').prop('disabled', true);
            $('.cheNotaDebitoBancaria').prop('disabled', true);
            $('.cheEgresos').prop('disabled', true);
            $('.cheEgresosVarios').prop('disabled', true);
            $('.cheEgresosMasivos').prop('disabled', true);
        }
    }
    $("#id_proveedor").select2({
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
                "X-CSRF-TOKEN": $("#token").val(),
            },
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    term: params.term,
                };
            },
            processResults: function (response) {
                return {
                    results: response,
                };
            },
            error(error) { },
            cache: true,
        },
    });
    $("#id_proveedor_modal").select2({
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
                "X-CSRF-TOKEN": $("#token").val(),
            },
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    term: params.term,
                };
            },
            processResults: function (response) {
                return {
                    results: response,
                };
            },
            error(error) { },
            cache: true,
        },
    });
    $('#btnGuardarTransferenciaBancaria').click(function () {
        if ($('#concepto').val() == '') {
            mensajes('info', 'Ingrese un concepto para la transferencia');
        } else if ($('#sucursal').val() == '') {
            mensajes('info', 'Seleccione una sucursal para la transferencia');
        } else if ($('#id_cuenta_origen').val() == '') {
            mensajes('info', 'Seleccione la cuenta de origen para la transferencia');
        } else if ($('#valor_origen').val() <= 0) {
            mensajes('info', 'El valor de la cuenta de origen debe ser mayor a cero');
        } else if ($('#id_cuenta_destino').val() == '') {
            mensajes('info', 'Seleccione la cuenta de destino para la transferencia');
        } else if ($('#glosa').val() == '') {
            mensajes('info', 'Ingrese la glosa de destino para la transferencia');
        } else {
            mensajes('question', $('#pregunta').val());
        }
    });
    $("#agregarlineaNotadeDebitoCajaBanco").click(function () {
        var CuentaCombo = "";
        var json = ajax("v3/" + $("#controlador").val() + "/getPlandeCuentas", null, 'json');
        if (json.responseJSON != '') {
            json = json.responseJSON;

            $.each(json, function (i, item) {
                CuentaCombo += '<option value="' + item.plan + ' | ' + item.nombre + '">' + item.plan + ' | ' + item.nombre + "</option>";
            });
            var html_ = '<tr style="background-color: white" id="trNotaDebitoCB_' + FilaNotaDebitoCajaBanco + '">';
            html_ += '<td style="min-width: 11%;"><select class="form-control select2_" style="width:100%;" id="id_det_nd_' + FilaNotaDebitoCajaBanco + '" name="id_det_nd[]"><option value="">--Seleccione--</option>' + CuentaCombo + "</select></td>";
            html_ += '<td style="min-width: 10%;"><input type="text" class="form-control text-right" id="debe' + FilaNotaDebitoCajaBanco + ' " name="debe[]" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0.00" onchange="calcula_total_nota_debito_cajabanco(' + FilaNotaDebitoCajaBanco + ')" /></td>';
            html_ += '<td style="min-width: 9%;" align="center"><a href="javascript:;" class="btn btn-danger" onclick="EliminarFilaNotaDebitoCB(\'' + FilaNotaDebitoCajaBanco + '\');"><i class="fa fa-trash-alt"></i></a></td>';
            html_ += "</tr>";
            $("#detalleNotadeDebitoCajaBanco").append(html_);
            $(".select2_").select2();
            FilaNotaDebitoCajaBanco++;
        }

    });
    $('#btnGuardarRetencion').click(function () {
        if ($('#id_proveedor_modal').val() == '') {
            mensajes('warning', 'Seleccione un proveedor por favor');
        } else if ($('#autorizacion_modal').val() == '') {
            mensajes('warning', 'Ingrese la autorización por favor');
        } else if ($('#base_fuente_modal').val() == '') {
            mensajes('warning', 'Ingrese la base de la fuente');
        } else if ($('#autorizacion_modal').val().length != 10 && $('#autorizacion_modal').val().length != 49) {
            mensajes('warning', 'El número de autorización debe ser de 10 o 49 digitos');
        } else if ($('#porcentaje_fuente_modal').val() == '') {
            mensajes('warning', 'Seleccione el porcentaje de la fuente');
        } else {
            mensajes('question', $('#pregunta').val(), 'frm_retencion', 'action_retencion');
            $('#myModal').modal('toggle');
        }
    });
    $('#autorizacion_modal').blur(function () {
        var data = {
            autorizacion: $('#autorizacion_modal').val()
        };
        var json = ajax('v3/' + $('#controlador').val() + '/verificarAutorizacion', data, 'json');
        if (json.responseJSON !== null) {
            json = json.responseJSON;
            if (json.status == 1) {
                mensajes('info', 'El número de autorización que intenta ingresar ya se encuentra en la base de datos');
                $('#autorizacion_modal').val('');
                $('#autorizacion_modal').focus();
            }
        }
    });
    $('#porcentaje_fuente_modal').change(function () {
        var datos = $('#porcentaje_fuente_modal').val().split('|');
        var fuenteretenida = (datos[1] * parseFloat($('#base_fuente_modal').val())) / 100;
        $('#fuente_retenida_modal').val(parseFloat(fuenteretenida).toFixed(2));
    });
    $('#porcentaje_iva_modal').change(function () {
        var datos = $('#porcentaje_iva_modal').val().split('|');
        var ivaretenida = (datos[1] * parseFloat($('#base_iva_modal').val())) / 100;
        $('#iva_retenido_modal').val(parseFloat(ivaretenida).toFixed(2));
    });
});
function calcula_total_nota_debito_cajabanco() {
    const inputs = document.querySelectorAll('input[name="debe[]"]');
    let total_nd = 0;
    inputs.forEach(input => {
        total_nd += parseFloat(input.value) || 0;

    });
    $('#total_detalle').val(total_nd.toFixed(2));
    $('#valor_total').val(total_nd.toFixed(2));
}
function EliminarFilaNotaDebitoCB(id) {
    $("#trNotaDebitoCB_" + id).remove();
    calcula_total_nota_debito_cajabanco();
    FilaNotaDebitoCajaBanco--;
}
function verConciliacion(id) {
    window.open($('#base_url').val() + '/v3/' + $('#controlador').val() + '/agregarConciliacion?i=' + id, '_self');
}
function validarCaja_banco() {
    $.ajax({
        url: "v3/" + $("#controlador").val() + "/verificarNombrecajabanco",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
            codigo: $("#codigo").val(),
            nombre: $("#nombre").val(),
        },
        dataType: "json",
        success: function (json) {
            if (json != "") {
                if (json.status == "error") {
                    return mensajes("warning", json.message);
                } else {
                    mensajes("question", $("#pregunta").val());
                }
            }
        },
        error: function () { },
    });
}
function cargar_autorizacion(e) {
    $.ajax({
        url: "v3/" + $('#controlador').val() + '/traerAutorizaciones',
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
            id: e.value,
        },
        type: 'POST',
        datatype: 'json',
        success: function (data) {
            let array = data.autorizaciones;
            if (array.length == 0) {
                $('#div_autorizacion').html('<label class="font-normal">Autorización</label><input type="text" name="autorizacion" onkeypress="return isNumberKey(event)" maxlength="49" id="autorizacion" class="form-control" placeholder="Autorización">');
            } else {
                $('#div_autorizacion').html('<label class="font-normal">Autorización</label><select name="autorizacion" id="autorizacion" class="form-control select2_autorizacion" onchange="cargar_datos_de_autorizacion(this)"> <option value="">Seleccione</option></select>');
                //cargar_autorizacion('id_proveedor');
                $('.select2_autorizacion').select2();
            }
            $("#autorizacion").find('option:not(:first)').remove();
            let est = '';
            let emi = '';
            let num = '';
            $.each(array, function (key, value) {
                est = value.autorizacion.substr(24, 3);
                emi = value.autorizacion.substr(27, 3);
                num = value.autorizacion.substr(30, 9);
                $("#autorizacion").append('<option value=' + value.id + '>' + value.autorizacion + ' ' + est + '-' + emi + '-' + num + '</option>');
            });
        },
        error: function (data) {
            console.error(data.responseText);
        }
    });
}
function cargar_datos_de_autorizacion(e) {
    $.ajax({
        url: "v3/" + $('#controlador').val() + '/traerDatosGenera',
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        type: 'POST',
        datatype: 'json',
        data: {
            id: e.value,
        },
        success: function (data) {
            let datos = data.dato;
            if (datos != null) {
                var porF = '';
                var porI = '';
                try {
                    var porF_ = datos.porcentaje_fuente.split('|');
                    porF = porF_[0];
                } catch (e) {
                    porF = 0;
                }
                try {
                    var porI_ = datos.porcentaje_iva.split('|');
                    porI = porI_[0];
                } catch (e) {
                    porI = 0;
                }
                $("#base_iva").val(datos.base_iva);
                $("#base_ret_fuente").val(datos.base_retencion);
                $("#id_base_iva").val(porI).trigger('change');
                $("#id_base_ret_fuente").val(porF).trigger('change');
            } else {
                $("#base_iva").val("");
                $("#base_ret_fuente").val("");
                $("#id_base_iva").val("");
                $("#id_base_ret_fuente").val("");
            }
            calcularRetencionesTransferencia();
        },
        error: function (error) {
            console.error(error);
        }
    });
}
function calcularRetencionesTransferencia() {
    var valor_origen = $("#valor_origen").val();
    var comision_tarjeta = $("#comision_tarjeta").val();
    var retencion_fuente = $("#retencion_fuente").val();
    var valores_adicionales = $("#valores_adicionales").val();
    var valores_destino = 0;
    var total = 0;
    var subtotal = 0;
    var base_ret_fuente = $("#base_ret_fuente").val();
    var base_iva = $("#base_iva").val();
    var id_base_iva = 0;
    var id_base_ret_fuente = 0;
    if ($("#id_base_iva option:selected").text() != '(Ninguno)') {
        id_base_iva = $("#id_base_iva option:selected").text();
    }
    if ($("#id_base_ret_fuente option:selected").text() != '(Ninguno)') {
        id_base_ret_fuente = $("#id_base_ret_fuente option:selected").text()
    }
    var retencion_iva = 0;
    var retencion_fuente = 0;
    if (base_ret_fuente !== 0 && id_base_ret_fuente !== 0) {
        retencion_fuente = (parseFloat(base_ret_fuente) * parseFloat(id_base_ret_fuente)) / 100;
        retencion_fuente = redondea_precio(retencion_fuente, 2);
        $("#retencion_fuente").val(retencion_fuente);
    }
    if (id_base_iva !== 0 && base_iva !== 0) {
        retencion_iva = (parseFloat(base_iva) * parseFloat(id_base_iva)) / 100;
        retencion_iva = redondea_precio(retencion_iva, 2);
        $("#iva_retenido").val(retencion_iva);
    }
    subtotal = redondea_precio(parseFloat(comision_tarjeta) + parseFloat(retencion_fuente) + parseFloat(retencion_iva) + parseFloat(valores_adicionales), 2);
    total = redondea_precio(parseFloat(valor_origen) - parseFloat(subtotal), 2);
    if (total == 'NaN') {
        total = 0;
    }
    // alert((retencion_fuente));
    $("#valor_destino").val(redondea_precio(total, 2));
    valores_destino = parseFloat(subtotal) + parseFloat(total);
    valores_destino = redondea_precio(valores_destino, 2);
    $("#total").val(redondea_precio(valores_destino, 2));
}
function agregarRetencion() {
    $('#titulo_modal_retencion').html('<i class="fas fa-edit"></i> Agregar retención');
    $('#myModal').modal('toggle');
}
function redondea_precio(value, decimales = 3) {
    value = +value;
    if (isNaN(value)) return NaN; // Shift 
    value = value.toString().split('e');
    value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2))); // Shift back 
    value = value.toString().split('e');
    return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
}
function seleccionarTodoosito() {
    /*$("#tableDepositos").find("tr").each(function () {
        var comidaID = $(this).val();
        alert(comidaID);
    });*/
    /*if ($('#cheDeposito').val() == 0) {
        $("#tableDepositos tbody").each(function (el) {
            $('.cheDeposito').attr('checked', true);
        });
        $('#cheDeposito').val(1);
    } else {
        $("#tableDepositos tbody").each(function (el) {
            $('.cheDeposito').attr('checked', false);
        });
        $('#cheDeposito').val(0);
        calcularDatosConciliacion();
    }*/
}
function buscarDatosConciliacion() {
    if ($('#instituciones').val() == '') {
        return mensajes('info', 'Seleccione un banco o institución financiera');
    }
    var data = {
        banco: $('#instituciones').val(),
        documento: $('#tipo_documento').val(),
        estado: $('#estado').val(),
        periodo: $('#fecha_periodo').val(),
    };
    var html = ajax('v3/' + $('#controlador').val() + '/taerConciliacionPeriodo', data, 'html');
    if (html.responseText != '') {
        $('#lblSaldoInicial').attr('readonly', false);
        html = html.responseText.split('¬');
        $('#depositos').html(html[0]);
        $('#transferenciasacreditadas').html(html[1]);
        $('#debitos').html(html[2]);
        $('#creditos').html(html[3]);
        $('#debitoBancario').html(html[4]);
        $('#egresos').html(html[5]);
        $('#egresosVarios').html(html[6]);
        $('#egresosMasivos').html(html[7]);
        $('#lblSaldoInicial').val(html[10]);
        $('#saldoAnteriorResumenLibros').val(html[9]);
        $('#htmlPendientes').html(html[12]);
        $('#htmlPendientesAgregados').html(html[13]);
        $('#transferenciasadebitadas').html(html[14]);
        var totalesLibros = html[11].split('|');
        conciliado = totalesLibros[8];
        $('#depositoResumenLibros').val(totalesLibros[0]);
        $('#acreditadosResumenLibros').val(parseFloat(parseFloat(totalesLibros[1]) + parseFloat(totalesLibros[3])).toFixed(2));
        $('#chequesGiradosResumenLibros').val(parseFloat(parseFloat(totalesLibros[5]) + parseFloat(totalesLibros[6])).toFixed(2));
        $('#debitadosResumenLibros').val(parseFloat(parseFloat(totalesLibros[2]) + parseFloat(totalesLibros[4]) + parseFloat(totalesLibros[7]) + parseFloat(totalesLibros[9])).toFixed(2));
        $('#saldoActualResumenLibros').val(parseFloat(parseFloat(html[9]) + parseFloat(totalesLibros[0]) + parseFloat(totalesLibros[1]) + parseFloat(totalesLibros[3]) - (parseFloat(totalesLibros[2]) + parseFloat(totalesLibros[4]) + parseFloat(totalesLibros[7])) - (parseFloat(totalesLibros[5]) + parseFloat(totalesLibros[6]) + parseFloat(totalesLibros[9]))).toFixed(2));
        calcularDatosConciliacion();
        if (conciliado == 1) {
            $('.cheDepositos').prop('disabled', true);
            $('.cheTransferenciasAcreditadas').prop('disabled', true);
            $('.cheTransferenciasDebitadas').prop('disabled', true);
            $('.cheNotaDebito').prop('disabled', true);
            $('.cheNotasCredito').prop('disabled', true);
            $('.cheNotaDebitoBancaria').prop('disabled', true);
            $('.cheEgresos').prop('disabled', true);
            $('.cheEgresosVarios').prop('disabled', true);
            $('.cheEgresosMasivos').prop('disabled', true);
            $('.chePendientesAgregados').prop('disabled', true);
        }
        $('.ocultar').css('display', 'initial');
    }
}
function calcularPendientesAgregados(id, valor) {
    var totalPendientes = $('#totalPendientesAgregados').html();
    console.log(totalPendientes);
    if ($('#chePendientes_' + id).is(':checked')) {
        totalPendientes = parseFloat(totalPendientes) + parseFloat(valor);
    }
    else {
        totalPendientes = parseFloat(totalPendientes) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionPendientes', data, 'json');
    if (totalPendientes < 0)
        totalPendientes = 0;
    $('#totalPendientesAgregados').html(parseFloat(totalPendientes).toFixed(2));
    calcularDatosConciliacion();
}
function calcularPendientes(id, valor) {
    var totalPendientes = $('#totalPendientes').html();
    if ($('#chePendientes_' + id).is(':checked')) {
        totalPendientes = parseFloat(totalPendientes) + parseFloat(valor);
    }
    else {
        totalPendientes = parseFloat(totalPendientes) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionPendientes', data, 'json');
    if (totalPendientes < 0)
        totalPendientes = 0;
    $('#totalPendientes').html(parseFloat(totalPendientes).toFixed(2));
    calcularDatosConciliacion();
}
function calcularDepositos(id, valor) {
    var totalDepositos = $('#totalDepositos').html();
    if ($('#chedeposito_' + id).is(':checked')) {
        totalDepositos = parseFloat(totalDepositos) + parseFloat(valor);
    }
    else {
        totalDepositos = parseFloat(totalDepositos) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionDeposito', data, 'json');
    if (totalDepositos < 0)
        totalDepositos = 0;
    $('#totalDepositos').html(parseFloat(totalDepositos).toFixed(2));
    calcularDatosConciliacion();
}
function calcularTransferenciasAcreditadas(id, valor) {
    var totalTransferenciasAcreditadas = $('#totalTransferenciasAcreditadas').html();
    if ($('#chetransferencia_' + id).is(':checked')) {
        totalTransferenciasAcreditadas = parseFloat(totalTransferenciasAcreditadas) + parseFloat(valor);
    }
    else {
        totalTransferenciasAcreditadas = parseFloat(totalTransferenciasAcreditadas) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionTransferencia', data, 'json');
    if (totalTransferenciasAcreditadas < 0)
        totalTransferenciasAcreditadas = 0;
    $('#totalTransferenciasAcreditadas').html(parseFloat(totalTransferenciasAcreditadas).toFixed(2));
    calcularDatosConciliacion();
}

function cambiarEstadoNotaCredito(id, estado) {


    Swal.fire({
        title: '¿Desea Anular la nota de crédito?',
        text: 'No puedes revertir esta acccion!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Si"
    }).then((result) => {
        if (result.isConfirmed) {
            testNotaCredito(id,estado);
        }
    })
}

async function testNotaCredito(id, estado) {
    try {
        const {
            value: text
        } = await Swal.fire({
            input: 'textarea',
            inputPlaceholder: 'Ingrese motivo de anulación...',
            inputAttributes: {
                'aria-label': 'Ingrese motivo de anulación'
            },
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Anular'
        })

        if (text) {
            $.ajax({
                url: "v3/" + $('#controlador').val() + "/anularNotaCredito",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    id: id,
                    text: text,
                    estado: estado
                },
                datatype: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        mensajes('success', data.msj);

                    } else {
                        mensajes('error', data.msj);
                    }
                },
                error: function (json) {
                    console.log(json);
                },
            });
        }
    } catch (err) {
        console.log(err);
    }
}


function calcularTransferenciasDebitadas(id, valor, tipo) {
    var totalTransferenciasDebitadas = $('#totalTransferenciasDebitadas').html();
    if ($('#chetransferencia_' + id).is(':checked')) {
        totalTransferenciasDebitadas = parseFloat(totalTransferenciasDebitadas) + parseFloat(valor);
    }
    else {
        totalTransferenciasDebitadas = parseFloat(totalTransferenciasDebitadas) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val(),
        tipo: tipo
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionTransferencia', data, 'json');
    if (totalTransferenciasDebitadas < 0)
        totalTransferenciasDebitadas = 0;
    $('#totalTransferenciasDebitadas').html(parseFloat(totalTransferenciasDebitadas).toFixed(2));
    calcularDatosConciliacion();
}
function calcularNotaDebito(id, valor) {
    var totalNotaDebito = $('#totalNotaDebito').html();
    if ($('#chenotaDebito_' + id).is(':checked')) {
        totalNotaDebito = parseFloat(totalNotaDebito) + parseFloat(valor);
    }
    else {
        totalNotaDebito = parseFloat(totalNotaDebito) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionNotaDebito', data, 'json');
    if (totalNotaDebito < 0)
        totalNotaDebito = 0;
    $('#totalNotaDebito').html(parseFloat(totalNotaDebito).toFixed(2));
    calcularDatosConciliacion();
}
function calcularNotaCredito(id, valor) {
    var totalNotaCredito = $('#totalNotaCredito').html();
    if ($('#chenotaCredito_' + id).is(':checked')) {
        totalNotaCredito = parseFloat(totalNotaCredito) + parseFloat(valor);
    }
    else {
        totalNotaCredito = parseFloat(totalNotaCredito) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionNotaCredito', data, 'json');
    if (totalNotaCredito < 0)
        totalNotaCredito = 0;
    $('#totalNotaCredito').html(parseFloat(totalNotaCredito).toFixed(2));
    calcularDatosConciliacion();
}
function calcularDebitoBancario(id, valor) {
    var totalDebitoBancario = $('#totalDebitoBancario').html();
    if ($('#chedebitobancario_' + id).is(':checked')) {
        totalDebitoBancario = parseFloat(totalDebitoBancario) + parseFloat(valor);
    }
    else {
        totalDebitoBancario = parseFloat(totalDebitoBancario) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionDebitoBancario', data, 'json');
    if (totalDebitoBancario < 0)
        totalDebitoBancario = 0;
    $('#totalDebitoBancario').html(parseFloat(totalDebitoBancario).toFixed(2));
    calcularDatosConciliacion();
}
function calcularEgresos(id, valor) {
    var totalEgresos = $('#totalEgresos').html();
    if ($('#cheEgresos_' + id).is(':checked')) {
        totalEgresos = parseFloat(totalEgresos) + parseFloat(valor);
    }
    else {
        totalEgresos = parseFloat(totalEgresos) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionEgresos', data, 'json');
    if (totalEgresos < 0)
        totalEgresos = 0;
    $('#totalEgresos').html(parseFloat(totalEgresos).toFixed(2));
    calcularDatosConciliacion();
}
function calcularEgresosVarios(id, valor) {
    var totalEgresosVarios = $('#totalEgresosVarios').html();
    if ($('#cheEgresosVarios_' + id).is(':checked')) {
        totalEgresosVarios = parseFloat(totalEgresosVarios) + parseFloat(valor);
    }
    else {
        totalEgresosVarios = parseFloat(totalEgresosVarios) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionEgresosVarios', data, 'json');
    if (totalEgresosVarios < 0)
        totalEgresosVarios = 0;
    $('#totalEgresosVarios').html(parseFloat(totalEgresosVarios).toFixed(2));
    calcularDatosConciliacion();
}
function calcularEgresosMasivos(id, valor) {
    var totalEgresosMasivos = $('#totalEgresosMasivos').html();
    if ($('#cheEgresosMasivos_' + id).is(':checked')) {
        totalEgresosMasivos = parseFloat(totalEgresosMasivos) + parseFloat(valor);
    }
    else {
        totalEgresosMasivos = parseFloat(totalEgresosMasivos) - parseFloat(valor);
    }
    var data = {
        id: id,
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    ajax('v3/' + $('#controlador').val() + '/verificarConciliacionEgresosMasivos', data, 'json');
    if (totalEgresosMasivos < 0)
        totalEgresosMasivos = 0;
    $('#totalEgresosMasivos').html(parseFloat(totalEgresosMasivos).toFixed(2));
    calcularDatosConciliacion();
}
function calcularDatosConciliacion() {
    var saldoInicialBancos = $('#lblSaldoInicial').val();
    var totalPendientes = $('#totalPendientes').html();
    var totalPendientesAgregados = $('#totalPendientesAgregados').html();
    var totalDepositos = $('#totalDepositos').html();
    var totalNotaDebito = $('#totalNotaDebito').html();
    var totalNotaCredito = $('#totalNotaCredito').html();
    var totalTransferenciasAcreditadas = $('#totalTransferenciasAcreditadas').html();
    var totalTransferenciasDebitadas = $('#totalTransferenciasDebitadas').html();
    var totalDebitoBancario = $('#totalDebitoBancario').html();
    var totalEgresos = $('#totalEgresos').html();
    var totalEgresosVarios = $('#totalEgresosVarios').html();
    var totalEgresosMasivos = $('#totalEgresosMasivos').html();
    var ingresos = (parseFloat(parseFloat(totalDepositos) + parseFloat(totalTransferenciasAcreditadas) + parseFloat(totalNotaCredito)));
    var egresos = parseFloat(parseFloat(totalNotaDebito) + parseFloat(totalDebitoBancario) + parseFloat(totalEgresos) + parseFloat(totalEgresosVarios) + parseFloat(totalEgresosMasivos) + parseFloat(totalTransferenciasDebitadas));
    $('#lblTotalIngresos').html('$ ' + parseFloat(ingresos).toFixed(2));
    $('#lblTotalEgresos').html('$ ' + parseFloat(egresos).toFixed(2));
    var diferencia = 0;
    $('#saldoAnteriorResumenBancos').val(parseFloat(saldoInicialBancos).toFixed(2));
    $('#depositoResumenBancos').val(parseFloat(totalDepositos).toFixed(2));
    $('#acreditadosResumenBancos').val(parseFloat(parseFloat(totalTransferenciasAcreditadas) + parseFloat(totalNotaCredito)).toFixed(2));
    $('#chequesGiradosResumenBancos').val(parseFloat(parseFloat(totalEgresos) + parseFloat(totalEgresosVarios)).toFixed(2));
    $('#debitadosResumenBancos').val(parseFloat(parseFloat(totalNotaDebito) + parseFloat(totalDebitoBancario) + parseFloat(totalEgresosMasivos) + parseFloat(totalTransferenciasDebitadas)).toFixed(2));
    $('#saldoActualResumenBancos').val(parseFloat(parseFloat(saldoInicialBancos) + parseFloat(totalDepositos) + parseFloat(totalTransferenciasAcreditadas) + parseFloat(totalNotaCredito) - (parseFloat(totalNotaDebito) + parseFloat(totalDebitoBancario) + parseFloat(totalEgresosMasivos)) - (parseFloat(totalTransferenciasDebitadas)) - (parseFloat(totalEgresos) + parseFloat(totalPendientes) + parseFloat(totalPendientesAgregados) + parseFloat(totalEgresosVarios))).toFixed(2));

    diferencia = (parseFloat($('#saldoActualResumenBancos').val()) - parseFloat($('#saldoActualResumenLibros').val()).toFixed(2));

    $('#lblDiferencia').html('');
    var parent1 = '';
    var parent2 = '';
    if (diferencia != 0) {
        $('.fa-check-circle')
            .removeClass('fa-check-circle')
            .addClass('fa-exclamation-triangle')
            .css({ color: 'orange' });
    } else {
        $('.fa-exclamation-triangle')
            .removeClass('fa-exclamation-triangle')
            .addClass('fa-check-circle')
            .css({ color: 'green' });
    }
    if (diferencia < 0) {
        parent1 = '(';
        parent2 = ')';
        diferencia = diferencia * -1;
    }
    $('#lblDiferencia').html('$ ' + parent1 + parseFloat(diferencia).toFixed(2) + parent2);
}
function finalizarConciliacion() {

    if ($('#instituciones').val() == '') {
        return mensajes('info', 'Debe seleccionar la institución...');
    } else if ($('#fecha_periodo').val() == '') {
        return mensajes('info', 'Debe seleccionar la el periodo...');
    }
    else {
        mensajes('question', $('#pregunta').val());
    }
}
function verPendientes() {
    var data = {
        fecha_periodo: $('#fecha_periodo').val(),
        id_banco: $('#instituciones').val()
    };
    var html = ajax('v3/' + $('#controlador').val() + '/verDocumentosPendientes', data, 'html');
    $('#contenidoPendientes').html(html.responseText);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Pendientes');
    $('#myModalPendientes').modal('toggle');
}
function agregarCociliacion() {
    window.open('v3/' + $('#controlador').val() + '/agregarConciliacion', '_self');
}
function agregarBancos() {
    limpiarModal();
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nuevo banco');
    $('#myModal').modal('toggle');
}
function agregarCajabancos() {
    limpiarModal();
    $("#codigo").removeAttr("readonly");
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nueva caja banco');
    $('#myModal').modal('toggle');
}
function verBanco(id) {
    $.ajax({
        url: 'v3/' + $('#controlador').val() + '/getConfiguracion',
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('#token').val(),
        },
        data: {
            id: id
        },
        dataType: 'json',
        success: function (json) {
            $('#id').val(json.id);
            $('#nombre').val(json.nombre);
            $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Modificar Banco');
            if (json.estado == 1) {
                $('#cheestado').attr('checked', true);
                $('#estado').val(1);
            } else {
                $('#cheestado').attr('checked', false);
                $('#estado').val(0);
            }
            $('#myModal').modal('toggle');
        }
    });
}
function verDepositoBancario(id) {
    window.open($("#base_url").val() + "/v3/cajabancos/crearDepositoBancario?id=" + id, "_self");
}
function verCajaBanco(id) {
    $.ajax({
        url: 'v3/' + $('#controlador').val() + '/getConfiguracionCajaBanco',
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('#token').val(),
        },
        data: {
            id: id
        },
        dataType: 'json',
        success: function (json) {
            $('#id').val(json.id);
            $('#codigo').val(json.codigo);
            $('#nombre').val(json.nombre);
            $('#clase').val(json.clase).trigger('change');
            $('#numero_cuenta').val(json.numero_cuenta);
            $('#sucursal').val(json.sucursal).trigger('change');
            $('#divisa').val(json.divisa).trigger('change');
            $('#formas_pago').val(json.formas_pago).trigger('change');
            $('#ultimo_cheque').val(json.ultimo_cheque);
            $('#comentarios').val(json.comentarios);
            $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Modificar Caja banco');
            if (json.estado == 1) {
                $('#cheestado').attr('checked', true);
                $('#estado').val(1);
            } else {
                $('#cheestado').attr('checked', false);
                $('#estado').val(0);
            }
            $("#codigo").attr("readonly", "readonly");
            $("#grupo").val(json.grupo).trigger("change");
            $.ajax({
                url: "v3/" + $("#controlador").val() + "/traerctaGrupo",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    id: json.cuenta_mayor,
                },
                dataType: "html",
                success: function (html) {
                    $("#cuenta_mayor").html(html).trigger("change");
                },
            });
            $('#myModal').modal('toggle');
        }
    });
}
function buscarFechaNotaDebito() {
    $('#tableNotadedebitoCajaBanco').dataTable().fnClearTable();
    $('#tableNotadedebitoCajaBanco').dataTable().fnDestroy();
    $("#tableNotadedebitoCajaBanco").DataTable({
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
        pageLength: 50,
        autoWidth: true,
        order: [[0, "desc"]],
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
            url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
            type: "post",
            data: {
                desde: $("#desde").val(),
                hasta: $("#hasta").val(),
            },
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
                data: "fecha",
            },
            {
                className: "text-center",
                data: "id_asiento",
                render: function (data, type, row) {
                    return ('<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + "');\">" + row.id_asiento + "</a>"
                    );
                },
            },
            {
                data: "tipo",
            },
            {
                data: "concepto",
            },
            {
                className: "text-right",
                data: "valor",
            },
            {
                className: "text-center",
                data: "estado",
                render: function (data, type, row) {
                    var estado =
                        row.estado == 1 ? '<label class="text-success">' + $("#activo").val() + "</label>" : '<label class="text-danger">' + $("#inactivo").val() + "</label>";
                    return estado;
                },
            },
            {
                className: "text-center",
                data: "usuariocrea",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verNotadeDebitoCajaBanco(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '<a class="dropdown-item" href="javascript:;" onclick="pdf_nota_debito_cj(\'' + row.id + '\');"><i class="fa fa-file-pdf" style="color:red;"></i> PDF</a>' + '<div class="dropdown-divider"></div>';
                    if (row.estado == 1) {
                        botones += '<a class="dropdown-item" href="javascript:;" onclick="anularNotaDebitoCajaBanco(\'' + row.id + "');\">" + accion + "</a>";
                    } else {
                        botones += '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' + accion + "</a>";
                    }
                    botones += "</div></div>";
                    return botones;
                },
            },
        ],
    });
}
function agregarNotaDebitoBancario() {
    window.open("v3/" + $("#controlador").val() + "/addDebito", "_self");
}
function agregarNotaDebitoCajaBanco() {
    window.open("v3/" + $("#controlador").val() + "/nuevanotadedebitocajabanco", "_self");
}
function buscar_forma_pago() {
    let id_forma_pago = $("#id_forma_pago").val();
    let fechaini = $("#fecha_desde").val();
    let fechafin = $("#fecha_hasta").val();
    if (id_forma_pago != '') {
        $.ajax({
            type: 'get',
            url: "v3/" + $("#controlador").val() + "/buscarformapago",
            datatype: 'html',
            data: {
                'id_forma_pago': id_forma_pago,
                'fecha_desde': fechaini,
                'fecha_hasta': fechafin,
            },
            success: function (datahtml) {
                $("#tbl_detalles").html(datahtml);
            },
            error: function (data) {
                console.log(data);
            }
        });
    }
}
$('body').on('click', '.relactivo', function () {
    if ($(this).prop("checked") == true) {
        $(this).parent().find('.veractivo').val(1);
        $(this).parent().parent().addClass('colorselect');

        calcular_todo();
    } else if ($(this).prop("checked") == false) {
        $(this).parent().find('.veractivo').val(0);
        $(this).parent().parent().removeClass('colorselect');

        calcular_todo();
    }
});
function calcular_todo(r) {
    let totefectivo = 0;
    let totetransfe = 0;
    let totcheques = 0;
    let totdeposito = 0;
    let tottarjetas = 0;
    let totdatalink = 0;
    let totcrucecuentas = 0;
    let total = 0;
    $('.veractivo').each(function (i, obj) {
        let activo = parseInt($(this).val());
        if (r == 1) {
            let tipo = $(this).parent().find('.tipox').val();
            let total = $(this).parent().find('.totax').val();
            if (tipo == 'TARJETA CREDITO') {
                tottarjetas += parseFloat(total);
            }
            if (tipo == 'TARJETA DEBITO') {
                tottarjetas += parseFloat(total);
            }
            if (tipo == 'CHEQUE') {
                totcheques += parseFloat(total);
            }
            if (tipo == 'EFECTIVO') {
                totefectivo += parseFloat(total);
            }
            if (tipo == 'DEPOSITO' || tipo == 'PAYPAL' || tipo == 'PAGO POR APP') {
                totdeposito += parseFloat(total);
            }
            if (tipo == 'TRANSFERENCIA BANCARIA') {
                totetransfe += parseFloat(total);
            }
            if (tipo == 'DATALINK') {
                totdatalink += parseFloat(total);
            }
            if (tipo == 'CRUCE CUENTAS') {
                totcrucecuentas += parseFloat(total);
            }
        } else {
            if (activo == 1) {
                let tipo = $(this).parent().find('.tipox').val();
                let total = $(this).parent().find('.totax').val();

                if (tipo == 'TARJETA CREDITO') {
                    tottarjetas += parseFloat(total);
                }
                if (tipo == 'TARJETA DEBITO') {
                    tottarjetas += parseFloat(total);
                }
                if (tipo == 'CHEQUE') {
                    totcheques += parseFloat(total);
                }
                if (tipo == 'EFECTIVO') {
                    totefectivo += parseFloat(total);
                }
                if (tipo == 'DEPOSITO' || tipo == 'PAYPAL' || tipo == 'PAGO POR APP') {
                    totdeposito += parseFloat(total);
                }
                if (tipo == 'TRANSFERENCIA BANCARIA') {
                    totetransfe += parseFloat(total);
                }
                if (tipo == 'DATALINK') {
                    totdatalink += parseFloat(total);
                }
                if (tipo == 'CRUCE CUENTAS') {
                    totcrucecuentas += parseFloat(total);
                }
            }
        }

    });
    $("#total_efectivo").val(devuelvefloat(totefectivo, 2));
    $("#total_pap_deposito").val(devuelvefloat(totetransfe, 2));
    $("#total_cheques").val(devuelvefloat(totcheques, 2));
    $("#total_tarjetas").val(devuelvefloat(tottarjetas, 2));
    $("#total_datalink").val(devuelvefloat(totdatalink, 2));
    $("#total_crucecuentas").val(devuelvefloat(totcrucecuentas, 2));
    total = parseFloat(totefectivo) + parseFloat(totetransfe) + parseFloat(totdeposito) + parseFloat(totcheques) + parseFloat(tottarjetas) + parseFloat(totdatalink) + parseFloat(totcrucecuentas);
    $("#total_deposito").val(devuelvefloat(total, 2));

}
const verificarChecks = (id_check, id_ing, id) => {
    let hiden = document.getElementById(id_check);
    if (id.checked) {
        hiden.value = id_ing;
    } else {
        hiden.value = ''
    }
}
function devuelvefloat(value, decimales) {
    value = +value;
    if (isNaN(value)) return NaN;
    value = value.toString().split('e');
    value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2)));
    value = value.toString().split('e');
    return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
}
function calcular() {
    var total = 0;
    try {
        $("input[name='debe[]']").each((i, v) => {
            total += Number(v.value);
        });
    } catch (error) {
        console.log(error);
    }
    $("#total").text(total.toFixed(2));
    $("#valor").val(total.toFixed(2));
}
function eliminarFila(fila) {
    $("#tr_" + fila).remove();
    fila--;
    calcular();
}
function seleccionar_todo(tablename) {

    let value = $('#marcarTodo').prop('checked');
    $('#' + tablename).find('.veractivo').val(1);
    $('#' + tablename).find('tr').addClass('colorselect');
    $('#' + tablename).find('.verify').prop("checked", true);

    if (!value) {
        $('#' + tablename).find('.verify').prop("checked", false);
        $('#' + tablename).find('.veractivo').val(0);
        $('#' + tablename).find('tr').removeClass('colorselect');
    }
    if (value) {
        calcular_todo(1);
    } else {
        calcular_todo(0);
    }
}
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
        return false;
    }
    if (charCode === 46 && evt.target.value.indexOf('.') !== -1) {
        return false;
    }

    return true;
}
$("#guardarDepositoBancario").on("click", function () {
    let hoy = new Date();
    let fechaFormulario = new Date($('#fecha_asiento').val());
    hoy.setHours(0, 0, 0, 0);
    if (hoy < fechaFormulario) {
        return mensajes("warning", "La fecha debe ser menor o igual a la de hoy " + String(hoy.getDate()).padStart(2, '0') + '/' + String(hoy.getMonth() + 1).padStart(2, '0') + '/' + hoy.getFullYear());
    }
    valor1 = parseFloat($('#valor1').val());
    if (isNaN(valor1)) {
        valor1 = 0;
    }

    texto_1 = $('#id_cuenta_destino').val();
    total_deposito = parseFloat($('#total_deposito').val());
    if (total_deposito != $("#valor1").val()) {
        return mensajes("warning", "Valores Descuadrados en los depositos");
    }
    if (valor1 > 0 && (texto_1 == null || texto_1 == "")) {
        return mensajes("warning", "Debe Ingresar la segunda cuenta destino");
    }

    if ($("#concepto").val() == '') {
        return mensajes("warning", "Debe Ingresar el concepto");
    }
    mensajes("question", $("#pregunta").val(), "frm_depositoBancario", "action", "v3/cajabancos/depositobancario");
});
$("#sucursal_empresa").on("change", function () {
    let sucursal = $(this).val();
    $.ajax({
        type: "post",
        url: "v3/" + $("#controlador").val() + "/getPuntoEmision",
        headers: {
            "X-CSRF-TOKEN": $("input[name=_token]").val(),
        },
        datatype: "json",
        data: {
            id_sucursal: sucursal,
        },
        success: function (data) {
            if (sucursal != 0) {
                $("#punto_emision").empty();
                $.each(data, function (key, registro) {
                    var select = "";
                    if (registro.codigo_caja == "100")
                        select = "selected";
                    $("#punto_emision").append("<option value=" + registro.id + " " + select + ">" + registro.codigo_sucursal + "-" + registro.codigo_caja + "</option>");
                });
            } else {
                $("#punto_emision").empty();
            }
        },
        error: function (data) { },
    });
});
function buscarFechaDepositoBancario() {
    $('#tableDepositoBancario').dataTable().fnClearTable();
    $('#tableDepositoBancario').dataTable().fnDestroy();
    $("#tableDepositoBancario").DataTable({
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
        order: [[5, "desc"]],
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
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/allDataDepositoBacancario" + "js",
            type: 'post',
            data: {
                desde: $("#desde").val(),
                hasta: $("#hasta").val(),
            },
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
                return '<a href="javascript:void(0)" onclick="visualizarAsiento(' + row.id_asiento + ')">' + row.id_asiento + '</a>';
            }
        }, {
            className: 'text-center',
            data: 'fecha_asiento',
            render: function (data, type, row) {
                return getfecha(row.fecha_asiento, null, '-');
            }

        }, {
            data: 'concepto',

        }, {
            className: 'text-right',
            data: 'total_deposito',

        }, {
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
            data: 'id',
            render: function (data, type, row) {
                var accion = '';
                if (row.estado == 1) {
                    accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                }
                var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDepositoBancario(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><a class="dropdown-item" href="javascript:;" onclick="descargarPdfDeposito(\'' + row.id + '\');"><i class="fas fa-file-pdf" style="color:red;"></i> PDF</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoDeposito(\'' + row.id + '\',\'' + row.estado + '\',\'ConfigNomina2\');">' + accion + '</a></div></div>';
                return botones;
            }

        },
        ],
    });
}
function descargarPdfDeposito(id) {
    window.open('v3/' + $('#controlador').val() + '/imprimirDeposito/' + id, '_blank');
}
function cambiarEstadoDeposito(id, estado) {
    Swal.fire({
        title: '¿Desea Anular este comprobante?',
        text: 'No puedes revertir esta acccion!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Si"
    }).then((result) => {
        if (result.isConfirmed) {
            testDepositoBancario(id);
        }
    })
}
async function testDepositoBancario(id, estado) {
    try {
        const {
            value: text
        } = await Swal.fire({
            input: 'textarea',
            inputPlaceholder: 'Ingrese motivo de anulación...',
            inputAttributes: {
                'aria-label': 'Ingrese motivo de anulación'
            },
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Anular'
        })

        if (text) {
            $.ajax({
                url: "v3/" + $('#controlador').val() + "/anularDepositoBnacario",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    id: id,
                    text: text,
                    estado: estado
                },
                datatype: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        mensajes('success', data.msj);

                    } else {
                        mensajes('error', data.msj);
                    }
                },
                error: function (json) {
                    console.log(json);
                },
            });
        }
    } catch (err) {
        console.log(err);
    }
}
function buscarNotaCredito() {
    $('#tableNotaCredito').dataTable().fnClearTable();
    $('#tableNotaCredito').dataTable().fnDestroy();
    $("#tableNotaCredito").DataTable({
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
        order: [[5, "desc"]],
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
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/allNotaCredito" + "js",
            type: 'post',
            data: {
                desde: $("#desde").val(),
                hasta: $("#hasta").val(),
            },
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            }, error(error) {
                console.log(error);
            }
        },
        columns: [{
            data: 'fecha'
        }, {
            className: "text-center",
            data: "id_asiento",
            render: function (data, type, row) {
                return ('<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + "');\">" + row.id_asiento + "</a>");
            },
        }, {
            className: "text-center",
            data: 'id',
        }, {
            data: 'estado',
            render: function (data, type, row) {
                return "BAN-NC	";
            }
        }, {
            data: 'concepto',

        }, {
            data: 'valor'
        }, {
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
            data: 'nombre_completo',
        },

        {
            className: 'text-center',
            data: 'id',
            render: function (data, type, row) {
                var accion = '';
                if (row.estado == 1) {
                    accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                }
                var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDepositoBancario(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoNotaCredito(\'' + row.id + '\',\'' + row.estado + '\');">' + accion + '</a></div></div>';
                return botones;
            }
        },
        ],
    });
}
function cargarDeudas() {
    var id_proveedor = $('#id_acreedor').val();
    $.ajax({
        type: 'post',
        url: "v3/" + $('#controlador').val() + "/getFacturasProveedor",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: {
            'id_proveedor': id_proveedor,
        },
        success: function (data) {
            let f;
            $("#detalleDeuda").empty();
            for (f = 0; f < data.length; f++) {
                var registro = data[f];
                let row = `<tr id="tr_${f}">'
                    '<td style="width: 10%;"><input type="text" class="form-control text-right" id="fecha_vencimiento${f}" name="fecha_vencimiento[]" value="${registro.fecha_asiento}" /></td>';
                    '<td style="width: 10%;"><input type="text" class="form-control text-right" id="tipo${f}" name="tipo[]" value="${registro.tipo}" /></td>';
                    '<td style="width: 20%;"><input type="text" class="form-control text-right" id="numero${f}" name="numero[]" value="${registro.numero}" /></td>';
                    '<td style="width: 20%;"><textarea type="text" class="form-control" style="width:100%;overflow:hidden;min-height:100px" id="concepto${f}" name="concepto[]" readonly>Fact #: ${registro.numero} Prov: ${registro.observacion}</textarea></td>';
                    '<td style="width: 10%;"><input type="text" class="form-control text-right" id="div${f}" name="div[]" value="$" readonly/></td>';
                    '<td style="width: 10%;"><input type="text" class="form-control text-right" id="saldo${f}" name="saldo[]" value="${registro.valor_contable}" readonly/></td>';
                    '<td style="width: 10%;"><input type="text" class="form-control text-right abonos" id="abono${f}" name="abono[]" value="0.00" readonly/></td>';
                    '<td style="width: 10%;"><input type="text" class="form-control text-right" id="nuevo_saldo${f}" name="nuevo_saldo[]" value="${registro.valor_nuevo}" readonly/>
                    <input type="hidden" class="form-control text-right" id="id_compra${f}" name="id_compra[]" value="${registro.id_compra}" readonly/></td>';
                    '</tr>`;
                $("#detalleDeuda").append(row);
            }
            fila_deuda = data.length - 1;
        },
        error: function (data) {
            console.log(data);
        }
    })
}
function nuevo() {
    let fila = $("#mifila").clone().removeAttr('id').show();
    let rowk = document.getElementById("agregar_cuentas").insertRow(-1);
    $(rowk).append(fila);
    $('.select2').select2();
}
function aplicarDeuda() {
    var valor = parseFloat($("#valor").val());
    var valor2 = parseFloat($("#valor").val());
    var valor_saldo = parseFloat($("#saldo0").val());
    var contador = parseInt(fila_deuda);
    var saldo = 0;
    var abono = 0;
    var total = 0;
    for (i = 0; i <= contador; i++) {
        saldo += parseFloat($("#saldo" + i).val());
        valor_saldo = parseFloat($("#saldo" + i).val());
        valor -= valor_saldo;
        var cont = parseFloat($("#abono" + i).val());
        if (isNaN(cont)) {
            cont = 0;
        }
        abono += cont;
        if (valor > valor_saldo) {
            $("#abono" + i).val(valor_saldo.toFixed(2, 2));
        } else {
            total = valor + valor_saldo;
            if (total <= valor2) {
                if (total > 0) {
                    if (total > valor_saldo) {
                        total = valor_saldo;
                        $("#abono" + i).val(total.toFixed(2, 2));
                    } else {
                        $("#abono" + i).val(total.toFixed(2, 2));
                    }
                }
            }
        }
    }
    suma_totales();
    var total = 0;
    if (!isNaN(valor) && !isNaN(valor_saldo)) {
    } else {
        Swal.fire("Error!", "trans('contableM.ingrevalorchequeprim')", "error");
    }
    $("#total_abono").val(valor2);

}
function suma_totales() {
    var total = 0;
    $('.abonos').each(function (i, obj) {
        total += parseFloat($(this).val());
    });
    var cheque = $("#valor").val();
    var restante = cheque - total;
    $("#debito_favor").val(restante.toFixed(2, 2));
    $("#total_suma").val(total.toFixed(2, 2));
    $("#total_abono").val(total.toFixed(2, 2));
}
function validarbanco() {
    if ($('#id').val() == '') {
        $.ajax({
            url: "v3/" + $("#controlador").val() + "/verificarbanco",
            type: "post",
            headers: {
                "X-CSRF-TOKEN": $("#token").val(),
            },
            data: {
                nombre: $("#nombre").val(),
            },
            dataType: "json",
            success: function (json) {
                if (json != "") {
                    if (json.status == "error") {
                        return mensajes("warning", json.message);
                    } else {
                        mensajes("question", $("#pregunta").val());
                    }
                }
            },
            error: function () { },
        });
    }
}
function pdfDatosConciliacion(id, anio, mes = null) {
    var periodo = anio + '-' + mes;
    if (mes === null) {
        id = $('#instituciones').val();
        periodo = $('#fecha_periodo').val().split('-');
    }
    else {
        periodo = periodo.split('-');
    }
    window.open("v3/" + $("#controlador").val() + "/pdfDatosConciliacion?periodo=" + periodo[0] + '-' + periodo[1] + "&banco=" + id, "_blank");
}
function pdf_nota_debito_cj(id) {
    window.open("v3/" + $("#controlador").val() + "/pdf_nota_debito?id=" + id, "_blank");
}
function pdfTransferencia(id) {
    window.open("v3/" + $("#controlador").val() + "/imprimirTransferencia?id=" + id, "_blank");
}
function excelDatosConciliacion(id, anio, mes = null) {
    var periodo = anio + '-' + mes;

    if (mes === null) {
        id = $('#instituciones').val();
        periodo = $('#fecha_periodo').val().split('-');
    }
    else {
        periodo = periodo.split('-');
    }
    window.open("v3/" + $("#controlador").val() + "/excelDatosConciliacion?periodo=" + periodo[0] + '-' + periodo[1] + "&banco=" + id, "_blank");
}
function validateNumberInput(event) {
    const value = event.target.value;
    const sanitizedValue = value.replace(/[^0-9.]/g, '');
    event.target.value = sanitizedValue;
}
function verNotadeDebitoCajaBanco(id, num) {
    $("#titulo_modal_ver_caja_banco").html('<i class="fas fa-edit"></i> No.: ' + id);
    $.ajax({
        url: "v3/" + $("#controlador").val() + "/getNotadeDebitoCajaBancoPrint",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
            id: id,
        },
        dataType: "html",
        success: function (html) {
            html = html.split("|");
            $("#contenidoHtml").html(html[0]);
            $("#titulo_modal_ver_caja_banco").html('<i class="fas fa-eye" style="color:cyan;"></i> Nota de débito No.:' + html[1]);
            $("#myModal").modal("toggle");
        },
        error(e) {
            //window.open("v3/admin", "_self");
            console.log(e);
        },
    });
}
function anularNotaDebitoCajaBanco(id) {
    Swal.fire({
        title: "¿Desea Anular este comprobante?",
        text: $("#informa").val(),
        icon: "info",
        showCancelButton: true,
        cancelButtonText: "No",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
    }).then((result) => {
        if (result.isConfirmed) {
            testnota_debito_cj(id);
        }
    });
}
async function testnota_debito_cj(id) {
    try {
        const { value: text } = await Swal.fire({
            input: "textarea",
            inputPlaceholder: "Ingrese motivo de anulación...",
            inputAttributes: {
                "aria-label": "Ingrese motivo de anulación",
            },
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Anular",
        });
        if (text) {
            $.ajax({
                url: "v3/" + $("#controlador").val() + "/anularNotadebito",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    id: id,
                    observacion: $("#swal2-textarea").val(),
                },
                datatype: "json",
                success: function (json) {
                    mensajes(json.status, json.message, "", "", "", "tabla");
                },
                error: function (json) {
                    console.log(json);
                },
            });
        }
    } catch (err) {
        console.log(err);
    }
}
function regresar(ruta) {
    Swal.fire({
        title: "¿Desea regresar a la pantalla anterior?",
        text: $("#informa").val(),
        icon: "info",
        showCancelButton: true,
        cancelButtonText: "No",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = $("#base_url").val() + ruta;
        }
    });
}
function cambiarEstadoTrasferenciaB(id, estado) {
    Swal.fire({
        title: '¿Desea Anular esta trasferencia?',
        text: 'No puedes revertir esta acccion!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Si"
    }).then((result) => {
        if (result.isConfirmed) {
            testTrasferenciaB(id, estado);
        }
    })
}
async function testTrasferenciaB(id, estado) {
    try {
        const {
            value: text
        } = await Swal.fire({
            input: 'textarea',
            inputPlaceholder: 'Ingrese motivo de anulación...',
            inputAttributes: {
                'aria-label': 'Ingrese motivo de anulación'
            },
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Anular'
        })

        if (text) {
            $.ajax({
                url: "v3/" + $('#controlador').val() + "/anularTrasferenciaBancaria",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    id: id,
                    text: text,
                    estado: estado
                },
                datatype: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        mensajes('success', data.msj, '', '', '', 'tabla');

                    } else {
                        mensajes('error', data.msj);
                    }
                },
                error: function (json) {
                    console.log(json);
                },
            });
        }
    } catch (err) {
        console.log(err);
    }
}
function buscarTransferenciaBancaria() {
    $('#tableTransferenciasBancarias').dataTable().fnClearTable();
    $('#tableTransferenciasBancarias').dataTable().fnDestroy();
    $("#tableTransferenciasBancarias").DataTable({
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
        order: [[1, "desc"]],
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
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
            type: 'post',
            data: {
                desde: $("#desde").val(),
                hasta: $("#hasta").val(),
            },
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
                return '<a href="javascript:void(0)" onclick="visualizarAsiento(' + row.id_asiento + ')">' + row.id_asiento + '</a>';
            }
        }, {
            className: 'text-center',
            data: 'id',
            render: function (data, type, row) {
                var numcheque = row.numcheque === null ? '' : row.numcheque;
                return numcheque;
            }
        }, {
            className: 'text-center',
            data: 'id',
            render: function (data, type, row) {
                return getfecha(row.fecha_cheque, null, '-');
            }
        }, {
            data: 'concepto',
        }, {
            className: 'text-right',
            data: 'valor_destino',
        }, {
            className: 'text-center',
            data: 'id',
            render: function (data, type, row) {
                var estado = '<label class="text-success">' + $('#activo').val() + '</label>';
                if (row.estado == 0) {
                    estado = '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                }
                return estado;
            }
        }, {
            className: 'text-center',
            data: 'usuariocrea',
        }, {
            className: 'text-center',
            data: 'id',
            render: function (data, type, row) {
                var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
                if (row.estado == 1) {
                    accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
                }
                var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verTransferenciaBancaria(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><a class="dropdown-item" href="javascript:;" onclick="pdfTransferencia(\'' + row.id + '\');"><i class="fa fa-file-pdf" style="color:red;"></i> PDF</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoTrasferenciaB(\'' + row.id + "','" + row.estado + "','TransferenciaBancaria');\">" + accion + "</a></div></div>";
                return botones;
            },
        }],
    });
}