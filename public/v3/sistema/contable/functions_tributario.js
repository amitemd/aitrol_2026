var tableGestionDocumentos = null;

$(document).ready(function () {
    if ($('#submodulo_').val() == 'declaraciones') {
        $(".botonMenu").trigger("click");
    }
   
    let verificarS = $("#verificarS").val();

    if(verificarS !== ""){
        console.log("entre");
        $("#checkSemestral").trigger("click");
        $("#reservation").val($("#fechaR").val());
    }

    try {
        $('#reservation').daterangepicker({
            autoUpdateInput: true, // No actualizar automáticamente el input
            locale: {
                cancelLabel: 'Cancelar',
                applyLabel: 'Aplicar',
                format: 'DD/MM/YYYY',
            }
        });
        $('#daterange').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        });
        $('#daterange').on('cancel.daterangepicker', function (ev, picker) {
            // Limpiar el input si se cancela la selección
            $(this).val('');
        });

        if ($('#aux').val() !== null && $('#aux').val() !== '') {
            document.getElementById('ventas-tab').classList.remove('active');
            document.getElementById('ventas').classList.remove('show', 'active');
            document.getElementById('ats_generado-tab').classList.add('active');
            document.getElementById('ats_generado').classList.add('show', 'active');
        }
    } catch (error) {

    }

    $('#tableVentasAts').DataTable({
        responsive: false,
        lengthChange: false,
        info: true,
        paginate: false,
    });
    var tableImportarDocumentos = $('#tableImportarDocumentos').DataTable({
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
        lengthChange: false,
        autoWidth: false,
        info: false,
        searching: false,
    });
    tableGestionDocumentos = $('#tableGestionDocumentos').DataTable({
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
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        processing: true,
        columns: columnasGestionDocumentos(),
        lengthChange: true,
        autoWidth: false,
        info: true,
        searching: true,
    });

    $('#btnBuscarDocumentos').on('click', buscarDocumentos);
    $('#tableGestionDocumentos').on('click', '.js-ver-log-documento', function () {
        var row = filaGestionDocumentos(this);
        if (row) {
            verLogDocumento(row.id, row.tipo);
        }
    });
    $('#tableGestionDocumentos').on('click', '.js-ver-comprobante', function () {
        var row = filaGestionDocumentos(this);
        if (row) {
            verComprobante(row.tipo, row.id, row.comprobante);
        }
    });
    $('#tableGestionDocumentos').on('click', '.js-ver-log', function () {
        var row = filaGestionDocumentos(this);
        if (row) {
            verLog(row.tipo, row.id, row.comprobante);
        }
    });
    $('#cuerpoDocumento').on('click', '.js-reenviar-documento', function () {
        reenviar($(this).data('tipo'), $(this).data('id'));
    });
    if ($('#tableGestionDocumentos').length > 0) {
        buscarDocumentos();
    }
    $('#docSri').change(function () {
        $('#loading').css({ 'position': 'absolute', 'top': '50%', 'left': '50%', 'transform': 'translate(-50%, -50%)' });
        $('#loading').html('<img src="http://www.dae.ugto.mx/solicitudcondonacion/estilos/media/Procesando.gif" alt="Procesando..." height="100" width="150">');
        var inputFileImage = document.getElementById('docSri');
        var file = inputFileImage.files[0];
        var fileSize = file.size;
        var siezekiloByte = parseInt(fileSize / 1024);
        if (siezekiloByte > 1072) {
            $('#estado_firma').html('');
            $('#estado_firma').css({ 'display': 'none' });
            $('#docSri').val('');
            $('#loading').html('');
            alert('Error: el archivo es muy pesado para ser procesado');
        } else {
            var fd = new FormData();
            fd.append('docSri', file);
            fd.append('tipo_archivo', 'docSri');
            fd.append('_token', $('input[name=_token]').val());
            $.ajax({
                url: "v3/" + $("#controlador").val() + "/subirArchivoSri",
                data: fd,
                processData: false,
                contentType: false,
                type: 'post',
                datatype: 'json',
                success: function (data) {
                    if (data.length == 0) {
                        $('#loading').html('');
                        return mensajes('info', 'Parece que el archivo que se ha subido no es el correspondiente a la empresa...');
                    }
                    $('#tableImportarDocumentos').dataTable().fnClearTable();
                    $('#tableImportarDocumentos').dataTable().fnDestroy();
                    var array = JSON.parse(data);
                    arrayDocumentos = array;
                    $('#docSri').val('');
                    $('#loading').html('');
                    if (array.length > 200) {
                        $('#registros').html(array.length);
                        $('#panel').show();
                    } else {
                        $('#registros').html(0);
                        $('#panel').hide();
                        $('#tableImportarDocumentos').DataTable({
                            responsive: true,
                            order: [[0, 'asc']],
                            pageLength: 100,
                            data: array,
                            columns: [
                                { data: "no" },
                                {
                                    data: "estado",
                                    render: function (data, type, row) {
                                        var acciones = '<a class="dropdown-item" href="javascript:;" onclick="abrirModal(\'' + row.clave_acceso + '\',\'' + row.tipo + '\',\'' + row.num_comprobante + '\');"><i class="fa fa-file-code-o" style="color:blue;"></i> Ingresar </a>';
                                        if (/Subido/.test(row.estado)) {
                                            acciones = '<a class="dropdown-item" href="javascript:;" onclick="verProveedor(\'' + row.id + '\');"></i> <i class="fa fa-check-circle" style="color:gray;"></i> Ingresado </a>';
                                        }
                                        var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fa fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu">' + acciones + '</div></div>';
                                        return botones;
                                    }
                                },
                                {
                                    className: 'text-center',
                                    data: "estado",
                                    render: function (data, type, row) {
                                        var estado = '';
                                        if (row.estado == 'Subido') {

                                            estado = '<span class="badge bg-success">Ingresado</span>';
                                        } else {
                                            estado = '<span class="badge bg-danger">Sin subir</span>';
                                        }
                                        return estado;
                                    }
                                },
                                { data: "tipo" },
                                { data: "fecha_emision" },
                                { data: "num_comprobante" },
                                {
                                    className: 'text-right',
                                    data: "total",
                                    render: function (data, type, row) {
                                        return parseFloat(row.total).toFixed(2);
                                    }
                                },
                                { data: "ruc_emisior" },
                                { data: "razon_social" },
                                { data: "fecha_autorizacion" },
                                { data: "clave_acceso" },
                                { data: "num_autorizacion" }
                            ]
                        });
                    }
                }
            });
        }
    });
    calcularFormulario104();
});
function abrirModal(a, b, c) {

}
function columnasGestionDocumentos() {
    return [
        { className: 'text-center', data: 'id' },
        {
            data: null,
            orderable: false,
            searchable: false,
            render: function () {
                return '<button type="button" class="btn btn-sm btn-' + textoSeguro($('#colorBoton').val()) + ' js-ver-log-documento" title="Ver eventos"><i class="fa fa-align-left"></i></button>';
            }
        },
        { className: 'text-center', data: 'tipo' },
        { className: 'text-center', data: 'fecha' },
        {
            data: 'comprobante',
            render: function (data, type, row) {
                if (type !== 'display') {
                    return data;
                }
                var estado = textoSeguro(row.estado || 'sin estado').toLowerCase();
                var estadoHtml = '<span class="text-secondary">' + textoSeguro(row.estado || 'Sin estado') + '</span>';
                if (parseInt(row.estado_comprobante, 10) === 0) {
                    estadoHtml = '<span class="text-danger"><i class="fa fa-ban"></i> Anulada</span>';
                } else if (estado === 'autorizado') {
                    estadoHtml = '<span class="text-success"><i class="fa fa-check-circle"></i> Autorizado</span>';
                } else if (estado === 'guardado') {
                    estadoHtml = '<span class="text-warning"><i class="fa fa-save"></i> Guardado</span>';
                } else if (estado === 'devuelto' || estado === 'recibido' || estado === 'firmado xml' || estado === 'no autorizado') {
                    estadoHtml = '<button type="button" class="btn btn-link btn-sm p-0 js-ver-log">' + textoSeguro(row.estado) + '</button>';
                }
                return '<button type="button" class="btn btn-link p-0 js-ver-comprobante">' + textoSeguro(data) + '</button><br>' + estadoHtml;
            }
        },
        {
            className: 'text-right',
            data: 'total',
            render: function (data, type) {
                var numero = parseFloat(data || 0);
                return type === 'display' ? numero.toFixed(2) : numero;
            }
        },
        { className: 'text-center', data: 'ruc' },
        {
            data: 'nombre',
            render: function (data, type) {
                return type === 'display' ? '<span class="small">' + textoSeguro(data) + '</span>' : data;
            }
        },
        {
            className: 'text-center',
            data: 'fecha_autorizacion',
            defaultContent: '',
            render: function (data, type) {
                if (type !== 'display') {
                    return data || '';
                }
                return data ? textoSeguro(data) : '<span class="text-muted">Pendiente</span>';
            }
        }
    ];
}

