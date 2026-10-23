@extends('limpieza_desinfeccion.base')
@section('action-content')

<style type="text/css">
  th, td{
    text-align: center;
  }
</style>
<div class="modal fade" id="editar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 80%;">
    </div>
  </div>
</div>

<section class="content">
  <div class="box">
      <div class="box-header">
        <h4 style="text-align: left;">REGISTROS LIMPIEZA</h4>
      </div>
      <div class="box-body">
        <div class="row">
          <div class="form-group col-md-6 ">
              <div class="row" >
                <div class="form-group col-md-10 ">
                        <h4>{{$sala->nombre_sala}}</h4>    
                </div>
              </div>
              </div>
        </div>

          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row" id="listado">
              <div class="table-responsive col-md-12">
                <table id="example4" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" >
                  <thead>
                    <tr>
	                    <th rowspan="2">Fecha</th>
	                    <th rowspan="2">Hora</th>
                      <th rowspan="2">Paciente</th>
	                    <th colspan="2">Tipo Desinfeccion</th>
	                    <th rowspan="2">Detergente/ Desinfectante</th>
	                    <th rowspan="2">Toallitas Desinfectantes</th>
	                    <th>Anestesiologia</th>
	                    <th rowspan="2">Responsable</th>
	                    <th colspan="5">Enfermeria</th>
                      <th rowspan="2">Responsable</th>
	                    <th rowspan="2">Observaciones</th>
	                    <th rowspan="2">Accion</th>
                    </tr>
                    <tr>
                      <th>Concurrente</th>
                      <th>Terminal</th>
                      <th>Maquina Anestesia</th>
                      <th>Camilla</th>
                      <th>Veladores</th>
                      <th>Monitores</th>
                      <th>Soporte de Monitores</th>
                      <th>Otros Equipos</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($limpieza as $value)
                      <tr>
                        <td>{{substr($paciente->fechaini,0,10)}}</td>
                        <td>{{substr($paciente->fechaini,11,18)}}</td>
                        <td>{{$value->paciente->nombre1}} {{$value->paciente->apellido1}}</td>
                        <td>@if($value->tipo_desinfecion == 1) X @endif</td>
                        <td>@if($value->tipo_desinfecion == 2) X @endif</td>
                        <td>{{$value->nom_deter_desinfec}}</td>
                        <td>{{$value->nom_toallitas}}</td>
                        <td>@if($value->anestesiologia == 1) X @elseif($value->anestesiologia == 2) XX @elseif($value->anestesiologia == 3) XXX @endif </td>
                        <td>@if(!is_null($value->responsable_anest)){{$value->user->nombre1}} {{$value->user->apellido1}} @endif </td>
                        <td>@if($value->en_camilla == 1) X @elseif($value->en_camilla == 2) XX @elseif($value->en_camilla == 3) XXX @endif</td>
                        <td>@if($value->en_velador == 1) X @elseif($value->en_velador == 2) XX @elseif($value->en_velador == 3) XXX @endif</td>
                        <td>@if($value->en_monitor == 1) X @elseif($value->en_monitor == 2) XX @elseif($value->en_monitor == 3) XXX @endif</td>
                        <td>@if($value->en_soporte == 1) X @elseif($value->en_soporte == 2) XX @elseif($value->en_soporte == 3) XXX @endif</td>
                        <td>@if($value->en_otros == 1) X @elseif($value->en_otros == 2) XX @elseif($value->en_otros == 3) XXX @endif</td>
                        <td>{{$value->responsable}}</td>
                        <td>{{$value->observacion}}</td>
                        <td>
                          <!--a type="button" class="btn btn-warning  btn-xs" id="edit" href="{{route('limpieza.editar',['id' => $value->id ])}}"><span class="glyphicon glyphicon-edit"></span></a-->
                          <a id="boton_editar" class="btn btn-warning btn-xs" href="{{route('limpieza.editar',['id' => $value->id ])}}" data-toggle="modal" data-target="#editar"> <span class="glyphicon glyphicon-edit"></span></a>

                          <a type="button" class="btn btn-danger btn-xs" id="eliminar" onclick="eliminar('{{$value->id}}')"><span class="glyphicon glyphicon-trash"></span></a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="row">
             
            </div>
        </div>


        </div>


    </div>
</section>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $('#fecha').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',        
  });

  $('#fecha_hasta').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD', 
  });

  $('#editar').on('hidden.bs.modal', function(){
    //location.reload();
    $(this).removeData('bs.modal');
  });

  $('#example4').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

  function eliminar(id){
        //alert(orden);
        $.ajax({
          type: 'get',
          url:"{{ url('limpieza/eliminar') }}/"+id,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          success: function(data){
           console.log(data);
           location.reload();

          },
          error: function(data){
             console.log(data);
          }
        });

    } 
  


</script>
@endsection