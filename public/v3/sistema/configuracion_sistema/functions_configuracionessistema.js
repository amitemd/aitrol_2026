$(document).ready(function () {
    $('#elementTour').val('hero-contenido,hero-accion,hero-agregar');
    try {
        // alert($('#controlador').val()+' '+$('#submodulo_').val());
        $("#tableUbicaciones").DataTable({
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
            order: [[0, "desc"]],
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
                data: 'nombre_hospital'
            }, {
                data: 'ciudad'
            }, {
                data: 'direccion'
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
            },{
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    console.log(row);
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $('#activar').val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verUbicacion(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + '\',\'' + row.estado + '\',\'ConfigUbicacion\');">' + accion + '</a></div></div>';
                    return botones;
                }
            }],
        });

        $("#tableEspecialidades").DataTable({
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
            order: [[0, "desc"]],
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
                data: 'nombre'
            }, {
                data: 'descripcion'
            }, {
                data: 'created_at'
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
            },{
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    console.log(row);
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $('#activar').val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verEspecialidad(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + '\',\'' + row.estado + '\',\'ConfigEspecialidad\');">' + accion + '</a></div></div>';
                    return botones;
                }
            }],
        });

    } catch (error) { console.log(error); }

    $('#btnGuardarUbicacion').click(function () {
        mensajes('question', $('#pregunta').val())
    });

    $('#btnGuardarEspecialidad').click(function () {
        mensajes('question', $('#pregunta').val())
    });

});

function agregarUbicacion(){
    limpiarModal();
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nueva Ubicacion');
    $('#myModal').modal('toggle');
}

function agregarEspecialidad(){
    limpiarModal();
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nueva Especialidad');
    $('#myModal').modal('toggle');
}


function verUbicacion(id) {
    $.ajax({
        url: 'v3/' + $('#controlador').val() + '/getUbicacion',
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('#token').val(),
        },
        data: {
            id: id
        },
        dataType: 'json',
        success: function (json) {
            console.log(json);
            $('#id').val(json.id);
            $('#nombre_hospital').val(json.nombre_hospital);
            $('#ciudad').val(json.ciudad);
            $('#direccion').val(json.direccion);
            $('#telefono1').val(json.telefono1);
            $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Modificar Ubicación');
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

function verEspecialidad(id) {
    $.ajax({
        url: 'v3/' + $('#controlador').val() + '/getEspecialidad',
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('#token').val(),
        },
        data: {
            id: id
        },
        dataType: 'json',
        success: function (json) {
            console.log(json);
            $('#id').val(json.id);
            $('#nombre').val(json.nombre);
            $('#descripcion').val(json.descripcion);
            $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Modificar Especialidad');
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