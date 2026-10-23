@extends('insumos.ingreso.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<style type="text/css">
    .ui-corner-all {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget {
        font-family: Verdana, Arial, sans-serif;
        font-size: 15px;
    }

    .ui-menu {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }

    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-menu .ui-menu-item {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .ui-menu .ui-menu-item a {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }

    .ui-menu .ui-menu-item a:hover {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }

    .ui-widget-content a {
        color: #222222;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-4">
                    <h3 class="box-title">{{trans('winsumos.visualizar_pedido')}}</h3>
                </div>
                <div class="col-md-8" style="text-align: right;">
                    <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-arrow-left">{{trans('winsumos.regresar')}}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- /.box-header -->
        <div class="box-body">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <!-- Fecha -->
                        <div class="form-group col-md-6">
                            <label for="fecha" class="col-md-4 control-label">{{trans('winsumos.fecha_pedido')}}</label>
                            <div class="col-md-8">
                                <span>{{$pedido->fecha}}</span>
                            </div>
                        </div>
                        <!-- Numero de Pedido -->
                        <div class="form-group cl_pedido col-md-6 {{ $errors->has('pedido') ? ' has-error' : '' }}">
                            <label for="pedido" class="col-md-4 control-label">{{trans('winsumos.numero_pedido')}}</label>
                            <div class="col-md-8">
                                <span>{{$pedido->pedido}}</span>
                            </div>
                            <span class="help-block">
                                <strong id="str_pedido"></strong>
                            </span>
                        </div>

                        <div class="form-group cl_pedido col-md-6 {{ $errors->has('num_factura') ? ' has-error' : '' }}">
                            <label for="num_factura" class="col-md-4 control-label">{{trans('winsumos.numero_factura')}}</label>
                            <div class="col-md-8">
                                <span>{{$pedido->factura}}</span>
                            </div>
                        </div>

                        <!-- Vencimiento -->
                        <div class="form-group col-md-6{{ $errors->has('vencimiento') ? ' has-error' : '' }}">
                            <label for="vencimiento" class="col-md-4 control-label">{{trans('winsumos.fecha_vencimiento')}}</label>
                            <div class="col-md-8">
                                <span>{{$pedido->vencimiento}}</span>
                            </div>
                        </div>

                        <!-- Proveedor -->
                        <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                            <label for="id_proveedor" class="col-md-4 control-label">{{trans('winsumos.proveedores')}}</label>
                            <div class="col-md-8">
                                <span>@if(isset($pedido->proveedor)) {{$pedido->proveedor->nombrecomercial}} @endif</span>
                            </div>
                        </div>
                        <!-- MULTIPLE EMPRESA-->
                        <div class="form-group col-md-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                            <label for="id_empresa" class="col-md-4 control-label">{{trans('winsumos.bodega_recibe')}}</label>
                            <div class="col-md-8">
                                <span> @if (isset($pedido->bodega)){{$pedido->bodega->nombre}} - @if(isset($pedido->bodega->empresa)) {{$pedido->bodega->empresa->nombrecomercial}} @endif @endif</span>
                            </div>
                        </div>
                        <!-- MULTIPLE EMPRESA (Nombre de empresa que recibe)-->
                        <div class="form-group col-md-6{{ $errors->has('bodega_recibe') ? ' has-error' : '' }}">
                            <label for="bodega_recibe" class="col-md-4 control-label">{{trans('winsumos.documento_de_bodega')}}</label>
                            <div class="col-md-8">
                                <span>@if (isset($pedido->movimiento_inv)) @if(isset($pedido->movimiento_inv->documento_bodega)) {{$pedido->movimiento_inv->documento_bodega->documento}} @endif @endif</span>
                            </div>
                        </div>


                        <!-- MULTIPLE EMPRESA-->
                        <!--div class="form-group col-md-6{{ $errors->has('consecion') ? ' has-error' : '' }}">
                    <label for="consecion" class="col-md-4 control-label">Posee Consecion</label>
                    <div class="col-md-8">
                      <span>@if(($pedido->consecion == 0) || (is_null($pedido->consecion))) No @else Si @endif</span>
                    </div>
                </div-->

                        <!-- Observaciones -->
                        <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                            <label for="observaciones" class="col-md-2 control-label">{{trans('winsumos.observacion')}}</label>
                            <div class="col-md-10">
                                <span>{{$pedido->observaciones}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="general form-group ">
                    <div class="col-md-12">
                        <table id="tbl_detalles" class="display compact responsive" role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">

                            <thead>
                                <tr role="row" style="text-align: center;">
                                    <th>{{trans('winsumos.codigo')}}</th>
                                    <th>{{trans('winsumos.nombre')}}</th>
                                    <th>{{trans('winsumos.cantidad')}}</th>
                                    <th>{{trans('winsumos.usos')}}</th>
                                    <th>{{trans('winsumos.serie')}}</th>
                                    <!--th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th-->
                                    <th>{{trans('winsumos.bodegas')}}</th>
                                    <th>{{trans('winsumos.lote')}}</th>
                                    <th>{{trans('winsumos.registro_sanitario')}}</th>
                                    <th>{{trans('winsumos.fecha_vencimiento')}}</th>
                                    <th>{{trans('winsumos.precio_unitario')}}</th>
                                    <th>{{trans('winsumos.traslado')}}</th>
                                    <th>{{trans('winsumos.existencia')}}</th>
                                    <th>{{trans('winsumos.factura')}}</th>
                                    <th>{{trans('winsumos.precio_final')}}</th>
                                    {{-- <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Consecion: activate to sort column ascending">Consecion</th> --}}
                                </tr>
                            </thead>



                            <tbody>
                                @foreach ($detalles as $value)
                                <tr style="text-align: center;">
                                    <td>{{$value['codigo']}}</td>
                                    <td>{{$value['nombre']}}</td>
                                    <td>{{$value['cantidad']}}</td>
                                    <td>{{$value['cant_uso']}}</td>
                                    <td>{{$value['serie']}}-</td>
                                    <td>{{$value['bodega']}}</td>
                                    <td> <input type="text" id="lote_{{$value['id']}}" value="{{$value['lote']}}" onchange="updateData('lote', {{$value['id']}})"> </td>
                                    <td>{{$value['registro_sanitario']}}</td>
                                    <td> <input type="date" id="fecha_exp_{{$value['id']}}" value="{{$value['vencimiento']}}" onchange="updateData('fecha_exp', {{$value['id']}})"></td>
                                    <td>{{$value['valor_unitario']}}</td>
                                    <td>{{$value['traslado']}}</td>
                                    <td>{{$value['existencia']}}</td>
                                    <td>{{$value['facturado']}}</td>
                                    <td>{{round(($value['valor_unitario'] * $value['cantidad']),2)}}</td>

                                </tr>

                                @endforeach
                                <tr>
                                    <th colspan="12"></th>
                                    <th>{{trans('winsumos.subtotal')}} 12%</th>
                                    <td>{{number_format($pedido->subtotal_12, 2, ".", "")}}</td>
                                </tr>
                                <tr>
                                    <th colspan="12"></th>
                                    <th>{{trans('winsumos.subtotal')}} 0%</th>
                                    <td>{{number_format($pedido->subtotal_0,2, ".", "")}}</td>
                                </tr>
                                <tr>
                                    <th colspan="12"></th>
                                    <th>{{trans('winsumos.descuento')}}</th>
                                    <td>{{number_format($pedido->descuento,2, ".", "")}}</td>
                                </tr>
                                <tr>
                                    <th colspan="12"></th>
                                    <th>{{trans('winsumos.iva')}}</th>
                                    <td>{{number_format($pedido->iva,2, ".", "")}}</td>
                                </tr>
                                <tr>
                                    <th colspan="12"></th>
                                    <th>{{trans('winsumos.total')}}</th>
                                    <td>{{number_format($pedido->total,2, ".", "")}}</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>
                    <span class="help-block">
                        <strong id="lote_errores"></strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const updateData = (key, value) => {
        console.log(key, value);
        let valor = document.getElementById(`${key}_${value}`).value;
        console.log(valor)

        $.ajax({
            type: 'post',
            url: "{{route('producto.editar_pedido_new_update')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'type': key,
                'id': value,
                'value': valor
            },
            success: function(data) {
                //console.log(data)
                alertas(data.status, (data.status).toUpperCase(), data.msj)
            },
            error: function(data) {
                alertas(data.status, (data.status).toUpperCase(), data.msj)
            }
        });
    }

    const alertas = (icon, title, msj) => {
        Swal.fire({
            position: 'top-end',
            icon: `${icon}`,
            title: `${msj}`,
            showConfirmButton: false,
            timer: 1200
        })
    }


    $(document).ready(function() {
        $('#consecion').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            increaseArea: '20%' // optional
        });


    });
</script>
<script type="text/javascript">
    $('#tbl_detalles').DataTable({
        'paging': false,
        dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': false,
        'responsive': true,
        'info': false,
        'autoWidth': true,
        'columnDefs': [{
                "width": "5%",
                "targets": 0
            },
            {
                "width": "5%",
                "targets": 2
            },
            {
                "width": "10%",
                "targets": 6
            },
            {
                "width": "5%",
                "targets": 8
            }
        ],
        language: {
            zeroRecords: " "
        },
        buttons: [{
                extend: 'copyHtml5',
                footer: true
            },

            {
                extend: 'excelHtml5',
                footer: true,
                title: "{{trans('winsumos.pedidos')}}"
            },
            {
                extend: 'csvHtml5',
                footer: true
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                footer: true,
                title: "{{trans('winsumos.pedidos')}}",
                customize: function(doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '17',
                        alignment: 'center'
                    }
                }
            }
        ],
    });
</script>
@endsection