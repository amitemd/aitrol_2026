@extends('insumos.producto.base')

@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-4">
                    <h3 class="box-title">Ingreso de Orden Conglomerada</h3>
                </div>
                <div class="col-md-8" style="text-align: right;">
                    <a type="button" href="{{route('codigo.barra')}}" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                    </a>
                    <button onclick="return window.location.href = window.location.href" type="button"
                        class="btn btn-primary  btn-gray">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="panel panel-default">
                <form method="POST" name="frm" id="frm">
                    <div class="panel-heading">

                        <div class="box-group" id="accordion">
                            <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                            <div class="panel box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                            aria-expanded="true" class="">
                                            Busqueda por Anexo de la Factura / Serie
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse in" aria-expanded="true" style="">
                                    <div class="box-body">
                                        <div class="form-group col-md-6">
                                            <label for="codigo" class="col-sm-2 control-label">Serie</label>

                                            <div class="col-sm-8">
                                                <input class="form-control" type="text" name="findserie"
                                                    id="findserie" placeholder="Ingrese la serie del item">
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="codigo" class="col-sm-2 control-label">Codigo</label>

                                            <div class="col-sm-8">
                                                <input class="form-control" type="text" name="findcodigo"
                                                    id="findcodigo" placeholder="Ingrese el código del item">
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="lote" class="col-sm-2 control-label">Lote</label>

                                            <div class="col-sm-8">
                                                <input class="form-control" type="text" name="findlote" id="findlote"
                                                    placeholder="Ingrese el lote del item">
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <button onclick="getLoader(this.value, 1)"
                                                class="btn btn-success btn-gray pull-right" type="button"> <i
                                                    class="fa fa-search"></i> </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel box box-danger">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"
                                            class="collapsed" aria-expanded="false">
                                            Busqueda por N&uacute;mero de Pedido
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse" aria-expanded="false"
                                    style="height: 0px;">
                                    <div class="box-body">
                                        <div class="form-group col-md-6">
                                            <label for="codigo" class="col-sm-2 control-label">Pedido</label>

                                            <div class="col-sm-8">
                                                <input class="form-control" type="text" name="findpedido"
                                                    id="findpedido" placeholder="Ingrese el n&uacute;mero del pedido">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                        </div>
                                        <div class="box-footer">
                                            <button onclick="getLoader(this.value, 2)"
                                                class="btn btn-success btn-gray pull-right" type="button"> <i
                                                    class="fa fa-search"></i> </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel box box-success">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"
                                            class="collapsed" aria-expanded="false">
                                            Busqueda Por Reporte de Usos
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseThree" class="panel-collapse collapse" aria-expanded="false"
                                    style="height: 0px;">
                                    <div class="box-body">
                                        <div class="form-group col-md-6">
                                            <label for="codigo" class="col-sm-2 control-label">Proveedor</label>

                                            <div class="col-sm-8">
                                                <select name="findidproveedor" style="width: 100%;"
                                                    class="form-control select2" id="findidproveedor">
                                                    <option value="">Seleccione..</option>
                                                    @foreach($proveedores as $value)
                                                    <option value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="findfechadesde" class="col-md-4 control-label">Fecha Desde</label>
                                            <div class="col-md-8">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" value="" name="findfechadesde" class="form-control"
                                                        id="findfechadesde" placeholder="AAAA/MM/DD">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6"> 
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="findfechahasta" class="col-md-4 control-label">Fecha Desde</label>
                                            <div class="col-md-8">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" value="" name="findfechahasta" class="form-control"
                                                        id="findfechahasta" placeholder="AAAA/MM/DD">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <button onclick="getLoader(this.value,3)"
                                                class="btn btn-success btn-gray pull-right" type="button"> <i
                                                    class="fa fa-search"></i> </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Fecha -->
                            <div class="form-group col-md-6">
                                <label for="fecha" class="col-md-4 control-label">Fecha Pedido</label>
                                <div class="col-md-8">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="" name="fecha" class="form-control" id="fecha"
                                            placeholder="AAAA/MM/DD">
                                    </div>
                                </div>
                            </div>
                            <!-- Numero de Pedido -->

                            <div
                                class="form-group cl_pedido col-md-6 {{ $errors->has('num_factura') ? ' has-error' : '' }}">
                                <label for="num_factura" class="col-md-4 control-label">Número de factura</label>
                                <div class="col-md-8">
                                    <input id="num_factura" type="text" class="form-control" name="num_factura"
                                        value="{{ old('num_factura') }}" required autofocus>
                                </div>
                            </div>
                            <!-- Vencimiento -->
                            <div class="form-group col-md-6{{ $errors->has('vencimiento') ? ' has-error' : '' }}">
                                <label for="vencimiento" class="col-md-4 control-label">Fecha de Vencimiento</label>
                                <div class="col-md-8">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="" name="vencimiento" class="form-control"
                                            id="vencimiento" placeholder="AAAA/MM/DD">
                                    </div>
                                </div>
                            </div>
                            <!-- Proveedor -->
                            <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                                <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>
                                <div class="col-md-8">
                                    <select name="id_proveedor" style="width: 100%;" class="form-control select2"
                                        required="" name="id_proveedor">
                                        <option value="">Seleccione..</option>
                                        @foreach($proveedores as $value)
                                        <option value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- MULTIPLE EMPRESA-->
                            <div class="form-group col-md-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                                <label for="id_empresa" class="col-md-4 control-label">Empresa</label>
                                <div class="col-md-8">
                                    <select id="id_empresa" class="form-control select2" style="width: 100%;"
                                        name="id_empresa">
                                        <option value="">Seleccione..</option>
                                        @foreach($empresa as $value)
                                        <option value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- MULTIPLE EMPRESA-->
                            <!--div class="form-group col-md-6{{ $errors->has('consecion') ? ' has-error' : '' }}">
                        <label for="consecion" class="col-md-4 control-label">Posee Consecion</label>
                        <div class="col-md-8">
                          <input id="consecion" name="consecion" type="checkbox" value="1" class="flat-blue"  style="position: absolute; opacity: 0;">
                        </div>
                        </div-->
                            <!-- Observaciones -->
                            <div class="form-group  col-md-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                                <label for="observaciones" class="col-md-4 control-label">Observaciones</label>
                                <div class="col-md-8">
                                    <input id="observaciones" type="text" class="form-control" name="observaciones"
                                        value="{{ old('observaciones') }}" autofocus>
                                </div>
                            </div>

                            {{-- <div class="form-group col-md-6">
                                <label class="col-md-4 control-label"> Buscador <br> # Pedido </label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" name="findpedido" id="findpedido" placeholder="Ingrese número de pedido">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <button onclick="getLoader(this.value)" class="btn btn-success btn-gray" type="button"> <i class="fa fa-search"></i> </button>
                            </div>  --}}



                        </div>
                    </div>

                    <div class="general form-group ">
                        <label class="col-md-4 control-label"></label>
                        <div class="col-md-8">
                        </div>
                        <span class="help-block">
                            <strong id="lote_errores"></strong>
                        </span>
                    </div>

                    <div class="box-body"> 
                            {{-- <input name='contador' type="hidden" value="0" id="contador"> --}}
                            <table id="tbl_detalles" name="tbl_detalles"  class="display compact responsive nowarp"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                                <thead>
                                    <tr>
                                        <th tabindex="0">Num.</th>
                                        <th tabindex="0">Proveedor.</th>
                                        <th tabindex="0">Codigo</th>
                                        <th tabindex="0">Prod.</th>
                                        <th tabindex="0">Cant.</th>
                                        <th tabindex="0">Bodega</th>
                                        <th tabindex="0">Serie</th>
                                        <th tabindex="0">Lote</th>
                                        <th tabindex="0">Fecha Vence</th>
                                        <th tabindex="0">Precio</th>
                                        {{-- <th tabindex="0">% Desc.</th> --}}
                                        <th tabindex="0">Desc.</th>
                                        <th tabindex="0">Total</th>
                                        <th tabindex="0">IVA</th>
                                        <th tabindex="0">Accion</th>
                                    </tr>
                                </thead>
                                <tbody id="tbldetalles">
                                </tbody>
                            </table> 
                    </div>
                    <table class="table table-bordered table-hover dataTable">
                        <tfoot class='well'>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal 12%</td>
                                <td id="subtotal_12" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal 0%</td>
                                <td id="subtotal_0" class="text-right px-1">0.00</td>
                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Descuento</td>
                                <td id="descuento" class="text-right px-1">0.00</td>
                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Subtotal sin Impuesto</td>
                                <td id="base" class="text-right px-1">0.00</td>
                                <input type="hidden" name="base1" id="base1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                <td id="total" class="text-right px-1">0.00</td>
                                <input type="hidden" name="total1" id="total1" class="hidden">
                            </tr>
                        </tfoot>
                    </table>
                    <div class="box-footer" style="text-align: center;">
                        <button type="button" class="btn btn-primary btn_add"
                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                            <i class="fa fa-save"></i> &nbsp;&nbsp;Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#fecha').datetimepicker({
                    useCurrent: false,
                    format: 'YYYY/MM/DD',
                    defaultDate: new Date()
                    //Important! See issue #1075

                });
                $('#findfechadesde').datetimepicker({
                    useCurrent: false,
                    format: 'YYYY/MM/DD',
                    defaultDate: new Date()
                    //Important! See issue #1075

                });
                $('#findfechahasta').datetimepicker({
                    useCurrent: false,
                    format: 'YYYY/MM/DD',
                    defaultDate: new Date()
                    //Important! See issue #1075

                });
                
                $('#vencimiento').datetimepicker({
                    useCurrent: false,
                    format: 'YYYY/MM/DD',
                    defaultDate: new Date()
                });
                
            });


            
        $('#tbl_detalles').DataTable({
            'paging': false,
            dom: 'lBrtip',
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'responsive': true,
            'info': false,
            'autoWidth': true,
            'columnDefs': [
                { "width": "5%", "targets": 0 },
                { "width": "5%", "targets": 2 },
                { "width": "10%", "targets": 6 },
                { "width": "5%", "targets": 8 }
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
            title: 'FACTURA CONGLOMERADA'
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
            title: 'FACTURA CONGLOMERADA',
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

            function getLoader(pedido,tipo) {
                if (tipo==1){
                    if($('#findpedido').val()==""&&$('#findcodigo').val()==""&&$('#findserie').val()=="") {
                        Swal.fire("Error: ", "No se ha ingresado ningun parametro para la consulta", "error");
                        return;
                    }
                } 
                if (tipo==2) {
                    if($('#findpedido').val()=="") {
                        Swal.fire("Error: ", "Ingrese el numero del pedido", "error");
                        return;
                    }
                } 
                if (tipo==3) {
                    if($('#findidproveedor').val()=="") {
                        Swal.fire("Error: ", "Ingrese el proveedor", "error");
                        return;
                    }
                    if($('#findfechadesde').val()==""&&$('#findfechahasta').val()=="") {
                        Swal.fire("Error: ", "Ingrese la fecha desde o la fecha hasta", "error");
                        return;
                    }
                }
                var pedido= $('#findpedido').val();
                $.ajax({
                    type: "get",
                    url: "{{route('ingreso.details')}}",
                    data: {
                        'serie': $('#findserie').val(),
                        'pedido': $('#findpedido').val(),
                        'codigo': $('#findcodigo').val(),
                        'lote': $('#findlote').val(),
                        'tipo': tipo,
                        'proveedor': $('#findidproveedor').val(),
                        'desde': $('#findfechadesde').val(),
                        'hasta': $('#findfechahasta').val(),

                    },
                    datatype: "html",
                    success: function (datahtml, data) {
                        console.log(data);
                        $('#findpedido').val('');
                        // $("#contes").append(datahtml);
                        var px= validPedido(pedido);
                        // if(tipo==2){
                            
                        // }
                        //console.log(px);
                        if(!px){
                            $("#tbldetalles").append(datahtml);
                            totales(0)
                        }else{
                            Swal.fire("Ingrese otro pedido");
                        }
                        if(datahtml==""){
                            Swal.fire("Mensaje: ", "No se encontraron resultados para la busqueda", "");
                        }
                        
                    },
                    error: function () {
                        Swal.fire("Error: ", "ingrese otro pedido", "error");
                        $('#findpedido').val('');
                        // alert('Error, ingrese otro pedido');
                    }
                });
            }
            function validPedido(a){
                console.log(a);
                var contador=0;
                $('.pedido').each(function(){
                    var pedido= $(this).val();
                    if(pedido==a){
                        contador++;
                    }
                    
                });
                if(contador>0){
                    return true;
                }
                return false;
            }

            function verificar(e) {
                var iva = $('option:selected', e).data("iva");
                var codigo = $(e).val(); //$('option:selected',e).data("codigo");
                var usadescuento = $('option:selected', e).data("descuento");
                var max = $('option:selected', e).data("maxdesc");
                var modPrecio = $('option:selected', e).data("precio");

                $(e).parent().children().closest(".codigo_producto").val($('option:selected', e).data("name"));
                //console.log('codigo', $(e).val());
                //console.log($(e).parent().next().next().children().closest(".cp"));
                /*
                if (modPrecio) {
                    //$(e).parent().next().next().closest(".cp");
                    console.log("modifica precio");
                    $(e).parent().next().next().children().closest(".cp").attr("disabled", "disabled");
                } else {
                    console.log("no modifca el precio");
                    $(e).parent().next().next().children().closest(".cp").removeAttr("disabled");
                }*/
                if (!usadescuento) {
                    $(e).parent().next().next().next().next().next().children().attr("readonly", "readonly");
                    $(e).parent().next().next().next().next().children().attr("readonly", "readonly");
                    $(e).parent().next().next().next().next().children().val(0);
                    $(e).parent().next().next().next().next().next().children().val(0);
                } else {
                    $(e).parent().next().next().next().next().next().children().removeAttr("readonly");
                    $(e).parent().next().next().next().next().children().removeAttr("readonly");
                    $(e).parent().next().next().next().next().next().children().val(0);
                    $(e).parent().next().next().next().next().children().val(0);
                }
                $(e).parent().next().next().next().next().children().closest(".maxdesc").val(max);
                if (iva == '1') {
                    $(e).parent().next().next().next().next().next().next().next().children().attr("checked",
                        "checked");
                } else {
                    $(e).parent().next().next().next().next().next().next().next().children().removeAttr("checked");
                }

                //cargarPrecios
                var tipo = $("#tipo_cliente").val();
                var selected = "";
                $.ajax({
                    type: 'post',
                    url: "{{route('precios')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        id: codigo
                    },
                    success: function (data) {
                        $(e).parent().next().next().children().find('option').remove();
                        $.each(data, function (key, value) {
                            if (tipo == value.nivel) {
                                selected = "selected";
                            } else {
                                selected = "";
                            }
                            $(e).parent().next().next().children().closest(".pneto").append(
                                '<option value=' + value.precio + ' ' + selected + '>' + value
                                .precio + '</option>');
                        });

                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }


            //cantidad
            //precio
            //copago
            //%descuento
            //descuento
            //precioneto
            $('body').on('blur', '.pneto', function () {
                // verificar(this);
                var cant = $(this).parent().prev().prev().prev().prev().prev().children().val();
                var copago = 0;
                var descuento = $(this).parent().next().next().next().children().val();
                var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().next().children().val(total);
                totales(0);
            });
            $('body').on('active', '.pneto', function () {
                // verificar(this);
                var cant = $(this).parent().prev().prev().prev().prev().prev().children().val();
                var copago = 0;
                var descuento = $(this).parent().next().next().children().val();
                var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().next().children().val(total);
                totales(0);
            });
            $('body').on('change', '.pneto', function () {
                // verificar(this);
                var cant = $(this).parent().prev().children().val();
                var copago = $(this).parent().next().children().val();
                var descuento = $(this).parent().next().next().next().children().val();
                var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().next().next().children().val(total);
                totales(0);
            });
            $('body').on('change', '.cneto', function () {
                // verificar(this);
                var cant = $(this).val();
                var precio = $(this).parent().next().next().next().next().next().next().children().val();
                // console.log("this", $(this).parent().next().children().val());
                var copago = 0;
                //console.log("copago", copago);
                var descuento = $(this).parent().next().next().next().next().next().next().next().next()
                    .children().val();
                var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().next().next().next().next().next().next().next().children().val(
                    total);

                totales(0);
            });

            function cnx(e) {
                var cant = $(e).val();
                var precio = $(e).parent().next().children().val();
                // console.log("e", $(e).parent().next().children().val());
                var copago = $(e).parent().next().next().children().val();
                //console.log("copago", copago);
                var descuento = $(e).parent().next().next().next().next().children().val();
                var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                total = redondeafinal(total);
                $(e).parent().next().next().next().next().next().children().val(total);
            }

            function redondeafinal(num, decimales = 2) {
                var signo = (num >= 0 ? 1 : -1);
                num = num * signo;
                if (decimales === 0) //con 0 decimales
                    return signo * Math.round(num); // to fixed num 
                // round(x * 10 ^ decimales)
                num = num.toString().split('e');
                num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
                // x * 10 ^ (-decimales)
                num = num.toString().split('e');
                return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
            }

            function comprueba_exist() {
                $('.cneto').each(function (i) {
                    var ths = $(this).val();
                    if (isNaN(ths)) {
                        ths = 0;
                    }
                });
            }

            $('body').on('change', '.copago', function () {
                verificar(this);
                var cant = $(this).parent().prev().prev().children().val();
                var precio = $(this).parent().prev().children().val();

                var copago = $(this).val();
                //console.log("copago", copago);
                var descuento = $(this).parent().next().next().children().val();
                var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                //console.log(total);
                total = redondeafinal(total);
                $(this).parent().next().next().next().children().val(total);

                totales(0);
            });


            $('body').on('change', '.pdesc', function () {

                var m = $(this).next().val();
                var cant = $(this).parent().prev().prev().prev().prev().prev().prev().prev().children().val();
                var precio = $(this).parent().prev().prev().children().val();
                var pdesc = $(this).val();

                var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100; //;
                $(this).parent().next().children().val(descuento.toFixed(2));
                var copago = $(this).parent().prev().children().val();
                var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().next().children().val(total);
                totales(0);
            });
            $('body').on('change', '.desc', function () {
                var m = verificar(this);
                var cant = $(this).parent().prev().prev().prev().prev().prev().prev().prev().prev().children()
                    .val();
                var precio = $(this).parent().prev().prev().children().val();
                /*if(pdesc> m){
                    swal("El descuento no puede ser mayor a "+m+"%");
                    $(this).val(m);
                }*/
                var descuento = $(this).val();
                verificar(this);
                console.log(cant, precio);
                var pdesc = 0;
                if (cant == 0 || precio == 0) {
                    pdesc = 0;
                } else {
                    pdesc = (descuento * 100) / (parseInt(cant) * parseFloat(precio));
                }
                //(parseInt(cant)* parseFloat(precio)) * pdesc /100;//;
                $(this).parent().prev().children().val(pdesc);
                var copago = $(this).parent().prev().prev().children().val();
                var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
                total = redondeafinal(total);
                $(this).parent().next().children().val(total);
                totales(0);
            });
            $('body').on('click', '.delete', function () {
                console.log($(this));

                $(this).parent().parent().remove();
                totales(0);
            });

            $('body').on('click', '.des', function () {
                console.log($(this));
                //alert('eliminando')
                $(this).parent().parent().parent().parent().hide('slow').remove()
                totales(0);
            });

            function totales(e) {
                var subt12 = [];
                var subt0 = [];
                var descuento = [];
                var sb12 = 0;
                var sb0 = 0;
                var descuentosub0 = 0;
                var descuentosub12 = 0;
                var d = 0;
                var copagoTotal = 0;

                if (e == 0) {
                    //$(".cneto").change();
                    //console.log("sda");
                    $('.cneto').each(function (i, obj) {
                        var cant = $(this).val();
                        $(this).parent().prev().find('.productos').attr('name', 'codigo[]');
                        $(this).parent().prev().find('.codigo_producto').attr('name', 'nombre[]');
                        //console.log(cant);
                        //$(this).attr('name', 'cantidad[]');
                        //var e = $(this).parent().prev().children().closest(".select2_cuentas");
                        var precio1 = 0;
                        var precio2 = 0;
                        var precio3 = 0;
                        var precio4 = 0;
                        var precio5 = 0;
                        var precioAut = 0;
                        var tipo = $("#tipo_cliente").val();
                        //console.log("el e es: ", e.val());
                        var precio = $(this).parent().next().next().next().next().next().children().val();
                        //$(this).parent().next().children().attr('name', 'precio[]');
                        //console.log(precio)
                        var copago = 0;
                        //$(this).parent().next().next().children().attr('name', 'copago[]');
                        var descuento = 0;
                        //$(this).parent().next().next().next().next().children().attr('name', 'descuento[]');
                        d = 0;
                        var iva = $(this).parent().next().next().next().next().next().next().next().next()
                            .next().next().children().prop('checked');
                        //console.log(iva);
                        precio = precio != null ? precio : 0;
                        //console.log(precio);
                        var total = (parseInt(cant) * parseFloat(precio)) - parseFloat(0) - parseFloat(copago);
                        //console.log("precio y cantidad" + total);

                        if (iva == 1) {
                            subt12.push(total);
                            sb12 = sb12 + total;
                            descuentosub12 += parseFloat(descuento);

                        } else {
                            subt0.push(total);
                            sb0 = sb0 + total;
                            descuentosub0 += parseFloat(descuento);
                        }
                        copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
                        //aqui falta
                        //console.log("subtotal12" + sb12);
                        $("#subtotal_12").html(sb12.toFixed(2));
                        $("#subtotal_0").html(sb0.toFixed(2));
                        $("#descuento").html(d.toFixed(2));
                        var descuento_total = descuentosub12 + descuentosub0;
                        var sum = sb12 + sb0 - descuento_total;
                        $("#base").html(sum.toFixed(2));
                        var iva = 0.12;
                        var ti = iva * sb12;
                        if (d > 0) {
                            if (sb12 > 0) {
                                ti = iva * (sb12 - descuentosub12);
                            }

                        }
                        ti = redondeafinal(ti);
                        $("#tarifa_iva").html(ti.toFixed(2, 2));
                        var t = sb12 + sb0 + ti - d;
                        //console.log(t);
                        var totax = sum + ti;
                        totax = redondeafinal(totax);
                        copagoTotal = redondeafinal(copagoTotal);
                        sb12 = redondeafinal(sb12);
                        sb0 = redondeafinal(sb0);
                        d = redondeafinal(d);

                        $("#total").html(totax.toFixed(2, 2));
                        $("#copagoTotal").html(copagoTotal.toFixed(2, 2));
                        $("#subtotal_121").val(sb12);
                        $("#subtotal_01").val(sb0);
                        $("#descuento1").val(d);
                        $("#tarifa_iva1").val(ti);
                        $("#total1").val(totax);
                        $("#totalc").val(copagoTotal);

                    });

                }
            }

            function isNumberKey(evt) {
                var charCode = (evt.which) ? evt.which : event.keyCode
                if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                    return false;

                return true;
            }

            function nuevo(id, data, patient) {
                var nuevafila = $("#mifila").html();
                var rowk = document.getElementById("entrega" + id).insertRow(-1);
                //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = "id_orden[]";
                input.value = data;
                rowk.innerHTML = fila;
                rowk.append(input);
                var input2 = document.createElement('input');
                input2.type = 'hidden';
                input2.name = "paciente[]";
                input2.value = patient;
                rowk.append(input);
                rowk.className = "well";
                $('.select2').select2({
                    tags: false
                });
            }
            /*$(".anadir").click(function(){
                alert("dsadada");
            });*/
            $(".btn_add").click(function () {

                if ($("#frm").valid()) {
                    //$(".print").css('visibility', 'visible');
                    /* $(".btn_add").attr("disabled", true);*/
                    $('.btn_add').prop('disabled', 'disabled');
                    $.ajax({
                        type: "POST",
                        url: "{{route('ingreso.store_new')}}",
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        data: $("#frm").serialize(),
                        datatype: "json",
                        success: function (data) {
                            //console.log(data);
                            Swal.fire("Mensaje: ", "Guardado correctamente", "success"); 
                            setTimeout(function(){ location.href ="{{ route('codigo.barra')}}"; }, 3000);
                        },
                        error: function (data) {
                            alert(data);
                        }
                    });
                } else {
                    alert("Existen campos vacios");
                }

            });
            function cantidad_permitida(a,e){
                var cantidadmax= parseFloat(a);
                var cantidadnow= parseFloat($(e).val());
                if(cantidadnow>cantidadmax){
                    $(e).val(a);
                    Swal.fire('Superaste la cantidad maxima');
                    totales(0);
                }
            }
        </script>
</section>
@endsection