<!DOCTYPE html>
<html lang="en">

<head>

  <title>SOLCITUD DE APROBACION DE PAGO</title>
  <style type="text/css">
    #principal {
      width: 800px;
    }

    @page {
      margin-top: 261px;
      margin-bottom: 100px;
    }

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
      width: 40%;
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


    .td-border {
      border: 2px solid black;
    }
  </style>
</head>





<body lang=ES-EC style="margin-top: 5px;margin-top:0px;padding-top:0px">
  <div id="principal" style="margin-top:0px;padding-top:0px; width: 100%;">
    <div valign="top" style="border">
      <table id="factura_head" class="bordes_padding">
        <tr>
          <td class="info_empresa">
            <div style="text-align: center">
              @if(!is_null($empresa->logo))
              <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:300px;height: 100px">
              @endif
            </div>
            <div style="text-align: center; font-size:0.8em">
              {{$empresa->id}}<br />
              {{$empresa->nombrecomercial}}<br />
              @if(!is_null($empresa->direccion)){{$empresa->direccion}} @endif<br />
              <br />
            </div>
          </td>
          <td class="info_factura">
            <div>
              <span class="h3" style="padding:20px">SOLICITUD DE APROBACION DE PAGO</span>
              <p style="padding-left: 10px;font-size: 20px; text-align: center;">
                No: <strong> {{$comp_egreso->secuencia}}</strong><br />

                <strong>POR $***********{{$comp_egreso->valor_pago}}</strong>
              </p>
            </div>
          </td>
        </tr>
      </table>
    </div>
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
                        <div class="mLabel">De:</div>
                        <div class="mValue3">

                          @if(($contador !=null))
                          {{$contador->nombre1}} {{$contador->nombre2}} {{$contador->apellido1}} {{$contador->apellido2}}
                          @endif
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          Para:
                        </div>
                        <div class="mValue3">
                          DR. CARLOS ROBLES MEDRANDA
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          DETALLES DE SOLICITUD:
                        </div>
                        <div class="mValue3">
                          {{$comp_egreso->descripcion}}
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          FECHA REVISION DE FLUJO:
                        </div>
                        <div class="mValue3">
                          {{$comp_egreso->fecha_revision}}
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          TIPO DE PAGO:
                        </div>
                        <div class="mValue3">
                          @if($comp_egreso->id_pago == 1) EFECTIVO
                          @elseif($comp_egreso->id_pago == 2) CHEQUE
                          @elseif($comp_egreso->id_pago == 3) DEPOSITO
                          @elseif($comp_egreso->id_pago == 4) TARJETA CREDITO
                          @elseif($comp_egreso->id_pago == 5) TRANSFERENCIA BANCARIA
                          @elseif($comp_egreso->id_pago == 6) TARJETA DEBITO
                          @elseif($comp_egreso->id_pago == 7) PENDIENTE PAGO
                          @elseif($comp_egreso->id_pago == 8) PAYPAL
                          @elseif($comp_egreso->id_pago == 9) DATALINK
                          @elseif($comp_egreso->id_pago == 10) PAGO POR APP
                          @elseif($comp_egreso->id_pago == 11) CRUCE DE CUENTA
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
                          {{trans('contableM.fecha')}}
                        </div>
                        <div class="mValue">
                          {{date("d-m-Y", strtotime($comp_egreso->fecha_comprobante))}}
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          {{trans('contableM.tipo')}}
                        </div>
                        <div class="mValue">
                          ACR-SOL
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          {{trans('contableM.estado')}}
                        </div>
                        <div class="mValue">
                          @if($comp_egreso->estado == 1)
                          ACTIVO
                          @else
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

    <table id="factura_detalle" class="bordes_padding" cellpadding="0">
      <thead>
        <tr>
          <td class="td-border" style="font-size: 15px; color: black; text-align: center;">Fecha</td>
          <td class="td-border" style="font-size: 15px; color: black; text-align: center;">Factura</td>
          <td class="td-border" style="font-size: 15px; color: black; text-align: center;">Beneficiario</td>
          <td class="td-border" style="font-size: 15px; color: black; text-align: center;">Concepto</td>
          <td class="td-border" style="font-size: 15px; color: black; text-align: center;">Monto</td>
          <td class="td-border" style="font-size: 15px; color: black; text-align: center;">Banco</td>
          <td class="td-border" style="font-size: 15px; color: black; text-align: center;">No CHEQUE/No ORDEN</td>
          <td class="td-border" style="font-size: 15px; color: black; text-align: center;">REQUIERE <br> APROBACION DE <br> DIRECTORIO</td>
        </tr>
        @if(($comp_egreso->detalles)!=null)

        @foreach($comp_egreso->detalles as $value)
        
        
        <tr>
          <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
            @if(($value->compras)!=null) {{$value->compras->fecha}} @endif
          </td>
          <td class="td-border" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
            @if(($value->compras)!=null)
            COM-FA -{{$value->compras->secuencia_f}} -Ref {{$value->compras->numero}} <br>
            @elseif(($value->gastos!=null)) COM-FACT {{$value->gastos->secuencia_f}} - Ref {{$value->gastos->numero}} <br>
            @endif
          </td>
          <td class="td-border" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
            
            {{$comp_egreso->proveedor->razonsocial}}
            
          </td>
          <td class="td-border" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">@if(isset($value->compras)) - {{$value->compras->asientoCab->observacion}} @endif</td>
          <td class="td-border" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">  @if(($comp_egreso->detalles)!=null) 
          {{number_format($value->abono,'2','.','')}} 
          <br> @endif </td>
          <td class="td-border" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;"> {{$comp_egreso->bancoa->nombre}} </td>
          <td class="td-border" style="font-size: 23px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black; text-align: center;">{{$comp_egreso->no_cheque}}</td>
          <td class="td-border" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black; text-align: center;"> @if($comp_egreso->aprobacion == 1) SI @else No @endif </td>
        </tr>
       
        @endforeach
        @endif
      </thead>
    </table>
    <table id="factura_detals" class="bordes_padding2" cellpadding="0">
      <table class="datos_rol bordes_padding2">
        <tr>
          <td>
            <div class="col-md-12" style="border-right: 1px solid black;">
              <br />
              <div class="row" style="padding-bottom: 0px;margin-bottom:0px ">
                <div class="mLabel">
                  {{trans('contableM.beneficiario')}}
                </div>
                <div class="mValue3">
                  @if(($comp_egreso->proveedor)!=null){{$comp_egreso->proveedor->razonsocial}} @endif
                </div>
              </div>
              <div class="row">
                <div class="mLabel">
                  {{trans('contableM.Lasumade')}}:
                </div>
                <div class="mValue3">
                 {{$total_str}}
                </div>
              </div>
              <div class="row">
                <div class="mLabel">
                  {{trans('contableM.concepto')}}
                </div>
                <div class="mValue3">

                  {{$comp_egreso->descripcion}}

                </div>
              </div>
            </div>
          </td>
        </tr>
      </table>
  </div>
  </table>
  <table id="factura_detail" class="bordes_padding2" cellpadding="0">
    <tbody>
      @php
      $total_debe=0;
      $total_haber=0;
      @endphp
      <tr style="background: #888888; color: white!important;">
        <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
          &nbsp;
        </td>
        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
          {{trans('contableM.DetalledeDeudasAplicadas')}}
        </td>
        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
          Div
        </td>
        <td class="totals_label2" style="border-top: 1px solid black; border-bottom: 1px solid black;">
          {{trans('contableM.SaldoAnt')}}
        </td>
        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
          {{trans('contableM.abono')}}
        </td>
        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
          {{trans('contableM.SaldoFinal')}}
        </td>
      </tr>
      @foreach($comp_egreso->detalles as $value)
      <tr>
        <td style="font-size: 16px;padding-left: 22px; ">
          &nbsp;
        </td>
        <td style="font-size: 16px;padding-left: 22px; ">
          @if(($comp_egreso->detalles)!=null)  @if(($value->compras)!=null) COM-FA -{{$value->compras->secuencia_f}} -Ref {{$value->compras->numero}} <br> @elseif(($value->gastos!=null)) COM-FACT {{$value->gastos->secuencia_f}} - Ref {{$value->gastos->numero}} <br> @endif  @endif
        </td>
        <td style="font-size: 16px;padding-left: 22px; ">
          @if(($comp_egreso->detalles)!=null)  @if(($value->compras)!=null) $ <br> @elseif(($value->gastos!=null)) $ <br> @endif  @endif
        </td>
        <td style="font-size: 16px; ">
          @if(($comp_egreso->detalles)!=null)  @if(($value->compras)!=null) {{number_format($value->saldo_base,'2','.','')}} <br> @elseif(($value->gastos!=null)) {{number_format($value->saldo_base,2,'.','')}} <br> @endif  @endif
        </td>
        <td style="font-size: 16px;padding-left: 22px; ">
          @if(($comp_egreso->detalles)!=null) 
          {{number_format($value->abono,'2','.','')}} 
          <br>  @endif
        </td>
        @php $sald= $value->saldo_base -$value->abono; @endphp
        <td style="font-size: 16px;padding-left: 22px; ">
          @if(($comp_egreso->detalles)!=null)  @if(($value->compras)!=null) {{number_format($sald,'2','.','')}} <br> @elseif(($value->gastos!=null)) {{number_format($value->gastos->valor_contable,2,'.','')}} <br>@endif  @endif
        </td>
      </tr>
      @endforeach
      <tr>
        <td style="font-size: 16px;padding-left: 22px;  border-top: 1px solid black; border-bottom: 1px solid black;">
          &nbsp;
        </td>
        <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
          &nbsp;
        </td>
        <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
          &nbsp;
        </td>
        <td class="totals_label2" style="border-top: 1px solid black; border-bottom: 1px solid black;">
          {{trans('contableM.SUMAS')}}
        </td>
        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
          {{number_format($comp_egreso->valor_pago,2,'.','')}}
        </td>
        <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
        </td>
      </tr>
    </tbody>
  </table>
  <table id="factura_detals" class="bordes_padding2" cellpadding="0">
    <thead>
      <tr>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th style="text-align: center; font-size: 16px; height: 75px; color: black; height: 150px;"> <label style="top: 900px;"> <br /> <br /> <br /> <br /> <br /> Recibi Conforme <br /> </label> </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
          {{trans('contableM.Elaboradopor')}} {{$comp_egreso->usuario->nombre1}} {{$comp_egreso->usuario->apellido1}}
        </td>
        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
          {{trans('contableM.Aprobado')}}
        </td>
        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; border-right: 1px solid black; ">
          {{trans('contableM.Contabilizado')}}
        </td>
        <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-bottom: 1px solid black; ">
          {{trans('contableM.cedula')}}
        </td>
      </tr>
    </tbody>
  </table>
  <div class="separator"></div>
  <!--<div id="footer1">
     

          <div style="float: left;font-size: 14px;width: 50%;text-align: center;">

            Av.Juan Tanca Marengo, Calle 13E NE <br> 
                Torre Médico Vitalis 1 - Mezanine 3 <br>
                Telfs.: 042109180 - 042109180 <br>
                Celular: 09993066407 - 0959777712 <br>
                iecedgye@gmail.com / www.ieced.com.ec
            
          </div>

           <div style="font-size: 14px;text-align: center;">
            Av.Juan Tanca Marengo, Calle 13E NE <br> 
                Torre Médica II - 4to piso # 408-406 <br> 
                Telfs.: 042109180 - 042109180 <br> 
                Celular: 09993066407 - 0959777712 <br> 
                iecedgye@gmail.com / www.ieced.com.ec
            
          </div>
      </div>-->
  </div>

</body>

</html>