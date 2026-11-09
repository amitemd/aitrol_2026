<style type="text/css">
  .parent{
    overflow-y:scroll;
    height: 462px;
  }
  .parent::-webkit-scrollbar{
    width: 8px;
  } /* this targets the default scrollbar (compulsory) */
  .parent::-webkit-scrollbar-thumb{
      background: #004AC1;
      border-radius: 10px;
  }
  .parent::-webkit-scrollbar-track{
    width: 10px;
    background-color: #004AC1; 
    box-shadow: inset 0px 0px 0px 3px #56ABE3;
  }
  .boton-lab{
    font-size: 14px ;
    width: 90%;
    background-color: #004AC1; 
    color: white; 
    text-align: center;
    margin-bottom: 10px;
  } 
</style>
 <!-- Etiqueta container-fluid contenedora para poder ocupar todo el ancho-->
<div id="lab_index"  class="container-fluid" style="padding-left: 8px">
  <div class="row">
    <div class="col-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;">
       <!--Agrupamos la fila en 12 columnas-->
      <div class="col-12 box hc4-standard-module-container" style="margin-left: 4px; margin-right: 0px; padding-right: 0px; padding-left: 0px;">
               <!--Titulo-->
          @include('hc4.partials.module_header', [
              'moduleTitle' => __('hc4.laboratory'),
              'moduleIcon' => asset('/').'hc4/img/iconos/lab.png',
              'paciente' => $paciente
          ])
          <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #56ABE3; margin-left: 0px">
            <!--Hacemos el llamado al estilo parent-->
            <div class="parent" style="background-color: #56ABE3">
              <div style=" margin-right: 30px;">
                 <!-- FOREACH DE LAS ORDENES DE LABORATORIO-->
                @foreach($ordenes as $value)
                  <div class="box" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                    <!--Cabecera-->
                    <div class="box-header with-border" style=" text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                      <!--@if(!is_null($value))
                        @php
                          $fecha = substr($value->fecha_orden,0,10);
                          $invert = explode( '-',$fecha);
                          $fecha_invert = $invert[2]."-".$invert[1]."-".$invert[0]; 
                        @endphp
                         <span> <b style="font-family: 'Helvetica';" class="box-title">{{$fecha_invert}}</b></span>
                      @endif-->

                      @if(!is_null($value))
                        @php 
                        $dia =  Date('N',strtotime($value->fecha_orden)); 
                        $mes =  Date('n',strtotime($value->fecha_orden)); 
                        @endphp
                              
                                <span> <b style="font-family: 'Helvetica';" class="box-title">
                                @if($dia == '1') @lang('hc4.monday')
                                   @elseif($dia == '2') @lang('hc4.tuesday')
                                   @elseif($dia == '3') @lang('hc4.wednesday')
                                   @elseif($dia == '4') @lang('hc4.thursday')
                                   @elseif($dia == '5') @lang('hc4.friday')
                                   @elseif($dia == '6') @lang('hc4.saturday')
                                   @elseif($dia == '7') @lang('hc4.sunday')
                                @endif 
                                  {{substr($value->fecha_orden,8,2)}} @lang('hc4.of')
                                @if($mes == '1') @lang('hc4.january')
                                   @elseif($mes == '2') @lang('hc4.february')
                                   @elseif($mes == '3') @lang('hc4.march')
                                   @elseif($mes == '4') @lang('hc4.april')
                                   @elseif($mes == '5') @lang('hc4.may')
                                   @elseif($mes == '6') @lang('hc4.june')
                                   @elseif($mes == '7') @lang('hc4.july')
                                   @elseif($mes == '8') @lang('hc4.august')
                                   @elseif($mes == '9') @lang('hc4.september')
                                   @elseif($mes == '10') @lang('hc4.october')
                                   @elseif($mes == '11') @lang('hc4.november')
                                   @elseif($mes == '12') @lang('hc4.december')
                                @endif 
                                  @lang('hc4.of') {{substr($value->fecha_orden,0,4)}}</b></span>
                      @endif              


                      <div class="pull-right box-tools ">
                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                        <i class="fa fa-minus"></i>
                        </button>
                      </div>
                    </div>
                    <!--Cuerpo-->
                    <div class="box-body">
                      <div style="margin-left: 0px;margin-bottom: 10px; margin-right: 10px">
                        <div class="col-12">
                          <div class="row ">
                            <div class="col-md-8 col-12" style="border: 2px solid #004AC1;margin-left: 10px;margin-right: 10px;padding-right: 0px;padding-left: 0px;border-radius: 10px;background-color: white; height: 100%; margin-bottom: 10px">
                              <div class="col-12" style="background: transparent; color: #334155; font-family: 'Helvetica general3'; text-align: center; padding: 8px 12px 4px;">
                                  <label class="box-title" style="background: transparent; color: #334155; font-size: 16px; font-weight: 600; margin: 0;">
                                  @lang('hc4.detalleOrdenLaboratorio')
                                  </label>
                              </div>
                              <br>
                              <div class="row">
                                <!--Obtenemos el nombre del Doctor de cada Orden de Laboratorio-->
                                <div class="form-group col-12" style="padding-right: 0px">
                                  <label style="font-family: 'Helvetica general';" for="id_doctor_ieced" class="col-12" >@lang('hc4.medico')</label>
                                  <div class="col-12">
                                    @foreach ($usuarios as $usuario)
                                        @if($value->id_doctor_ieced==$usuario->id) 
                                          {{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}} 
                                          @endif 
                                    @endforeach 
                                  </div>
                                </div>
                                <div class="form-group col-12" style="padding-right: 0px">
                                  <label style="font-family: 'Helvetica general';" for="id_doctor_ieced" class="col-12" >@lang('hc4.seguro')</label>
                                  <div class="col-12">
                                    @if(!is_null($value->snombre))
                                      {{$value->snombre}} 
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                            <div class="col-md-3 col-12">
                                <span>@lang('hc4.progreso'):</span>
                                <br>
                                <div class="progress progress">
                                  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                                  <span id="sp{{$value->id}}" style="color: ##00c0ef;"></span>
                                  </div>
                                </div>
                                <br>
                                <!--<div>
                                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                  <a style="width: 200px; " href="{{ route('descargar.orden', ['id' => $value->id]) }}" class="btn btn-info boton-lab">
                                  <span class="fa fa-download">&nbsp;&nbsp; </span> @lang('hc4.descargarOrden')
                                  </a>
                                </div>-->
                                <center>
                                  <div class="col-md-12 col-8">
                                    <a target="_blank" onclick="descargar({{$value->id}});" style="width: 100%; height: 100%; color: white" class="btn btn-info boton-lab">
                                      <span style="color: white" class="fa fa-download"></span> @lang('hc4.resultados')
                                    </a>
                                  </div>
                                </center>
                            </div>
                            
                          </div>  
                        </div>
                      </div>
                    <!--Contenedor Listado de Examenes de Laboratorio-->
                      <div class="col-12" style="border-radius: 0px">
                        <!--Div que contiene el Contenedor-->
                        <div style="border: 2px solid #004AC1;background-color:#FFFFFF; border-radius: 10px;" id="{{$value->id}}">
                        <!--Div que contiene el Titulo-->
                          <div class="col-12" class="box-header " style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; height: 35px; text-align: center;">
                                <label class="box-title" style="background-color: #004AC1;  font-size: 16px">
                                  &nbsp;@lang('hc4.listadoExamenesLaboratorio')
                                </label>
                          </div>
                          <div class="col-12" style="padding: 7px;">
                             <!--Realizamos la Consulta a la tabla Examen_Detalle-->
                            <div class="row">
                              @php 
                               $orden = Sis_medico\Examen_Orden::find($value->id);
                               $detalle = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();
                               $resultados = $orden->resultados;
                              @endphp
                              @if($orden->seguro->tipo=='0')
                                @php 
                                  $agrupador = Sis_medico\Examen_Agrupador::all(); 
                                @endphp
                              @else
                                @php 
                                  $agrupador = Sis_medico\Examen_Agrupador_labs::orderBy('secuencia')->get();
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
                                      @if($value_detalle->examen->resultado_especial == null)
                                        @php 
                                          if($value_detalle->examen->sexo_n_s=='0'){
                                            $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');  
                                          }else{
                                            $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                                          }
                                        @endphp
                                        <div class="col-12" >
                                            @if($i_agrupador == 0)
                                            <!--Contenedor Nombre Agrupador-->
                                            <div style="text-align: center;border-bottom: 1px solid #004AC1;padding: 0px;background-color: #004AC1;">
                                              <!--Nombre Agrupador-->
                                              <label style="color: white; padding-top: 2px;font-size: 12px;"> {{$value->nombre}}</label>
                                              @php 
                                                $i_agrupador=1;
                                              @endphp  
                                            </div>
                                              <div style="width: 28%;float:left;color: #004AC1;"><b>@lang('hc4.nombre')</b></div>
                                              <div style="width: 18%;float:left;color: #004AC1;"><b>@lang('hc4.resultado')</b></div>
                                              <div style="width: 13%;float:left;color: #004AC1;"><b>@lang('hc4.unidades')</b></div>
                                              <div style="width: 40%;float:left;color: #004AC1;"><b>@lang('hc4.referencia')</b></div>
                                            <div style="clear:both;"></div> 
                                            @endif
                                            @if($i == 0)
                                              @if($parametro_nuevo->count() > 1)
                                                @if($value_detalle->id_examen!='639')
                                                  <div class="col-12" style="border-bottom: 1px solid #004AC1;padding: 0px">
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
                                              <table id="example2" style="font-size: 12px;  width: 100%">
                                                <tbody>
                                                  @if($value_detalle->id_examen=='661')
                                                    <tr style="font-size: 12px;">
                                                        <td style="width: 20%;"><b>@lang('hc4.grado')</b></td>
                                                        <td style="width: 10%;"><b>% @lang('hc4.celulas')</b></td>
                                                        <td style="width: 8%;"><b>L.A.P. PUNTOS</b></td>
                                                        <td style="width: 40%;"><b>@lang('hc4.referencia')</b></td>
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
                                                      <center>
                                                        <b>ALERGIAS A ALIM. POR IGG - 59</b>
                                                      </center>  
                                                      <br>
                                                      <div class="row">
                                                        <div class="col-md-4">
                                                          <div style="background-color: #ACCBEE;font-size: 12px;text-align: center;">@lang('hc4.reaccionLeve')</div>
                                                        </div>
                                                        <div class="col-md-4">
                                                          <div style="background-color: #8AB0DB;font-size: 12px;text-align: center;">@lang('hc4.reaccionModerada')</div>
                                                        </div>
                                                        <div class="col-md-4">
                                                          <div style="background-color: #376EAC;color: white;font-size: 12px;text-align: center;">@lang('hc4.reaccionFuerte')</div>
                                                        </div>
                                                      </div>
                                                      <br>  
                                                      @php $cuenta=0;
                                                      @endphp
                                                      <tr role="row">
                                                        @foreach($parametro_nuevo as $value_agrupador)
                                                          @php 
                                                        
                                                            $rvalor=0; 

                                                            $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();

                                                            if(!is_null($resultado)){
                                                              $rvalor=$resultado->valor;

                                                            }
                                                          

                                                          @endphp
                                                        
                                                          @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                                            @if($cuenta == 0) <tr> @endif  
                                                            <td style="width: 3%; font-size: 10px; padding:0;">
                                                              {{$value_agrupador->sec}}
                                                            </td>
                                                            <td style="width: 12%;  font-size: 10px;padding:0;">
                                                              {{$value_agrupador->nombre}}
                                                            </td>
                                                            <td style="width: 3%;padding:0;" >
                                                            </td>
                                                            <td style="width: 3%;padding:0;">
                                                            </td>
                                                            <td style="width: 3%; background-color:#A8A8A8; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">@if($value_agrupador->orden=='48')  <span style="color: white">x</span> @else &nbsp; @endif
                                                            </td>
                                                            <td style="width: 3%;  font-size: 10px; padding:0;">
                                                              &nbsp;
                                                            </td>
                                                            @php $cuenta++; @endphp
                                                            @if($cuenta == 2)
                                                              </tr>
                                                            @endif  
                                                          @else
                                                            @if($cuenta == 0) <tr> @endif 
                                                            <td style="width: 3%;  font-size: 10px; padding:0;">
                                                              {{$value_agrupador->sec}}
                                                            </td>
                                                            <td style="width: 12%;  font-size: 10px;padding:0;">
                                                              {{$value_agrupador->nombre}}
                                                            </td>
                                                            <td style="width: 3%; background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;font-size: 13px;padding:0;">
                                                              @if($rvalor==1) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif
                                                            </td>
                                                            <td style="width: 3%; background-color:#8AB0DB; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">
                                                              @if($rvalor==2) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif
                                                            </td>
                                                            <td style="width: 3%; background-color:#376EAC; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">
                                                              @if($rvalor==3) <span style="color: white">@if($resultado->certificado=='1') X @endif</span> @else &nbsp; @endif
                                                            </td>
                                                            <td style="width: 3%;  font-size: 10px; padding:0;">
                                                              &nbsp;
                                                            </td>
                                                            @php $cuenta++; @endphp
                                                            @if($cuenta == 2)
                                                              </tr> 
                                                              @php
                                                                $cuenta=0;
                                                              @endphp
                                                            @endif 
                                                          @endif
                                                        @endforeach
                                                        
                                                      </tr>
                                                      <table>
                                                        <tr role="row">
                                                          <br>
                                                          <br>
                                                          <div>*19 Pescado Blanco Mix: Bacalao y Lenguado.</div>
                                                          <div>*20 Pescado de Agua Dulce Mix: Salmón y Trucha.</div>
                                                          <div>*22 Marisco Mix: Camarón, Langostino, Cangrejo, Langosta, Mejillones.</div>
                                                          <div>*30 Mezclas de pimientos: rojo, verde y amarillo</div>
                                                          <div>*31 Leguminosas: Arverjas, lentejas, fréjol, habas.</div>
                                                          <div>*33 Melón Mix: Melón y Sandía</div>
                                                          <div style="text-align: justify;"> Si sus resultados indican una reacción elevada al gluten, le recomendamos que evite todos los alimentos que contengan gliadina/gluten, aunque estos alimentos no muestren una respuesta positiva como el trigo, el centeno, la cebada, espelta, kamut, malta, esencia de malta, vinagre de malta, salvado, triticale, dextrina.</div>
                                                          <div>Algunas personas con intolerancia al gluten son sensibles también a la avena. </div>
                                                        
                                                          
                                                        </tr>
                                                      </table>
                                                  
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
                                        @else
                                        <!------------->
                                        <div class="col-md-12" style="text-align: center;">{{$value_detalle->examen->nombre}}</div>
                                        @php 
                                            $parametros_nuevos = $parametros->where('id_examen', $value_detalle->id_examen)->where('id_parametro_ref','<>',null); 
                                            $xp_cant = 0;  $limite2 = 0;
                                            $ele_cant = count($parametros_nuevos);
                                        @endphp
                  
                    
                                        <table class="table table-striped" style="width: 95%">
                                          <tr>                    
                                            <td style="font-size: 15px;width:1%;"></td>
                                            <td style="font-size: 15px;width:19%;"></td>
                                            <td style="font-size: 15px;border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">NEGATIVO</td>
                                            <td style="font-size: 15px;border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">POSITIVO</td>
                                            <td style="font-size: 15px;width:1%;"></td>
                                            <td style="font-size: 15px;width:19%;"></td>
                                            <td style="font-size: 15px;border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">NEGATIVO</td>
                                            <td style="font-size: 15px;border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">POSITIVO</td>
                                          </tr>
                                          <tr>                    
                                          @foreach($parametros_nuevos as $px_n)
                                            @php  
                                              $xresultado = $orden->resultados
                                                ->where('id_parametro', $px_n->id)
                                                ->where('certificado','1')
                                                ->first();
                                              if(!is_null($xresultado)){
                                                $xp_cant ++; $limite2 ++;
                                              }    
                                                
                                              $color1 = '#ACCBEE'; $color2 = '#8AB0DB';  
                                              if($px_n->id_parametro_ref == '1103'){
                                                $color2 = '#76D7C4'; $color1 = '#A3E4D7';
                                              }
                                              if($px_n->id_parametro_ref == '1146'){
                                                $color1 = '#FADBD8'; $color2 = '#FDEDEC';
                                              }
                                              
                                            @endphp
                                            @if(!is_null($xresultado))
                                            <td style="font-size: 15px;width:1%;"> {{$xp_cant}}</td>
                                            <td style="font-size: 15px;width:19%;"> {{$xresultado->parametro->nombre}}</td>
                                            <td style="font-size: 15px;background-color:{{$color1}} ; border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;"> @if($xresultado->valor == 'NEGATIVO') X @endif</td>
                                            <td style="font-size: 15px;background-color:{{$color2}} ; border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;"> @if($xresultado->valor == 'POSITIVO') X @endif</td>
                                            @if($limite2 == 2) @php $limite2 = 0;  @endphp </tr><tr> @endif
                                            @endif 
                                          @endforeach
                                        </table> 

                                        @php 
                                            $parametros_nuevos2 = $parametros->where('id_examen', $value_detalle->id_examen)->where('id_parametro_ref',null); 
                                            
                                        @endphp
                   
                    
                                        <table class="table table-striped" style="width: 50%">
                                          <tr>                    
                                            <td style="font-size: 15px;width:1%;"></td>
                                            <td style="font-size: 15px;width:19%;"></td>
                                            <td style="font-size: 15px;border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">NEGATIVO</td>
                                            <td style="font-size: 15px;border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">POSITIVO</td>
                                          </tr>
                                                            
                                          @foreach($parametros_nuevos2 as $px_n)
                                            @php  
                                              $xresultado = $orden->resultados
                                                ->where('id_parametro', $px_n->id)
                                                ->where('certificado','1')
                                                ->first();
                                              
                                              if(!is_null($xresultado)){
                                                $xp_cant ++; 
                                              }  
                                                
                                                
                                              $color1 = '#ACCBEE'; $color2 = '#8AB0DB';  
                                              if($px_n->id == '1103'){
                                                $color2 = '#76D7C4'; $color1 = '#A3E4D7';
                                              }
                                              if($px_n->id == '1146'){
                                                $color1 = '#FADBD8'; $color2 = '#FDEDEC';
                                              }
                                            @endphp
                                            @if(!is_null($xresultado))
                                            <tr> 
                                            <td style="font-size: 15px;width:1%;"> {{$xp_cant}} </td>
                                            <td style="font-size: 15px;width:19%;"> {{$xresultado->parametro->nombre}} </td>
                                            <td style="font-size: 15px;background-color:{{$color1}} ; border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;"> @if($xresultado->valor == 'NEGATIVO') X @endif </td>
                                            <td style="font-size: 15px;background-color:{{$color2}} ; border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;"> @if($xresultado->valor == 'POSITIVO') X @endif </td>
                                            </tr>
                                            @endif
                                          @endforeach
                                        </table> 
                                        
                                          <!------------->
                                        @endif
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
           
    
<script type="text/javascript">
  function descargar(id_or){
    //location.href = '{{url('inicio/laboratorio/resultados/imprimir')}}/'+id_or;
     var cert = $('#sp'+id_or).text();

      if(cert=='0%'){
        alert({!! json_encode(trans('hc4.sinExamenesIngresados')) !!});
      }else{
        
        window.open('{{url('inicio/laboratorio/hc4resultados/imprimir')}}/'+id_or,'_blank');  

      }

  }

$(document).ready(function($){
    @foreach ($ordenes as $value)
      $.ajax({
        type: 'get',
        url:"{{ route('valida.puede_imprimir',['id_orden' => $value->id]) }}",
        success: function(data){
          console.log(data);
          if(data.cant_par==0){
             var pct = 0;
             //console.log(pct);  
          }else{
            var pct = data.certificados/data.cant_par*100;  
            //console.log(pct); 
          }
          //alert(pct);
          //$('#st').val(pct);
          if(pct <= 0){
            document.getElementById("{{$value->id}}").style.display="none";
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

</script>

