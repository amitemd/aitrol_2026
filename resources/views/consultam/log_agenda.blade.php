

@extends('consultam.base')
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
       
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Descripción del Cambio</th>
                  <th>Usuario</th>
                  <th>Fecha Agendada Anterior</th>
                  <th>Fecha Agendada Actual</th>
                  <th>Doctor Ant</th>
                  <th>Doctor</th>
                  <th>Observación</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($logs as $log)
                @php 
                  $doc_ant = Sis_medico\User::find($log->id_doctor1_ant);
                  $doc_des = Sis_medico\User::find($log->id_doctor1);
                @endphp
                <tr >
                  <td>{{ substr($log->created_at,0,10) }}</a></td>
                   <td>{{ substr($log->created_at,11,5) }}</a></td>
                  <td>@if($log->descripcion!=null){{ $log->descripcion }} / @endif @if($log->descripcion2!=null){{ $log->descripcion2 }} / @endif @if($log->descripcion3!='CAMBIO: '){{ $log->descripcion3 }} @endif</a></td>
                  <td>{{ $log->id_usuariocrea }} : {{ $log->nombre1 }} {{ $log->apellido1 }}</a></td>
                  <td>{{ $log->fechaini_ant }} - {{ $log->fechafin_ant }}</a></td>
                  <td>{{ $log->fechaini }} - {{ $log->fechafin }}</a></td>
                  <td>@if($doc_ant){{$doc_ant->apellido1}} @if($doc_ant->apellido2!='N/A'){{$doc_ant->apellido2}} @endif @endif</td>
                  <td>@if($doc_des){{$doc_des->apellido1}} @if($doc_des->apellido2!='N/A'){{$doc_des->apellido2}} @endif @endif</td>
                  <td>{{ $log->observaciones_ant }}/{{ $log->observaciones }}</a></td>
              </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
        
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
      
    });
  

function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}


 </script> 

@endsection