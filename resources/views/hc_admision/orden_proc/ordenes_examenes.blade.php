@extends('hc_admision.orden_proc.index')
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
  <div class="row">
    <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
      <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">
        <div class="row">
          <div class="col-md-12" style="text-align: center;">
              <h4 style="color: white;"">
                <b>LISTADO DE ORDENES DE PROCEDIMIENTOS</b>
              </h4> 
          </div>
          @if(!is_null($paciente)) 
          <div class="row"> 
            <div class="col-md-12" style="text-align: center;"> 
              <h4 style="color: white;" >
                <b>USUARIO: {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif 
                  {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
                  </b>
              </h4>
            </div> 
          </div>  
          @endif
          <br>    
        </div>
        <div>
          <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #ffffff; margin-left: 0px">
            <div class="parent" style="background-color: #ffffff">
              <div style=" margin-right: 30px;">
                @foreach($listado_ordenes as $value)
                  @php
                    $fecha_orden = $value->fecha_orden;
                    if(!is_null($value->fecha_orden)){
                          $fecha_r =  Date('Y-m-d',strtotime($value->fecha_orden));
                    }
                    
                    $xedad = Carbon\Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age; 
                  
                    $fecha = substr($value->fecha_orden,0,10);
                    $invert = explode( '-',$fecha);
                    $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
                  
                    if(!is_null($value->id_orden)){
                      $procedimiento_orden_tipo = \Sis_medico\Orden_Tipo::where('id_orden', $value->id_orden)->get();
                    }

                    $texto1 = ""; 
                    $texto2 = "";
                    $texto3 = "";
                    $texto4 = "";
                    $texto5 = "";
                    $texto6 = "";
                    $texto7 = "";
                    $texto8 = "";

                    if(!is_null($procedimiento_orden_tipo)){ 

                      foreach($procedimiento_orden_tipo as $value1){

                        if($value1->id_grupo_procedimiento == 1){
                          $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();
                          $mas = true;

                          foreach($procedimiento_orden_proced as $value2)
                          {
                            $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                            if($mas == true){
                              $texto1 = $nombre_procedimiento->nombre;
                              $mas = false; 
                            }else{
                              $texto1 = $texto1.' + '.$nombre_procedimiento->nombre;
                            }

                          }
                        }

                        if($value1->id_grupo_procedimiento == 2){
                          $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

                          $mas = true; 

                          foreach($procedimiento_orden_proced as $value2)
                          {
                            $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                            if($mas == true){
                              $texto2 = $nombre_procedimiento->nombre;
                              $mas = false; 
                            }else{
                              $texto2 = $texto2.' + '.$nombre_procedimiento->nombre;
                            }
                          }
                        }

                        if($value1->id_grupo_procedimiento == 3){
                          $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

                          $mas = true; 

                          foreach($procedimiento_orden_proced as $value2)
                          {
                            $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                            if($mas == true){
                              $texto3 = $nombre_procedimiento->nombre;
                              $mas = false; 
                            }else{
                              $texto3 = $texto3.' + '.$nombre_procedimiento->nombre;
                            }
                          }
                        }

                        if($value1->id_grupo_procedimiento == 9){
                          $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

                          $mas = true; 

                          foreach($procedimiento_orden_proced as $value2)
                          {
                            $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                            if($mas == true){
                              $texto4 = $nombre_procedimiento->nombre;
                              $mas = false; 
                            }else{
                              $texto4 = $texto4.' + '.$nombre_procedimiento->nombre;
                            }
                          }
                        }

                        if($value1->id_grupo_procedimiento == 10){
                          $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

                          $mas = true; 

                          foreach($procedimiento_orden_proced as $value2)
                          {
                            $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                            if($mas == true){
                              $texto5 = $nombre_procedimiento->nombre;
                              $mas = false; 
                            }else{
                              $texto5 = $texto5.' + '.$nombre_procedimiento->nombre;
                            }

                          }
                        }

                        if($value1->id_grupo_procedimiento == 14){
                          $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

                          $mas = true; 

                          foreach($procedimiento_orden_proced as $value2)
                          {
                            $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                            if($mas == true){
                              $texto6 = $nombre_procedimiento->nombre;
                              $mas = false; 
                            }else{
                              $texto6 = $texto6.' + '.$nombre_procedimiento->nombre;
                            }

                          }
                        }

                        if($value1->id_grupo_procedimiento == 18){
                            $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

                          $mas = true;

                          foreach($procedimiento_orden_proced as $value2)
                          {
                            $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                            if($mas == true){
                              $texto7 = $nombre_procedimiento->nombre;
                              $mas = false; 
                            }else{
                              $texto7 = $texto7.' + '.$nombre_procedimiento->nombre;
                            }

                          }
                        }

                        if($value1->id_grupo_procedimiento == 20){
                            $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

                          $mas = true;

                          foreach($procedimiento_orden_proced as $value2)
                          {
                            $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                            if($mas == true){
                              $texto8 = $nombre_procedimiento->nombre;
                              $mas = false; 
                            }else{
                              $texto8 = $texto8.' + '.$nombre_procedimiento->nombre;
                            }

                          }
                        }

                      }
                    
                    }
                  
                  @endphp
                    <div class="box @if($fecha_r != date('Y-m-d')) collapsed-box @endif" style="border: 2px solid #4682B4; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 1px;margin-top: 0px;">
                      <div class="box-header with-border" style="text-align: center;font-family: 'Helvetica general3';border-bottom: #004AC1;">
                            <div class="col-md-4">
                              <span> <b style="font-family: 'Helvetica';" class="box-title">
                              PACIENTE: {{$value->papellido1}} @if($value->papellido2!='(N/A)') {{$value->papellido2}} @endif {{$value->pnombre1}} @if($value->pnombre2!='(N/A)') {{$value->pnombre2}} @endif
                              </b></span>
                            </div>
                            <div class="col-md-1" style="color: white">
                              @if(!is_null($value->id_orden)) 
                                  {{$value->id_orden}}
                              @endif
                            </div>
                            <div class="col-md-3">
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
                            </div>
                            <div class="col-md-1" style="color: white">
                            </div>
                            <!--<div class="col-md-2">
                              <a class="btn btn-danger" onclick="descargar_orden({{$value->id_orden}});" style="color:white; background-color:#4682B4 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Orden</a>
                            </div>-->
                            <div class="col-md-2">
                              @if($value->tipo_procedimiento == 0)
                                <span> <b style="font-family: 'Helvetica';" class="box-title">
                                  ENDOSCOPICO
                                </b></span>
                              @elseif($value->tipo_procedimiento == 1)
                                <span> <b style="font-family: 'Helvetica';" class="box-title">
                                  FUNCIONAL
                                </b></span>
                              @elseif($value->tipo_procedimiento == 2)
                                <span> <b style="font-family: 'Helvetica';" class="box-title">
                                  IMAGENES
                                </b></span>
                              @endif
                            </div>
                            <div class="pull-right box-tools ">
                              <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                              <i class="fa @if($fecha_r != date('Y-m-d')) fa-plus @else  fa-minus @endif"></i>
                              </button>
                            </div>
                      </div>
                      <div class="col-12" style="padding-left: 22px;">
                            <div class="row">
                              <div class="col-md-12">
                                <label style="font-family: 'Helvetica general';">Procedimientos:</label>
                              </div>
                              @if($value->tipo_procedimiento == 0)
                                <div class="col-md-10">
                                  <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">{{$texto1}}</span>
                                  <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">{{$texto2}}</span>
                                  <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">{{$texto3}}</span>
                                  <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">{{$texto4}}</span>
                                  <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">{{$texto5}}</span>
                                  <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">{{$texto6}}</span>
                                </div>
                              @elseif($value->tipo_procedimiento == 1)
                                <div class="col-md-12">
                                  <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">@if(!is_null($texto7)) {{$texto7}} @endif</span>
                                </div>
                              @elseif($value->tipo_procedimiento == 2)
                                <div class="col-md-12">
                                  <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">@if(!is_null($texto8)) {{$texto8}} @endif</span>
                                </div>
                              @endif
                            </div>
                            <br>
                          <div class="col-12" style="padding-top: 8px"></div>
                          @if(!is_null($value->obs_recepcion))
                            <div class="row">
                                <div class="col-md-6">
                                  <label style="font-family: 'Helvetica general';">Observaci&oacute;n Recepci&oacute;n:</label>
                                  <span><?php echo $value->obs_recepcion?></span>
                                </div>
                            </div>
                          @endif
                      </div>
                      <div class="box-body">
                        <div style="padding-left: 18px;margin-left: 0px;margin-bottom: 10px; margin-right: 10px;">
                          <div class="col-12">
                            <div class="row">
                              <div class="col-md-8" style="border: 2px solid #4682B4;margin-left: 10px;margin-right: 10px;padding-right: 0px;padding-left: 0px;border-radius: 10px;background-color: white;margin-bottom: 10px">
                                <div class="col-md-12"  style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4; text-align: center ">
                                    <label class="box-title" style="background-color: #4682B4;  font-size: 16px;">
                                      Datos de la Orden
                                    </label>
                                </div>
                                <div class="col-md-12">
                                  <div class="col-md-3" ><b>Cédula:</b></div>
                                  <div class="col-md-3" >{{$value->id_paciente}}</div>
                                  <div class="col-md-3"><b>Parentesco</b></div>
                                  @if($value->id_paciente != $paciente->id)
                                  <div class="col-md-3">Familiar</div>
                                  @else
                                  <div class="col-md-3">Titular</div>
                                  @endif
                                  <div class="col-md-3" ><b>Edad:</b></div>
                                  <div class="col-md-3" >{{$xedad}} años</div>
                                  <div class="col-md-3" ><b>Seguro:</b></div>
                                  <div class="col-md-3" >{{$value->snombre}}</div>
                                  <div class="col-md-3" ><b>Doctor:</b></div>
                                  <div class="col-md-6" >{{$value->dnombre1}} {{$value->dapellido1}}</div>
                                </div>
                              </div>
                              <div class="col-md-3">
                                  <center>
                                    <div class="col-md-12 col-8">
                                      <a target="_blank" onclick="descargar_orden({{$value->id_orden}});" style="width: 100%; color: white; background-color:#4682B4 ; border-radius: 5px; border: 2px solid white;" class="btn btn-info boton-lab">
                                        <span style="color: white" class="glyphicon glyphicon-download-alt"></span> Descargar Orden
                                      </a>
                                    </div>
                                  </center>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
                          <div class="box" style="border: 2px solid #4682B4; background-color: white; border-radius: 3px; margin-bottom: 0;">
                            <div class="box-header with-border" style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4;padding: 2px;">
                              <div class="col-md-12">
                                <div class="row">
                                  @if($value->tipo_procedimiento == 0)
                                    <div class="col-3" style="text-align: center;"> 
                                        
                                        <label class="box-title" style="background-color: #4682B4;  font-size: 16px;">
                                          Detalle de Orden Endoscopico
                                        </label>
                                    </div>
                                  @elseif($value->tipo_procedimiento == 1)
                                    <div class="col-3" style="text-align: center;"> 
                                      <label class="box-title" style="background-color: #4682B4;  font-size: 16px;">
                                         Detalle de Orden Funcional
                                      </label>
                                    </div>
                                  @elseif($value->tipo_procedimiento == 2)
                                    <div class="col-3" style="text-align: center;"> 
                                      <label class="box-title" style="background-color: #4682B4;  font-size: 16px;">
                                         Detalle de Orden Imagenes
                                      </label>
                                  </div>
                                  @endif
                                </div>
                              </div>
                            </div>
                            <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general';" id="xorden{{$value->id_orden}}">
                              @if($value->necesita_valoracion == 'SI')<span style="color: red;">REQUIERE VALORACION CARDIOLÓGICA</span>@endif
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                  <div class="col-md-12">
                                    <label style="font-family: 'Helvetica general';">ANTECEDENTES PATOLOGICOS:</label> 
                                  </div>
                                  <div class="col-12" style="padding: 15px;">
                                    <span><?php echo $value->antecedentes_patologico?></span>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                  <div class="col-md-12">
                                    <label style="font-family: 'Helvetica general';">ANTECEDENTES FAMILIARES:</label> 
                                  </div>
                                  <div class="col-12" style="padding: 15px;">
                                    <span><?php echo $value->antecedentes_familiares?></span>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                  <div class="col-md-12">
                                    <label style="font-family: 'Helvetica general';">ANTECEDENTES QUIRURGICOS:</label> 
                                  </div>
                                  <div class="col-12" style="padding: 15px;">
                                    <span><?php echo $value->antecedentes_quirurgico?></span>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                  <div class="col-md-12">
                                    <label style="font-family: 'Helvetica general';">MOTIVO:</label> 
                                  </div>
                                  <div class="col-12" style="padding: 15px;">
                                    <span><?php echo $value->motivo_consulta?></span>
                                  </div>
                                </div>
                              </div>
                              <br>
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                  <div class="col-md-12">
                                    <label style="font-family: 'Helvetica general';">RESUMEN DE LA HISTORIA CL&IacuteNICA:</label> 
                                  </div>
                                  <div class="col-12" style="padding: 15px;">
                                    <?php echo $value->resumen_clinico?>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                  <div class="col-md-12">
                                    <label style="font-family: 'Helvetica general';">DIAGNOSTICO:</label> 
                                  </div>
                                  <div class="col-12" style="padding: 15px;">
                                    <span><?php echo $value->diagnostico?></span>
                                  </div>
                                </div>
                              </div>
                              @if($texto7 != "")
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                  <div class="col-md-12">
                                    <div style="background-color: #4682B4; color: white;padding: 3px;">
                                      <label style="font-family: 'Helvetica general';">PROCEDIMIENTOS FUNCIONALES</label>
                                    </div>
                                  </div>
                                  <div class="col-12" style="padding: 15px;">
                                    <span>
                                      {{$texto7}}
                                    </span>
                                  </div>
                                </div>
                              </div>
                              @endif
                              @if($texto8 != "")
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <div style="background-color: #4682B4; color: white;padding: 3px;">
                                        <label style="font-family: 'Helvetica general';">PROCEDIMIENTOS IMAGENES</label>
                                      </div>
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span>
                                        {{$texto8}}
                                      </span>
                                    </div>
                                  </div>
                                </div>
                              @endif
                              @if($texto1 != "")
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                  <div class="col-md-12">
                                    <div style="background-color: #4682B4; color: white;padding: 3px;">
                                      <label style="font-family: 'Helvetica general';">ENDOSCOPIAS DIGESTIVAS</label>
                                    </div>
                                  </div>
                                  <div class="col-12" style="padding: 15px;">
                                    <span>
                                      {{$texto1}}
                                    </span>
                                  </div>
                                </div>
                              </div>
                              @endif
                              @if($texto2 != "")
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                    <div class="col-md-12">
                                      <div style="background-color: #4682B4; color: white;padding: 3px;">
                                        <label style="font-family: 'Helvetica general';">COLONOSCOPIA-PROCTOLOGIA</label>
                                      </div>
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span>
                                        {{$texto2}}
                                      </span>
                                    </div>
                                  </div>
                              </div>
                              @endif
                              @if($texto3 != "")
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                    <div class="col-md-12">
                                      <div style="background-color: #4682B4; color: white;padding: 3px;">
                                        <label style="font-family: 'Helvetica general';">INTESTINO DELGADO</label>
                                      </div>
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span>
                                        {{$texto3}}
                                      </span>
                                    </div>
                                </div>
                              </div>
                              @endif
                              @if($texto4 != "")
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                    <div class="col-md-12">
                                      <div style="background-color: #4682B4; color: white;padding: 3px;">
                                        <label style="font-family: 'Helvetica general';">ECOENDOSCOPIAS</label>
                                      </div>
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span>
                                        {{$texto4}}
                                      </span>
                                    </div>
                                  </div>
                              </div>
                              @endif
                              @if($texto5 != "")
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                    <div class="col-md-12">
                                      <div style="background-color: #4682B4; color: white;padding: 3px;">
                                        <label style="font-family: 'Helvetica general';">CPRE</label>
                                      </div>
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span>
                                        {{$texto5}}
                                      </span>
                                    </div>
                                </div>
                              </div>
                              @endif
                              @if($texto6 != "")
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                    <div class="col-md-12">
                                      <div style="background-color: #4682B4; color: white;padding: 3px;">
                                        <label style="font-family: 'Helvetica general';">BRONCOSCOPIA</label>
                                      </div>
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span>
                                        {{$texto6}}
                                      </span>
                                    </div>
                                </div>
                              </div>
                              @endif
                              @if(!is_null($value->obs_medica))
                              <div class="col-md-12" style="padding: 1px;">
                                <div class="row">
                                  <div class="col-md-12">
                                    <label style="font-family: 'Helvetica general';">OBSERVACI&Oacute;N M&Eacute;DICA:</label> 
                                  </div>
                                  <div class="col-12" style="padding: 15px;">
                                    <span><?php echo $value->obs_medica?></span>
                                  </div>
                                </div>
                              </div>
                              @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <br>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

<script type="text/javascript">

  //Descarga Orden de Procedimiento 
  function descargar_orden(id_or){
     window.open('{{url('imprimir/orden_hc3/general')}}/'+id_or,'_blank');  
  }

</script>