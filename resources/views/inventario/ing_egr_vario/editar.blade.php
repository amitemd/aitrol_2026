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

    .td_der {
        text-align: right;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">

<!-- Main content -->
<section class="content">
    <form method="POST" id="frm_ingreso">
        <div class="box">
            <div class="box-header">
                <div class="row">
                    <div class="col-sm-4">
                        <h3 class="box-title">Ingreso / Egreso Varios</h3>
                    </div>
                    <div class="col-md-8" style="text-align: right;">
                        <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
                        </a>
                    </div>

                </div>
                <div class="row">
                    <div class="panel-heading">
                        <div class="row">
                            <input type='hidden' id='id_pedido' name='id_pedido' value='{{@$pedido->id}}' > 
                            <!-- update headers type date: 17 Nov 2020  -->
                            <div class="form-group col-md-6{{ $errors->has('bodega_recibe') ? ' has-error' : '' }}">
                                <label for="bodega_recibe" class="col-md-4 control-label">Bodega</label>
                                <div class="col-md-8">
                                    <select class="form-control select2 " style="width: 100%;"
                                        required="" id="" disabled="disabled">
                                        <!--l-->
                                        @foreach($bodegas as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}&nbsp;@if(isset($value->empresa))-&nbsp;{{$value->empresa->nombrecomercial}}@endif
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6{{ $errors->has('tipo') ? ' has-error' : '' }}">
                                <label for="tipo" class="col-md-4 control-label">Tipo</label>
                                <div class="col-md-8">
                                    <p>@if(isset($pedido->tipo_movimiento)) {{$pedido->tipo_movimiento->descripcion}} @endif</p>

                                </div>
                            </div>
                            <!-- Proveedor -->
                            <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                                <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>
                                <div class="col-md-8">
                                    <select name="id_proveedor" style="width: 100%;" class="form-control select2"
                                        required="" id="id_proveedor">
                                        <option value="">Seleccione..</option>
                                        @foreach($proveedores as $value)
                                        <option value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- Observaciones -->
                            <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                                <label for="observaciones" class="col-md-2 control-label">Observaciones</label>
                                <div class="col-md-10">
                                    <input id="observaciones" type="text" class="form-control" name="observaciones"
                                        value="@if(!is_null($pedido)) {{ $pedido->observaciones }} @endif" autofocus>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="panel panel-default">
                    <div class="general form-group col-md-12 ">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="help-block">
                                    <strong id="lote_errores"></strong>
                                </span>
                            </div>
                            <div class="col-md-6">
                                <span class="help-block">
                                    <strong id="fecha_errores"></strong>
                                </span>
                            </div>

                        </div>

                    </div>

                    <div class="box-body">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="display compact responsive" role="grid"
                                aria-describedby="example2_info"
                                style="margin-top:0 !important; width: 100%!important;">
                                <thead>
                                    <tr role="row">
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Cantidad</th>
                                        <th>Serie</th>
                                        <th>Bodega</th>
                                        <th>Lote</th>
                                        <th>Registro Sanitario</th>
                                        <th>Fecha de Vecimiento</th>
                                        <th>Descuento %</th>
                                        <th>Precio Unitario</th>
                                        <th>Precio Final</th>
                                        <th>Acción</th>

                                    </tr>
                                </thead>
                                <tbody id="crear">
                                    @php 
                                        $i=0; 
                                    @endphp
                                    @if(!is_null($pedido))
                                    
                                    @foreach($pedido->movimientos as $row)
                                    <tr>
                                    <input type='hidden' id='iva{{$i}}' value='0' >
                                    <input type='hidden' id='id_movimiento{{$i}}' name='id_movimiento{{$i}}' value='{{$row->id}}' >
                                    <td><input type='hidden' id='visibilidad{{$i}}' name='visibilidad{{$i}}' value='1' >  <input name='id{{$i}}' type='hidden' value='{{$row->id_producto}}' > <input name='usos{{$i}}' type='hidden' value='{{$row->usos}}'  >{{$row->producto->codigo}}</td> 
                                    <td>{{$row->producto->nombre}}</td> 
                                    <td><input type='hidden' class='input-number td_der' style='width: 60px;text-align: right;' id='cantidad{{$i}}' name='cantidad{{$i}}' value='{{$row->cantidad}}' readonly>{{$row->cantidad}}</td> 
                                    <td><input type='hidden' value='{{$row->serie}}' name='serie{{$i}}' id='s{{$i}}' >{{$row->serie}}</td> 
                                    <td>@if(isset($row->bodega)){{$row->bodega->nombre}}@endif</td>
                                    <td><div class='form-group  lote_error{{$i}}'> <input style='width: 90px;' type='text' name='lote{{$i}}' value="{{$row->lote}}"  required> </div> </td> 
                                    <td><div class='form-group' >@if(isset($row->producto)){{$row->producto->registro_sanitario}}@endif</div> </td> 
                                    <td><div class='form-group fecha_vencimiento_error{{$i}}'> <input type='date' class='input-number' value='{{$row->fecha_vencimiento}}' name='fecha_vencimiento{{$i}}' > </div></td>
                                    <td><input type="hidden" name='descuento{{$i}}' id='descuento{{$i}}' class='input-number td_der' style='width: 50%;' value='0.00' readonly='readonly'> 0.00 <input type='hidden' name='descuentof{{$i}}' id='descuentof{{$i}}' value='0.00'> </td> 
                                    <td><input class='td_der' value='{{number_format($row->precio, 2, '.', '')}}' id='precio{{$i}}' type='text' style='width: 60px;' name='precio{{$i}}' readonly></td> 
                                    <td id='precio_final{{$i}}' >{{number_format(($row->cantidad * $row->precio) , 2, '.', '')}}</td>
                                    <td> <button type='button' onclick='eliminardato({{$i}})' class='btn btn-warning btn-margin' disabled="disabled">Eliminar</button> </td>  
                                    </tr>
                                    @php $i++; @endphp
                                    @endforeach
                                    @else

                                    @foreach($cab_movimiento->detalles as $row)
                                    <tr>
                                    <td>{{$row->producto->codigo}}</td> 
                                    <td>{{$row->producto->nombre}}</td> 
                                    <td><input type='hidden' class='input-number td_der' style='width: 60px;text-align: right;' id='cantidad{{$i}}' name='cantidad{{$i}}' value='{{$row->cantidad}}' readonly>{{$row->cantidad}}</td> 
                                    <td><input type='hidden' value='{{$row->serie}}' name='serie{{$i}}' id='s{{$i}}' >{{$row->serie}}</td> 
                                    <td>@if(isset($row->bodega)){{$row->bodega->nombre}}@endif</td>
                                    <td><div class='form-group  lote_error{{$i}}'> <input style='width: 90px;' type='text' name='lote{{$i}}' value="{{$row->lote}}"  required> </div> </td> 
                                    <td><div class='form-group' >@if(isset($row->producto)){{$row->producto->registro_sanitario}}@endif</div> </td> 
                                    <td><div class='form-group fecha_vencimiento_error{{$i}}'> <input type='date' class='input-number' value='{{$row->fecha_vencimiento}}' name='fecha_vencimiento{{$i}}' > </div></td>
                                    <td><input type="hidden" name='descuento{{$i}}' id='descuento{{$i}}' class='input-number td_der' style='width: 50%;' value='0.00' readonly='readonly'> 0.00 <input type='hidden' name='descuentof{{$i}}' id='descuentof{{$i}}' value='0.00'> </td> 
                                    <td><input class='td_der' value='{{number_format($row->valor_unitario, 2, '.', '')}}' id='precio{{$i}}' type='text' style='width: 60px;' name='precio{{$i}}' readonly></td> 
                                    <td id='precio_final{{$i}}' >{{number_format(($row->total) , 2, '.', '')}}</td>
                                    <td> <button type='button' onclick='eliminardato({{$i}})' class='btn btn-warning btn-margin' disabled="disabled">Eliminar</button> </td>  
                                    </tr>
                                    @php $i++; @endphp
                                    @endforeach   

                                    @endif
                                </tbody>
                                <tfoot>
                                    @php
                                        $conf = \Sis_medico\Ct_Configuraciones::find(3);
                                    @endphp
                                    <tr>
                                        
                                        <td colspan="8"><input name='contador' type="hidden" value="{{$i}}" id="contador"></td>
                                        <td colspan="2" class="td_der">{{trans('winsumos.subtotal')}} {{$conf->iva*100}}%:</td>
                                        <td><input type="hidden" name="subtotal_12" id="subtotal_12"> <input
                                                class="td_der" style="width: 55px;border: 0px;" type="hidden" readonly
                                                id="subtotal_12_1" value="{{number_format($cab_movimiento->subtotal,2,'.', '')}}">
                                                {{number_format($cab_movimiento->subtotal,2,'.', '')}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8"></td>
                                        <td colspan="2" class="td_der">Subtotal 0%:</td>
                                        <td><input type="hidden" name="subtotal_0" id="subtotal_0"> <input
                                                class="td_der" style="width: 55px;border: 0px;" type="hidden" readonly
                                                id="subtotal_0_1" value="{{number_format($cab_movimiento->subtotal_0,2,'.', '')}}">
                                                {{number_format($cab_movimiento->subtotal_0,2,'.', '')}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8"></td>
                                        <td colspan="2" class="td_der">IVA:</td>
                                        <td><input type="hidden" name="iva" id="iva"> <input class="td_der"
                                                style="width: 55px;border: 0px;" type="hidden" readonly id="iva_1"
                                                value="{{number_format($cab_movimiento->iva,2,'.', '')}}">
                                                {{number_format($cab_movimiento->iva,2,'.', '')}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8"></td>
                                        <td colspan="2" class="td_der">Total:</td>
                                        <td><input type="hidden" name="total" id="total"> <input class="td_der"
                                                style="width: 55px;border: 0px;" type="hidden" readonly id="total_1"
                                                value="{{number_format($cab_movimiento->total,2,'.', '')}}">
                                                {{number_format($cab_movimiento->total,2,'.', '')}}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="box-footer" style="text-align: center;">
                        <button type="button" id="actualizar" class="btn btn-primary"
                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                            <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
                        </button>
                    </div>
                </div>
            </div>

    </form>
</section>
<!-- /.content -->


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>

@include('inventario.ing_egr_vario.partial')
@endsection