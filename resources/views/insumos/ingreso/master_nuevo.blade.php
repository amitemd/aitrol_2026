@extends('insumos.producto.base')

@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-4">
                    <h3 class="box-title">{{trans('winsumos.ingreso_orden_congl')}}</h3>
                </div>
                <div class="col-md-8" style="text-align: right;">
                    <a type="button" href="{{route('codigo.barra')}}" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                    </a>
                    <button onclick="return window.location.href = window.location.href" type="button" class="btn btn-primary  btn-gray">
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
                        <div class="row">
                            <input id="id_pedido" type="hidden" name="id_pedido" value=" @if(@isset($pedido)) @if (@$pedido->id!=""){{@$pedido->id}} @endif @endif">
                            <!-- Fecha -->
                            <div class="form-group col-md-6">
                                <label for="fecha" class="col-md-4 control-label">{{trans('winsumos.fecha_pedido')}}</label>
                                <div class="col-md-8">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="fecha" class="form-control" id="fecha" placeholder="AAAA/MM/DD" required @if(@isset($pedido)) @if(!is_null($pedido->id)) readonly @endif @endif >
                                    </div>
                                </div>
                            </div>
                            <!-- Numero de Pedido -->
                            <div class="form-group cl_pedido col-md-6 {{ $errors->has('num_factura') ? ' has-error' : '' }}">
                                <label for="num_factura" class="col-md-4 control-label">{{trans('winsumos.numero_factura')}}</label>
                                <div class="col-md-8">
                                    <input id="num_factura" type="text" class="form-control" name="num_factura" value="@if (@$pedido->factura!="") {{ $pedido->factura }} @endif" required autofocus @if(@isset($pedido)) @if(!is_null($pedido->id)) readonly @endif @endif >
                                </div>
                            </div>
                            <!-- Vencimiento -->
                            <div class="form-group col-md-6{{ $errors->has('vencimiento') ? ' has-error' : '' }}">
                                <label for="vencimiento" class="col-md-4 control-label">{{trans('winsumos.fecha_vencimiento')}}</label>
                                <div class="col-md-8">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="vencimiento" class="form-control" id="vencimiento" placeholder="AAAA/MM/DD" required @if(@isset($pedido)) @if(!is_null($pedido->id)) readonly @endif @endif >
                                    </div>
                                </div>
                            </div>
                            <!-- Proveedor -->
                            <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                                <label for="id_proveedor" class="col-md-4 control-label">{{trans('winsumos.proveedores')}}</label>
                                <div class="col-md-8">
                                    @if(@isset($pedido))
                                    @if(!is_null($pedido->id))
                                    <input type="text" name="id_proveedor2" class="form-control" id="id_proveedor2" required readonly value="{{$pedido->proveedor->nombrecomercial}}">
                                    @else
                                    <select name="id_proveedor" style="width: 100%;" class="form-control select2" required name="id_proveedor">
                                        <option value="">{{trans('winsumos.seleccione')}}</option>
                                        @foreach($proveedores as $value)
                                        <option value="{{$value->id}}" @if (@$pedido->id_proveedor!="" and
                                            @$pedido->id_proveedor==$value->id ) selected
                                            @endif>{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                    @else
                                    <select name="id_proveedor" style="width: 100%;" class="form-control select2" required name="id_proveedor">
                                        <option value="">{{trans('winsumos.seleccione')}}</option>
                                        @foreach($proveedores as $value)
                                        <option value="{{$value->id}}" @if (@$pedido->id_proveedor!="" and
                                            @$pedido->id_proveedor==$value->id ) selected
                                            @endif>{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                            </div>
                            <!-- MULTIPLE EMPRESA-->
                            <div class="form-group col-md-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                                <label for="id_empresa" class="col-md-4 control-label">{{trans('winsumos.empresa')}}</label>
                                <div class="col-md-8">
                                    @if(@isset($pedido))
                                    @if(!is_null($pedido->id))
                                    <input type="text" name="id_proveedor2" class="form-control" id="id_proveedor2" required readonly value="{{$pedido->empresa->nombrecomercial}}">
                                    @else
                                    <select id="id_empresa" class="form-control select2" style="width: 100%;" name="id_empresa" required>
                                        @foreach($empresa as $value)
                                        <option value="{{$value->id}}" @if (@$pedido->id_empresa==$value->id )
                                            selected='selected' @endif >{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                    @else
                                    <select id="id_empresa" class="form-control select2" style="width: 100%;" name="id_empresa" required>
                                        @foreach($empresa as $value)
                                        <option value="{{$value->id}}" @if (@$pedido->id_empresa==$value->id)
                                            selected='selected' @endif >{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                            </div>
                            <!-- Observaciones -->
                            <div class="form-group  col-md-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                                <label for="observaciones" class="col-md-4 control-label">{{trans('winsumos.observacion')}}</label>
                                <div class="col-md-8">
                                    <input id="observaciones" type="text" class="form-control" name="observaciones" value=" @if (@$pedido->observaciones!="") {{ @$pedido->observaciones }} @endif" autofocus required @if(@isset($pedido)) @if(!is_null($pedido->id)) readonly @endif @endif >
                                </div>
                            </div>
                            <!-- pedido -->
                            <div class="form-group  col-md-6 {{ $errors->has('pedido') ? ' has-error' : '' }}">
                                <label for="pedido" class="col-md-4 control-label">{{trans('winsumos.pedido')}}</label>
                                <div class="col-md-8">
                                    <input id="pedido" type="text" class="form-control" name="pedido" value=" @if (@$pedido->pedido!="") {{ @$pedido->pedido }} @endif" autofocus required @if(@isset($pedido)) @if(!is_null($pedido->id)) readonly @endif @endif >
                                </div>
                            </div>
                        </div>
                        @if(!@isset($pedido))
                        <div class="box-group" id="accordion">
                            <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                            <div class="panel box box-primary">
                                <div class="box-header with-border">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" class="">
                                            {{trans('winsumos.busqueda_reporte_usos')}}
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse in" aria-expanded="true" style="">
                                    <div class="box-body">
                                        <!--  -->
                                        <div class="form-group col-md-6">
                                            <label for="findfechadesde" class="col-md-4 control-label">{{trans('winsumos.fecha_desde')}}</label>
                                            <div class="col-md-8">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" value="" name="findfechadesde" class="form-control" id="findfechadesde" placeholder="AAAA/MM/DD" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="findfechahasta" class="col-md-4 control-label">{{trans('winsumos.fecha_hasta')}}</label>
                                            <div class="col-md-8">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" value="" name="findfechahasta" class="form-control" id="findfechahasta" placeholder="AAAA/MM/DD" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label col-md-4">{{trans('winsumos.Tipo')}} </label>
                                            <div class="col-md-8">
                                                <select name="tipo" class="form-control select2" style="width: 100%;" id="tipo" required>
                                                    <option value="">{{trans('winsumos.seleccione')}}</option>
                                                    @foreach($doc_bodega as $value)
                                                    @if ($value->id == '1') <option selected value="{{$value->id}}"> {{$value->documento}} </option> @endif 
                                                    <!-- @if ($value->id == '3') <option @if(isset($data['tipo'])) @if(!is_null($data['tipo']) and $data['tipo']==3) selected @endif @endif value="{{$value->id}}"> {{$value->documento}} </option> @endif -->
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                            <!-- <button onclick="getLoader(this.value, 1)" class="btn btn-success btn-gray pull-right" type="button"> <i class="fa fa-search"></i> </button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- <div class="general form-group ">
                        <label class="col-md-4 control-label"></label>
                        <div class="col-md-8">
                        </div>
                        <span class="help-block">
                            <strong id="lote_errores"></strong>
                        </span>
                    </div> -->
                    @if (isset($pedido))
                    <div class="box-body table-responsive">
                        {{-- <input name='contador' type="hidden" value="0" id="contador"> --}}
                        <table id="tbl_detalles" name="tbl_detalles" class="display compact responsive nowarp" role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%;">
                            <thead>
                                <tr>
                                    <th tabindex="0" style="text-align: center;">{{trans('winsumos.numero')}}</th>
                                    <th tabindex="0">{{trans('winsumos.proveedores')}}</th>
                                    <th tabindex="0">{{trans('winsumos.codigo')}}</th>
                                    <th tabindex="0">{{trans('winsumos.productos')}}</th>
                                    <th tabindex="0">{{trans('winsumos.cantidad')}}</th>
                                    <th tabindex="0">{{trans('winsumos.bodegas')}}</th>
                                    <th tabindex="0">{{trans('winsumos.serie')}}</th>
                                    <th tabindex="0">{{trans('winsumos.lote')}}</th>
                                    <th tabindex="0">{{trans('winsumos.fecha_vencimiento')}}</th>
                                    <th style="text-align: right;">{{trans('winsumos.precio')}}</th>
                                    {{-- <th tabindex="0">% {{trans('winsumos.descuento')}}</th> --}}
                                    <th style="text-align: right;">{{trans('winsumos.descuento')}}</th>
                                    <th style="text-align: right;">{{trans('winsumos.total')}}</th>
                                    <th style="text-align: right">{{trans('winsumos.iva')}}</th>
                                    <th style="text-align: right;">{{trans('winsumos.accion')}}</th>
                                </tr>
                            </thead>
                            <tbody id="tbldetalles">
                                @if(count($pedido->pre_movimientos()->get())>0)
                                @foreach (@$pedido->pre_movimientos()->get() as $x)
                                    @php
                                        $bodega = Sis_medico\Bodega::find($x->id_bodega);
                                        $producto = Sis_medico\Producto::find($x->id_producto);
                                    @endphp
                                <tr>
                                    <td>
                                        <input style="width: 180px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="text" class="form-control input-sm pedido" readonly name="pedido[]" value="{{$x->pre_pedido->pedido}}">
                                    </td>
                                    <td>
                                        <input style="width: 200px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="text" class="form-control input-sm" readonly name="proveedor[]" value="{{$x->pre_pedido->proveedor->nombrecomercial}}">
                                        <input style="width: 200px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="hidden" value="@if(isset($x->pre_pedido)) {{$x->pre_pedido->proveedor->id_proveedor}} @endif">
                                    </td>
                                    <td>
                                        <input style="width: 200px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="text" class="form-control input-sm" readonly name="codigo[]" value="{{$x->producto->codigo}}">
                                    </td>
                                    <td>
                                        <input style="@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="text" class="form-control input-sm" readonly name="nombre[]" value="{{$x->producto->nombre}}">
                                        <input type="hidden" name="id[]" value="{{$x->id_producto}}">
                                    </td>
                                    <td>
                                        <input style="width: 70px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="text" style="text-align:right;" class="form-control input-sm cneto" onchange="cantidad_permitida2('{{$x->cantidad}}',this,'{{$x->id}}')" name="cantidad[]" required value="{{$x->cantidad}}">
                                    </td>
                                    <td>
                                        
                                        <input style="width: 200px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="text" class="form-control input-sm" readonly name="nbodega[]" value="@if(isset($bodega)){{$bodega->nombre}}@endif">
                                        <input type="hidden" name="bodega[]" value="{{$bodega->id}}">
                                    </td>
                                    <td>
                                        <input style="width: 150px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="text" class="form-control input-sm serie" readonly name="serie[]" value="{{$x->serie}}" readonly>
                                    </td>
                                    <td>
                                        <input style="width: 70px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="text" class="form-control input-sm" name="lote[]" readonly value="{{$x->lote}}">
                                    </td>
                                    <td>
                                        <input style="width: 90px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="date" name="fecha_vencimiento[]" class="form-control input-sm" readonly value="{{$x->fecha_vencimiento}}">
                                    </td>
                                    <td>
                                        <input style="width: 70px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" type="text" style="text-align:right;" class="form-control input-sm pneto" readonly onkeypress="return isNumberKey(event)" name="precio[]" value="{{$x->precio}}">
                                    </td>
                                    <td>
                                        <input style="width: 70px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" class="form-control input-sm text-right pdesc" type="text" readonly style="text-align:right;" name="pDescuento[]" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" @if($x->descuentop!=null) value="{{$x->descuentop}}"
                                        @else value="0.00" @endif required>
                                        <input type="hidden" readonly onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                    </td>
                                    @php $precio_neto= $x->cantidad * $x->precio; @endphp
                                    <td>
                                        <input style="width: 70px;@if(!is_null($producto->cantidad_usos_kardex)) background-color: yellow; @endif" class="form-control input-sm pneto" readonly type="text" style="text-align:right;" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{number_format($precio_neto,2)}}" readonly required>
                                    </td>
                                    <td align="right">
                                        <input class="form" type="checkbox" disabled readonly name="valor_iva[]" @if($x->iva > 0)
                                        checked="checked" @endif
                                        value="1">
                                    </td>
                                    <td style="text-align: center;">
                                        <!--button type="button" class="btn btn-danger  delete">
                                            <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                        </button-->
                                        <button type="button" class="btn btn-danger" onclick="eliminar_item('{{$x->id}}')">
                                            <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="box-body">
                    <table class="display compact responsive nowarp" style="margin-top:0 !important; width: 100%!important;">
                                @php
                                    if (isset($pedido)) {
                                        $subtotal_12 = 0.00;
                                        $subtotal_0 = 0.00;
                                        $descuento = 0.00;
                                        if(!is_null($pedido->subtotal_12)){
                                            $subtotal_12 = $pedido->subtotal_12;
                                        }

                                        if(!is_null($pedido->subtotal_0)){
                                            $subtotal_0 = $pedido->subtotal_0;
                                        }
                                        
                                        if(!is_null($pedido->subtotal_0)){
                                            $descuento = $pedido->descuento;
                                        }
                                        $sub_total_sin_iva = $subtotal_12 + $subtotal_0 - $descuento;
                                    }
                                @endphp
                                <tr>
                                    <th colspan="11" class="text-right">{{trans('winsumos.subtotal')}} 15%</th>
                                    <th id="subtotal_12_" colspan="1" class="text-right px-1"> {{$subtotal_12}}</th>
                                    <input type="hidden" name="subtotal_121_" id="subtotal_121_" class="hidden">
                                </tr>
                                <tr>
                                    <th colspan="11" class="text-right">{{trans('winsumos.subtotal')}} 0%</th>
                                    <th id="subtotal_0_" colspan="1" class="text-right px-1">{{$subtotal_0}}</th>
                                    <input type="hidden" name="subtotal_01_" id="subtotal_01_" class="hidden">
                                </tr>
                                <tr>
                                    <th colspan="11" class="text-right">{{trans('winsumos.descuento')}}</th>
                                    <th id="descuento_" colspan="1" class="text-right px-1">{{ $descuento }}</th>
                                    <input type="hidden" name="descuento1_" id="descuento1_" class="hidden">
                                </tr>
                                <tr>
                                    <th colspan="11" class="text-right">{{trans('winsumos.subtotal_sin_iva')}}</th>
                                    <th id="base_" colspan="1" class="text-right px-1">{{$sub_total_sin_iva}}</th>
                                    <input type="hidden" name="base1_" id="base1_" class="hidden">
                                </tr>
                                <tr>
                                    <!-- <th colspan="11" class="text-right">{{trans('contableM.tarifaiva')}}</th> -->
                                    <th colspan="11" class="text-right">Tarifa Iva 15%	</th>
                                    <th id="v_iva" colspan="1" class="text-right px-1">@if (isset($pedido)) @if(!is_null($pedido->iva)) {{ $pedido->iva }} @else 0.00 @endif @endif</th>
                                    <input type="hidden" name="v_iva1" id="v_iva1" class="hidden">
                                </tr>
                                <tr>
                                    <th colspan="11" class="text-right"><strong>{{trans('winsumos.total')}}</strong></th>
                                    <th id="total_" colspan="1" class="text-right px-1">@if (isset($pedido)) @if(!is_null($pedido->total)) {{ $pedido->total }} @else 0.00 @endif @endif</th>
                                    <input type="hidden" name="total2" id="total2" class="hidden">
                                </tr>
                        </table>
                    </div>
                    @endif
                    <div class="box-footer" style="text-align: center;">
                        
                        @if(!@isset($pedido))
                        <a onclick="guardar_prepedido()" class="btn btn-primary btn-sm" id="btn_guardar_prepedido" method="post">
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos.crear_conglomerada')}}&nbsp;</span></a>
                        <a onclick="guardar_prepedido_consumibles()" class="btn btn-success btn-sm" id="btn_guardar_prepedido" method="post">
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos.crear_conglomerada')}} Consumibles&nbsp;</span></a>
                        @else
                        <a onclick="crear_conglomerada()" class="btn btn-primary btn-sm" id="btn_guardar" method="post">
                            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos.guardar')}}&nbsp;</span></a>
                        @endif
                        @if (@isset($pedido) and $pedido->orden_conglomerada!=1)
                        <!-- <button type="button" class="btn btn-success btn_generar_orden" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                            <i class="fa fa-file-o"></i> &nbsp;&nbsp;{{trans('winsumos.crear_orden')}}
                            </button> -->
                        @endif
                    </div>
                    <div style="display: flex;align-items:center;justify-content:center;z-index:99999">
                        <img src="https://media.tenor.com/DPEfCqnChk0AAAAi/loading-slow-net.gif" alt="load" id="carga" style="display: none">
                    </div>
                </form>
            </div>
        </div>
        <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
        <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @include('insumos.ingreso.partial')
</section>
<script>
    function cantidad_permitida2(a, e, id_mov) {
        var cantidadmax = parseFloat(a);
        var cantidadnow = parseFloat($(e).val());
        if (cantidadnow > cantidadmax) {
            $(e).val(a);
            Swal.fire("{{trans('winsumos.cantidad_excedida')}}");
            totales(0);
        }else{
            actualizar_item(id_mov, cantidadnow);
        }
    }
    function eliminar_item(id_mov){
        $.ajax({
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                url: "{{route('ingreso.nvo_cgl_eliminar_item')}}",
                datatype: 'html',
                data: { id_mov: id_mov },
                success: function(data) {                
                   location.reload();
                },
                error: function(data) {
                document.getElementById("carga").style.display = "none";
                Swal.fire({
                    title: 'Error!',
                    text: data.error,
                    icon: 'error',
                    confirmButtonText: 'ok'
                })
                }
            });    
    }
    function actualizar_item(id_mov, cantidad){
        $.ajax({
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                url: "{{route('ingreso.nvo_cgl_actualizar_item')}}",
                datatype: 'html',
                data: { id_mov: id_mov, cantidad: cantidad },
                success: function(data) {                
                   location.reload();
                },
                error: function(data) {
                document.getElementById("carga").style.display = "none";
                Swal.fire({
                    title: 'Error!',
                    text: data.error,
                    icon: 'error',
                    confirmButtonText: 'ok'
                })
                }
            });
    }
    function guardar_prepedido() {
        var formulario = document.forms["frm"];
        var observacion = $("#observaciones").val().trim();
        var factura = $("#num_factura").val().trim();
        var fecha = $("#fecha").val();
        var vencimiento = $("#vencimiento").val().trim();
        var proveedor = formulario.id_proveedor.value;
        var id_empresa = formulario.id_empresa.value;
        var pedido = $("#pedido").val().trim();
        var findfechadesde = $("#findfechadesde").val();
        var findfechahasta = $("#findfechahasta").val();
        var tipo = $("#tipo").val();
        var msj = "";
            if (observacion == "") {
                msj += "Por favor, Llene el campo observaciones<br/>";
            }
            if (factura == "") {
                msj += "Por favor, Llene el campo numero factura<br/>";
            }
            if (fecha == "") {
                msj += "Ingrese fecha pedido<br/>";
            }
            if (vencimiento == "") {
                msj += "Por favor, Llene el campo vencimiento<br/>";
            }
            if (proveedor == "") {
                msj += "Por favor, Seleccione el proveedor<br/>";
            }
            if (id_empresa == "") {
                msj += "Por favor, Seleccione la empresa<br/>";
            }
            if (pedido == "") {
                msj += "Por favor, Llene el numero de pedido<br/>";
            }
            if (findfechadesde == "") {
                msj += "Por favor, Llene fecha desde<br/>";
            }
            if (findfechahasta == "") {
                msj += "Por favor, Llene fecha hasta<br/>";
            }
            if (tipo == "") {
                msj += "Por favor, Llene tipo<br/>";
            }
            if (msj == "") {
            document.getElementById("carga").style.display = "block";
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('ingreso.guardar_conglomerada_nueva')}}",
                    datatype: 'html',
                    data: $("#frm").serialize(),
                    success: function(data) {                
                    let id  = data.id;
                    if (id != null) {
                        document.getElementById("carga").style.display = "none";
                        window.location.href="{{url('/')}}/producto/editar/conglomerada/nueva/"+id;
                    } else {
                        document.getElementById("carga").style.display = "none";
                        Swal.fire({
                            title: 'Error!',
                            text: data.error,
                            icon: 'error',
                            confirmButtonText: 'ok'
                        })
                    }
                    },
                    error: function(data) {
                        document.getElementById("carga").style.display = "none";
                        Swal.fire({
                            title: 'Error!',
                            text: data.error,
                            icon: 'error',
                            confirmButtonText: 'ok'
                        })
                    }
                });
        } else {
            Swal.fire({
                    title: "Error!",
                    type: "error",
                    html: msj
                });
            }
        
    }

    
    function guardar_prepedido_consumibles() {
        var formulario = document.forms["frm"];
        var observacion = $("#observaciones").val().trim();
        var factura = $("#num_factura").val().trim();
        var fecha = $("#fecha").val();
        var vencimiento = $("#vencimiento").val().trim();
        var proveedor = formulario.id_proveedor.value;
        var id_empresa = formulario.id_empresa.value;
        var pedido = $("#pedido").val().trim();
        var findfechadesde = $("#findfechadesde").val();
        var findfechahasta = $("#findfechahasta").val();
        var tipo = $("#tipo").val();
        var msj = "";
            if (observacion == "") {
                msj += "Por favor, Llene el campo observaciones<br/>";
            }
            if (factura == "") {
                msj += "Por favor, Llene el campo numero factura<br/>";
            }
            if (fecha == "") {
                msj += "Ingrese fecha pedido<br/>";
            }
            if (vencimiento == "") {
                msj += "Por favor, Llene el campo vencimiento<br/>";
            }
            if (proveedor == "") {
                msj += "Por favor, Seleccione el proveedor<br/>";
            }
            if (id_empresa == "") {
                msj += "Por favor, Seleccione la empresa<br/>";
            }
            if (pedido == "") {
                msj += "Por favor, Llene el numero de pedido<br/>";
            }
            if (findfechadesde == "") {
                msj += "Por favor, Llene fecha desde<br/>";
            }
            if (findfechahasta == "") {
                msj += "Por favor, Llene fecha hasta<br/>";
            }
            if (tipo == "") {
                msj += "Por favor, Llene tipo<br/>";
            }
            if (msj == "") {
            document.getElementById("carga").style.display = "block";
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('ingreso.guardar_conglomerada_nueva_consumibles')}}",
                    datatype: 'html',
                    data: $("#frm").serialize(),
                    success: function(data) {                
                    let id  = data.id;
                    if (id != null) {
                        document.getElementById("carga").style.display = "none";
                        window.location.href="{{url('/')}}/producto/editar/conglomerada/nueva/"+id;
                    } else {
                        document.getElementById("carga").style.display = "none";
                        Swal.fire({
                            title: 'Error!',
                            text: data.error,
                            icon: 'error',
                            confirmButtonText: 'ok'
                        })
                    }
                    },
                    error: function(data) {
                        document.getElementById("carga").style.display = "none";
                        Swal.fire({
                            title: 'Error!',
                            text: data.error,
                            icon: 'error',
                            confirmButtonText: 'ok'
                        })
                    }
                });
        } else {
            Swal.fire({
                    title: "Error!",
                    type: "error",
                    html: msj
                });
            }
        
    }
    function anular_conglomerada2(){
        if ($("#frm").valid()) {
            $('.btn_add').prop('disabled', 'disabled');
            $.ajax({
                type: "POST",
                url: "{{route('ingreso.anular_envio_orden')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $("#frm").serialize(),
                datatype: "json",
                success: function (data) {
                    //console.log(data);
                    Swal.fire("Mensaje: ", `{{trans('proforma.GuardadoCorrectamente')}}`, "success");
                    setTimeout(function () {
                       location.href = "{{ route('codigo.barra')}}";
                    }, 3000);
                },
                error: function (data) {
                    alert(data);
                }
            });
        } else {
            alert(`{{trans('proforma.camposvacios')}}`);
        }
    }
    function crear_conglomerada(){
        var idPedido= $("#id_pedido").val();
        document.getElementById("btn_guardar").style.display = "none";
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            //url: "{{route('ingreso.crear_conglomerada')}}",
            url: "{{ url('producto/crear/conglomerada')}}",
            datatype: 'html',
            data: { idPedido: idPedido },
            success: function(data) {
                let id  = data.id;
                if (id != null) {
                    window.location.href="{{url('/')}}/codigo/barras";
                }
                else{
                    document.getElementById("btn_guardar").style.display = "initial";
                    Swal.fire({
                        title: 'Error!',
                        text: data.error,
                        icon: 'error',
                        confirmButtonText: 'ok'
                    })
                }
            },
            error: function(data) {
                document.getElementById("btn_guardar").style.display = "initial";
                Swal.fire({
                    title: 'Error!',
                    text: data.error,
                    icon: 'error',
                    confirmButtonText: 'ok'
                })
            }
        });  
    }
</script>
@endsection