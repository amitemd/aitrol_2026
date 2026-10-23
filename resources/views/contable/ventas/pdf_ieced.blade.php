<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Venta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Assistant&display=swap" rel="stylesheet">
    <style>
        #page_pdf {
            width: 100%;
            margin: 5px auto 5px auto;
            font-family: 'Assistant', sans-serif;
        }

        #factura_head,
        #factura_cliente,
        #factura_detalle {
            width: 100%;
            margin-bottom: 10px;
            font-size: 0.7em
        }

        #detalle_productos tr:nth-child(even) {
            background: #ededed;
            border-radius: 10px;
            border: 1px solid #3d7ba8;
            overflow: hidden;
            padding-bottom: 15px;

        }

        .container_client {
            display: grid;
            grid-template-columns: 0.5fr 0.5fr;
        }

        .cont {
            background-color: black !important;
        }

        #container {
            page-break-inside: initial;
        }

        #container .left {
            width: 50%;
            float: left;
            font-size: 0.7em;
        }

        #container .right {
            width: 50%;
            float: right;
            font-size: 0.7em;
        }

        #container2 .left {
            width: 50%;
            float: left;
            font-size: 0.7em;
        }

        #container2 .right {
            width: 50%;
            float: right;
            font-size: 0.7em;
        }

        #container_right .rightr {
            width: 50%;
            float: right;
        }

        #container_right .leftr {
            width: 50%;
            float: left;
        }

        #container_left .leftr {
            width: 50%;
            float: left;
        }

        #container_left .rightr {
            width: 50%;
            float: right;
        }

        .left_border {
            font-weight: bold;
            margin-top: 14px !important;
            text-transform: uppercase;

        }

        .right_border {
            font-weight: normal;
            margin-top: 14px !important;
        }

        .lf {
            text-transform: uppercase;
        }

        .border {
            border: 2px solid black !important;
            text-align: center;
        }

        .header_table {
            vertical-align: super !important;
            /* background-color: green; */
            height: 100px !important;
        }

        .details_product {
            max-height: 20px;


        }
        .padd{
            vertical-align: super !important;
            /* background-color: green; */
            height:45px !important;
            max-heigth: 50px;
        }
        .details_products {
            max-height: 100px !important;
        }
    </style>

</head>

