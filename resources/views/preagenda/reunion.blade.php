
@extends('agenda.base')

@section('action-content')

<div class="modal fade" id="favoritesModal" 
     tabindex="-1" role="dialog" 
     aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" 
          data-dismiss="modal" 
          aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-footer">
        <button type="button" 
           class="btn btn-default" 
           data-dismiss="modal">Close</button>
        <span class="pull-right">
          <button type="button" class="btn btn-primary">
            Add to Favorites
          </button>
        </span>
      </div>
    </div>
  </div>
</div>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css' />
<section class="content" >
 <div class="box">

  <div class="panel panel-default">
                <div class="panel-heading">Suspender Reunion</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('agenda.updatereunion', ['id' => $agenda->id]) }}">
               
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                        <input type="hidden" id="fecha" value="{{date('Y-m-d H:i')}}"> 
                        
                        @php 

                            $doctor=Sis_medico\User::find($agenda->id_doctor1);
                            $sala=Sis_medico\Sala::find($agenda->id_sala); 
                            $hospital=Sis_medico\Hospital::find($sala->id_hospital);
                            $doctor2=Sis_medico\User::find($agenda->id_doctor2);
                            $doctor3=Sis_medico\User::find($agenda->id_doctor3);
                            $especialidad=Sis_medico\Especialidad::find($agenda->espid);

                        @endphp

                        <!--proc_consul-->
                        <div class="form-group col-md-6 {{ $errors->has('proc_consul') ? ' has-error' : '' }}">
                            <label for="proc_consul" class="col-md-4 control-label">Tipo Agendamiento</label>
                            <div class="col-md-7">
                                <input id="proc_consul" type="hidden" class="form-control" name="proc_consul" value="{{$agenda->proc_consul}}" >
                                <input id="tproc_consul" type="text" class="form-control" name="tproc_consul" value="@if($agenda->proc_consul=='0'){{'Consulta'}}@elseif($agenda->proc_consul=='1'){{'Procedimiento'}}@else{{'Reuniones'}}@endif" readonly="readonly" >
                            </div>
                        </div>

                        <!--estado cita-->
                        <div class="form-group col-md-6 {{ $errors->has('estado_cita') ? ' has-error' : '' }}">
                            <label for="est_amb_hos" class="col-md-4 control-label">ACCIÓN A REALIZAR</label>
                            <div class="col-md-7">
                                <select id="estado_cita" name="estado_cita" class="form-control" required>
                                    <?php $bandera='0'; ?>
                                    @if($agenda->fechaini<=date('Y-m-d H:i')) {{$bandera='1'}} @endif
                                    @if ($bandera=='1')
                                        <option value="">Fecha expirada, Sin Ninguna Acción a realizar ..</option>
                                    @else                                   
                                        <option value="">Seleccione ..</option>
                                        <option value="3">Suspender</option>
                                    @endif                                    

                                </select>  
                                @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> 
                        
                        <!--Doctor-->
                        <div class="form-group col-md-6 {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                            <label for="id_doctor1" class="col-md-4 control-label">Doctor</label>
                            <div class="col-md-7">
                                <input id="nombre_doctor" type="text" class="form-control" name="nombre_doctor" value="{{ $doctor->nombre1}} {{ $doctor->nombre2}} {{ $doctor->apellido1}} {{ $doctor->apellido2}}"  readonly="readonly">
                                @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                @endif 
                            </div>
                        </div>

                        <!--salas-->
                        <div class="form-group col-md-6 {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                            <label for="id_sala" class="col-md-4 control-label">Ubicación</label>
                            <div class="col-md-7">

                                <input id="tid_sala" type="text" class="form-control" name="tid_sala" value="{{$sala->nombre_sala}} / {{$hospital->nombre_hospital}}" readonly="readonly"> 
                                @if ($errors->has('id_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_sala') }}</strong>
                                    </span>
                                @endif    
                            </div>
                        </div> 
                        
                        <div class="form-group col-md-6 {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                            <label class="col-md-4 control-label">Inicio</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="@if(old('inicio')!=''){{old('inicio')}}@else{{$agenda->fechaini}}@endif" name="inicio" class="form-control pull-right" id="inicio" required >
                                </div>
                                   @if ($errors->has('inicio'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('inicio') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-6 {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Fin</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="@if(old('fin')!=''){{old('fin')}}@else{{$agenda->fechafin}}@endif" name="fin" class="form-control pull-right" id="fin" required >

                                </div>
                                @if ($errors->has('fin'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fin') }}</strong>
                                    </span>
                                    @endif
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
    

                         <div class="form-group col-md-6 {{ $errors->has('cortesia') ? ' has-error' : '' }}" >
                            <label for="cortesia" class="col-md-4 control-label">Cortesia</label>
                            <div class="col-md-7">
                                
                                <select id="cortesia" name="cortesia" class="form-control" required>
                                    <option @if($agenda->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                                    <option @if($agenda->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                                </select>    
                            </div>
                        </div>  


                        <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">

                            <label for="observaciones" class="col-md-2 control-label">Observaciones</label>
                            <div class="col-md-9">
                                <textarea maxlength="200" id="observaciones" class="form-control" name="observaciones">@if(old('observaciones')!=''){{old('observaciones')}}@else{{$agenda->observaciones}}@endif</textarea>
                                @if ($errors->has('observaciones'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observaciones') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>    
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" id="enviar" class="btn btn-primary">
                                    Aceptar
                                </button>
                        </div>  
                    </form>
                </div>
            </div>


	<div id='calendar' style="height: 1220px;"></div>
	</div>
</section>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script src="{{ asset ("/plugins/fullcalendar/jquery.min.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/fullcalendar.js") }}"></script>
<script src="{{ asset ("/plugins/fullcalendar/es.js") }}"></script>

    <script src="{{ asset ("/plugins/colorpicker/bootstrap-colorpicker.js") }}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script type="text/javascript">
    
    
    $('#inicio').bootstrapMaterialDatePicker({ 
        date: true,
        shortTime: false,
        format : 'YYYY/MM/DD HH:mm',
        lang : 'es',

        });

    $('#fin').bootstrapMaterialDatePicker({ 
        date: true,
        shortTime: false,
        format : 'YYYY/MM/DD  HH:mm',
        lang : 'es',

        });

    $(document).ready(function() {
        var estado = document.getElementById("estado_cita").value;
        $("#observaciones").attr("readonly","readonly");
        $("#inicio").attr("disabled","disabled");
        $("#fin").attr("disabled","disabled");
        $("#id_doctor1").hide();
        $("#nombre_doctor").show();
        $("#id_doctor2").hide();
        $("#nombre_doctor2").show();  
        $("#id_doctor3").hide();
        $("#nombre_doctor3").show(); 
        $("#id_sala").hide();
        $("#tid_sala").show();
        $("#estado_cita").focus();

        if (estado==2){
            $("#observaciones").removeAttr("readonly");
                $("#observaciones").removeAttr("readonly");
                $("#inicio").removeAttr("disabled");
                $("#fin").removeAttr("disabled");
                $("#observaciones").prop("required", true);
                //$("#observaciones").val("");
                //$("#inicio").val("");
                //$("#fin").val("");
                $("#id_doctor1").show();  
                $("#nombre_doctor").hide();
                $("#id_doctor2").show();
                $("#nombre_doctor2").hide();
                $("#id_doctor3").show();
                $("#nombre_doctor3").hide();
                $("#id_sala").show();
                $("#tid_sala").hide();
        }

           
       
        $("#estado_cita").change(function () {
            

            var estado = document.getElementById("estado_cita").value;

   
            if(estado==1 ){
                $("#observaciones").removeAttr("readonly");
                $("#observaciones").val("");
                $("#id_doctor1").hide();
                $("#nombre_doctor").show();
                $("#id_doctor2").hide();
                $("#nombre_doctor2").show(); 
                $("#id_doctor3").hide();
                $("#nombre_doctor3").show();
                $("#id_sala").hide();
                $("#tid_sala").show(); 
            } else if(estado==3){
                $("#observaciones").removeAttr("readonly");
                $("#observaciones").val("");
                $("#observaciones").prop("required", true);
                $("#inicio").attr("disabled","disabled");
                $("#fin").attr("disabled","disabled");
                $("#id_doctor1").hide();
                $("#nombre_doctor").show();
                $("#id_doctor2").hide();
                $("#nombre_doctor2").show();
                $("#id_doctor3").hide();
                $("#nombre_doctor3").show();
                $("#id_sala").hide();
                $("#tid_sala").show(); 
            } else if(estado==2){
                $("#observaciones").removeAttr("readonly");
                $("#inicio").removeAttr("disabled");
                $("#fin").removeAttr("disabled");
                $("#observaciones").prop("required", true);
                $("#observaciones").val("");
                //$("#inicio").val("");
                //$("#fin").val("");
                $("#id_doctor1").show();  
                $("#nombre_doctor").hide();
                $("#id_doctor2").show();
                $("#nombre_doctor2").hide();
                $("#id_doctor3").show();
                $("#nombre_doctor3").hide();
                $("#id_sala").show();
                $("#tid_sala").hide();               
            } 
            else{
                $("#observaciones").attr("readonly","readonly");
                $("#inicio").attr("disabled","disabled");
                $("#fin").attr("disabled","disabled");
                $("#id_doctor1").hide();
                $("#nombre_doctor").show();
                $("#id_doctor2").hide();
                $("#nombre_doctor2").show();
                $("#id_doctor3").hide();
                $("#nombre_doctor3").show();
                $("#id_sala").hide();
                $("#tid_sala").show(); 
            }
             
        });
           

    });




</script>
@include('sweet::alert')
@endsection
