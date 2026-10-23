<!DOCTYPE html>
<html>

<head>
    <title>Importaciones</title>
    <style>
        #page_pdf {
            width: 100%;
            margin: 15px auto 10px auto;
        }

        #importaciones_head {
            width: 100%;
            margin-bottom: 10px;
        }


        .info_empresa {
            width: 50%;
            text-align: left;
        }



        table {
            border-collapse: collapse;
            font-size: 12pt;
            font-family: 'sans-serif';
            width: 100%;
        }


        table tr:nth-child(odd) {
            background: #FFF;
        }

        table td {
            text-align: center;
            color: #000000;
            padding: 10px;
        }

        table th {
            text-align: center;
            color: #000000;
            font-size: 14px;
        }

        * {
            font-family: 'sans-serif' !important;
        }

        .table_1 {

            margin-right: 0px;
            margin-top: 0px;
            margin-left: 0px;
            margin-bottom: 0px;
            font-size: 14px;
            width: 100%;

        }

        .table_2 {

            margin-right: 0px;
            margin-top: 0px;
            margin-left: 0px;
            margin-bottom: 0px;
            font-size: 20px;
            width: 100%;
        }

        .table_3 {

            margin: 15px auto;
            margin-top: 20px;
            font-size: 12px;
            width: 20%;
        }


        .titulo_css {

            text-align: center;
            color: black;
        }

        .page_break {
            page-break-before: always;
        }
    </style>

</head>

