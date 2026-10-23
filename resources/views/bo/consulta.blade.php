

@extends('bo.base_agenda')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
/* unvisited link */
a:link {
    color: black;
}

/* visited link */
a:visited {
    color: lightgreen;
}

/* mouse over link */
a:hover {
    color: blue;
}
button{
  width: 100%;
}


</style>

<div class="container-fluid">
  <div class="row">
    
    <div class="col-md-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          
        </div>          
        <div class="box-body">
          <form method="POST" action="{{ route('solicitud.search_consulta') }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="proc_consul" class="col-md-3 control-label">Tipo</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="proc_consul" id="proc_consul" onchange="buscar();">
                  <option @if($proc_consul=='2') selected @endif value="2" >Todos</option>
                  <option @if($proc_consul=='0') selected @endif value="0" >Consultas</option>
                  <option @if($proc_consul=='1') selected @endif value="1" >Procedimientos</option>  
                </select>
              </div>
            </div>
            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="fecha" class="col-md-3 control-label">Desde</label>
              <div class="col-md-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                  </div>   
                </div>
              </div>  
            </div>
            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
              <div class="col-md-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                  </div>   
                </div>
              </div>  
            </div>

            @if($proc_consul=='1')
            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="pentax" class="col-md-3 control-label">Unidad</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="pentax" id="pentax" onchange="buscar();"  >
                  <option @if($pentax=='x') selected @endif value="x" >Todos</option>
                  <option @if($pentax=='0') selected @endif value="0" >Otros</option>
                  <option @if($pentax=='2') selected @endif value="2" >Pentax</option>  
                </select>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="id_procedimiento" class="col-md-3 control-label" style="font-size: 12px;">Procedimiento</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="id_procedimiento" id="id_procedimiento" onchange="buscar();">
                  <option value="">Todos ...</option>
                @foreach($procedimientos as $procedimiento)
                  <option @if($procedimiento->id==$id_procedimiento) selected @endif value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                @endforeach  
                </select>
              </div>
            </div>
            @endif

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="cedula" class="col-md-3 control-label">Cédula</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cédula" >
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = ''; buscar();"></i>
                  </div>  
                </div>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="nombres" class="col-md-3 control-label">Paciente</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                  </div>
                </div>  
              </div>
            </div>
            

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="id_doctor1" class="col-md-3 control-label">Doctor</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="id_doctor1" id="id_doctor1" onchange="buscar();">
                  <option value="">Seleccione ...</option>
                @foreach($doctores as $doctor)
                  <option @if($doctor->id==$id_doctor1) selected @endif value="{{$doctor->id}}">{{$doctor->nombre1}} {{$doctor->apellido1}}</option>
                @endforeach  
                </select>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="id_seguro" class="col-md-3 control-label">Seguro</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="id_seguro" id="id_seguro" onchange="buscar();">
                  <option value="">Seleccione ...</option>
                @foreach($seguros as $seguro)
                  <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                @endforeach  
                </select>
              </div>
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="espid" class="col-md-3 control-label">Especialidad</label>
              <div class="col-md-9">
                <select class="form-control input-sm" name="espid" id="espid" onchange="buscar();">
                  <option value="">Todos ...</option>
                @foreach($especialidades as $especialidad)
                  <option @if($especialidad->id==$id_especialidad) selected @endif value="{{$especialidad->id}}">{{$especialidad->nombre}}</option>
                @endforeach  
                </select>
              </div>
            </div>

            <div class="form-group col-md-1 col-xs-4" >
              <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
            </div>
            

            <!--div class="form-group col-md-2 col-xs-6">
              <a style="color: white;" href="{{ url('consultam/pastelpentax ') }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Estadísticas</a>
            </div-->
 
            <!--div class="form-group col-md-2 col-xs-6" >
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{ url('consultam/pastelpentax ') }}"><span class="glyphicon glyphicon-stats" aria-hidden="true"> Estadísticas</button>
            </div-->

            <div class="form-group col-md-2 col-xs-6">
              <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('solicitud.reporte')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar</button>
            </div>
  

            <!--div class="form-group col-md-3 col-xs-6">
              <button type="submit" class="btn btn-success btn-sm" formaction="{{route('masterhc.masterhc')}}"> Historia Clinica</button>
            </div-->
               
          </form>
          <div class="col-md-2" style="padding: 5px;">
            <a class="btn btn-block btn-success" href="{{ route('solicitud.agenda') }}" style="color: white"> <i class="glyphicon glyphicon-th-list" >    </i> Agendar</a>
                
          </div>
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            <div class="table-responsive col-md-12 col-xs-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Cédula</th>
                    <th>Doctor</th>
                    <th>Sala</th>
                    @if($proc_consul=='1' || $proc_consul=='2')<th>Procedimientos</th>@endif
                    <th>Seguro/Convenio</th>
                    <th>Modifica</th>
                    <th>Estado</th>
                    <th><span data-toggle="tooltip" title="Ambulatorio/Hospitalizado">Amb/Hosp</span></th>
                    <th><span data-toggle="tooltip" title="Documentos Pendientes">P</span></th>
                  </tr>
                </thead>
                <tbody>
                @foreach ($agendas as $agenda)
                @php $agprocedimientos = Sis_medico\AgendaProcedimiento::where('id_agenda',$agenda->id)->get();
                      $p_color1="black"; if($agenda->estado_cita != 0){ if($agenda->paciente_dr == 1) { $p_color1=$agenda->d1color; } else{ $p_color1=$agenda->scolor;} };
                      $p_color2="black"; if($agenda->d1color!=''){ $p_color2=$agenda->d1color;}
                      $historia_clinica = Sis_medico\Historiaclinica::where('id_agenda',$agenda->id)->first();
                      $empresa=null;
                      if($agenda->id_empresa!=null){
                        $empresa = Sis_medico\Empresa::find($agenda->id_empresa);
                      }
                      
                 @endphp
                  <tr >
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ substr($agenda->fechaini,0,10) }}</a></td>
                     <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ substr($agenda->fechaini,11,5) }}</a></td>
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ $agenda->pnombre1 }} @if($agenda->pnombre2=='N/A'||$agenda->pnombre2=='(N/A)') @else{{ $agenda->pnombre2 }} @endif {{ $agenda->papellido1 }} @if($agenda->papellido2=='N/A'||$agenda->papellido2=='(N/A)') @else{{ $agenda->papellido2 }} @endif</a></td>
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ $agenda->id_paciente }}</a></td>
                    <td ><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color2}}">{{ $agenda->dnombre1 }} {{ $agenda->dapellido1 }}</a></td>
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">{{ $agenda->snombre }}</a></td>
                    @if($proc_consul=='1' || $proc_consul=='2')<td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}" style="color: {{$p_color1}};">@if(!is_null($agenda->probservacion)){{$agenda->probservacion}}@else Consulta @endif @if(!$agprocedimientos->isEmpty()) @foreach($agprocedimientos as $agendaproc) + {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->observacion}} @endforeach @endif</a></td>@endif 
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">@if(is_null($historia_clinica)){{ $agenda->senombre }}@else {{$historia_clinica->seguro->nombre}} @endif @if($empresa!=null)/ {{$empresa->nombre_corto}}@endif </a></td>
                    <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">{{ substr($agenda->aunombre1,0,1) }}{{ $agenda->auapellido1 }}</a></td>

                    <td @if($agenda->estado_cita=='3' || $agenda->estado_cita=='-1') style="background-color: #ffcccc;" @endif><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">@if($agenda->estado_cita=='0'){{'Por Confirmar'}}@elseif($agenda->estado_cita=='1'){{'Confirmado'}}@elseif($agenda->estado_cita=='-1'){{'No Asiste'}}@elseif($agenda->estado_cita=='3'){{'Suspendido'}}@elseif($agenda->estado_cita=='4')
                      @php 
                       $pentax = DB::table('pentax')->where('id_agenda', $agenda->id)->first();
                      @endphp
                        @if($pentax != "")
                          @if($pentax->estado_pentax == 0)
                          {{'EN ESPERA'}}
                          @elseif($pentax->estado_pentax == 1)
                          {{'PREPARACION'}}
                          @elseif($pentax->estado_pentax == 2)
                          {{'EN PROCEDIMIENTO'}}
                          @elseif($pentax->estado_pentax == 3)
                          {{'RECUPERACION'}}
                          @elseif($pentax->estado_pentax == 4)
                          {{'ALTA'}}
                          @elseif($pentax->estado_pentax == 5)
                          {{'SUSPENDIDO'}}
                          @endif
                        @else
                          {{'Asistió'}}
                        @endif
                      @elseif($agenda->estado_cita=='2')@if($agenda->estado=='1'){{'Completar Datos'}}@else{{'Reagendar'}}@endif @endif</a></td>
                      <td><a href="{{ route('consultam.detalle',['id' => $agenda->id]) }}"" style="color: {{$p_color1}};">@if($agenda->est_amb_hos=='0') AMBU @elseif($agenda->est_amb_hos=='1') @if($agenda->omni=='SI') OMNI @else HOSP @endif @endif</a></td>
                    <td>@if(array_has($dp_proc, $agenda->id)){{$dp_proc[$agenda->id]}}@else 0 @endif</td>
                </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
          
            <div class="col-md-5 col-xs-12">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($agendas->currentPage()-1)*$agendas->perPage()}}  / @if(($agendas->currentPage()*$agendas->perPage())<$agendas->total()){{($agendas->currentPage()*$agendas->perPage())}} @else {{$agendas->total()}} @endif de {{$agendas->total()}} registros</div>
            </div>
            <div class="col-md-7 col-xs-12">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $agendas->appends(Request::only(['fecha','cedula', 'nombres', 'proc_consul', 'pentax', 'fecha_hasta', 'id_doctor1', 'id_seguro', 'id_procedimiento', 'espid']))->links() }}
              </div>
            </div>
          
        </div>
      </div>
    </div>

    
  </div>
</div>  
 
    
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

  <script type="text/javascript">

  $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$fecha}}',
            
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$fecha_hasta}}',
            
            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });
  });
   

  $('#editMaxPacientes').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });
  

function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}


 </script> 

@endsection