function textoSeguro(valor) {
    return $('<div>').text(valor === null || typeof valor === 'undefined' ? '' : valor).html();
}

function filaGestionDocumentos(elemento) {
    var tr = $(elemento).closest('tr');
    if (tr.hasClass('child')) {
        tr = tr.prev();
    }
    return tableGestionDocumentos ? tableGestionDocumentos.row(tr).data() : null;
}

function urlGestionDocumentos(accion) {
    return $('#base_url').val() + '/v3/' + $('#controlador').val() + '/' + accion;
}

function mensajeErrorAjax(xhr, mensajePredeterminado) {
    if (xhr.responseJSON && xhr.responseJSON.message) {
        return xhr.responseJSON.message;
    }
    return mensajePredeterminado;
}

function buscarDocumentos() {
    if ($('#desde').val() == '') {
        return mensajes('info', 'Debe seleccionar una fecha de inicio');
    } else if ($('#hasta').val() == '') {
        return mensajes('info', 'Debe seleccionar una fecha final');
    }

    var desde = new Date($('#desde').val() + 'T00:00:00');
    var hasta = new Date($('#hasta').val() + 'T00:00:00');
    var dias = Math.round((hasta.getTime() - desde.getTime()) / 86400000);
    if (dias < 0 || dias > 92) {
        return mensajes('info', 'Seleccione un rango válido de hasta 92 días');
    }

    var boton = $('#btnBuscarDocumentos');
    boton.prop('disabled', true).find('.botones').text('Consultando...');
    $.ajax({
        url: urlGestionDocumentos('buscarDocumentos'),
        type: 'POST',
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('#token').val() },
        data: {
            desde: $('#desde').val(),
            hasta: $('#hasta').val(),
            tipo: $('#tipo').val(),
            estado: $('#estado').val()
        },
        success: function (data) {
            var filas = $.isArray(data) ? data : [];
            tableGestionDocumentos.clear().rows.add(filas).draw();
            if (filas.length === 1000) {
                mensajes('info', 'Se muestran los primeros 1000 documentos. Reduzca el rango para una consulta más precisa.');
            }
        },
        error: function (xhr) {
            mensajes('error', mensajeErrorAjax(xhr, 'No fue posible consultar los documentos.'));
        },
        complete: function () {
            boton.prop('disabled', false).find('.botones').text('Buscar');
        }
    });
}

