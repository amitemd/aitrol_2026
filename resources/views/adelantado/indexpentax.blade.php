

@extends('consultam.base3')
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


</style>

<div class="container-fluid">
  <div class="row">
    
    <div class="col-md-12">
      <div class="box box-primary">
      <div class="box-header">
        
      </div>          
      <div class="box-body">
        <form method="POST" action="{{ route('consultam.pentax') }}" >
          {{ csrf_field() }}
          <div class="form-group col-md-4 col-xs-6">
            <label for="proc_consul" class="col-md-3 control-label">Tipo</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="proc_consul" id="proc_consul" onchange="buscar();">
                <option @if($proc_consul=='2') selected @endif value="2" >Todos</option>
                <option @if($proc_consul=='0') selected @endif value="0" >Consultas</option>
                <option @if($proc_consul=='1') selected @endif value="1" >Procedimientos</option>  
              </select>
            </div>
          </div>
          <div class="form-group col-md-4 col-xs-6">
            <label for="fecha" class="col-md-3 control-label">Fecha Desde</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>   
              </div>
            </div>  
          </div>
          <div class="form-group col-md-4 col-xs-6">
            <label for="fecha" class="col-md-3 control-label">Fecha Hasta</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_h').value = ''; buscar();"></i>
                </div>   
              </div>
            </div>  
          </div>

          @if($proc_consul=='1')
          <div class="form-group col-md-4 col-xs-6">
            <label for="pentax" class="col-md-3 control-label">Unidad</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="pentax" id="pentax" onchange="buscar();"  >
                <option @if($pentax=='x') selected @endif value="x" >Todos</option>
                <option @if($pentax=='0') selected @endif value="0" >Otros</option>
                <option @if($pentax=='2') selected @endif value="2" >Pentax</option>  
              </select>
            </div>
          </div>
          @endif

          <div class="form-group col-md-4 col-xs-6">
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

          <div class="form-group col-md-4 col-xs-6">
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
          

          <div class="form-group col-md-4 col-xs-6">
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

          <div class="form-group col-md-4 col-xs-6">
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

          <div class="form-group col-md-1 col-xs-6">
            <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>Buscar</button>
          </div>    

          @if($proc_consul=='1' && $pentax=='2')
          <div class="form-group col-md-1 col-xs-6">
            <button type="submit" class="btn btn-primary" formaction="{{route('consultam.search')}}">
              <span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span>Agenda</button>
          </div>    
          @endif
             
        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Paciente</th>
                  <th>Doctor Asignado</th>
                  <th>Doctor Pentax</th>
                  <th>Asistentes Encargados</th>
                  <th>Procedimientos Realizados</th>
                  <th>Estado</th>
                  <th>Preparación</th>
                  <th>Recuperación</th>
                  <th>Alta</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($ctr_pentax as $value)
                @php 
                    $p_color1="black"; if($value->estado_cita != 0){ if($value->paciente_dr == 1) { $p_color1=$value->d1color; } else{ $p_color1=$value->scolor;} };
                    $p_color2="black"; if($value->d1color!=''){ $p_color2=$value->d1color;}
               @endphp
                <tr >
                  <td><a href="{{ route('consultam.detalle',['id' => $value->aid]) }}" style="color: {{$p_color1}};">{{ substr($value->fechaini,0,10) }} </a></td>
                  <td><a href="{{ route('consultam.detalle',['id' => $value->aid]) }}" style="color: {{$p_color1}};">{{ substr($value->fechaini,11,5) }}</a></td>
                  <td><a href="{{ route('consultam.detalle',['id' => $value->aid]) }}" style="color: {{$p_color1}};">{{ $value->pnombre1 }} {{ $value->papellido1 }}</a></td>
                  <td ><a href="{{ route('consultam.detalle',['id' => $value->aid]) }}" style="color: {{$p_color1}};">{{ $value->danombre1 }} {{ $value->daapellido1 }}</a></td>
                  <td ><a href="{{ route('consultam.detalle',['id' => $value->aid]) }}" style="color: {{$p_color1}};">{{ $value->dnombre1 }} {{ $value->dapellido1 }}</a></td>
                  <td ><a href="{{ route('consultam.detalle',['id' => $value->aid]) }}" style="color: {{$p_color1}};">{{ $value->d2apellido1 }} @if(!is_null($value->d2apellido1) && !is_null($value->d3apellido1)) + @endif {{ $value->d3apellido1 }}</a></td>
                  <td ><a href="{{ route('consultam.detalle',['id' => $value->id]) }}" style="color: {{$p_color1}};">@if(!is_null($p_procs) && !is_null($p_procs[$value->id])) @php $ban='0'; @endphp @foreach($p_procs[$value->id] as $proc) @if($ban=='0') @php $ban='1'; @endphp @else + @endif {{ $proc->observacion }} @endforeach  @endif</a></td>
                  <td ><a href="#" style="color: {{$p_color1}};">
                      @if($value->estado_pentax=='0') EN ESPERA  
                      @elseif($value->estado_pentax=='1') PREPARACIÓN  
                      @elseif($value->estado_pentax=='2') EN PROCEDIMIENTO  
                      @elseif($value->estado_pentax=='3') RECUPERACION  
                      @elseif($value->estado_pentax=='4') ALTA  
                      @elseif($value->estado_pentax=='5') SUSPENDER 
                      @else NO ADMISIONADO @endif</a></td>
                  <td ><a href="{{ route('consultam.detalle',['id' => $value->aid]) }}" style="color: {{$p_color1}};">{{ substr($value->ingresa_prepa,10,10) }}</a></td>
                  <td ><a href="{{ route('consultam.detalle',['id' => $value->aid]) }}" style="color: {{$p_color1}};">{{ substr($value->ingresa_rec,10,10) }}</a></td>  
                  <td ><a href="{{ route('consultam.detalle',['id' => $value->aid]) }}" style="color: {{$p_color1}};">{{ substr($value->ingresa_alt,10,10) }}</a></td> 
              </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($ctr_pentax->currentPage()-1)*$ctr_pentax->perPage()}}  / @if(($ctr_pentax->currentPage()*$ctr_pentax->perPage())<$ctr_pentax->total()){{($ctr_pentax->currentPage()*$ctr_pentax->perPage())}} @else {{$ctr_pentax->total()}} @endif de {{$ctr_pentax->total()}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $ctr_pentax->appends(Request::only(['cedula', 'nombres', 'proc_consul', 'pentax', 'fecha_hasta', 'id_doctor1', 'id_seguro']))->links() }}
          </div>
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
            
            @if($fecha!=null)
              defaultDate: '{{$fecha}}',  
            @endif
            
            
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