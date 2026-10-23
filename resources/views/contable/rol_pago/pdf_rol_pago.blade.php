<!DOCTYPE html>
<html lang="en">
<head>

  <title>Rol de Pago</title>
  <style type="text/css">

    #principal{
      width:800px;
    }

    @page { margin-top:250px;margin-bottom:100px; }

      #footer1 { position: fixed; left: 0px; bottom: -120px; right: 0px; height: 130px;}
      #footer2 { position: fixed; left: 380px; bottom: -120px; right: 0px; height: 130px;}
      #footer3 { position: fixed; left: 800px; bottom: -120px; right: 0px; height: 130px;}
      #footer4 { position: fixed; left: 1250px; bottom: -120px; right: 0px; height: 130px;}



      /*#footer2 {margin-top: 115px;}
      #footer { margin-top: 115px;}*/


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
      margin-top: -80px;
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
      height:10px;
      clear: both;
    }

    .round{
      border-radius: 10px;
      border: 1px solid #3d7ba8;
      overflow: hidden;
      padding-bottom: 5px;
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

    .datos_rol
    {
      font-size: 0.8em;
    }


    .mLabel{
      width:25%;
      display: inline-block;
      vertical-align: top;
      font-weight: bold;
      padding-left:15px;
      font-size: 0.8em;

    }
    .mValue{
      width:65%;
      display: inline-block;
      vertical-align: top;
      padding-left:10px;
      font-size: 0.8em;

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
      padding: 2px;
    }

    table th{
       text-align: left;
       color:#3d7ba8;
       font-size: 1em;
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

    .head-title1{
      background-color: #888;
      margin-left: 0px;
      margin-right: 0px;
      height: 30px;
      line-height: 10px;
      color: #cccccc;
      text-align: center;
    }

    .dobra{
       background-color: #D4D0C8;
    }

    .info_empresa{
      width: 50%;
      text-align: center;
    }

    .info_factura{
      width: 60%;
    }

    .saltoDePagina {
        display: block;
        page-break-before: always;
    }
  </style>
</head>


<body lang=ES-EC style="margin-top: 5px;margin-top:0px;padding-top:0px">

<div id="principal" style="margin-top:0px;padding-top:0px; width: 99%;">
  <div id="page_pdf" style="width:49%;border-right:1px solid dashed; display: inline-block;" valign="top">
    <table id="factura_head">
      <tr>
        @if(!is_null($empresa->logo))
        <td class="info_empresa">
          <div style="text-align: left">
            <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:300px;height: 100px">
          </div>
        </td>
        @endif
        <td class="info_factura">
          <div style="text-align: right">
            <label style="font-size: 15px"><b>{{$empresa->nombrecomercial}}</b></label><br/>
            <label style="font-size: 14px">CALLE:{{$empresa->direccion}}</label><br/>
            <label style="font-size: 15px">RUC:<b>{{$empresa->id}}</b></label></br>
          </div>
        </td>
      </tr>
    </table>
      <div class="row head-title">
        <div class="col-md-12">
          <label class="color_texto" for="title">ROL DE PAGO MENSUAL</label>
        </div>
      </div>
      <table id="factura_cliente">
        <tr>
          <td class="info_rol">
            <div class="round">
              <div class="col-md-12">
                <table class="datos_rol">
                  <tr>
                    <td>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                        <div class="mLabel">
                          NOMBRE:
                        </div>
                        <div class="mValue">
                        {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                        <div class="mLabel">
                          ÁREA:
                        </div>
                        <div class="mValue">
                          @if($registro->area == 1)
                          ADMINISTRATIVA
                          @elseif($registro->area == 2)
                          MEDICA
                          @endif
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="row">
                        <div class="mLabel">
                          CÉDULA:
                        </div>
                        <div class="mValue">
                          @if(!is_null($registro->id_user)){{$registro->id_user}}@endif
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row">
                        <div class="mLabel">
                          CARGO:
                        </div>
                        <div class="mValue">
                          @if(!is_null($registro->cargo)){{$registro->cargo}}@endif
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      @php
                        $fecha = "";
                      @endphp
                      <div class="row">
                        <div class="mLabel">
                          FECHA:
                        </div>
                        <div class="mValue">
                          @if(!is_null($rol_pago->created_at))
                            @php
                            $dia =  Date('N',strtotime($rol_pago->created_at));
                            $mes =  Date('n',strtotime($rol_pago->created_at));
                            @endphp
                            @php
                              if($dia == '1'){
                                $fecha = 'Lunes';
                              }elseif($dia == '2'){
                                $fecha = 'Martes';
                              }elseif($dia == '3'){
                                $fecha = 'Miércoles';
                              }elseif($dia == '4'){
                                $fecha = 'Jueves';
                              }elseif($dia == '5'){
                                $fecha = 'Viernes';
                              }elseif($dia == '6'){
                                $fecha = 'Sábado';
                              }elseif($dia == '7'){
                                $fecha = 'Domingo';
                              }
                              $fecha = $fecha.' '.substr($rol_pago->created_at,8,2).' de ';
                              if($mes == '1'){
                                $fecha = $fecha.'Enero';
                              }elseif($mes == '2'){
                                $fecha = $fecha.'Febrero';
                              }elseif($mes == '3'){
                                $fecha = $fecha.'Marzo';
                              }elseif($mes == '4'){
                                $fecha = $fecha.'Abril';
                              }elseif($mes == '5'){
                                $fecha = $fecha.'Mayo';
                              }elseif($mes == '6'){
                                $fecha = $fecha.'Junio';
                              }elseif($mes == '7'){
                                $fecha = $fecha.'Julio';
                              }elseif($mes == '8'){
                                $fecha = $fecha.'Agosto';
                              }elseif($mes == '9'){
                                $fecha = $fecha.'Septiembre';
                              }elseif($mes == '10'){
                                $fecha = $fecha.'Octubre';
                              }elseif($mes == '11'){
                                $fecha = $fecha.'Noviembre';
                              }elseif($mes == '12'){
                                $fecha = $fecha.'Diciembre';
                              }
                            $fecha = $fecha.' del '.substr($rol_pago->created_at,0,4);
                            @endphp
                            {{$fecha}}
                          @endif
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                        <div class="mLabel">
                          ROL:
                        </div>
                        <div class="mValue">
                          @php
                          $mes =  $rol_pago->mes;
                          $fecha = "";
                          @endphp
                          @php
                          if($mes == '1'){
                              $fecha = $fecha.'Enero';
                            }elseif($mes == '2'){
                              $fecha = $fecha.'Febrero';
                            }elseif($mes == '3'){
                              $fecha = $fecha.'Marzo';
                            }elseif($mes == '4'){
                              $fecha = $fecha.'Abril';
                            }elseif($mes == '5'){
                              $fecha = $fecha.'Mayo';
                            }elseif($mes == '6'){
                              $fecha = $fecha.'Junio';
                            }elseif($mes == '7'){
                              $fecha = $fecha.'Julio';
                            }elseif($mes == '8'){
                              $fecha = $fecha.'Agosto';
                            }elseif($mes == '9'){
                              $fecha = $fecha.'Septiembre';
                            }elseif($mes == '10'){
                              $fecha = $fecha.'Octubre';
                            }elseif($mes == '11'){
                              $fecha = $fecha.'Noviembre';
                            }elseif($mes == '12'){
                              $fecha = $fecha.'Diciembre';
                            }
                            $fecha = $fecha.' del '.$rol_pago->anio;

                          @endphp
                          {{$fecha}}
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
      <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th width="40%" style="font-size: 16px;"><div class="details_title_border_left">DETALLE</div></th>
            <th width="30%" style="font-size: 16px;"><div class="details_title">OBSERVACION</div></th>
            <th width="15%" style="font-size: 16px;"><div class="details_title">INGRESOS</div></th>
            <th width="15%" style="font-size: 16px;"><div class="details_title_border_right">EGRESOS</div></th>
          </tr>
        </thead>
        <tbody id="detalle_rol">
          <tr class="round">
            <td style="font-size: 18px">
              Sueldo Mensual
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              @if(!is_null($detalle_rol->sueldo_mensual))
                {{$detalle_rol->sueldo_mensual}}
              @endif
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @if($detalle_rol->sobre_tiempo50 > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Sobre Tiempo 50%
            </td>
            <td style="font-size: 18px">
            {{$detalle_rol->observacion_horas_extras_50}}
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->sobre_tiempo50}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->sobre_tiempo100 > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Sobre Tiempo 100%
            </td>
            <td style="font-size: 18px">
            {{$detalle_rol->observacion_horas_extras_100}}
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->sobre_tiempo100}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->bonificacion > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Bonificacion
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_bono))
              {{$detalle_rol->observacion_bono}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->bonificacion}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->bono_imputable > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Bono Imputable
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_bonoimp))
              {{$detalle_rol->observacion_bonoimp}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->bono_imputable}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->alimentacion > 0)
          <tr class="round">
            <td style="font-size: 18px">
             Alimentacion
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_alimentacion))
              {{$detalle_rol->observacion_alimentacion}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->alimentacion}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->transporte > 0)
          <tr class="round">
            <td style="font-size: 18px">
             Transporte
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_transporte))
              {{$detalle_rol->observacion_transporte}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->transporte}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->vacaciones > 0)
          <tr class="round">
            <td style="font-size: 18px">Vacaciones</td>
            <td style="font-size: 18px">{{$detalle_rol->observacion_vacaciones}}</td>
            <td style="font-size: 16px;padding-left: 60px">{{$detalle_rol->vacaciones}}</td>
            <td style="font-size: 16px"></td>
          </tr>
          @endif
          @if($detalle_rol->fondo_reserva > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Fondo Reserva
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->fondo_reserva}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->decimo_tercero > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Decimo Tercero
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->decimo_tercero}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->decimo_cuarto > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Decimo Cuarto
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->decimo_cuarto}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->porcentaje_iess > 0)
          <tr class="round">
            <td style="font-size: 18px">
              {{$rol_pago->porcentaje_aportacion}}
            </td>
            <td>
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->porcentaje_iess}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->extension_conyuge > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Extension Conyuge
            </td>
            <td>
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->extension_conyuge}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->seguro_privado > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Seguro Privado
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observ_seg_privado))
              {{$detalle_rol->observ_seg_privado}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->seguro_privado}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->exam_laboratorio > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Examen de Laboratorio
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observ_examlaboratorio))
              {{$detalle_rol->observ_examlaboratorio}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->exam_laboratorio}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->parqueo > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Parqueo
            </td>
            <td style="font-size: 18px">
            
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->parqueo}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->impuesto_renta > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Impuesto a la Renta
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observ_imp_renta))
              {{$detalle_rol->observ_imp_renta}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->impuesto_renta}}
            </td>
          </tr>
          @endif

          @if($detalle_rol->multa > 0)
            <tr class="round">
              <td style="font-size: 18px">
                Multas
              </td>
              <td style="font-size: 18px">
              @if(!is_null($detalle_rol->observacion_multa))
                {{$detalle_rol->observacion_multa}}
              @endif
              </td>
              <td>
              </td>
              <td style="font-size: 16px;padding-left: 60px">
                {{$detalle_rol->multa}}
              </td>
            </tr>
          @endif
          @if($detalle_rol->fond_reserv_cobrar > 0)
          <tr class="round">
            <td style="font-size: 18px">
               Cobro Fondo Reserva
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_fondo_cobrar))
              {{$detalle_rol->observacion_fondo_cobrar}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->fond_reserv_cobrar}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->otros_egresos > 0)
            <tr class="round">
              <td style="font-size: 18px">
                Otros Egresos
              </td>
              <td style="font-size: 18px">
              @if(!is_null($detalle_rol->observacion_otro_egreso))
                {{$detalle_rol->observacion_otro_egreso}}
              @endif
              </td>
              <td>
              </td>
              <td style="font-size: 16px;padding-left: 60px">
                {{$detalle_rol->otros_egresos}}
              </td>
            </tr>
          @endif
          @if($detalle_rol->prestamos_empleado > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Prestamo a Empresa
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_prestamo))
              {{$detalle_rol->observacion_prestamo}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->prestamos_empleado}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->saldo_inicial_prestamo > 0)
          <tr class="round">
            <td style="font-size: 18px">
             Saldo Prestamo
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_saldo_inicial))
              {{$detalle_rol->observacion_saldo_inicial}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->saldo_inicial_prestamo}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->anticipo_quincena > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Anticipo 1era Quincena
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_anticip_quinc))
              {{$detalle_rol->observacion_anticip_quinc}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->anticipo_quincena}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->otro_anticipo > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Otros Anticipos
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_otro_anticip))
              {{$detalle_rol->observacion_otro_anticip}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->otro_anticipo}}
            </td>
          </tr>
          @endif
          <!--Aqui va Foreach de Cuotas_Quirografario-->
          @foreach($lista_cuota_quirog as $value_quiro)
            <tr class="round">
              <td style="font-size: 18px">
                PRESTAMO HIPOTECARIO 
              </td>
              <td style="font-size: 18px">
                {{$value_quiro->detalle_cuota}}
              </td>
              <td>
              </td>
              <td style="font-size: 16px;padding-left: 60px">
                {{$value_quiro->valor_cuota}}
              </td>
            </tr>
          @endforeach
          <!--Aqui va Foreach de Cuotas_hipotecarios-->
          @if(count($lista_cuota_hip) <= 5)
            @foreach($lista_cuota_hip as $value_hip)
              <tr class="round">
                <td style="font-size: 18px">
                PRESTAMO QUIROGRAFARIO
                </td>
              <td style="font-size: 18px">
                {{$value_hip->detalle_cuota}}
              </td>
                <td>
                </td>
                <td style="font-size: 16px;padding-left: 60px">
                  {{$value_hip->valor_cuota}}
                </td>
              </tr>
            @endforeach
          @else
            @php $sum_pq = 0; @endphp
            @foreach($lista_cuota_hip as $value_hip)
              @php $sum_pq += $value_hip->valor_cuota; @endphp
            @endforeach           
            <tr class="round">
                <td style="font-size: 18px">
                PRESTAMOS QUIROGRAFARIOS
                </td>
                <td>
                </td>
                <td>
                </td>
                <td style="font-size: 16px;padding-left: 60px">
                  {{$sum_pq}}
                </td>
            </tr>
          @endif
          <tr class="round2">
            <td class="totals_label2">
              TOTALES
            </td>
            <td>
            </td>
            <td style="font-size: 20px;padding-left: 60px">
              @if(!is_null($detalle_rol->total_ingresos))
              <b>{{$detalle_rol->total_ingresos}}</b>
              @endif
            </td>
            <td style="font-size: 20px;padding-left: 60px">
              @if(!is_null($detalle_rol->total_egresos))
              <b>{{round($detalle_rol->total_egresos,2)}}</b>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
      <div class="separator"></div>
      <div class="totals_wrapper">
          <div class="totals_label">
              NETO A RECIBIR
          </div>
          <div class="totals_value" style="font-size: 20px;">
              @if(!is_null($detalle_rol->neto_recibido))
                <b>{{round($detalle_rol->neto_recibido,2)}}</b>
              @endif
          </div>
      </div>

      @php
      /*
      <!--<table id="detalle_pago_rol" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 16px;"><div class="details_title_border_left">TIPO PAGO</div></th>
            <th style="font-size: 16px;"><div class="details_title">VALOR</div></th>
            <th style="font-size: 16px;"><div class="details_title">CHEQUE</div></th>
            <th style="font-size: 16px;"><div class="details_title">CUENTA</div></th>
            <th style="font-size: 16px;"><div class="details_title">BANCO</div></th>
            <th style="font-size: 16px;"><div class="details_title_border_right">N# CUENTA</div></th>
          </tr>
        </thead>
        <tbody id="detalle_pago">
          <tr class="round">
            <td style="font-size: 20px">
                @if($rol_forma_pago->id_tipo_pago == 1)
                  ACREDITACION
                @elseif($rol_forma_pago->id_tipo_pago == 2)
                  EFECTIVO
                @elseif($rol_forma_pago->id_tipo_pago == 3)
                  CHEQUE
                @endif
            </td>
            <td style="font-size: 16px;padding-left: 18px">
              @if(!is_null($rol_forma_pago->valor))
                {{$rol_forma_pago->valor}}
              @endif
            </td>
            <td style="font-size: 16px">
              @if(!is_null($rol_forma_pago->num_cheque))
                {{$rol_forma_pago->num_cheque}}
              @endif
            </td>
            <td style="font-size: 16px">
                @if($rol_forma_pago->id_tipo_cuenta == 1)
                  AHORRO
                @elseif($rol_forma_pago->id_tipo_cuenta == 2)
                  CORRIENTE
                @endif
            </td>
            <td style="font-size: 16px">
                @if($rol_forma_pago->banco == 1)
                  Banco Pichincha
                @elseif($rol_forma_pago->banco == 2)
                  Banco del Pacífico
                @elseif($rol_forma_pago->banco == 3)
                  Banco Guayaquil
                @elseif($rol_forma_pago->banco == 4)
                  Banco Internacional
                @elseif($rol_forma_pago->banco == 5)
                  Banco Bolivariano
                @elseif($rol_forma_pago->banco == 6)
                  Produbanco
                @elseif($rol_forma_pago->banco == 7)
                  Banco del Austro
                @elseif($rol_forma_pago->banco == 8)
                  Banco Solidario
                @elseif($rol_forma_pago->banco == 9)
                  Banco General Rumiñahui
                @elseif($rol_forma_pago->banco == 10)
                  Banco de Loja
                @endif
            </td>
            <td style="font-size: 16px">
              @if(!is_null($rol_forma_pago->numero_cuenta))
                {{$rol_forma_pago->numero_cuenta}}
              @endif
            </td>
          </tr>
        </tbody>
      </table>-->
      */ @endphp
      <div>
        @if($ct_for_pag !='[]')
            <div class="row head-title">
              <div class="col-md-12">
                <label class="color_texto" for="title">FORMAS DE PAGO</label>
              </div>
            </div>
            <div class="separator"></div>
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
                    @php
                    if(!is_null($value->id_tipo_pago)){
                    $tipo_nomb = Sis_medico\Ct_Rh_Tipo_Pago::where('id',$value->id_tipo_pago)->first();
                    }
                    @endphp
                    @if(!is_null($tipo_nomb))
                    {{$tipo_nomb->tipo}}
                    @endif
                  </td>
                  <td style="font-size: 16px">
                    @if(!is_null($value->valor))
                    {{$value->valor}}
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
        @endif
      </div>

      <div id="footer1">
        <div style="position:relative;font-size:14px;width:25%;">
          <p>
            @if(!is_null($firma_responsable) && !empty($firma_responsable->firma) && file_exists(base_path().'/storage/app/avatars/'.$firma_responsable->firma))
              <img src="{{base_path().'/storage/app/avatars/'.$firma_responsable->firma}}" style="position:absolute;top:-98px;left:50%;width:250px;height:105px;margin-left:-140px;" alt="Firma responsable">
            @endif
            <span style="display:block;height:35px;"></span>
            <hr style="width: 60%;margin-left: 100pt;margin:0 auto">
            <label class="control-label" style="margin-left: 48pt;font-family: 'Helvetica general';font-size: 19px;">FIRMA RESPONSABLE
            </label>
          </p>
        </div>
      </div>

      <div id="footer2">
        <div style="position:relative;font-size:14px;width:35%;">
          <p>
            @if(!is_null($firma_recibi) && !empty($firma_recibi->firma) && file_exists(base_path().'/storage/app/avatars/'.$firma_recibi->firma))
              <img src="{{base_path().'/storage/app/avatars/'.$firma_recibi->firma}}" style="position:absolute;top:-98px;left:50%;width:250px;height:105px;margin-left:-140px;" alt="Firma recibí conforme">
            @endif
            <span style="display:block;height:35px;"></span>
            <hr style="width: 60%;margin-left: 260pt;margin:0 auto">
            <label class="control-label" style="margin-left: 65pt;font-family: 'Helvetica general';font-size: 19px;">RECIBÍ CONFORME
            </label>
          </p>
        </div>
      </div>

  </div>
  <div id="page_pdf2" style="width:49%;  display: inline-block;"  valign="top">
    <table id="factura_head">
      <tr>
          @if(!is_null($empresa->logo))
          <td class="info_empresa">
            <div style="text-align: left">
              <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}" style="width:300px;height: 100px">
            </div>
          </td>
          @endif
          <td class="info_factura">
            <div style="text-align: right">
              <label style="font-size: 15px"><b>{{$empresa->nombrecomercial}}</b></label><br/>
              <label style="font-size: 14px">CALLE:{{$empresa->direccion}}</label><br/>
              <label style="font-size: 15px">RUC:<b>{{$empresa->id}}</b></label></br>
            </div>
          </td>

      </tr>
    </table>
      <!--@if($rol_pago->id_tipo_rol == 2)
        <div class="row head-title">
          <div class="col-md-12">
          <label class="color_texto" for="title">ROL DE PAGO QUINCENAL</label>
          </div>
        </div>
      @elseif($rol_pago->id_tipo_rol == 1)
        <div class="row head-title">
          <div class="col-md-12">
          <label class="color_texto" for="title">ROL DE PAGO MENSUAL</label>
          </div>
        </div>
      @endif-->
      <div class="row head-title">
        <div class="col-md-12">
          <label class="color_texto" for="title">ROL DE PAGO MENSUAL</label>
        </div>
      </div>
      <table id="factura_cliente">
        <tr>
          <td class="info_rol">
            <div class="round">
              <div class="col-md-12">
              <table class="datos_rol">
                  <tr>
                    <td>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                        <div class="mLabel">
                          NOMBRE:
                        </div>
                        <div class="mValue">
                        {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                        <div class="mLabel">
                          ÁREA:
                        </div>
                        <div class="mValue">
                          @if($registro->area == 1)
                          ADMINISTRATIVA
                          @elseif($registro->area == 2)
                          MEDICA
                          @endif
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="row">
                        <div class="mLabel">
                          CÉDULA:
                        </div>
                        <div class="mValue">
                          @if(!is_null($registro->id_user)){{$registro->id_user}}@endif
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row">
                        <div class="mLabel">
                          CARGO:
                        </div>
                        <div class="mValue">
                          @if(!is_null($registro->cargo)){{$registro->cargo}}@endif
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      @php
                        $fecha = "";
                      @endphp
                      <div class="row">
                        <div class="mLabel">
                          FECHA:
                        </div>
                        <div class="mValue">
                          @if(!is_null($rol_pago->created_at))
                            @php
                            $dia =  Date('N',strtotime($rol_pago->created_at));
                            $mes =  Date('n',strtotime($rol_pago->created_at));
                            @endphp
                            @php
                              if($dia == '1'){
                                $fecha = 'Lunes';
                              }elseif($dia == '2'){
                                $fecha = 'Martes';
                              }elseif($dia == '3'){
                                $fecha = 'Miércoles';
                              }elseif($dia == '4'){
                                $fecha = 'Jueves';
                              }elseif($dia == '5'){
                                $fecha = 'Viernes';
                              }elseif($dia == '6'){
                                $fecha = 'Sábado';
                              }elseif($dia == '7'){
                                $fecha = 'Domingo';
                              }
                              $fecha = $fecha.' '.substr($rol_pago->created_at,8,2).' de ';
                              if($mes == '1'){
                                $fecha = $fecha.'Enero';
                              }elseif($mes == '2'){
                                $fecha = $fecha.'Febrero';
                              }elseif($mes == '3'){
                                $fecha = $fecha.'Marzo';
                              }elseif($mes == '4'){
                                $fecha = $fecha.'Abril';
                              }elseif($mes == '5'){
                                $fecha = $fecha.'Mayo';
                              }elseif($mes == '6'){
                                $fecha = $fecha.'Junio';
                              }elseif($mes == '7'){
                                $fecha = $fecha.'Julio';
                              }elseif($mes == '8'){
                                $fecha = $fecha.'Agosto';
                              }elseif($mes == '9'){
                                $fecha = $fecha.'Septiembre';
                              }elseif($mes == '10'){
                                $fecha = $fecha.'Octubre';
                              }elseif($mes == '11'){
                                $fecha = $fecha.'Noviembre';
                              }elseif($mes == '12'){
                                $fecha = $fecha.'Diciembre';
                              }
                            $fecha = $fecha.' del '.substr($rol_pago->created_at,0,4);
                            @endphp
                            {{$fecha}}
                          @endif
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                        <div class="mLabel">
                          ROL:
                        </div>
                        <div class="mValue">
                          @php
                          $mes =  $rol_pago->mes;
                          $fecha = "";
                          @endphp
                          @php
                          if($mes == '1'){
                              $fecha = $fecha.'Enero';
                            }elseif($mes == '2'){
                              $fecha = $fecha.'Febrero';
                            }elseif($mes == '3'){
                              $fecha = $fecha.'Marzo';
                            }elseif($mes == '4'){
                              $fecha = $fecha.'Abril';
                            }elseif($mes == '5'){
                              $fecha = $fecha.'Mayo';
                            }elseif($mes == '6'){
                              $fecha = $fecha.'Junio';
                            }elseif($mes == '7'){
                              $fecha = $fecha.'Julio';
                            }elseif($mes == '8'){
                              $fecha = $fecha.'Agosto';
                            }elseif($mes == '9'){
                              $fecha = $fecha.'Septiembre';
                            }elseif($mes == '10'){
                              $fecha = $fecha.'Octubre';
                            }elseif($mes == '11'){
                              $fecha = $fecha.'Noviembre';
                            }elseif($mes == '12'){
                              $fecha = $fecha.'Diciembre';
                            }
                            $fecha = $fecha.' del '.$rol_pago->anio;

                          @endphp
                          {{$fecha}}
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
      <table id="factura_detalle" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th width="40%" style="font-size: 16px;"><div class="details_title_border_left">DETALLE</div></th>
            <th width="30%" style="font-size: 16px;"><div class="details_title">OBSERVACION</div></th>
            <th style="font-size: 16px;"><div class="details_title">INGRESOS</div></th>
            <th style="font-size: 16px;"><div class="details_title_border_right">EGRESOS</div></th>
          </tr>
        </thead>
        <tbody id="detalle_rol">
          <tr class="round">
            <td style="font-size: 18px">
              Sueldo Mensual
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              @if(!is_null($detalle_rol->sueldo_mensual))
                {{$detalle_rol->sueldo_mensual}}
              @endif
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @if($detalle_rol->sobre_tiempo50 > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Sobre Tiempo 50%
            </td>
            <td style="font-size: 18px">
            {{$detalle_rol->observacion_horas_extras_50}}
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->sobre_tiempo50}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->sobre_tiempo100 > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Sobre Tiempo 100%
            </td>
            <td style="font-size: 18px">
              {{$detalle_rol->observacion_horas_extras_100}}
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->sobre_tiempo100}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->bonificacion > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Bonificacion
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_bono))
              {{$detalle_rol->observacion_bono}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->bonificacion}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->bono_imputable > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Bono Imputable
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_bonoimp))
              {{$detalle_rol->observacion_bonoimp}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->bono_imputable}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->alimentacion > 0)
          <tr class="round">
            <td style="font-size: 18px">
             Alimentacion
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_alimentacion))
              {{$detalle_rol->observacion_alimentacion}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->alimentacion}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->transporte > 0)
          <tr class="round">
            <td style="font-size: 18px">
             Transporte
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_transporte))
              {{$detalle_rol->observacion_transporte}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->transporte}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->vacaciones > 0)
          <tr class="round">
            <td style="font-size: 18px">Vacaciones</td>
            <td style="font-size: 18px">{{$detalle_rol->observacion_vacaciones}}</td>
            <td style="font-size: 16px;padding-left: 60px">{{$detalle_rol->vacaciones}}</td>
            <td style="font-size: 16px"></td>
          </tr>
          @endif
          @if($detalle_rol->fondo_reserva > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Fondo Reserva
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->fondo_reserva}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->decimo_tercero > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Decimo Tercero
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->decimo_tercero}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->decimo_cuarto > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Decimo Cuarto
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->decimo_cuarto}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($detalle_rol->porcentaje_iess > 0)
          <tr class="round">
            <td style="font-size: 18px">
              {{$rol_pago->porcentaje_aportacion}}
            </td>
            <td>
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->porcentaje_iess}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->extension_conyuge > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Extension Conyuge
            </td>
            <td>
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->extension_conyuge}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->seguro_privado > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Seguro Privado
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observ_seg_privado))
              {{$detalle_rol->observ_seg_privado}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->seguro_privado}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->exam_laboratorio > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Examen de Laboratorio
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observ_examlaboratorio))
              {{$detalle_rol->observ_examlaboratorio}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->exam_laboratorio}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->parqueo > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Parqueo
            </td>
            <td style="font-size: 18px">
            
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->parqueo}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->impuesto_renta > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Impuesto a la Renta
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observ_imp_renta))
              {{$detalle_rol->observ_imp_renta}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->impuesto_renta}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->multa > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Multas
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_multa))
              {{$detalle_rol->observacion_multa}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->multa}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->fond_reserv_cobrar > 0)
          <tr class="round">
            <td style="font-size: 18px">
               Cobro Fondo Reserva
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_fondo_cobrar))
              {{$detalle_rol->observacion_fondo_cobrar}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->fond_reserv_cobrar}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->otros_egresos > 0)
            <tr class="round">
              <td style="font-size: 18px">
                Otros Egresos
              </td>
              <td style="font-size: 18px">
              @if(!is_null($detalle_rol->observacion_otro_egreso))
                {{$detalle_rol->observacion_otro_egreso}}
              @endif
              </td>
              <td>
              </td>
              <td style="font-size: 16px;padding-left: 60px">
                {{$detalle_rol->otros_egresos}}
              </td>
            </tr>
          @endif
          @if($detalle_rol->prestamos_empleado > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Prestamo a Empresa
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_prestamo))
              {{$detalle_rol->observacion_prestamo}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->prestamos_empleado}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->saldo_inicial_prestamo > 0)
          <tr class="round">
            <td style="font-size: 18px">
             Saldo Prestamo
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_saldo_inicial))
              {{$detalle_rol->observacion_saldo_inicial}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->saldo_inicial_prestamo}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->anticipo_quincena > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Anticipo 1era Quincena
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_anticip_quinc))
              {{$detalle_rol->observacion_anticip_quinc}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->anticipo_quincena}}
            </td>
          </tr>
          @endif
          @if($detalle_rol->otro_anticipo > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Otros Anticipos
            </td>
            <td style="font-size: 18px">
            @if(!is_null($detalle_rol->observacion_otro_anticip))
              {{$detalle_rol->observacion_otro_anticip}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$detalle_rol->otro_anticipo}}
            </td>
          </tr>
          @endif
          <!--Aqui va Foreach de Cuotas_Quirografario-->
          @foreach($lista_cuota_quirog as $value_quiro)
            <tr class="round">
              <td style="font-size: 18px">
                PRESTAMO HIPOTECARIO
              </td>
              <td style="font-size: 18px">
                {{$value_quiro->detalle_cuota}}
              </td>
              <td>
              </td>
              <td style="font-size: 16px;padding-left: 60px">
                {{$value_quiro->valor_cuota}}
              </td>
            </tr>
          @endforeach
          <!--Aqui va Foreach de Cuotas_hipotecarios-->
          @if(count($lista_cuota_hip) <= 5)
            @foreach($lista_cuota_hip as $value_hip)
              <tr class="round">
                <td style="font-size: 18px">
                PRESTAMO QUIROGRAFARIO
              </td>
              <td style="font-size: 18px">
                {{$value_hip->detalle_cuota}}
                </td>
                <td>
                </td>
                <td style="font-size: 16px;padding-left: 60px">
                  {{$value_hip->valor_cuota}}
                </td>
              </tr>
            @endforeach
          @else
            @php $sum_pq = 0; @endphp
            @foreach($lista_cuota_hip as $value_hip)
              @php $sum_pq += $value_hip->valor_cuota; @endphp
            @endforeach           
            <tr class="round">
                <td style="font-size: 18px">
                  PRESTAMOS QUIROGRAFARIOS
                </td>
                <td>
                </td>
                <td>
                </td>
                <td style="font-size: 16px;padding-left: 60px">
                  {{$sum_pq}}
                </td>
            </tr>
          @endif
          <tr class="round2">
            <td class="totals_label2">
              TOTALES
            </td>
            <td>
            </td>
            <td style="font-size: 20px;padding-left: 60px">
              @if(!is_null($detalle_rol->total_ingresos))
              <b>{{$detalle_rol->total_ingresos}}</b>
              @endif
            </td>
            <td style="font-size: 20px;padding-left: 60px">
              @if(!is_null($detalle_rol->total_egresos))
              <b>{{round($detalle_rol->total_egresos,2)}}</b>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
      <div class="separator"></div>
      <div class="totals_wrapper">
          <div class="totals_label">
              NETO A RECIBIR
          </div>
          <div class="totals_value" style="font-size: 20px;">
              @if(!is_null($detalle_rol->neto_recibido))
                <b>{{round($detalle_rol->neto_recibido,2)}}</b>
              @endif
          </div>
      </div>
      @php
      /*
      <!--<table id="detalle_pago_rol" border="0" cellpadding="0" cellpadding="0">
        <thead>
          <tr>
            <th style="font-size: 16px;"><div class="details_title_border_left">TIPO PAGO</div></th>
            <th style="font-size: 16px;"><div class="details_title">VALOR</div></th>
            <th style="font-size: 16px;"><div class="details_title">CHEQUE</div></th>
            <th style="font-size: 16px;"><div class="details_title">CUENTA</div></th>
            <th style="font-size: 16px;"><div class="details_title">BANCO</div></th>
            <th style="font-size: 16px;"><div class="details_title_border_right">N# CUENTA</div></th>
          </tr>
        </thead>
        <tbody id="detalle_pago">
          <tr class="round">
            <td style="font-size: 20px">
                @if($rol_forma_pago->id_tipo_pago == 1)
                  ACREDITACION
                @elseif($rol_forma_pago->id_tipo_pago == 2)
                  EFECTIVO
                @elseif($rol_forma_pago->id_tipo_pago == 3)
                  CHEQUE
                @endif
            </td>
            <td style="font-size: 16px;padding-left: 18px">
              @if(!is_null($rol_forma_pago->valor))
                {{$rol_forma_pago->valor}}
              @endif
            </td>
            <td style="font-size: 16px">
              @if(!is_null($rol_forma_pago->num_cheque))
                {{$rol_forma_pago->num_cheque}}
              @endif
            </td>
            <td style="font-size: 16px">
                @if($rol_forma_pago->id_tipo_cuenta == 1)
                  AHORRO
                @elseif($rol_forma_pago->id_tipo_cuenta == 2)
                  CORRIENTE
                @endif
            </td>
            <td style="font-size: 16px">
                @if($rol_forma_pago->banco == 1)
                  Banco Pichincha
                @elseif($rol_forma_pago->banco == 2)
                  Banco del Pacífico
                @elseif($rol_forma_pago->banco == 3)
                  Banco Guayaquil
                @elseif($rol_forma_pago->banco == 4)
                  Banco Internacional
                @elseif($rol_forma_pago->banco == 5)
                  Banco Bolivariano
                @elseif($rol_forma_pago->banco == 6)
                  Produbanco
                @elseif($rol_forma_pago->banco == 7)
                  Banco del Austro
                @elseif($rol_forma_pago->banco == 8)
                  Banco Solidario
                @elseif($rol_forma_pago->banco == 9)
                  Banco General Rumiñahui
                @elseif($rol_forma_pago->banco == 10)
                  Banco de Loja
                @endif
            </td>
            <td style="font-size: 16px">
              @if(!is_null($rol_forma_pago->numero_cuenta))
                {{$rol_forma_pago->numero_cuenta}}
              @endif
            </td>
          </tr>
        </tbody>
      </table>-->
      */
      @endphp
      <div>
        @if($ct_for_pag !='[]')
            <div class="row head-title">
              <div class="col-md-12">
                <label class="color_texto" for="title">FORMAS DE PAGO</label>
              </div>
            </div>
            <div class="separator"></div>
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
                    @php
                    if(!is_null($value->id_tipo_pago)){
                    $tipo_nomb = Sis_medico\Ct_Rh_Tipo_Pago::where('id',$value->id_tipo_pago)->first();
                    }
                    @endphp
                    @if(!is_null($tipo_nomb))
                    {{$tipo_nomb->tipo}}
                    @endif
                  </td>
                  <td style="font-size: 16px">
                    @if(!is_null($value->valor))
                    {{$value->valor}}
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
        @endif
      </div>

      <div id="footer3">
        <div style="position:relative;font-size:14px;width:49%;">
          <p>
            @if(!is_null($firma_responsable) && !empty($firma_responsable->firma) && file_exists(base_path().'/storage/app/avatars/'.$firma_responsable->firma))
              <img src="{{base_path().'/storage/app/avatars/'.$firma_responsable->firma}}" style="position:absolute;top:-98px;left:50%;width:250px;height:105px;margin-left:-140px;" alt="Firma responsable">
            @endif
            <span style="display:block;height:35px;"></span>
            <hr style="width: 60%;margin-left: 200pt;margin:0 auto">
            <label class="control-label" style="margin-left: 45pt;font-family: 'Helvetica general';font-size: 19px;">FIRMA RESPONSABLE
            </label>
          </p>
        </div>
      </div>

      <div id="footer4">
        <div style="position:relative;font-size:14px;width:100%;">
          <p>
            @if(!is_null($firma_recibi) && !empty($firma_recibi->firma) && file_exists(base_path().'/storage/app/avatars/'.$firma_recibi->firma))
              <img src="{{base_path().'/storage/app/avatars/'.$firma_recibi->firma}}" style="position:absolute;top:-98px;left:50%;width:250px;height:105px;margin-left:-140px;" alt="Firma recibí conforme">
            @endif
            <span style="display:block;height:35px;"></span>
            <hr style="width: 60%;margin-left: 220pt;margin:0 auto">
            <label class="control-label" style="margin-left: 45pt;font-family: 'Helvetica general';font-size: 19px;">RECIBÍ CONFORME
            </label>
          </p>
        </div>
      </div>
  </div>
  @if(count($lista_cuota_hip) > 5)
    <div class="saltoDePagina"></div>
    <div class="row head-title">
      <div class="col-md-12">
        <label class="color_texto" for="title">Detalle Prestamos Quirografarios</label>
      </div>
    </div>
    <table border="0" cellpadding="0" cellpadding="0">
      <thead>
        <tr>
          <th style="font-size: 16px;"><div class="details_title">DETALLE</div></th>
          <th style="font-size: 16px;"><div class="details_title">VALOR</div></th>
        </tr>
      </thead>
      <tbody id="detalle_rol">
        @php $sum_pqt = 0; @endphp
        @foreach($lista_cuota_hip as $value_hip)
          @php $sum_pqt += $value_hip->valor_cuota; @endphp
          <tr class="round">
            <td style="font-size: 18px">
              {{$value_hip->detalle_cuota}}
            </td>
            <td style="font-size: 16px;padding-left: 30px;">
              {{$value_hip->valor_cuota}}
            </td>
          </tr> 
        @endforeach
      </tbody>
      <thead>
        <tr>
          <th style="font-size: 18px"><div class="details_title">TOTAL</div></th>
          <th style="font-size: 18px"><div class="details_title">{{$sum_pqt}}</div></th>
        </tr>
      </thead>
    </table>
  @endif
</div>
</body>
</html>
