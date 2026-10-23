@php
 $variable_timepicker = 'a'.rand(0,999);
@endphp
<div class="infinite-scroll" >
@if(count($procedimientos2)>0)
    @foreach($procedimientos2 as $value)
        @php
            $variable_tiempo  = rand(100000,999999);
        @endphp
        <div  class="col-12" id="consulta{{$value->hc_id_procedimiento}}">
            <div class="box @if(substr($value->fechaini,0,10) != date('Y-m-d'))  collapsed-box @endif " style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                <div class="row">
                   <div class="col-3">

                    @php
                        $evolucion = null;
                        $evolucion = DB::table('hc_evolucion as e')->where('e.hcid',$value->hcid)->first();
                        if($value->id_doctor_examinador != ""){
                            $xdoctor = DB::table('users as us')->where('us.id', $value->id_doctor_examinador)->first();
                        }else{
                            $xdoctor = DB::table('users as us')->where('us.id', $value->id_doctor1)->first();
                        }
                        $fecha = "";
                    @endphp
                    @if(!is_null($value->fecha_atencion))
                        @php
                        $dia =  Date('N',strtotime($value->fecha_atencion));
                        $mes =  Date('n',strtotime($value->fecha_atencion));
                        @endphp
                        <b>
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
                            $fecha = $fecha.' '.substr($value->fecha_atencion,8,2).' de ';
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
                            $fecha = $fecha.' del '.substr($value->fecha_atencion,0,4);
                        @endphp
                        {{$fecha}}</b>
                    @else
                        @php
                        $dia =  Date('N',strtotime($value->fechaini));
                        $mes =  Date('n',strtotime($value->fechaini));
                        @endphp
                        <b>
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
                            $fecha = $fecha.' '.substr($value->fechaini,8,2).' de ';
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
                            $fecha = $fecha.' del '.substr($value->fechaini,0,4);
                        @endphp
                        {{$fecha}}</b>
                    @endif

                    </div>
                    <div class="col-4">
                        <div>
                            <span style="font-family: 'Helvetica general'; font-size: 12px">Especialidad: </span>
                            <span style="font-size: 12px">   {{$value->espe_nombre}}
                            </span>
                        </div>
                    </div>
                    <div class="col-4">
                       <div>
                            <span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a):</span>
                            <span style="font-size: 12px">
                                {{$xdoctor->nombre1}} {{$xdoctor->apellido1}}
                            </span>
                       </div>
                    </div>
                    <div class="pull-right box-tools" style="padding-top: 4px;">
                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                        <i class="fa  @if(substr($value->fechaini,0,10) != date('Y-m-d')) fa-plus @else fa-minus @endif "></i></button>
                    </div>
                  </div>
                </div>
                <div class="box-body" style="background: white;">
                    <form id="frm_evol{{$value->hc_id_procedimiento}}">
                        <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
                        <input type="hidden" name="id_hc_procedimiento" value="{{$value->hc_id_procedimiento}}">
                        <div class="col-12" style="padding: 1px;">
                            <div class="row">
                                <div class="col-7">
                                    <b>Fecha Visita: </b>
                                    @if($value->proc_consul ==0 )
                                        {{$fecha}}
                                        <b><br>Hora: <input type="hidden" value="{{$value->fechaini}}" name="fecha_doctor"></b>{{substr($value->fechaini,10,10)}}@else
                                        <div style="border: 2px solid #004AC1; padding-top: 1px" class="input-group date datetimepicker2{{$variable_timepicker}}" id="datetimepicker<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}" data-target-input="nearest" >
                                            <input  class="form-control datetimepicker-input" data-target="#datetimepicker<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}" value="@if(!is_null($evolucion->fecha_doctor)){{date('Y/m/d h:i', strtotime($evolucion->fecha_doctor))}}@else{{date('Y/m/d h:i', strtotime($value->fechaini))}}@endif"  name="fecha_doctor"/>
                                            <div class="input-group-append" data-target="#datetimepicker<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}" data-toggle="datetimepicker" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" type="text" @endif>
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-5" style="font-size: 12px">
                                    <b>
                                    @if($value->proc_consul=='1')
                                        Tipo:PROCEDIMIENTO
                                        @if(!is_null($evolucion))
                                            @php
                                            $procedimiento_evolucion  =  Sis_medico\hc_procedimientos::find($evolucion->hc_id_procedimiento);
                                                if($procedimiento_evolucion != null){
                                                   if($procedimiento_evolucion->id_procedimiento_completo != null){
                                                    echo $procedimiento_evolucion->procedimiento_completo->nombre_general;
                                                   }
                                                }
                                            @endphp
                                        @endif
                                    @endif
                                    </b>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" style="padding: 1px;">
                        <div class="row">
                               <div class="col-8"><h6><b>Datos Generales</b></h6></div>

                               </div>
                               <div class="col-12">
                                   <div class="row">
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="id_doctor_examinador" class="control-label" style="font-size: 12px">Medico Examinador @if(substr($value->fechaini,0,10) != date('Y-m-d')) @endif</label>
                                            <select class="form-control input-sm" style="width: 100%; font-size: 12px; border: 2px solid #004AC1;" name="id_doctor_examinador" id="id_doctor_examinador{{$value->hc_id_procedimiento}}" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif  >
                                                @foreach($doctores as $doc)
                                                    <option @if($value->id_doctor_examinador == $doc->id) selected @elseif($value->id_doctor_examinador == "" && $doc->id == $value->id_doctor1) selected @endif value="{{$doc->id}}" >{{$doc->apellido1}} @if($doc->apellido2 != "(N/A)"){{ $doc->apellido2}}@endif {{ $doc->nombre1}} @if($doc->nombre2 != "(N/A)"){{ $doc->nombre2}}@endif</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="id_seguro" class="control-label" style="font-size: 12px">Seguro</label>
                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="id_seguro" id="id_seguro{{$value->hc_id_procedimiento}}" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif>
                                                @foreach($seguros as $seg)
                                                    <option @if($value->id_seguro == $seg->id) selected @endif value="{{$seg->id}}" >{{$seg->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="id_seguro" class="control-label" style="font-size: 12px">Cortesia</label>
                                            <select id="consulta_cortesia_paciente{{$value->hc_id_procedimiento}}" name="consulta_cortesia_paciente" class="form-control input-sm" required style="background-color: #ccffcc; font-size: 11px; border: 2px solid #004AC1;">
                                            @if(!is_null($value->cortesia))
                                                <option @if($value->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                                                <option @if($value->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                                            @else
                                                <option value="NO" selected >NO</option>
                                                <option value="SI" >SI</option>
                                            @endif
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-6 has-error" style="padding: 1px;">
                                            <label for="observaciones" class="control-label" style="font-size: 12px">Observaciones</label>
                                            <textarea class="form-control input-sm" id="observaciones{{$value->hc_id_procedimiento}}" name="observaciones" style="width: 100%;background-color: #ffffb3; border: 2px solid #004AC1;" rows="1" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >{{strip_tags($value->observaciones)}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- Default box -->
                                <div class="box box-solid collapsed-box">
                                    <div class="box-header">
                                        <h3 class="box-title"><b>Información Administrativa</b></h3>
                                        <div class="box-tools pull-right">
                                            <button class="btn btn-default btn-sm" data-widget="collapse" onclick="ver_log_agenda('{{$value->id_agenda}}')"><i class="fa fa-plus"></i></button>
                                            <button class="btn btn-default btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                    <div style="display: none;" class="box-body">
                                        <div id="log_agenda_{{$value->id_agenda}}"></div>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->

                                <!--Agregar Visita OMNI HOSPITAL-->
                                @if(!is_null($value->procedencia))
                                <input type="hidden" name="ubicacion_omni" value="{{$value->procedencia}}">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-3 col-6" style="padding: 1px;">
                                                <label for="ubicacion" class="control-label" style="font-family: 'Helvetica general'; font-size: 12px">Ubicaci&oacute;n</label><br>
                                                <input readonly class="form-control input-sm" style="font-family: 'Helvetica general';width: 100%; border: 2px solid #004AC1;background-color: #ADFF2F;" value="{{$value->procedencia}}">
                                            </div>
                                            <div class="col-md-4 col-6" style="padding: 1px;">
                                                <label for="sala" class="control-label" style="font-family: 'Helvetica general'; font-size: 12px">Sala</label>
                                                <input class="form-control input-sm" name="sala" id="sala{{$value->hc_id_procedimiento}}" style="font-family: 'Helvetica general';width: 100%; border: 2px solid #004AC1;background-color: #ADFF2F;" rows="1" value="{{$value->sala_hospital}}" @if(substr($value->fechaini,0,10) == date('Y-m-d'))
                                                onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif>
                                            </div>
                                            <div class="col-md-5 col-6" style="padding: 1px;">
                                                <label for="estado" class="control-label" style="font-family: 'Helvetica general'; font-size: 12px">Estado</label>
                                                <select id="estado_visita{{$value->hc_id_procedimiento}}" name="estado_visita" class="form-control input-sm" required style="font-family: 'Helvetica general';background-color: #ADFF2F; font-size: 12px; border: 2px solid #004AC1;" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif>
                                                @if(!is_null($value->estado_cita))
                                                    <option @if($value->estado_cita=='4'){{'selected '}}@endif value="4">INGRESO</option>
                                                    <option @if($value->estado_cita=='5'){{'selected '}}@endif value="5">ALTA</option>
                                                    <option @if($value->estado_cita=='6'){{'selected '}}@endif value="6">EMERGENCIA</option>
                                                    <option @if($value->estado_cita=='7'){{'selected '}}@endif value="7">POST POEM</option>
                                                @else
                                                    <option value="4" selected >INGRESO</option>
                                                    <option value="5" >ALTA</option>
                                                    <option value="6" >EMERGENCIA</option>
                                                    <option value="7" >POST POEM</option>
                                                @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <!-- Fin Agregar Visita OMNI HOSPITAL -->
                        </div>
                        <div class="col-12">
                            <input type="hidden" name="id_agenda" value="{{$value->id_agenda}}">
                            <input type="hidden" name="hcid" value="{{$evolucion->hcid}}">
                            <input type="hidden" name="id_evolucion" value="{{$evolucion->id}}">
                            <div class="row">
                                <div class="col-12">
                                    <h6><b>Preparación</b></h6>
                                    <div class="row">
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="presion" class="control-label" style="font-size: 12px">P. Arterial</label>
                                            <input class="form-control input-sm" name="presion" id="pre{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->presion}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif>
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="pulso" class="control-label" style="font-size: 12px">Pulso</label>
                                            <input class="form-control input-sm" name="pulso" id="pul{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->pulso}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="temperatura" class="control-label" style="font-size: 12px">Temperatura (ºC)</label>
                                            <input class="form-control input-sm" name="temperatura" id="tem{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->temperatura}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="o2" class="control-label" style="font-size: 12px">SaO2:</label>
                                            <input class="form-control input-sm" name="o2" id="sao{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->o2}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="estatura" class="control-label" style="font-size: 12px">Estatura (cm)</label>
                                            <input class="form-control input-sm" id="estatura{{$value->hc_id_procedimiento}}" name="estatura" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->altura}}" onchange="calcular_indice({{$value->hc_id_procedimiento}});" @if($value->estado_cita!='4') readonly="yes" @endif  @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="peso" class="control-label" style="font-size: 12px">Peso (kg)</label>
                                            <input class="form-control input-sm" id="peso{{$value->hc_id_procedimiento}}" name="peso" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->peso}}" onchange="calcular_indice({{$value->hc_id_procedimiento}});" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="perimetro" class="control-label" style="font-size: 12px">Perimetro Abdominal</label>
                                            <input class="form-control input-sm" id="perimetro{{$value->hc_id_procedimiento}}" name="perimetro" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->perimetro}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;" >
                                            <label for="peso_ideal" class="control-label" style="font-size: 12px">Peso Ideal (kg)</label>
                                            <input class="form-control input-sm" id="peso_ideal{{$value->hc_id_procedimiento}}" name="peso_ideal" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6" style="padding: 1px;">
                                            <label for="gct" class="control-label" style="font-size: 12px">% GCT RECOMENDADO</label>
                                            <input class="form-control input-sm" id="gct{{$value->hc_id_procedimiento}}" name="gct" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                        <div class="col-md-4 col-6" style="padding: 1px;">
                                            <label for="imc" class="control-label" style="font-size: 12px">IMC</label>
                                            <input class="form-control input-sm" id="imc{{$value->hc_id_procedimiento}}" name="imc" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                        <div class="col-md-4 col-6" style="padding: 1px;">
                                            <label for="cimc" class="control-label" style="font-size: 12px">Categoria IMC</label>
                                            <input class="form-control input-sm" id="cimc{{$value->hc_id_procedimiento}}" name="cimc" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                        </div>
                                    </div>
                                    <h6><b>Clasificación Child Pugh</b></h6>
                                    @php
                                        $idusuario  = Auth::user()->id;
                                        $ip_cliente = $_SERVER["REMOTE_ADDR"];

                                        $child_pugh = null;
                                        $child_pugh = \Sis_medico\hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();

                                        if (is_null($child_pugh) && (!is_null($evolucion))) {

                                            $input_child_pugh = [
                                                'id_hc_evolucion' => $evolucion->id,
                                                'ip_modificacion' => $ip_cliente,
                                                'id_usuariomod'   => $idusuario,
                                                'id_usuariocrea'  => $idusuario,
                                                'examen_fisico'   => 'ESTADO CABEZA Y CUELLO:
                                                                                            ESTADO TORAX:
                                                                                            ESTADO ABDOMEN:
                                                                                            ESTADO MIEMBROS SUPERIORES:
                                                                                            ESTADO MIEMBROS INFERIORES:
                                                                                            OTROS: ',
                                                'ip_creacion'     => $ip_cliente,
                                                'created_at'      => date('Y-m-d H:i:s'),
                                                'updated_at'      => date('Y-m-d H:i:s'),
                                            ];
                                            \Sis_medico\hc_child_pugh::insert($input_child_pugh);

                                            $child_pugh = \Sis_medico\hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
                                        }
                                    @endphp
                                    <input type="hidden" name="id_child_pugh" value="{{$child_pugh->id}}">
                                    <div class="row">
                                        <!--<input type="hidden" name="id_child_pugh" value="">-->
                                        <div class="col-md-2 col-6" style="padding: 1px;">
                                            <label for="ascitis" class="control-label" style="font-size: 12px">Ascitis</label>
                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="ascitis" id="ascitis{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});" >

                                                <option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 1) selected @endif @endif value="1" >Ausente</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 2) selected @endif @endif value="2" >Leve</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 3) selected @endif @endif value="3" >Moderada</option>

                                            </select>
                                        </div>
                                        <div class="col-md-2 col-6" style="padding: 1px;">
                                            <label for="encefalopatia" class="control-label" style="font-size: 12px">Encefalopatia</label>
                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="encefalopatia" id="encefalopatia{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});">

                                                <option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 1) selected @endif @endif value="1" >No</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 2) selected @endif @endif value="2" >Grado 1 a 2</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 3) selected @endif @endif value="3" >Grado 3 a 4</option>

                                            </select>
                                        </div>
                                        <div class="col-md-2 col-6" style="padding: 1px;">
                                            <label for="albumina" class="control-label" style="font-size: 12px">Albúmina(g/l)</label>
                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="albumina" id="albumina{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});">

                                                <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 1) selected @endif @endif value="1" >&gt; 3.5</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 2) selected @endif @endif value="2" >2.8 - 3.5</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 3) selected @endif @endif value="3" >&lt; 2.8</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="bilirrubina" class="control-label" style="font-size: 12px">Bilirrubina(mg/dl)</label>
                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="bilirrubina" id="bilirrubina{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});">

                                                <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 1) selected @endif @endif value="1" >&lt; 2</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 2) selected @endif @endif value="2" >2 - 3</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 3) selected @endif @endif value="3" >&gt; 3</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="inr" class="control-label" style="font-size: 12px">Protrombina% (INR)</label>
                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="inr" id="inr{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});">

                                                <option @if(!is_null($child_pugh)) @if($child_pugh->inr == 1) selected @endif @endif value="1" >&gt; 50 (&lt; 1.7)</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->inr == 2) selected @endif @endif value="2" >30 - 50 (1.8 - 2.3)</option>
                                                <option @if(!is_null($child_pugh)) @if($child_pugh->inr == 3) selected @endif @endif value="3" >&lt; 30 (&gt; 2.3)</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="puntaje" class="control-label" style="font-size: 12px">Puntaje</label>
                                            <input class="form-control input-sm" id="puntaje{{$value->hc_id_procedimiento}}" name="puntaje" disabled style="width: 100%; border: 2px solid #004AC1;" readonly="yes" >
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="clase" class="control-label" style="font-size: 12px">Clase</label>
                                            <input class="form-control input-sm" id="clase{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;"  readonly="yes">
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="sv1" class="control-label" style="font-size: 12px">SV1 Año:</label>
                                            <input class="form-control input-sm" id="sv1{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;"  readonly="yes">
                                        </div>
                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                            <label for="sv2" class="control-label" style="font-size: 12px">SV2 años:</label>
                                            <input class="form-control input-sm" id="sv2{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;" readonly="yes">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" style="padding: 1px;">
                            <label for="motivo" class="control-label" style="font-size: 14px"><b>Motivo</b></label>
                            <textarea name="motivo" id="motivo{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="3"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif > @if(!is_null($value)){{$value->motivo}}@endif </textarea>
                        </div>
                        <div class="col-12" style="padding: 1px;">
                            <label for="thistoria_clinica" class="control-label" style="font-size: 14px"><b>Evolución</b></label>
                            <div id="thistoria_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" style="border: 2px solid #004AC1;">@if(!is_null($value))<?php echo $value->cuadro_clinico ?>@endif</div>
                            <input type="hidden" name="historia_clinica" id="historia_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}"  >
                        </div>
                        <div class="col-12" style="padding: 1px;">
                            <label for="tresultado_exam" class="control-label" style="font-size: 14px"><b>Resultados de Exámenes y Procedimientos Diagnósticos</b></label>
                            <div id="tresultado_exam<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}" style="border: 2px solid #004AC1;">@if(!is_null($value))<?php echo $value->resultado ?>@endif</div>
                            <input type="hidden" name="resultado_exam" id="resultado_exam<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}" >
                        </div>

                        <div class="col-12" style="padding: 1px;">
                            <label for="examen_fisico" class="control-label" style="font-size: 14px"><b>Examen Fisico</b></label>
                            <textarea id="examen_fisico{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" name="examen_fisico" style="width: 100%; border: 2px solid #004AC1;" rows="7"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif > @if(!is_null($child_pugh)){{strip_tags($child_pugh->examen_fisico)}}@endif </textarea>
                        </div>
                        @if($value->espid=='8')
                            @php
                                $cardiologia = DB::table('hc_cardio')->where('hcid',$value->hcid)->first();
                            @endphp
                            <div class="col-12" style="padding: 1px;">
                                <label for="resumen" class="control-label"><b>Resumen</b></label>
                                <textarea id="resumen{{$value->hc_id_procedimiento}}" name="resumen" style="width: 100%; border: 2px solid #004AC1;" rows="1"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >@if(!is_null($cardiologia)){{$cardiologia->resumen}}@endif</textarea>
                            </div>
                            <div class="col-12" style="padding: 1px;">
                                <label for="plan_diagnostico" class="control-label"><b>Plan Diagnóstico</b></label>
                                <textarea id="plan_diagnostico{{$value->hc_id_procedimiento}}" name="plan_diagnostico" style="width: 100%; border: 2px solid #004AC1;" rows="1" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >@if(!is_null($cardiologia)){{$cardiologia->plan_diagnostico}}@endif</textarea>
                            </div>
                            <div class="col-12" style="padding: 1px;">
                                <label for="plan_tratamiento" class="control-label"><b>Plan Tratamiento</b></label>
                                <textarea id="plan_tratamiento{{$value->hc_id_procedimiento}}" name="plan_tratamiento" style="width: 100%; border: 2px solid #004AC1;" rows="1" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >@if(!is_null($cardiologia)){{$cardiologia->plan_tratamiento}}@endif</textarea>
                            </div>
                        @endif
                        <input type="hidden" name="codigo" id="codigo{{$value->hc_id_procedimiento}}{{$variable_tiempo}}">
                        <div class="col-12" style="padding: 1px;">
                            <label for="indicacion" class="control-label" style="font-size: 14px"><b>Indicaciones</b></label>
                            <textarea name="indicacion" id="indicacion{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="3"@if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif>@if(!is_null($value)){{$value->indicaciones}}@endif</textarea>
                        </div>
                        <label for="cie10" class="col-12 control-label" style="padding-left: 0px; @if($value->proc_consul=='1') display: none; @endif"><b>Diagnóstico</b>
                        </label>
                        <div class="row">
                            <div class="form-group col-md-6 col-sm-6 col-12" style="padding: 15px; @if($value->proc_consul=='1') display: none; @endif">
                                <input id="cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase; border: 2px solid #004AC1;" onkeyup="javascript:this.value=this.value.toUpperCase(); " required placeholder="Diagnóstico" @if($value->estado_cita!='4') readonly="yes" @endif>
                            </div>

                             <div class="form-group col-md-3 col-sm-6 col-6" style=" padding: 15px; @if($value->proc_consul=='1') display: none; @endif">
                                <select id="pre_def{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" name="pre_def" class="form-control input-sm" >
                                    <option value="">Seleccione ...</option>
                                    <option value="PRESUNTIVO">PRESUNTIVO</option>
                                    <option value="DEFINITIVO">DEFINITIVO</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-12 col-6" >
                                <center>
                                    <div class="col-md-12 col-sm-6 col-12" style="padding: 15px; ">
                                        @if($value->estado_cita=='4')
                                        <button id="bagregar{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" class="btn btn_agregar_diag btn-sm col-10" style=" color: white; @if($value->proc_consul=='1') display: none; @endif"><span class="glyphicon glyphicon-plus"> Agregar</span>
                                        </button>
                                        @endif
                                    </div>
                                </center>
                            </div>
                        </div>
                        <div class="form-group col-12" style="padding: 1px;margin-bottom: 0px;">
                            <table id="tdiagnostico{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" class="table table-striped" style="font-size: 12px;">

                            </table>
                        </div>
                        <div class="col-12" style="padding: 1px;">
                            <label for="examenes_realizar" class="control-label" style="font-size: 14px"><b>Examenes a Realizar</b></label>
                            <textarea id="examenes_realizar{{$value->hc_id_procedimiento}}" name="examenes_realizar" style="width: 100%; border: 2px solid #004AC1;" rows="2"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >@if(!is_null($value)){{$value->examenes_realizar}}@endif</textarea>
                        </div>
                        <!-- RECETA-->
                        @php

                            $rec = \Sis_medico\hc_receta::where('id_hc', $value->hcid)->OrderBy('created_at', 'desc')->first();
                            if(is_null($rec)){
                                $ip_cliente= $_SERVER["REMOTE_ADDR"];
                                $input_hc_receta = [
                                    'id_hc' => $value->hcid,
                                    'ip_creacion' => $ip_cliente,
                                    'id_usuariocrea' => '9666666666',
                                    'ip_modificacion' => $ip_cliente,
                                    'id_usuariomod' => '9666666666',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                               $id_receta = \Sis_medico\hc_receta::insertGetId($input_hc_receta);
                            }else{
                                $id_receta = $rec->id;
                            }


                        @endphp
                        <input type="hidden" name="id_receta" value="{{$id_receta}}">

                        <div class="col-md-12" >
                            <div class="row">
                                <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">
                                  <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $id_receta, 'tipo' => '2']) }}">
                                  <div class="col-md-12" style="text-align: center; ">
                                    <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                      <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                        <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                      </div>
                                      <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                        <label style="font-size: 14px; ">Imprimir Membretada</label>
                                      </div>
                                    </div>
                                  </div>
                                  </a>
                                </div>
                                <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">
                                  <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $id_receta, 'tipo' => '1']) }}">
                                    <div class="col-md-12" style="text-align: center">
                                      <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                        <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                          <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                        </div>
                                        <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                          <label style="font-size: 14px">Imprimir</label>
                                        </div>
                                      </div>
                                    </div>
                                  </a>
                                </div>
                                @if($value->espid==5)
                                <div class="col-md-2 col-sm-2 col-2" style="margin: 10px; padding: 0px">
                                  <a target="_blank" class="btn btn-success"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $id_receta, 'tipo' => '3']) }}">
                                    <div class="col-md-12" style="text-align: center">
                                      <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                        <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                          <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                        </div>
                                        <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                          <label style="font-size: 14px">CIR</label>
                                        </div>
                                      </div>
                                    </div>
                                  </a>
                                </div>
                                @endif
                            </div>
                        </div>



                        <div class="col-md-11 col-sm-11 col-11" style="margin-left: 8px;margin-right: 14px;margin-left: 14px;padding-right: 0px;padding-left: 0px;border-radius: 3px;">
                          <!--Contenedor Historial de Recetas-->
                            <div  style=" color: white; font-family: 'Helvetica general'; font-size: 16px; ">
                                <div class="box-title" style=" margin-left: 10px">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-4" style="margin-left: 0px; ">

                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                          <div class="col-12">

                                            <div class="form-group">
                                              <label style="font-family: 'Helvetica general';" for="inputid" class="control-label">Medicina</label>
                                              <div class="row">
                                                <div class=" col-md-9 col-sm-9 col-12">
                                                  <input style="margin-bottom: 10px" value="" type="text" class="form-control" name="nombre_generico" id="nombre_generico{{$id_receta}}{{$variable_tiempo}}" placeholder="Nombre">
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="button" class="btn btn-primary col-md-8 col-sm-8 col-12" style="background-color: #004AC1;"
                                                    onClick="buscar_nombre_medicina('{{$id_receta}}{{$variable_tiempo}}')">
                                                        <span class="fa fa-plus"></span> Agregar
                                                   </button>
                                                   <br>
                                                   <a  style="background-color: #004AC1;" class="btn btn-primary col-md-8 col-sm-8 col-12 vademecum" data-toggle="modal_vade" data-target="#vademecum" data-remote="{{$id_receta}}{{$variable_tiempo}}">
                                                        Revisar Vademecum
                                                    </a>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                    </div>
                                    <div id="index{{$id_receta}}{{$variable_tiempo}}">

                                    </div>
                                    <div style="font-family: 'Helvetica general'; color: black" class="col-md-2">Alergias:</div>
                                    <div class="col-md-10">
                                      @if($alergiasxpac->count()==0)
                                       <b>NO TIENE </b>
                                      @else
                                        @foreach($alergiasxpac  as $ale)<span style="margin-bottom: 20px; padding-left: 10px; padding-right: 10px; border-radius: 5px;background-color: red;color: white"> {{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;
                                        @endforeach
                                      @endif
                                    </div>
                                </div>
                                </div>
                            </div>

                            <div class="contenedor2" id="receta{{$id_receta}}{{$variable_tiempo}}" style="padding-bottom: 20px; padding-right: 15px">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
                                            <div id="trp{{$id_receta}}{{$variable_tiempo}}" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
                                              <?php if (!is_null($rec)): ?>
                                                <?php echo $rec->rp ?>
                                              <?php endif;?>
                                            </div>

                                            <input type="hidden" value="{{$rec->rp}}" name="rp" id="rp{{$id_receta}}{{$variable_tiempo}}">
                                        </div>
                                        <div class="col-md-6">
                                            <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
                                            <div id="tprescripcion<?php echo e($id_receta); ?>{{$variable_tiempo}}"  style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
                                                <?php if (!is_null($rec)): ?>
                                                <?php echo $rec->prescripcion ?>
                                                <?php endif;?>
                                            </div>

                                            <input type="hidden" value="{{$rec->prescripcion}}" name="prescripcion" id="prescripcion{{$id_receta}}{{$variable_tiempo}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <script type="text/javascript">

                            cargar_tabla({{$value->hc_id_procedimiento}}, '{{$variable_tiempo}}');
                            calcular_indice({{$value->hc_id_procedimiento}});
                            datos_child_pugh({{$value->hc_id_procedimiento}});

                            $('#edad{{$value->hc_id_procedimiento}}').val( edad );

                            tinymce.init({
                            selector: '#thistoria_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}',
                            inline: true,
                            menubar: false,
                            content_style: ".mce-content-body {font-size:14px;}",

                            @if($value->estado_cita!='4')
                            readonly: 1,
                            @else

                            setup: function (editor) {
                                editor.on('init', function (e) {
                                   var ed = tinyMCE.get('thistoria_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}');
                                   //alert(ed.getContent());
                                    $('#historia_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());

                                });
                            },
                            @endif

                            init_instance_callback: function (editor) {
                                editor.on('Change', function (e) {
                                    var ed = tinyMCE.get('thistoria_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}');
                                    $('#historia_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                                    //guardar_protocolo({{$value->hc_id_procedimiento}});
                                    @if(substr($value->fechaini,0,10) == date('Y-m-d'))
                                        guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})
                                    @endif
                                });
                              }
                            });


                            tinymce.init({
                            selector: '#tresultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}',
                            inline: true,
                            menubar: false,
                            content_style: ".mce-content-body {font-size:14px;}",

                            @if($value->estado_cita!='4')
                            readonly: 1,
                            @else

                            setup: function (editor) {
                                editor.on('init', function (e) {
                                   var ed = tinyMCE.get('tresultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}');
                                   //alert(ed.getContent());
                                    $('#resultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());

                                });
                            },
                            @endif

                            init_instance_callback: function (editor) {
                                editor.on('Change', function (e) {
                                    var ed = tinyMCE.get('tresultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}');
                                    $('#resultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                                    //guardar_protocolo({{$value->hc_id_procedimiento}});
                                    @if(substr($value->fechaini,0,10) == date('Y-m-d'))
                                        guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})
                                    @endif
                                });
                              }
                            });



                            $('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').autocomplete({
                            source: function( request, response )
                            {
                                $.ajax({
                                    url:"{{route('epicrisis.cie10_nombre')}}",
                                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                                    data: {
                                        term: request.term
                                          },
                                    dataType: "json",
                                    type: 'post',
                                    success: function(data){
                                        response(data);
                                    }
                                })
                            },
                                minLength: 2,
                            });


                            $('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').change( function()
                            {
                            $.ajax({
                                type: 'post',
                                url:"{{route('epicrisis.cie10_nombre2')}}",
                                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                                datatype: 'json',
                                data: $('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}'),
                                success: function(data){
                                    if(data!='0'){

                                        $('#codigo{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(data.id);
                                        // guardar_cie10({{$value->hc_id_procedimiento}}, {{$value->hcid}}, {{$value->hc_id_procedimiento}});
                                    }
                                },
                                error: function(data){
                                }
                            })
                            });


                            $('#bagregar{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').click( function(){

                                if($('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val()!='' ){
                                    if($('#pre_def{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val()!='' ){
                                        @php
                                                $rec = \Sis_medico\hc_receta::where('id_hc', $value->hcid)->OrderBy('created_at', 'desc')->first();
                                        @endphp
                                        guardar_cie10_consulta({{$value->hcid}}, {{$value->hc_id_procedimiento}}, '{{$rec->id}}{{$variable_tiempo}}', '{{$variable_tiempo}}');
                                    }else{
                                        alert("Seleccione Presuntivo o Definitivo");
                                    }
                                }else{
                                    alert("Seleccione CIE10");
                                }

                                $('#codigo{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val('');
                                $('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val('');
                                $('#pre_def{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val('');

                            });
                            tinymce.init({
                            selector: '#tprescripcion{{$id_receta}}{{$variable_tiempo}}',
                            inline: true,
                            menubar: false,
                            content_style: ".mce-content-body {font-size:14px;}",
                            //readonly: 1,

                              setup: function (editor){
                                    editor.on('init', function (e){
                                       var ed = tinyMCE.get('tprescripcion<?php echo e($id_receta); ?>{{$variable_tiempo}}');
                                        $("#prescripcion<?php echo e($id_receta); ?>{{date('his')}}").val(ed.getContent());
                                    });
                              },

                              init_instance_callback: function (editor){
                                    editor.on('Change', function (e) {
                                        var ed = tinyMCE.get('tprescripcion<?php echo e($id_receta); ?>{{$variable_tiempo}}');
                                        $("#prescripcion<?php echo e($id_receta); ?>{{$variable_tiempo}}").val(ed.getContent());
                                        guardar_receta('{{$value->hc_id_procedimiento}}');

                                    });
                              }
                          });


                            tinymce.init({
                            selector: '#trp<?php echo e($id_receta); ?>{{$variable_tiempo}}',
                            inline: true,
                            menubar: false,
                            content_style: ".mce-content-body {font-size:14px;}",
                            //readonly: 1,

                              setup: function (editor){
                                  editor.on('init', function (e) {
                                     var ed = tinyMCE.get('trp<?php echo e($id_receta); ?>{{$variable_tiempo}}');
                                      $("#rp<?php echo e($id_receta); ?>{{date('his')}}").val(ed.getContent());
                                  });
                              },

                              init_instance_callback: function (editor){
                                  editor.on('Change', function (e) {
                                      var ed = tinyMCE.get('trp<?php echo e($id_receta); ?>{{$variable_tiempo}}');
                                      $("#rp<?php echo e($id_receta); ?>{{$variable_tiempo}}").val(ed.getContent());
                                      guardar_receta('{{$value->hc_id_procedimiento}}');
                                  });
                              }
                          });

                              $("#nombre_generico<?php echo e($id_receta); ?>{{$variable_tiempo}}").autocomplete({
                                source: function( request, response ) {
                                  $.ajax({
                                    url:"{{route('buscar_nombre.receta')}}",
                                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                                    data: {
                                        term: request.term,
                                        seguro: {{$value->id_seguro}}
                                          },
                                          dataType: "json",
                                          type: 'post',
                                          success: function(data){
                                            response(data);
                                          }
                                        })
                                    },
                                minLength:2,
                              });






                            $("#prescripcion<?php echo e($id_receta); ?>{{$variable_tiempo}}").change( function(){
                                guardar_receta('{{$value->hc_id_procedimiento}}');
                            });

                            $("#rp<?php echo e($id_receta); ?>{{$variable_tiempo}}").change( function(){
                                guardar_receta('{{$value->hc_id_procedimiento}}');
                            });
                        </script>
                        <div class="col-12">
                            <center>
                                <div class="col-4">
                                    <button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" id="bguardar{{$id_receta}}{{$variable_tiempo}}" onclick="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})"  ><span class="fa fa-floppy-o"></span>&nbsp;Guardar
                                    </button>
                               </div>
                            </center>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    <div id="paginationLinks" class="hidden-paginator oculto">{{ $procedimientos2->appends(Request::all())->render() }}</div>
@endif
</div>