$(document).ready(function () {



    $("#tableMarca").DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " START " + $('#a').val() + " END " + $('#de').val() + " TOTAL " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " MAX total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " MENU " + $('#entradas').val(),
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
        "ordering": false,
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
                "X-CSRF-TOKEN": $("#token").val(),
            }, error(error) {
                console.log(error);

            }
        },
        columns: [{
            data: 'nombre'
        }, {
            data: 'descripcion'
        },
        {
            className: 'text-center',
            data: 'id',
            render: function (data, type, row) {
                var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
                if (row.estado == 1) {
                    accion = '<i class="fas fa-times-circle" style="color:red;"></i> Inactivar';
                }
                var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verMarca(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Marca');\">" + accion + "</a></div></div>";
                return botones;
            }
        }],
    });


    $("#tableTipo").DataTable({
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
                data: "nombre",
            },
            {
                data: "descripcion",
            },
            {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verTipo(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoTipo(\'' + row.id + "','" + row.estado + "');\">" + accion + "</a></div></div>";
                    return botones;
                }
            }],
    });
    $("#tableUnidades").DataTable({
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
                data: "nombre",
            },
            {
                data: "descripcion",
            },
            {
                className: 'text-center',
                data: 'id',
                render: function (data, type, row) {
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
                    }
                    var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verUnidades(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoUnidadesM(\'' + row.id + "','" + row.estado + "');\">" + accion + "</a></div></div>";
                    return botones;
                }
            }],
    });
    $("#tableMantenimientoBodega").DataTable({
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
                "X-CSRF-TOKEN": $("#token").val(),
            }, error(error) {
                console.log(error);

            }
        },
        columns: [{
            className: 'text-center',
            data: 'nombre'
        }, {
            className: 'text-center',
            data: 'nombre_hospital'
        },
        {
            data: 'nombre'
        },
        {
            className: 'text-center',
            data: 'nombre'
        },
        {
            className: 'text-center',
            data: 'nombre'
        },
        {
            className: 'text-center',
            data: 'nombre'
        }, {
            className: 'text-center',
            data: 'id',
            render: function (data, type, row) {
                var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
                if (row.estado == 1) {
                    accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
                }
                var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verBodega(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Transportista');\"|>" + accion + "</a></div></div>";
                return botones;
            }
        }],
    });
    $("#btnMarca").on("click", function () {

        if ($("#nombre").val() == "") {
            return mensajes("warning", "Ingrese el nombre de la marca...");
        } else if ($("#descripcion").val() == "") {
            return mensajes("warning", "Inserte la descripcion...");
        } else if ($("#estado").val() == "") {
            return mensajes("warning", "Inserte el estado...");
        }
        else {

            mensajes(
                "question",
                $("#pregunta").val(),
                "frm_create_bodega",
                "action",
                "",
                "tableMarca"
            );



        }
    });



    $("#btnBodegaNueva").on("click", function () {
        if ($("#nombre_bodega").val() == "") {
            return mensajes("warning", "Ingrese el nombre de la bodega...");
        } else if ($("#hospital").val() == "") {
            return mensajes("warning", "Inserte el hospital...");
        } else if ($("#tipo_bodega").val() == "") {
            return mensajes("warning", "Inserte el tipo bodega...");
        }
        else if ($("#estado").val() == "") {
            return mensajes("warning", "Inserte el estado...");
        }
        else if ($("#importacion").val() == "") {
            return mensajes("warning", "Indique si es una importación...");
        }
        else if ($("#departamento").val() == "") {
            return mensajes("warning", "Inserte el departamento...");
        } else {

            mensajes(
                "question",
                $("#pregunta").val(),
                "frm_create_bodega",
                "action",
            );

        }
    });

    $("#btnTipoNueva").on("click", function () {

        if ($("#nombre").val() == "") {
            return mensajes("warning", "Ingrese el nombre...");
        } else if ($("#descripcion").val() == "") {
            return mensajes("warning", "Inserte una descripcion...");
        } else {

            mensajes(
                "question",
                $("#pregunta").val(),
                "frm_",
                "action",
            );

        }
    });

    $("#btnUnidadNueva").on("click", function () {

        if ($("#nombre").val() == "") {
            return mensajes("warning", "Ingrese el nombre...");
        } else if ($("#descripcion").val() == "") {
            return mensajes("warning", "Inserte una descripcion...");
        } else {

            mensajes(
                "question",
                $("#pregunta").val(),
                "frm_",
                "action",
            );

        }
    });



    $('#modalBodega').on('click', function (e) {
        e.preventDefault();
        $('#modal').modal('show');
    });


});

function agregar() {
    limpiarModal();
    $("#id").val("");
    $("#cheestado").attr("checked", true);
    $("#estado").val(1);
    $("#titulo_modal").html('<i class="fas fa-plus-square"></i> ' + $("#nuevo").val() + " " + $("#submodulo_").val());
    $("#myModal").modal("toggle");
}

function agregarTipo() {
    limpiarModal();
    $("#id").val("");
    $("#cheestado").attr("checked", true);
    $("#estado").val(1);
    $("#titulo_modal").html('<i class="fas fa-plus-square"></i> ' + $("#nuevo").val() + " " + $("#submodulo_").val());
    $("#myModal").modal("toggle");
}


