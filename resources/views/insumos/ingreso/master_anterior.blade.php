@extends('insumos.producto.base')

@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-4">
                    <h3 class="box-title">Ingreso de Orden Conglomerada Anterior</h3>
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
                                                <input class="form-control" type="text" name="findserie" id="findserie"
                                                    placeholder="Ingrese la serie del item">
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
                                            <label for="findfechadesde" class="col-md-4 control-label">Fecha
                                                Desde</label>
                                            <div class="col-md-8">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" value="" name="findfechadesde"
                                                        class="form-control" id="findfechadesde"
                                                        placeholder="AAAA/MM/DD">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="findfechahasta" class="col-md-4 control-label">Fecha
                                                Desde</label>
                                            <div class="col-md-8">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" value="" name="findfechahasta"
                                                        class="form-control" id="findfechahasta"
                                                        placeholder="AAAA/MM/DD">
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
                            <input id="id_pedido" type="hidden" name="id_pedido" value="@if (@$pedido->id!=""){{@$pedido->id}}@endif">
                            <!-- Fecha -->
                            <div class="form-group col-md-6">
                                <label for="fecha" class="col-md-4 control-label">Fecha Pedido</label>
                                <div class="col-md-8">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="fecha" class="form-control" id="fecha"
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
                                        value="@if (@$pedido->factura!="") {{ $pedido->factura }} @else {{ old('num_factura') }} @endif"
                                        required autofocus>
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
                                        <input type="text" name="vencimiento" class="form-control" id="vencimiento"
                                            placeholder="AAAA/MM/DD">
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
                                        <option value="{{$value->id}}" @if (@$pedido->id_proveedor!="" and
                                            @$pedido->id_proveedor==$value->id ) selected
                                            @endif>{{$value->nombrecomercial}}</option>
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
                                        <option value="{{$value->id}}" @if (@$pedido->id_empresa==$value->id )
                                            selected='selected' @endif >{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- Observaciones -->
                            <div class="form-group  col-md-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                                <label for="observaciones" class="col-md-4 control-label">Observaciones</label>
                                <div class="col-md-8">
                                    <input id="observaciones" type="text" class="form-control" name="observaciones"
                                        value=" @if (@$pedido->observaciones!="") {{ @$pedido->observaciones }} @else {{ old('observaciones') }} @endif"
                                        autofocus>
                                </div>
                            </div>




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
                        <table id="tbl_detalles" name="tbl_detalles" class="display compact responsive nowarp"
                            role="grid" aria-describedby="example2_info"
                            style="margin-top:0 !important; width: 100%!important;">
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
                                    <th style="text-align: right">Precio</th>
                                    {{-- <th tabindex="0">% Desc.</th> --}}
                                    <th style="text-align: right">Desc.</th>
                                    <th style="text-align: right">Total</th>
                                    <th style="text-align: right">IVA</th>
                                    <th style="text-align: right">Accion</th>
                                </tr>
                            </thead>
                            <tbody id="tbldetalles"> 
                                @if (isset($pedido))
                                @foreach (@$pedido->movimientos()->get() as $x)
                                <tr>
                                    <td> <input type="text" style="width: 99%;height:20px;"
                                            class="form-control input-sm pedido" readonly name="pedido[]"
                                            value="{{$x->pedido->pedido}}">
                                    </td>
                                    <td> <input type="text" style="width: 99%;height:20px;"
                                            class="form-control input-sm" readonly name="proveedor[]"
                                            value="{{$x->pedido->proveedor->nombrecomercial}}">
                                        <input type="hidden" value="{{$x->pedido->proveedor->id_proveedor}}">
                                    </td>
                                    <td> <input type="text" style="width: 99%;height:20px;"
                                            class="form-control input-sm" readonly name="codigo[]"
                                            value="{{$x->producto->codigo}}">
                                    </td>
                                    <td> <input type="text" style="width: 99%;height:20px;"
                                            class="form-control input-sm" readonly name="nombre[]"
                                            value="{{$x->producto->nombre}}">
                                        <input type="hidden" name="id[]" value="{{$x->id_producto}}">
                                    </td>
                                    <td> <input type="text" style="width: 97%;height:20px; text-align:right;"
                                            class="form-control input-sm cneto"
                                            onchange="cantidad_permitida({{$x->cantidad}},this)" name="cantidad[]" required
                                            value="{{$x->cantidad}}"> </td>
                                    <td> <input type="text" style="width: 99%;height:20px;"
                                                class="form-control input-sm" readonly name="nbodega[]"
                                                value="@if(isset($x->pedido->bodega)){{$x->pedido->bodega->nombre}}@endif">
                                            <input type="hidden" name="bodega[]" value="@if(!is_null($x->pedido->id_bodega) and $x->pedido->id_bodega !="" ){{$x->pedido->id_bodega}} @else {{ env('BODEGA_PRINCIPAL',1) }} @endif">
                                    </td>
                                    <td> <input type="text" style="width: 100%;height:20px;"
                                            class="form-control input-sm serie" readonly name="serie[]" value="{{$x->serie}}"
                                            readonly> </td>
                                    <td> <input type="text" style="width: 99%;height:20px;"
                                            class="form-control input-sm" name="lote[]" readonly value="{{$x->lote}}">
                                    </td>
                                    <td> <input type="date" style="width: 99%;height:20px;" name="fecha_vencimiento[]"
                                            class="form-control input-sm" readonly value="{{$x->fecha_vencimiento}}">
                                    </td>
                                    <td> <input type="text" style="width: 95%;height:20px; text-align:right;"
                                            class="form-control input-sm pneto" readonly
                                            onkeypress="return isNumberKey(event)" name="precio[]"
                                            value="{{number_format($x->precio,2)}}">
                                    </td>
                                    <td>
                                        <input class="form-control input-sm text-right pdesc" type="text" readonly
                                            style="width: 95%;height:20px; text-align:right;" name="pDescuento[]"
                                            onkeypress="return isNumberKey(event)"
                                            onblur="this.value=parseFloat(this.value).toFixed(0);"
                                            @if($x->descuentop!=null) value="{{$x->descuentop}}"
                                            @else value="0.00" @endif required>
                                        <input type="hidden" readonly onkeypress="return isNumberKey(event)"
                                            onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                    </td>
                                    @php $precio_neto= $x->cantidad * $x->precio; @endphp
                                    <td>
                                        <input class="form-control input-sm pneto" readonly type="text"
                                            style="width: 95%;height:20px; text-align:right;"
                                            onkeypress="return isNumberKey(event)"
                                            onblur="this.value=parseFloat(this.value).toFixed(2);"
                                            value="{{number_format($precio_neto,2)}}" required>
                                    </td>
                                    <td align="right">
                                        <input class="form" type="checkbox" readonly name="valor_iva[]" @if($x->iva==1)
                                        checked="checked" @endif
                                        value="1">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger  delete">
                                            <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                            <tfoot class='well'>
                                <tr>
                                    <th colspan="9" class="text-right">Subtotal 12%</th>
                                    <th id="subtotal_12" colspan="3" class="text-right px-1">0.00</th>
                                    <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th colspan="9" class="text-right">Subtotal 0%</th>
                                    <th id="subtotal_0" colspan="3" class="text-right px-1">0.00</th>
                                    <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th colspan="9" class="text-right">Descuento</th>
                                    <th id="descuento" colspan="3" class="text-right px-1">0.00</th>
                                    <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th colspan="9" class="text-right">Subtotal sin Impuesto</th>
                                    <th id="base" colspan="3" class="text-right px-1">0.00</th>
                                    <input type="hidden" name="base1" id="base1" class="hidden">
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th colspan="9" class="text-right">Tarifa Iva 12%</th>
                                    <th id="tarifa_iva" colspan="3" class="text-right px-1">0.00</th>
                                    <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th colspan="9" class="text-right"><strong>Total</strong></th>
                                    <th id="total" colspan="3" class="text-right px-1">0.00</th>
                                    <input type="hidden" name="total1" id="total1" class="hidden">
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="box-footer" style="text-align: center;">
                        <button type="button" class="btn btn-primary btn_add"
                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                            <i class="fa fa-save"></i> &nbsp;&nbsp;Guardar
                        </button>
                        @if (@isset($pedido) and $pedido->orden_conglomerada!=1)
                        <button type="button" class="btn btn-success btn_generar_orden"
                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                            <i class="fa fa-file-o"></i> &nbsp;&nbsp;Crear  Orden
                        </button>
                        @endif
                        @if (@isset($pedido) and $pedido->orden_conglomerada==1)
                        <button type="button" class="btn btn-danger btn_anular_envio_orden"
                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                            <i class="fa fa-file-o"></i> &nbsp;&nbsp;Anular el envio de la Orden
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
        <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

        @include('insumos.ingreso.partial')
</section>
@endsection