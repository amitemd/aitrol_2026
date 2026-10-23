<?php
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proforma</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Assistant&display=swap" rel="stylesheet">
  <style>
    #page_pdf {
      width: 100%;
      margin: 5px auto 5px auto;
      font-family: 'Assistant', sans-serif;
    }

    #factura_head,
    #factura_cliente,
    #factura_detalle {
      width: 100%;
      margin-bottom: 10px;
      font-size: 0.7em
    }

    #detalle_productos tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;

    }

    .container_client {
      display: grid;
      grid-template-columns: 0.5fr 0.5fr;
    }

    .cont {
      background-color: black !important;
    }

    #container {
      page-break-inside: initial;
    }

    #container .left {
      width: 50%;
      float: left;
      font-size: 0.7em;
    }

    #container .right {
      width: 50%;
      float: right;
      font-size: 0.7em;
    }

    #container2 .left {
      width: 50%;
      float: left;
      font-size: 0.7em;
    }

    #container2 .right {
      width: 50%;
      float: right;
      font-size: 0.7em;
    }

    #container_right .rightr {
      width: 50%;
      float: right;
    }

    #container_right .leftr {
      width: 50%;
      float: left;
    }

    #container_left .leftr {
      width: 50%;
      float: left;
    }

    #container_left .rightr {
      width: 50%;
      float: right;
    }

    .left_border {
      font-weight: bold;
      margin-top: 14px !important;
      text-transform: uppercase;

    }

    .right_border {
      font-weight: normal;
      margin-top: 14px !important;
    }

    .lf {
      text-transform: uppercase;
    }

    .border {
      border: 2px solid black !important;
      text-align: center;
    }

    .header_table {
      vertical-align: super !important;
      /* background-color: green; */
      height: 100px !important;
    }

    .details_product {
      max-height: 20px;


    }

    .padd {
      vertical-align: super !important;
      /* background-color: green; */
      height: 45px !important;
      max-heigth: 50px;
    }

    .details_products {
      max-height: 100px !important;
    }
  </style>

</head>

