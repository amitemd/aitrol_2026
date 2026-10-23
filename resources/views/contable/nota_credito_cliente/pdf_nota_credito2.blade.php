<!DOCTYPE html>
<html lang="en">
<head>

  <title>Nota de Crédito Cliente</title>
  <style>


    #page_pdf{
      width: 50%;
      /*margin: 15px auto 10px auto;*/
      margin: 0 0;
      float: left;
      padding-right: 20px;
      border-right: solid 1px;
    }
    #page_pdf2{
      width: 50%;
      /*margin: 15px auto 10px auto;*/
      float: left;
      padding-left: 20px;

    }

    #factura_head,#factura_cliente,#factura_detalle{
      width: 100%;
      /*margin-bottom: 10px;*/

    }
    #factura_head{
      margin-top: -50px;
    }

    #detalle_productos tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 10px;

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
      width: 27%;
    }

    .info_nota{
      width: 25%;
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

    .marco{
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
      width:12%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;

    }

    .mLabel1{
      width:39%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;
    }

    .mLabel2{
      width:18%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;
    }

    .mLabel3{
      width:48%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;
    }

    .mValue{
      /*width:79%;*/
      width:50%;
      display: inline-block;
      vertical-align: top;
      /*padding-left:7px;*/
      padding: 0;
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
      height:20px;
      clear: both;
    }
    .separator2{
      width:100%;
      height:20px;
    }
    .separator3{
      width:100%;
      height:10px;
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

  .enlace_desactivado {
    pointer-events: none;
    cursor: default;
  }
  .row {
    padding: 0;
  }

  #detalles_fotter tr:nth-child(even) {
      background: #ededed;
      border-radius: 10px;
      border: 1px solid black;
      overflow: hidden;
      padding-bottom: 10px;

  }
  #detalles_fotter span {
      font-family: 'BrixSansBlack';
      text-align: right;
      font-size: 14px;

    }
    #detalles_fotter b {
      font-family: 'BrixSansBlack';
      text-align: right;
      font-size: 17px;

    }
  </style>
</head>
@php
  $subtotal   = 0;
  $iva    = 0;
  $impuesto   = 0;
  $tl_sniva   = 0;
  $total    = 0;
  $fech_inver_not = null;
  $fech_inver_venta  = null;
  $stilo = "";
  $ids = "page_pdf";
@endphp

<body>
@for($t =0; $t < 2; $t++ )
@php
    if($t == 1){
      $stilo="style='float: left'";
      $ids ="page_pdf2";
      //dd($stilo);
    }
    
