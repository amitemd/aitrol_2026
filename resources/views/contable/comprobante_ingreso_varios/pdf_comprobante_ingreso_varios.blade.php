<!DOCTYPE html>
<html lang="en">
<head>

  <title>Comprobante de Ingreso Varios</title>
  <style type="text/css">

    #principal{
      width:800px;
    }

    @page { margin-top:261px;margin-bottom:100px; }

      /*#footer1 { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 110px; }*/

      #footer1 {margin-top: 90px;}
      #footer2 {margin-top: 190px;}


    #page_pdf{
      width:800px;
     /*width: 49%;*/
      /*margin: 0 0;*/
      /*float: left;*/
      padding-right: 20px;
      /*border-right: solid 1px;*/
    }

    #page_pdf2{
      /*width: 49%;*/
      /*float: left;*/
      width:800px;
      padding-left: 20px;

    }


    #factura_head,#factura_cliente,#factura_detalle{
      width: 100%;
      /*margin-bottom: 10px;*/
    }

    #factura_head{
      margin-top: -210px;
    }


    .info_empresa{
      width: 50%;
      text-align: center;
    }

    .separator1{
      width:100%;
      height:15px;
      clear: both;
    }

    .separator{
      width:100%;
      height:60px;
      clear: both;
    }

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 15px;
    }

    .round2{
      border-radius: 15px;
      border: 3px solid #3d7ba8;
      padding-bottom: 15px;
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
        padding: 7px;
        font-size: 1em;
        margin-bottom: 15px;
    }

    .info_rol{
      width: 69%;
    }
    .bordes_padding3{
      border-left: 1px solid black;
      border-right: 1px solid black;
      border-top: 1px solid black;
      padding: -10px;
      border-bottom: 1px solid white;
    }
    .datos_rol
    {
      font-size: 0.8em;
    }


    .mLabel{
      width:20%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.9em;

    }
    .mLabel2{
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
      padding-left:40px;
      font-size: 0.9em;

    }
    .mValue4{
      width:30%;
      display: inline-block;
      vertical-align: top;
      padding-left:2px;
      font-size: 0.9em;

    }
    .mValue3{
      width:79%;
      display: inline-block;
      vertical-align: top;
      padding-left:2px;
      font-size: 0.9em;

    }


    table{
       border-collapse: collapse;
       font-size: 12pt;
       font-family: 'arial';
       width: 100%;
    }

    table tr:nth-child(odd){

    }

    table td{
      padding: 2px;
    }

    table th{
       text-align: left;
       color:#3d7ba8;
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

    *{
      font-family:'Arial' !important;
    }

    .details_title_border_left{
      background: #888;
      border-top-left-radius: 10px;
      color:#FFF;
      padding: 10px;
      padding-left:10px;
    }

    .details_title_border_right{
      background: #888;
      border-top-right-radius: 10px;
      color:#FFF;
      padding: 10px;
      padding-right:3px;
    }

    .details_title{
      background: #888;
      color:#FFF;
      padding: 10px;
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

    .totals_label2{
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


    .color_texto{
      color:#FFF;
    }

    .head-title{
      background-color: #888;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 30px;
      color: #cccccc;
      text-align: center;
    }

    .dobra{
       background-color: #D4D0C8;
    }
    .bordes_padding{
      border-top: 1px solid black;
      border-left: 1px solid black;
      border-right: 1px solid black;
      padding: -10px;
    }
    .bordes_padding2{
      border-left: 1px solid black;
      border-right: 1px solid black;
      padding: -10px;
    }
    .separater{
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
            <img  src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:250px;height: 50px">
           
            @else
            <img src="{{base_path().'/storage/app/logo/iec_logo1391707460001.png'}}" style="width:250px;height: 50px">

            @endif

          </div>
          <div style="text-align: center; font-size:0.8em">

            {{$empresa->id}}<br/>
            {{$empresa->nombrecomercial}}<br/>
            {{$empresa->telefono1}}<br/>
            {{$empresa->direccion}}<br/>
            <br/>
          </div>
        </td>
        <td class="info_factura">
          <div>
            <span class="h3" style="padding:20px">COMPROBANTE DE INGRESO VARIOS</span>
            <p style="padding-left: 10px;font-size: 20px; text-align: center;">
              No: <strong> {{$comp_ingreso->secuencia}}</strong><br/>

              <strong>POR $***********{{$comp_ingreso->total_ingreso}}</strong>
            </p>
          </div>
        </td>

      </tr>
    </table>
    <table id="factura_cliente" class="bordes_padding">
      <tr>
        <td class="info_rol">
          <div >
            <div class="col-md-12">
              <table class="datos_rol">
                <tr>
                  <td>
                    <div class="col-md-12" style="font-size: 15px;">
                      <br/>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px ">
                        <div class="mLabel">
                          RECIBI DE :
                        </div>
                        <div class="mValue3">
                          @if(($comp_ingreso->pago_ingresos!=null)){{$comp_ingreso->pago_ingresos[0]->girador}} @endif
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          La suma de: &nbsp;
                        </div>
                        <div class="mValue3" style="margin-left: 10px;" id="resultado">
                        @include ('contable.nota_debito.conversor')
                          @php

                              $cent = $comp_ingreso->total_ingreso - (int)($comp_ingreso->total_ingreso);
                              $val = $comp_ingreso->total_ingreso - $cent;
                              $cent = number_format($cent, 2);

                              echo convertir($val, $cent);
                          @endphp
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          CONCEPTO:
                        </div>
                        <div class="mValue3">
                            {{$comp_ingreso->concepto}}
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          Codigo:
                        </div>
                        <div class="mValue4">
                            {{$asiento_detalle[0]->id_plan_cuenta}}
                        </div>
                        <div class="mLabel">
                          Caja:
                        </div>
                        <div class="mValue4">
                          {{$asiento_detalle[0]->cuenta_empresa->nombre}}
                        </div>
                      </div>

                    </div>
                  </td>

                  <td>
                    <div class="col-md-12" style="font-size: 15px;">
                      <br/>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px;">
                        <div class="mLabel">
                          FECHA:
                        </div>
                        <div class="mValue">
                            {{date("d-m-Y", strtotime($comp_ingreso->fecha))}}
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          ASIENTO:
                        </div>
                        <div class="mValue">
                            {{$comp_ingreso->id_asiento_cabecera}}
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          TIPO:
                        </div>
                        <div class="mValue">
                            BAN-IN
                        </div>
                      </div>
                      <div class="row">
                        <div class="mLabel">
                          ESTADO:
                        </div>
                        <div class="mValue">
                            ACTIVO
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>

                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
      </tr>
    </table>

    <table id="factura_detalle" style="margin-top: 20px;" class="bordes_padding" cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 16px; color: black;">Código</th>
          <th style="font-size: 16px; color: black;">Cuenta</th>
          <th style="font-size: 16px; color: black;">Div</th>
          <th style="font-size: 16px; color: black;">Valor</th>
          <th style="font-size: 16px; color: black;">DEBE</th>
          <th style="font-size: 16px; color: black;">HABER</th>
        </tr>
      </thead>
      <tbody >
       @php
        $total_debe=0;
        $total_haber=0;
       @endphp
       @foreach($asiento_detalle as $value)
        @php
          $total_debe+=$value->debe;
          $total_haber+=$value->haber;
          $id_empresa= Session::get('id_empresa');
          $plan_cuentas_empresa= Sis_medico\Plan_Cuentas_Empresa::where('id_empresa', $id_empresa)->where('id_plan', $value->id_plan_cuenta)->first();
        @endphp
        <tr>
          <!-- <td style="font-size: 16px;">
           {{$value->id_plan_cuenta}}
          </td> -->
          <td style="font-size: 16px;">
           {{$plan_cuentas_empresa->plan}}
          </td>
          <td style="font-size: 16px;">
           {{$value->cuenta_empresa->nombre}}
          </td>
          <td style="font-size: 16px;">
           $
          </td>
          <td style="font-size: 16px;">
           @php $total= $value->debe+$value->haber; @endphp
           {{number_format($total,2,'.','')}}
          </td>
          <td style="font-size: 16px;">
            @if(($value->debe)>0) {{number_format($value->debe,2,'.','')}} @else 0.00 @endif
          </td>
          <td style="font-size: 16px;">
            @if(($value->haber)>0) {{number_format($value->haber,2,'.','')}} @else 0.00  @endif
          </td>
        </tr>
        @endforeach

        <tr>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td class="totals_label2">
           &nbsp;
          </td>
          <td class="totals_label2">
          &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
          &nbsp;
          </td>
        </tr>
        <tr>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td class="totals_label2">
           &nbsp;
          </td>
          <td class="totals_label2">
          &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
          &nbsp;
          </td>
        </tr>
        <tr>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td class="totals_label2">
           &nbsp;
          </td>
          <td class="totals_label2">
          &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
          &nbsp;
          </td>
        </tr>
        <tr>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td class="totals_label2">
           &nbsp;
          </td>
          <td class="totals_label2">
          &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
          &nbsp;
          </td>
        </tr>
        <tr>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td class="totals_label2">
           &nbsp;
          </td>
          <td class="totals_label2">
          &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
          &nbsp;
          </td>
        </tr>
        <tr>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px;  ">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
            &nbsp;
          </td>
          <td class="totals_label2" style="">
           &nbsp;
          </td>
          <td class="totals_label2" style="">
          &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; ">
          &nbsp;
          </td>
        </tr>
        <tr>
          <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
            &nbsp;
          </td>
          <td style="font-size: 16px;padding-left: 22px; border-top: 1px solid black; border-bottom: 1px solid black;">
            &nbsp;
          </td>
          <td class="totals_label2" style="border-top: 1px solid black; border-bottom: 1px solid black;">
            SUMAS
          </td>
          <td class="totals_label2" style="font-size: 16px; font-weight: bold; border-top: 1px solid black; border-bottom: 1px solid black;">
            {{number_format($total_debe,2,'.','')}}
          </td>
          <td style="font-size: 16px; border-top: 1px solid black; font-weight: bold; border-bottom: 1px solid black;">
            {{number_format($total_haber,2,'.','')}}
          </td>
        </tr>
      </tbody>
    </table>

    <table class="bordes_padding3" style="margin-top: 20px;"  cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 16px; color: black;">Observacion:</th>
        </tr>
        <tr>
          <td style="font-size: 16px; border-top: 1px solid black; border-bottom: 1px solid black;">
            @if(!is_null($comp_ingreso)) {{$comp_ingreso->comentarios}} @endif
          </td>
        </tr>
      </thead>
    </table>

    <table id="factura_detals" class="bordes_padding3" style="margin-top: 20px;"  cellpadding="0">
      <thead>
        <tr>
          <th style="text-align: left; font-size: 16px; height: 1px; border-right: 1px solid black; color: black; font-weight: bold;">  <label style="margin-left: 3px;" >Concepto: </label> <div class="separator"></div> </th>
          <th style="text-align: left; font-size: 16px; height: 1px; color: black; border-right: 1px solid black; color: black;"> <div class="separator"> </div> <label style="margin-left: 3px;" >Elaborado {{$comp_ingreso->usuario->nombre1[0]}} . {{$comp_ingreso->usuario->apellido1[0]}} </label></th>
          <th style="text-align: left; font-size: 16px; height: 1px; color: black; border-right: 1px solid black; color: black;"> <div class="separator"></div> <label style="margin-left: 3px;" >Aprobado </label> </th>
          <th style="text-align: left; font-size: 16px; height: 1px; color: black; "> <div class="separator"></div> <label style="margin-left: 3px;" >Aprobado </label> </th>
        </tr>
      </thead>
      <tbody>

      </tbody>
    </table>

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
