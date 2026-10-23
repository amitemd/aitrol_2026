<!DOCTYPE html>
<html lang="en">

<head>

    <title>DEBITO COMPROBANTE</title>
    <style>
        .h2 {
            font-family: 'BrixSansBlack';
            font-size: 40px;
            display: block;
            background: #888888;
            color: #FFF;
            text-align: center;
            padding: 3px;
            margin-bottom: 5px;
            padding: 7px;
            font-size: 1em;
            margin-bottom: 15px;
        }

        #page_pdf {
            width: 100%;
            /*margin: 15px auto 10px auto;*/
            margin: 0 0;
            float: left;

        }

        .info_empresa {
            width: 50%;
            text-align: center;
        }

        .info_factura {
            width: 31%;
        }

        .round {
            border-bottom: dashed;
            overflow: hidden;
            padding-bottom: 15px;
        }

        #factura_head,
        #factura_cliente,
        #factura_detalle {
            width: 100%;
            /*margin-bottom: 10px;*/

        }

        #factura_head {
            margin-top: -50px;
        }

        .h3 {
            font-family: 'BrixSansBlack';
            font-size: 8pt;
            display: block;
            color: black;
            text-align: center;
        }

        .titulo {
            background: #eee;
            padding: 5px;
        }

        .center {
            text-align: center;
        }

        .t9 {
            font-size: 0.9rem
        }

        .p5 {
            padding: 0.5rem;
        }

        .celda {
            float: left;
            border: 1px solid #ccc;
            margin: -1px;
        }

        .th {
            text-align: center;
        }

        * {
            font-size: 18px;
        }

        table {
            border-collapse: collapse;
            font-size: 12pt;
            font-family: 'arial';
            width: 100%;
        }
        .td{
            text-align: center;
        }
    </style>

</head>