function verMarca(id) {

    var data = { id: id };
    var json = ajax("v3/" + $("#controlador").val() + "/getMarca", data, 'json');
    json = json.responseJSON;
    if (json != null) {
        $("#nombre").val(json.nombre);
        $("#id").val(json.id);
        $("#descripcion").val(json.descripcion);
        $("#estado").val(json.estado).trigger("change");
        $("#btnMarca").html("Editar");
        $("#titulo").html("Editar Marca");
        $("#modal").modal("show");

    }

}

function verBodega(id) {
    var data = { id: id };
    var json = ajax("v3/" + $("#controlador").val() + "/getBodega", data, 'json');
    json = json.responseJSON;
    if (json != null) {
        $("#nombre_bodega").val(json.nombre);
        $("#hospital").val(json.id_hospital).trigger("change");
        $("#departamento").val(json.departamento).trigger("change");
        $("#estado").val(json.estado).trigger("change");
        $("#color_etiqueta").val(json.color);
        $("#tipo_bodega").val(json.tipo);
        $("#importacion").val(json.importacion);
        $("#id").val(json.id);
        $("#titulo").html("Editar Bodega");
        $("#btnBodegaNueva").html("Editar");
        $("#modal").modal("show");

    }
}

function cambiarEstadoMarca(id, estado) {
    Swal.fire({
        title: '¿Está seguro de cambiar el estado de la marca?',
        text: 'Esta acción no se puede revertir.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Sí, continuar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            anularMarca(id, estado);
        }
    })
}

async function anularMarca(id, estado) {
    $.ajax({
        url: "v3/" + $('#controlador').val() + "/cambiarEstadoMarca",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
            id: id,
            estado: estado
        },
        datatype: "json",
        success: function (data) {
            const response = JSON.parse(data);

            if (response.status === 'success') {
                mensajes('success', response.message, "", "", "", "tableMarca");
            } else {
                mensajes('error', response.message);
            }
        },

        error: function (json) {
            console.log(json);
        },
    });
}


function cambiarEstadoBodega(id, estado) {
    Swal.fire({
        title: '¿Está seguro de cambiar el estado de la bodega?',
        text: 'Esta acción no se puede revertir.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Sí, continuar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            anularBodega(id, estado);
        }
    })
}

async function anularBodega(id, estado) {
    $.ajax({
        url: "v3/" + $('#controlador').val() + "/cambiarEstado",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
            id: id,
            estado: estado
        },
        datatype: "json",
        success: function (data) {
            const response = JSON.parse(data);

            if (response.status === 'success') {
                mensajes('success', response.message, "", "", "", "tableMantenimientoBodega");
            } else {
                mensajes('error', response.message);
            }
        },

        error: function (json) {
            console.log(json);
        },
    });
}


function verUnidades(id) {
    var data = { id: id };
    var json = ajax("v3/" + $("#controlador").val() + "/getUnidadesMedida", data, 'json');
    json = json.responseJSON;
    if (json != null) {
        $("#nombre").val(json.nombre);
        $("#descripcion").val(json.descripcion);
        $("#estado").val(json.estado);
        $("#id").val(json.id);
        $("#titulo").html("Editar Unidades");
        $("#btnUnidadNueva").html("Editar");
        $("#myModal").modal("show");

    }
}

async function cambiarEstadoUnidadesM(id, estado) {

    $.ajax({
        url: "v3/" + $('#controlador').val() + "/cambiarEstadoUnidadesM",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
            id: id,
            estado: estado
        },
        datatype: "json",
        success: function (data) {
            const response = JSON.parse(data);

            if (response.status === 'success') {
                mensajes('success', response.message, "", "", "", "tableMantenimientoBodega");
            } else {
                mensajes('error', response.message);
            }
        },

        error: function (json) {
            console.log(json);
        },
    });
}
function cambiarEstadoUnidades(id, estado) {
    Swal.fire({
        title: '¿Está seguro de cambiar el estado de la Unidad?',
        text: 'Esta acción no se puede revertir.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Sí, continuar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            cambiarEstadoUnidadesM(id, estado);
        }
    })
}



function verTipo(id) {
    var data = { id: id };
    var json = ajax("v3/" + $("#controlador").val() + "/getTipo", data, 'json');
    json = json.responseJSON;
    if (json != null) {
        $("#nombre").val(json.nombre);
        $("#descripcion").val(json.descripcion);
        $("#estado").val(json.estado);
        $("#id").val(json.id);
        $("#titulo1").html("Editar Unidades");
        $("#btnTipoNueva").html("Editar");
        $("#myModal").modal("show");

    }
}


async function cambiarEstadoTipoI(id, estado) {

    $.ajax({
        url: "v3/" + $('#controlador').val() + "/cambiarEstadoTipoI",
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
            id: id,
            estado: estado
        },
        datatype: "json",
        success: function (data) {
            const response = JSON.parse(data);

            if (response.status === 'success') {
                mensajes('success', response.message, "", "", "", "tableMantenimientoBodega");
            } else {
                mensajes('error', response.message);
            }
        },

        error: function (json) {
            console.log(json);
        },
    });
}
function cambiarEstadoTipo(id, estado) {
    Swal.fire({
        title: '¿Está seguro de cambiar el estado del Tipo?',
        text: 'Esta acción no se puede revertir.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Sí, continuar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            cambiarEstadoTipoI(id, estado);
        }
    })
}


$('#modal').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
});


