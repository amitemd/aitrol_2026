<!DOCTYPE html>
<html lang="en">

<head>

  <title>Factura</title>
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
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .round {
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
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
      padding: 4px;


    }

    table th {
      text-align: left;
      color: #3d7ba8;
      font-size: 14px;
    }

    .datos_cliente {
      font-size: 0.8em;
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
      padding-left: 7px;
      font-size: 0.9em;
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

    .h3{
      font-family: 'BrixSansBlack';
      font-size: 12pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }
    th{
      text-transform: uppercase;
    }
    .bordes td {
      border: 1px solid black;
    }
  </style>

</head>

<body>
<div id="page_pdf">
  <table id="factura_head">
    <tr>
      <td class="info_empresa">
        <div style="text-align: center">
          <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}"  style="width:250px;height: 100px">
        </div>
        <div style="text-align: center; font-size:0.8em">
          R.U.C.: {{$fact_venta->empresa->id}}<br/>
          Nombre Comercial: {{$fact_venta->empresa->nombrecomercial}}<br/>
          Teléfono: {{$fact_venta->empresa->telefono1}}<br/>
          Dir.Matriz: {{$fact_venta->empresa->direccion}}<br/>
          <br/>
        </div>
      </td>
      <td class="info_factura">
        <div class="round">
          <span class="h3" style="padding:20px">{{trans('contableM.DETALLEDEPAQUETE')}}</span>
          <p style="padding-left: 10px;font-size: 20px;">
            Paciente :<strong> @if(!is_null($fact_venta)){{$fact_venta->paciente->nombre1}} {{$fact_venta->paciente->apellido1}}@endif</strong><br />
            Seguro:<strong> @if(!is_null($orden)){{$orden->seguro->nombre}}@endif</strong><br />
            <!--Nivel:<strong> @if(!is_null($orden->id_nivel)){{$orden->id_nivel}}@endif</strong><br />-->
            Detalle de Factura No.:<strong> @if(!is_null($fact_venta)){{$fact_venta->nro_comprobante}}@endif</strong><br />
            Procedimiento:@if(!is_null($fact_venta)){{$fact_venta->procedimientos}}@endif<br />
            Fecha:@if(!is_null($fact_venta)){{$fact_venta->fecha}}@endif<br />
          </p>
        </div>
      </td>
    </tr>
  </table>
  <div style="margin-top:50px;" class="modal-content">
    @for ($i = 0; $i < count($mergue) ; $i++)

      @foreach($mergue[$i] as $value)
        <table style="margin-top:20px;">
          <thead class="bordes">
          <tr>
            <th colspan="8" style="text-align:center; padding:10px; background-color: #3d7ba8; color:white;font-size:20px;" >
              {{$value["title"]}}
            </th>
          </tr>
          <tr>
            <th style="border: 1px solid black;">codigo</th>
            <th style="border: 1px solid black;">descripcion</th>
            <th style="border: 1px solid black;">cantidad</th>
            <th style="border: 1px solid black;">precio</th>
            <th style="border: 1px solid black;">Desc</th>
            <th style="border: 1px solid black;">iva</th>
            <th style="border: 1px solid black;">valor iva</th>
            <th style="border: 1px solid black;">total</th>
          </tr>
          </thead>
          <tbody class="bordes">
          @if(count($value["details"]) > 0)
            @php $total = 0; @endphp
            @foreach($value["details"] as $det)
              <tr>
                <td style="font-size: 15px;">{{$det["codigo"]}}</td>
                <td style="font-size: 15px;">{{$det["descripcion"]}}</td>
                <td style="font-size: 15px;">{{$det["cantidad"]}}</td>
                <td style="font-size: 15px;">{{number_format($det["precio"], 2)}}</td>
                <td style="font-size: 15px;">{{number_format($det["descuento"], 2)}}</td>
                <td style="font-size: 15px;">{{number_format($det["iva"], 2)}}</td>
                <td style="font-size: 15px;">{{number_format($det["valor_iva"], 2)}}</td>
                <td style="font-size: 15px;">{{number_format($det["total"], 2)}}</td>
              </tr>
              @php $total += $det["total"] ; @endphp
            @endforeach
            <tr>
              <td style="text-transform: uppercase; font-size: 16px;font-weight: bold; text-align: end;" colspan="7">Total {{$value["title"]}}: </td>
              <td style="font-size: 16px;font-weight: bold;" >$ {{number_format($total, 2)}}</td>
            </tr>
          @else
            <tr>
              <td style="text-transform: uppercase; font-size: 16px;font-weight: bold; text-align: end;" colspan="7">Total {{$value["title"]}}: </td>
              <td style="font-size: 16px;font-weight: bold;" >$ 0.00</td>
            </tr>
          @endif
          </tbody>
        </table>
      @endforeach
    @endfor
  </div>
  <div class="separator"></div>
</div>
</body>

</html>