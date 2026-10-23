<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <title>Factura</title>
    <style>
        #page_pdf {
            width: 100%;
            margin: 5px auto 5px auto;
        }

        #factura_head,
        #factura_cliente,
        #factura_detalle {
            width: 100%;
            margin-bottom: 10px;
            font-size: 0.7em;
        }

        #detalle_productos tr:nth-child(even) {
            background: #ededed;
            border-radius: 10px;
            border: 1px solid #3d7ba8;
            overflow: hidden;
            padding-bottom: 15px;

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
                        @else
                        <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}" style="width:470px;height:180px">

                        @endif
                    </div>

                <td class="info_factura">
                    <div class="round" style="font-size:2.0em;text-align: center">
                        <strong> F A C T U R A</strong><br /><br />
                    </div>
                    <div class="round" style="text-align: center">
                        No. {{$ventas->nro_comprobante}}<br />
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><br /><br /> {{$ventas->empresa->nombre}} <br />{{$ventas->empresa->razonsocial}}</strong><br />
                    <strong> R.U.C: </strong>{{$ventas->id_empresa}}<br />
                    <strong> Dir. Matriz: </strong>{{$ventas->empresa->direccion}}<br />
                    <strong> Dir. Sucursal: </strong>{{$ventas->empresa->direccion}}<br />
                    <strong> Obligado a llevar contabilidad:</strong> SI<br />
                    <strong> Contribuyente Especial No:</strong> 18337<br />

                    <br />
                </td>
                <td>
                    <strong> Autorizacion Numero:</strong><br />
                    {{$ventas->nro_autorizacion}}<br />
                    <strong> Fecha Y Hora:</strong> {{$ventas->fecha}}<br />
                    <strong>Ambiente: </strong> SISTEMA<br />
                    <strong>Emision: </strong> NORMAL<br />
                    <strong>&nbsp;</strong> <br />
                    <strong>&nbsp;</strong> <br />


                </td>
            </tr>
    </div>
    </tr>
    </table>
    <table id="factura_cliente" style="border: 2px solid black;border-left-color:#FFFFFF;border-right-color:#FFFFFF" cellpadding="0" cellpadding="0">
        <tr>
            <td class="info_cliente">
                <div class="round">
                    <div class="col-md-12" style="padding-top:2px;padding-bottom:5px">
                        <table class="datos_cliente">
                            <tr>
                                <td width="10%">
                                    <div class="mLabel" style="line-height: 180%">
                                        <strong> Razon Social: </strong>{{$ventas->cliente->nombre}}
                                    </div>
                                </td>

                                <td width="15%">
                                    <div class="mLabel" style="padding-left: 430px;line-height: 180%">
                                        <strong> RUC /CI:</strong> {{$ventas->cliente->identificacion}}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="15%">
                                    <div class="mLabel" style="line-height: 180%">
                                        <strong>Fecha de Emision: </strong> {{$ventas->fecha}}
                                    </div>
                                </td>

                                <td width="15%">
                                    <div class="mLabel" style="padding-left: 400px;line-height: 180%">

                                    </div>
                                </td>
                            </tr>

                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <table id="factura_detalle" cellpadding="0" cellpadding="0" style="border: 2px solid black;border-top-color:#FFFFFF;border-left-color:#FFFFFF;border-right-color:#FFFFFF;padding-top:5px;padding-bottom:15px;">
        <thead>
            <tr>
                <th style="font-size: 18px;">
                    <div class="details_title_border_left">Cod. Principal</div>
                </th>
                <th style="font-size: 18px">
                    <div class="details_title" style="line-height: 180%">Descripcion</div>
                </th>
                <th style="font-size: 18px">
                    <div class="details_title" style="line-height: 180%">Cantidad</div>
                </th>
                <th style="font-size: 18px">
                    <div class="details_title" style="line-height: 180%">Precio unitario</div>
                </th>
                <th style="font-size: 18px">
                    <div class="details_title" style="line-height: 180%">Descuento</div>
                </th>
                <th style="font-size: 18px" style="line-height: 180%">
                    <div class="details_title_border_right">Preci Total</div>
                </th>
            </tr>

        </thead>
        <tbody style=" font-size: 14px!important;">

            @foreach($detalles as $key=>$z)
            @php
            $finproduct= DB::table('ct_productos')->where('codigo',$z->id_ct_productos)->first();
            @endphp
            <tr>    

                <th style="font-size: 14px; ">
                    <div class="details_title_border_left" style="font-weight:normal">{{$finproduct->codigo}}</div>
                </th>
                <th style="font-size: 14px; margin-top:10px!important;">
                    <div class="details_title" style="font-weight:normal">{{$finproduct->nombre}}</div>
                    <div class="details" style="margin-left: 10px; margin-top: 10px">
                        <span style="font-weight: normal; ">{{$z->detalle}}</span>
                    </div>
                </th>
                <th style="font-size: 14px; margin-top:10px!important;">
                    <div class="details_title" style="font-weight:normal">{{$z->cantidad}}</div>
                </th>
                <th style="font-size: 14px; margin-top:10px!important;">
                    <div class="details_title" style="font-weight:normal">{{number_format($z->precio,2,'.',',')}}</div>
                </th>
                <th style="font-size: 14px; margin-top:10px!important;">
                    <div class="details_title" style="font-weight:normal">0.00</div>
                </th>
                <th style="font-size: 14px; margin-top:10px!important;">
                    <div class="details_title_border_right" style="font-weight:normal">{{number_format($z->extendido,2,'.',',')}}</div>
                </th>



            </tr>
            @endforeach

        </tbody>

    </table>
    <table id="ADICIONAL" border="0" cellpadding="0" cellpadding="0" style=" font-size: 13px; padding-top: 10px;">
        <thead>
            <tr>

                <th style="line-height: 200%;width: 180px!important; max-width: 180px; ">
                    <div class="details_title_border_left">Informacion Adicional</div>
                    <div class="details_title_border_left">Direccion: </div>
                    <div class="details_title_border_left">Email: </div>
                    <div class="details_title_border_left">Paciente: </div>
                    <div class="details_title_border_left">Seguro: </div>
                    <div class="details_title_border_left">Procedimiento: </div>
                    <div class="details_title_border_left">Fecha Procedimiento: </div>
                </th>

                <th style="line-height: 200%;width: 420px!important; max-width: 420px;">
                    <div class="details_title_border_left">&nbsp;</div>
                    <div class="details_title_border_left" style="font-weight:normal;">@if($ventas->direccion_cliente!=null) {{$ventas->direccion_cliente}} @else &nbsp; @endif</div>
                    <div class="details_title_border_left" style="font-weight:normal;"> @if($ventas->email_cliente!=null) {{$ventas->email_cliente}} @else &nbsp; @endif</div>
                    <div class="details_title_border_left" style="font-weight:normal;">@if($ventas->nombres_paciente!=null){{$ventas->nombres_paciente}} @else &nbsp; @endif</div>
                    <div class="details_title_border_left" style="font-weight:normal;"> @if($ventas->paciente!=null) {{$ventas->paciente->seguro->nombre}} @else &nbsp; @endif</div>
                    <div class="details_title_border_left" style="font-weight:normal;"> @if($ventas->procedimientos!=null) {{$ventas->procedimientos}} @else &nbsp; @endif</div>
                    <div class="details_title_border_left" style="font-weight:normal;">@if($ventas->fecha_procedimiento!=null){{$ventas->fecha_procedimiento}} @else &nbsp; @endif</div>
                </th>
                <th style="line-height: 200%; width: 300px; float:right;">
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
                <th style="line-height: 200%; float:right!important; width: 100px;">

                    <div class="details_title_border_right" style="font-weight:normal">@if(($ventas->subtotal_12)>0){{number_format($ventas->subtotal_12,2,'.',',')}} @else 0.00 @endif</div>
                    <div class="details_title_border_right" style="font-weight:normal">@if(($ventas->subtotal_0)>0){{number_format($ventas->subtotal_0,2,'.',',')}} @else 0.00 @endif</div>
                    <div class="details_title_border_right" style="font-weight:normal">@if(($ventas->subtotal)>0){{number_format($ventas->subtotal,2,'.',',')}} @else 0.00 @endif</div>
                    <div class="details_title_border_right" style="font-weight:normal">@if(($ventas->subtotal_12)>0){{number_format($ventas->subtotal_12,2,'.',',')}} @else 0.00 @endif</div>
                    <div class="details_title_border_right" style="font-weight:normal">@if(($ventas->subtotal_0)>0){{number_format($ventas->subtotal_0,2,'.','')}}@else 0.00 @endif</div>
                    <div class="details_title_border_right" style="font-weight:normal">@if(($ventas->descuento)>0){{number_format($ventas->descuento,2,'.',',')}} @else 0.00 @endif</div>
                    <div class="details_title_border_right" style="font-weight:normal">0.00</div>
                    <div class="details_title_border_right" style="font-weight:normal">@if(($ventas->impuesto)>0) {{number_format($ventas->impuesto,2,'.',',')}} @else 0.00 @endif</div>
                    <div class="details_title_border_right">@if(($ventas->total_final)>0){{number_format($ventas->total_final,2,'.',',')}} @else 0.00 @endif</div>
                </th>


            </tr>

        </thead>

        <tr>
            <td>
                <div class="mLabel" style="line-height: 180%">
                    <strong> Forma de Pago </strong>
                    <p></p>
                </div>
            </td>
            <td>
                <div class="mLabel" style="line-height: 180%;padding-left:50px;padding-top:40px">


                </div>
            </td>

        </tr>


    </table>


</body>

</html>