<body>

    <div id="page_pdf">
        <div style="text-align: left;margin-left:80px;">


        </div>
        <table class="table" width="100%;">
            <thead>
                <tr>
                    <td>
                        @if(!is_null($empresa->logo))
                        <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:265px;height: 120px;">
                        @endif
                    </td>
                    <td style="width:50%">
                        <span class="h3" style="padding:2px;font-size:15px !important;"><strong> {{$empresa->razonsocial}}</strong></span>
                        <span class="h3" style="padding:2px;font-size:15px !important;"><strong> {{$empresa->direccion}}</strong></span>
                    </td>
                    <td style="width:50%;">
                        <span class="h2" style="padding:20px">DEBITO NO: {{$registro->secuencia}}</span>
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table" width="100%;" style="border:1px solid black;border-bottom:none !important;">
            <thead style="text-align:center;">
                <tr>
                    <td class="td" scope="col" style="border-right:0.5px solid black !important;">
                        Concepto<br>
                        {{$registro->concepto}}

                    </td>
                    <td class="td" scope="col" style="border-right:0.5px solid black !important;">
                        Tipo <br>
                        {{$registro->tipo}}

                    </td>
                    <td class="td" scope="col" style="border-right:0.5px solid black !important;">
                        Fecha <br>
                        {{date('d/m/Y', strtotime($registro->fecha))}}
                    </td>
                    <td class="td" scope="col" style="border-right:0.5px solid black !important;">
                        Estado <br>
                        @if($registro->estado==1)Activa @else Anulada @endif
                    </td>

                </tr>
            </thead>
        </table>
        @php

        $divisas = Sis_medico\Ct_Divisas::where('id',$registro->id_divisa)->first();
        @endphp
        <table class="table" width="100%;border:1px solid back;border-bottom:none !important;">
            <thead>
                <tr>
                    <td class="td" scope="col" style="border-right:0.5px solid black !important;text-align:center">
                        Beneficiario <br>
                        {{$beneficierio->nombrecomercial}}
                    </td>
                    <td class="td" scope="col" style="border-right:0.5px solid black !important;text-align:center">
                        Cuenta <br>
                        {{$beneficierio->cuenta}}
                    </td>
                    <td class="td" scope="col" style="border-right:0.5px solid black !important;text-align:center">
                        Divisa <br>
                        {{$divisas->descripcion}}
                    </td>
                    <td class="td" scope="col" style="border-right:0.5px solid black !important;text-align:center">
                        Valor <br>
                        {{$registro->valor}}

                    </td>
            </thead>
            </tr>
        </table>
        <table class="table" width="100%;border:1px solid back;border-bottom:none !important;">
            <thead>
                <tr>
                    <td class="td" style="text-align:left;margin-left:50px !important;font-weight:none">
                        <span style="font-weight: bold;">Valor</span> <br>
                        {{$total_str}} - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                    </td>
                </tr>
            </thead>
        </table>
        <table width="100%;border:1px solid back">
            <tr style="background: #888888;color:white;width:100%">
                <th class="th" style="border-bottom:1px solid black;margin:0px;">
                    Cuenta
                </th>
                <th class="th" style="border-bottom:1px solid black">
                    Div
                </th>
                <th class="th" style="border-bottom:1px solid black">
                    Valor
                </th>
                <th class="th" style="border-bottom:1px solid black">
                    Debe
                </th>
                <th class="th" style="border-bottom:1px solid black">
                    Haber
                </th>
            </tr>
            @php
            $ct_asiento_detalle = Sis_medico\Ct_Asientos_Detalle::where('id_asiento_cabecera',$registro->id_asiento)->get();
            $suma_debe =0;
            $suma_haber =0;
            @endphp
            @foreach($ct_asiento_detalle as $value)
            @php
            $plan_cuenta = Sis_medico\Plan_Cuentas::where('id',$value->id_plan_cuenta)->first();
            $suma_debe+=$value->debe;
            $suma_haber+=$value->haber;
            @endphp
            <tr>
                <td class="td">{{$plan_cuenta->nombre}}</td>
                <td class="td">$</td>
                <td class="td">{{$registro->valor}}</td>
                <td class="td">{{$value->debe}}</td>
                <td class="td">{{$value->haber}}</td>
            </tr>
            @endforeach
            <tr style="background: #888888;color:white;font-weight:bold;text-align:center">
                <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                    Detalle de Deudas Aplicadas
                </td>
                <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                    Div
                </td>
                <td class="totals_label2" style="border-top: 1px solid black; border-bottom: 1px solid black;">
                    Saldo Ant.
                </td>
                <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                    Abono
                </td>
                <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                    Saldo final
                </td>
            </tr>
            <tr>
                <td class="td" style="font-size: 16px;padding-left: 22px; border-top: none !important; border-bottom: 1px solid black;">
                    @if(($registro->detalles)!=null) @foreach($registro->detalles as $value) @if(($value->compras)!=null) COM-FA -{{$value->compras->secuencia_f}} -Ref {{$value->compras->numero}} <br> @elseif(($value->gastos!=null)) COM-FACT {{$value->gastos->secuencia_f}} - Ref {{$value->gastos->numero}} <br> @endif @endforeach @endif
                </td>
                <td class="td" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                    @if(($registro->detalles)!=null) @foreach($registro->detalles as $value) @if(($value->compras)!=null) $ <br> @elseif(($value->gastos!=null)) $ <br> @endif @endforeach @endif
                </td>
                <td class="td" style="font-size: 16px; border-top: 1px solid black; border-bottom: 1px solid black;">
                    @if(($registro->detalles)!=null) @foreach($registro->detalles as $value) @if(($value->compras)!=null) {{number_format($value->saldo,'2','.','')}} <br> @elseif(($value->gastos!=null)) {{number_format($value->saldo,2,'.','')}} <br> @endif @endforeach @endif
                </td>
                <td class="td" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                    @if(($registro->detalles)!=null) @foreach($registro->detalles as $value) {{number_format($value->abono,'2','.','')}} <br> @endforeach @endif
                </td>
                @php $sald= $value->saldo -$value->abono; @endphp
                <td class="td" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                    @if(($registro->detalles)!=null) @foreach($registro->detalles as $value) @if(($value->compras)!=null) {{number_format($sald,'2','.','')}} <br> @elseif(($value->gastos!=null)) {{number_format($value->gastos->valor_contable,2,'.','')}} <br>@endif @endforeach @endif
                </td>
            </tr>
        </table>
        <table class="table" width="100%;border:1px solid back;">
            <tr>
                <td style="width:20%">&nbsp;</td>
                <td style="width:20%">&nbsp;</td>
                <td style="width:20%">&nbsp;</td>
                <td class="td">Sumas:</td>
                <td class="td" style="text-align: center; "> <span style="margin-left: 21px!important;">{{number_format($suma_debe,2,'.',',')}}</span> </td>
                <td class="td" style="text-align: center;"> <span style=" margin-right: 20px!important;">{{number_format($suma_haber,2,'.',',')}}</span> </td>
            </tr>

        </table>
        <table width="100%;border:1px solid back;border-top-style:none;">
            <tr style="margin-top:60px !important;">
                <th class="th" style="border-right:1px solid black;height:40px!important;">

                    <b style="display:block; margin-top:100px!important;">Notas</b>
                </th>
            </tr>

        </table>
    </div>
</body>

</html>