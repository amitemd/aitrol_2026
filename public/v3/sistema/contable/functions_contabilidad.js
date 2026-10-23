var filaCruce = 0;
var filaCruceIds = [];
var aux_filaCruce = 0;


$(document).ready(function () {
    try {
        function initializeSelect2() {
            $(".select2_cuenta").select2({
                width: "100%",
                placeholder: "--Seleccione--",
                minimumInputLength: 3,
                ajax: {
                    url: "v3/" + $("#controlador").val() + "/buscarCuenta",
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
                    error: function (error) {
                        console.log(error);
                    },
                    cache: true,
                },
            });
        }
        initializeSelect2();
        $("#tablePlanCuenta").DataTable({
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
            "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo').val() + "js",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error() {
                    window.open('v3/admin', '_self');
                }
            },
            columns: [{
                data: 'plan'
            }, {
                data: 'nombre'
            }, {
                data: 'id',
                render: function (data, type, row) {
                    var tipo = '';
                    if (row.estado == 1) {
                        tipo = 'Grupo';
                    }
                    else {
                        tipo = 'Detalle';
                    }
                    return tipo;
                }
            }, {
                data: 'id',
                render: function (data, type, row) {
                    var clase = '';
                    var clase_ = row.plan.charAt(0);
                    if (clase_ == 1) {
                        clase = 'Activo';
                    } else if (clase_ == 2) {
                        clase = 'Pasivo';
                    } else if (clase_ == 3) {
                        clase = 'Patrimono';
                    } else if (clase_ == 4) {
                        clase = 'Ingresos';
                    } else if (clase_ == 5) {
                        clase = 'Costos y Gastos';
                    }
                    return clase;
                }
            }, {
                className: 'text-center',
                data: 'created_at'
            }, {
                className: 'text-center',
                data: 'updated_at',
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var cadena = row.nombre1;
                    var letra = cadena.charAt(0);
                    return letra + '. ' + row.apellido1;
                }
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var cadena = row.nombre1_;
                    var letra = cadena.charAt(0);
                    return letra + '. ' + row.apellido1_;
                }
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var estado = row.estado != 0 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
                    return estado;
                }
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> Activar';
                    if (row.estado_menu == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> Desactivar';
                    }
                    var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verPlan(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> Ver</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + '\',\'' + row.estado + '\',\'Plan\');">' + accion + '</a></div></div>';
                    return botones;
                }
            }],
            pageLength: 500
        });
        $('#btnGuardarPlan').click(function () {
            if ($('#txtcodigo').val() == '') {
                return mensajes('info', 'Ingrese el código contable de la cuenta');
            }
            else if ($('#txtnombre').val() == '') {
                return mensajes('info', 'Ingrese el nombre de la cuenta');
            }
            else if ($('#cmbtipo').val() == '') {
                return mensajes('info', 'Seleccione el tipo de la cuenta');
            }
            else if ($('#cmbnaturaleza_2').val() == '') {
                return mensajes('info', 'Seleccione donde se calcula la cuenta');
            }
            else {
                mensajes("question", $("#pregunta").val(), "frm_agregar_plan", "action_");
            }
        });
        $('#btnModificarPlan').click(function () {
            if ($('#plan').val() == '') {
                return mensajes('info', 'Ingrese el código contable de la cuenta');
            }
            else if ($('#nombre').val() == '') {
                return mensajes('info', 'Ingrese el nombre de la cuenta');
            }
            else if ($('#tipo').val() == '') {
                return mensajes('info', 'Seleccione el tipo de la cuenta');
            }
            else if ($('#estado').val() == '') {
                return mensajes('info', 'Seleccione el estado de la cuenta');
            }
            else if ($('#naturaleza').val() == '') {
                return mensajes('info', 'Seleccione la naturaleza de la cuenta');
            }
            else if ($('#naturaleza_2').val() == '') {
                return mensajes('info', 'Seleccione donde se calcula la cuenta');
            }
            else if ($('#naturaleza_2').val() == '') {
                return mensajes('info', 'Seleccione donde se calcula la cuenta');
            }
            else {
                mensajes("question", $("#pregunta").val(), "frm_editar_plan");
            }
        });
        $("#tableAsientosAnulados").DataTable({
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
            pageLength: 50,
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            order: [[3, 'desc']],
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
                error() {
                    //window.open("v3/admin", "_self");
                },
            },
            columns: [
                {
                    data: "observacion",
                },
                {
                    className: 'text-center',
                    data: 'id_ant',
                    render: function (data, type, row) {
                        return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_ant + '\');">' + row.id_ant + '</a>';
                    }
                },
                {
                    className: 'text-center',
                    data: 'id_referencia',
                    render: function (data, type, row) {
                        return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_referencia + '\');">' + row.id_referencia + '</a>';
                    }
                },
                {
                    className: 'text-center',
                    data: "nombre_usuario",
                },
                {
                    className: 'text-center',
                    data: "created_at",
                },
            ],
        });
        $("#tableDescuadrados").DataTable({
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
                }
            },
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            order: [[0, "desc"]],
            iDisplayLength: 100,
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
                        $(win.document.body).find("table").addClass("compact").css("font-size", "inherit");
                    }
                }
            ],
            ajax: {
                url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: function (d) {
                    d.anio = $('#anio_asiento').val(); // Envía parámetros adicionales si es necesario
                },
                dataSrc: 'data', // Asegúrate de que los datos están en la clave 'data' de la respuesta
                error: function (error) {
                    console.log(error);
                }
            },
            columns: [
                {
                    className: "text-center",
                    data: "id_asiento",
                    render: function (data, type, row) {
                        return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + '\')" style="color: red;"><strong>' + row.id_asiento + '</strong></a>';
                    }
                },
                {
                    data: "modulo"
                },
                {
                    className: "text-right",
                    data: "debe"
                },
                {
                    className: "text-right",
                    data: "haber"
                },
                {
                    className: "text-right",
                    data: "diferencia"
                },
                {
                    className: "text-center",
                    data: "id_asiento",
                    render: function (data, type, row) {
                        return '<a href="javascript:;" onclick="EditarAsiento(\'' + row.id_asiento + '\');"><i class="fa fa-pencil-square-o" style="font-size: 18px; color:orange; text-decoration: none;"></i> </a>'
                    },
                },
            ]
        });
        $("#agregarlineaDetalleAsiento").on("click", function () {
            var uniqueIdasiento = filaCruce;
            filaCruceIds.push(uniqueIdasiento);

            var html = '<tr id="tr_' + uniqueIdasiento + '">';
            html += '<td style="max-width: 100%;"><select class="form-control select2_cuenta" id="id_cuenta_' + uniqueIdasiento + '" name="cuenta[]"><option value="">--Seleccione--</option></select></td>';
            html += '<td><input type="text" class="form-control " id="detalle_' + uniqueIdasiento + '" name="detalle[]" value="" " /></td>';
            html += '<td><input type="text" class="form-control text-right" id="debe_' + uniqueIdasiento + '" name="debe[]" value="0.0000" onchange="valor_debe(' + uniqueIdasiento + ')" "/></td>';
            html += '<td><input type="text" class="form-control text-right" id="haber_' + uniqueIdasiento + '" name="haber[]" value="0.0000" onchange="valor_haber(' + uniqueIdasiento + ')" "/></td>';
            html += '<td><a href="javascript:;" class="btn btn-danger" onclick="eliminarFilaAsiento(\'' + uniqueIdasiento + '\');"><i class="fa fa-trash-alt"></i></a></td>';
            html += "</tr>";
            $("#detalleAsiento").append(html);
            $(".select2_cuenta").select2({
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
                    url: "v3/" + $("#controlador").val() + "/buscarCuenta",
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
            filaCruce++;
            aux_filaCruce++;
        });
        $("#btnUpdateAsientoDescuadrado").click(function () {
            if ($("#observacion").val() == "") {
                return mensajes("warning", "Ingrese una observación");
            } else if ($("#totaldebe").val() != $("#totalhaber").val()) {
                return mensajes("warning", "No coinciden los totales, por favor verificar");
            } else {
                //if (aux_filaCruce > 0) {
                var validacionCuenta = false;
                var validacionDebe = false;
                var validacionHaber = false;
                $("select[name='cuenta[]']").each((i, v) => {
                    if (v.value == "") {
                        validacionCuenta = true;
                    }
                });
                if (validacionCuenta) {
                    return mensajes("warning", "Debe ingresar la cuenta");
                }
                if (validacionDebe) {
                    return mensajes("warning", "Ingrese el valor del debe");
                }
                if (validacionHaber) {
                    return mensajes("warning", "Ingrese el valor del haber");
                }
                //}
                //test_clave();
                //mensajes("question", $("#pregunta").val(), 'frm_', 'action', $('#base_url').val() + '/v3/contabilidad/descuadrados');
                mensajes("question", $("#pregunta").val());
            }
        });
    } catch (error) { }
});
function exportarPlan() {
    window.open("v3/" + $('#controlador').val() + "/exportarPlan", '_self');
}
function agregarPlanCuenta() {
    if ($('#id_plan').val() == '') {
        return mensajes('info', 'Seleccione una cuenta de grupo de la lista');
    }
    limpiarModal('frm_agregar_plan');
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nuevo ' + $('#submodulo').val());
    var data = {
        plan: $('#id_plan').val(),
    };
    var json = ajax("v3/" + $('#controlador').val() + "/secuenciaCuenta", data, 'html');

    $('#lblcodigo').val($('#id_plan').val() + '.');
    $('#txtcodigo').val(json.responseText);
    $('#myModal').modal('toggle');
}
function detalleCuenta(plan) {
    var data = {
        plan: plan
    };
    var json = ajax("v3/" + $('#controlador').val() + "/detalleCuenta", data, 'json');
    if (json.responseJSON != '') {
        json = json.responseJSON;
        $('#id_plan').val(json.plan);
        $('#plan').val(json.plan);
        $('#nombre').val(json.nombre);
        $('#tipo').val(json.estado);
        var estado = json.estado != 0 ? 3 : 0;
        $('#estado').val(estado);
        $('#naturaleza').val(json.naturaleza);
        $('#naturaleza_2').val(json.naturaleza_2);
        $('#estado_cierre_ano').val(json.estado_cierre_ano);
        $('#infoCuenta').removeClass('collapsed-card');
        $('#btnExpandir').html('<i class="fas fa-minus"></i>');
    }
}
function mostrarCuenta(plan) {
    var data = {
        plan: plan
    };
    var html = ajax("v3/" + $('#controlador').val() + "/traerCuentasPlan", data, 'html');
    if (html.responseText != '') {
        $('#id_plan').val(plan);
        $('#cuerpoCuentas').html(html.responseText);
    }
}
function agregar() {
    limpiarModal();
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nuevo ' + $('#submodulo').val());
    $('#myModal').modal('toggle');
}
function toggleChildren(event) {
    var ul = event.target.nextElementSibling;
    ul.classList.toggle('collapsed');
    event.stopPropagation();
}
var spans = document.querySelectorAll('.contenedor-arbol span');
spans.forEach(function (span) {
    span.addEventListener('click', toggleChildren);
});