<body>
    <div class="box-body">

        <div class="col-md-12 ">

            <div class="col-md-12 ">
                <label class="table_2" for="cliente">Cliente: {{$ct_importaciones_cab[0]->cliente->nombrecomercial}}</label>
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>
            <div class="col-md-12">
                <label class="table_2" for="cliente">Proveedor: {{$ct_importaciones_cab[0]->proveedor_da->razonsocial}}</label>
            </div>
            <div class="col-md-12">
                &nbsp;
            </div>

            <div class="col-md-1">
                <label class="table_2" for="cliente">Factura: {{$ct_importaciones_cab[0]->secuencia_factura}}</label>
            </div>

            <div class="col-md-12">
                &nbsp;
            </div>


            <table border=1 id="example" class="table table-bordered table-responsive" role="grid" aria-describedby="example2_info">
                <thead>
                    <tr role="row" id="cabecera">
                        <th>CODIGO</th>
                        <th>NOMBRE</th>
                        <th>DESCRIPCION</th>
                        <th>CANTIDAD</th>
                        <th>PESO KGs</th>
                        <th>PRECIOS</th>
                        <th>SUB TOTAL</th>
                        <th>PRECIO NETO</th>
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
                @foreach ($ct_importaciones_cab as $cab)
                @php
                foreach($cab->detalles as $valores){
                if($valores->productos->codigo != "TRANS"){
                $subTotalTotal += $valores->subtotal;
                }

                }
                @endphp
                @endforeach
                @php
                $gastosTotales = 0;
                $contadorIva = 0;
                @endphp
                @foreach ($gastos as $val)
                @php
                $gastosTotales += $val->detalle_compra->total;
                $contadorIva += $val->compra->iva_total;
                @endphp
                @endforeach
                @php
                $egresosVariosValor =0;
                @endphp
                $egresosVariosValor =0;
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


                @foreach ($ct_importaciones_cab as $cab)
                @php
                $descuentoTotal += $cab->descuento;
                $sumaTotalUni = 0;
                $subTotalUni = 0;
                $costoasignadoSuma = 0;
                $porcentajeUnitario = 0;
                $cantidad = 0;
                $costototal = 0;
                $descuento = 0;
                @endphp


                <tbody id="cuerpo">
                    @foreach ($cab->detalles as $value)
                    @if($value->productos->codigo != 'TRANS')
                    @php
                    $subTotalUni = $value->cantidad * $value->precio_desc;
                    $sumaTotalUni += $subTotalUni;
                    $descuento += $value->porcentaje;
                    $cantidad += $value->cantidad;
                    $porcentajeNumerico = $value->subtotal / $subTotalTotal;
                    $porcentajeUnitario = round(((float)$value->subtotal / $subTotalTotal) * 100, 2);
                    $costoasignado = $gastosTotales * $porcentajeNumerico;
                    $porcentaje += $porcentajeUnitario;
                    $costoasignadoSuma += $costoasignado;
                    $costoUnitario = $costoasignado / $value->cantidad;
                    $costoUn = $costoUnitario + $value->precio_desc;
                    $costTotal = $costoUn * $value->cantidad;
                    $costototal += $value->cantidad * $costoUn;
                    @endphp
                    <tr class="table_1">
                        <td>{{$value->productos->codigo}}</td>
                        <td>{{$value->productos->nombre}}</td>
                        <td>{{$value->productos->descripcion}}</td>
                        <td>{{$value->cantidad}}</td>
                        <td>{{$value->peso}}</td>
                        <td>{{$value->precio}}</td>
                        <td>{{ number_format($value->precio_desc, 2, ",", "")}}</td>
                        <td>{{number_format($subTotalUni, 2, ",", "")}}</td>
                        <td>{{$porcentajeUnitario}}%</td>
                        <td>{{number_format($costoasignado, 2, ",", "")}}</td>
                        <td>{{ number_format($costoUnitario, 2, ",", "")}}</td>
                        <td>{{ number_format($costoUn, 2, ",", "")}}</td>
                        <td>{{ number_format($costTotal, 2, ",", "")}}</td>
                    </tr>
                    @endif
                    @endforeach
                    @php

                    $arrayUnic[] = [
                    '',
                    '',
                    'Total',
                    $cantidad,
                    '',
                    '',
                    '',
                    $sumaTotalUni,
                    $porcentaje,
                    $costoasignadoSuma,
                    '',
                    '',
                    $costototal,

                    ];

                    @endphp
                    @endforeach
                    @php
                    $sumaTotalUniSub = 0;
                    $cantidadSub = 0;
                    $costoasignadoSumaSub = 0;
                    $costototalSub = 0;
                    @endphp
                    @foreach ($arrayUnic as $unic)
                    @php
                    $cantidadSub += $unic[3];
                    $sumaTotalUniSub += $unic[7];
                    $costoasignadoSumaSub += $unic[9];
                    $costototalSub += $unic[12];
                    @endphp
                    @endforeach
                    @php
                    $arr[] = [
                    'Total',
                    '',
                    '',
                    $cantidadSub,
                    '',
                    '',
                    '',
                    number_format($sumaTotalUniSub, 2, ",", ""),
                    $porcentaje . '%',
                    number_format($costoasignadoSumaSub, 2, ",", ""),
                    '',
                    '',
                    number_format($costototalSub, 2, ",", ""),
                    ];
                    @endphp
                    <tr class="table_1">
                        <td colspan="3" style="font-weight: bold;">{{$arr[0][0]}}</td>
                        <td>{{$arr[0][3]}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$arr[0][7]}}</td>
                        <td>{{$arr[0][8]}}</td>
                        <td>{{$arr[0][9]}}</td>
                        <td></td>
                        <td></td>
                        <td>{{$arr[0][12]}}</td>
                    </tr>
                </tbody>
            </table>
            @php
            $total = $subTotalTotal + $descuentoTotal;
            $totT = $total + $gastosTotales;
            $valoreTabla = [
            'TOTAL COMPRA'=> number_format($subTotalTotal, 2, ",", ""),
            'DESCUENTO'=> number_format($descuentoTotal, 2, ",", ""),
            'TOTAL' => number_format($total, 2, ",", ""),
            'GASTOS' => number_format($gastosTotales, 2, ",", ""),
            'TOTAL ' => number_format($totT, 2, ",", ""),
            'FACTOR'=>'0,00'
            ];
            @endphp

            <div class="col-md-12">
                &nbsp;
            </div>

            <div class="row">
                <div class="col-md-5" style="margin-left:40%;">
                    <table border=1 id="example" class="table_1" role="grid" aria-describedby="example2_info">
                        <thead>
                            @foreach($valoreTabla as $key=>$val)
                            <tr>
                                <td>
                                    {{$key}}
                                </td>
                                <td>
                                    {{$val}}
                                </td>
                            </tr>
                            @endforeach
                        </thead>
                        <tbody id="cuerpo">

                        </tbody>
                    </table>
                    <div class="col-md-12 text-right">

                    </div>
                </div>
            </div>
        </div>

        @foreach($gastos as $j=>$fact)
        @if($fact->tipo == 3 && $fact->estado == 1)

        <div class="col-md-12">
            <div class="col-md-4">
                <span style="font-weight: bold;">Ordenes</span>
            </div>
        </div>

        <div class="col-md-12 text-right" style="margin-bottom: 10px;">
            <button class="btn btn-danger" data-toggle="collapse" data-target="#botonOrdenes{{$j}}" role="button" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </div>
        <div class="col-md-11" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row collapse in" id="botonOrdenes{{$j}}">
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
                        <select class="form-control select2_cuentas" style="width: 100%;" name="proveedor" id="proveedor">
                            <option value="">Seleccione...</option>
                            @foreach($proveedor as $val)
                            <option {{$fact->compra->proveedor == $val->id ? 'selected' : ''}} value="{{$val->id}}">{{$val->razonsocial}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-xs-3 px-1">
                        <label class=" label_header">Dirección</label>
                        <div class="input-group">
                            <input id="direccion_proveedor" value="{{$fact->compra->direccion_proveedor}}" type="text" class="form-control" name="direccion_proveedor">
                            <div class="input-group-addon ">
                                <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-2 px-1">
                        <label class=" label_header">Fecha Pedido</label>
                        <div class="input-group col-md-12">
                            <input id="f_autorizacion" value="{{$fact->compra->f_autorizacion}}" type="date" class="form-control   col-md-12" name="f_autorizacion" value="@php echo date('Y-m-d');@endphp">
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-2 px-1">
                        <label class=" label_header">Serie</label>
                        <div class="input-group">
                            <input id="serie" maxlength="25" value="{{$fact->compra->serie}}" type="text" class="form-control" name="serie" onkeyup="agregar_serie()">
                            <div class="input-group-addon ">
                                <i class="glyphicon glyphicon-remove-circle" style="color: black;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 col-xs-2 px-1">
                        <label class=" label_header">Secuencia Pedido</label>
                        <div class="input-group">
                            <input id="secuencia_factura" value="{{$fact->compra->secuencia_factura}}" maxlength="30" type="text" class="form-control  " name="secuencia_factura" onchange="ingresar_cero()">
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
                        <table id="example2" class="table_1" role="grid" aria-describedby="example2_info">
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
                                    <td> <input type="checkbox" @if($var->iva == 1) checked @else @endif></td>

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
        </div>
        @endif
        @endforeach


        <!-- TABLA DE GASTOS -->
        <div class="col-md-12">
            &nbsp;
        </div>

        <div class="col-md-12 table-responsive page_break" style="width: 100%;">
            <table border=1 id="example2" class="table_1" role="grid" aria-describedby="example2_info">
                <thead style="color:black">
                    <tr>
                        <th colspan="4" class="gastos">
                            OTROS GASTOS
                        </th>
                    </tr>
                    <tr>
                        <th class="gastos">
                            Fecha
                        </th>
                        <th class="gastos">
                            Proveedor
                        </th>
                        <th class="gastos">
                            Cuenta
                        </th>
                        <th class="gastos">
                            Valor
                        </th>
                    </tr>
                    @php $conti = 0; @endphp
                    @foreach($gastos as $val)
                    @php
                    $conti += $val->compra->subtotal ;
                    @endphp

                    <tr>
                        <td class="borde">{{$val->compra->fecha}}</td>
                        <td class="borde">{{$val->compra->proveedor_da->nombrecomercial}}</td>
                        <td class="borde">@if(!empty($val->detalle_compra->gasto)) {{$val->detalle_compra->gasto->nombre}} @endif</td>
                        <td class="borde">{{$val->compra->subtotal}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="borde" colspan="3" style="text-align: center">TOTAL</td>

                        <td class="borde"> ${{number_format($conti,2,",","")}}</td>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-md-12">
            &nbsp;
        </div>


        <div class="col-md-6" style="margin-top:10%;">
            <table class="table_1" id="importaciones_head">
                <tr>
                    <td>
                        <div style="text-align: left; font-size:15px;margin-right:15px;">
                            IVA LIQUIDACION ADUANERA: {{$arrayIvaLiqui[1]}}<br />
                            IVA FACTURA DE GASTOS: {{$arrayIvaLiqui[0]}} <br>
                            TOTAL IVA: {{$arrayIvaLiqui[2]}}<br />
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
        @foreach ($ct_importaciones_cab as $val)
        @php
        $valorxprod =0;
        foreach($val->detalles as $detailsprod){
        // if($detailsprod->productos->codigo != "TRANS"){
        $valorxprod = $detailsprod->subtotal;
        //}

        }
        $arrp[] = [
        $val->fecha,
        'haber',
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
            <table border=1 class="table_1" role="grid" aria-describedby="example2_info">
                <thead>
                    <tr class="nuevo">
                        <th>FECHA</th>
                        <th>PRODUCTO</th>
                        <th>PRECIO</th>
                    </tr>
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
</body>

</html>