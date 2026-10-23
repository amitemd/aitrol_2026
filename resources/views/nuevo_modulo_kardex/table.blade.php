<style type="text/css">
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

    #insertar_informacion {
        height: 10em;
        overflow-y: scroll;
        display: block
    }
</style>
<div class="content" id="contenedor">
    <div class="row">
        <h2>Karden General</h2>
    </div>
    <div id="tbl_kardex_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
            <div class="table table-responsive">
                <table id="tabla_index" class="table-bordered table-hover dataTable table-striped" role="grid"
                    aria-describedby="tbl_kardex_info">
                    <thead>

                        <tr class="well-dark">
                            <th width="33.33%">Codigo</th>
                            <th width="33.33%">Producto</th>
                            <th width="33.33%">Saldo</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($query as $value)
                            <tr onclick="abrirModal('{{ $value->codigo }}')" style="cursor:pointer">
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





<div class="modal fade" id="myModal" data-keyboard="false" tabindex=null role="dialog"
    aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="width: 96vw;margin-left:20px;margin-right:20px;">
        <div class="modal-content">
            <div class="modal-header" style="text-align: center">
                <h5 class="modal-title">Detalle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="ordering" class="table-bordered table-hover table-striped">
                        <thead style="display:block">

                            <tr class="well-dark">
                                <th colspan="15">
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
                                    PRECIO UNITARIO
                                </th>
                                <th>
                                    PRECIO TOTAL
                                </th>
                            </tr>
                        </thead>
                        <tbody id="insertar_informacion" syu>
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


<link rel="stylesheet" type="text/css" src="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

<script type="text/javascript">
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
            'autoWidth': false,
        });
    });


    function abrirModal(codigo) {

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


                        if (element.tipo == 'E') {


                            precio_total_egreso = element.cantidad;
                            precio_unitario_egreso = element.valor_unitario;
                            cantidad_egreso = precio_total_ingreso * precio_unitario_ingreso;

                            //saldo 

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
                        $("#insertar_informacion").append(`
                      <tr>
                        
                        <td>
                          ${element.bodega}
                        </td>
                        <td>
                          ${element.codigo}
                        </td>


                        <td>
                          ${element.descripcion}
                        </td>

                        <td>
                          ${element.documento}
                        </td>


                        <td>
                          ${element.fecha}
                        </td>

                        <td>
                          ${element.fecha_vence}
                        </td>

                        <td>
                          ${element.id_asiento_cabecera != null ?  element.id_asiento_cabecera : ""}
                        </td>

                        <td>
                          ${element.iva}
                        </td>

                        <td>
                          ${element.lote}
                        </td>

                        <td>
                          ${element.nombre}
                        </td>

                        <td>
                          ${element.pedido}
                        </td>

                        <td>
                          ${element.producto}
                        </td>

                        <td>
                          ${element.serie}
                        </td>

                        <td>
                          ${element.subtotal}
                        </td>
                      
                        <td>
                          ${precio_total_ingreso}
                        </td>
                          
                        <td>
                          ${precio_unitario_ingreso}
                        </td>
                        
                        <td>
                          ${cantidad_ingreso.toFixed(4)}
                        </td>

                        <td>
                          ${precio_total_egreso}
                        </td>

                        <td>
                          ${precio_unitario_egreso}
                        </td>

                        <td>
                          ${cantidad_egreso.toFixed(4)}
                        </td>


                        <td>
                          ${dato}
                        </td>

                        <td>
                          ${valor_anterior.toFixed(4)}
                        </td>

                        <td>
                          ${saldo_final.toFixed(4)}
                        </td>
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
