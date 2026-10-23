<!DOCTYPE html>
<html lang="en">

<head>

  <title>Retencion Proveedores</title>
  <style type="text/css">
    #principal {
      width: 800px;
    }

    @page {
      margin-top: 250px;
      margin-bottom: 100px;
    }

    #footer1 {
      position: fixed;
      left: 0px;
      bottom: -120px;
      right: 0px;
      height: 130px;
    }

    #footer2 {
      position: fixed;
      left: 380px;
      bottom: -120px;
      right: 0px;
      height: 130px;
    }

    #footer3 {
      position: fixed;
      left: 800px;
      bottom: -120px;
      right: 0px;
      height: 130px;
    }

    #footer4 {
      position: fixed;
      left: 1250px;
      bottom: -120px;
      right: 0px;
      height: 130px;
    }



    /*#footer2 {margin-top: 115px;}
  #footer { margin-top: 115px;}*/


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
      margin-top: -80px;
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
      height: 10px;
      clear: both;
    }

    .round {
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 5px;
      margin-left: 10px;
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
      background: #888888;
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
      width: 25%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left: 15px;
      font-size: 0.8em;

    }

    .mValue {
      width: 65%;
      display: inline-block;
      vertical-align: top;
      padding-left: 10px;
      font-size: 0.8em;

    }

    table {
      border-collapse: collapse;
      font-size: 12pt;
      font-family: 'arial';
      width: 100%;
    }

    table tr:nth-child(odd) {
      background: #FFF;
    }

    table td {
      padding: 2px;
    }

    table th {
      text-align: left;
      color: #3d7ba8;
      font-size: 1em;
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
      padding: 8px;
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

    .head-title1 {
      background-color: #888;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 10px;
      color: #cccccc;
      text-align: center;
    }

    .dobra {
      background-color: #D4D0C8;
    }

    .info_empresa {
      width: 50%;
      text-align: center;
    }

    .info_factura {
      width: 60%;
      padding: 5px;
    }
  </style>


</head>
@php
$subtotal = 0;
$iva = 0;
$impuesto = 0;
$tl_sniva = 0;
$total = 0;
@endphp

<body lang=ES-EC style="margin-top: 5px;margin-top:0px;padding-top:0px">
  <div id="principal" style="margin-top:0px;padding-top:0px; width: 99%;">
    <div id="page_pdf" style="width:49%;border-right:1px solid dashed; display: inline-block;" valign="top">
      <table id="factura_head">
        <tr>
          <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->

          @php
          $datosrete = Sis_medico\Ct_configuraciones_pdf::where('id_empresa',$empresa1->id)->first();
          @endphp
          <td class="info_empresa">

            @if($empresa1!=null)
            <div style="text-align: center">
              @if(!is_null($empresa1->logo))
              <img src="{{public_path().'/imagenes/endoscopynet_gastroquito.png'}}" style="width:200px;height: 100px">
              @endif
            </div>
            <div style="text-align: center; font-size:20px; ">
              <b>{{$empresa1->nombrecomercial}}</b> <br />
              <b>{{$empresa1->id}}</b> <br />
              <b> AGENTE DE RETENCIÓN <br /></b>
              <b> @if($empresa1->id=='0993069299001')
                RESOLUCIÓN Nro. NAC-DNCRASC20-00000001 <br />
                @endif</b>
              <b> @if(strlen($empresa1->telefono1)>3)
                {{$empresa1->telefono1}}<br />
                @endif</b>
              <b> @if(strlen($empresa1->direccion)>3)
                {{$empresa1->direccion}}<br />
                @endif </b>
              <br />
              @else
              @endif
            </div>
          </td>

          <td class="info_factura">
            <div class="round" style="width:400px !important;">
              <span class="h3" style="padding:20px">COMPROBANTE DE RETENCIÓN</span>
              <span class="hz"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; S-{{$retenciones_all->nro_comprobante}}</span>
              <span class="hz"></span>
              <p style="padding-left:20px;padding-right:20px;padding-bottom:0px; text-align: center; font-size: 18px;padding-left:20px !important;width:100% !important">
                @php
                $fechamovimiento = "2018-12-05";
                $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                $mes = substr($retenciones_all->fecha, 5, -3);
                $fax= substr($retenciones_all->fecha,8);
                $anio= substr($retenciones_all->fecha,0,4);
                if( !is_null($datosrete)){
                $mes = substr($datosrete->fech_autorizacion, 5, -3);
                $fax= substr($datosrete->fech_autorizacion,8);
                $anio= substr($datosrete->fech_autorizacion,0,4);
                }else{
                $mes = substr($retenciones_all->fecha, 5, -3);
                $fax= substr($retenciones_all->fecha,8);
                $anio= substr($retenciones_all->fecha,0,4);
                }

                if ($mes
                <= 12) { } else{ echo "Solo existen 12 meses hay un error en el formato de tu fecha: " .$fechamovimiento; } @endphp @if(!is_null($datosrete)) AUT. S.R.I : {{$datosrete->autorizacion}} <br />
                @else
                AUT. S.R.I : {{$retenciones_all->autorizacion}} <br />
                @endif
                Fecha de Aut. {{$fax}} de {{$meses[$mes-1]}} del {{$anio}} <br />
                Documento Categorizado: No <br />
              </p>
            </div>
          </td>
        </tr>
      </table>
      <table id="factura_cliente">
        <tr>
          <td class="info_cliente">

            <div class="round">
              <div class="col-md-12">
                <table class="datos_cliente">
                  <tr>
                    <td>
                      <br />

                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px;padding:15px;">
                        <div class="mLabelf" style="font-size: 16px;">
                          <b>Proveedor:</b> &nbsp; <span style="font-size: 16px;"> @if($proveedor1!=null) {{$proveedor1->razonsocial}} @endif</span>
                        </div>
                        <div class="mValue">
                          &nbsp;
                        </div>
                      </div>
                      <div class="row" style="padding:15px;">
                        <div class="mLabelf" style="font-size: 16px;">
                          <b> RUC:</b> &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;<span style="font-size: 16px;"> @if($proveedor1!=null){{$proveedor1->id}} @else {{$proveedor2->id}} @endif </span>
                        </div>
                        <div class="mValue">
                          &nbsp;
                        </div>
                      </div>
                      <div class="row" style="padding:15px;">
                        <div class="mLabelf" style="font-size: 16px;">
                          <b> Dirección: </b> &nbsp; <span style="font-size: 16px;"> @if($proveedor1!=null){{$proveedor1->direccion}}@else {{$proveedor2->direccion}} @endif </span>
                        </div>
                        <div class="mValue">
                          &nbsp;
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row" style="padding-bottom: 10px;margin-bottom:-100px;margin-top:30px;">
                        <div class="mLabel2 derecha_total" style="font-size: 16px;">
                          <b>Fecha de Emisión:</b> <span style="font-size: 16px;">@if(($compras!=null)) {{date("d-m-Y", strtotime($compras->f_autorizacion))}} @endif </span>
                        </div>
                        <div class="mValue">
                          &nbsp;
                        </div>
                      </div>
                      <br />
                      <br />
                      <div class="row">
                        <div class="mLabel2 derecha_total" style="font-size: 16px;margin-top:40px;">
                          @if($compras!=null)
                          <b>Tipo de Comprobante:</b> <span style="font-size: 16px;"> FACTURA </span>@else Tipo de Comprobante de Factura Contable: @endif
                        </div>
                        <div class="mValue" style="font-size: 25px;">
                          &nbsp;
                        </div>
                      </div>
                      <br />
                      <div class="row">
                        <div class="mLabel2 derecha_total" style="font-size: 16px;">
                          <b>No. de Comprobante:</b> <span style="font-size: 16px;"> @if($compras!=null){{$compras->numero}} @endif </span>
                        </div>
                        <div class="mValue" style="font-size: 25px;">
                          &nbsp;
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
      <div style="margin: 5px !important;">
        <span class="h4" style="text-align: center; font-size: 16px; font-weight:bold;">RETENCIÓN DE IMPUESTO A LA RENTA</span>
      </div>
      <table class="table" id="factura_detalle" style="text-align: center;" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr style="background:#888888 !important ;color:white  !important;text-align:center !important">
            <th style="font-size: 16px;padding:4px;">
              Ejercicio Fiscal
            </th>
            <th style="font-size: 16px">
              Base Imponible
            </th>
            <th style="font-size: 16px">
              Código de Impuesto
            </th>
            <th style="font-size: 16px">
              % Detalle de la Retención
            </th>
            <th style="font-size: 16px">
              Total Retenido
            </th>
          </tr>
        </thead>
        <tbody id="detalle_productos">

          @foreach($detalle_retenciones as $value)
          <tr class="round">
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              @if($compras!=null) {{date("Y", strtotime($compras->fecha))}} @else {{date("Y", strtotime($compras->fecha))}} @endif
            </td>

            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8; text-align: center;">
              $ {{$value->base_imponible}}
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">

              {{$value->codigo}}
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">

              @if(isset($value->porcentajer)) {{$value->porcentajer->valor}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">

              $ {{$value->totales}}
            </td>
          </tr>

          @endforeach

        </tbody>
      </table>
      @if(($detalle_retenciones_iva)!='[]')
      <span style="text-align: center; font-size: 16px; font-weight:bold;">RETENCIÓN DE IVA</span>
      <table id="factura_detalle" style="text-align: center;" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 16px;">
              <div class="details_title_border_left">Ejercicio Fiscal</div>
            </th>
            <th style="font-size: 16px">
              <div class="details_title">Base Imponible</div>
            </th>
            <th style="font-size: 16px">
              <div class="details_title">Código de Impuesto</div>
            </th>
            <th style="font-size: 13.5px">
              <div class="details_title">% Detalle de la Retención</div>
            </th>
            <th style="font-size: 16px">
              <div class="details_title_border_right">Total Retenido</div>
            </th>
          </tr>
        </thead>
        <tbody id="detalle_productos">

          @foreach($detalle_retenciones_iva as $value)
          <tr class="round">
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              @if($compras!=null) {{date("Y", strtotime($compras->fecha))}} @else {{date("Y", strtotime($compras->fecha))}} @endif
            </td>

            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8; text-align: center;">
              $ {{$value->base_imponible}}
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              @if(!is_null($value->codigo)) {{$value->codigo}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              @if(isset($value->porcentajer)) {{$value->porcentajer->valor}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              $ {{$value->totales}}
            </td>
          </tr>

          @endforeach
          @php
          $total=0;
          if($compras!=null){
          $total= $retenciones_all->valor_fuente+$retenciones_all->valor_iva;
          }

          @endphp

          <tr class="round">
            <td style="background-color: white;"></td>
            <td style="background-color: white;"></td>
            <td style="background-color: white;"></td>
            <td style="background-color: white; font-size: 16px; font-weight: bold; ">TOTAL:</td>
            <td style="background-color: white; font-size: 16px; border-radius: 1px; border: 3px solid #3d7ba8; font-weight: bold;">@if($retenciones_all!=null)$ {{number_format($total,2,'.','')}} @else {{number_format($total,22,'.','')}} @endif</td>
          </tr>
        </tbody>
      </table>
      @else
      <table id="factura_detalle" style="text-align: center;" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 16px;"></th>
            <th style="font-size: 16px"></th>
            <th style="font-size: 16px"></th>
            <th style="font-size: 16px"></th>
            <th style="font-size: 16px"></th>
          </tr>
        </thead>
        <tbody id="detalle_productos">
          @php
          $total=0;
          if($compras!=null){
          $total= $retenciones_all->valor_fuente+$retenciones_all->valor_iva;
          }

          @endphp
          <tr class="round">
            <td style="background-color: white;">&nbsp;</td>
            <td style="background-color: white;">&nbsp;</td>
            <td style="background-color: white;">&nbsp;</td>
            <td style="background-color: white; font-size: 16px; font-weight: bold; ">TOTAL:</td>
            <td style="background-color: white; font-size: 16px; border-radius: 1px; border: 2px solid #3d7ba8; font-weight: bold;">@if($retenciones_all!=null)$ {{number_format($total,2,'.','')}} @else {{number_format($total,22,'.','')}} @endif</td>
          </tr>
        </tbody>
      </table>
      @endif
      <div class="separator"></div>
      <div style="width: 100%; font-size: 10pt;   font-family: BrixSansBlack;">
        <b>________________________</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <b>___________________________</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ORIGINAL :

      </div>
      <div style="width: 100%; font-size: 9pt;   font-family: BrixSansBlack;margin:3px;">
        <b> AGENTE DE RETENCION </b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <b style="margin:3px;"> SUJETO PASIVO RETENIDO </b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <b> SUJETO PASIVO RETENIDO </b>

      </div>
      <div class="separator2"></div>

      @if(!is_null($datosrete))
      <div style="font-size: 12px;">
        {{$datosrete->detalle}}
      </div>
      @endif

      <!--      <div class="separator"></div>
      <div class="totals_wrapper">
          <div class="totals_label">
              SUBTOTAL 0%
          </div>
          <div class="totals_value">
              
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              SUBTOTAL 12%
          </div>
          <div class="totals_value">
             
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              DESCUENTO
          </div>
          <div class="totals_value">
             
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              BASE IMPONIBLE:
          </div>
          <div class="totals_value">
              
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              TARIFA 12%
          </div>
          <div class="totals_value">
             
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              TOTAL
          </div>
          <div class="totals_value">
              
          </div>
      </div>
      <div>
        Forma de Pago: Efectivo
      </div>-->
    </div>
    <div id="page_pdf2" style="width:49%;  display: inline-block;margin-top:-1px;"  valign="top">
      <table id="factura_head">
        <tr>
          <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->

          @php
          $datosrete = Sis_medico\Ct_configuraciones_pdf::where('id_empresa',$empresa1->id)->first();
          @endphp
          <td class="info_empresa">

            @if($empresa1!=null)
            <div style="text-align: center">
              @if(!is_null($empresa1->logo))
              <img src="{{public_path().'/imagenes/endoscopynet_gastroquito.png'}}" style="width:200px;height: 100px">
              @endif
            </div>
            <div style="text-align: center; font-size:20px; ">
              <b>{{$empresa1->nombrecomercial}}</b> <br />
              <b>{{$empresa1->id}}</b> <br />
              <b> AGENTE DE RETENCIÓN <br /></b>
              <b> @if($empresa1->id=='0993069299001')
                RESOLUCIÓN Nro. NAC-DNCRASC20-00000001 <br />
                @endif</b>
              <b> @if(strlen($empresa1->telefono1)>3)
                {{$empresa1->telefono1}}<br />
                @endif</b>
              <b> @if(strlen($empresa1->direccion)>3)
                {{$empresa1->direccion}}<br />
                @endif </b>
              <br />
              @else
              @endif
            </div>
          </td>

          <td class="info_factura">
            <div class="round" style="width:400px !important;">
              <span class="h3" style="padding:20px">COMPROBANTE DE RETENCIÓN</span>
              <span class="hz"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; S-{{$retenciones_all->nro_comprobante}}</span>
              <span class="hz"></span>
              <p style="padding-left:20px;padding-right:20px;padding-bottom:0px; text-align: center; font-size: 18px;padding-left:20px !important;width:100% !important">
                @php
                $fechamovimiento = "2018-12-05";
                $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                $mes = substr($retenciones_all->fecha, 5, -3);
                $fax= substr($retenciones_all->fecha,8);
                $anio= substr($retenciones_all->fecha,0,4);
                if( !is_null($datosrete)){
                $mes = substr($datosrete->fech_autorizacion, 5, -3);
                $fax= substr($datosrete->fech_autorizacion,8);
                $anio= substr($datosrete->fech_autorizacion,0,4);
                }else{
                $mes = substr($retenciones_all->fecha, 5, -3);
                $fax= substr($retenciones_all->fecha,8);
                $anio= substr($retenciones_all->fecha,0,4);
                }

                if ($mes
                <= 12) { } else{ echo "Solo existen 12 meses hay un error en el formato de tu fecha: " .$fechamovimiento; } @endphp @if(!is_null($datosrete)) AUT. S.R.I : {{$datosrete->autorizacion}} <br />
                @else
                AUT. S.R.I : {{$retenciones_all->autorizacion}} <br />
                @endif
                Fecha de Aut. {{$fax}} de {{$meses[$mes-1]}} del {{$anio}} <br />
                Documento Categorizado: No <br />
              </p>
            </div>
          </td>
        </tr>
      </table>
      <table id="factura_cliente">
        <tr>
          <td class="info_cliente">

            <div class="round">
              <div class="col-md-12">
                <table class="datos_cliente">
                  <tr>
                    <td>
                      <br />

                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px;padding:15px;">
                        <div class="mLabelf" style="font-size: 16pxpx;">
                          <b>Proveedor:</b> &nbsp; <span style="font-size: 16px;"> @if($proveedor1!=null) {{$proveedor1->razonsocial}} @endif</span>
                        </div>
                        <div class="mValue">
                          &nbsp;
                        </div>
                      </div>
                      <div class="row" style="padding:15px;">
                        <div class="mLabelf" style="font-size: 16px;">
                          <b> RUC:</b> &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;<span style="font-size: 16px;"> @if($proveedor1!=null){{$proveedor1->id}} @else {{$proveedor2->id}} @endif </span>
                        </div>
                        <div class="mValue">
                          &nbsp;
                        </div>
                      </div>
                      <div class="row" style="padding:15px;">
                        <div class="mLabelf" style="font-size: 16px;">
                          <b> Dirección: </b> &nbsp; <span style="font-size: 16px;"> @if($proveedor1!=null){{$proveedor1->direccion}}@else {{$proveedor2->direccion}} @endif </span>
                        </div>
                        <div class="mValue">
                          &nbsp;
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row" style="padding-bottom: 10px;margin-bottom:-100px;margin-top:30px;">
                        <div class="mLabel2 derecha_total" style="font-size: 16px;">
                          <b>Fecha de Emisión:</b> <span style="font-size: 16px;">@if(($compras!=null)) {{date("d-m-Y", strtotime($compras->f_autorizacion))}} @endif </span>
                        </div>
                        <div class="mValue">
                          &nbsp;
                        </div>
                      </div>
                      <br />
                      <br />
                      <div class="row">
                        <div class="mLabel2 derecha_total" style="font-size: 16px;margin-top:40px;">
                          @if($compras!=null)
                          <b>Tipo de Comprobante:</b> <span style="font-size: 16px;"> FACTURA </span>@else Tipo de Comprobante de Factura Contable: @endif
                        </div>
                        <div class="mValue" style="font-size: 25px;">
                          &nbsp;
                        </div>
                      </div>
                      <br />
                      <div class="row">
                        <div class="mLabel2 derecha_total" style="font-size: 16px;">
                          <b>No. de Comprobante:</b> <span style="font-size: 16px;"> @if($compras!=null){{$compras->numero}} @endif </span>
                        </div>
                        <div class="mValue" style="font-size: 25px;">
                          &nbsp;
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
      <div style="margin: 5px !important;">
        <span class="h4" style="text-align: center; font-size: 16px; font-weight:bold;">RETENCIÓN DE IMPUESTO A LA RENTA</span>
      </div>
      <table class="table" id="factura_detalle" style="text-align: center;" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr style="background:#888888 !important ;color:white  !important;text-align:center !important">
            <th style="font-size: 16px;padding:4px;">
              Ejercicio Fiscal
            </th>
            <th style="font-size: 16px">
              Base Imponible
            </th>
            <th style="font-size: 16px">
              Código de Impuesto
            </th>
            <th style="font-size: 16px">
              % Detalle de la Retención
            </th>
            <th style="font-size: 16px">
              Total Retenido
            </th>
          </tr>
        </thead>
        <tbody id="detalle_productos">

          @foreach($detalle_retenciones as $value)
          <tr class="round">
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              @if($compras!=null) {{date("Y", strtotime($compras->fecha))}} @else {{date("Y", strtotime($compras->fecha))}} @endif
            </td>

            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8; text-align: center;">
              $ {{$value->base_imponible}}
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">

              {{$value->codigo}}
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">

              @if(isset($value->porcentajer)) {{$value->porcentajer->valor}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">

              $ {{$value->totales}}
            </td>
          </tr>

          @endforeach

        </tbody>
      </table>
      @if(($detalle_retenciones_iva)!='[]')
      <span style="text-align: center; font-size: 16px; font-weight:bold;">RETENCIÓN DE IVA</span>
      <table id="factura_detalle" style="text-align: center;" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 16px;">
              <div class="details_title_border_left">Ejercicio Fiscal</div>
            </th>
            <th style="font-size: 16px">
              <div class="details_title">Base Imponible</div>
            </th>
            <th style="font-size: 16px">
              <div class="details_title">Código de Impuesto</div>
            </th>
            <th style="font-size: 13.5px">
              <div class="details_title">% Detalle de la Retención</div>
            </th>
            <th style="font-size: 16px">
              <div class="details_title_border_right">Total Retenido</div>
            </th>
          </tr>
        </thead>
        <tbody id="detalle_productos">

          @foreach($detalle_retenciones_iva as $value)
          <tr class="round">
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              @if($compras!=null) {{date("Y", strtotime($compras->fecha))}} @else {{date("Y", strtotime($compras->fecha))}} @endif
            </td>

            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8; text-align: center;">
              $ {{$value->base_imponible}}
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              @if(!is_null($value->codigo)) {{$value->codigo}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              @if(isset($value->porcentajer)) {{$value->porcentajer->valor}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 2px; border: 2px solid #3d7ba8;">
              $ {{$value->totales}}
            </td>
          </tr>

          @endforeach
          @php
          $total=0;
          if($compras!=null){
          $total= $retenciones_all->valor_fuente+$retenciones_all->valor_iva;
          }

          @endphp

          <tr class="round">
            <td style="background-color: white;"></td>
            <td style="background-color: white;"></td>
            <td style="background-color: white;"></td>
            <td style="background-color: white; font-size: 16px; font-weight: bold; ">TOTAL:</td>
            <td style="background-color: white; font-size: 16px; border-radius: 1px; border: 3px solid #3d7ba8; font-weight: bold;">@if($retenciones_all!=null)$ {{number_format($total,2,'.','')}} @else {{number_format($total,22,'.','')}} @endif</td>
          </tr>
        </tbody>
      </table>
      @else
      <table id="factura_detalle" style="text-align: center;" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 16px;"></th>
            <th style="font-size: 16px"></th>
            <th style="font-size: 16px"></th>
            <th style="font-size: 16px"></th>
            <th style="font-size: 16px"></th>
          </tr>
        </thead>
        <tbody id="detalle_productos">
          @php
          $total=0;
          if($compras!=null){
          $total= $retenciones_all->valor_fuente+$retenciones_all->valor_iva;
          }

          @endphp
          <tr class="round">
            <td style="background-color: white;">&nbsp;</td>
            <td style="background-color: white;">&nbsp;</td>
            <td style="background-color: white;">&nbsp;</td>
            <td style="background-color: white; font-size: 16px; font-weight: bold; ">TOTAL:</td>
            <td style="background-color: white; font-size: 16px; border-radius: 1px; border: 2px solid #3d7ba8; font-weight: bold;">@if($retenciones_all!=null)$ {{number_format($total,2,'.','')}} @else {{number_format($total,22,'.','')}} @endif</td>
          </tr>
        </tbody>
      </table>
      @endif
      <div class="separator"></div>
      <div style="width: 100%; font-size: 10pt;   font-family: BrixSansBlack;">
        <b>________________________</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <b>___________________________</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ORIGINAL :

      </div>
      <div style="width: 100%; font-size: 9pt;   font-family: BrixSansBlack;margin:3px;">
        <b> AGENTE DE RETENCION </b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <b style="margin:3px;"> SUJETO PASIVO RETENIDO </b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <b> SUJETO PASIVO RETENIDO </b>

      </div>
      <div class="separator2"></div>

      @if(!is_null($datosrete))
      <div style="font-size: 12px;">
        {{$datosrete->detalle}}
      </div>
      @endif

      <!--      <div class="separator"></div>
      <div class="totals_wrapper">
          <div class="totals_label">
              SUBTOTAL 0%
          </div>
          <div class="totals_value">
              
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              SUBTOTAL 12%
          </div>
          <div class="totals_value">
             
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              DESCUENTO
          </div>
          <div class="totals_value">
             
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              BASE IMPONIBLE:
          </div>
          <div class="totals_value">
              
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              TARIFA 12%
          </div>
          <div class="totals_value">
             
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              TOTAL
          </div>
          <div class="totals_value">
              
          </div>
      </div>
      <div>
        Forma de Pago: Efectivo
      </div>-->
    </div>
  </div>
</body>

</html>