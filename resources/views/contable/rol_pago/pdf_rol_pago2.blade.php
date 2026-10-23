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

    #page_pdf{
      width:800px;
      padding-right: 20px;
    }

    #page_pdf2{
      width:800px;
      padding-left: 20px;
      
    }

    
    #factura_head,#factura_cliente,#factura_detalle{
      width: 100%;
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
  </style>  
</head>
@if(empty($rol_det_consulta))
 <h1>No hay Roles por Mostrar</h1>
@else

@foreach ($rol_det_consulta as $value)
<body lang=ES-EC style="margin-top: 5px;margin-top:0px;padding-top:0px">
<div id="principal" style="margin-top:0px;padding-top:0px; width: 99%;">
  @for($doble = 0; $doble <2 ; $doble++)
  @if($doble==0)
    <div id="page_pdf" style="width:49%;border-right:1px solid dashed; display: inline-block;" valign="top">
  @elseif($doble == 1)
    <div id="page_pdf2" style="width:49%;  display: inline-block;"  valign="top">
  @endif
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
      <!--@if($value->id_tipo_rol == 2)
        <div class="row head-title">
          <div class="col-md-12">
          <label class="color_texto" for="title">ROL DE PAGO QUINCENAL</label>
          </div>
        </div>
      @elseif($value->id_tipo_rol == 1)
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
                        {{$value->nombre1}} {{$value->nombre2}} {{$value->apellido1}} {{$value->apellido2}}
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                        <div class="mLabel">
                          ÁREA:
                        </div>
                        <div class="mValue">
                          @if($value->area == 1)
                          ADMINISTRATIVA
                          @elseif($value->area == 2)
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
                          @if(!is_null($value->usuario)){{$value->usuario}}@endif
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row">
                        <div class="mLabel">
                          CARGO:
                        </div>
                        <div class="mValue">
                          @if(!is_null($value->cargo)){{$value->cargo}}@endif
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
                          @if(!is_null($value->fecha_elaboracion))
                            @php
                            $dia =  Date('N',strtotime($value->fecha_elaboracion));
                            $mes =  Date('n',strtotime($value->fecha_elaboracion));
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
                              $fecha = $fecha.' '.substr($value->fecha_elaboracion,8,2).' de ';
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
                            $fecha = $fecha.' del '.substr($value->fecha_elaboracion,0,4);
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
                          $mes =  $value->mes;
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
                            $fecha = $fecha.' del '.$value->anio;
                            
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
              @if(!is_null($value->sueldo_mensual))
                {{$value->sueldo_mensual}}
              @endif
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @if($value->sobre_tiempo50 > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Sobre Tiempo 50%
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->sobre_tiempo50}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($value->sobre_tiempo100 > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Sobre Tiempo 100%
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->sobre_tiempo100}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($value->bonificacion > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Bonificacion
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observacion_bono))
              {{$value->observacion_bono}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->bonificacion}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($value->bono_imputable > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Bono Imputable
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->bono_imputable))
              {{$value->bono_imputable}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->bono_imputable}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($value->alimentacion > 0)
          <tr class="round">
            <td style="font-size: 18px">
             Alimentacion
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observacion_alimentacion))
              {{$value->observacion_alimentacion}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->alimentacion}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($value->transporte > 0)
          <tr class="round">
            <td style="font-size: 18px">
             Transporte
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observacion_transporte))
              {{$value->observacion_transporte}}
            @endif
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->transporte}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($value->vacaciones > 0)
          <tr class="round">
            <td style="font-size: 18px">Vacaciones</td>
            <td style="font-size: 18px">{{$value->observacion_vacaciones}}</td>
            <td style="font-size: 16px;padding-left: 60px">{{$value->vacaciones}}</td>
            <td style="font-size: 16px"></td>
          </tr>
          @endif
          @if($value->fondo_reserva > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Fondo Reserva
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->fondo_reserva}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($value->decimo_tercero > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Decimo Tercero
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->decimo_tercero}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($value->decimo_cuarto > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Decimo Cuarto
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->decimo_cuarto}}
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          @endif
          @if($value->porcentaje_iess > 0)
          <tr class="round">
            <td style="font-size: 18px">
              IESS 9.45%
            </td>
            <td>
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->porcentaje_iess}}
            </td>
          </tr>
          @endif
          @if($value->seguro_privado > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Seguro Privado
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observ_seg_privado))
              {{$value->observ_seg_privado}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->seguro_privado}}
            </td>
          </tr>
          @endif
          @if($value->exam_laboratorio > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Examen de Laboratorio
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observ_examlaboratorio))
              {{$value->observ_examlaboratorio}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->exam_laboratorio}}
            </td>
          </tr>
          @endif
          @if($value->impuesto_renta > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Inpuesto a la Renta
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observ_imp_renta))
              {{$value->observ_imp_renta}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->impuesto_renta}}
            </td>
          </tr>
          @endif
          
          @if($value->multa > 0)
            <tr class="round">
              <td style="font-size: 18px">
                Multas
              </td>
              <td style="font-size: 18px">
              @if(!is_null($value->observacion_multa))
                {{$value->observacion_multa}}
              @endif
              </td>
              <td>
              </td>
              <td style="font-size: 16px;padding-left: 60px">
                {{$value->multa}}
              </td>
            </tr>
          @endif
          @if($value->fond_reserv_cobrar > 0)
          <tr class="round">
            <td style="font-size: 18px">
               Cobro Fondo Reserva
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observacion_fondo_cobrar))
              {{$value->observacion_fondo_cobrar}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->fond_reserv_cobrar}}
            </td>
          </tr>
          @endif
          @if($value->otros_egresos > 0)
            <tr class="round">
              <td style="font-size: 18px">
                Otros Egresos
              </td>
              <td style="font-size: 18px">
              @if(!is_null($value->observacion_otro_egreso))
                {{$value->observacion_otro_egreso}}
              @endif
              </td>
              <td>
              </td>
              <td style="font-size: 16px;padding-left: 60px">
                {{$value->otros_egresos}}
              </td>
            </tr>
          @endif
          @if($value->prestamo_empleado > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Prestamo a Empresa
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observacion_prestamo))
              {{$value->observacion_prestamo}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->prestamo_empleado}}
            </td>
          </tr>
          @endif
          @if($value->saldo_inicial_prestamo > 0)
          <tr class="round">
            <td style="font-size: 18px">
             Saldo Prestamo
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observacion_saldo_inicial))
              {{$value->observacion_saldo_inicial}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->saldo_inicial_prestamo}}
            </td>
          </tr>
          @endif
          @if($value->anticipo_quincena > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Anticipo 1era Quincena
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observacion_anticip_quinc))
              {{$value->observacion_anticip_quinc}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->anticipo_quincena}}
            </td>
          </tr>
          @endif
          @if($value->otro_anticipo > 0)
          <tr class="round">
            <td style="font-size: 18px">
              Otros Anticipos
            </td>
            <td style="font-size: 18px">
            @if(!is_null($value->observacion_otro_anticip))
              {{$value->observacion_otro_anticip}}
            @endif
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              {{$value->otro_anticipo}}
            </td>
          </tr>
          @endif
          <!--Aqui va Foreach de Cuotas_Quirografario-->
          @foreach($lista_cuota_quirog as $value_quiro)
           @if($value_quiro->id_rol == $value->id_rol)
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
            @endif
          @endforeach 
          <!--Aqui va Foreach de Cuotas_hipotecarios-->
          @foreach($lista_cuota_hip as $value_hip)
            @if($value_hip->id_rol == $value->id_rol)
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
            @endif
          @endforeach 
          <tr class="round2">
            <td class="totals_label2">
              TOTALES
            </td>
            <td>
            </td>
            <td style="font-size: 20px;padding-left: 60px">
              @if(!is_null($value->total_ingresos))
              <b>{{$value->total_ingresos}}</b>
              @endif
            </td>
            <td style="font-size: 20px;padding-left: 60px">
              @if(!is_null($value->total_egresos))
              <b>{{round($value->total_egresos,2)}}</b>
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
              @if(!is_null($value->neto_recibido))
                <b>{{round($value->neto_recibido,2)}}</b>
              @endif
          </div>
      </div>
      @php
        $rol_form_pag = Sis_medico\Ct_Rol_Forma_Pago::where('id_rol_pago',$value->id_rol_pag)->where('estado','1')->get();
      @endphp
   
      @if(count($rol_form_pag)>0)
          <div class="row head-title">
            <div class="col-md-12">
              <label class="color_texto" for="title">FORMAS DE PAGO</label>
            </div>
          </div>
          <div class="separator"></div>
          <table id="detalle_pago_rol" border="0" cellpadding="0" cellpadding="0">
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
                @foreach($rol_form_pag as $values)
                <tr class="round">
                  <td style="font-size: 16px">
                    @php
                    if(!is_null($values->id_tipo_pago)){
                    $tipo_nomb = Sis_medico\Ct_Rh_Tipo_Pago::where('id',$values->id_tipo_pago)->first();
                    }
                    @endphp
                    @if(!is_null($tipo_nomb))
                    {{$tipo_nomb->tipo}}
                    @endif
                  </td>
                  <td style="font-size: 16px">
                    @if(!is_null($values->valor))
                    {{$values->valor}}
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
          </table>
      @endif


      @if($doble ==0)
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
      @elseif($doble == 1)
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
      @endif
  </div>
  
  @endfor
</div>
</body>
@endforeach
@endif
</html>