function abrirModalDocumento(titulo) {
    $('#titulo_modal_documentos').empty().append($('<i>', { 'class': 'fa fa-plus-square mr-1' })).append(document.createTextNode(titulo));
    $('#cuerpoDocumento').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Consultando...</div>');
    $('#myModalDocumentos').modal('show');
}

function cargarModalDocumento(accion, datos, titulo) {
    abrirModalDocumento(titulo);
    $.ajax({
        url: urlGestionDocumentos(accion),
        type: 'POST',
        dataType: 'html',
        headers: { 'X-CSRF-TOKEN': $('#token').val() },
        data: datos,
        success: function (html) {
            $('#cuerpoDocumento').html(html);
        },
        error: function (xhr) {
            $('#cuerpoDocumento').html('<div class="alert alert-danger"></div>');
            $('#cuerpoDocumento .alert').text(mensajeErrorAjax(xhr, 'No fue posible obtener la información del documento.'));
        }
    });
}

function verComprobante(tipo, id, comprobante) {
    cargarModalDocumento('verComprobante', { tipo: tipo, id: id }, tipo + ': ' + comprobante);
}

function verLog(tipo, id, comprobante) {
    cargarModalDocumento('verLog', { tipo: tipo, id: id }, 'Log de ' + tipo + ': ' + comprobante);
}

