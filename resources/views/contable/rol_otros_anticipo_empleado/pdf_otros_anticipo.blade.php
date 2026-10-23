<!DOCTYPE html>
<html lang="en">

<head>

    <title>Comprobante de Otros Anticipos</title>
    <style type="text/css">
        
    #page_pdf{
      width: 49%;
      margin: 0 0;
      float: left;
      padding-right: 20px;
      border-right: solid 1px;
    }
    
    #page_pdf2{
      width: 49%;
      float: left;
      padding-left: 20px;

    }
        
        
        #principal {
            width: 800px;
        }

        @page {
            margin-top: 261px;
            margin-bottom: 100px;
        }

        /*#footer1 { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 110px; }*/

        #footer1 {
            margin-top: 90px;
        }

        #footer2 {
            margin-top: 190px;
        }


        #page_pdf {
            width: 800px;
            /*width: 49%;*/
            /*margin: 0 0;*/
            /*float: left;*/
            padding-right: 20px;
            /*border-right: solid 1px;*/
        }

        #page_pdf2 {
            /*width: 49%;*/
            /*float: left;*/
            width: 800px;
            padding-left: 20px;

        }


        #factura_head,
        #factura_cliente,
        #factura_detalle {
            width: 100%;
            /*margin-bottom: 10px;*/
        }

        #factura_head {
            margin-top: -210px;
        }


        .info_empresa {
            width: 50%;
            text-align: center;
        }

        .separator1 {
            width: 100%;
            height: 15px;
            clear: both;
        }

        .separator {
            width: 100%;
            height: 60px;
            clear: both;
        }

        .round {
            border-radius: 10px;
            border: 1px solid #3d7ba8;
            overflow: hidden;
            padding-bottom: 15px;
        }

        .round2 {
            border-radius: 15px;
            border: 3px solid #3d7ba8;
            padding-bottom: 15px;
        }

        .h3 {
            font-family: 'BrixSansBlack';
            font-size: 8pt;
            display: block;
            background: #3d7ba8;
            color: #FFF;
            text-align: center;
            padding: 3px;
            margin-bottom: 5px;
            padding: 7px;
            font-size: 1em;
            margin-bottom: 15px;
        }

        .info_rol {
            width: 69%;
        }

        .datos_rol {
            font-size: 0.8em;
        }


        .mLabel {
            width: 20%;
            display: inline-block;
            vertical-align: top;
            font-weight: bold;
            padding-left: 15px;
            font-size: 0.9em;

        }

        .mValue {
            width: 79%;
            display: inline-block;
            vertical-align: top;
            padding-left: 40px;
            font-size: 0.9em;

        }

        .mValue3 {
            width: 79%;
            display: inline-block;
            vertical-align: top;
            padding-left: 2px;
            font-size: 0.9em;

        }

        table {
            border-collapse: collapse;
            font-size: 12pt;
            font-family: 'arial';
            width: 100%;
        }

        table tr:nth-child(odd) {}

        table td {
            padding: 2px;
        }

        table th {
            text-align: left;
            color: #3d7ba8;
            font-size: 1em;
            border-bottom: 1px solid black;
        }

        #detalle_rol tr:nth-child(even) {
            background: #ededed;
            border-radius: 10px;
            border: 1px solid #3d7ba8;
            overflow: hidden;
            padding-bottom: 15px;

        }

        * {
            font-family: 'Arial' !important;
        }

        .details_title_border_left {
            background: #888;
            border-top-left-radius: 10px;
            color: #FFF;
            padding: 10px;
            padding-left: 10px;
        }

        .details_title_border_right {
            background: #888;
            border-top-right-radius: 10px;
            color: #FFF;
            padding: 10px;
            padding-right: 3px;
        }

        .details_title {
            background: #888;
            color: #FFF;
            padding: 10px;
        }

        .totals_wrapper {
            width: 100%;
        }

        .totals_label {
            display: inline-block;
            vertical-align: top;
            width: 85%;
            text-align: right;
            font-size: 0.7em;
            font-weight: bold;
            font-family: 'Arial';
        }

        .totals_value {
            display: inline-block;
            vertical-align: top;
            width: 14%;
            text-align: right;
            font-size: 0.7em;
            font-weight: normal;
            font-family: 'Arial';
        }

        .totals_label2 {
            font-size: 0.7em;
            font-weight: bold;
            font-family: 'Arial';
        }

        /* Nuevo CSS*/
        .texto {
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 0;
            line-height: 15px;
        }


        .color_texto {
            color: #FFF;
        }

        .head-title {
            background-color: #888;
            margin-left: 0px;
            margin-right: 0px;
            height: 30px;
            line-height: 30px;
            color: #cccccc;
            text-align: center;
        }

        .dobra {
            background-color: #D4D0C8;
        }

        .bordes_padding {
            border-top: 1px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
            padding: -10px;
        }

        .bordes_padding2 {
            border-left: 1px solid black;
            border-right: 1px solid black;
            padding: -10px;
        }

        .separater {
            border: 1px solid black;
        }
    </style>
    </style>
