$('#buscarEmpleado').click(function () {
    $.ajax({
        url: $('#asset_').val()+"contable/ventas/buscar",
        type: 'post',
        data: $('#reporte_master').serialize(),
        dataType: 'json',
        success: function (json) {
            if (json.respuesta == 'no')
                window.open('ventas?page=1', '_self');
            $('#tablaVentas').dataTable().fnClearTable();
            $('#tablaVentas').dataTable().fnDestroy();
            $('#tablaVentas').DataTable({
                paging: false,
                ordering: false,
                info: false,
                language: {
                    "decimal": "",
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                    "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                    "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ Entradas",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "Sin resultados encontrados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                "order": [
                    [0, "desc"]
                ],
                data: json,
                responsive: true,
                displayLengtn: 25,
                aoColumns: [{
                    data: 'id'
                }, {
                    className: 'text-center',
                    render: function (data, type, row) {
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + row.id_asiento + '</a>';
                    }
                }, {
                    className: 'text-center',
                    render: function (data, type, row) {
                        var estado = '';
                        let arr = row.nro_comprobante !== null ? row.nro_comprobante?.split('-') : [];
                        if (arr.includes('100')) {
                            estado += '<div class="btn-group">';
                            estado += '<button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" title="Acciones"><i class="fa fa-stack-overflow"></i> <span class="caret"></span></button>';
                            estado += '<ul class="dropdown-menu" role="menu">';
                            if (row.estado == 0) {
                                estado += '<li><a href="#" class="btn btn-success"><i class="fa fa-minus"></i> Anulado</a></li>';
                            } else {
                                estado += '<li><a href="javascript:anular(\'' + row.id + '\');" class="btn btn-danger"><i class="fa fa-close"></i> Anular</a></li>';
                            }
                            if (row.ct_orden_venta != null)
                                estado += '<li><a target="_blank" href="pdf_comprobante_detalle.paquete?id=$value->id" class="btn btn-success" style="">Orden</a> </li>';
                            estado += '<li><a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '" class="btn btn-warning"><i class="fa fa-eye"></i> Visualizar</a></li>';
                            if (row.doc_electronico == 5) {
                                estado += '<li><a href="'+$('#asset_').val()+'api_doc_electronico?opcion=descargarXML&clave=' + row.nro_autorizacion + '" class="btn btn-success"><i class="fa fa-file-code-o">XML</i></a></li>';
                                estado += '<li><a href="'+$('#asset_').val()+'api_doc_electronico?opcion=generarPdf&clave=' + row.nro_autorizacion + '" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> PDF</a></li>';
                            }
                            estado += '<li><a data-toggle="modal" onclick="limpiarModal()" data-target="#log_factura" href="'+$('#asset_').val()+'ventas/logFactura?id=' + row.id + '&tipo=01" class="btn btn-info"><i class="fa fa-list"></i> Log Factura</a></li>';
                            estado += '</ul>';
                            estado += '</div>';
                            //estado=1;
                        } else {
                            const vizualizar = $('#asset_').val()+'ventas/editar/' + row.id;
                            const vizualizar_pdf = 'ventas/pdf/visualizador/html/' + row.id;
                            estado += '<div class="btn-group">';
                            estado += '<button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" title="Acciones"><i class="fa fa-stack-overflow"></i> <span class="caret"></span></button>';
                            estado += '<ul class="dropdown-menu" role="menu">';
                            if (row.estado == 1) {
                                estado += '<li><a href="javascript:anular(\'' + row.id + '\');" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">&nbsp;&nbsp;ANULAR</a></li>';

                            } else if (row.estado == 0) {
                                estado += `<li><a class="btn btn-danger" disabled style="font-size: 10px">&nbsp;&nbsp;Anulado</a></li>`;
                            }
                            estado += '<li><a href="' + vizualizar + '" class="btn btn-warning" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Visualizar</a></li>';
                            if (row.permisos) {
                                estado += '<li><a href="' + vizualizar_pdf + '" class="btn btn-danger" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Ventas IECED</a></li>';
                            }
                            if (row.rutapdf != '') {
                                estado += `<li><a target="_blank" href="pdf.visualizar?id=${row.id}" class="btn btn-warning btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Archivo</a></li>`;
                            }
                            if (row.ct_orden_venta != null) {
                                estado += `<li><a target="_blank" href="pdf_comprobante_detalle.paquete?id=${row.id}" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Detalle orden</a></li>`;
                            }
                            if (row.electronica == 0) {
                                if (row.ip_creacion == 'OMNI') {

                                    estado += `<li><a target="_blank" href="ventas.pdf_omni?id=${row.id}" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">PDF</a></li>`;
                                } else {

                                    estado += `<li><a target="_blank" href="contable/ventas/comprobante/no.tributario/${row.id}" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">PDF</a></li>`;
                                }
                            } else {
                                estado += `<li><a target="_blank" href="contable/ventas/ride_pdf/${row.nro_comprobante}/${row.id_empresa}/pdf" class="btn btn-primary" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Ride Factura</a></li>`;
                                estado += `<li><a target="_blank" href="contable/ventas/ride_pdf/${row.nro_comprobante}/${row.id_empresa}/xml" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">XML</a></li>`;
                                estado += `<li><a data-toggle="modal" data-target="#log_factura" href="../contable/ventas/ride_pdf/${row.nro_comprobante}/${row.id_empresa}/comprobante" class="btn btn-info" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Log factura</a></li>`;
                            }
                            if (row.usuario_actual == '0957258056') {

                                estado += `<li><button type="button" onclick="enviarSri(${row.id})" class="btn btn-success">Enviar SRI</button></li>`;
                            }

                            estado += `<ul class="dropdown-menu cot" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 69%;">`;
                            if (row.estado == 1) {
                                estado += '<li><a href="javascript:anular(\'' + row.id + '\');" class="btn btn-danger" style="">&nbsp;&nbsp;Ventas Anular</a> </li>';
                            } else if (row.estado == 0) {
                                estado += `<li><a class="btn btn-danger" disabled style="">&nbsp;&nbsp;Anular</a> </li>`;
                            }
                            estado += `<li><a href="ventas_editar?id=${row.id}" class="btn btn-warning" style="">Ventas Vizualizar</a> </li>`;
                            //estado=2;
                        }
                        estado += '</ul>';
                        estado += '</div>';
                        return estado;
                    }
                }, {
                    className: 'text-center',
                    render: function (data, type, row) {
                        var electronica = '<img src="../img/puntoplomo.png" style="width:15px;" title="Sin Autorizar" />';
                        let arr = row.nro_comprobante !== null ? row.nro_comprobante?.split('-') : [];
                        if (arr.includes(100)) {
                            if (row.doc_electronico == 5)
                                electronica = '<img src="../img/puntoverde.png" style="width:20px;" title="Autorizado" />';
                        } else {
                            if (row.estado_electronica == 3)
                                electronica = '<img src="../img/puntoverde.png" style="width:20px;" title="Autorizado" />';
                        }
                        return '<a href="javascript:;">' + electronica + '</a>';
                    }
                }, {
                    className: 'text-center',
                    render: function (data, type, row) {
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + row.nro_comprobante + '</a>';
                    }
                }, {
                    className: 'text-center',
                    render: function (data, type, row) {
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + row.tipo + '</a>';
                    }
                }, {
                    className: 'text-center',
                    render: function (data, type, row) {
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + row.fecha + '</a>';
                    }
                }, {
                    render: function (data, type, row) {
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + row.nombre_cliente + '</a>';
                    }
                }, {
                    render: function (data, type, row) {
                        var paciente = row.nombres_paciente !== null ? row.nombres_paciente : '';
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + paciente + '</a>';
                    }
                }, {
                    className: 'text-center',
                    render: function (data, type, row) {
                        console.log(row.seguro, row.id);
                        var seguro = row.seguro !== null ? row.seguro : '';
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + seguro + '</a>';
                    }
                }, {
                    render: function (data, type, row) {
                        var procedimientos = row.procedimientos !== null ? row.procedimientos : '';
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + procedimientos + '</a>';
                    }
                }, {
                    className: 'text-center',
                    render: function (data, type, row) {
                        var fecha_procedimiento = row.fecha_procedimiento !== null ? row.fecha_procedimiento : '';
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + fecha_procedimiento + '</a>';
                    }
                }, {
                    render: function (data, type, row) {
                        return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + row.nombre1 + ' ' + row.apellido1 + '</a>';
                    }
                }, {
                    className: 'text-center',
                    render: function (data, type, row) {
                        if (row.estado == 0)
                            return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + row.nombremod1 + ' ' + row.apellidomod1 + '</a>';
                        else
                            return '';
                    }
                },]
            });
        }
    });
})
//qui es
$(document).ready(function () {
    $('#detalle_paquete').on('hidden.bs.modal', function () {
        location.reload();
        $(this).removeData('bs.modal');
    });
    $('#edit_orde_det_paq').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });
    $('#log_factura').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });
    $('#md_planilla').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
    });
    $('#tablaVentas').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "order": [
            [0, "desc"]
        ],
        sAjaxSource: "../ventas/getventasjs?page=" + $('#page').val(),
        displayLengtn: 25,
        ordering: true,
        info: false,
        searching: false,
        paging: false,
        responsive: true,
        aoColumns: [{
            data: 'id'
        },
        {
            className: 'text-center',
            render: function (data, type, row) {
                return '<a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '">' + row.id_asiento + '</a>';
            }
        }
            , {
            className: 'text-center',
            render: function (data, type, row) {
                var estado = '';
                let arr = row.nro_comprobante !== null ? row.nro_comprobante?.split('-') : [];
                if (arr.includes('100')) {
                    estado += '<div class="btn-group">';
                    estado += '<button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" title="Acciones"><i class="fa fa-stack-overflow"></i> <span class="caret"></span></button>';
                    estado += '<ul class="dropdown-menu" role="menu">';
                    if (row.estado == 0) {
                        estado += '<li><a href="#" class="btn btn-success"><i class="fa fa-minus"></i> Anulado</a></li>';
                    } else {
                        estado += '<li><a href="javascript:anular(\'' + row.id + '\');" class="btn btn-danger"><i class="fa fa-close"></i> Anular</a></li>';
                    }
                    if (row.ct_orden_venta != null)
                        estado += '<li><a target="_blank" href="pdf_comprobante_detalle.paquete?id=$value->id" class="btn btn-success" style="">Orden</a> </li>';
                    estado += '<li><a href="'+$('#asset_').val()+'contable/ventas/editar/' + row.id + '" class="btn btn-warning"><i class="fa fa-eye"></i> Visualizar</a></li>';

                    if (row.doc_electronico == 5) {
                        estado += '<li><a href="'+$('#asset_').val()+'api_doc_electronico?opcion=descargarXML&clave=' + row.nro_autorizacion + '" class="btn btn-success"><i class="fa fa-file-code-o">XML</i></a></li>';
                        estado += '<li><a href="'+$('#asset_').val()+'api_doc_electronico?opcion=generarPdf&clave=' + row.nro_autorizacion + '" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> PDF</a></li>';
                    }
                    estado += '<li><a data-toggle="modal" onclick="limpiarModal()" data-target="#log_factura" href="'+$('#asset_').val()+'ventas/logFactura?id=' + row.id + '&tipo=01" class="btn btn-info"><i class="fa fa-list"></i> Log Factura</a></li>';
                    estado += '</ul>';
                    estado += '</div>';
                    //estado=1;
                } else {
                    const vizualizar = 'ventas/editar/' + row.id;
                    const vizualizar_pdf = 'ventas/pdf/visualizador/html/' + row.id;
                    estado += '<div class="btn-group">';
                    estado += '<button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" title="Acciones"><i class="fa fa-stack-overflow"></i> <span class="caret"></span></button>';
                    estado += '<ul class="dropdown-menu" role="menu">';
                    if (row.estado == 1) {
                        estado += '<li><a href="javascript:anular(\'' + row.id + '\');" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">&nbsp;&nbsp;ANULAR</a></li>';

                    } else if (row.estado == 0) {
                        estado += `<li><a class="btn btn-danger" disabled style="font-size: 10px">&nbsp;&nbsp;Anulado</a></li>`;
                    }
                    estado += '<li><a href="' + vizualizar + '" class="btn btn-warning" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Visualizar</a></li>';
                    if (row.permisos) {
                        estado += '<li><a href="' + vizualizar_pdf + '" class="btn btn-danger" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Ventas IECED</a></li>';
                    }
                    if (row.rutapdf != '') {
                        estado += `<li><a target="_blank" href="../pdf.visualizar?id=${row.id}" class="btn btn-warning btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Archivo</a></li>`;
                    }
                    if (row.ct_orden_venta != null) {
                        estado += `<li><a target="_blank" href="../pdf_comprobante_detalle.paquete?id=${row.id}" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Detalle orden</a></li>`;
                    }
                    if (row.electronica == 0) {
                        if (row.ip_creacion == 'OMNI') {

                            estado += `<li><a target="_blank" href="../ventas.pdf_omni?id=${row.id}" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">PDF</a></li>`;
                        } else {

                            estado += `<li><a target="_blank" href="../contable/ventas/comprobante/no.tributario/${row.id}" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">PDF</a></li>`;
                        }
                    } else {
                        estado += `<li><a target="_blank" href="../contable/ventas/ride_pdf/${row.nro_comprobante}/${row.id_empresa}/pdf" class="btn btn-primary" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Ride Factura</a></li>`;
                        estado += `<li><a target="_blank" href="../contable/ventas/ride_pdf/${row.nro_comprobante}/${row.id_empresa}/xml" class="btn btn-success" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">XML</a></li>`;
                        estado += `<li><a data-toggle="modal" data-target="#log_factura" href="../contable/ventas/ride_pdf/${row.nro_comprobante}/${row.id_empresa}/comprobante" class="btn btn-info" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Log factura</a></li>`;
                    }
                    if (row.usuario_actual == '0957258056') {

                        estado += `<li><button type="button" onclick="enviarSri(${row.id})" class="btn btn-success">Enviar SRI</button></li>`;
                    }

                    estado += `<ul class="dropdown-menu cot" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 69%;">`;
                    if (row.estado == 1) {
                        estado += '<li><a href="javascript:anular(\'' + row.id + '\');" class="btn btn-danger" style="">&nbsp;&nbsp;Ventas Anular</a> </li>';
                    } else if (row.estado == 0) {
                        estado += `<li><a class="btn btn-danger" disabled style="">&nbsp;&nbsp;Anular</a> </li>`;
                    }
                    estado += `<li><a href="../ventas_editar?id=${row.id}" class="btn btn-warning" style="">Ventas Vizualizar</a> </li>`;
                    //estado=2;
                }
                estado += '</ul>';
                estado += '</div>';
                return estado;
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                var electronica = '<img src="../img/puntoplomo.png" style="width:15px;" title="Sin Autorizar" />';
                let arr = row.nro_comprobante !== null ? row.nro_comprobante?.split('-') : [];
                if (arr.includes("100")) {
                    if (row.doc_electronico == 5) {
                        electronica = '<img src="../img/puntoverde.png" style="width:20px;" title="Autorizado" />';
                    }

                    return '<a href="javascript:;">' + electronica + '</a>';
                } else {

                    if (row.estado_electronica == 3) {
                        electronica = '<img src="../img/puntoverde.png" style="width:20px;" title="Autorizado" />';
                    }

                    return '<a href="javascript:;">' + electronica + '</a>';


                    return '<a href="javascript:;">' + electronica + '</a>';
                }


            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + row.nro_comprobante + '</a>';
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + row.tipo + '</a>';
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + row.fecha + '</a>';
            }
        }, {
            render: function (data, type, row) {
                return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + row.nombre_cliente + '</a>';
            }
        }, {
            render: function (data, type, row) {
                var paciente = row.nombres_paciente !== null ? row.nombres_paciente : '';
                return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + paciente + '</a>';
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                var seguro = row.seguro !== null ? row.seguro : '';
                return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + seguro + '</a>';
            }
        }, {
            render: function (data, type, row) {
                var procedimientos = row.procedimientos !== null ? row.procedimientos : '';
                return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + procedimientos + '</a>';
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                var fecha_procedimiento = row.fecha_procedimiento !== null ? row.fecha_procedimiento : '';
                return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + fecha_procedimiento + '</a>';
            }
        }, {
            render: function (data, type, row) {
                return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + row.nombre1 + ' ' + row.apellido1 + '</a>';
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                if (row.estado == 0)
                    return '<a href="../ventas/editarCabeceraFactura?id=' + row.id + '">' + row.nombremod1 + ' ' + row.apellidomod1 + '</a>';
                else
                    return '';
            }
        },]
    });
});
const enviarSri = id => {
    console.log(id)
    $.ajax({
        url: `{{ url('contable/ventas/reproceso/venta/${id}') }}`,
        type: 'get',
        data: {
            'id': id
        },
        datatype: 'json',
        success: function (data) {
            console.log(data)
        },
        error: function (data) { }
    })
}
function limpiarModal() {
    $('.modal-content').html('');
}