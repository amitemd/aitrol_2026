<!DOCTYPE html>
<html lang="en">

<head>
    <title>Importaciones</title>

    <style>
        th {
            font-size: 16px;
            text-align: center;

        }

        td {
            text-align: center;
            font-size: 16px;
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

        *{
            text-transform: uppercase;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <section class="content">
        <div class="box">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-10">
                        <h4 class="box-title">IMPORTACIÓN # {{$id}}</h4>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12">
                    <div class="col-md-12 ">
                        <label class="table_2" for="cliente" style="font-size: 18px">Observacion: {{$compras->observacion}}</label>
                    </div>
                    <div class="col-md-12">
                        &nbsp;
                    </div>
                    <div class="col-md-12">
                        <label class="table_2" for="cliente" style="font-size: 18px">Fecha: {{$compras->fecha}}</label>
                    </div>
                    <div class="col-md-12">
                        &nbsp;
                    </div>

                    

                    <div class="col-md-12">
                        &nbsp;
                    </div>
                </div>


                <table id="example" class="table" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr role="row" id="cabecera" class="gastos">
                            <th>{{trans('contableM.codigo')}}</th>
                            <th>NOMBRE</th>
                            <th>{{trans('contableM.Descripcion')}}</th>
                            <th>{{trans('contableM.cantidad')}}</th>
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
                    @foreach ($ct_importaciones_cab as $cab)
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
                    //dd($val->compra);
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


                    @foreach ($compras->detalles as $value)
                    <tr class="borde-celda">
                        <td>{{$value->codigo}}</td>
                        <td>@if(isset($value->producto)) {{$value->producto->nombre}} @endif</td>
                        <td>@if(isset($value->producto)) {{$value->producto->descripcion}} @endif</td>
                        <td>{{$value->cantidad}}</td>
                        <td>{{$value->peso_kg}}</td>
                        <td>{{$value->precio}}</td>
                        <td>{{number_format($value->total, 2, ",", "")}}</td>
                        <td>{{$value->prct_item}}%</td>
                        <td>{{number_format($value->costo_asignado_total, 2, ",", "")}}</td>
                        <td>{{number_format($value->costo_asignado_unidad, 2, ",", "")}}</td>
                        <td>{{number_format($value->costo_unitario, 2, ",", "")}}</td>
                        <td>{{number_format($value->costo_total, 2, ",", "")}}</td>
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
                        <td>{{number_format($total_subtotal,2,'.', '')}}</td>
                        <td>{{intval($total_porcentaje)}} %</td>
                        <td>{{number_format($total_costo_asignado_total,2,'.', '')}}</td>
                        <td colspan="2"></td>
                        <td>{{round($total_costo_total, 2)}}</td>
                    </tr>
                </table>

                <div class="col-md-6" style="margin-top:10%;">
                    <table id="importaciones_head" align="left">
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

                <div class="col-md-6" >
                    <center class="container">
                        <div class="row container">
                            <table id="example" class="table table-bordered table-responsive table-sm" role="grid" aria-describedby="example2_info" align="center" style="width: 20%;">
                                <thead>
                                    <tr class="gastos">
                                        <th style="border: 1px solid #f4f4f4;" width="50%">{{trans('contableM.detalle')}}</th>
                                        <th style="border: 1px solid #f4f4f4;" width="50%">{{trans('contableM.valor')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="borde-celda">
                                        <td>TOTAL COMPRA</td>
                                        <td>{{number_format($total_subtotal,2,'.', '')}}</td>
                                    </tr>
                                    <tr class="borde-celda">
                                        <td>{{trans('contableM.descuento')}}</td>
                                        <td>0.00</td>
                                    </tr>
                                    <tr class="borde-celda">
                                        <td>{{trans('contableM.total')}}</td>
                                        <td>{{number_format(($total_subtotal),2,'.', '')}}</td>
                                    </tr>


                                    <tr class="borde-celda">
                                        <td>GASTOS</td>
                                        <td>{{number_format($otros_gastos["total_gastos"] ,2,'.', '')}}</td>
                                    </tr>
                                    <tr class="borde-celda">
                                        <td>{{trans('contableM.total')}}</td>
                                        <td>{{number_format(($otros_gastos["total_gastos"] + $total_subtotal) ,2,'.', '')}}</td>
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


                <!--div class="col-md-12 table-responsive" style="width: 100%;">
                    <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead style="color:black">
                            <tr>
                                <td colspan="8" class="gastos">
                                    OTROS GASTOS
                                </td>
                            </tr>
                            <tr>
                                <td class="gastos">{{trans('contableM.fecha')}}</td>
                                <td class="gastos">{{trans('contableM.proveedor')}}</td>
                                <td class="gastos">{{trans('contableM.Cuenta')}}</td>
                                <td class="gastos">{{trans('contableM.valor')}}</td>
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
                                <td class="borde" colspan="3" style="text-align: end">{{trans('contableM.total')}}</td>

                                <td class="borde"> ${{number_format($conti,2,",","")}}</td>
                            </tr>
                        </thead>
                    </table>
                </div-->


                

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
                $val->compra->proveedor_da->nombrecomercial. '-'. $val->compra->observacion,
                $val->compra->subtotal,
                ];
                @endphp
                @endforeach

                <div class="col-md-6" align="center">
                    
                    <h4>CARGA CONSOLIDADA IMPORTACIÓN</h4>
                    <table class="table  table-responsive" role="grid" aria-describedby="example2_info" width="60%" align="center">
                        <thead>
                            <tr class="nuevo">
                                <th>{{trans('contableM.fecha')}}</th>
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
                                <td class="borde">{{trans('contableM.total')}}</td>
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
</body>

</html>


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