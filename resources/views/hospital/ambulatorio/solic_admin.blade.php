
<style type="text/css">
    .ui-autocomplete {
      z-index:2147483647;
    }
</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-12" style="padding-right: 6px;">
            <div class="box box-primary " style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="row">
                        <div class="col-md-9">
                            @php 
                                $paciente = $solicitud->paciente; 
                                $agenda   = $solicitud->agenda;
                                $datos_pac= $paciente->ho_datos_paciente;
                            @endphp
                            <h4 style="color: red;">
                                <b>Paciente: {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}   Fecha: {{$agenda->fechaini}}  </b>
                            </h4>
                        </div>
                        <div class="col-md-3">     
                            <a target="_blank" href="{{route('epicrisis.mostrar_final', ['id_solicitud' => $solicitud->id])}}" class="btn btn-danger btn-sm"><i class="fa fa-lock"></i> Cierre clínico</a>
                            <a target="_blank" href="{{route('clinical_safety.index', ['encounter_id' => $solicitud->encounter_id])}}" class="btn btn-warning btn-sm"><i class="fa fa-shield"></i> Seguridad clínica</a>
                            <a target="_blank" href="{{route('clinical_access.index', ['encounter_id' => $solicitud->encounter_id])}}" class="btn btn-default btn-sm"><i class="fa fa-unlock"></i> Acceso excepcional</a>
                            <a target="_blank" href="{{route('clinical_consults.index', ['encounter_id' => $solicitud->encounter_id])}}" class="btn btn-info btn-sm"><i class="fa fa-user-md"></i> Interconsultas</a>
                            <a target="_blank" href="{{route('clinical_orders.index', ['encounter_id' => $solicitud->encounter_id])}}" class="btn btn-primary btn-sm"><i class="fa fa-list-alt"></i> Órdenes y resultados</a>
                            <a target="_blank" href="{{route('hospital.nuevo.formulario008_pdf', ['id' => $solicitud->id])}}" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o" aria-hidden="true"></i>Emergencia F008</a>
                            <a target="_blank" href="{{route('formulario.001', ['id' => $solicitud->id])}}" class="btn btn-success btn-sm">F-001 Admision</a>
                        </div>

                    </div>
                </div>

                <div class="box-body with-border" style="padding: 1px;">
                    <form id="form_008">
                        <input type="hidden" name="id_solicitud" value="{{$solicitud->id}}" />
                        <h4>B. REGISTRO DE ADMISION</h4>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="nacionalidad" class="control-label">Nacionalidad</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="nacionalidad" value="{{$datos_pac->nacionalidad}}" maxlength="50">
                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="grupo_prioritario" class="control-label">Grupo Prioritario</label>
                            <select class="form-control input-sm"  name="grupo_prioritario" onchange="guardar_f008();">
                                <option >Seleccione ...</option>
                                <option @if($agenda->grupo_prioritario == 1) selected @endif value="1">SI</option>
                                <option @if($agenda->grupo_prioritario == 0) selected @endif value="2">NO</option>
                            </select>
                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="especifique_grupo" class="control-label">Especifique Grupo</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="especifique_grupo" value="{{$datos_pac->especifique_grupo}}" maxlength="50">
                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="autoidentificacion_etnica" class="control-label">Autoidentificacion Etnica</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="autoidentificacion_etnica" value="{{$datos_pac->autoidentificacion_etnica}}" maxlength="50">
                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="nacionalidad_etnica" class="control-label">Nacionalidad Etnica</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="nacionalidad_etnica" value="{{$datos_pac->nacionalidad_etnica}}" maxlength="50">
                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="pueblos" class="control-label">Pueblos</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="pueblos" value="{{$datos_pac->pueblos}}" maxlength="50">
                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="nivel_educacion" class="control-label">Nivel Educación</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="nivel_educacion" value="{{$datos_pac->nivel_educacion}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="estado_nivel_educacion" class="control-label">Estado Nivel Educación</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="estado_nivel_educacion" value="{{$datos_pac->estado_nivel_educacion}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="empresa_trabajo" class="control-label">Tipo Empresa Trabajo</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="empresa_trabajo" value="{{$datos_pac->empresa_trabajo}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="ocupacion" class="control-label">Ocupación</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="ocupacion" value="{{$paciente->ocupacion}}" maxlength="50">
                        </div>

                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="provincia" class="control-label">Provincia</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="provincia" value="{{$datos_pac->provincia}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="canton" class="control-label">Cantón</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="canton" value="{{$datos_pac->canton}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="parroquia" class="control-label">Parroquia</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="parroquia" value="{{$datos_pac->parroquia}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="barrio" class="control-label">Barrio</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="barrio" value="{{$datos_pac->barrio}}" maxlength="50">
                        </div>

                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="calle_principal" class="control-label">Calle Principal</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="calle_principal" value="{{$datos_pac->calle_principal}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="calle_secundaria" class="control-label">Calle Secundaria</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="calle_secundaria" value="{{$datos_pac->calle_secundaria}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="referencia" class="control-label">Referencia</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="referencia" value="{{$datos_pac->referencia}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="llamar_a" class="control-label">En Casos Necesario Llamar A:</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="llamar_a" value="{{$datos_pac->llamar_a}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="parentesco_afinidad" class="control-label">Parentesco/Afinidad</label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="parentesco_afinidad" value="{{$datos_pac->parentesco_afinidad}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="telefono_inst_per_paci" class="control-label">Telefono Personal Del Paciente </label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="telefono_inst_per_paci" value="{{$datos_pac->telefono_inst_per_paci}}" maxlength="50">
                        </div>
                    
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="forma_llegada" class="control-label">Forma De Llegada</label>
                            <select class="form-control input-sm"  name="forma_llegada" onchange="guardar_f008();">
                                <option value="">Seleccione ...</option>
                                <option @if($datos_pac->forma_llegada == 'Ambulatorio') selected @endif value="Ambulatorio">Ambulatorio</option>
                                <option @if($datos_pac->forma_llegada == 'Ambulancia') selected @endif value="Ambulancia">Ambulancia</option>
                                <option @if($datos_pac->forma_llegada == 'Otro') selected @endif value="Otro">Otro</option>
                            </select>

                        </div>
                        <div class="col-md-3" style="padding: 1px;">
                            <label for="fuente_informacion" class="control-label">Fuente De Informacion </label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="fuente_informacion" value="{{$datos_pac->fuente_informacion}}" maxlength="50">
                        </div>
                        
                        <div class="col-md-6" style="padding: 1px;">
                            <label for="inst_per_paci_entrega" class="control-label">Institución/Persona entrega al paciente </label>
                            <input class="form-control input-sm" onchange="guardar_f008();" type="text" name="inst_per_paci_entrega" value="{{$datos_pac->inst_per_paci_entrega}}" maxlength="50">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>                

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
//Function para agregar el nuevo paciente

    function guardar_f008(){
        $.ajax({
          type: 'post',
          url:"{{route('hospital.guardar_f008')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_008").serialize(),
          success: function(data){
            console.log(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }
    

</script>
