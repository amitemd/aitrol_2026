var btnCed = $(".ladda-button-demo-ced").ladda();
var btnFT = $(".ladda-button-demo-fichat").ladda();
var btnFO = $(".ladda-button-demo-fichao").ladda();
var btnEmpLogo = $(".ladda-button-demo-emplogo").ladda();
$(document).ready(function () {
    $('#elementTour').val('hero-contenido,hero-accion,hero-agregar');
    try {
        $("#tableConfiguraciones").DataTable({
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
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);

                }
            },
            columns: [{
                data: 'tipo'
            }, {
                className: 'text-right',
                data: 'valor'
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                    return estado;
                }
            }, {
                className: 'text-center',
                data: 'usuariocrea',
            }, {
                className: 'text-center',
                data: 'usuariomod',
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    console.log(row);
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $('#activar').val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verConfiguracion(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + '\',\'' + row.estado + '\',\'ConfigNomina\');">' + accion + '</a></div></div>';
                    return botones;
                }
            }],


        });

        $("#tableRoles").DataTable({
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
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, data: {
                    id_empresa: $('#id_empresa').val(),
                    anio: $('#anio').val(),
                    mes: $('#mes').val(),
                }, error(error) {
                    //window.open('v3/admin', '_self');
                    console.log(error);
                }
            },
            columns: [{
                data: 'razonsocial'
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    return row.anio + '-' + PadLeft(row.mes, 2);
                }
            }, {
                data: 'nombres'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            },],

        });
        $("#tableEmpleados").DataTable({
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
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    console.log(error);

                }
            },
            columns: [{
                data: 'id_user'
            }, {
                className: 'text-right',
                data: 'nombres'
            }, {
                className: 'text-right',
                data: 'codigo_sucursal'
            }, {
                className: 'text-right',
                data: 'total_prestamos'
            }, {
                className: 'text-right',
                data: 'total_saldos'
            }, {
                className: 'text-right',
                data: 'fecha_ingreso',
                render: function (data, type, row) {
                    var freserva = '<label class="text-success">' + row.fecha_ingreso + '</label>';
                    var dias = diasHastaUnAnio(row.fecha_ingreso);
                    if (dias > 0) {
                        freserva = freserva + '<span class="right badge badge-danger">Faltan ' + dias + ' días para el año  </span>';
                    }

                    return freserva;
                }
            }, {
                className: 'text-right',
                data: 'sueldo_neto'
            }, {
                className: 'text-right',
                data: 'cargo'
            }, {
                className: 'text-right',
                data: 'jornada'
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
                    return estado;
                }
            }, {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    console.log(row);
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $('#activar').val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verEmpleado(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + '\',\'' + row.estado + '\',\'ConfigNomina\');">' + accion + '</a></div></div>';
                    return botones;
                }
            }],


        });
    } catch (error) { console.log(error); }

    $('#btnGuardarConfiguracion').click(function () {
        if ($('#tipo').val() == '') {
            mensajes('info', 'Ingrese el tipo');
        } else if ($('#valor').val() == '') {
            mensajes('info', 'Ingrese el valor');
        } else {
            mensajes('question', $('#pregunta').val())
        }
    });
    $("#empleadoCedula").change(function (e) {
        if (($("#empleadoCedula").val().length = 0)) {
            btnCed.ladda("stop");
        } else {
            var inputFileImage = document.getElementById("empleadoCedula");
            var file = inputFileImage.files[0];
            var fileSize = $("#empleadoCedula")[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                btnCed.ladda("stop");
                $("#empleadoCedula").val("");
                mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
            } else {
                var fd = new FormData();
                fd.append("empleadoCedula", file);
                fd.append("tipo_archivo", "empleadoCedula");
                fd.append("id_empleado", $("#id_user").val());
                $.ajax({
                    url: "v3/utilidades/guardarArchivo",
                    headers: {
                        "X-CSRF-TOKEN": $("#token").val(),
                    },
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: "post",
                    success: function (data) {
                        var imagen = data.split("|");
                        if (imagen[0] == "ok") {
                            btnCed.ladda("stop");
                            $("#empleadoCedula").val("");
                            $("#lblempleadoCedula").html(
                                'Cedula: <a href="' +
                                $("#base_url").val() +
                                "/v3/utilidades/viewPdf?id=" +
                                $("#id").val() +
                                '&t=empleadoCedula" target="new">ver/descargar</a>'
                            );
                        } else {
                            btnCed.ladda("stop");
                            alert(imagen[1]);
                            $("#empleadoCedula").val("");
                        }
                    },
                });
            }
        }
    });
    $("#lblempleadoCedula").click(function (e) {
        if ($("#id_user").val() == "") {
            e.preventDefault();
            btnCed.ladda("stop");
            return mensajes(
                "warning",
                "Debe ingresar la identificación del empleado a crear..."
            );
        }
    });
    $("#empleadoFichaOcupacional").change(function (e) {
        if (($("#empleadoFichaOcupacional").val().length = 0)) {
            btnFO.ladda("stop");
        } else {
            var inputFileImage = document.getElementById("empleadoFichaOcupacional");
            var file = inputFileImage.files[0];
            var fileSize = $("#empleadoFichaOcupacional")[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                btnFO.ladda("stop");
                $("#empleadoFichaOcupacional").val("");
                mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
            } else {
                var fd = new FormData();
                fd.append("empleadoFichaOcupacional", file);
                fd.append("tipo_archivo", "empleadoFichaOcupacional");
                fd.append("id_empleado", $("#id_user").val());
                $.ajax({
                    url: "v3/utilidades/guardarArchivo",
                    headers: {
                        "X-CSRF-TOKEN": $("#token").val(),
                    },
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: "post",
                    success: function (data) {
                        var imagen = data.split("|");
                        if (imagen[0] == "ok") {
                            btnFO.ladda("stop");
                            $("#empleadoFichaOcupacional").val("");
                            $("#lblFichaOcupacional").html(
                                'Ficha Ocupacional: <a href="' +
                                $("#base_url").val() +
                                "/v3/utilidades/viewPdf?id=" +
                                $("#id").val() +
                                '&t=empleadoFichaOcupacional" target="new">ver/descargar</a>'
                            );
                        } else {
                            btnFO.ladda("stop");
                            $("#empleadoFichaOcupacional").val("");
                        }
                    },
                });
            }
        }
    });
    $("#empleadoFichaTecnica").change(function (e) {
        if (($("#empleadoFichaTecnica").val().length = 0)) {
            btnFT.ladda("stop");
        } else {
            var inputFileImage = document.getElementById("empleadoFichaTecnica");
            var file = inputFileImage.files[0];
            var fileSize = $("#empleadoFichaTecnica")[0].files[0].size;
            var siezekiloByte = parseInt(fileSize / 1024);
            if (siezekiloByte > 1072) {
                btnFT.ladda("stop");
                $("#empleadoFichaTecnica").val("");
                alert("Error: imagen muy grande o tiene una resolucion muy alta");
                mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
            } else {
                var fd = new FormData();
                fd.append("empleadoFichaTecnica", file);
                fd.append("tipo_archivo", "empleadoFichaTecnica");
                fd.append("id_empleado", $("#id_user").val());
                $.ajax({
                    url: "v3/utilidades/guardarArchivo",
                    headers: {
                        "X-CSRF-TOKEN": $("#token").val(),
                    },
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: "post",
                    success: function (data) {
                        var imagen = data.split("|");
                        if (imagen[0] == "ok") {
                            btnFT.ladda("stop");
                            $("#empleadoFichaTecnica").val("");
                            $("#lblFichaTecnica").html(
                                'Ficha Tecnica: <a href="' +
                                $("#base_url").val() +
                                "/v3/utilidades/viewPdf?id=" +
                                $("#id").val() +
                                '&t=empleadoFichaTecnica" target="new">ver/descargar</a>'
                            );
                        } else {
                            btnFT.ladda("stop");
                            $("#empleadoFichaTecnica").val("");
                        }
                    },
                });
            }
        }
    });
});

