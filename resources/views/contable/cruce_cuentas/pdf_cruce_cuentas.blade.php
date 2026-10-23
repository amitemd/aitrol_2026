<!DOCTYPE html>
<html lang="en">

<head>

    <title>Comprobante de Cruce de Valores</title>
    <style type="text/css">
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
            height: 35px;
            clear: both;
        }

        .separator {
            width: 100%;
            height: 120px;
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
            font-size: 35px !important;
            display: block;
            color: black;
            text-align: center;
            padding: 3px;
            margin-bottom: 5px;
            padding: 7px;
            font-size: 1em;
            margin-bottom: 15px;
            font-weight: bold;
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

        #factura_detals {
            border-bottom: 1px;
            border-bottom-color: #FFF;
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

        .totals_label3 {
            font-size: 0.6em;
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

        .bordes_padding3 {
            border-left: 1px solid black;
            border-right: 1px solid black;
            padding: -10px;
            border-bottom: 1px solid white;
        }

        .bordes_padding4 {
            border-left: 1px solid black;
            border-right: 1px solid black;
            padding: -10px;
            border-top: 1px solid black;
            border-bottom: 1px solid white;
        }

        .bordes_padding5 {
            border-left: 1px solid black;
            border-right: 1px solid black;
            padding: -10px;


        }

        .separater {
            border: 1px solid black;
        }
    </style>


</head>


<body lang=ES-EC style="margin-top: 5px;margin-top:0px;padding-top:0px">

    <div id="principal" style="margin-top:0px;padding-top:0px; width: 99%;">
        <div valign="top" style="border">
            <table id="factura_head" class="bordes_padding">
                <tr>
                    <td class="info_empresa">
                        <div style="text-align: center;">
                            @if($empresa->logo!=null)
                            <img src="{{public_path().'/imagenes/endoscopynet_gastroquito.png'}}" style="width:250px;height: 50px">
                            @else
                            
                            <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}" style="width:250px;height: 50px">
                            @endif
                        </div><br>
                        <div style="text-align: center; font-size:0.8em">
                            {{$empresa->nombrecomercial}}<br />
                            {{$empresa->id}}<br />
                            {{$empresa->telefono1}}<br />
                            {{$empresa->direccion}}<br />
                            <br />
                        </div>
                    </td>
                    <td class="info_factura">
                        <div>
                            <span class="h3" style="padding:20px">Cruce de Cuentas <br> {{$datos->secuencia}} <br></span>
                        </div>
                    </td>
                </tr>
            </table>
            <table id="factura_detalles" class="bordes_padding" cellpadding="0">
                <thead>
                    <tr style="background: #EEEEEE;text-align:center!important">
                        <th style="font-size: 16px;width:50%;border-right: 1px solid black;color: black; border-bottom: 1px solid black;">Concepto</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Documento</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Asiento</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Tipo</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="text-align: center !important;">
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                            {{$datos->detalle}}
                        </td>
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                            {{$datos->secuencia}}
                        </td>
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                            {{$datos->id_asiento_cabecera}}
                        </td>
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                            CR-CUENTAS
                        </td>
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                           {{date('d/m/Y',strtotime($datos->fecha))}}
                        </td>
                    </tr>
                </tbody>
            </table>
            <table id="factura_detalles" class="bordes_padding" cellpadding="0" style="padding-top: 8px !important;">
                <thead>
                    <tr style="background: #EEEEEE;text-align:center!important">
                        <th style="font-size: 16px;border-right: 1px solid black;color: black; border-bottom: 1px solid black;">Código</th>
                        <th style="font-size: 16px;width:70%;color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Acreedor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="text-align: center !important;">
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                            @if(!is_null($nombre)){{$nombre->id}} @endif
                        </td>
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                            @if(!is_null($nombre)){{$nombre->nombrecomercial}} @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <table id="factura_detalles" class="bordes_padding" cellpadding="0" style="padding-top: 8px !important;">
                <thead>
                    <tr style="background: #EEEEEE;">
                        <th style="font-size: 16px; border-right: 1px solid black;color: black; border-bottom: 1px solid black;margin-left:20px !important ;">Detalle de las Deudas Aplicadas:</th>
                    </tr>
                </thead>
            </table>
            <table id="factura_detalles" class="bordes_padding" cellpadding="0" style="padding-top: 8px !important;">
                <thead>
                    <tr style="background: #EEEEEE;text-align:center!important;">
                        <th style="font-size: 16px; border-right: 1px solid black;color: black; border-bottom: 1px solid black;">Detalle</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Tipo</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Número</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Vencimiento</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Saldo Inicia</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Abono</th>
                       
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos2 as $value)
                    @php
                    //dd($value);
                    $resta = $value->total - $value->total_factura;
                    @endphp
                    <tr style="text-align: center !important;">
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                            {{$value->observaciones}}
                        </td>

                            @php
                                $tipo_cruce= "";
                            if(!is_null($compras)){
                                if($compras->tipo== 1){
                                    $tipo_cruce = "COM-FA";
                                } else
                                {
                                     $tipo_cruce = "COM-FACT";
                                }
                            }
                            @endphp



                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                          {{$tipo_cruce}}
                        </td>
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                            {{$value->secuencia_factura}}
                        </td>
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                            {{$value->fecha}}
                        </td>
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                          @if(isset($compras)) {{$compras->total_final}} @endif 
                        </td>
                        <td class="totals_label3" style=" border-bottom: 1px solid black;border-right: 1px solid black;">
                               {{$value->total}} 
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

             <div style="margin-top: 20px">

            <table id="factura_detalles" class="bordes_padding" cellpadding="0">
                <thead>
                    <tr style="background: #EEEEEE;text-align:left!important">
                        <th style="font-size: 16px;border-right: 1px solid black;color: black; border-bottom: 1px solid black;">Código</th>
                        <th style="font-size: 16px;width:50%; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Cuenta</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Div</th>
                        <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">Valor</th>
                         <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">DEBE</th>
                          <th style="font-size: 16px; color: black;border-right: 1px solid black; border-bottom: 1px solid black;">HABER</th>
                        
                    </tr>
                </thead>
                <tbody>
                   @php
                    $total_debe=0;
                    $total_haber=0;
                  @endphp
                @foreach($detalles as $value)
                   @php
                    $total_debe+=$value->debe;
                    $total_haber+=$value->haber;
                  @endphp
                <tr>
                  <td style="font-size: 16px; border-bottom: 1px solid black;border-right: 1px solid black;">{{$value->cuenta_empresa->plan}}</td>
                  <td style="font-size: 16px; border-bottom: 1px solid black;border-right: 1px solid black;">{{$value->cuenta_empresa->nombre}}</td>
                  <td style="font-size: 16px; border-bottom: 1px solid black;border-right: 1px solid black;">$</td>
                  <td style="font-size: 16px; border-bottom: 1px solid black;border-right: 1px solid black;"> @php $total= $value->debe+$value->haber; @endphp ${{number_format($total,2,'.','')}}</td>
                  <td  style="font-size: 16px; border-bottom: 1px solid black;border-right: 1px solid black ">$ {{$value->debe}}</td>
                  <td style="font-size: 16px; border-bottom: 1px solid black;border-right: 1px solid black">$ {{$value->haber}}</td>

                  </tr>
                    @endforeach
                   <tr>
                             
                        <td >&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td  class="totals_label2" style="font-size: 16px; color:black">SUMAS</td>
                        <td class="totals_label2" style="font-size: 16px; color:black ">  {{number_format($total_debe,2,'.','')}} </td>
                        <td class="totals_label2" style="font-size: 16px; color:black">{{number_format($total_haber,2,'.','')}}</td>
                    </tr>


                </tbody>
            </table>
         </div> 
            <table id="factura_detal" class="bordes_padding2" cellpadding="0">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th style="text-align: center; font-size: 16px; height: 250px; color: black;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr  style = "background: #EEEEEE">
                        <td  class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black;width:60% !important"></td>
                        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
                            Elaborado
                        </td>
                        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
                            Aprobado
                        </td>
                        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
                            Recibido
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>