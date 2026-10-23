<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Assistant&display=swap" rel="stylesheet">
  <title>Recibo de Cobro</title>
  <style>
    #page_pdf {
      max-width: 50%;
      /*margin: 15px auto 10px auto;*/
      margin: 0 0;
      float: left;
      padding-right: 20px;
      border-right: solid 1px;
      font-family: 'Assistant', sans-serif!important;
    }

    #page_pdf2 {
      max-width: 50%;
      /*margin: 15px auto 10px auto;*/
      float: left;
      padding-left: 20px;
      font-family: 'Assistant', sans-serif!important;

    }

    #factura_head,
    #factura_cliente,
    #factura_detalle {
      width: 100%;
      font-size: 15px!important;
      /*margin-bottom: 10px;*/

    }

    #factura_head {
      margin-top: -50px;
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
      font-size: 1em;
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
      /*width:79%;*/
      width: 50%;
      display: inline-block;
      vertical-align: top;
      /*padding-left:7px;*/
      padding: 0;
      font-size: 0.8em;
    }

    .totals_wrapper {
      width: 100%;
      font-size: 17px!important;
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
      height: 20px;
      clear: both;
    }

    .separator2 {
      width: 100%;
      height: 20px;

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

    .enlace_desactivado {
      pointer-events: none;
      cursor: default;
    }

    .row {
      padding: 0;
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
  @php 
     $detallesventa= DB::table('ct_orden_venta_detalle')->where('id_orden',$fact_venta->id)->get();
    @endphp
    <table id="factura_head">
      <tr>
        <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->
        @if($fact_venta->id_empresa == '0992704152001')

        <td class="info_empresa">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/'.$fact_venta->empresa->logo}}" style="width:250px;height: 100px">
          </div>
          <div style="text-align: center; font-size:0.8em">
            R.U.C.: {{$fact_venta->empresa->id}}<br />
            Nombre Comercial: {{$fact_venta->empresa->nombrecomercial}}<br />
            Teléfono: {{$fact_venta->empresa->telefono1}}<br />
            Dir.Matriz: {{$fact_venta->empresa->direccion}}<br />
            <br />
          </div>
        </td>
        <td class="info_factura">
          <div class="round">
            <span class="h3" style="padding:20px">RECIBO DE COBRO</span>
            <p style="padding-left: 10px;font-size: 20px;">
              No. Orden:<strong> {{$fact_venta->id}}</strong><br />
              Fecha Emisión:<strong> {{$fact_venta->fecha_emision}}</strong><br />
              Fecha Cita:<strong> {{substr($fact_venta->agenda->fechaini,0,10)}}</strong>
            </p>
          </div>
        </td>
        @endif
      </tr>
    </table>
    <table id="factura_cliente">
      <tr>
        <td class="info_cliente">
          <div class="round">
            <div class="col-md-12">
            <table class="datos_cliente">
                <tr>
                  @php 
                    $ct=DB::table('ct_clientes')->where('identificacion',$fact_venta->identificacion)->first();
                    //dd($ct);
                  @endphp
                  <td style="font-weight: bold; font-size: 0.9em;">CLIENTE:</td>
                  <td  style=" font-size: 0.8em;">@if(!is_null($fact_venta->razon_social)){{trim($fact_venta->razon_social)}} @else @if(!is_null($ct)) {{$ct->nombre}} @endif @endif</td>
                  <td style="font-weight: bold;">PACIENTE:</td>
                  <td  style=" font-size: 0.8em;">
                    {{$fact_venta->agenda->paciente->apellido1}} @if($fact_venta->agenda->paciente->apellido2!='(N/A)'){{$fact_venta->agenda->paciente->apellido2}}@endif {{$fact_venta->agenda->paciente->nombre1}} @if($fact_venta->agenda->paciente->nombre2!='(N/A)'){{$fact_venta->agenda->paciente->nombre2}}@endif
                  </td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    DIRECCION:
                  </td>
                  <td style=" font-size: 0.8em;">
                    {{$fact_venta->direccion}}
                  </td>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    SEGURO:
                  </td>
                  <td  style=" font-size: 0.8em;">
                    {{$fact_venta->seguro->nombre}}
                  </td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    MAIL:
                  </td>
                  <td>
                    <span class="enlace_desactivado"  style=" font-size: 0.8em;">{{$fact_venta->email}}</span>
                  </td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    @if($fact_venta->tipo_identificacion == 5) C.I. @elseif($fact_venta->tipo_identificacion == 4) R.U.C. @elseif($fact_venta->tipo_identificacion == 6) PASAPORTE @elseif($fact_venta->tipo_identificacion == 8) CEDULA EXTRANJERA @endif:
                  </td>
                  <td  style=" font-size: 0.8em;">
                    {{$fact_venta->identificacion}}
                  </td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    TELÉFONO:
                  </td>
                  <td  style=" font-size: 0.8em;">
                    {{$fact_venta->telefono}}
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
      </tr>
    </table>
    <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 16px">
            <div class="details_title">DESCRIPCIÓN</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title">CANTIDAD</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title">PRECIO</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title_border_right">P.NETO</div>
          </th>
        </tr>
      </thead>
      <tbody id="detalle_productos">
        @foreach ($detallesventa as $value)
        <tr class="round">
          <td style="font-size: 16px">
            @php
            $pro= DB::table('ct_productos')->where('codigo',$value->cod_prod)->first();
            @endphp
            @if(!is_null($pro))
            {{$pro->nombre}}
            @else
            {{$value->descripcion}}
            @endif
          </td>
          <td style="font-size: 16px;">
            {{$value->cantidad}}
          </td>
         
          <td style="font-size: 16px;">
            {{$value->precio}}
          </td>
          <td style="font-size: 16px;">
            {{$value->total}}
          </td>
         
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="separator"></div>
    <div class="totals_wrapper">
      <div class="totals_label">
        SUBTOTAL 0%
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->subtotal_0))
        {{sprintf("%.2f",$fact_venta->subtotal_0)}}
        @endif
      </div>
      <div class="totals_separator"></div>
      <div class="totals_label">
        SUBTOTAL 12%
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->subtotal_12))
        {{sprintf("%.2f", $fact_venta->subtotal_12)}}
        @endif
      </div>
      <div class="totals_separator"></div>
      <div class="totals_label">
        DESCUENTO
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->descuento))
        {{sprintf("%.2f",$fact_venta->descuento)}}
        @else
        0.00
        @endif
      </div>
      <div class="totals_separator"></div>
      <div class="totals_label">
        TARIFA 12%
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->iva))
        {{sprintf("%.2f",$fact_venta->iva)}}
        @endif
      </div>
      <div class="totals_separator"></div>
      <div class="totals_label">
        TOTAL
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->total))
        {{sprintf("%.2f",$fact_venta->total)}}
        @endif
      </div>
    </div>
    <div class="separator"></div>
    <div>
      <!--FormaS de Pago-->
      @if($ct_for_pag !='[]')
      <span class="h3">FORMAS DE PAGO</span>
      <table id="form_pag" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 15px">
              <div class="details_title_border_left">TIPO</div>
            </th>
            <th style="font-size: 15px">
              <div class="details_title_border_right">VALOR</div>
            </th>
          </tr>
        </thead>
        <tbody id="detalle_pago">
          @foreach ($ct_for_pag as $value)
          <tr class="round">
            <td style="font-size: 16px">
              {{$value->metodo->nombre}} @if($value->tipo_tarjeta!=null) - {{$value->tarjeta->nombre}} @endif @if($value->banco!=null) - {{$value->ct_banco->nombre}} @endif {{$value->numero}}
            </td>
            <td style="font-size: 16px">
              @if(!is_null($value->valor))
              {{sprintf("%.2f",$value->valor)}}
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>
    <div class="separator">

    </div>
    <div class="col-md-12">
      <span style="font-size: 18px;"><br>Observacion: {{$fact_venta->observacion}}  Nro. Oda: {{$fact_venta->numero_oda}} @if($fact_venta->valor_oda!='0')Valor Oda: $ {{$fact_venta->valor_oda}}@endif</span>
    </div>
    <div class="col-md-12">
      <br>
      <span style="font-size: 18px;border-top: solid 1px;"><br>Recibi Conforme &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    </div>
    <div class="col-md-12">
      <span style="font-size: 18px;"><br>Elaborador Por: {{$fact_venta->usercrea->nombre1}} {{$fact_venta->usercrea->apellido1}}</span>
    </div>

  </div>

  <div id="page_pdf2" style="float: left;">
    <table id="factura_head">
      <tr>
        <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->
        @if($fact_venta->id_empresa == '0992704152001')

        <td class="info_empresa">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/'.$fact_venta->empresa->logo_form}}" style="width:250px;height: 100px">
          </div>
          <div style="text-align: center; font-size:0.8em">
            R.U.C.: {{$fact_venta->empresa->id}}<br />
            Nombre Comercial: {{$fact_venta->empresa->nombrecomercial}}<br />
            Teléfono: {{$fact_venta->empresa->telefono1}}<br />
            Dir.Matriz: {{$fact_venta->empresa->direccion}}<br />
            <br />
          </div>
        </td>
        <td class="info_factura">
          <div class="round">
            <span class="h3" style="padding:20px">RECIBO DE COBRO</span>
            <p style="padding-left: 10px;font-size: 20px;">
              No. Orden:<strong> {{$fact_venta->id}}</strong><br />
              Fecha Emisión:<strong> {{$fact_venta->fecha_emision}}</strong><br />
              Fecha Cita:<strong> {{substr($fact_venta->agenda->fechaini,0,10)}}</strong>
            </p>
          </div>
        </td>
        @endif
      </tr>
    </table>
    <table id="factura_cliente">
      <tr>
        <td class="info_cliente">
          <div class="round">
            <div class="col-md-12">
              <table class="datos_cliente">
                <tr>
                  <td style="font-weight: bold; font-size: 0.9em;">CLIENTE:</td>
                  <td  style=" font-size: 0.8em;">@if(!is_null($fact_venta->razon_social)){{trim($fact_venta->razon_social)}} @else @if(!is_null($ct)) {{$ct->nombre}} @endif @endif</td>
                  <td style="font-weight: bold;">PACIENTE:</td>
                  <td  style=" font-size: 0.8em;">
                    {{$fact_venta->agenda->paciente->apellido1}} @if($fact_venta->agenda->paciente->apellido2!='(N/A)'){{$fact_venta->agenda->paciente->apellido2}}@endif {{$fact_venta->agenda->paciente->nombre1}} @if($fact_venta->agenda->paciente->nombre2!='(N/A)'){{$fact_venta->agenda->paciente->nombre2}}@endif
                  </td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    DIRECCION:
                  </td>
                  <td style=" font-size: 0.8em;">
                    {{$fact_venta->direccion}}
                  </td>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    SEGURO:
                  </td>
                  <td  style=" font-size: 0.8em;">
                    {{$fact_venta->seguro->nombre}}
                  </td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    MAIL:
                  </td>
                  <td>
                    <span class="enlace_desactivado"  style=" font-size: 0.8em;">{{$fact_venta->email}}</span>
                  </td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    @if($fact_venta->tipo_identificacion == 5) C.I. @elseif($fact_venta->tipo_identificacion == 4) R.U.C. @elseif($fact_venta->tipo_identificacion == 6) PASAPORTE @elseif($fact_venta->tipo_identificacion == 8) CEDULA EXTRANJERA @endif:
                  </td>
                  <td  style=" font-size: 0.8em;">
                    {{$fact_venta->identificacion}}
                  </td>
                </tr>
                <tr>
                  <td style="font-weight: bold; font-size: 0.9em;">
                    TELÉFONO:
                  </td>
                  <td  style=" font-size: 0.8em;">
                    {{$fact_venta->telefono}}
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
      </tr>
    </table>
    <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 16px">
            <div class="details_title">DESCRIPCIÓN</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title">CANTIDAD</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title">PRECIO</div>
          </th>
          <th style="font-size: 16px">
            <div class="details_title_border_right">P.NETO</div>
          </th>
        </tr>
      </thead>
      <tbody id="detalle_productos">
        @foreach ($detallesventa as $value)

        <tr class="round">
          <td style="font-size: 16px">
            @php
            $pro= DB::table('ct_productos')->where('codigo',$value->cod_prod)->first();
            @endphp
            @if(!is_null($pro))
            {{$pro->nombre}}
            @endif

          </td>
          <td style="font-size: 16px;">
            {{$value->cantidad}}
          </td>
          @if($fact_venta->seguro->id == "4")
          <td style="font-size: 16px;">
            {{$value->precio}}
          </td>
          <td style="font-size: 16px;">
            {{$value->total}}
          </td>
          @else
          <td style="font-size: 16px;">
            @if($value->cantidad > 0)
            {{number_format(( ( ($value->precio * $value->cantidad))), 2)}}
            @endif
          </td>
          <td style="font-size: 16px;">
            {{sprintf("%.2f",( ($value->precio * $value->cantidad) - $value->valor_oda))}}
          </td>
          @endif
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="separator2"></div>
    <div class="totals_wrapper">
      <div class="totals_label">
        SUBTOTAL 0%
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->subtotal_0))
        {{sprintf("%.2f",$fact_venta->subtotal_0)}}
        @endif
      </div>
      <div class="totals_separator2"></div>
      <div class="totals_label">
        SUBTOTAL 12%
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->subtotal_12))
        {{sprintf("%.2f", $fact_venta->subtotal_12)}}
        @endif
      </div>
      <div class="totals_separator2"></div>
      <div class="totals_label">
        DESCUENTO
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->descuento))
        {{sprintf("%.2f",$fact_venta->descuento)}}
        @else
        0.00
        @endif
      </div>
      <div class="totals_separator2"></div>
      <div class="totals_label">
        TARIFA 12%
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->iva))
        {{sprintf("%.2f",$fact_venta->iva)}}
        @endif
      </div>
      <div class="totals_separator2"></div>
      <div class="totals_label">
        TOTAL
      </div>
      <div class="totals_value">
        @if(!is_null($fact_venta->total))
        {{sprintf("%.2f",$fact_venta->total)}}
        @endif
      </div>
    </div>
    <div class="separator2"></div>
    <div>
      <!--FormaS de Pago-->
      @if($ct_for_pag !='[]')
      <span class="h3">FORMAS DE PAGO</span>
      <table id="form_pag" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 12px">
              <div class="details_title_border_left">TIPO</div>
            </th>
            <th style="font-size: 12px">
              <div class="details_title_border_right">VALOR</div>
            </th>
          </tr>
        </thead>
        <tbody id="detalle_pago">
          @foreach ($ct_for_pag as $value)
          <tr class="round">
            <td style="font-size: 15px">
              {{$value->metodo->nombre}} @if($value->tipo_tarjeta!=null) - {{$value->tarjeta->nombre}} @endif @if($value->banco!=null) - {{$value->ct_banco->nombre}} @endif {{$value->numero}}
            </td>
            <td style="font-size: 15px">
              @if(!is_null($value->valor))
              {{sprintf("%.2f",$value->valor)}}
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>
    <div class="separator2">

    </div>
    <div class="col-md-12">
      <span style="font-size: 15px;"><br>Observacion: {{$fact_venta->observacion}} Nro. Oda: {{$fact_venta->numero_oda}} @if($fact_venta->valor_oda!='0')Valor Oda: $ {{$fact_venta->valor_oda}}@endif</span>
    </div>
    <div class="col-md-12">
      <br>
      <span style="font-size: 15px;border-top: solid 1px;"><br>Recibi Conforme &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    </div>

    <div class="col-md-12">
      <span style="font-size: 15px;"><br>Elaborador Por: {{$fact_venta->usercrea->nombre1}} {{$fact_venta->usercrea->apellido1}}</span>
    </div>

  </div>

</body>

</html>