function agregarConfiguracion() {
    limpiarModal();
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nueva Configuración');
    $('#myModal').modal('toggle');
}
function verConfiguracion(id) {
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
            $('#tipo').val(json.tipo);
            $('#valor').val(json.valor);
            $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Modificar Configuración');
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
$(document).ready(function () {
    let condicion = $("#pasante").val();
    if (condicion == "on") {
        $("#sueldo_pasante").attr("disabled", true);
    }
});
$(document).ready(function () {
    let condicion = $("#jornada").val();
    if (condicion == "0") {
        $("#horas_mes").attr("disabled", true);
        $("#dias_laborables").attr("disabled", true);
    }
});
function agregarEmpleado() {
    limpiarModal();
    $("#edit_nomina").val("");
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nuevo Empleado');
    $('#myModal').modal('toggle');
}

function diasHastaUnAnio(fechaReferencia) {
    // Convertir la fecha de referencia a un objeto Date
    var fechaReferenciaDate = new Date(fechaReferencia);

    // Sumar un año a la fecha de referencia
    var aniversarioDate = new Date(fechaReferenciaDate.getFullYear() + 1, fechaReferenciaDate.getMonth(), fechaReferenciaDate.getDate());

    // Obtener la fecha actual
    var fechaActual = new Date();

    // Calcular la diferencia en milisegundos entre la fecha de aniversario y la fecha actual
    var diferenciaMs = aniversarioDate.getTime() - fechaActual.getTime();

    // Convertir la diferencia en días
    var diasRestantes = Math.ceil(diferenciaMs / (1000 * 60 * 60 * 24)) + 1;

    return diasRestantes;
}

function tipo_pasante(item) {
    //alert(item.value);
    let condicion = $("#pasante").is(":checked");
    if (condicion) {
        $('#sueldo_pasante').removeAttr("disabled");
    } else {
        $('#sueldo_pasante').attr("disabled", "disabled");
    }
    console.log(item);
}
function tipo_jornada(item) {
    //alert(item.value);
    if (item.value == '1' || item.value == '2') {
        $('#horas_mes').removeAttr("disabled");
        $('#dias_laborables').removeAttr("disabled");
    } else {
        $('#horas_mes').attr("disabled", "disabled");
        $('#dias_laborables').attr("disabled", "disabled");
    }
    console.log(item);
}
function verEmpleado(id) {
    limpiarModal();
    $.ajax({
        url: "v3/" + $("#controlador").val() + "/getEmpleados",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
            id: id,
        },
        dataType: "json",
        success: function (json) {
            $("#edit_nomina").val(1);
            $("#titulo_modalEmpleado").html('<i class="fas fa-edit"></i> ' + " Empleado: " + capitalizar(json.nombres));
            $("#id_user").val(json.id_user);
            $("#apellido1").val(json.apellido1);
            $("#apellido2").val(json.apellido2);
            $("#nombre1").val(json.nombre1);
            $("#nombre2").val(json.nombre2);
            $("#pais").val(json.pais).trigger("change");
            $("#etapa").val(json.etapa);
            $("#manzana").val(json.manzana);
            $("#villa").val(json.villa);
            $("#fecha_nacimiento").val(json.fecha_nacimiento);
            $("#ocupacion").val(json.ocupacion);
            $("#genero").val(json.genero).trigger("change");
            $("#etnia").val(json.etnia);
            $("#discapacidad").val(json.discapacidad);
            $("#nivel_academico").val(json.nivel_academico).trigger("change");
            $("#estado_civil").val(json.estado_civil).trigger("change");
            $("#numero_cargas").val(json.numero_cargas);
            $("#telefono1").val(json.telefono1);
            $("#telefono2").val(json.telefono2);
            $("#direccion").val(json.direccion);
            $("#email").val(json.email);
            $("#mail_opcional").val(json.mail_opcional);
            $("#area").val(json.area).trigger("change");
            $("#cargo").val(json.cargo).trigger("change");
            $("#fecha_ingreso").val(json.fecha_ingreso);
            $("#fecha_ant").val(json.fecha_ant);
            $("#pago_beneficio").val(json.pago_fondo_reserva).trigger("change");
            $("#decimo_tercero").val(json.decimo_tercero).trigger("change");
            $("#decimo_cuarto").val(json.decimo_cuarto).trigger("change");
            $("#horario").val(json.horario).trigger("change");
            $("#codigo_sucursal").val(json.id_sucursal).trigger("change");
            $("#aporte_personal").val(json.aporte_personal).trigger("change");
            $("#banco").val(json.banco).trigger("change");
            $("#sueldo_neto").val(json.sueldo_neto);
            $("#extension_conyuge").val(json.extension_conyuge);
            if(json.pasante == 1){
                $('#pasante').attr('checked', true);
            }
            $("#pasante").val(json.pasante).trigger("change");
            $("#sueldo_pasante").val(json.sueldo_pasante);
            $("#numero_cuenta").val(json.numero_cuenta);
            $("#funciones").val(json.funciones);
            $("#jornada").val(json.jornada).trigger("change");
            $("#horas_mes").val(json.horas_mes);
            $("#dias_laborables").val(json.dias_laborables);
            $("#bono").val(json.bono);
            $("#bono_imputable").val(json.bono_imputable);
            $("#impuesto_renta").val(json.impuesto_renta);
            $("#alimentacion").val(json.alimentacion);
            $("#parqueo").val(json.parqueo);
            $("#movilizacion").val(json.movilizacion);
            $("#seguro_privado").val(json.seguro_privado);
            
           
            /*if (json.logo !== null && json.logo !== '') {
              alert('../storage/app/logo/' + json.logo);
              $('#logoProveedor').html('<img src="../storage/app/logo/' + json.logo + ' />');
            }
            else {
              $('#logoProveedor').html('');
            }*/
            //idPais = json.id_pais_ciudad;
            idProvincia = json.id_provincia_ciudad;
            idCiudad = json.id_ciudad;
            //$("#pais").val(idPais).trigger("change");
            if (json.estado == 1) {
                $("#cheestado").attr("checked", true);
                $("#estado").val(1);
            } else {
                $("#cheestado").attr("checked", false);
                $("#estado").val(0);
            }
            if (json.ruta_cedula !== null)
                $("#lblempleadoCedula").html('Cedula: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id_user").val() + '&t=empleadoCedula" target="new">ver/descargar</a>');
            else $("#lblempleadoCedula").html("Cedula");
            if (json.ruta_ficha_ocupacional !== null)
                $("#lblFichaOcupacional").html('Ficha Ocupacional: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id_user").val() + '&t=empleadoFichaOcupacional" target="new">ver/descargar</a>');
            else $("#lblFichaOcupacional").html("Ficha Ocupacional");
            if (json.ruta_ficha_tecnica !== null)
                $("#lblFichaTecnica").html('Ficha Técnica: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id_user").val() + '&t=empleadoFichaTecnica" target="new">ver/descargar</a>');
            else $("#lblFichaTecnica").html("Ficha Técnica");
            $("#myModal").modal("toggle");
        },
        error(error) {
            console.log(error);
        },
    });
}

