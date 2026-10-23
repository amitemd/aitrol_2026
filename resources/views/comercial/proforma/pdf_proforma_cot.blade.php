<?php
$porcentajeIva = $porIva->iva * 100;
$valorIva = $porIva->iva;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="initial-scale=1.0">
  <title> {{trans('proforma.cotizacion')}} - {{$proforma_pdf->id}}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Assistant&display=swap" rel="stylesheet">
  <style>
    #page_pdf {
      max-width: 100%;
      /*margin: 15px auto 10px auto;*/
      margin: 0 0;
      float: left;
      font-size: 0.8em !important;
      padding-right: 10px;
      font-family: 'Assistant', sans-serif !important;
    }

    .upx {
      font-size: 1.0em !important;
    }

    #factura_head,
    #factura_cliente,
    #factura_detalle {
      width: 100% !important;
      margin-bottom: 8px;
    }

    .pdf_footer {
      margin-top: 100px;
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
      grid-template-columns: 1fr 1fr;
    }

    .mleft {
      margin-left: 180px;
      width: 100%;
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
      font-size: 0.5em;
    }

    .mLabel {
      width: 100%;
    }

    #container .right {
      width: 50%;
      float: right;
      font-size: 0.5em;
    }

    #container2 .left {
      width: 100%;
      float: left;
      font-size: 0.5em;
    }

    #container2 .right {
      width: 100%;
      float: right;
      font-size: 0.5em;
    }

    #container_right .rightr {
      width: 100%;
      float: right;
    }

    #container_right .leftr {
      width: 50%;
      float: left;
    }

    #container_left .leftr {
      width: 100%;
      float: left;
    }

    #container_left .rightr {
      width: 100%;
      float: right;
    }

    .left_border {
      font-weight: bold;
      margin-top: 2px !important;
      text-transform: uppercase;

    }

    .right_border {
      font-weight: normal;
      margin-top: 2px !important;
    }

    .lf {
      text-transform: uppercase;
    }

    .border {
      border: 0px solid black !important;
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
            @if(isset($proforma_pdf->empresa))
              @if($proforma_pdf->empresa->logo!=null)
              <img src="{{base_path().'/storage/app/logo/'.$proforma_pdf->empresa->logo}}" style="width:220px;height: 80px">
              @endif
            @endif
          </div>
        <td class="info_factura">
          <div class="round" style="font-size:1.0em!important;text-align: center">
            <strong> {{trans('proforma.cotizacion')}} </strong><br /><br />

          </div>
        </td>
      </tr>
      <tr>
        <td>
          <strong><br /><br />{{$proforma_pdf->empresa->razonsocial}}</strong><br />
          <strong class="lf"> R.U.C: </strong>{{$proforma_pdf->empresa->id}}<br />
          <strong class="lf"> Dir. Matriz: </strong>{{$proforma_pdf->empresa->direccion}}<br />
          <strong class="lf"> Telefono: </strong>{{$proforma_pdf->empresa->telefono1}} - {{$proforma_pdf->empresa->telefono2}}<br />
          <!--<strong class="lf"> Contribuyente Especial No:</strong> 18337<br />-->

          <br />
        </td>
        <td style="margin-left: 60px!important;">
          {{-- <strong class="lf"> número de autorizacion:</strong><br />
                    {{$proforma_pdf->nro_autorizacion}}<br /> --}}
          <strong class="lf upx">Nro Orden: </strong> <span class="upx">{{$proforma_pdf->id}}</span> <br />
          <strong class="lf"> Fecha Emision:</strong> {{date('d/m/Y',strtotime($proforma_pdf->fecha_emision))}}<br />

          {{-- <strong class="lf">Emision: </strong> NORMAL<br />
                    <strong>CLAVE DE ACCESO:</strong> <br />
                    <strong>&nbsp;</strong>&nbsp; {{$proforma_pdf->nro_autorizacion}} <br />
          <strong><img style="width: 450px; height: 30px;" src="data:image/png;base64, {{ DNS1D::getBarcodePNG('$proforma_pdf->nro_autorizacion', "C128",2,30)}}" alt="barcode" /> <br /> --}}



        </td>
      </tr>
    </table>
    @php $tota= count($proforma_pdf->detalles); @endphp
    <div id="content">
      <table id="factura_cliente" style="border: 2px solid black;border-left-color:#FFFFFF;border-right-color:#FFFFFF; width:100%" cellpadding="0" cellpadding="0">
        <tr>
          <td class="info_cliente">
            <div class="round">
              <div class="col-md-12" style="padding-top:2px;padding-bottom:5px">
                <table class="datos_cliente">
                  <tr>
                    <td width="100%">
                      <div>
                        <strong >Seguro: </strong> @if(isset($proforma_pdf->seguro)) {{$proforma_pdf->seguro->nombre}}@endif
                      </div>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </td>
        </tr>
      </table>
      <table id="factura_detalle" style="border: 2px solid black;border-top-color:#FFFFFF;border-left-color:#FFFFFF;border-right-color:#FFFFFF;padding-top:5px;padding-bottom:15px; @if($tota>=30) line-height: 80%!important; overflow:visible!important; @endif ">
        <thead style="display: table-row-group;">
          <tr>
            <th style="font-size: 14px;">
              <div class="details_title_border_left">CÓDIGO</div>
            </th>
            <th style="font-size: 14px">
              <div class="details_title">DESCRIPCIÓN</div>
            </th>
            <th style="font-size: 10px">
              <div class="details_title">CANT.</div>
            </th>
            <th style="font-size: 14px">
              <div class="details_title">PRECIO</div>
            </th>
            <th style="font-size: 14px">
              <div class="details_title">DESC.</div>
            </th>
            <th style="font-size: 14px" style="line-height: 180%">
              <div class="details_title_border_right">CUB. <br>SEG.</div>
            </th>
            <th style="font-size: 14px" style="line-height: 180%">
              <div class="details_title_border_right">CUB. <br>PAC.</div>
            </th>
          </tr>
        </thead>
        <tbody>
         @php $acum_deducible = 0;  $acum_fee = 0; @endphp
          @foreach($proforma_pdf->detalles as $x)
          <tr style="@if($tota>=30) line-height: 80%!important; overflow:hidden!important; @endif">
            @php
            $producto= Sis_medico\Ct_productos::where('id',$x->id_producto)->first();
            @endphp
            @if(!is_null($producto))
            <td>
              <div class="details_product" style="font-weight:normal; text-transform: uppercase;">{{$producto->codigo}}</div>
            </td>
            <td>
              <div class="details_product" style="font-weight:normal; @if($tota>30) height: 1.2em!important; line-height: 1.1;
                overflow: auto; text-align: center;@endif">@if($tota<30){{$producto->nombre}} @else {{substr($producto->nombre,0,20)}} @endif </div>
              @if($x->descripcion!=null && $tota<30)
              <div class="details_products" style="font-weight:normal;text-align: center;"> {{$x->descripcion}}</div>
              @endif
              </td>
            <td>
              <div class="details_product" style="font-weight:normal;text-align: center; ">{{round($x->cantidad)}}</div>
            </td>
            <td>
              <div class="details_product" style="font-weight:normal;text-align: center;">{{number_format(round($x->precio,2),2,'.','')}}</div>
            </td>
            <td>
              <div class="details_product" style="font-weight:normal;text-align: center;">{{number_format(round($x->descuento,2),2,'.','')}}</div>
            </td>
            <td>
              <div class="details_product" style="font-weight:normal;text-align: center;">{{number_format(round($x->cobrar_seguro,2),2,'.','')}}</div>
            </td>
            <td>
              @php
                $cub_pac = (isset($x->cobrar_paciente)and$x->cobrar_paciente!=null)?$x->cobrar_paciente:0;
                $deducible = (isset($x->valor_deducible)and$x->valor_deducible!=null)?$x->valor_deducible:0;
                $fee = (isset($x->fee)and$x->fee!=null)?$x->fee:0;
                $cub_pac = $cub_pac - $deducible - $fee;
                $cub_pac -=  $x->descuento;
              @endphp
              <div class="details_product" style="font-weight:normal;text-align: center;">{{number_format($cub_pac,2,'.','')}}</div>
            </td>
          </tr>
          @php
          if ($x->valor_deducible!=null&&$x->valor_deducible!="") {
            $acum_deducible += $x->valor_deducible;
          }
          if ($x->fee!=null&&$x->fee!="") {
            $acum_fee += $x->fee;
          }
          @endphp

          @if($x->valor_deducible!=null&&$x->valor_deducible!=0)
          <tr style="@if($tota>=30) line-height: 80%!important; overflow:visible!important; @endif">
            <td>DEDUC. </td>
            <td>
              <div class="details_product" style="font-weight:normal; text-transform: uppercase;">DEDUCIBLE SEGURO M&Eacute;DICO</div>
            </td>
            <td colspan="4"> </td>
            <td>
              <div class="details_product" style="font-weight:normal; text-transform: uppercase;">{{number_format(round($x->valor_deducible,2),2,'.','')}}</div>
            </td>
          </tr>
          @endif
          @if($x->fee!=null&&$x->fee!=0)
          <tr style="@if($tota>=30) line-height: 80%!important; overflow:visible!important; @endif">
            <td> </td>
            <td>
              <div class="details_product" style="font-weight:normal; text-transform: uppercase;">FEE</div>
            </td>
            <td colspan="4"> </td>
            <td>
              <div class="details_product" style="font-weight:normal; text-transform: uppercase;">{{number_format(round($x->fee,2),2,'.','')}}</div>
            </td>
          </tr>
          @endif
          @endif
          @endforeach
        </tbody>
      </table>
    </div>
    <div>
      <table>
        <tr>
          <td class="left_border">OBSERVACION</td>
        </tr>
        <tr>
          <td class="right_border">{{$proforma_pdf->observacion}}</td>
        </tr>  
      </table>
    </div>
    <div id="footer">
      <div id="container">
        <div class="">
          <table>
            <tbody>
              <tr>
                <td class="left_border">Nro Oda</td>
                <td class="right_border">{{$proforma_pdf->numero_oda}}</td>
              </tr>
              {{-- @if($proforma_pdf->valor_oda!='0')
              <tr>
                <td class="left_border">Valor Oda</td>
                <td class="right_border">{{$proforma_pdf->valor_oda}}</td>
              </tr>
              @endif
              @if($acum_deducible!='0')
              <tr>
                <td class="left_border">Deducible</td>
                <td class="right_border">{{number_format(round($acum_deducible,2),2,'.','')}}</td>
              </tr>
              @endif
              @if($acum_fee!='0')
              <tr>
                <td class="left_border">Valor Fee</td>
                <td class="right_border">{{number_format(round($acum_fee,2),2,'.','')}}</td>
              </tr>
              @endif --}}
              <tr>
                <td class="left_border">
                <br>______________________ </br>
                </td>
                <td class="right_border"></td>
              </tr>
              <tr>
                <td class="left_border">Recibido Conforme</td>
                <td class="right_border"></td>
              </tr>
              <tr>
                <td class="left_border">Elaborador Por:</td>
                <td >@if(isset($proforma_pdf->usercrea)) {{$proforma_pdf->usercrea->nombre1}} {{$proforma_pdf->usercrea->apellido1}} @endif</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="right">
          <table cellpadding="0" style="width: 100%; text-align: right;">
            <tbody>
              @php
              $subtotal= $proforma_pdf->subtotal_12+ $proforma_pdf->subtotal_0;
              @endphp
              <tr>
                <td class="left_border">&nbsp;</td>

              </tr>
              <tr>
                <td class="left_border">&nbsp;</td>

              </tr>
              <tr>
                <td class="left_border">Subtotal {{$porcentajeIva}}%</td>
                <td class="right_border">$ {{number_format($proforma_pdf->subtotal_12,2,'.','')}}</td>
              </tr>
              <tr>
                <td class="left_border">Subtotal 0%</td>
                <td class="right_border">$ {{number_format($proforma_pdf->subtotal_0,2,'.','')}}</td>
              </tr>
              <tr>
                <td class="left_border">Descuento</td>
                <td class="right_border">$ {{number_format($proforma_pdf->descuento,2,'.','')}}</td>
              </tr>
              <tr>
                <td class="left_border">Subtotal</td>
                <td class="right_border">$ {{number_format(round($subtotal,2),2,'.','')}}</td>
              </tr>

              <tr>
                <td class="left_border">Tarifa {{$porcentajeIva}}%</td>
                <td class="right_border">$ {{number_format($proforma_pdf->iva,2,'.','')}}</td>
              </tr>
              <tr>
                <td class="left_border">Total</td>
                <td class="right_border">$ {{number_format($proforma_pdf->total,2,'.','')}}</td>
              </tr>

            </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
  <div style="position: fixed; bottom: 50px" class="col-md-12">
    <p style="font-size: 17px"> Esta proforma está sujeta a cambios de acuerdo a los procedimientos realizados. A medida que avancemos en el mismo, es posible que se requieran incluir costos adicionales previa autorización. </p>
  </div> 
</body>

</html>