@endphp

  <div id ="{{$ids}}" {{$stilo}} >
    <table id="factura_head">
      <tr>
        <td class="info_factura">
          <div style="text-align: center">.
            @if(!is_null($empresa->logo))
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}"  style="width:250px;height: 110px">
            @else
            <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}"  style="width:250px;height: 110px">
            @endif
          </div>
          <div class="round">
            <p style="padding-left: 10px;font-size: 0.7em">
              <strong>R.U.C.:</strong>&nbsp;@if(!is_null($empresa->id)){{$empresa->id}}@endif<br/>
              <strong>Nombre Comercial:</strong>&nbsp;@if(!is_null($empresa->nombrecomercial)){{$empresa->nombrecomercial}}@endif<br/>
              <strong>Teléfono:</strong>&nbsp;{{$empresa->telefono1}}<br/>
              <strong>Dir.Matriz:</strong>&nbsp;{{$empresa->direccion}}<br/>
              <strong>OBLIGADO A LLEVAR CONTABILIDAD:</strong>&nbsp;SI<br/>
              <strong>CONTRIBUYENTE ESPECIAL No.:</strong>&nbsp;18337<br/>
              <strong>Correo Electrónico:</strong>&nbsp;{{$empresa->email}} <br/>
            </p>
          </div>
        </td>
        <td class="info_nota">
          <div class="round">
            <span class="h3" style="padding:20px">NOTA DE CRÉDITO</span>
            <p style="padding-left: 10px;font-size: 0.6em">
              <strong>No.:</strong>&nbsp;{{$nota_cred->nro_comprobante}}<br/>
              <strong>NÚMERO DE AUTORIZACIÓN:</strong><br/>
              @if(!is_null($nota_cred->nro_autorizacion)) {{$nota_cred->nro_autorizacion}} @endif<br/>
              <strong>FECHA Y HORA DE AUTORIZACION:</strong><br/>
              @if(!is_null($nota_cred->fecha_envio)) {{$nota_cred->fecha_envio}} @endif<br/>
              <strong>AMBIENTE:</strong>&nbsp;Prueba<br/>
              <strong>CLAVE DE ACCESO:</strong><br/>
              1607202004099270415200120010020200000150000000113<br/>
            </p>
          </div>
        </td>
      </tr>
    </table>
    <table id="factura_cliente">
      <tr>
        <td class="info_cliente">
          <div class="marco">
            <div class="col-md-12">
              <table class="datos_cliente">
                <tr>
                  <td>
                    <br/>
                    <div class="row">
                      <div class="mLabel1">
                        Razón Social / Nombres y Apellidos:
                      </div>
                      <div class="mValue">
                      @if(!is_null($cliente->nombre))
                        {{trim($cliente->nombre)}}
                      @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                       RUC / CI:
                      </div>
                      <div class="mValue">
                      @if(!is_null($cliente->identificacion))
                        {{$cliente->identificacion}}
                      @endif
                      </div>
                    </div>
                    @php
                      if(!is_null($nota_cred) && !is_null($ventas)){
                        $fech_inver_not = date("d/m/Y",strtotime($nota_cred->fecha));
                        $fech_inver_venta = date("d/m/Y",strtotime($ventas->fecha));
                      }
                    @endphp
                    <div class="row">
                      <div class="mLabel2">
                       Fecha Emisión:
                      </div>
                      <div class="mValue">
                      @if(!is_null($fech_inver_not))
                        {{$fech_inver_not}}
                      @endif
                      </div>
                    </div>
                    <div class="separator2"></div>
                    <div class="row" style="border-bottom: 2px solid black;">
                    </div>
                    <div class="separator3"></div>
                    <div class="row">
                      <div class="mLabel1">
                       Comprobante que se modifica:
                      </div>
                      <div class="mValue">
                        FACTURA
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                      No:
                      </div>
                      <div class="mValue">
                      @if(!is_null($ventas))
                       {{$ventas->nro_comprobante}}
                      @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel3">
                      Fecha Emisión (Comprobante a modificar):
                      </div>
                      <div class="mValue">
                      @if(!is_null($fech_inver_venta))
                        {{$fech_inver_venta}}
                      @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel3">
                      Razon por la que se modifica :
                      </div>
                      <div class="mValue">
                      @if(!is_null($asiento_cabecera->observacion))
                        {{$asiento_cabecera->observacion}}
                      @endif
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
    <table id="factura_detalle" cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 13px"><div class="details_title">Código Principal</div></th>
          <th style="font-size: 13px"><div class="details_title">Cantidad</div></th>
          <th style="font-size: 13px"><div class="details_title">Descripción</div></th>
          <th style="font-size: 13px"><div class="details_title">Precio</div></th>
          <th style="font-size: 13px"><div class="details_title">Descuento</div></th>
          <th style="font-size: 13px"><div class="details_title_border_right">Precio Total</div></th>
        </tr>
      </thead>
      <tbody id="detalle_productos">
        @foreach ($det_rub as $value)
          <tr class="round">
            <td style="font-size: 15px">@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
            <td style="font-size: 15px;">1.00</td>
            <td style="font-size: 15px;">@if(!is_null($value->nombre_rubro)){{$value->nombre_rubro}}@endif</td>
            <td style="font-size: 15px;">@if(!is_null($value->valor)){{number_format($value->valor,2)}}@endif</td>
            <td  style="font-size: 15px;">0.00</td>
            <td  style="font-size: 15px;">@if(!is_null($value->total_base)){{number_format($value->total_base,2)}}@endif</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot id="detalles_fotter">
          <tr>
              <td>
                &nbsp;
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <b>Subtotal 12%</b>
              </td>
              <td>
              &nbsp;
              </td>
              <td>
                <span>@if(($nota_cred->impuesto)>0) {{number_format($nota_cred->subtotal,2)}} @else 0.00 @endif</span>
              </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal 0%</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>@if(($nota_cred->impuesto)==0) {{number_format($nota_cred->subtotal,2)}} @else 0.00 @endif</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal No Sujeto de Iva</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal Total Sin Impuestos</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Descuento</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Ice </b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Iva 12%</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
            <span>@if(($nota_cred->tar_iva_12)>0) {{number_format($nota_cred->tar_iva_12,2)}} @else 0.00 @endif</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Valor Total</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>@if(($nota_cred->total_credito)>0) {{number_format($nota_cred->total_credito,2)}} @else 0.00 @endif</span>
            </td>
          </tr>
      </tfoot>
    </table>
  </div>