function buscarDescuadrados() {
    // Destruir la instancia de DataTables si existe
    if ($.fn.DataTable.isDataTable('#tableDescuadrados')) {
        $('#tableDescuadrados').DataTable().destroy();
    }

    // Inicializar DataTables
    $("#tableDescuadrados").DataTable({
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
            }
        },
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        order: [[0, "desc"]],
        iDisplayLength: 100,
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
                    $(win.document.body).find("table").addClass("compact").css("font-size", "inherit");
                }
            }
        ],
        ajax: {
            url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
            type: "post",
            headers: {
                "X-CSRF-TOKEN": $("#token").val(),
            },
            data: function (d) {
                d.anio = $('#anio_asiento').val(); // Envía parámetros adicionales si es necesario
            },
            dataSrc: 'data', // Asegúrate de que los datos están en la clave 'data' de la respuesta
            error: function (error) {
                console.log(error);
            }
        },
        columns: [
            {
                className: "text-center",
                data: "id_asiento",
                render: function (data, type, row) {
                    return '<a href="javascript:;" onclick="visualizarAsientos(\'' + row.id_asiento + '\')" title="Ver Asiento" style="color: red;"><strong>' + row.id_asiento + '</strong></a>';
                }
            },
            {
                data: "modulo"
            },
            {
                className: "text-right",
                data: "debe"
            },
            {
                className: "text-right",
                data: "haber"
            },
            {
                className: "text-right",
                data: "diferencia"
            },
            {
                className: "text-center",
                data: "id_asiento",
                render: function (data, type, row) {
                    return '<a href="javascript:;" onclick="EditarAsiento(\'' + row.id_asiento + '\');"><i class="fa fa-pencil-square-o" style="font-size: 18px; color:orange; text-decoration: none;"></i> </a>'
                },
            },
        ]
    });
}