function verLogDocumento(id, tipo) {
    cargarModalDocumento('verLogDocumento', { tipo: tipo, id: id }, 'Eventos de ' + tipo + ': ' + id);
}

function reenviar(tipo, id) {
    if (!window.confirm('¿Desea enviar nuevamente este documento para su procesamiento?')) {
        return;
    }

    $.ajax({
        url: urlGestionDocumentos('reenviar'),
        type: 'POST',
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('#token').val() },
        data: { tipo: tipo, id: id },
        success: function (respuesta) {
            if (!respuesta || respuesta.status !== 'success') {
                return mensajes('error', respuesta && respuesta.message ? respuesta.message : 'No fue posible reenviar el documento.');
            }
            $('#myModalDocumentos').modal('hide');
            mensajes('success', respuesta.message);
            buscarDocumentos();
        },
        error: function (xhr) {
            mensajes('error', mensajeErrorAjax(xhr, 'No fue posible reenviar el documento.'));
        }
    });
}
function seleccionarTodosVentas(source) {
    var checkboxes = document.getElementsByName('seleccionVentas[]');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}
function seleccionarTodosCompras(source) {
    var checkboxes = document.getElementsByName('seleccionCompras[]');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}
function guardarVc() {
    $('#aux').val('si');
    mensajes("question", $("#pregunta").val(), "frm_declaraciones", "action2");
}
function calcularFormulario104() {
    var f409 = 0;
    var f419 = 0;
    var f421 = 0;
    var f422 = 0;
    var f429 = 0;
    var f482 = 0;

    f409 = parseFloat($('#401').val()) + parseFloat($('#402').val()) + parseFloat($('#410').val()) + parseFloat($('#403').val()) + parseFloat($('#404').val()) + parseFloat($('#405').val()) + parseFloat($('#406').val()) + parseFloat($('#407').val()) + parseFloat($('#408').val());
    f419 = parseFloat($('#411').val()) + parseFloat($('#412').val()) + parseFloat($('#420').val()) + parseFloat($('#413').val()) + parseFloat($('#414').val()) + parseFloat($('#415').val()) + parseFloat($('#416').val()) + parseFloat($('#417').val()) + parseFloat($('#418').val());
    var anio = Number($('#anio').val());
    var mes = Number($('#mes').val() - 1);
    var impuesto = calcularImpuesto(anio, mes);
    f421 = parseFloat($('#411').val() * impuesto);
    f422 = parseFloat($('#412').val() * impuesto);

    f429 = parseFloat($('#421').val()) + parseFloat($('#422').val()) + parseFloat($('#430').val()) + parseFloat($('#423').val()) - parseFloat($('#424').val()) + parseFloat($('#424').val());

    $('#409').val(parseFloat(f409).toFixed(2));
    $('#419').val(parseFloat(f419).toFixed(2));
    $('#421').val(parseFloat(f421).toFixed(2));
    $('#422').val(parseFloat(f422).toFixed(2));
    $('#429').val(parseFloat(f429).toFixed(2));

    $('#482').val(parseFloat(f429).toFixed(2));
}
function calcularImpuesto(anio, mes) {
    try {
        if (typeof anio !== 'number' || anio <= 0 || typeof mes !== 'number' || mes < 0 || mes > 11) {
            throw new Error('Año o mes inválido');
        }
        if (anio > 2024) {
            return 0.15;
        }
        if (anio === 2024) {
            if (mes >= 3) {
                return 0.15;
            } else {
                return 0.12;
            }
        }

        return 0.12;
    } catch (e) {
        console.log(typeof anio);
        console.log(e.message);
        return 0.12;
    }
}
function generarExcel() {
    let data = {
        anio: $("#anio").val(),
        mes: $("#mes").val(),
    };
    $.ajax({
        url: 'v3/' + $('#controlador').val() + '/excelTributacion',
        type: "post",
        headers: {
            "X-CSRF-TOKEN": $("#token").val(),
        },
        data: data,
        xhrFields: {
            responseType: 'blob'
        },
        success: function (data) {
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = 'declaraciones.xlsx';
            document.body.append(a);
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();

        }, error(error) {
            return error.message;
        }
    });
}
function semestral() {
    if ($("#checkSemestral").is(':checked')) {
        $('.oculto').css('display', 'initial');
    } else {
        $('.oculto').css('display', 'none');
    }
}