@endfor
  <!--<div id="page_pdf2" style="float: left;">
    <table id="factura_head">
      <tr>
        <td class="info_factura">
          <div style="text-align: center">
            @if(!is_null($empresa->logo))
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}"  style="width:250px;height: 110px">
            @else
            <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}"  style="width:250px;height: 110px">
            @endif
          </div>
          <div class="round">
            <p style="padding-left: 10px;font-size: 0.7em">
              <strong>R.U.C.:</strong>&nbsp;{{$empresa->id}}<br/>
              <strong>Nombre Comercial:</strong>&nbsp;{{$empresa->nombrecomercial}}<br/>
              <strong>Teléfono:</strong>&nbsp;{{$empresa->telefono1}}<br/>
              <strong>Dir.Matriz:</strong>&nbsp;{{$empresa->direccion}}<br/>
              <strong>OBLIGADO A LLEVAR CONTABILIDAD:</strong>&nbsp;SI<br/>
              <strong>CONTRIBUYENTE ESPECIAL No.:</strong>&nbsp;18337<br/>
              <strong>Correo Electrónico:</strong>&nbsp;jesseniac.ieced@gmail.com; andreal.ieced@gmail.com<br/>
            </p>
        </td>
        <td class="info_nota">
          <div class="round">
            <span class="h3" style="padding:20px">NOTA DE CRÉDITO</span>
            <p style="padding-left: 10px;font-size: 0.6em">
              <strong>No.:</strong>&nbsp;{{$nota_cred->nro_comprobante}}<br/>
               <strong>NÚMERO DE AUTORIZACIÓN:</strong><br/>
               1607202004099270415200120010020200000150000000113<br/>
               <strong>FECHA Y HORA DE AUTORIZACION:</strong><br/>
               2020-07-16T12:27:26-05:00<br/>
               <strong>AMBIENTE:</strong>&nbsp;Prueba<br/>
               <strong>CLAVE DE ACCESO:</strong><br/>
               1607202004099270415200120010020200000150000000113<br/>
            </p>
          </div>
        </td>
      </tr>
    </table>
    <table id="factura_cliente">
      <tr>
        <td class="info_cliente">
          <div class="marco">
            <div class="col-md-12">
              <table class="datos_cliente">
                <tr>
                  <td>
                    <br/>
                    <div class="row">
                      <div class="mLabel1">
                        Razón Social / Nombres y Apellidos:
                      </div>
                      <div class="mValue">
                      @if(!is_null($cliente->nombre))
                        {{trim($cliente->nombre)}}
                      @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        RUC / CI:
                      </div>
                      <div class="mValue">
                      @if(!is_null($cliente->identificacion))
                        {{$cliente->identificacion}}
                      @endif
                      </div>
                    </div>
                    @php
                    if(!is_null($nota_cred) && !is_null($ventas)){
                      $fech_inver_not = date("d/m/Y",strtotime($nota_cred->fecha));
                      $fech_inver_venta = date("d/m/Y",strtotime($ventas->fecha));
                    }
                    @endphp
                    <div class="row">
                      <div class="mLabel2">
                       Fecha Emisión:
                      </div>
                      <div class="mValue">
                      @if(!is_null($fech_inver_not))
                        {{$fech_inver_not}}
                      @endif
                      </div>
                    </div>
                    <div class="separator2"></div>
                    <div class="row" style="border-bottom: 2px solid black;">
                    </div>
                    <div class="separator3"></div>
                    <div class="row">
                      <div class="mLabel1">
                       Comprobante que se modifica:
                      </div>
                      <div class="mValue">
                        FACTURA
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                      No:
                      </div>
                      <div class="mValue">
                      @if(!is_null($ventas))
                       {{$ventas->nro_comprobante}}
                      @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel3">
                      Fecha Emisión (Comprobante a modificar):
                      </div>
                      <div class="mValue">
                      @if(!is_null($fech_inver_venta))
                        {{$fech_inver_venta}}
                      @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel3">
                      Razon por la que se modifica :
                      </div>
                      <div class="mValue">
                      @if(!is_null($asiento_cabecera->observacion))
                        {{$asiento_cabecera->observacion}}
                      @endif
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
    <table id="factura_detalle" cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 13px"><div class="details_title">Código Principal</div></th>
          <th style="font-size: 13px"><div class="details_title">Cantidad</div></th>
          <th style="font-size: 13px"><div class="details_title">Descripción</div></th>
          <th style="font-size: 13px"><div class="details_title">Precio</div></th>
          <th style="font-size: 13px"><div class="details_title">Descuento</div></th>
          <th style="font-size: 13px"><div class="details_title_border_right">Precio Total</div></th>
        </tr>
      </thead>
      <tbody id="detalle_productos" >
      @foreach ($det_rub as $value)
          <tr class="round">
            <td style="font-size: 15px">@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
            <td style="font-size: 15px;">1.00</td>
            <td style="font-size: 15px;">@if(!is_null($value->nombre_rubro)){{$value->nombre_rubro}}@endif</td>
            <td style="font-size: 15px;">@if(!is_null($value->valor)){{number_format($value->valor,2)}}@endif</td>
            <td  style="font-size: 15px;">0.00</td>
            <td  style="font-size: 15px;">@if(!is_null($value->total_base)){{number_format($value->total_base,2)}}@endif</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot id="detalles_fotter">
          <tr>
              <td>
                &nbsp;
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                &nbsp;
              </td>
              <td>
                <b>Subtotal 12%</b>
              </td>
              <td>
              &nbsp;
              </td>
              <td>
                <span>@if(($nota_cred->impuesto)>0) {{number_format($nota_cred->subtotal,2)}} @else 0.00 @endif</span>
              </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal 0%</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>@if(($nota_cred->impuesto)==0) {{number_format($nota_cred->subtotal,2)}} @else 0.00 @endif</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal No Sujeto de Iva</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Subtotal Total Sin Impuestos</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Descuento</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Ice </b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>0.00</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Iva 12%</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
            <span>@if(($nota_cred->tar_iva_12)>0) {{number_format($nota_cred->tar_iva_12,2)}} @else 0.00 @endif</span>
            </td>
          </tr>
          <tr>
            <td>
              &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             &nbsp;
            </td>
            <td>
             <b>Valor Total</b>
            </td>
            <td>
            &nbsp;
            </td>
            <td>
             <span>@if(($nota_cred->total_credito)>0) {{number_format($nota_cred->total_credito,2)}} @else 0.00 @endif</span>
            </td>
          </tr>
      </tfoot>
    </table>
  </div>-->

</body>
</html>