function EditarAsiento(id_asiento) {
    limpiarModal();
    $("#titulo_modal").html('<i class="fas fa-eye"></i> Visualizador comprobante de ingreso');
    var data = {
        id: id_asiento,
    };
    var json = ajax("v3/" + $("#controlador").val() + "/getAsientoCabecera", data, 'json');
    if (json.responseJSON != null) {
        json = json.responseJSON;
        document.getElementById("id_asiento").textContent = json.id;
        document.getElementById("f_creacion").textContent = json.created_at;
        document.getElementById("modulo").textContent = json.modulo;
        document.getElementById("valor").textContent = json.valor;
        document.getElementById("estado").textContent = json.estado;
        document.getElementById("u_crea").textContent = json.nombre_crea;
        $("#id_asiento_cabecera").val(json.id);
        $('#observacion').val(json.observacion);
        var checkbox = document.getElementById("sri");
        if (json.sri == 1) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    }

    var json1 = ajax("v3/" + $("#controlador").val() + "/DetalleLogContable", data, 'json');
    if (json1.responseJSON != null) {
        json1 = json1.responseJSON;
        $("#log_contable").empty();
        $("#log_contable").html(json1.table);
    } else {
        $("#log_contable").empty();
    }
    var json2 = ajax("v3/" + $("#controlador").val() + "/getdetalleAsiento", data, 'json');
    if (json2.responseJSON != '') {
        json2 = json2.responseJSON;
        $("#detalleAsiento").empty();
        $("#detalleAsiento").html(json2.table);
    } else {
        $("#detalleAsiento").empty();
    }
    suma_debe();
    suma_haber();
    $('.select2_cuenta').select2({
        width: "100%",
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/" + $("#controlador").val() + "/buscarCuenta",
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
            error: function (error) {
                console.log(error);
            },
            cache: true,
        },
    })
    $("#myModal").modal("toggle");
}

