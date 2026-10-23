@extends('nuevo_modulo_kardex.base')
@section('action-content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<style>
    .td_der {
        text-align: right;
    }

    .center {
        text-align: center;
    }

    #tabla_index_first a {
        background-color: #337AB7;
    }

    #tabla_index_previous a {
        background-color: #337AB7;
    }

    #tabla_index_next a {
        background-color: #337AB7;
    }

    #tabla_index_last a {
        background-color: #337AB7;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: none;
        border: none;
        color: black;
    }

    .pagination>li>a {
        background: #84B3DB;
    }

    .pagination>li>a:hover {
        background-color: #337AB7;
    }

    /*below block of css for change style when active*/

    .dataTables_wrapper .dataTables_paginate .paginate_button:active {
        background: none;
        color: black;
    }
</style>
<!-- Main content -->
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{ trans('winsumos_reportes.insumos') }}</a></li>
            <li class="breadcrumb-item"><a href="../">{{ trans('winsumos_reportes.kardex') }}</a></li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-body dobra">
            <div class="form-group col-md-12" id="table">
                <div class="content" id="contenedor">
                    <div class="row">
                        <h2>Kardex General</h2>
                    </div>
                    <div id="tbl_kardex" class="dataTables_wrapper form-inline dt-bootstrap t9">
                        <div class="row">
                            <div class="table table-responsive">
                                <table id="tabla_index" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="tbl_kardex_info">
                                    <thead>
                                        <tr class="well-dark">
                                            <th width="33.33%">Codigo</th>
                                            <th width="33.33%">Producto</th>
                                            <th width="33.33%">Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($query as $value)
                                        <tr onclick="abrirModal('{{ $value->codigo }}','{{$value->producto}}')" style="cursor:pointer">
                                            <td>
                                                {{ $value->codigo }}
                                            </td>
                                            <td>
                                                {{ $value->producto }}
                                            </td>
                                            <td style="text-align: right">
                                                {{ $value->saldo }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
{{-- Modal --}}
<div class="modal fade" id="myModal" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="width: 96vw;margin-left:20px;margin-right:20px;">
        <div class="modal-content">
            <div class="modal-header" style="text-align: center">
                <h5 class="modal-title">Detalle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <input type="hidden" id="nombreFile" />
                <div class="row" style="text-align: left;margin-left:15px;">
                    <button type="button" class="btn btn-danger" onclick="exportTableToExcel('ordering')">
                        Exportar
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="ordering" class="table-bordered table-hover table-striped">
                        <thead>
                            <tr class="well-dark">
                                <th colspan="14">
                                </th>
                                <th colspan="3" class="center">
                                    ENTRADAS
                                </th>
                                <th colspan="3" class="center">
                                    SALIDAS
                                </th>
                                <th colspan="3" class="center">
                                    SALDO
                                </th>
                            </tr>
                            <tr class="well-dark">
                                <th>Bodega</th>
                                <th>Codigo</th>
                                <th>Descripcion</th>
                                <th>Documento</th>
                                <th>Fecha</th>
                                <th>Fecha Vence</th>
                                <th>ID asiento cabecera</th>
                                <th>Iva</th>
                                <th>Lote</th>
                                <th>Nombre</th>
                                <th>Pedido</th>
                                <th>Producto</th>
                                <th>Serie</th>
                                <th>Subtotal</th>
                                <th>
                                    CANTIDAD
                                </th>
                                <th>
                                    PRECIO UNITARIO
                                </th>
                                <th>
                                    PRECIO TOTAL
                                </th>
                                <th>
                                    CANTIDAD
                                </th>
                                <th>
                                    PRECIO UNITARIO
                                </th>
                                <th>
                                    PRECIO TOTAL
                                </th>
                                <th>
                                    CANTIDAD
                                </th>
                                <th>
                                    COSTO PROMEDIO
                                </th>
                                <th>
                                    PRECIO TOTAL
                                </th>
                            </tr>
                        </thead>
                        <tbody id="insertar_informacion">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    function exportTableToExcel(tableID, filename = '') {
        filename = $('#nombreFile').val();
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
        // Specify file name
        filename = filename ? filename + '.xls' : 'excel_data.xls';
        // Create download link element
        downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        if (navigator.msSaveOrOpenBlob) {
            var blob = new Blob(['ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            // Create a link to the file
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            // Setting the file name
            downloadLink.download = filename;
            //triggering the function
            downloadLink.click();
        }

    }
    $(document).ready(function() {
        $("#ordering").DataTable({
            ordering: false,
            info: false,
            autoWidth: false,
            lengthChange: false,
            search: false,
            searching: false
        });
        $('#tabla_index').DataTable({
            pagingType: 'full_numbers',
            'lengthChange': false,
            'ordering': false,
            'info': false,
            'autoWidth': true,
            'pageLength': 500,
            responsive: 'true',
            dom: 'Bfrtilp',
            buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                    titleAttr: "{{ trans('winsumos_reportes.exportar_excel') }}",
                    className: 'btn btn-primary'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
                    titleAttr: "{{ trans('winsumos_reportes.exportar_pdf') }}",
                    className: 'btn btn-success'
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> ',
                    titleAttr: "{{ trans('winsumos_reportes.imprimir') }}",
                    className: 'btn btn-danger'
                },
            ]
        });
    });

    function reloadtable() {
        $('#tbl_kardex').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': false,
            'info': false,
            'autoWidth': false,
            "scrollY": 300,
            "scrollX": true,
            'scrollCollapse': true,
            responsive: 'true',
            dom: 'Bfrtilp',
            buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                    titleAttr: "{{ trans('winsumos_reportes.exportar_excel') }}",
                    className: 'btn btn-success'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
                    titleAttr: "{{ trans('winsumos_reportes.exportar_pdf') }}",
                    className: 'btn btn-danger'
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> ',
                    titleAttr: "{{ trans('winsumos_reportes.imprimir') }}",
                    className: 'btn btn-info'
                },
            ]
        });
    }

    function abrirModal(codigo, producto) {
        $.ajax({
            type: 'get',
            url: "{{ route('detalle_kardex.detalle') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                "codigo": codigo
            },
            success: function(data) {
                if (data.status) {
                    let array = data.query;
                    let dato = 0;
                    $("#insertar_informacion").empty();
                    $('#nombreFile').val(codigo + ' | ' + producto);
                    array.forEach((element, index) => {
                        let valor_anterior = 0;
                        let precio_total_egreso = 0;
                        let precio_unitario_egreso = 0;
                        let cantidad_egreso = 0;
                        let precio_total_ingreso = 0;
                        let precio_unitario_ingreso = 0;
                        let cantidad_ingreso = 0;
                        let precio_unitario = 0;
                        let saldo_final = 0;
                        let documento = ''


                        if (element.tipo == 'E') {
                            precio_total_egreso = element.cantidad;
                            precio_unitario_egreso = element.valor_unitario;
                            cantidad_egreso = precio_total_ingreso * precio_unitario_ingreso;
                            dato -= precio_total_egreso;
                            valor_anterior = Number(element.valor_unitario);
                        }
                        if (element.tipo == 'I') {
                            precio_total_ingreso = element.cantidad;
                            precio_unitario_ingreso = element.valor_unitario;
                            cantidad_ingreso = precio_total_ingreso * precio_unitario_ingreso;
                            //saldo 
                            dato += precio_total_ingreso;
                            if (array[index - 1] !== undefined) {
                                if (array[index - 1].tipo != 'I') {
                                    for (let i = index - 1; i >= 0; i--) {
                                        if (array[i].tipo == "I") {
                                            valor_anterior = (Number(array[i].valor_unitario) +
                                                Number(
                                                    element.valor_unitario)) / 2;
                                            break;
                                        }
                                    }
                                } else {
                                    valor_anterior = (Number(array[index - 1].valor_unitario) +
                                        Number(
                                            element.valor_unitario)) / 2;
                                }
                            } else {
                                valor_anterior = Number(element.valor_unitario);
                            }
                            saldo_final = valor_anterior * dato;
                        }

                        if (element.id_documento_bodega == 7) {
                            documento = `<a href="{{url('/')}}/contable/planilla/costo/detalle/pdf/${element.id_hc_procedimiento}/${element.id_hc_procedimiento}" target="_blank" > ${element.descripcion} </a>`
                        }else if (element.id_documento_bodega == 4) {
                            documento = `<a href="{{url('/')}}/transito/index/pdf/${element.id_cab_mov}" target="_blank"> ${element.descripcion} </a>`
                            
                        }else if (element.id_documento_bodega == 16 || element.id_documento_bodega == 17 || element.id_documento_bodega == 25 || element.id_documento_bodega == 55) {
                            documento = `<a href="{{url('/')}}/insumos/egreso/ingreso/pdf/${element.id_cab_mov}" target="_blank"> ${element.descripcion} </a>`
                        }else{
                            documento  =  `${element.descripcion} `
                        }
                        

                        $("#insertar_informacion").append(`
                            <tr>
                                <td> ${element.bodega} </td>
                                <td> ${element.codigo} </td>
                                <td> ${documento}</td>
                                <td> ${element.documento}   </td>
                                <td> ${element.fecha} </td>
                                <td> ${element.fecha_vence} </td>
                                <td> 
                                    <a href="{{url('/')}}/contable/contabilidad/libro/edit/${element.id_asiento_cabecera != null ?  element.id_asiento_cabecera : ""}" target="_blank">${element.id_asiento_cabecera != null ?  element.id_asiento_cabecera : ""}</a> 
                                </td>
                                <td> ${element.iva} </td>
                                <td> ${element.lote} </td>
                                <td> ${element.nombre} </td>
                                <td> ${element.pedido} </td>
                                <td> ${element.producto} </td>
                                <td> ${element.serie} </td>
                                <td> ${element.subtotal} </td>
                                <td> ${precio_total_ingreso} </td>
                                <td> ${precio_unitario_ingreso} </td>
                                <td> ${cantidad_ingreso.toFixed(4)} </td>
                                <td> ${precio_total_egreso} </td>
                                <td> ${precio_unitario_egreso}</td>
                                <td> ${cantidad_egreso.toFixed(4)} </td>
                                <td> ${dato} </td>
                                <td> ${valor_anterior.toFixed(4)} </td>
                                <td> ${saldo_final.toFixed(4)} </td>
                            </tr>
                        `);
                    });
                    $('#myModal').modal();
                } else {
                    alert("Error de carga");
                }
            },
            error: function(data) {
                Swal.close();
                console.log(data);
            }
        })
    }
</script>
@endsection