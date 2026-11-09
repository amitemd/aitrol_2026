function PadLeft(value, length) {
    try {
        //  $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-white btn-sm';
        return (value.toString().length < length) ? PadLeft("0" + value, length) : value;
    } catch (error) {
        console.log(error);
    }
}
var tabla = $('#tableGuias').DataTable();
$(document).ready(function () {
    $('#tableGuias').dataTable().fnClearTable();
    $('#tableGuias').dataTable().fnDestroy();
    $('#tableGuias').DataTable({
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
        responsive: true,
        sAjaxSource: "../../guiaremision/getguiasjs",
        displayLengtn: 25,
        bPaginate: true,
        aoColumns: [{
            data: 'id'
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                var estado = '';
                if (row.estado == -1) {
                    estado = '<div class="dropdown" id="btnEnviar' + row.id + '"><button type="button" class="btn btn-primary  btn-xs" onclick="confirmar(\'' + row.id + '\')" title="Enviar"><i id="changeIcon" class="fa fa-send" aria-hidden="true"></i> <span id="changeText"></span></button></div>';
                } else if (row.estado == 0) {
                    estado = '<button type="button" class="btn btn-primary btn-xs text-center"><i class="fa fa-spinner" aria-hidden="true" title="En proceso"></i></button>';
                } else if (row.estado == 5) {
                    estado = '<div class="dropdown"><button class="btn btn-info btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Autorizado"><i class="fa fa-thumbs-o-up"></i> <i class="fa fa-caret-down"></i></button><div class="dropdown-menu" aria-labelledby="dropdownMenu2"><a href="../../api_doc_electronico?opcion=descargarXML&clave=' + row.clave_acceso + '" class="dropdown-item btn btn-success" type="button"><i class="fa fa-file-code-o"></i> XML</a>&nbsp;&nbsp;<a href="../../api_doc_electronico?opcion=generarPdf&clave=' + row.clave_acceso + '" class="dropdown-item btn btn-danger" type="button"><i class="fa fa-file-pdf-o"></i> PDF</a></div></div>';
                } else if (row.estado == 7) {
                    estado = '<button type="button" class="btn btn-danger btn-xs text-center" onclick="verErrorXml(\'' + row.id + '\')"><i class="fa fa-close" aria-hidden="true" title="Error"></i> </button>';
                } else if (row.estado == 9) {
                    estado = '<button type="button" class="btn btn-danger btn-xs text-center" onclick="verErrorXml(\'' + row.id + '\')" title="Error"><i class="fa fa-close" aria-hidden="true"></i> </buttor>';
                } else {

                }
                return estado;
            }
        }, {
            render: function (data, type, row) {
                var numGuia = '';
                if (row.clave_acceso !== null && row.estado == 5)
                    numGuia = '<a href="../../guia/remision/update?id=' + row.id + '">' + row.establecimiento + ' - ' + row.punto_emision + ' - ' + PadLeft(row.num_secuencial, 9) + '</a>';
                else
                    numGuia = '&nbsp;';
                return numGuia;
            }
        }, {
            render: function (data, type, row) {
                return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.razon_social_destinatario + '</a>';
            }
        }, {
            render: function (data, type, row) {
                return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.datos_adicionales + '</a>';
            }
        }, {
            render: function (data, type, row) {
                return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.created_at + '</a>';
            }
        }, {
            render: function (data, type, row) {
                return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.direccion_destinatario + '</a>';
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.codigo_est_destino + '</a>';
            }
        }, {
            render: function (data, type, row) {
                var numDocDestino = row.num_doc_destino !== null ? row.num_doc_destino : '';
                return '<a href="../../guia/remision/update?id=' + row.id + '">' + numDocDestino + '</a>';
            }
        }, {
            render: function (data, type, row) {
                return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.razon_social + '</a>';
            }
        }, {
            render: function (data, type, row) {
                return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.direccion_partida + '</a>';
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.placa + '</a>';
            }
        }]
    });
    $('#buscarEmpleado').click(function () {
        if ($('#desde').val() != '') {
            if ($('#hasta').val() == '') {
                Swal.fire({
                    title: 'Debe ingresar la fecha de inicio y final',
                    confirmButtonText: 'Ok',
                });
                return false;
            }
        }
        $.ajax({
            url: "../../guia/remision/buscador",
            type: 'post',
            data: $('#form_busqueda').serialize(),
            dataType: 'json',
            success: function (json) {
                $('#tableGuias').dataTable().fnClearTable();
                $('#tableGuias').dataTable().fnDestroy();
                $('#tableGuias').DataTable({
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
                    bPaginate: true,
                    aoColumns: [{
                        data: 'id'
                    }, {
                        className: 'text-center',
                        render: function (data, type, row) {
                            var estado = '';
                            if (row.estado == -1) {
                                estado = '<div class="dropdown" id="btnEnviar' + row.id + '"><button type="button" class="btn btn-primary  btn-xs" onclick="confirmar(\'' + row.id + '\')" title="Enviar"><i id="changeIcon" class="fa fa-send" aria-hidden="true"></i> <span id="changeText"></span></button></div>';
                            } else if (row.estado == 0) {
                                estado = '<button type="button" class="btn btn-primary btn-xs text-center"><i class="fa fa-spinner" aria-hidden="true" title="En proceso"></i></button>';
                            } else if (row.estado == 5) {
                                estado = '<div class="dropdown"><button class="btn btn-info btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Autorizado"><i class="fa fa-thumbs-o-up"></i> <i class="fa fa-caret-down"></i></button><div class="dropdown-menu" aria-labelledby="dropdownMenu2"><a href="../../api_doc_electronico?opcion=descargarXML&clave=' + row.clave_acceso + '" class="dropdown-item btn btn-success" type="button"><i class="fa fa-file-code-o"></i> XML</a>&nbsp;&nbsp;<a href="../../api_doc_electronico?opcion=generarPdf&clave=' + row.clave_acceso + '" class="dropdown-item btn btn-danger" type="button"><i class="fa fa-file-pdf-o"></i> PDF</a></div></div>';
                            } else if (row.estado == 7) {
                                estado = '<button type="button" class="btn btn-danger btn-xs text-center" onclick="verErrorXml(\'' + row.id + '\')"><i class="fa fa-close" aria-hidden="true" title="Error"></i> </button>';
                            } else if (row.estado == 9) {
                                estado = '<button type="button" class="btn btn-danger btn-xs text-center" onclick="verIErrorTributaria(\'' + row.clave_acceso + '\')" title="Error"><i class="fa fa-close" aria-hidden="true"></i> </buttor>';
                            } else {
            
                            }
                            return estado;
                        }
                    }, {
                        render: function (data, type, row) {
                            var numGuia = '';
                            if (row.clave_acceso !== null && row.estado == 5)
                                numGuia = '<a href="../../guia/remision/update?id=' + row.id + '">' + row.establecimiento + ' - ' + row.punto_emision + ' - ' + PadLeft(row.num_secuencial, 9) + '</a>';
                            else
                                numGuia = '&nbsp;';
                            return numGuia;
                        }
                    }, {
                        render: function (data, type, row) {
                            return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.razon_social_destinatario + '</a>';
                        }
                    }, {
                        render: function (data, type, row) {
                            return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.datos_adicionales + '</a>';
                        }
                    }, {
                        render: function (data, type, row) {
                            return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.created_at + '</a>';
                        }
                    }, {
                        render: function (data, type, row) {
                            return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.direccion_destinatario + '</a>';
                        }
                    }, {
                        className: 'text-center',
                        render: function (data, type, row) {
                            return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.codigo_est_destino + '</a>';
                        }
                    }, {
                        render: function (data, type, row) {
                            var numDocDestino = row.num_doc_destino !== null ? row.num_doc_destino : '';
                            return '<a href="../../guia/remision/update?id=' + row.id + '">' + numDocDestino + '</a>';
                        }
                    }, {
                        render: function (data, type, row) {
                            return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.razon_social + '</a>';
                        }
                    }, {
                        render: function (data, type, row) {
                            return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.direccion_partida + '</a>';
                        }
                    }, {
                        className: 'text-center',
                        render: function (data, type, row) {
                            return '<a href="../../guia/remision/update?id=' + row.id + '">' + row.placa + '</a>';
                        }
                    }]
                });
            }
        });
    });
});