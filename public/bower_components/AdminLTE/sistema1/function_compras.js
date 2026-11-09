$(document).ready(function () {
    $('#buscarCompra').click(function () {
        $.ajax({
            url: $('#asset_').val() + "contable/compras/buscar",
            type: 'post',
            data: $('#reporte_master').serialize(),
            dataType: 'json',
            success: function (json) {
                console.log(json)
                if (json.respuesta == 'no')
                    window.open('compras?page=1', '_self');
                $('#tableCompras').dataTable().fnClearTable();
                $('#tableCompras').dataTable().fnDestroy();
                $('#tableCompras').DataTable({
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
                    aoColumns: [
                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="' + $('#asset_').val() + 'contable/compras/editarCabeceraCompras/' + row.id + '">' + row.id + '</a>';
                            }
                        },
                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                var estado = '';
                                if (row.doc_electronico == -1) {
                                    estado = '<div class="dropdown" id="btnEnviar' + row.id + '"><button type="button" class="btn btn-primary  btn-xs" onclick="confirmar(\'' + row.id + '\')" title="Enviar"><i id="changeIcon" class="fa fa-send" aria-hidden="true"></i> <span id="changeText"></span></button></div>';
                                } else if (row.doc_electronico == 0) {
                                    estado = '<button type="button" class="btn btn-primary btn-xs text-center"><i class="fa fa-spinner" aria-hidden="true" title="En proceso"></i></button>';
                                } else if (row.doc_electronico == 5) {
                                    estado = '<button  class="btn btn-info btn-xs dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Autorizado"><i class="fa fa-thumbs-o-up"></i> <i class="fa fa-caret-down"></i></button><div class="dropdown-menu" aria-labelledby="dropdownMenu2"><a href="../../api_doc_electronico?opcion=descargarXML&clave=' + row.clave_acceso + '" class="dropdown-item btn btn-success" type="button"><i class="fa fa-file-code-o"></i> XML</a>&nbsp;&nbsp;<a href="../../api_doc_electronico?opcion=generarPdf&clave=' + row.clave_acceso + '" class="dropdown-item btn btn-danger" type="button"><i class="fa fa-file-pdf-o"></i> PDF</a></div>';
                                    estado += '<a  class="btn btn-success" href="' + $('#asset_').val() + 'contable/compras/editar/' + row.id + '"><i  class="fa fa-eye" aria-hidden="true"></i></a>';
                                } else if (row.doc_electronico == 7) {
                                    estado = '<button type="button" class="btn btn-danger btn-xs text-center" onclick="verErrorXml(\'' + row.id + '\')"><i class="fa fa-close" aria-hidden="true" title="Error"></i> </button>';
                                } else if (row.doc_electronico == 9) {
                                    estado = '<button type="button" class="btn btn-danger btn-xs text-center" onclick="verIErrorTributaria(\'' + row.clave_acceso + '\')" title="Error"><i class="fa fa-close" aria-hidden="true"></i> </buttor>';
                                } else {
                                    estado = '<a href="' + $('#asset_').val() + 'contable/compras/editar/' + row.id + '">'+'No posee estado electronico'+'</a>';
                                }
                                return estado;
                            }
                        },
                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.id_asiento_cabecera + '</a>';
                            }
                        },
                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.serie + '</a>';
                            }
                        },
                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.fecha + '</a>';
                            }
                        },
                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.razonsocial + '</a>';
                            }
                        },
                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.autorizacion + '</a>';
                            }
                        },
                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.tipo_comprobante + '</a>';
                            }
                        },
                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.observacion + '</a>';
                            }
                        },

                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.nombre1 + ' ' + row.apellido1 + '</a>';
                            }
                        },

                        {
                            className: 'text-center',
                            render: function (data, type, row) {
                                let estado = '';
                                if (row.estado == 0) {

                                    estado = '<button class="btn btn-xs btn-danger">Anulada</button>'
                                } else if (row.estado == 1) {
                                    estado = '<button class="btn btn-xs btn-success">Anulada</button>'
                                } else {
                                    estado = '<button class="btn btn-xs btn-info">Con retención</button>'
                                }

                                return estado;
                            }
                        }
                    ]
                });
            }
        });
    })
    $('#tableCompras').DataTable({
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
        sAjaxSource: $('#asset_').val() + "compras/getcomprasjs?page=" + $('#page').val(),
        ordering: true,
        info: false,
        searching: false,
        paging: false,
        responsive: true,
        aoColumns: [
            {
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras?id=' + row.id + '">' + row.id + '</a>';
                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    let estado = '';
                    estado += '<div class="btn-group">';
                    estado += '<button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars" aria-hidden="true"></i></button>';
                    estado += '<div class="dropdown-menu">';
                    if (row.doc_electronico == 0) {
                        estado += '<button type="button" class="btn btn-primary btn-sm text-center"><i class="fa fa-spinner" aria-hidden="true" title="En proceso"></i></button>';
                    } else if (row.doc_electronico == 5) {
                        estado += '<a href="' + $('#asset_').val() + 'api_doc_electronico?opcion=generarPdf&clave=' + row.nro_autorizacion + '" class="dropdown-item btn btn-danger btn-sm" type="button"><i class="fa fa-file-pdf-o"></i> </a>&nbsp';
                        estado += '<a href="' + $('#asset_').val() + 'api_doc_electronico?opcion=descargarXML&clave=' + row.nro_autorizacion + '" class="dropdown-item btn btn-success btn-sm" type="button"><i class="fa fa-file-code-o"></i> </a>&nbsp';
                        estado += '<a href="' + $('#asset_').val() + 'contable/compras/editar/' + row.id + '" class="dropdown-item btn btn-info btn-sm" type="button"><i class="fa fa-eye"></i> </a>&nbsp';
                        estado += '<a href="javascript:;" onclick="anular(\''+row.id+'\')" class="dropdown-item btn btn-danger btn-sm btn-sm"><i class="fa fa-close"></i></a>';
                    } else if (row.doc_electronico == 7) {
                        estado += '<button type="button" class="btn btn-danger btn-sm text-center" onclick="verErrorXml(\'' + row.id + '\')"><i class="fa fa-close" aria-hidden="true" title="Error"></i> </button>';
                    } else if (row.doc_electronico == 9) {
                        estado += '<button type="button" class="btn btn-danger btn-sm text-center" onclick="verIErrorTributaria(\'' + row.nro_autorizacion + '\')" title="Error"><i class="fa fa-close" aria-hidden="true"></i> </buttor>';
                    } else {
                        estado += '<a href="' + $('#asset_').val() + 'contable/compras/editar/' + row.id + '" class="dropdown-item btn btn-info btn-sm"><i class="fa fa-eye"></i></a>&nbsp;';
                        estado += '<a href="javascript:;" onclick="anular(\''+row.id+'\')" class="dropdown-item btn btn-danger btn-sm"><i class="fa fa-close"></i></a>';
                    }
                    if (row.estado == 0) {
                        estado += '<li><a href="#" class="btn btn-success"><i class="fa fa-minus"></i> Anulado</a></li>';
                    }
                    estado += '</div>';
                    estado += '</div>';
                    return estado;
                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.id_asiento_cabecera + '</a>';
                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.serie + '</a>';
                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.fecha + '</a>';
                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.razonsocial + '</a>';
                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.autorizacion + '</a>';


                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.nombre + '</a>';
                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.observacion + '</a>';
                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="' + $('#asset_').val() + 'compras/editarCabeceraCompras/' + row.id + '">' + row.nombre1 + ' ' + row.apellido1 + '</a>';
                }
            },
            {
                className: 'text-center',
                render: function (data, type, row) {
                    let estado = '';
                    if (row.estado == 0) {
                        estado = '<button class="btn btn-xs btn-danger">Anulada</button>'
                    } else if (row.estado == 1) {
                        estado = '<button class="btn btn-xs btn-success">Activa</button>'
                    } else {
                        estado = '<button class="btn btn-xs btn-info">Con retención</button>'
                    }
                    return estado;
                }
            }
        ]
    });
});