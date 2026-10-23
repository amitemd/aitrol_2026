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
            <label style="font-size: 14px">{{trans('contableM.CALLE')}}:{{$empresa->direccion}}</label><br/>
            <label style="font-size: 15px">{{trans('contableM.ruc')}}:<b>{{$empresa->id}}</b></label></br>
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
                           {{trans('contableM.fecha')}}
                        </div>
                        <div class="mValue">
                          @if(!is_null($decimo->created_at))
                            @php
                            $dia =  Date('N',strtotime($decimo->created_at));
                            $mes =  Date('n',strtotime($decimo->created_at));
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
                              $fecha = $fecha.' '.substr($decimo->created_at,8,2).' de ';
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
                            $fecha = $fecha.' del '.substr($decimo->anio,0,4);
                            @endphp
                            {{$fecha}}
                          @endif
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                        <div class="mLabel">
                          Periodo Decimo:
                        </div>
                        <div class="mValue">
                          @php
                          
                          $mes_inicio =  $periodo->inicio_decimo_cuarto;
                          $mes_fin = $periodo->fin_decimo_cuarto;
                          $fecha = "";
                          $fecha1 = "";
                          $fecha2  = "";               
                          @endphp
                          @php
                          if($mes_inicio == '1'){
                              $fecha1 = $fecha1.'Enero';
                            }elseif($mes_inicio == '2'){
                              $fecha1 = $fecha1.'Febrero';
                            }elseif($mes_inicio == '3'){
                              $fecha1 = $fecha1.'Marzo';
                            }elseif($mes_inicio == '4'){
                              $fecha1 = $fecha1.'Abril';
                            }elseif($mes_inicio == '5'){
                              $fecha1 = $fecha1.'Mayo';
                            }elseif($mes_inicio == '6'){
                              $fecha1 = $fecha1.'Junio';
                            }elseif($mes_inicio == '7'){
                              $fecha1 = $fecha1.'Julio';
                            }elseif($mes_inicio == '8'){
                              $fecha1 = $fecha1.'Agosto';
                            }elseif($mes_inicio == '9'){
                              $fecha1 = $fecha1.'Septiembre';
                            }elseif($mes_inicio == '10'){
                              $fecha1 = $fecha1.'Octubre';
                            }elseif($mes_inicio == '11'){
                              $fecha1 = $fecha1.'Noviembre';
                            }elseif($mes_inicio == '12'){
                              $fecha1 = $fecha1.'Diciembre';
                            }
                            
                            if($mes_fin == '1'){
                              $fecha2 = $fecha2.'Enero';
                            }elseif($mes_fin == '2'){
                              $fecha2 = $fecha2.'Febrero';
                            }elseif($mes_fin == '3'){
                              $fecha2 = $fecha2.'Marzo';
                            }elseif($mes_fin == '4'){
                              $fecha2 = $fecha2.'Abril';
                            }elseif($mes_fin == '5'){
                              $fecha2 = $fecha2.'Mayo';
                            }elseif($mes_fin == '6'){
                              $fecha2 = $fecha2.'Junio';
                            }elseif($mes_fin == '7'){
                              $fecha2 = $fecha2.'Julio';
                            }elseif($mes_fin == '8'){
                              $fecha2 = $fecha2.'Agosto';
                            }elseif($mes_fin == '9'){
                              $fecha2 = $fecha2.'Septiembre';
                            }elseif($mes_fin == '10'){
                              $fecha2 = $fecha2.'Octubre';
                            }elseif($mes_fin == '11'){
                              $fecha2 = $fecha2.'Noviembre';
                            }elseif($mes_fin == '12'){
                              $fecha2 = $fecha2.'Diciembre';
                            }
                            $fecha = $fecha1 .' a '. $fecha2 .' del '. $decimo->anio;

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
            <th width="40%" style="font-size: 16px;"><div class="details_title_border_left">{{trans('contableM.detalle')}}</div></th>
            <th width="30%" style="font-size: 16px;"><div class="details_title">{{trans('contableM.observaciones')}}</div></th>
            <th width="15%" style="font-size: 16px;"><div class="details_title">INGRESOS</div></th>
            <th width="15%" style="font-size: 16px;"><div class="details_title_border_right">EGRESOS</div></th>
          </tr>
        </thead>
        <tbody id="detalle_rol">
          <tr class="round">
            <td style="font-size: 18px">
              Decimo Cuarto
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              @if(!is_null($decimo->valor_pago))
                {{$decimo->valor_pago}}
              @endif
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          <tr class="round2">
            <td class="totals_label2">
              TOTALES
            </td>
            <td>
            </td>
            <td style="font-size: 20px;padding-left: 60px">
              @if(!is_null($decimo->valor_pago))
              <b>{{$decimo->valor_pago}}</b>
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
              @if(!is_null($decimo->valor_pago))
                <b>{{round($decimo->valor_pago,2)}}</b>
              @endif
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
            <label style="font-size: 14px">{{trans('contableM.CALLE')}}:{{$empresa->direccion}}</label><br/>
            <label style="font-size: 15px">{{trans('contableM.ruc')}}:<b>{{$empresa->id}}</b></label></br>
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
                               {{trans('contableM.fecha')}}
                            </div>
                            <div class="mValue">
                              @if(!is_null($decimo->created_at))
                                @php
                                $dia =  Date('N',strtotime($decimo->created_at));
                                $mes =  Date('n',strtotime($decimo->created_at));
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
                                  $fecha = $fecha.' '.substr($decimo->created_at,8,2).' de ';
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
                                $fecha = $fecha.' del '.substr($decimo->anio,0,4);
                                @endphp
                                {{$fecha}}
                              @endif
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="row" style="padding-bottom: 0px;margin-bottom:0px">
                            <div class="mLabel">
                              Periodo Decimo:
                            </div>
                            <div class="mValue">
                              @php
                              
                              $mes_inicio =  $periodo->inicio_decimo_cuarto;
                              $mes_fin = $periodo->fin_decimo_cuarto;
                              $fecha = "";
                              $fecha1 = "";
                              $fecha2  = "";               
                              @endphp
                              @php
                              if($mes_inicio == '1'){
                                  $fecha1 = $fecha1.'Enero';
                                }elseif($mes_inicio == '2'){
                                  $fecha1 = $fecha1.'Febrero';
                                }elseif($mes_inicio == '3'){
                                  $fecha1 = $fecha1.'Marzo';
                                }elseif($mes_inicio == '4'){
                                  $fecha1 = $fecha1.'Abril';
                                }elseif($mes_inicio == '5'){
                                  $fecha1 = $fecha1.'Mayo';
                                }elseif($mes_inicio == '6'){
                                  $fecha1 = $fecha1.'Junio';
                                }elseif($mes_inicio == '7'){
                                  $fecha1 = $fecha1.'Julio';
                                }elseif($mes_inicio == '8'){
                                  $fecha1 = $fecha1.'Agosto';
                                }elseif($mes_inicio == '9'){
                                  $fecha1 = $fecha1.'Septiembre';
                                }elseif($mes_inicio == '10'){
                                  $fecha1 = $fecha1.'Octubre';
                                }elseif($mes_inicio == '11'){
                                  $fecha1 = $fecha1.'Noviembre';
                                }elseif($mes_inicio == '12'){
                                  $fecha1 = $fecha1.'Diciembre';
                                }
                                
                                if($mes_fin == '1'){
                                  $fecha2 = $fecha2.'Enero';
                                }elseif($mes_fin == '2'){
                                  $fecha2 = $fecha2.'Febrero';
                                }elseif($mes_fin == '3'){
                                  $fecha2 = $fecha2.'Marzo';
                                }elseif($mes_fin == '4'){
                                  $fecha2 = $fecha2.'Abril';
                                }elseif($mes_fin == '5'){
                                  $fecha2 = $fecha2.'Mayo';
                                }elseif($mes_fin == '6'){
                                  $fecha2 = $fecha2.'Junio';
                                }elseif($mes_fin == '7'){
                                  $fecha2 = $fecha2.'Julio';
                                }elseif($mes_fin == '8'){
                                  $fecha2 = $fecha2.'Agosto';
                                }elseif($mes_fin == '9'){
                                  $fecha2 = $fecha2.'Septiembre';
                                }elseif($mes_fin == '10'){
                                  $fecha2 = $fecha2.'Octubre';
                                }elseif($mes_fin == '11'){
                                  $fecha2 = $fecha2.'Noviembre';
                                }elseif($mes_fin == '12'){
                                  $fecha2 = $fecha2.'Diciembre';
                                }
                                $fecha = $fecha1 .' a '. $fecha2 .' del '. $decimo->anio;
    
                              @endphp
                              {{$fecha}}
                            </div>
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
            <th width="40%" style="font-size: 16px;"><div class="details_title_border_left">{{trans('contableM.detalle')}}</div></th>
            <th width="30%" style="font-size: 16px;"><div class="details_title">{{trans('contableM.observaciones')}}</div></th>
            <th width="15%" style="font-size: 16px;"><div class="details_title">INGRESOS</div></th>
            <th width="15%" style="font-size: 16px;"><div class="details_title_border_right">EGRESOS</div></th>
          </tr>
        </thead>
        <tbody id="detalle_rol">
          <tr class="round">
            <td style="font-size: 18px">
              Decimo Cuarto
            </td>
            <td>
            </td>
            <td style="font-size: 16px;padding-left: 60px">
              @if(!is_null($decimo->valor_pago))
                {{$decimo->valor_pago}}
              @endif
            </td>
            <td style="font-size: 16px">
            </td>
          </tr>
          <tr class="round2">
            <td class="totals_label2">
              TOTALES
            </td>
            <td>
            </td>
            <td style="font-size: 20px;padding-left: 60px">
              @if(!is_null($decimo->valor_pago))
              <b>{{$decimo->valor_pago}}</b>
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
              @if(!is_null($decimo->valor_pago))
                <b>{{round($decimo->valor_pago,2)}}</b>
              @endif
          </div>
      </div>

</body>
</html>