<body>
    <div id="page_pdf">
        <table id="factura_head">
            <tr>
                <td class="info_empresa">
                    <div style="text-align: left">
                        @if($ventas->empresa->logo!=null)
                        <img src="{{base_path().'/storage/app/logo/'.$ventas->empresa->logo}}" style="width:470px;height:180px">

                    

                        @endif
                    </div>
                <td class="info_factura">
                    <div class="round" style="font-size:2.0em;text-align: center">
                        <strong> F A C T U R A</strong><br /><br />
                    </div>
                    <div class="round" style="text-align: center">
                        {{$ventas->nro_comprobante}}<br />
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><br /><br />{{$ventas->empresa->razonsocial}}</strong><br />
                    <strong class="lf"> R.U.C: </strong>{{$ventas->empresa->id}}<br />
                    <strong class="lf"> Dir. Matriz: </strong>{{$ventas->empresa->direccion}}<br />
                    <strong class="lf"> Obligado a llevar contabilidad:</strong> @if($ventas->empresa->ocontabilidad==1) SI @else NO @endif  <br />
                    <!--<strong class="lf"> Contribuyente Especial No:</strong> 18337<br />-->

                    <br />
                </td>
                <td style="margin-left: 60px!important;">
                    <strong class="lf"> número de autorizacion:</strong><br />
                    {{$ventas->nro_autorizacion}}<br />

                    <strong class="lf"> Fecha y Hora:</strong> {{date('d/m/Y H:i:s',strtotime($ventas->updated_at))}}<br />
                    <strong class="lf">Ambiente: </strong> PRODUCCION<br />
                    <strong class="lf">Emision: </strong> NORMAL<br />
                    <strong>CLAVE DE ACCESO:</strong> <br />
                    <strong>&nbsp;</strong>&nbsp; {{$ventas->nro_autorizacion}} <br />
                    <strong><img style="width: 450px; height: 30px;" src="data:image/png;base64, {{ DNS1D::getBarcodePNG('$ventas->nro_autorizacion', "C128",2,30)}}" alt="barcode" /> <br />



                </td>
            </tr>
     </table>


    <div id="content">
        <table id="factura_cliente" style="border: 2px solid black;border-left-color:#FFFFFF;border-right-color:#FFFFFF" cellpadding="0" cellpadding="0">
            <tr>
                <td class="info_cliente">
                    <div class="round">
                        <div class="col-md-12" style="padding-top:2px;padding-bottom:5px">
                            <table class="datos_cliente">
                                <tr>
                                    <td width="45%">
                                        <div class="mLabel" style="width: 700px;!important">
                                            <strong class="lf"> Razon Social: </strong>{{$ventas->cliente->nombre}}
                                        </div>
                                    </td>

                                    <td width="55%">
                                        <div class="mLabel">
                                            <strong class="lf"> RUC /CI:</strong> {{$ventas->cliente->identificacion}}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%">
                                        <div class="mLabel" style="width: 700px;!important">
                                            <strong class="lf">Fecha de Emision: </strong> {{date('d/m/Y',strtotime($ventas->fecha))}}
                                        </div>
                                    </td>

                                    <td width="60%">
                                        <div class="mLabel" style="padding-left: 220px;">

                                        </div>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <table id="factura_detalle" style="border: 2px solid black;border-top-color:#FFFFFF;border-left-color:#FFFFFF;border-right-color:#FFFFFF;padding-top:5px;padding-bottom:15px; page-break-after:auto">
            <thead style="display: table-row-group;">
                <tr>
                    <th style="font-size: 18px;">
                        <div class="details_title_border_left">CÓDIGO</div>
                    </th>
                    <th style="font-size: 18px">
                        <div class="details_title">DESCRIPCIÓN</div>
                    </th>
                    <th style="font-size: 18px">
                        <div class="details_title">CANTIDAD</div>
                    </th>
                    <th style="font-size: 18px">
                        <div class="details_title">PRECIO</div>
                    </th>
                    <th style="font-size: 18px">
                        <div class="details_title">DESCUENTO</div>
                    </th>
                    <th style="font-size: 18px" style="line-height: 180%">
                        <div class="details_title_border_right">PRECIO TOTAL</div>
                    </th>
                </tr>
            </thead>
            <tbody>

                @php
                $result= $ventas->detalles;
                $verificar=true;
                if($ventas->tipo=='VENFA-CO' || $ventas->omni==1){
                $verificar=false;
                $data= \Sis_medico\Ct_Detalle_Venta_Conglomerada::where('id_ct_ventas',$ventas->id)->get()->toArray();
                if($ventas->omni==1){
                $data= \Sis_medico\Ct_Detalle_Venta_Omni::where('id_ct_ventas',$ventas->id)->get()->toArray();
                }
                $result = array();
                $key="id_paciente";
                //result data or group by
                foreach ($data as $val) {
                //dd($val);
                if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
                } else {
                $result[""][] = $val;
                }
                }
                }
                $xconter=0;

                @endphp
                @if($verificar)
                @foreach($ventas->detalles as $x)
                <tr>
                    @php
                    $producto= Sis_medico\Ct_productos::where('codigo',$x->id_ct_productos)->first();
                    @endphp

                    <th class="@if($x->detalle!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal; text-transform: uppercase;">{{$producto->codigo}}</div>
                    </th>
                    <th class="@if($x->detalle!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{$producto->nombre}} </div>
                        @if($x->detalle!=null)
                        <div class="details_products" style="font-weight:normal"> {{$x->detalle}}</div>
                        @endif
                    </th>
                    <th class="@if($x->detalle!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{number_format(round($x->cantidad,2),2,'.','')}}</div>
                    </th>
                    <th class="@if($x->detalle!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{number_format(round($x->precio,2),2,'.','')}}</div>
                    </th>
                    <th class="@if($x->detalle!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{number_format(round($x->descuento,2),2,'.','')}}</div>
                    </th>
                    <th class="@if($x->detalle!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{number_format(round($x->extendido,2),2,'.','')}}</div>
                    </th>
                </tr>
                @endforeach
                @else
                @foreach($result as $s=>$z)
                @foreach($z as $l)
                @php
                $producto= Sis_medico\Ct_productos::where('codigo',$l['id_ct_productos'])->first();
                //dd($producto);
                @endphp
                @if(!is_null($producto))
                <tr>
                    <th class="@if($l['detalle']!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal; text-transform: uppercase;">{{$producto->codigo}}</div>
                    </th>
                    <th class="@if($l['detalle']!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{$producto->nombre}} </div>
                        <div class="details_products" style="font-weight:normal"> {{$l['detalle']}}</div>
                    </th>
                    <th class="@if($l['detalle']!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{number_format(round($l['cantidad'],2),2,'.','')}}</div>
                    </th>
                    <th class="@if($l['detalle']!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{number_format(round($l['precio'],2),2,'.','')}}</div>
                    </th>
                    <th class="@if($l['detalle']!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{number_format(round($l['descuento'],2),2,'.','')}}</div>
                    </th>
                    <th class="@if($l['detalle']!=null) header_table @else padd @endif">
                        <div class="details_product" style="font-weight:normal">{{number_format(round($l['precio'],2),2,'.','')}}</div>
                    </th>
                </tr>
                @endif
                @php
                $xconter++;
                @endphp
                @endforeach
                @endforeach
                @endif

            </tbody>

        </table>
    </div>
    <div id="footer">
        <div id="container">
            <div class="left">

                <table cellpadding="0">
                    <tbody>
                        <tr>
                            <td class="border" colspan="2">
                                <label style="font-weight: bold;">SON</label>
                                @include ('contable.nota_debito.conversor')
                                @php
                                $cent = $ventas->total_final - (int)($ventas->total_final);
                                $val = $ventas->total_final - $cent;
                                $cent = number_format($cent, 2);
                                echo convertir($val, $cent);
                                @endphp
                                <label style="font-weight: bold;">AMERICANOS </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="left_border" colspan="2">INFORMACIÓN ADICIONAL</td>

                        </tr>
                        <tr>
                            <td class="left_border">&nbsp;</td>

                        </tr>
                        @if(isset($ventas->paciente))
                        <tr>
                            <td class="left_border">PACIENTE</td>
                            <td class="right_border">{{$ventas->paciente->apellido1}} {{$ventas->paciente->apellido2}} {{$ventas->paciente->nombre1}}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="left_border">CORREO</td>
                            <td class="right_border">{{$ventas->cliente->email_representante}}</td>
                        </tr>
                       {{--  <tr>
                            <td class="left_border">CIUDAD</td>
                            <td class="right_border">{{$ventas->cliente->ciudad_representante}}</td>
                        </tr> --}}
                        <tr>
                            <td class="left_border">DIRRECION</td>
                            <td class="right_border" style='text-transform: uppercase;'>{{$ventas->cliente->direccion_representante}}</td>
                        </tr>
                        @if(isset($ventas->paciente))
                        <tr>
                            <td class="left_border">SEGURO</td>
                            <td class="right_border">{{$ventas->paciente->seguro->nombre}}</td>
                        </tr>
                        @endif
                        @if(!is_null($ventas->procedimientos))
                        <tr>
                            <td class="left_border">PROCEDIMIENTO</td>
                            <td class="right_border">{{$ventas->procedimientos}}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="left_border">FORMA PAGO</td>
                            <td class="right_border">OTROS UTILIZANDO EL SISTEMA FINANCIERO</td>
                        </tr>

                    </tbody>

                </table>



            </div>
            <div class="right">
                <table cellpadding="0" style="width: 100%; text-align: right;">
                    <tbody>
                        @php
                        $subtotal= $ventas->subtotal_12+ $ventas->subtotal_0;
                        @endphp
                        <tr>
                            <td class="left_border">&nbsp;</td>

                        </tr>
                        <tr>
                            <td class="left_border">&nbsp;</td>

                        </tr>
                        <tr>
                            <td class="left_border">Subtotal 12%</td>
                            <td class="right_border">{{$ventas->subtotal_12}}</td>
                        </tr>
                        <tr>
                            <td class="left_border">Subtotal 0%</td>
                            <td class="right_border">{{$ventas->subtotal_0}}</td>
                        </tr>
                        <tr>
                            <td class="left_border">Descuento</td>
                            <td class="right_border">{{$ventas->descuento}}</td>
                        </tr>
                        <tr>
                            <td class="left_border">Subtotal</td>
                            <td class="right_border">{{number_format(round($subtotal,2),2,'.','')}}</td>
                        </tr>

                        <tr>
                            <td class="left_border">Tarifa 12%</td>
                            <td class="right_border">{{$ventas->impuesto}}</td>
                        </tr>
                        <tr>
                            <td class="left_border">Total</td>
                            <td class="right_border">{{$ventas->total_final}}</td>
                        </tr>

                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- <table id="ADICIONAL" border="0" cellpadding="0" cellpadding="0" style="font-size: 0.7em">

        <tr>
            <th style="line-height: 180%;padding-bottom:300px;">
                <div class="details_title_border_left">Informacion Adicional</div>
                <div class="details_title_border_left">MAIL</div>
                <div class="details_title_border_left">CIUDAD</div>
                <div class="details_title_border_left">DIRECCION</div>
                <div class="details_title_border_left">FORMA PAGO</div>
            </th>
            <th style="line-height: 180%;padding-bottom: 245px;">
                <div class="details_title_border_left">&nbsp;</div>
                <div class="details_title_border_left" style="font-weight:normal;padding-right: 220px; font-size:13px;">{{$ventas->email_cliente}}</div>
                <div class="details_title_border_left" style="font-weight:normal;padding-right: 220px; font-size:13px;">{{$ventas->cliente->ciudad_representante}}</div>
                <div class="details_title_border_left" style="font-weight:normal;padding-right: 220px; font-size:13px;">{{$ventas->direccion_cliente}}</div>
                <div class="details_title_border_left" style="font-weight:normal;padding-right: 220px; font-size:13px;">OTROS UTILIZANDO EL SISTEMA FINANCIERO</div>
            </th>
            <th style="line-height: 200%;padding-left:50px; width:220px;">
                <div class="details_title_border_left">Subtotal 12%</div>
                <div class="details_title_border_left">Subtotal 0%</div>
                <div class="details_title_border_left">Subtotal no objeto de IVA</div>
                <div class="details_title_border_left">Subtotal exento de IVA</div>
                <div class="details_title_border_left">Subtotal sin impuestos</div>
                <div class="details_title_border_left">Total Descuento</div>
                <div class="details_title_border_left">ICE</div>
                <div class="details_title_border_left">IVA 12%</div>
                <div class="details_title_border_left">Valor Total</div>
            </th>
            <th style="line-height: 200%;padding-left:70px;">
                <div class="details_title_border_right" style="font-weight:normal;">{{number_format($ventas->subtotal_12,2,'.','')}}</div>
                <div class="details_title_border_right" style="font-weight:normal">{{number_format($ventas->subtotal_0,2,'.','')}}</div>
                <div class="details_title_border_right" style="font-weight:normal">{{number_format($ventas->base_imponible,2,'.','')}}</div>
                <div class="details_title_border_right" style="font-weight:normal">{{number_format($ventas->base_imponible,2,'.','')}}</div>
                <div class="details_title_border_right" style="font-weight:normal">{{number_format($ventas->descuento,2,'.','')}}</div>
                <div class="details_title_border_right" style="font-weight:normal">{{number_format($ventas->descuento,2,'.','')}}</div>
                <div class="details_title_border_right" style="font-weight:normal">0.00</div>
                <div class="details_title_border_right" style="font-weight:normal">{{number_format($ventas->impuesto,2,'.','')}}</div>
                <div class="details_title_border_right">{{number_format($ventas->total_final,2,'.','')}}</div>
            </th>


        </tr>
        <tr>
            <td>
                <div class="mLabel" style="line-height: 180%">
                    <strong> Forma de Pago </strong>
                    <p> Sin utilizacion del sistema financiero </p>
                </div>
            </td>
            <td>
                <div class="mLabel" style="line-height: 180%;padding-left:50px;padding-top:40px">
                    $11.20 - 30 dias

                </div>
            </td>

        </tr>


    </table> -->
            </div>
    </div>
    

</body>

</html>