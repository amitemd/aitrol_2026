<!DOCTYPE html>
<html lang="en">

<head>

  <title>Comprobante de Ingreso</title>
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
              @if(!is_null($empresa->logo))
                <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:250px;height: 50px">
              @else
                <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}" style="width:250px;height: 50px">
              @endif
            </div>
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
              <span class="h3" style="padding:20px">COMPROBANTE DE INGRESO No.</span>
              <p style="padding-left: 10px;font-size: 20px; text-align: center;">
                {{--<strong>@if(($comp_ingreso->cliente)!=null) {{$comp_ingreso->cliente->identificacion}} - 00 - @endif {{$comp_ingreso->secuencia}}</strong><br />--}}
                <strong>{{$comp_ingreso->secuencia}}</strong><br />

              </p>
            </div>
          </td>
        </tr>
      </table>
      <table id="factura_detalles" class="bordes_padding" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 16px; color: black; border-bottom-color: white;"></th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;"></th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;"></th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;"></th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;"></th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;"></th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;"></th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;">{{trans('contableM.tipo')}}</th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;">{{trans('contableM.fecha')}}</th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;">{{trans('contableM.asiento')}}</th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;">{{trans('contableM.estado')}}</th>
            <th style="font-size: 16px; color: black; border-bottom-color: white;"></th>
            <th style="font-size: 16px; color: black;  border-bottom-color: white;"></th>
            <th style="font-size: 16px; color: black;  border-bottom-color: white;"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="totals_label3">
              &nbsp;
            </td>
            <td class="totals_label3">
              &nbsp;
            </td>
            <td class="totals_label3">
              &nbsp;
            </td>
            <td class="totals_label3">
              &nbsp;
            </td>
            <td class="totals_label3">
              &nbsp;
            </td>
            <td class="totals_label3">
              &nbsp;
            </td>
            <td class="totals_label3">
              &nbsp;
            </td>
            <td class="totals_label3">
              CLI-IN
            </td>
            <td class="totals_label3">
              {{date("d-m-Y", strtotime($comp_ingreso->fecha))}}
            </td>
            <td class="totals_label3">
              {{$comp_ingreso->id_asiento_cabecera}}
            </td>
            <td class="totals_label3">
              @if(($comp_ingreso->estado)==1) {{trans('contableM.activo')}} @else {{trans('contableM.inactivo')}} @endif
            </td>
            <td class="totals_label3">
              &nbsp;
            </td>
            <td class="totals_label3">
              &nbsp;
            </td>
            <td class="totals_label3">
              &nbsp;
            </td>
          </tr>
        </tbody>
      </table>
      <table id="factura_cliente" class="bordes_padding5">
        <tr>
          <td class="info_rol">
            <div>
              <div class="col-md-12">
                <table class="datos_rol">
                  <tr>
                    <td>
                      <div class="col-md-12">
                        <br />
                        <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                          <div class="mLabel">
                            Codigo:
                          </div>
                          <div class="mValue3">
                            @if(($comp_ingreso->cliente)!=null){{$comp_ingreso->cliente->identificacion}} @endif
                          </div>
                        </div>
                        <div class="row">
                          <div class="mLabel">
                            Cliente
                          </div>
                          <div class="mValue3">
                            @if(($comp_ingreso->cliente)!=null){{$comp_ingreso->cliente->nombre}} @endif
                          </div>
                        </div>
                        <div class="row">
                          <div class="mLabel">
              {{trans('contableM.concepto')}}
            </div>
                          <div class="mValue3">
                            {{$comp_ingreso->observaciones}}
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

      <table id="factura_detalle" class="bordes_padding" style="padding-top: 4px;" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 16px; color: black;">{{trans('contableM.fecha')}}</th>
            <th style="font-size: 16px; color: black;">{{trans('contableM.banco')}}</th>
            <th style="font-size: 16px; color: black;">{{trans('contableM.Cuenta')}}</th>
            <th style="font-size: 16px; color: black;">{{trans('contableM.NroCheque')}}</th>
            <th style="font-size: 16px; color: black;">{{trans('contableM.Girador')}}</th>
            <th style="font-size: 16px; color: black;">{{trans('contableM.divisas')}}</th>
            <th style="font-size: 16px; color: black;">{{trans('contableM.valor')}}</th>
          </tr>
        </thead>
        <tbody>


          @foreach($comp_ingreso->pago_ingresos as $value)
          @if($value->tipo_c != "CIV")
          <tr>
            <td style="font-size: 16px;">
              @if(!is_null($value->fecha))
              {{date("d-m-Y", strtotime($value->fecha))}}
              @endif

            </td>
            <td style="font-size: 16px;">
              @if(($value->banco)!=null){{$value->banco->nombre}} @elseif(($value->tarjeta)!=null) {{$value->tarjeta->nombre}} @else EFECTIVO @endif
            </td>
            <td style="font-size: 16px;">
              {{$value->cuenta}}
            </td>
            <td style="font-size: 16px;">
              {{$value->numero}}
            </td>
            <td style="font-size: 16px;">
              {{$value->girador}}
            </td>
            <td style="font-size: 16px;">
              $
            </td>
            <td style="font-size: 16px;">
              {{$value->total}}
            </td>
          </tr>
          @endif
          @endforeach
          @if(($comp_ingreso->detalle)!='[]')

          <tr>
            <td class="totals_label2">
              {{trans('contableM.DetalledeDeudasAplicadas')}}:

            </td>
            <td class="totals_label2">
              &nbsp;
            </td>
            <td class="totals_label2">
              &nbsp;
            </td>
            <td class="totals_label2">
              &nbsp;
            </td>
            <td class="totals_label2">
              &nbsp;
            </td>
            <td class="totals_label2">
              &nbsp;
            </td>
            <td class="totals_label2">
              &nbsp;
            </td>
          </tr>
          <tr>
            <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              {{trans('contableM.detalle')}}
            </td>
            <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              {{trans('contableM.Vencimiento')}}
            </td>
            <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              {{trans('contableM.SaldoInicial')}}
            </td>
            <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              {{trans('contableM.abono')}}
            </td>
            <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              {{trans('contableM.saldo')}}
            </td>
            <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              &nbsp;
            </td>
            <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              &nbsp;
            </td>
          </tr>
          <tr>
            <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              @if(($comp_ingreso->detalle)!=null) @foreach($comp_ingreso->detalle as $value) Fact #: {{$value->ventas->nro_comprobante}}- @if(($value->ventas->procedimientos)!=null) {{$value->ventas->procedimientos}} @endif <br> @endforeach @endif
            </td>
            <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              @if(($comp_ingreso->detalle)!=null) @foreach($comp_ingreso->detalle as $value) {{date("d-m-Y", strtotime($value->fecha))}} <br> @endforeach @endif
            </td>
            <td style="font-size: 16px; border-top: 1px solid black; border-bottom: 1px solid black;">
              @if(!is_null($comp_ingreso->detalle)) @foreach($comp_ingreso->detalle as $value) @if(($value->total_factura)!=null) $ {{number_format($value->total_factura,'2','.','')}} <br> @endif @endforeach @endif
            </td>
            <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              @if(($comp_ingreso->detalle)!=null) @foreach($comp_ingreso->detalle as $value) {{number_format($value->total,'2','.','')}} <br> @endforeach @endif
            </td>
            <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              @php $total_fint=0; @endphp
              @if(($comp_ingreso->detalle)!=null) @foreach($comp_ingreso->detalle as $value) @php $total_fin= $value->total_factura-$value->total; $total_fint+= $value->total_factura-$value->total;@endphp {{number_format($total_fin,2,'.','')}} <br> @endforeach @endif
            </td>
            <td class="totals_label2" style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              &nbsp;
            </td>
            <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
              &nbsp;
            </td>
          </tr>
          @endif
        </tbody>
      </table>
      <table id="factura_detals" class="@if(($comp_ingreso->detalle)!='[]') bordes_padding3 @else bordes_padding4  @endif" style="margin-top: 20px; " cellpadding="0">
        <thead>
          <tr>
            <th style="text-align: left; font-size: 16px; height: 1px; border-right: 1px solid black; color: black; font-weight: bold;"> <label style="margin-left: 3px;">{{trans('contableM.nota')}}:</label>
              <div class="separator"></div>
            </th>
            <th style="text-align: left; font-size: 16px; height: 1px; color: black; border-right: 1px solid black; color: black;">
              <div class="separator"> </div> <label style="margin-left: 3px;">{{trans('contableM.Elaborado')}} {{$comp_ingreso->usuario->nombre1}} . {{$comp_ingreso->usuario->apellido1}} </label>
            </th>
            <th style="text-align: left; font-size: 16px; height: 1px; color: black; border-right: 1px solid black; color: black;">
              <div class="separator"></div> <label style="margin-left: 3px;">{{trans('contableM.Aprobado')}}</label>
            </th>
            <th style="text-align: left; font-size: 16px; height: 1px; color: black; "> <label style="margin-left: 3px;">{{trans('contableM.Recibido')}}</label>
              <div class="separator"> </div> <label style="margin-left: 3px;">{{trans('contableM.ruc')}}/{{trans('contableM.cedula')}}</label>
            </th>
          </tr>
        </thead>
        <tbody>

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
  </div>

</body>

</html>