function eliminarFilaAsiento(idCruce) {
    $("#tr_" + idCruce).remove();
    aux_filaCruce--;
    suma_debe();
    suma_haber();
    filaCruceCivIds = filaCruceIds.filter(function (item) {
        return item != id;
    });
}

function valor_debe(id) {
    var debe = parseFloat($("#debe_" + id).val());
    if (debe == "" || isNaN(debe) || debe <= 0) {
        debe = 0;
    }
    $("#debe_" + id).val(parseFloat(debe).toFixed(4));
    suma_debe();
}

function valor_haber(id) {
    var haber = parseFloat($("#haber_" + id).val());
    if (haber == "" || isNaN(haber) || haber <= 0) {
        haber = 0;
    }
    $("#haber_" + id).val(parseFloat(haber).toFixed(4));
    suma_haber();
}

function suma_debe() {
    var total = 0;
    $("input[name='debe[]']").each(function () {
        var cantidad = parseFloat($(this).val());
        if (!isNaN(cantidad)) {
            total += cantidad;
        }
    });
    if (isNaN(total)) {
        total = 0;
    }
    $("#debe_total").html('$ ' + total.toFixed(4));
    $("#totaldebe").val(total.toFixed(4));
}

function suma_haber() {
    var total = 0;
    $("input[name='haber[]']").each(function () {
        var cantidad = parseFloat($(this).val());
        if (!isNaN(cantidad)) {
            total += cantidad;
        }
    });
    if (isNaN(total)) {
        total = 0;
    }
    $("#haber_total").html('$ ' + total.toFixed(4));
    $("#totalhaber").val(total.toFixed(4));
}

async function test_clave() {
    try {
        const {
            value: text
        } = await Swal.fire({
            title: 'Ingresa tu contraseña',
            input: 'password',
            inputPlaceholder: 'Ingrese contraseña',
            inputAttributes: {
                maxlength: 18,
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true
        })

        if (text) {
            $.ajax({
                url: "v3/" + $('#controlador').val() + "/checkpass",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    'userpass': text
                },
                datatype: "json",
                success: function (json) {
                    if (json == 'ok') {
                        mensajes("question", $("#pregunta").val(), 'frm_', 'action', $('#base_url').val() + '/v3/contabilidad/descuadrados');
                    } else {
                        return mensajes("warning", "Contraseña incorrecta, intente de nuevamente");
                    }
                },
                error: function (json) {
                    return mensajes("warning", "Contraseña incorrecta, intente de nuevamente");
                },
            });
        }

    } catch (err) {
        console.log(err);
    }
}





