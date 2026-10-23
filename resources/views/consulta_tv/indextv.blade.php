@extends('consulta_tv.base1')
@section('action-content')

<!--Para buscador-->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header">
              <form method="POST" action="{{ route('pentax.consultatv') }}" id="form_consulta">
                  {{ csrf_field() }}
                  <div class="form-group col-md-4">
                      <label class="col-md-3 control-label">Fecha</label>
                      <div class="col-md-6">
                          <input type="date" value="{{$fecha}}" name="fecha" class="form-control" id="fecha" required>
                      </div>
                  </div>

                  <div class="form-group col-md-4">
                      <label class="col-md-3 control-label">Doctor</label>
                      <div class="col-md-6">
                          <select name="id_doctor" id="id_doctor" class="form-control" >
                              @foreach($doctores as $doctor)
                                  @php $usuario = Sis_medico\User::find($doctor->id_doctor1) @endphp
                                  @if(!is_null($usuario))<option @if($doctor->id_doctor1 == $id_doctor ) selected @endif value="{{$doctor->id_doctor1}}">{{$usuario->apellido1}} {{$usuario->nombre1}}</option>@endif
                              @endforeach  
                          </select>
                      </div>
                  </div>
                 
                  <div class="form-group col-md-4">
                      <button type="submit" class="btn btn-primary">
                          Buscar
                      </button>
                  </div>
              </form>
        </div>        
        <div class="box-body">
            @php $user = Sis_medico\User::find($id_doctor); @endphp      
                <h4>Dr. {{$user->apellido1}} {{$user->nombre1}}</h4>
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                  <div class="row">
                    <div class="table-responsive col-md-12">
                      <table id="example2" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Hora</th>
                            <th>Paciente</th>            
                          </tr>
                        </thead>
                        <tbody>

                          @foreach ($consultas as $value)
                            @php
                              $color = '#f1f2c0';
                              $historia = Sis_medico\Historiaclinica::where('id_agenda',$value->id)->first();
                              if(!is_null($historia)){
                                $hc_procedimiento = Sis_medico\hc_procedimientos::where('id_hc',$historia->hcid)->first();
                                if(!is_null($hc_procedimiento)){
                                  if($hc_procedimiento->hora_inicio != null){
                                    $color = '#ffccdd';
                                  }
                                  if($hc_procedimiento->hora_fin != null){
                                    $color = '#b3f0ff';
                                  }
                                }         
                              }
                              $p_color1="black"; if($value->estado_cita != 0){ if($value->paciente_dr
                              == 1) {
                              $p_color1=$value->d1color; } else{ $p_color1=$value->scolor;} };
                              $p_color2="black"; if($value->d1color!=''){ $p_color2=$value->d1color;}
                            @endphp
                            <tr style="background-color:{{$color}}">
                                <td>{{ substr($value->fechaini,11) }}</td>
                                
                                <td>{{ $value->papellido }}
                                    @if($value->papellido2!='(N/A)') {{ $value->papellido2 }} @endif
                                    {{ $value->pnombre1 }}
                                    @if($value->pnombre2!='(N/A)'){{ $value->pnombre2 }} @endif</td>
                            </tr>
                                        
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              
        </div>
    </div>
        
</section>


<!-- SCRIPT PARA RECARGAR TIEMPO-->
            <script type="text/javascript">
            $("#body2").addClass('sidebar-collapse');
            const ajs_doctores = [
              @foreach($doctores as $doctor)
                  "{{$doctor->id_doctor1}}" ,
              @endforeach
            ];  
            var vartiempo = setInterval(function(){ cambiar_doctor(); }, 20000);
            //AQUI VA ALGO DE PRUEBA

            console.log(ajs_doctores);

            function  cambiar_doctor(){ 
              
                var js_hasta = ajs_doctores.length;
                let indice_doctor = ajs_doctores.indexOf('{{$id_doctor}}');
                let indice_nuevo_doctor = indice_doctor + 1;
                let nuevo_doctor = ajs_doctores[indice_nuevo_doctor];

                console.log(nuevo_doctor);
                $('#id_doctor').val(nuevo_doctor);
                $('#form_consulta').submit();
                //location.reload(); 
            }

            function fechacalendario() {
                var dato = document.getElementById('fecha').value;
                $('#enviar_fecha').click();
            }

            </script>
            <!-- AQUI TERMINA EL RECARGADOR DE TIEMPO-->
@endsection