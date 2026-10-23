<!DOCTYPE html>
<html lang="en">

<head>

  <title>Comprobante de Egreso Masivo</title>
  <style type="text/css">


    @page {
      margin-top: 261px;
      margin-bottom: 100px;
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

    #header {
      position: absolute;
      left: 0px;
      top: 60px;
      right: 0px;
      height: 150px;
      text-align: center;
    }


  </style>



</head>

<body lang=ES-EC style="margin-top: 5px;margin-top:0px;padding-top:0px">

  <div id="header">
    <table id="factura_head" class="bordes_padding">
      <tr>
        <td class="info_empresa">
          <div style="text-align: center;">
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:250px;height: 50px">
          </div>
          <div style="text-align: center; font-size:0.8em">
            {{$empresa->nombrecomercial}}<br />
            {{$empresa->id}}<br />

            {{$empresa->direccion}}<br />
            <br />
          </div>
        </td>
        <td class="info_factura">
          <div>
            <span class="h3" style="padding:20px">Anexo del Egreso Masivo</span>
            <p style="padding-left: 10px;font-size: 20px; text-align: center;">
              No: <strong> {{$comprobante_egreso_m->secuencia}}</strong><br />

              <strong>POR $***********{{$comprobante_egreso_m->valor_pago}}</strong>
            </p>
          </div>
        </td>
      </tr>
    </table>
  </div>
  <table id="factura_detalle" style="padding-top: 30px; " cellpadding="0" class="bordes_padding4">
    <thead>
      <tr>
        <th style="font-size: 20px; height: 12px; width: 100%; border-right: 1px solid black; color: black;">{{trans('contableM.concepto')}}:</th>
        <th style="text-align: center;font-size: 16px; border-right: 1px solid black; color: black;">Tipo</th>
        <th style="text-align: center;font-size: 16px; border-right: 1px solid black;color: black;">{{trans('contableM.fecha')}}</th>
        <th style="text-align: center;font-size: 16px; border-right: 1px solid black;color: black;">{{trans('contableM.estado')}}</th>
        <th style="text-align: center;font-size: 16px; border-right: 1px solid black;color: black;">Pag.</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="totals_label3" style="text-align:left; font-size: 18px; height: 60px; width: 20%; border-right: 1px solid black; color: black; font-weight: bold;">
          @if(isset($asiento_cabecera))
          {{$asiento_cabecera->observacion}}
          @else
          {{$comprobante_egreso_m->descripcion}}
          @endif
        </td>
        <td class="totals_label3" style="text-align: center; font-size: 16px; height: 60px; width: 20%; border-right: 1px solid black; color: black;">
          ACR-EG-MA
        </td>
        <td class="totals_label3" style="text-align: center; font-size: 16px; height: 60px; width: 20%; border-right: 1px solid black; color: black;">
          {{date("d-m-Y", strtotime($comprobante_egreso_m->fecha_comprobante))}}
        </td>
        <td class="totals_label3" style="text-align: center; font-size: 16px; height: 60px; width: 20%; border-right: 1px solid black; color: black;"></td>
        <td class="totals_label3" style="text-align: center; font-size: 16px; height: 60px; width: 20%; border-right: 1px solid black; color: black;"></td>
      </tr>
    </tbody>
  </table>
  <table id="factura_detalle" class="bordes_padding" style="padding-top: 0px;" cellpadding="0">
    <thead>
      <tr>
        <th style="font-size: 22px; height: 12px; width: 65%; border-right: 1px solid black; color: black;">Son</th>
        <th style="text-align: right; font-size: 20px; height:12px; width: 1%; border-right: 1px solid black; color: black;">Valor</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="totals_label3" style="text-align:left; font-size: 16px; height: 60px; width: 20%; border-right: 1px solid black; color: black; font-weight: bold;">
          @include ('contable.nota_debito.conversor')
          @php

          $cent = $comprobante_egreso_m->valor_pago - (int)($comprobante_egreso_m->valor_pago);
          $val = $comprobante_egreso_m->valor_pago - $cent;
          $cent = number_format($cent, 2);
          echo convertir($val, $cent);
          @endphp
        </td>
        <td class="totals_label3" style="text-align: center; font-size: 16px; height: 60px; width: 20%; border-right: 1px solid black; color: black;">
          {{$comprobante_egreso_m->valor_pago}}
        </td>
      </tr>
    </tbody>
    </thead>
  </table>

  <div id="content">
    <table id="factura_detalle" class="bordes_padding" cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 20px; color: black;">{{trans('contableM.cliente')}}</th>
          <th style="font-size: 20px; color: black;">{{trans('contableM.numero')}}</th>
          <th style="font-size: 20px; color: black;">{{trans('contableM.detalle')}}</th>
          <th style="font-size: 20px; color: black;">Div</th>
          <th style="font-size: 20px; color: black;">Saldo</th>
          <th style="font-size: 20px; color: black;">Abono</th>
          <th style="font-size: 20px; color: black;">A Favor</th>
        </tr>
      </thead>
      <tbody>

        @php
        $t_saldo=0;
        $t_abono=0;
        //abono
        
        @endphp
        @foreach($comprobante_egreso_detalle as $value)
        @php
        $t_saldo+=$value->saldo_base;
        $t_abono = $value->abono + $t_abono;
        @endphp
        <tr>
          <td style="font-size: 16px;">
            @if(isset($value->proveedor)) {{$value->proveedor->razonsocial}} @endif

          </td>
          <td style="font-size: 16px;">
            @if(isset($value->compras)){{$value->compras->numero}} @endif
          </td>
          <td style="font-size: 16px;">
            {{$comprobante_egreso_m->descripcion}}
          </td>
          <td style="font-size: 16px;">
            $
          </td>
          <td style="font-size: 16px;">
            @if(($value->saldo_base)>0) {{number_format($value->saldo_base,2,'.','')}} @else 0.00 @endif
          </td>
          <td style="font-size: 16px;">
            @if(($value->abono)>0) {{number_format($value->abono,2,'.','')}} @else 0.00 @endif
          </td>
          </td>
          <td style="font-size: 16px;">
            &nbsp;
          </td>
        </tr>
        @endforeach
        <tr>
          <td style="font-size: 16px;padding-left: 22px; solid black; border-bottom: 1px solid black;">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; solid black; border-bottom: 1px solid black;">
            &nbsp;
          </td>
          <td class="totals_label2" style="font-size: 20px;padding-left: 22px;  border-bottom: 1px solid black;">
            Total Deudas:{{$t_saldo+=$value->saldo_base}}
          </td>
          <td class="totals_label2" style="font-size: 20px;padding-left: 22px; solid black; border-bottom: 1px solid black;">
            Total Abonos: {{$t_abono}}
          </td>
          <td class="totals_label2" style="font-size: 20px;padding-left: 22px; solid black; border-bottom: 1px solid black;">
            A Favor:
          </td>
          <td class="totals_label2" style="font-size: 16px;padding-left: 22px; solid black; border-bottom: 1px solid black;">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; solid black; border-bottom: 1px solid black;">
            &nbsp;
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div id="footer">
    <table id="factura_detals" style="margin-top: 20px; " cellpadding="0" class="bordes_padding4">
      <thead>
        <tr>
          <th style="text-align: center; font-size: 20px; height:10px;width: 50%; border-right: 1px solid black; color: black; font-weight: bold;"> </th>
          <th style="text-align: center; font-size: 20px; height:10px;width: 40%; border-right: 1px solid black; color: black;">
            <div class="separator"> </div> {{$comprobante_egreso_m->usuario->nombre1}} {{$comprobante_egreso_m->usuario->apellido1}}
            <label style="margin-center: 3px;"> Elaborado </label>
          </th>
          <th style="text-align: center; font-size: 20px; height:10px;width: 20%; border-right: 1px solid black; color: black; font-weight: bold;">
            <div class="separator"></div> <label style="margin-center: 3px;">Aprobado </label>
          </th>
          <th style="text-align: center; font-size: 20px; height:10px;width: 20%; border-right: 1px solid black; color: black; font-weight: bold;">
            <div class="separator"> </div> <label style="margin-left: 3px;">Recibido </label>
          </th>
        </tr>
      </thead>
      <tbody>

      </tbody>
    </table>
  </div>


</body>


</html>