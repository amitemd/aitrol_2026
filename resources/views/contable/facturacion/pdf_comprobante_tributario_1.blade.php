

<!DOCTYPE html>
<html lang="en">
<head>

  <title>Recibo de Cobro</title>
  <style>


    #page_pdf{
      width: 95%;
      margin: 15px auto 10px auto;
    }

    #factura_head,#factura_cliente,#factura_detalle{
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

    #detalle_totales span{
      font-family: 'BrixSansBlack';
      text-align: right;
    }

    .logo_factura{
      width: 25%;
    }

    .info_empresa{
      width: 50%;
      text-align: center;
    }

    .info_factura{
      width: 31%;
    }

    .info_cliente{
      width: 69%;
    }

    .textright{
      padding-left: 3;
    }


    .h3{
      font-family: 'BrixSansBlack';
      font-size: 8pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    table{
       border-collapse: collapse;
       font-size: 12pt;
       font-family: 'arial';
       width: 100%;
    }


    table tr:nth-child(odd){
       background: #FFF;
    }

    table td{
      padding: 4px;


    }

    table th{
       text-align: left;
       color:#3d7ba8;
       font-size: 1em;
    }

    .datos_cliente
    {
      font-size: 0.8em;
    }

    .datos_cliente label{
       width: 75px;
       display: inline-block;
    }

    .lab{
      font-size: 18px;
      font-family: 'arial';
    }

    *{
      font-family:'Arial' !important;
    }

    .mLabel{
      width:20%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;

    }
    .mValue{
      width:79%;
      display: inline-block;
      vertical-align: top;
      padding-left:7px;
      font-size: 0.9em;
    }

    .totals_wrapper{
      width:100%;
    }
    .totals_label{
      display: inline-block;
      vertical-align: top;
      width:85%;
      text-align: right;
      font-size: 0.7em;
      font-weight: bold;
      font-family: 'Arial';
    }
    .totals_value{
      display: inline-block;
      vertical-align: top;
      width:14%;
      text-align: right;
      font-size: 0.7em;
      font-weight: normal;
      font-family: 'Arial';
    }
    .totals_separator{
      width:100%;
      height:1px;
      clear: both;
    }

    .separator{
      width:100%;
      height:60px;
      clear: both;
    }

    .details_title_border_left{
      background: #3d7ba8;
      border-top-left-radius: 10px;
      color:#FFF;
      padding: 10px;
      padding-left:10px;
    }

    .details_title_border_right{
      background: #3d7ba8;
      border-top-right-radius: 10px;
      color:#FFF;
      padding: 10px;
      padding-right:3px;
    }

    .details_title{
      background: #3d7ba8;
      color:#FFF;
      padding: 10px;
    }
  </style>
</head>
@php
  $subtotal   = 0;
  $iva    = 0;
  $impuesto   = 0;
  $tl_sniva   = 0;
  $total    = 0;
@endphp

<body>

  <div id="page_pdf">
    <table id="factura_head">
      <tr>
        <!--INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A-->
        @if($fact_venta->id_empresa == '0992704152001')

        <td class="info_empresa">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}"  style="width:350px;height: 150px">
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
            <span class="h3" style="padding:20px">RECIBO DE COBRO</span>
            <p  style="padding-left:20px;padding-right:20px;padding-top:0px;padding-bottom:0px">
              No. Orden:<strong> {{$fact_venta->id}}</strong><br/>
              Fecha: {{$fact_venta->fecha_emision}}<br/>
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
                  <td>
                    <br/>
                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                      <div class="mLabel">
                        CLIENTE:
                      </div>
                      <div class="mValue">
                        {{trim($fact_venta->razon_social)}}
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        DIRECCION:
                      </div>
                      <div class="mValue">
                        {{$fact_venta->direccion}}
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        MAIL:
                      </div>
                      <div class="mValue">
                        {{$fact_venta->email}}
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        R.U.C./C.I.:
                      </div>
                      <div class="mValue">
                        {{$fact_venta->identificacion}}
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        TELÉFONO:
                      </div>
                      <div class="mValue">
                        {{$fact_venta->telefono}}
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        FECHA:
                      </div>
                      <div class="mValue">
                        {{$fact_venta->fecha_emision}}
                      </div>
                    </div>

                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                      <div class="mLabel">
                        PACIENTE:
                      </div>
                      <div class="mValue">
                        {{$fact_venta->agenda->paciente->apellido1}} @if($fact_venta->agenda->paciente->apellido2!='(N/A)'){{$fact_venta->agenda->paciente->apellido2}}@endif  {{$fact_venta->agenda->paciente->nombre1}} @if($fact_venta->agenda->paciente->nombre2!='(N/A)'){{$fact_venta->agenda->paciente->nombre2}}@endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        SEGURO:
                      </div>
                      <div class="mValue">
                        {{$fact_venta->seguro->nombre}}
                      </div>
                    </div>
                  </td>
                  <td>
                    <br/>
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
          <th style="font-size: 16px"><div class="details_title">DESCRIPCIÓN</div></th>
          <th style="font-size: 16px"><div class="details_title">CANTIDAD</div></th>
          <th style="font-size: 16px"><div class="details_title">PRECIO</div></th>
          <th style="font-size: 16px"><div class="details_title_border_right">P.NETO</div></th>
        </tr>
      </thead>
      <tbody id="detalle_productos" >
        @foreach ($fact_venta->detalles as $value)
          <tr class="round">
            <td style="font-size: 16px">
                {{$value->descripcion}}
            </td>
            <td  style="font-size: 16px;">
                {{$value->cantidad}}
            </td>
            <td  style="font-size: 16px;">
                {{sprintf("%.2f",$value->precio)}}
            </td>
            <td  style="font-size: 16px;">
                {{sprintf("%.2f",$value->total)}}
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
              <th style="font-size: 15px"><div class="details_title_border_left">TIPO</div></th>
              <th style="font-size: 15px"><div class="details_title_border_right">VALOR</div></th>
            </tr>
          </thead>
          <tbody id="detalle_pago">
            @foreach ($ct_for_pag as $value)
              <tr class="round">
                <td style="font-size: 16px">
                  {{$value->metodo->nombre}}
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
      <br><br><br><br><br>
      <span style="font-size: 18px;border-top: solid 1px;"><br>Recibi Conforme &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    </div>

  </div>

</body>
</html>
