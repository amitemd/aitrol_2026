@extends('importaciones.base')
@section('action-content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">


<style>
    th {
        font-size: 12px;
        text-align: center;

    }

    td {
        text-align: center;
    }

    .gastos {
        background-color: #aaa;
        font-weight: bold;
        color: white;

    }

    .tot {
        background-color: white;
        color: black;

    }

    .borde {
        border: 1px solid #aaa;

    }

    .nuevo {
        background-color: #aaa;
        font-weight: bold;
        color: white;
    }

    .nuevo1 {
        background-color: #aaa;
        font-weight: bold;
        color: white;
    }


    .bord {
        border: 1px solid #aaa;
    }

    .estilo1 {
        background-color: white;
        color: black;
    }

    .info_empresa {
        width: 50%;
        text-align: left;

    }

    .borde-celda td {
        border: 1px solid #aaa;
    }

    * {
        font-family: 'Poppins', sans-serif;
    }
</style>


<div class="modal fade" id="visualizar_estado" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-10">
                    <h3 class="box-title">DATOS</h3>
                </div>
            </div>
        </div>


        <div class="box-body">
            <div class="col-md-12">
                <div class="col-md-5">
                    <div class="col-md-4">
                        <label for="cliente">OBSERVACIÓN:</label>
                    </div>
                    <div class="col-md-8">
                        <p style="text-transform: uppercase;">{{$compra_cabecera->observacion}}</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="col-md-4">
                        <label for="cliente">FECHA:</label>
                    </div>
                    <div class="col-md-8">
                        <p style="text-transform: uppercase;">{{$compra_cabecera->fecha}}</p>
                    </div>
                </div>
                <div class="col-md-2">
                    @if(!is_null($log_compra))
                    <a class="btn btn-success " data-remote="{{ route('compras.modal_estado',[$log_compra->id_asiento_cabecera])}}" data-toggle="modal" data-target="#visualizar_estado"><i class="fa fa-eye"></i> Visualizador Asiento</a>
                    @endif
                </div>
            </div>
            <br>
            <br>
            <br>

            <table id="example" class="table" role="grid" aria-describedby="example2_info">
                <thead>
                    <tr role="row" id="cabecera" class="gastos">
                        <th>CODIGO</th>
                        <th>NOMBRE</th>
                        <th>DESCRIPCION</th>
                        <th>CANTIDAD</th>
                        <th>PESO KGs</th>
                        <th>PRECIO</th>
                        <th>SUB TOTAL</th>
                        <th>%</th>
                        <th>COSTO ASIGNADO DEL TOTAL</th>
                        <th>COSTO ASIGNADO UNIDAD</th>
                        <th>COSTO UNITARIO</th>
                        <th>COSTO TOTAL</th>
                    </tr>
                </thead>
                @php
                $subTotalTotal = 0;

                @endphp
                @foreach ($imp as $cab)
                @php

                foreach($cab->detalles as $valores){
                if($valores->productos->codigo != "TRANS"){
                $subTotalTotal += $valores->subtotal;
                }

                }
                //dd($cab->detalle);

                @endphp
                @endforeach
                @php
                $gastosTotales = 0;
                $contadorIva = 0;
                @endphp
                @foreach ($gastos as $val)
                @php
                $gastosTotales += $val->compra->subtotal;
                $contadorIva += $val->compra->iva_total;
                @endphp
                @endforeach
                @php
                $egresosVariosValor = $sum_egre_info;
                @endphp

                @php
                $arrayIvaLiqui = [
                $contadorIva,
                $sum_egre_info,
                $contadorIva + $egresosVariosValor
                ];
                //Calculos Generales
                //SUBTOTAL TOTAL
                $descuentoTotal = 0;
                //END
                $arrayTabla = array();
                $arrayUnic = array();
                $porcentaje = 0;
                @endphp


                @php
                $total_porcentaje = 0;
                $total_subtotal = 0;
                $total_cantidad = 0;
                $total_costo_asignado_total = 0;
                $total_costo_total =0;
                @endphp


                @foreach ($compra_cabecera->detalles as $value)
                <tr class="borde-celda">
                    <td>{{$value->codigo}}</td>
                    <td>@if(isset($value->producto)) {{$value->producto->nombre}} @endif</td>
                    <td>@if(isset($value->producto)) {{$value->producto->descripcion}} @endif</td>
                    <td>{{$value->cantidad}}</td>
                    <td>{{$value->peso_kg}}</td>
                    <td>{{$value->precio}}</td>
                    <td>{{number_format($value->total, 4, ",", "")}}</td>
                    <td>{{$value->prct_item}}%</td>
                    <td>{{number_format($value->costo_asignado_total, 4, ",", "")}}</td>
                    <td>{{number_format($value->costo_asignado_unidad, 4, ",", "")}}</td>
                    <td>{{number_format($value->costo_unitario, 4, ",", "")}}</td>
                    @php
                    $new_calculo = $value->cantidad * $value->costo_unitario;
                    //if($new_calculo < $value->costo_total){
                        // $new_calculo = $value->cantidad * (round($value->costo_unitario, 2) + 0.01);
                        //}
                        @endphp
                        <td>{{number_format($value->costo_total, 4, ",", "")}}</td>
                </tr>
                @php
                $total_cantidad += $value->cantidad;
                $total_subtotal += $value->total;
                $total_porcentaje += $value->prct_item;
                $total_costo_asignado_total += $value->costo_asignado_total;
                $total_costo_total += $value->costo_total;
                @endphp
                @endforeach



                <tr class="borde-celda">
                    <td colspan="3" style="font-weight: bold;"></td>
                    <td>{{$total_cantidad}}</td>
                    <td colspan="2"></td>
                    <td>{{number_format($total_subtotal,4,'.', '')}}</td>
                    <td>{{intval($total_porcentaje)}} %</td>
                    <td>{{number_format($total_costo_asignado_total,4,'.', '')}}</td>
                    <td colspan="2"></td>
                    <td>{{number_format($compra_cabecera->subtotal,4,'.', '')}}</td>
                </tr>
            </table>

            <div class="container">
                <center class="container">
                    <div class="row container">
                        <table id="example" class="table table-bordered table-responsive table-sm" role="grid" aria-describedby="example2_info" style="width: 40%;">
                            <thead>
                                <tr class="gastos">
                                    <th style="border: 1px solid #f4f4f4;" width="50%">DETALLE</th>
                                    <th style="border: 1px solid #f4f4f4;" width="50%">VALOR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="borde-celda">
                                    <td>TOTAL COMPRA</td>
                                    <!-- <td>{{number_format($total_subtotal,4,'.', '')}}</td> -->
                                    <td>{{number_format(($compra_cabecera->subtotal - $otros_gastos["total_gastos"] ),4,'.', '')}}</td>
                                </tr>
                                <tr class="borde-celda">
                                    <td>DESCUENTO</td>
                                    <td>0.00</td>
                                </tr>
                                <tr class="borde-celda">
                                    <td>TOTAL</td>
                                    <td>{{number_format(($compra_cabecera->subtotal - $otros_gastos["total_gastos"] ),4,'.', '')}}</td>
                                </tr>


                                <tr class="borde-celda">
                                    <td>GASTOS</td>
                                    <td>{{number_format($otros_gastos["total_gastos"] ,4,'.', '')}}</td>
                                </tr>
                                <tr class="borde-celda">
                                    <td>TOTAL</td>
                                    <!-- <td>{{number_format(($otros_gastos["total_gastos"] + $total_subtotal) ,4,'.', '')}}</td> -->
                                    <td>{{number_format(($compra_cabecera->subtotal) ,4,'.', '')}}</td>

                                </tr>
                                <tr class="borde-celda">
                                    <td>FACTOR</td>
                                    <td>0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </center>
            </div>



            @foreach($gastos as $i=>$value)
            @if($value->tipo == 1 && $value->estado == 1)
            <div class="col-md-12" style="margin-bottom: 5px;">
                <div class="col-md-11">
                    <span style="font-weight: bold;">Factura</span>
                </div>

                <div class="col-md-1">
                    <button class="btn btn-danger collapsed" data-toggle="collapse" data-target="#botonFactura{{$i}}" role="button" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-11" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row collapse" id="botonFactura{{$i}}">
                    <!--
                    @if($cab->id_asiento_cabecera != '')
                    <div class="col-md-12">
                        <div style="text-align:end ; margin-bottom:5px;">
                            <a class="btn btn-danger" href="{{route('librodiario.edit',['id'=>$value->compra->id_asiento_cabecera])}}" target="_blank">
                                Asiento
                            </a>
                        </div>
                    </div>
                    @endif
        -->

                    <div class="col-md-12" style="padding:10px; display: flex; justify-content: end;">
                        <a target="_blank" class="btn btn-success" href="{{ url('contable/contabilidad/libro/edit') }}/{{$value->compra->id_asiento_cabecera}}"><i class="fa fa-eye"></i> Visualizador Asiento</a>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class="label_header" style="padding-left: 0px">Estado</label>
                            <input class="form-control col-md-12 col-xs-12" @if($value->compra->estado == 1) style="background-color: green;" @else style="background-color: red;" @endif>
                        </div>

                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Tipo</label>
                            <input class="form-control " type="text" name="tipo" id="tipo" readonly value="FACT-COMPRA">
                        </div>

                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Fecha</label>
                            <div class="input-group col-md-12">
                                <input class="form-control col-md-12 col-xs-12" id="fecha" type="date" name="fecha" value="{{$value->compra->fecha}}">
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class="label_header">ID:</label>
                            <input class="col-md-12 col-xs-12 form-control" id="id_fc" name="id_fc" readonly value="{{$value->compra->id}}">
                        </div>
                        <div class="col-md-2 col-2 px-1">
                            <label class=" label_header">Aparece Archivo SRI &nbsp;
                            </label>
                            <div class="input-group col-md-12" style="text-align: center;">
                                <input type="hidden" name="archivosri" id="archivosri" value="1">
                                <input id="archivo_sri" name="archivo_sri" type="checkbox" value="1" checked class="flat-blue smr" style="position: absolute; opacity: 0;">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Id</label>
                            <div class="input-group">
                                <input id="proveedor" type="text" class="form-control  " name="proveedor" value="{{$cab->id_proveedor}}" onchange="cambiar_proveedor()" readonly>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('proveedor').value = ''; cambiar_proveedor()"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Proveedor</label>

                            <select class="form-control select2_cuentas" style="width: 100%;" onchange="cambiar_nombre_proveedor()" name="nombre_proveedor" id="nombre_proveedor" disabled>
                                <option value="">Seleccione...</option>
                                @foreach($proveedor as $val)
                                <option {{$value->compra->proveedor == $val->id ? 'selected' : ''}} value="{{$val->id}}">{{$val->razonsocial}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Dirección</label>
                            <div class="input-group">
                                <input id="direccion_proveedor" type="text" value="{{$value->compra->direccion_proveedor}}" class="form-control" name="direccion_proveedor" readonly>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('direccion_proveedor').value = ''; cambiar_nombre_proveedor()"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Término</label>

                            <select class="form-control" name="" disabled>
                                @foreach($termino as $term)
                                <option @if($term->id == $value->compra->termino) selected @endif>{{$term->nombre}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Fecha Caducidad</label>
                            <div class="input-group col-md-12">
                                <input id="f_caducidad" type="date" class="form-control col-md-12" name="f_caducidad" value="{{$value->compra->f_caducidad}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Autorización</label>
                            <div class="input-group">
                                <input id="autorizacion" type="text" class="form-control  " name="autorizacion" value="{{$value->compra->autorizacion}}" readonly>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Fecha Facturación</label>
                            <div class="input-group col-md-12">
                                <input id="f_autorizacion" type="date" class="form-control   col-md-12" name="f_autorizacion" value="{{$value->compra->f_autorizacion}}" readonly>
                            </div>
                        </div>

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Serie</label>
                            <div class="input-group">
                                <input id="serie" maxlength="25" type="text" class="form-control" value="{{$value->compra->serie}}" name="serie" readonly>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Secuencia Factura</label>
                            <div class="input-group">
                                <input id="secuencia_factura" maxlength="30" type="text" class="form-control  " name="secuencia_factura" value="{{$value->compra->secuencia_factura}}" readonly>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;" onclick="document.getElementById('secuencia_factura').value = '';"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 col-xs-2 col-1 px-1">
                            <label class=" label_header">Credito Tributario</label>

                            <select class="form-control" name="" disabled>
                                @foreach($c_tributario as $tributario)
                                <option @if($tributario->id == $value->compra->credito_tributario) selected @endif>{{$tributario->nombre}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 col-xs-2 col-1 px-0">
                            <label class=" label_header">Tipo de Comprobante</label>

                            <select class="form-control" name="" disabled>
                                @foreach($t_comprobante as $comprobante)
                                <option @if($comprobante->id == $value->compra->tipo_comprobante) selected @endif>{{$comprobante->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <label class=" label_header">Concepto</label>
                        <input autocomplete="off" type="text" class="form-control col-md-12" name="observacion" id="observacion" value="{{$value->compra->observacion}}" readonly>
                    </div>

                    <div class="col-md-12">
                        <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr>

                                    <th width="40%" class="" tabindex="0">Descripción del Producto</th>
                                    <th width="10%" class="" tabindex="0">Cantidad</th>
                                    <th width="10%" class="" tabindex="0">Precio</th>
                                    <th width="10%" class="" tabindex="0">% Desc</th>
                                    <th width="10%" class="" tabindex="0">Descuento</th>
                                    <th width="10%" class="" tabindex="0">Precio Neto</th>
                                    <th width="5%" class="" tabindex="0">IVA</th>
                                    <th width="10%" class="" tabindex="0">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $detalle_compra = Sis_medico\Ct_Importaciones_Detalle_Compra::where('id_ct_compras',$value->compra->id)->get(); @endphp
                                @foreach($detalle_compra as $var)
                                @php $nombre = Sis_medico\Ct_Imp_Gastos::where('id',$var->id_gasto)->first(); @endphp
                                <tr>
                                    <td>{{$nombre->nombre}}</td>
                                    <td>{{$var->cantidad}}</td>
                                    <td>{{$var->precio}}</td>
                                    <td>{{$var->descuento}}</td>
                                    <td>{{$var->descuento_porcentaje}}</td>
                                    <td>{{$var->total}}</td>
                                    <td> <input disabled type="checkbox" @if($var->iva == 1) checked @else @endif></td>

                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6"></td>
                                    <td colspan="2" class="text-right">Descuento</td>
                                    <td id="descuento" class="text-right px-1">{{$value->compra->descuento}}</td>
                                    <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                                </tr>
                                <tr>
                                    <td colspan="6"></td>
                                    <td colspan="2" class="text-right">Subtotal</td>
                                    <td id="base" class="text-right px-1">{{$value->compra->subtotal}}</td>

                                    <input type="hidden" name="base1" id="base1" class="hidden">
                                </tr>
                                <tr>
                                    <td colspan="6"></td>
                                    <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                    <td id="tarifa_iva" class="text-right px-1">{{$value->compra->iva_total}}</td>
                                    <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                                </tr>

                                <tr>
                                    <td colspan="6"></td>
                                    <td colspan="2" class="text-right"><strong>Total</strong></td>
                                    <td id="total" class="text-right px-1">{{$value->compra->total_final}}</td>
                                    <input type="hidden" name="total1" id="total1" class="hidden">
                                </tr>
                                <tr>
                                    <td colspan="6"></td>
                                    <td colspan="2" class="text-right"></td>
                                    <td id="copagoTotal" class="text-right px-1"></td>
                                    <input type="hidden" name="totalc" id="totalc" class="hidden">
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
            @endif
            @endforeach



            @foreach($gastos as $j=>$fact)
            @if($fact->tipo == 2 && $fact->estado == 1)
            <div class="col-md-12" style="margin-bottom:5px">
                <div class="col-md-11">
                    <span style="font-weight: bold;">Recibo</span>
                </div>

                <div class="col-md-1">
                    <button class="btn btn-danger collapsed" data-toggle="collapse" data-target="#botonRecibo{{$j}}" role="button" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-11" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row collapse" id="botonRecibo{{$j}}">
                    @if($fact->compra->id_asiento_cabecera != '')
                    <div class="col-md-12">
                        <div style="text-align:end ; margin-bottom:5px;">
                            <a class="btn btn-danger" href="{{route('librodiario.edit',['id'=>$fact->compra->id_asiento_cabecera])}}" target="_blank">
                                Asiento
                            </a>
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-12">
                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Proveedor</label>
                            <select class="form-control select2_cuentas" style="width: 100%;" name="proveedor" id="proveedor" disabled>
                                <option value="">Seleccione...</option>
                                @foreach($proveedor as $val)
                                <option {{$fact->compra->proveedor == $val->id ? 'selected' : ''}} value="{{$val->id}}">{{$val->razonsocial}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Dirección</label>
                            <div class="input-group">
                                <input id="direccion_proveedor" value="{{$fact->compra->direccion_proveedor}}" type="text" class="form-control" name="direccion_proveedor" readonly>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Fecha Pedido</label>
                            <div class="input-group col-md-12">
                                <input id="f_autorizacion" value="{{$fact->compra->f_autorizacion}}" type="date" class="form-control   col-md-12" name="f_autorizacion" value="@php echo date('Y-m-d');@endphp" readonly>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Serie</label>
                            <div class="input-group">
                                <input id="serie" maxlength="25" value="{{$fact->compra->serie}}" type="text" class="form-control" name="serie" onkeyup="agregar_serie()" readonly>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Secuencia Pedido</label>
                            <div class="input-group">
                                <input id="secuencia_factura" value="{{$fact->compra->secuencia_factura}}" maxlength="30" type="text" class="form-control  " name="secuencia_factura" onchange="ingresar_cero()" readonly>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row  ">

                                <input type="text" name="ivareal" id="ivareal" class="hidden" value="0.12">


                                <div class="col-md-12 px-1">
                                    <label class=" label_header">Concepto</label>
                                    <input value="{{$fact->compra->observacion}}" autocomplete="off" type="text" class="form-control col-md-12" name="observacion" id="observacion" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 table-responsive" style="width: 100%;">
                            <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th width="25%" class="" tabindex="0">Descripci&oacute;n del Producto</th>
                                        <th width="10%" class="" tabindex="0">Cantidad</th>
                                        <th width="20%" class="" tabindex="0">Precio</th>
                                        <th width="10%" class="" tabindex="0">% Desc</th>
                                        <th width="15%" class="" tabindex="0">Descuento</th>
                                        <th width="10%" class="" tabindex="0">Precio Neto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $detalle_compra_recibo = Sis_medico\Ct_Importaciones_Detalle_Compra::where('id_ct_compras',$fact->compra->id)->get(); @endphp
                                    @foreach($detalle_compra_recibo as $var)
                                    @php $nombre = Sis_medico\Ct_Imp_Gastos::where('id',$var->id_gasto)->first(); @endphp
                                    <tr>
                                        <td>@if(!is_null($nombre)) {{$nombre->nombre}} @endif</td>
                                        <td>{{$var->cantidad}}</td>
                                        <td>{{$var->precio}}</td>
                                        <td>{{$var->descuento}}</td>
                                        <td>{{$var->descuento_porcentaje}}</td>
                                        <td>{{$var->total}}</td>

                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class=''>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right">Descuento</td>
                                        <td id="descuento" class="text-right px-1">{{$fact->compra->descuento}}</td>
                                        <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                                    </tr>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right">Subtotal</td>
                                        <td id="base" class="text-right px-1">{{$fact->compra->subtotal}}</td>

                                        <input type="hidden" name="base1" id="base1" class="hidden">
                                    </tr>
                                    <!-- <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                        <td id="tarifa_iva" class="text-right px-1">$fact->compra->iva_total</td>
                                        <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                                    </tr> -->

                                    <!-- <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                        <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                        <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                                    </tr>  -->

                                    <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right"><strong>Total</strong></td>
                                        <td id="total" class="text-right px-1">{{$fact->compra->total_final}}</td>
                                        <input type="hidden" name="total1" id="total1" class="hidden">
                                    </tr>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right"></td>
                                        <td id="copagoTotal" class="text-right px-1"></td>
                                        <input type="hidden" name="totalc" id="totalc" class="hidden">
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach

            @foreach($gastos as $j=>$fact)
            @if($fact->tipo == 3 && $fact->estado == 1)
            <div class="col-md-12">
                <div class="col-md-4">
                    <span style="font-weight: bold;">Ordenes</span>
                </div>
            </div>

            <div class="col-md-12 text-right" style="margin-bottom: 10px;">
                <button class="btn btn-danger collapsed" data-toggle="collapse" data-target="#botonOrdenes{{$j}}" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>
            <div class="col-md-11" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row collapse" id="botonOrdenes{{$j}}">
                    @if($fact->compra->id_asiento_cabecera != '')
                    <div class="col-md-12">
                        <div style="text-align:end ; margin-bottom:5px;">
                            <a class="btn btn-danger" href="{{route('librodiario.edit',['id'=>$fact->compra->id_asiento_cabecera])}}" target="_blank">
                                Asiento
                            </a>
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-12">
                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Proveedor</label>
                            <input class="form-control" type="text" disabled="" name="proveedor" value="@if(isset($fact->compra->proveedor_da)){{$fact->compra->proveedor_da->nombrecomercial}} @endif">
                        </div>
                        <div class="col-md-3 col-xs-3 px-1">
                            <label class=" label_header">Dirección</label>
                            <div class="input-group">
                                <input id="direccion_proveedor" value="{{$fact->compra->direccion_proveedor}}" type="text" class="form-control" name="direccion_proveedor" disabled>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Fecha Pedido</label>
                            <div class="input-group col-md-12">
                                <input id="f_autorizacion" value="{{$fact->compra->fecha}}" type="date" class="form-control   col-md-12" name="f_autorizacion" value="@php echo date('Y-m-d');@endphp" disabled>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Serie</label>
                            <div class="input-group">
                                <input id="serie" maxlength="25" value="{{$fact->compra->serie}}" type="text" class="form-control" name="serie" onkeyup="agregar_serie()" disabled>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 col-xs-2 px-1">
                            <label class=" label_header">Secuencia Pedido</label>
                            <div class="input-group">
                                <input id="secuencia_factura" value="{{$fact->compra->secuencia_factura}}" maxlength="30" type="text" class="form-control  " name="secuencia_factura" onchange="ingresar_cero()" disabled>
                                <div class="input-group-addon ">
                                    <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row  ">

                                <input type="text" name="ivareal" id="ivareal" class="hidden" value="0.12">


                                <div class="col-md-12 px-1">
                                    <label class=" label_header">Concepto</label>
                                    <input value="{{$fact->compra->observacion}}" autocomplete="off" type="text" class="form-control col-md-12" name="observacion" id="observacion">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 table-responsive" style="width: 100%;">
                            <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th width="25%" class="" tabindex="0">Descripci&oacute;n del Producto</th>
                                        <th width="10%" class="" tabindex="0">Cantidad</th>
                                        <th width="20%" class="" tabindex="0">Precio</th>
                                        <th width="10%" class="" tabindex="0">% Desc</th>
                                        <th width="15%" class="" tabindex="0">Descuento</th>
                                        <th width="10%" class="" tabindex="0">Precio Neto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $detalle_compra_recibo = Sis_medico\Ct_Importaciones_Detalle_Compra::where('id_ct_compras',$fact->compra->id)->get(); @endphp
                                    @foreach($detalle_compra_recibo as $var)
                                    @php $nombre = Sis_medico\Ct_Imp_Gastos::where('id',$var->id_gasto)->first(); @endphp
                                    <tr>
                                        <td>@if(!is_null($nombre)) {{$nombre->nombre}} @endif</td>
                                        <td>{{$var->cantidad}}</td>
                                        <td>{{$var->precio}}</td>
                                        <td>{{$var->descuento}}</td>
                                        <td>{{$var->descuento_porcentaje}}</td>
                                        <td>{{$var->total}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class=''>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right">Descuento</td>
                                        <td id="descuento" class="text-right px-1">{{$value->compra->descuento}}</td>
                                        <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                                    </tr>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right">Subtotal</td>
                                        <td id="base" class="text-right px-1">{{$value->compra->subtotal}}</td>

                                        <input type="hidden" name="base1" id="base1" class="hidden">
                                    </tr>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                        <td id="tarifa_iva" class="text-right px-1">0.00</td>
                                        <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                                    </tr>

                                    <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right"><strong>Total</strong></td>
                                        <td id="total" class="text-right px-1">{{$value->compra->total_final}}</td>
                                        <input type="hidden" name="total1" id="total1" class="hidden">
                                    </tr>
                                    <tr>
                                        <td colspan="6"></td>
                                        <td colspan="2" class="text-right"></td>
                                        <td id="copagoTotal" class="text-right px-1"></td>
                                        <input type="hidden" name="totalc" id="totalc" class="hidden">
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach


            <div class="col-md-12 table-responsive" style="width: 100%;">
                <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead style="color:black">
                        <tr>
                            <td colspan="8" class="gastos">
                                OTROS GASTOS
                            </td>
                        </tr>
                        <tr>
                            <td class="gastos">Fecha</td>
                            <td class="gastos">Proveedor</td>
                            <td class="gastos">Detelle</td>
                            <td class="gastos">Valor</td>
                        </tr>
                        @php $conti = 0; @endphp
                        @foreach($gastos as $val)
                        @php
                        $conti += $val->compra->subtotal ;
                        @endphp
                        @if($val->tipo != 3 )
                        <tr>
                            <td class="borde">{{$val->compra->fecha}}</td>
                            <td class="borde">{{$val->compra->proveedor_da->nombrecomercial}}</td>
                            <!-- <td class="borde">@if(!empty($val->detalle_compra->gasto)) {{$val->detalle_compra->gasto->nombre}} @endif</td> -->
                            <td class="borde">{{$val->compra->observacion}}</td>
                            <td class="borde">{{$val->compra->subtotal}}</td>
                        </tr>
                        @endif
                        @endforeach
                        @if($otros_gastos["total_gastos"] > 0)
                        <tr>
                            <td class="borde"></td>
                            <td class="borde">OTROS GASTOS</td>
                            <td class="borde">OTROS GASTOS</td>
                            <td class="borde">{{number_format($otros_gastos["otros_gastos"], 2, ".", "")}}</td>
                        </tr>
                        @php
                        $conti += $otros_gastos["otros_gastos"];
                        @endphp
                        @endif

                        @if($otros_gastos["egre_varios"] > 0)
                        <tr>
                            <td class="borde"></td>
                            <td class="borde">FONDINFA</td>
                            <td class="borde">FONDINFA</td>
                            <td class="borde">{{number_format($otros_gastos["egre_varios"], 2, ".", "")}}</td>
                        </tr>
                        @php
                        $conti += $otros_gastos["egre_varios"];
                        @endphp
                        @endif
                        <tr>
                            <td class="borde" colspan="3" style="text-align: end">TOTAL</td>

                            <td class="borde"> ${{number_format($conti,2,",","")}}</td>
                        </tr>
                    </thead>
                </table>
            </div>


            <div class="col-md-6" style="margin-top:5%;">
                <table id="importaciones_head">
                    <tr>
                        <td>
                            <div style="text-align: left; font-size:15px;margin-right:15px; ">
                                IVA LIQUIDACION ADUANERA<br />
                                IVA FACTURA DE GASTOS <br>
                                TOTAL IVA<br />
                            </div>
                        </td>
                        <td>
                            <div style="text-align: left; font-size:15px;margin-right:15px;">
                                {{$arrayIvaLiqui[1]}}<br />
                                {{$arrayIvaLiqui[0]}}<br>
                                {{$arrayIvaLiqui[2]}}<br />
                            </div>
                        </td>

                    </tr>
                </table>
            </div>

            @php
            $sumaTabla = 0;
            $arrp = array();
            $sig =0;
            @endphp
            @foreach ($imp as $val)
            @php
            $valorxprod =0;
            foreach($val->detalles as $detailsprod){
            // if($detailsprod->productos->codigo != "TRANS"){
            $valorxprod = $detailsprod->subtotal;
            //}

            }
            // dd($val);
            $arrp[] = [
            $val->fecha,
            $val->observacion,
            $val->subtotal,
            //$valorxprod,
            ];
            @endphp
            @endforeach

            @php
            foreach ($ct_comprobante_egreso_varios as $egre_vario){
            foreach ($egre_vario->detalles as $det_varios){
            if($det_varios->tipo_liq == 2){
            $arrp[] = [
            $egre_vario->fecha_comprobante,
            $egre_vario->descripcion,
            $det_varios->debe,
            ];
            }
            }
            }
            @endphp


            @foreach ($gastos as $val)
            @php
            $arrp[] = [
            $val->compra->fecha,
            $val->compra->proveedor_da->nombrecomercial,
            $val->compra->subtotal,
            ];
            @endphp
            @endforeach



            <div class="col-md-6">
                <h3>CARGA CONSOLIDADA IMPORTACIÓN</h3>
                <table class="table  table-responsive" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr class="nuevo">
                            <th>FECHA</th>
                            <th>PRODUCTO</th>
                            <th>PRECIO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($arrp as $i=>$value)
                        <tr class="bord">
                            @php $sumaTabla += $arrp[$i][2]; @endphp
                            @foreach($value as $val)
                            <td class="borde">{{$val}}</td>

                            @endforeach
                        </tr>
                        @endforeach
                        <tr class="bord">
                            <td class="borde">Total</td>
                            <td class="borde"></td>
                            <td style="background-color: greenyellow;">${{number_format($sumaTabla,2,',',',')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>


    </div>
</section>
<script type="text/javascript">
    var boolValor = true;

    function cambiaValor() {
        if (boolValor) {
            document.querySelector("#cambiar").className = "fa fa-minus";
            boolValor = false;
        } else {
            document.querySelector("#cambiar").className = "fa fa-plus";
            boolValor = true;
        }
    }
</script>

@endsection