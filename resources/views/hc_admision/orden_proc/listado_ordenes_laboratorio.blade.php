@extends('laboratorio.orden.base')
@section('action-content')


<style type="text/css"> 
  .parent{
      overflow-y:scroll;
      height: 600px;
  }
  .parent::-webkit-scrollbar {
      width: 8px;
  } /* this targets the default scrollbar (compulsory) */
  .parent::-webkit-scrollbar-thumb {
      background: #3c8dbc;
      border-radius: 10px;
  }
  .parent::-webkit-scrollbar-track {
    width: 10px;
      background-color: #3c8dbc;
      box-shadow: inset 0px 0px 0px 3px #3c8dbc;
  }
  .contenedor2{
        padding-left: 15px; 
        padding-right: 60px; 
        padding-top: 20px;
        padding-bottom: 0px;
        background-color: #FFFFFF;
        margin-left: 0px;
    }
    p {
      font-size: 11px;
    }

</style>

<div class="container-fluid">
  <div class="row ">
    <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
      <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">
        <div class="row">
          <div class="col-md-9" style="padding-left:460px">
              <h1 style="font-size: 15px; margin:0; color: white;padding-top: 12px;">
                <b>HISTORIAL ORDENES DE LABORATORIO</b>
              </h1> 
          </div>
          <br>
          @if(!is_null($paciente))
            @php
                $xedad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age; 
            @endphp 
            <div class="row"> 
              <div class="col-md-9" style="padding-top: 15px">
                <h1 style="font-size: 14px; margin:0;color: white;padding-left:20px" >
                  <b>Bienvenido al Sistema de Resultados de Exámenes de Laboratorio: {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif 
                    {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
                    </b>
                </h1>
              </div>
              <div class="col-md-3" style="padding-top: 15px">
                <h1 style="font-size: 14px; margin:0;color: white;padding-left: 10px" >
                  <b>
                    EDAD: {{$xedad}} AÑOS
                  </b>
                </h1>
              </div> 
            </div>  
          @endif    
          <br>
        </div>
        <div>
          <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
            <div class="parent" style="background-color: #ffffff">
              <div style=" margin-right: 30px;">
                @foreach($ordenes as $value)
                  @php
                    if(!is_null($value->fecha_orden)){
                          $fecha_r =  Date('Y-m-d',strtotime($value->fecha_orden));
                    }
                    $xorden = Sis_medico\Examen_Orden::find($value->id);
                    $resultados = $xorden->resultados;
                  @endphp
                  <div class="box" style="border: 2px solid #4682B4; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                    <div class="box-header with-border" style=" text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                      @if(!is_null($value))
                        @php 
                          $dia =  Date('N',strtotime($value->fecha_orden)); 
                          $mes =  Date('n',strtotime($value->fecha_orden)); 
                        @endphp
                          <span> <b style="font-family: 'Helvetica';" class="box-title">
                        @if($dia == '1') Lunes 
                           @elseif($dia == '2') Martes
                           @elseif($dia == '3') Miércoles 
                           @elseif($dia == '4') Jueves 
                           @elseif($dia == '5') Viernes 
                           @elseif($dia == '6') Sábado 
                           @elseif($dia == '7') Domingo 
                        @endif 
                          {{substr($value->fecha_orden,8,2)}} de 
                        @if($mes == '1') Enero 
                           @elseif($mes == '2') Febrero 
                           @elseif($mes == '3') Marzo 
                           @elseif($mes == '4') Abril 
                           @elseif($mes == '5') Mayo 
                           @elseif($mes == '6') Junio 
                           @elseif($mes == '7') Julio 
                           @elseif($mes == '8') Agosto 
                           @elseif($mes == '9') Septiembre 
                           @elseif($mes == '10') Octubre 
                           @elseif($mes == '11') Noviembre 
                           @elseif($mes == '12') Diciembre 
                        @endif 
                          del {{substr($value->fecha_orden,0,4)}}</b></span>
                      @endif              
                      <div class="pull-right box-tools ">
                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                        <i class="fa fa-minus"></i>
                        </button>
                      </div>
                    </div>
                    <div class="box-body">
                      <div style="margin-left: 0px;margin-bottom: 10px; margin-right: 10px">
                        <div class="col-12">
                          <div class="row">
                            <div class="col-md-8 col-12" style="border: 2px solid #4682B4;margin-left: 10px;margin-right: 10px;padding-right: 0px;padding-left: 0px;border-radius: 10px;background-color: white; height: 30%; margin-bottom: 10px">
                              <div class="col-12"  style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4; text-align: center ">
                                  <label class="box-title" style="background-color: #4682B4;  font-size: 16px;">
                                  Detalle de la Orden de Laboratorio
                                  </label>
                              </div>
                              <br>
                              <div class="row">
                                <!--Obtenemos el nombre del Doctor de cada Orden de Laboratorio-->
                                <div class="form-group col-12" style="padding-left:24px">
                                  <label style="font-family: 'Helvetica general';" for="id_doctor_ieced" class="col-12" >Paciente</label>
                                  <div class="col-12">
                                   {{$value->papellido1}} @if($value->papellido2!='(N/A)'){{$value->papellido2}}@endif  {{$value->pnombre1}} @if($value->pnombre2!='(N/A)'){{$value->pnombre2}}@endif
                                  </div>
                                </div>
                                @if($value->parentesco != 'Principal')
                                <div class="form-group col-12" style="padding-left:24px">
                                  <label style="font-family: 'Helvetica general';" for="id_doctor_ieced" class="col-12" >Parentesco</label>
                                  <div class="col-12">
                                    {{$value->parentesco}}
                                  </div>
                                </div>
                                @endif
                                <div class="form-group col-12" style="padding-left:24px">
                                  <label style="font-family: 'Helvetica general';" for="id_doctor_ieced" class="col-12" >Cédula</label>
                                  <div class="col-12">
                                    @if(!is_null($value->id_paciente))
                                      {{$value->id_paciente}} 
                                    @endif
                                  </div>
                                </div>
                                <div class="form-group col-12" style="padding-left:24px">
                                  <label style="font-family: 'Helvetica general';" for="id_doctor_ieced" class="col-12" >Médico</label>
                                  <div class="col-12">
                                    @foreach ($usuarios as $usuario)
                                        @if($value->id_doctor_ieced==$usuario->id) 
                                          {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}} 
                                          @endif 
                                    @endforeach 
                                  </div>
                                </div>
                                <div class="form-group col-12" style="padding-left:24px">
                                  <label style="font-family: 'Helvetica general';" for="id_doctor_ieced" class="col-12" >Seguro</label>
                                  <div class="col-12">
                                    @if(!is_null($value->snombre))
                                      {{$value->snombre}} 
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <span>Progreso:</span>
                                <br>
                                <div class="progress progress">
                                  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                                  <span id="sp{{$value->id}}" style="color: ##00c0ef;"></span>
                                  </div>
                                </div>

                                <br>
                                <center>
                                  <div class="col-md-12 col-8">
                                    <a target="_blank" onclick="descargar({{$value->id}});" style="width: 100%; color: white; background-color:#4682B4 ; border-radius: 5px; border: 2px solid white;" class="btn btn-info boton-lab">
                                      <span style="color: white" class="glyphicon glyphicon-download-alt"></span> Resultados
                                    </a>
                                  </div>
                                </center>
                            </div>
                          </div>
                        
                        </div>
                      </div>
                      <div class="col-12" style="border-radius: 0px">
                        <div style="border: 2px solid #4682B4;background-color:#FFFFFF; border-radius: 10px;" id="{{$value->id}}">
                          <div class="col-12" class="box-header " style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4; height: 35px; text-align: center;">
                                <label class="box-title" style="background-color: #4682B4;  font-size: 16px">
                                  &nbsp;Listado de ex&aacute;menes de laboratorio
                                </label>
                          </div>
                          <div class="col-12" style="padding: 7px;">
                            <div class="row">
                              @php 
                               $orden = Sis_medico\Examen_Orden::find($value->id);
                               $detalle = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();
                              @endphp
                              @if($orden->seguro->tipo=='0')
                                @php 
                                  $agrupador = Sis_medico\Examen_Agrupador::all(); 
                                @endphp
                              @else
                                @php 
                                  $agrupador = Sis_medico\Examen_Agrupador_labs::all();
                                @endphp
                              @endif
                              @php 
                               $parametros = Sis_medico\Examen_Parametro::orderBy('orden')->get();
                              @endphp
                              <br>
                              @foreach($agrupador as $value)
                                @php  
                                  $i_agrupador=0;
                                  $xycont = 0; 
                                @endphp
                                @foreach($detalle as $value_detalle)
                                  @php 
                                    $i=0; 
                                    $xycont ++; 
                                    if($orden->seguro->tipo == '0' ){
                                       $id_agrupador = $value_detalle->examen->id_agrupador;
                                    }else{
                                      $agrupador_part = DB::table('examen_agrupador_sabana')->where('id_examen',$value_detalle->examen->id)->first();
                                      $id_agrupador = 0;
                                    
                                      if(!is_null($agrupador_part)){
                                        $id_agrupador = $agrupador_part->id_examen_agrupador_labs;
                                      } 
                                    }
                                  @endphp
                                  @if($value_detalle->examen->no_resultado=='0')
                                      @if($id_agrupador == $value->id)
                                        @php 
                                          if($value_detalle->examen->sexo_n_s=='0'){
                                            $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');  
                                          }else{
                                            $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                                          }
                                        @endphp
                               
                                      <div class="col-12" style="width: 99%;padding-left: 12px">
                                          @if($i_agrupador == 0)
                                          <!--Contenedor Nombre Agrupador-->
                                          <div style="text-align: center;border-bottom: 1px solid #4682B4;padding: 0px;background-color: #4682B4;">
                                            <!--Nombre Agrupador-->
                                            <label style="color: white; padding-top: 2px;font-size: 12px;"> {{$value->nombre}}</label>
                                            @php 
                                              $i_agrupador=1;
                                            @endphp  
                                          </div>
                                            <div style="width: 28%;float:left;color: #4682B4;"><b>NOMBRE</b></div>
                                            <div style="width: 19%;float:left;color: #4682B4;"><b>RESULTADO</b></div>
                                            <div style="width: 10%;float:left;color: #4682B4;"><b>UNIDADES</b></div>
                                            <div style="width: 40%;float:left;color: #4682B4;"><b>REFERENCIA</b></div>
                                          <div style="clear:both;"></div> 
                                          @endif
                                          @if($i == 0)
                                            @if($parametro_nuevo->count() > 1)
                                              @if($value_detalle->id_examen!='639')
                                              <div class="col-12" style="border-bottom: 1px solid #4682B4;padding: 0px">
                                                <b >{{$value_detalle->examen->nombre}}</b>
                                              </div>
                                              @endif
                                            @endif  
                                            @php 
                                               $i = 1; 
                                            @endphp
                                          @endif
                                          <div style="clear:both;"></div>  
                                          <div class="col-12">
                                            <table id="example2" style="font-size: 12px;  width: 96%">
                                              <tbody>
                                                @if($value_detalle->id_examen=='661')
                                                  <tr style="font-size: 12px;">
                                                      <td style="width: 20%;"><b>GRADO</b></td>
                                                      <td style="width: 10%;"><b>% CELULAS</b></td>
                                                      <td style="width: 8%;"><b>L.A.P. PUNTOS</b></td>
                                                      <td style="width: 40%;"><b>REFERENCIA</b></td>
                                                  </tr>
                                                @endif
                                                  @if($value_detalle->id_examen=='661')
                                                    @foreach($parametro_nuevo as $value_agrupador)
                                                      @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                                        @php
                                                         $resultado = $orden->resultados->where('id_parametro', $value_agrupador->id)->first();
                                                        @endphp
                                                          <tr role="row">
                                                              <td style="width: 30%;">
                                                                {{$value_agrupador->nombre}}
                                                              </td>
                                                              @php 
                                                                $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                                                $resultado_2 = null;
                                                                if(!is_null($hermano)){
                                                                $resultado_2 = $orden->resultados->where('id_parametro', $hermano->id)->first();
                                                                } 
                                                              @endphp
                                                              <td style="width: 20%;">
                                                                  <div style="word-wrap: break-word;">
                                                                    @if(!is_null($hermano))
                                                                      @if(!is_null($resultado_2)) 
                                                                        @if($resultado_2->certificado=='1') 
                                                                          {{$resultado_2->valor}}
                                                                        @else
                                                                          {{"0"}} 
                                                                        @endif 
                                                                      @endif
                                                                    @endif
                                                                  </div>  
                                                              </td>
                                                              <td style="width: 10%;">
                                                                  @if(!is_null($resultado))
                                                                    {{$resultado->valor}}
                                                                  @else
                                                                    {{"0"}}
                                                                  @endif
                                                              </td>
                                                              <td style="width: 40%;">
                                                                  @if($value_agrupador->texto_referencia == "")
                                                                    {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                                                  @else 
                                                                    <?php echo 
                                                                     $value_agrupador->texto_referencia; 
                                                                    ?> 
                                                                  @endif
                                                              </td>
                                                            </tr>
                                                      @endif    
                                                    @endforeach
                                                    
                                                  @elseif($value_detalle->id_examen=='639')
                                                        <br>
                                                        <b>ALERGIAS A ALIM. POR IGG - 59</b>
                                                        <br>
                                                        
                                                        <div class="col-md-9" style="padding:0;"> 
                                                            @foreach($parametro_nuevo as $value_agrupador)
                                                              @php 
                                                             
                                                                $rvalor=0; 

                                                                $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();

                                                                if(!is_null($resultado)){
                                                                  $rvalor=$resultado->valor;

                                                                }
                                                              

                                                              @endphp
                                                              @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                                                <div class="col-md-6" style="padding:0;">
                                                                  <div class="col-md-1"><p style=" font-size: 10px;">{{$value_agrupador->orden}}</p></div>
                                                                  <div class="col-md-5"><p style=" font-size: 10px;">{{$value_agrupador->nombre}}</p></div>
                                                                  <div class="col-md-1" style="background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;">
                                                                  </div>
                                                                  <div class="col-md-1" style="background-color:#8AB0DB; border: 1px solid #FFF;;text-align: center;">
                                                                  </div>
                                                                  <div class="col-md-1"  style="background-color:#A8A8A8; border: 1px solid #FFF;text-align: center;"> @if($value_agrupador->orden=='48')  <span style="color: white">X</span> @else &nbsp; @endif
                                                                  </div>
                                                                  
                                                                </div>
                                                              @else
                                                                <div class="col-md-6" style="padding:0;">
                                                                    <div class="col-md-1"><p style=" font-size: 10px;">{{$value_agrupador->orden}}</p></div>
                                                                    
                                                                    <div class="col-md-5"><p style=" font-size: 10px;">{{$value_agrupador->nombre}}</p></div>

                                                                    <div class="col-md-1" style="background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;">
                                                                      @if($rvalor==1) @if($resultado->certificado=='1') X @else &nbsp; @endif @else &nbsp; @endif
                                                                    </div>
                                                                    <div class="col-md-1" style="background-color:#8AB0DB; border: 1px solid #FFF;;text-align: center;">@if($rvalor==2) @if($resultado->certificado=='1') X @else &nbsp; @endif @else &nbsp; @endif
                                                                    </div>
                                                                    <div class="col-md-1"  style="background-color:#376EAC; border: 1px solid #FFF;text-align: center;" >@if($rvalor==3) <span style="color: white">@if($resultado->certificado=='1') X @else &nbsp; @endif</span> @else &nbsp; @endif
                                                                    </div>
                                                                    
                                                                </div>
                                                              @endif
                                                            @endforeach 
                                                        </div>
                                                             
                                                        <div class="col-md-3" style="padding:0;">
                                                          <div class="col-md-11" style="background-color: #ACCBEE">Reacción Leve</div>
                                                          <div class="col-md-11" style="background-color: #8AB0DB">Reacción Moderada</div>
                                                          <div class="col-md-11" style="background-color: #376EAC;color: white;">Reacción Fuerte</div>
                                                          <div class="col-md-11" >&nbsp;</div>
                                                          <div class="col-md-11" style="padding:0px;">
                                                            <p>*37 Pescado Blanco Mix: Bacalao y Lenguado.</p>
                                                            <p>*39 Pescado de Agua Dulce Mix: Salmón y Trucha.</p>
                                                            <p>*43 Marisco Mix: Camarón, Langostino, Cangrejo, Langosta, Mejillones.</p>
                                                            <p>*12 Mezclas de pimientos: rojo, verde y amarillo</p>
                                                            <p>*14 Leguminosas: Arverjas, lentejas, fréjol, habas.</p>
                                                            <p>*18 Melón Mix: Melón y Sandía</p>
                                                            <p align="justify"> Si sus resultados indican una reacción elevada al gluten, le recomendamos que evite todos los alimentos que contengan gliadina/gluten, aunque estos alimentos no muestren una respuesta positiva como el trigo, el centeno, la cebada, espelta, kamut, malta, esencia de malta, vinagre de malta, salvado, triticale, dextrina.</p>
                                                            <br> <p>Algunas personas con intolerancia al gluten son sensibles también a la avena. </p>
                                                          </div>      
                                                            
                                                        </div>  
                                                      
                                                  @else
                                                    @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291'))
                                                      @foreach($parametro_nuevo as $value_agrupador)
                                                        @php
                                                          $resultado = $orden->resultados
                                                            ->where('id_parametro', $value_agrupador->id)
                                                            ->first();
                                                        @endphp
                                                       
                                                        @if(!is_null($resultado))
                                                          @if($resultado->certificado=='1')
                                                          
                                                          
                                                            <tr role="row">
                                                                
                                                                <td style="width: 30%;@if($parametro_nuevo->count() == 1) font-weight: 700;padding-left: -3px;font-size: 14px; @endif">
                                                                  {{$value_agrupador->nombre}}
                                                                </td>
                                                                
                                                                <td style="width: 20%;">
                                                                  <div style="word-wrap: break-word;">
                                                                    @if(!is_null($resultado))
                                                                     {{$resultado->valor}}
                                                                    @else
                                                                     {{"0"}}
                                                                    @endif
                                                                  </div>  
                                                                </td>
                                                                <td style="width: 10%;">  {{$value_agrupador->unidad1}}
                                                                </td>
                                                                <td style="width: 40%;">
                                                                  @if($value_agrupador->texto_referencia == "")
                                                                    {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                                                  @else 
                                                                    <?php 
                                                                      echo $value_agrupador->texto_referencia; 
                                                                    ?> 
                                                                  @endif
                                                                </td>
                                                            </tr>
                                                          @endif    
                                                        @endif
                                                      @endforeach

                                                      @if($value_detalle->id_examen == '610' || $value_detalle->id_examen == '615')
                                                        @php
                                                          $sub_resultados = DB::table('examen_sub_resultado')
                                                           ->where('id_orden',$orden->id)
                                                           ->where('estado','1')
                                                           ->where('id_examen',$value_detalle
                                                           ->id_examen)->get();
                                                        @endphp
                                                        <tr>
                                                          <td colspan="4" id="sub_tabla">
                                                            <table class="table table-bordered table-hover dataTable">
                                                              <tbody>
                                                                @foreach($sub_resultados as $sub)
                                                                <tr>
                                                                  <td style="width: 300px !important;">{{$sub->campo1}}</td>
                                                                  <td style="width: 300px !important;">{{$sub->campo2}}</td>
                                                                  <td style="width: 300px !important;">{{$sub->campo3}}</td>
                                                                </tr>
                                                                @endforeach
                                                               
                                                                    
                                                              </tbody>
                                                            </table>  
                                                          </td>
                                                        </tr>
                                                      @endif
                                                    @endif
                                                @endif
                                                </tbody>
                                            </table>
                                          </div>
                                      </div>
                                      @endif          
                                    @endif
                                @endforeach
                              @endforeach
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">

  $(document).ready(function($){
    
    @foreach ($ordenes as $value)

      $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}", 
        
        success: function(data){
          
            if(data.cant_par==0){
              var pct = 0;  
            }else{
              var pct = data.certificados/data.cant_par*100;  
            }
            
            $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$value->id}}').text(Math.round(pct)+"%");
            if(pct < 10){
              $('#td{{$value->id}}').addClass("progress-bar-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$value->id}}').addClass("progress-bar-warning");  
            }else{
              $('#td{{$value->id}}').addClass("progress-bar-success");
            }
          

        },


        error: function(data){
          
           
        }
      });

    @endforeach


  });

  function descargar(id_or){
    
    var cert = $('#sp'+id_or).text();
    
    if(cert=='0%'){
      
      alert("Sin Exámenes Ingresados");
    
    }else{
      
      window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');  
    }
    
  }

</script>

@endsection