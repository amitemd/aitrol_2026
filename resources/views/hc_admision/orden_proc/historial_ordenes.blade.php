@extends('hc_admision.visita.base')
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
</style>

<br>
<div class="container-fluid">
  <div class="row ">
    <div class="col-md-12" style="padding-left: 0.;padding-left: 0px;padding-right: 9px;margin-left: 5px;margin-right: 0px;border-radius: 10px;">
      <div class="col-md-12" style="border: 0px solid #000000;margin-left: 8px;margin-right: 0px;margin-left: 4px;padding-right: 0px;padding-left: 0px;background-color:#4682B4;">
        <div class="row">
          
            <div class="col-md-9" style="padding-left:460px">
              <h1 style="font-size: 15px; margin:0; color: white;padding-top: 12px;">
                <b>HISTORIAL ORDENES DE PROCEDIMIENTOS</b>
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
                  <b>PACIENTE : {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif 
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
                  @if($listado_ordenes != null)
                    @foreach($listado_ordenes as $list_ord)

                      @php  $seguro = Sis_medico\Seguro::find($paciente->id_seguro); @endphp

                      @php
                        
                        if(!is_null($list_ord->fecha_orden)){
                          $fecha_r =  Date('Y-m-d',strtotime($list_ord->fecha_orden));
                        }                        

                        if($list_ord->id_doctor != ""){
                          $xdoctor = DB::table('users as us')->where('us.id',$list_ord->id_doctor)->first();
                        }
                      

                      @endphp
                      @php
                        $fecha = substr($list_ord->fecha_orden,0,10);
                        $invert = explode( '-',$fecha);
                        $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
                      @endphp
                      @php
                        if(!is_null($list_ord->id)){
                          $procedimiento_orden_tipo = \Sis_medico\Orden_Tipo::where('id_orden', $list_ord->id)->get();
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
                    <div class="box @if($fecha_r != date('Y-m-d')) collapsed-box @endif" style="border: 2px solid #3c8dbc; border-radius: 10px; background-color: white; font-size: 13px; font-family: Helvetica; margin-bottom: 10px;margin-top: 0px;">
                      <div class="box-header with-border" style="font-family: 'Helvetica general3';border-bottom: #004AC1;">
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-3">
                            @if(!is_null($list_ord->fecha_orden))
                              @php 
                                $dia =  Date('N',strtotime($list_ord->fecha_orden)); 
                                $mes =  Date('n',strtotime($list_ord->fecha_orden)); 
                              @endphp
                              <b>
                              <span style="font-family: 'Helvetica'; font-size: 14px" class="box-title" >    
                              @if($dia == '1') {{trans('ehistorialexam.Lunes')}}  
                                @elseif($dia == '2') {{trans('ehistorialexam.Martes')}}
                                @elseif($dia == '3') {{trans('ehistorialexam.Miércoles')}} 
                                @elseif($dia == '4') {{trans('ehistorialexam.Jueves')}} 
                                @elseif($dia == '5') {{trans('ehistorialexam.Viernes')}} 
                                @elseif($dia == '6') {{trans('ehistorialexam.Sábado')}} 
                                @elseif($dia == '7') {{trans('ehistorialexam.Domingo')}} 
                              @endif
                              {{substr($list_ord->fecha_orden,8,2)}} {{trans('ehistorialexam.de')}}
                              @if($mes == '1') {{trans('ehistorialexam.Enero')}} 
                                 @elseif($mes == '2') {{trans('ehistorialexam.Febrero')}}  
                                 @elseif($mes == '3') {{trans('ehistorialexam.Marzo')}} 
                                 @elseif($mes == '4') {{trans('ehistorialexam.Abril')}} 
                                 @elseif($mes == '5') {{trans('ehistorialexam.Mayo')}} 
                                 @elseif($mes == '6') {{trans('ehistorialexam.Junio')}} 
                                 @elseif($mes == '7') {{trans('ehistorialexam.Julio')}} 
                                 @elseif($mes == '8') {{trans('ehistorialexam.Agosto')}} 
                                 @elseif($mes == '9') {{trans('ehistorialexam.Septiembre')}} 
                                 @elseif($mes == '10') {{trans('ehistorialexam.Octubre')}} 
                                 @elseif($mes == '11') {{trans('ehistorialexam.Noviembre')}} 
                                 @elseif($mes == '12') {{trans('ehistorialexam.Diciembre')}} 
                              @endif
                              {{trans('ehistorialexam.del')}} {{substr($list_ord->fecha_orden,0,4)}}</span></b>  
                            @endif
                            </div>
                            <div  class="col-md-4"> 
                              <div>
                                  <b>
                                    <span style="font-family: 'Helvetica general'; font-size: 12px" class="box-title">{{trans('ehistorialexam.Dr(a):')}}</span> 
                                    <span style="font-size: 12px" class="box-title">@if(!is_null($xdoctor->nombre1)) {{$xdoctor->apellido1}} {{$xdoctor->apellido2}} {{$xdoctor->nombre1}} {{$xdoctor->nombre2}}@endif</span></b>
                              </div>
                            </div>  
                            <div class="col-md-1" style="color: white">
                              @if(!is_null($list_ord->id)) 
                                {{$list_ord->id}}
                              @endif
                            </div>
                            <br>
                            @php 
                              $orden_012 = \Sis_medico\Orden_012::where('id_orden', $list_ord->id)->first();
                            @endphp
                            @if(!is_null($orden_012))
                            <div class="col-md-4">
                              <a class="btn btn-danger" target="_blank" href="{{route('formulario.12A')}}?id={{$orden_012->id}}" style="color:white; border-radius: 5px; border: 1px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Orden 012</a>
                            </div>
                            @else
                            <div class="col-md-4">
                              <button class="btn btn-danger" style="color:white; border-radius: 5px; border: 1px solid white;" onclick="cargar_012('{{$list_ord->id_evolucion}}','{{$list_ord->id}}')" ><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Orden 012</button>
                            </div>
                            @endif
                            @php 
                              $form_053 = Sis_medico\Formulario_053::where('id_orden', $list_ord->id)->first();
                            @endphp
                            @if(!is_null($form_053))
                            <div class="col-md-4">
                              <a class="btn btn-danger" target="_blank" href="{{route('orden_012.pdf_053', ['id_orden' => $list_ord->id])}}" style="color:white; background-color:#4682B4 ; border-radius: 5px; border: 1px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Formulario 053</a>
                            </div>
                            @endif
                            <div class="col-md-4">
                                         <a class="btn btn-danger" onclick="descargar_orden({{$list_ord->id}});" style="color:white; background-color:#4682B4 ; border-radius: 5px; border: 2px solid white;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Orden</a>
                            </div>
                            <br>
                            @if($list_ord->tipo_procedimiento == 0)
                              <div class="col-md-1">
                                <span style="font-size: 14px;padding-top: 8px;" class="box-title">{{trans('ehistorialexam.ENDOSCOPICO')}}</span>
                              </div>
                            @elseif($list_ord->tipo_procedimiento == 1)
                              <div class="col-md-1">
                                <span style="font-size: 14px;padding-top: 8px;" class="box-title">{{trans('ehistorialexam.FUNCIONAL')}}</span>
                              </div>
                            @elseif($list_ord->tipo_procedimiento == 2)
                              <div class="col-md-1">
                                <span style="font-size: 14px;padding-top: 8px;" class="box-title">{{trans('ehistorialexam.IMAGENES')}}</span>
                              </div>
                            @endif
                            <div class="pull-right box-tools ">
                              <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" style="background-color: #3c8dbc;">
                                <i class="fa fa-minus"></i>
                              </button>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.Procedimientos:')}}</label>
                            </div>
                            @if($list_ord->tipo_procedimiento == 0)
                              <div class="col-md-12">
                                @if(!is_null($list_ord->orden_tipo))
                                  @foreach($list_ord->orden_tipo as $tipo) 
                                    <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary"> 
                                        @php 
                                          $pflag = 0; 
                                        @endphp 
                                        @foreach($tipo->orden_procedimiento as $proc) 
                                          @if(!$pflag) 
                                            @php 
                                              $pflag=1; 
                                            @endphp 
                                          @else + @endif 
                                          {{$proc->procedimiento->observacion}} 
                                        @endforeach 
                                    </span> 
                                  @endforeach 
                                @endif
                              </div>
                            @elseif($list_ord->tipo_procedimiento == 1)
                              <div class="col-md-12">
                                <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">@if(!is_null($texto7)) {{$texto7}} @endif</span>
                              </div>
                            @elseif($list_ord->tipo_procedimiento == 2)
                              <div class="col-md-12">
                                <span style="font-size: 10px;margin-right: 5px;border-radius: 2px;background-color:#4682B4;" class="badge badge-primary">@if(!is_null($texto8)) {{$texto8}} @endif</span>
                              </div>
                            @endif
                          </div>
                          <div class="col-12" style="padding-top: 8px"></div>
                          @if(!is_null($list_ord->observacion_recepcion))
                            <div class="row">
                                <div class="col-md-6">
                                  <label style="font-family: 'Helvetica general';"> {{trans('ehistorialexam.ObservacionRecepcion')}}</label>
                                  <span><?php echo $list_ord->observacion_recepcion?></span>
                                </div>
                            </div>
                          @endif
                        </div>
                      </div>
                      <div class="box-body" style="background: white;">
                          <div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
                            <div class="box" style="border: 2px solid #4682B4; background-color: white; border-radius: 3px; margin-bottom: 0;">
                              <div class="box-header with-border" style="background-color: #4682B4; color: white; font-family: 'Helvetica general3';border-bottom: #4682B4;padding: 2px;">
                                <div class="col-md-12">
                                  <div class="row">
                                    @if($list_ord->tipo_procedimiento == 0)
                                      <div class="col-3" style="text-align: center;"> 
                                          <span >Detalle de Orden Endoscopico</span>
                                      </div>
                                    @elseif($list_ord->tipo_procedimiento == 1)
                                      <div class="col-3" style="text-align: center;"> 
                                        <span >Detalle de Orden Funcional</span>
                                      </div>
                                    @elseif($list_ord->tipo_procedimiento == 2)
                                      <div class="col-3" style="text-align: center;"> 
                                        <span >Detalle de Orden Imagenes</span>
                                    </div>
                                    @endif
                                  </div>
                                </div>
                              </div>
                              <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general';" id="xorden{{$list_ord->id}}">
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-8">
                                      @if(!is_null($fecha_invert))
                                        <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.FECHA:')}}</label>
                                        <span>
                                          {{$fecha_invert}}
                                        </span>
                                      @endif 
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-8">
                                      <div>
                                        @if(!is_null($xdoctor)) 
                                          <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.DOCTORSOLICITANTE:')}}</label> 
                                          <span>
                                            {{$xdoctor->apellido1}} {{$xdoctor->apellido2}}
                                            {{$xdoctor->nombre1}} {{$xdoctor->nombre2}}
                                          </span>
                                        @endif 
                                      </div>  
                                    </div>
                                    <div class="col-md-4">
                                      @if(!is_null($seguro)) 
                                        <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.SEGURO:')}}</label> 
                                        <span>
                                          {{$seguro->nombre}}
                                        </span>
                                      @endif
                                    </div>
                                  </div>
                                </div>
                                @if($list_ord->necesita_valoracion=='SI')<span style="color: red;"> {{trans('ehistorialexam.REQUIEREVALORACIONCARDIOLÓGICA')}}</span>@endif
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.ANTECEDENTESPATOLOGICOS:')}}</label> 
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span><?php echo $paciente->antecedentes_pat?></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.ANTECEDENTESFAMILIARES:')}}</label> 
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span><?php echo $paciente->antecedentes_fam?></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.ANTECEDENTESQUIRURGICOS:')}}</label> 
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span><?php echo $paciente->antecedentes_quir?></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.MOTIVO:')}}</label> 
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span><?php echo $list_ord->motivo_consulta?></span>
                                    </div>
                                  </div>
                                </div>
                                <br>
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.RESUMENDELAHISTORIACLINICA:')}}</label> 
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <?php echo $list_ord->resumen_clinico?>
                                    </div>
                                  </div>
                                </div>
                                @if(!is_null($list_ord->diagnosticos))
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.DIAGNOSTICO:')}}</label> 
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span><?php echo $list_ord->diagnosticos?></span>
                                    </div>
                                  </div>
                                </div>
                                @endif
                                @if($texto7 != "")
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <div style="background-color: #4682B4; color: white;padding: 3px;">
                                        <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.PROCEDIMIENTOSFUNCIONALES')}}</label>
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
                                        <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.PROCEDIMIENTOSIMAGENES')}}</label>
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
                                        <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.ENDOSCOPIASDIGESTIVAS')}}</label>
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
                                          <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.COLONOSCOPIA-PROCTOLOGIA')}}</label>
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
                                          <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.INTESTINODELGADO')}}</label>
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
                                          <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.ECOENDOSCOPIAS')}}</label>
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
                                          <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.CPRE')}}</label>
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
                                          <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.BRONCOSCOPIA')}}</label>
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
                                @if(!is_null($list_ord->observacion_medica))
                                <div class="col-md-12" style="padding: 1px;">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <label style="font-family: 'Helvetica general';">{{trans('ehistorialexam.OBSERVACIONMEDICA:')}}</label> 
                                    </div>
                                    <div class="col-12" style="padding: 15px;">
                                      <span><?php echo $list_ord->observacion_medica?></span>
                                    </div>
                                  </div>
                                </div>
                                @endif
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
                    @endforeach
                  @endif
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