</head>

<body>
    
    <div id="principal" style="margin-top:0px;padding-top:0px; width: 99%;">
        <div valign="top" style="border">
            <table id="factura_head" class="bordes_padding">
                <tr>
                    <td class="info_empresa">
                        <div style="text-align: center;">
                            @if(!is_null($empresa->logo_form))
                            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo_form}}" style="width:350px;height: 150px">
                            @endif
                        </div>
                        <div style="text-align: center; font-size:0.8em">
                            @if(!is_null($empresa->nombrecomercial)){{$empresa->nombrecomercial}} @endif<br />
                            @if(!is_null($empresa->id)){{$empresa->id}} @endif<br />
                            @if(!is_null($empresa->telefono1)) {{$empresa->telefono1}} @endif<br/>
                            @if(!is_null($empresa->direccion)){{$empresa->direccion}} @endif<br/>
                            <br/>
                        </div>
                    </td>
                    <td class="info_factura">
                        <div>
                            <span class="h3" style="padding:20px">COMPROBANTE DE EGRESO (OTRO ANTICIPO)</span>
                            <p style="padding-left: 10px;font-size: 20px; text-align: center;">
                                No: <strong> {{$anticip_empleado->secuencia}}</strong><br />

                                <strong>POR $***********{{$anticip_empleado->monto_anticipo}}</strong>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            <table id="factura_cliente" class="bordes_padding">
                <tr>
                    <td class="info_rol">
                        <div>
                            <div class="col-md-12">
                                <table class="datos_rol">
                                    <tr>
                                        <td>
                                            <div class="col-md-12" style="border-right: 1px solid black;">
                                                <br />
                                                <div class="row" style="padding-bottom: 0px;margin-bottom:0px ">
                                                    <div class="mLabel">
                                                        EMPLEADO:
                                                    </div>
                                                    <div class="mValue3">
                                                      {{$anticip_empleado->usuario->nombre1}} @if($anticip_empleado->usuario->nombre2!='(N/A)'){{$anticip_empleado->usuario->nombre2}}@endif {{$anticip_empleado->usuario->apellido1}} @if($anticip_empleado->usuario->apellido2!='(N/A)'){{$anticip_empleado->usuario->apellido2}}@endif  
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mLabel">
                                                        LA SUMA DE:
                                                    </div>
                                                    <div class="mValue3" id="resultado">
                                                        @include ('contable.nota_debito.conversor')
                                                        @php
                                                          $cent = $anticip_empleado->monto_anticipo - (int)($anticip_empleado->monto_anticipo);
                                                          $val = $anticip_empleado->monto_anticipo - $cent;
                                                          $cent = number_format($cent, 2);
                                                          echo convertir($val, $cent);
                                                        @endphp
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mLabel">
                                                        CONCEPTO:
                                                    </div>
                                                    <div class="mValue3">
                                                    @if(!is_null($anticip_empleado))
                                                      {{$anticip_empleado->concepto}}
                                                    @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-md-12">
                                                <br />
                                                <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                                                    <div class="mLabel">
                                                        FECHA:
                                                    </div>
                                                    <div class="mValue">
                                                        {{date("d-m-Y", strtotime($anticip_empleado->fecha_creacion))}}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mLabel">
                                                        ASIENTO:
                                                    </div>
                                                    <div class="mValue">
                                                      {{$anticip_empleado->id_asiento_cabecera}}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mLabel">
                                                        TIPO:
                                                    </div>
                                                    <div class="mValue">
                                                        EG-NOM
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="mLabel">
                                                        ESTADO:
                                                    </div>
                                                    <div class="mValue">
                                                        @if($anticip_empleado->estado == 1)
                                                          ACTIVO
                                                        @elseif($anticip_empleado->estado == 0)
                                                          INACTIVO
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <table id="factura_detail" class="bordes_padding" cellpadding="0">
                <thead>
                    <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
                            @if(($caj_banc->nombre)!=null){{$caj_banc->nombre}} @endif
                        </td>
                        <td style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
                           
                        </td>
                        <td style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
                        No. Cheque  @if(($anticip_empleado->num_cheque)!=null){{$anticip_empleado->num_cheque}} @endif
                        </td>
                        <td style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; ">
                            Fecha Ch. <strong>{{date("d-m-Y", strtotime($anticip_empleado->fecha_cheque))}}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table id="factura_detalle" style="margin-top: 20px;" class="bordes_padding" cellpadding="0">
                <thead>
                    <tr>
                        <th style="font-size: 16px; color: black;">Código</th>
                        <th style="font-size: 16px; color: black;">Cuenta</th>
                        <th style="font-size: 16px; color: black;">Div</th>
                        <th style="font-size: 16px; color: black;">Valor</th>
                        <th style="font-size: 16px; color: black;">DEBE</th>
                        <th style="font-size: 16px; color: black;">HABER</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                      $total_debe=0;
                      $total_haber=0;
                    @endphp
                    @foreach($asiento_detalle as $value)
                        @php
                        $total_debe+=$value->debe;
                        $total_haber+=$value->haber;
                        @endphp  
                    <tr>
                        <td style="font-size: 16px;">
                           {{$value->id_plan_cuenta}}
                        </td>
                        <td style="font-size: 16px;">
                           {{$value->descripcion}}
                        </td>
                        <td style="font-size: 16px;">
                          $
                        </td>
                        <td style="font-size: 16px;">
                        @php $total= $value->debe+$value->haber; @endphp 
                        {{number_format($total,2,'.','')}}
                        </td>
                        <td style="font-size: 16px;">
                            @if(($value->debe)>0) {{number_format($value->debe,2,'.','')}} @else 0.00  @endif
                        </td>
                        <td style="font-size: 16px;">
                            @if(($value->haber)>0) {{number_format($value->haber,2,'.','')}} @else 0.00  @endif
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                            &nbsp;
                        </td>
                        <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                            &nbsp;
                        </td>
                        <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
                            &nbsp;
                        </td>
                        <td class="totals_label2" style="border-top: 1px solid black; border-bottom: 1px solid black;">
                            SUMAS
                        </td>
                        <td style="font-size: 16px; font-weight: bold; border-top: 1px solid black; border-bottom: 1px solid black;">
                            {{number_format($total_debe,2,'.','')}}
                        </td>
                        <td style="font-size: 16px; font-weight: bold; border-top: 1px solid black; border-bottom: 1px solid black;">
                            {{number_format($total_haber,2,'.','')}}
                        </td>
                    </tr>
                </tbody>
            </table>
            <table id="factura_detal" class="bordes_padding2" cellpadding="0">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th style="text-align: center; font-size: 16px; height: 75px; color: black;"> <label style="top: 800px;">Recibi Conforme</label> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
                          Elaborado por {{$anticip_empleado->usuario_crea->nombre1}}  {{$anticip_empleado->usuario_crea->apellido1}}
                        </td>
                        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
                            Aprobado
                        </td>
                        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
                            Contabilizado
                        </td>
                        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; ">
                            Cédula Identidad No. : 
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="separator"></div>
        </div>

    </div>

</body>

</html>