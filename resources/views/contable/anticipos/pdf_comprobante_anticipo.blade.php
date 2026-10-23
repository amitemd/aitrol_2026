<!DOCTYPE html>
<html lang="en">
<head>
  
  <title>Retencion Proveedores</title>
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
      font-size: 10pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }
    .h4{
      font-family: 'BrixSansBlack';
      font-size: 10pt;  
      display: block;
      background: #3d7ba8;
      color: #fff;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

    .round{
      padding-bottom: 15px;
    }

    table{
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
    table tr:nth-child(odd){
       background: #FFF;
    }
    
    table td{
      padding: 4px;


    }

    table th{
       text-align: left;
       /*
       padding: 4px;
       */
       /*
       background: #3d7ba8;
       color: #FFF;
       */
       color:#3d7ba8;
       font-size: 1em;
    }

    .datos_cliente
    {
      font-size: 0.8em;
      border-radius: 10 px;
      border: 1px solid #3d7ba8;
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
      width:30%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.89em;

    }
    .mValue{
      width:70%;
      display: inline-block;
      vertical-align: top;
      padding-left:10px;
      font-size: 0.89em;
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
    .hz{
        margin-left: 20px;
        font-family: 'BrixSansBlack';
        text-align: center;
        font-size: 15pt;
        color: #FF5733;
    }
    .h10{
        font-family: 'BrixSansBlack';
        text-align: center;
        font-size: 7pt;
        color: black;
    }
    .derecha_total{
        margin-left: 280px;
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

        
        <td class="info_empresa">
          <div style="text-align: center">
            <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}"  style="width:350px;height: 150px">
          </div>
          @if($empresa!=null)
          <div style="text-align: center; font-size:0.8em">
            R.U.C.: {{$empresa->id}}<br/>
            Nombre Comercial: {{$empresa->nombrecomercial}}<br/>
            Teléfono: {{$empresa->telefono1}}<br/>
            Dir.Matriz: {{$empresa->direccion}}<br/>
            <br/>
          @endif
          </div>
        </td>
        <td class="info_factura">
          <div class="round">
            <span class="h3" style="padding:20px">COMPROBANTE DE ANTICIPO</span>
            <span class="hz">N° @if($anticipo!=null) {{$anticipo->nro_comprobante}}-{{$anticipo->secuencia}}  @endif</span>
            <p  style="padding-left:20px;padding-right:20px;padding-top:0px;padding-bottom:0px; text-align: center; font-size: 9pt;">
              @if($anticipo!=null) {{$anticipo->fecha_pago}}  @endif<br/>
            </p>             
            <!--Hora: 10:30am</p>-->
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
                    <br/>
                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                      <div class="mLabel">
                        Proveedor:
                      </div>
                      <div class="mValue">
                           @if($proveedor!=null) {{$proveedor->nombrecomercial}}  @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        RUC:
                      </div>
                      <div class="mValue">
                            @if($proveedor!=null){{$proveedor->id}} @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel">
                        Direccion:
                      </div>
                      <div class="mValue">
                            @if($proveedor!=null){{$proveedor->direccion}}@endif
                      </div>
                    </div>
                  </td>
                  <td>
                    <br/>
                    <div class="row" style="padding-bottom: 0px;margin-bottom:0px;">
                      <div class="mLabel derecha_total">
                        E-mail
                      </div>
                      <div class="mValue">
                           @if($proveedor!=null) {{$proveedor->email}} @endif
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel derecha_total">
                      </div>
                      <div class="mValue">
                            Telefono
                      </div>
                    </div>
                    <div class="row">
                      <div class="mLabel derecha_total">
                        No.:
                      </div>
                      <div class="mValue">
                         @if($proveedor!=null) {{$proveedor->telefono1}} @endif
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
    <span class="h4">DETALLE TIPO DE PAGO ANTICIPO</span>
    <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 16px;"><div class="details_title_border_left">Descripción</div></th>
          <th style="font-size: 16px"><div class="details_title">Pago</div></th>
        </tr>
      </thead>
      <tbody id="detalle_productos" >
         <tr class="round">
            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
             @if($anticipo!=null) {{$anticipo->observacion}}  @endif
            </td>

            <td style="font-size: 16px; border-radius: 3px; border: 3px solid #3d7ba8;">
               @if($tipo_pago!=null) {{$tipo_pago->nombre}} @endif
            </td>
          </tr>

      </tbody>
    </table>


    <div class="separator"></div>
      <div class="totals_wrapper">
          <div class="totals_label">
              PAGO
          </div>
          <div class="totals_value">
              @if($tipo_pago!=null) {{$tipo_pago->nombre}} @endif
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              FECHA PAGO
             
          </div>
          <div class="totals_value">
              @if($anticipo!=null) {{$anticipo->fecha_pago}} @endif
          </div>
          <div class="totals_separator"></div>
          <div class="totals_label">
              MONTO
          </div>
          <div class="totals_value">
             @if($anticipo!=null) {{$anticipo->total}} @endif
          </div>
          <div class="totals_separator"></div>
      </div>
      <div class="totals_wrapper">
            <div class="totals_label">
                &nbsp;
            </div>
          <div class="totals_value">
            ___________________________
          </div>
            <div>
            &nbsp;
            </div>
            <div class="totals_separator"></div>
            <div class="totals_label">
            &nbsp;
            </div>
          <div class="totals_value">
                RECIBE <br/>
              @if($proveedor!=null) ( {{$proveedor->nombrecomercial}} ) @endif <br/>
              @if($proveedor!=null)    ({{$proveedor->id}}) @endif <br/>
          </div>
          <div class="totals_separator"></div>
          <div class="totals_separator"></div>
          <div class="totals_separator"></div>
          <div class="totals_label">
            &nbsp;
          </div>
          <div class="totals_value">
            ___________________________
          </div>
          <div class="totals_label">
            &nbsp;
          </div>
          <div class="totals_value">
            GENERA @if($usuario_crea!=null) {{$usuario_crea->nombre1}} {{$usuario_crea->apellido1}}  @endif <br/>
          </div>
      </div>
  </div>

</body>
</html>  