<body>
  <div id="page_pdf">
    <table id="factura_head">
      <tr>
        <td class="info_empresa">
          <div style="text-align: left">

            @if($proforma->empresa->logo!=null)
            <img src="{{base_path().'/storage/app/logo/'.$proforma->empresa->logo}}" style="width:470px;height:180px">
            @endif
          </div>
        </td>
        <td class="info_factura">
          <div class="round" style="font-size:2.0em;text-align: center">
            @if($proforma->tipo == 2)
            <strong style="white-space: nowrap; letter-spacing: 5px;">NOTA ENTREGA</strong><br /><br />
            @else 
            <strong style="white-space: nowrap; letter-spacing: 10px;">PROFORMA</strong><br /><br />
            @endif
          </div>

          
          @if($proforma->tipo == 2)
          <div class="round" style="text-align: center !important;font-size:2em;margin-top: 0px">
              {{$proforma->secuencia}}<br />
          </div>
          @endif
        </td>
      </tr>
      <tr>
        <td>
          <strong><br /><br />{{$proforma->empresa->razonsocial}}</strong><br /><br />
          <strong class="lf" style="margin-top: 20px;"> R.U.C: </strong>{{$proforma->empresa->id}}<br />
          <strong class="lf"> Dir. Matriz: </strong>{{$proforma->empresa->direccion}}<br />

          <br />
        </td>
        <td style="margin-left: 60px!important;">
          <strong class="lf">{{trans('contableM.fechaexpira')}}:</strong> {{date('d/m/Y',strtotime($proforma->fecha_exp))}}<br />
        </td>
      </tr>
    </table>
    <div id="content">
      <table id="factura_cliente" style="border: 2px solid black;border-left-color:#FFFFFF;border-right-color:#FFFFFF" cellpadding="0" cellpadding="0">
        <tr>
          <td class="info_cliente">
            <div class="round">
              <div class="col-md-12" style="padding-top:2px;padding-bottom:5px">
                <table class="datos_cliente">
                  <tr>
                    <td width="45%">
                      <div class="mLabel" style="width: 700px!important;">
                        <strong class="lf"> Razon Social: </strong>@if(isset($proforma->cliente)) {{$proforma->cliente->nombre}} @else N/A CLIENTE @endif
                      </div>
                    </td>

                    <td width="55%">
                      <div class="mLabel">
                        <strong class="lf"> RUC /CI:</strong>@if(isset($proforma->cliente))  {{$proforma->cliente->identificacion}} @else N/A CLIENTE @endif
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td width="40%">
                      <div class="mLabel" style="width: 700px!important;">
                        <strong class="lf">{{trans('contableM.fechadeemision')}}: </strong> {{date('d/m/Y',strtotime($proforma->updated_at))}}
                      </div>
                    </td>

                    <td width="60%">
                      <div class="mLabel" style="padding-left: 220px;">
                      </div>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </td>
        </tr>
      </table>
      <table id="factura_detalle" style="border: 2px solid black;border-top-color:#FFFFFF;border-left-color:#FFFFFF;border-right-color:#FFFFFF;padding-top:5px;padding-bottom:15px; page-break-after:auto">
        <thead style="display: table-row-group;">
          <tr>
            <th style="font-size: 18px;">
              <div style="text-align: left;" class="details_title_border_left">{{trans('contableM.codigo')}}</div>
            </th>
            <th style="font-size: 18px">
              <div style="text-align: left;" class="details_title">{{trans('contableM.Descripcion')}}</div>
            </th>
            <th style="font-size: 18px">
              <div style="text-align: right;" class="details_title">{{trans('contableM.cantidad')}}</div>
            </th>
            <th style="font-size: 18px">
              <div style="text-align: right;" class="details_title">{{trans('contableM.precio')}}</div>
            </th>
            <th style="font-size: 18px">
              <div style="text-align: right;" class="details_title">{{trans('contableM.descuento')}}</div>
            </th>
            <th style="font-size: 18px" style="line-height: 180%">
              <div style="text-align: right;" class="details_title_border_right">Total</div>
            </th>
          </tr>
        </thead>
        <tbody>
          @php
          $subtotal0 = 0;
          $subtotal12 = 0;
          $descuento = 0;
          $total = 0;
          $iva_total = 0;
          @endphp

          @foreach($proforma->detalles as $det)
          @if($det->estado == 1)
          <tr>
            <th class="padd">
              <div class="details_product" style="font-weight:normal; text-align: left; text-transform: uppercase;">{{$det->inv_serie->producto->codigo}}</div>
            </th>
            <th class="padd">
              <div class="details_product" style="font-weight:normal; text-align: left">{{$det->inv_serie->producto->nombre}} </div>
            </th>
            <th class="padd">
              <div class="details_product" style="font-weight:normal; text-align: right">{{$det->cantidad}}</div>
            </th>
            <th class="padd">
              <div class="details_product" style="font-weight:normal; text-align: right">{{$det->precio}}</div>
            </th>
            <th class="padd">
              <div class="details_product" style="font-weight:normal; text-align: right">{{$det->descuento}}</div>
            </th>
            <th class="padd">
              <div class="details_product" style="font-weight:normal; text-align: right">{{$det->total}}</div>
            </th>
          </tr>
          @php
          $subtotal12+=$det->base12;
          $subtotal0+=$det->base0;
          $descuento+=$det->descuento;
          $total+= $det->total;
          if($det->iva == 1){
          $iva_total += $det->base12 * $det->porct_iva;
          }

          @endphp
          @endif
          @endforeach
        </tbody>

      </table>
    </div>
    <div id="footer">
      <div id="container">
        <div class="left">
          <table cellpadding="0" style="width: 100%; text-align: right;">
            <tbody>
              @php
              $subtotal= $subtotal12+ $subtotal0;

              @endphp
              <tr>
                <td style="text-align: left; width: 200px;">
                  <p style="font-weight: 700;">{{trans('informacionadicional')}}</p>
                </td>
                <td></td>
              </tr>
              
              @if($proforma->tipo == 2)
              <tr style="place-items: center; align-items: center; color: #4B5563;">
                <td style="text-align: left; width: 200px;"><b>Correo: </b></td>
                <td class="right_border" style="text-align: left; margin-top: 0px!important;"><b>{{ $proforma->empresa->email }}</b></td>
              </tr>

              <tr style="place-items: center; align-items: center; color: #4B5563;">
                <td style="text-align: left; width: 200px;"><b>Telefono: </b></td>
                <td class="right_border" style="text-align: left; margin-top: 0px!important;"><b>{{ $proforma->empresa->telefono1 }}</b></td>
              </tr>
              @else
              <tr style="place-items: center; align-items: center; color: #4B5563;">
                <td style="text-align: left; width: 200px;"><b>Bco. Pacifico: </b></td>
                <td class="right_border" style="text-align: left; margin-top: 0px!important;"><b>Cta Corriente- #7875959</b></td>
              </tr>

              <tr style="place-items: center; align-items: center; color: #4B5563;">
                <td style="text-align: left; width: 200px;"><b>Correo: </b></td>
                <td class="right_border" style="text-align: left; margin-top: 0px!important;"><b>comercial1@gastritech.ec</b></td>
              </tr>
              @endif


            </tbody>
          </table>

        </div>
        <div class="right">
          <table cellpadding="0" style="width: 100%; text-align: right;">
            <tbody>
              @php
              $subtotal= $subtotal12+ $subtotal0;

              @endphp
              <tr>
                <td class="left_border">&nbsp;</td>
              </tr>
              <tr>
                <td class="left_border">SUBTOTAL {{$porcentajeIva}}%</td>
                <td class="right_border">{{number_format(round($subtotal12,2),2,'.','')}}</td>
              </tr>
              <tr>
                <td class="left_border">{{trans('contableM.subtotal0')}}%</td>
                <td class="right_border">{{number_format(round($subtotal0,2),2,'.','')}}</td>
              </tr>
              <tr>
                <td class="left_border">{{trans('contableM.descuento')}}</td>
                <td class="right_border">{{number_format(round($descuento,2),2,'.','')}}</td>
              </tr>
              <tr>
                <td class="left_border">{{trans('contableM.subtotal')}}</td>
                <td class="right_border">{{number_format(round($subtotal,2),2,'.','')}}</td>
              </tr>
              <tr>
                <td class="left_border">Iva</td>
                <td class="right_border">{{number_format(round($iva_total,2),2,'.','')}}</td>
              </tr>
              <tr>
                <td class="left_border">{{trans('contableM.total')}}</td>
                <td class="right_border">{{number_format(round($total, 2), 2, '.', '')}}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>

</html>