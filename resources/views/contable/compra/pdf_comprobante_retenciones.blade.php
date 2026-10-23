<!DOCTYPE html>
<html lang="en">

<head>

  <title>Retencion Proveedores</title>
  <style>
    #page_pdf {
      width: 95%;
      margin: 15px auto 10px auto;
    }

    #factura_head,
    #factura_cliente,
    #factura_detalle {
      width: 100%;
      margin-bottom: 10px;

    }

    #detalle_productos tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;

    }

    #detalle_totales span {
      font-family: 'BrixSansBlack';
      text-align: right;
    }

    .logo_factura {
      width: 25%;
    }

    .info_empresa {
      width: 50%;
      text-align: center;
    }

    .info_factura {
      width: 31%;
    }

    .info_cliente {
      width: 69%;
    }

    .textright {
      padding-left: 3;
    }


    .h3 {
      font-family: 'BrixSansBlack';
      font-size: 10pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .h4 {
      font-family: 'BrixSansBlack';
      font-size: 10pt;
      display: block;
      background: #3d7ba8;
      color: #fff;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .round {
      padding-bottom: 15px;
    }

    table {
      border-collapse: collapse;
      font-size: 12pt;
      font-family: 'arial';
      width: 100%;
    }

    /*
    text-align: left;
       padding: 4px;
       background: #3d7ba8;
       color: #FFF;
    }
    */
    table tr:nth-child(odd) {
      background: #FFF;
    }

    table td {
      padding: 4px;


    }

    table th {
      text-align: left;
      /*
       padding: 4px;
       */
      /*
       background: #3d7ba8;
       color: #FFF;
       */
      color: #3d7ba8;
      font-size: 1em;
    }

    .datos_cliente {
      font-size: 0.8em;
      border-radius: 10 px;
      border: 1px solid #3d7ba8;
      border-bottom: 1px solid #3d7ba8;
    }

    .datos_cliente label {
      width: 75px;
      display: inline-block;
    }

    .lab {
      font-size: 18px;
      font-family: 'arial';
    }

    * {
      font-family: 'Arial' !important;
    }

    .mLabel {
      width: 30%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left: 15px;
      font-size: 0.89em;


    }

    .mLabel2 {
      width: 40%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left: 15px;
      font-size: 0.89em;


    }

    .mLabelf {
      width: 100%;
      display: inline-block;
      vertical-align: top;
      text-align: left;
      padding-left: 15px;
      font-size: 0.89em;


    }

    .mValue {
      width: 79%;
      display: inline-block;
      vertical-align: top;
      padding-left: 7px;
      font-size: 0.89em;
    }

    .mValue20 {
      width: 75%;
      display: inline-block;
      vertical-align: top;
      padding-left: 7px;
      font-size: 0.95em;
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

    .totals_separator {
      width: 100%;
      height: 1px;
      clear: both;
    }

    .separator {
      width: 100%;
      height: 60px;
      clear: both;
    }

    .separator2 {
      width: 100%;
      height: 680px;
      clear: both;
    }

    .details_title_border_left {
      background: #3d7ba8;
      border-top-left-radius: 10px;
      color: #FFF;
      padding: 10px;
      padding-left: 10px;
    }

    .details_title_border_right {
      background: #3d7ba8;
      border-top-right-radius: 10px;
      color: #FFF;
      padding: 10px;
      padding-right: 3px;
    }

    .details_title {
      background: #3d7ba8;
      color: #FFF;
      padding: 10px;
    }

    .hz {
      margin-left: 20px;
      font-family: 'BrixSansBlack';
      text-align: center;
      font-size: 15pt;
      color: #FF5733;
    }

    .h10 {
      font-family: 'BrixSansBlack';
      text-align: center;
      font-size: 7pt;
      color: black;
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

<body>
  <div id="page_pdf">
    <table id="factura_head">
      <tr>
        <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->


        <td class="info_empresa">

          @if($empresa1 != null)
            <div style="text-align: center">
              @if(!is_null($empresa1->logo_form))
                <img src="{{base_path() . '/storage/app/logo/' . $empresa1->logo_form}}" style="width:350px;height: 150px">
              @endif
            </div>
            <div style="text-align: center; font-size:0.8em; ">
              {{$empresa1->id}}<br />
              {{$empresa1->nombrecomercial}}<br />
              {{$empresa1->telefono1}}<br />
              {{$empresa1->direccion}}<br />
              <br />
          @else
            @endif
          </div>
        </td>
        <td class="info_factura">
          <div class="round">
            <span class="h3" style="padding:20px">COMPROBANTE DE RETENCIÓN</span>
            <span class="hz">N° {{$retenciones_all->nro_comprobante}}</span>
            <p
              style="padding-left:20px;padding-right:20px;padding-top:0px;padding-bottom:0px; text-align: center; font-size: 9pt;">

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

                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                      <div class="mLabelf">
                        <b>Proveedor:</b> &nbsp; @if($proveedor1 != null) {{$proveedor1->nombrecomercial}} @endif
                      </div>
                      <div class="mValue">
                        &nbsp;
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabelf">
                        <b> RUC:</b> &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                        @if($proveedor1 != null){{$proveedor1->id}}
                        @else {{$proveedor2->id}} @endif
                      </div>
                      <div class="mValue">
                        &nbsp;
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabelf">
                        <b> Direccion: </b> &nbsp; @if($proveedor1 != null){{$proveedor1->direccion}}@else
                        {{$proveedor2->direccion}} @endif
                      </div>
                      <div class="mValue">
                        &nbsp;
                      </div>
                    </div>
                  </td>
                  <td>
                    <br />
                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px; ">
                      <div class="mLabel2 derecha_total">
                        Fecha de Emisión:
                      </div>
                      <div class="mValue20">
                        @if(($compras != null)) {{date("d-m-Y", strtotime($compras->f_autorizacion))}} @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel2 derecha_total">
                        @if($compras != null)
                          Tipo de Comprobante de Compra:
                        @else
                          Tipo de Comprobante de Factura Contable:
                        @endif
                      </div>
                      <div class="mValue20">
                        FACTURA
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel2 derecha_total">
                        No. de Comprobante de Compra:
                      </div>
                      <div class="mValue20">
                        @if($compras != null){{$compras->numero}} @endif
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
    <span class="h4">RETENCIÓN DE IMPUESTO A LA RENTA</span>
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
          <th style="font-size: 16px">
            <div class="details_title">% Detalle de la Retención</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title_border_right">Total Retenido</div>
          </th>
        </tr>
      </thead>
      <tbody id="detalle_productos">

        @if($renta2 != null)
          @php
            $consult_r = DB::table('ct_porcentaje_retenciones')->where('id', $consulta_renta[0]->id_porcentaje)->first();
            $consult_r2 = DB::table('ct_porcentaje_retenciones')->where('id', $consulta_renta[1]->id_porcentaje)->first();
          @endphp
          <tr class="round">
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              @if($compras != null) {{date("Y", strtotime($compras->fecha))}} @endif
            </td>

            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              $ @if($compras != null) {{number_format($compras->subtotal, 2, '.', '')}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              @if($compras != null || $compras != null) {{$renta1->codigo}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              @if($compras != null || $compras != null) {{number_format($renta1->valor, 2, '.', '')}} % @endif
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              $ @if($compras != null) {{number_format($consulta_renta[0]->totales, 2, '.', '')}} @else
              {{$consulta_renta[0]->totales}} @endif
            </td>
          </tr>
          <tr class="round">
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              @if($compras != null) {{date("Y", strtotime($compras->fecha))}} @endif
            </td>

            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              $ @if($compras != null){{$compras->subtotal}} @else {{$compras->subtotal}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              @if($compras != null || $compras != null) {{$renta2->codigo}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              @if($compras != null || $compras != null || $retenciones_iva) {{$renta2->valor}} % @endif
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8; text-align: center;">
              $ @if($compras != null) {{number_format($consulta_renta[1]->totales, 2, '.', '')}} @else
              {{number_format($consulta_renta[1]->totales, 2, '.', '')}} @endif
            </td>
          </tr>

        @else
          <tr class="round">
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              @if($compras != null) {{date("Y", strtotime($compras->fecha))}} @else
              {{date("Y", strtotime($compras->fecha))}} @endif
            </td>

            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8; text-align: center;">
              $ @if($compras != null){{$compras->subtotal}} @else {{$compras->subtotal}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              @if($compras != null || $compras != null) {{$renta1->codigo}} @endif
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              @if($compras != null || $compras != null || $retenciones_iva) {{$renta1->valor}} % @endif
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              $ @if($compras != null) {{$consulta_renta[0]->totales}} @else {{$consulta_renta[0]->totales}} @endif
            </td>
          </tr>


        @endif

      </tbody>
    </table>
    <span class="h4">RETENCIÓN DE IVA</span>
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
          <th style="font-size: 16px">
            <div class="details_title">% Detalle de la Retención</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title_border_right">Total Retenido</div>
          </th>
        </tr>
      </thead>
      <tbody id="detalle_productos">
        @if(($consulta_iva) != '[]')
          @if(sizeof($consulta_iva) > 0)
            @php

              $consult_iv = DB::table('ct_porcentaje_retenciones')->where('id', $consulta_iva[0]->id_porcentaje)->first();
              //dd($consult_iv);
            @endphp
            <tr class="round">
              <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
                @if($compras != null) {{date("Y", strtotime($compras->fecha))}} @else
                {{date("Y", strtotime($compras->fecha))}} @endif
              </td>

              <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
                $ @if($compras != null){{number_format($compras->iva_total, 2, '.', '')}} @else
                {{number_format($compras->iva_total, 2, '.', '')}} @endif
              </td>
              <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
                @if($compras != null || $compras != null) {{$consult_iv->codigo}} @endif
              </td>
              <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
                @if($compras != null || $compras != null) {{$consult_iv->valor}} % @endif
              </td>
              <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
                $ @if($compras != null) {{$consulta_iva[0]->totales}} @else
                {{number_format($consulta_iva[0]->totales, 2, '.', '')}} @endif
              </td>
            </tr>

          @endif
        @else
          <tr class="round">
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              &nbsp;
            </td>
            &nbsp;
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              &nbsp;
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              &nbsp;
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              &nbsp;
            </td>
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
              &nbsp;
            </td>
          </tr>
        @endif
        @php
          $total = 0;
          if ($compras != null) {
            $total = $retenciones_all->valor_fuente + $retenciones_all->valor_iva;
          } else {

          }

        @endphp
        <tr class="round">
          <td style="background-color: white;"></td>
          <td style="background-color: white;"></td>
          <td style="background-color: white;"></td>
          <td style="background-color: white; font-size: 16px; font-weight: bold; ">TOTAL:</td>
          <td
            style="background-color: white; font-size: 16px; border-radius: 1px; border: 3px solid #3d7ba8; font-weight: bold;">
            @if($retenciones_all != null)$ {{number_format($total, 2, '.', '')}} @else
              {{number_format($total, 22, '.', '')}}
            @endif
          </td>
        </tr>
      </tbody>
    </table>
    <div class="separator2"></div>
    <div>
      <b style="font-size: 15px;"> Elaborado por {{$retenciones_all->usuario->nombre1}}
        {{$retenciones_all->usuario->apellido1}} </b>
    </div>
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

</body>

</html>