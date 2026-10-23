@extends('observacion.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style type="text/css">

.table td{
  font-size: 14px;
  padding-bottom: 4px !important;
  padding-top: 4px !important;
}  

</style>

<!-- Ventana modal editar -->
<div class="modal fade" id="CreaObservacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <form method="POST" action="{{ route('consultam.search') }}" >
          {{ csrf_field() }}
          <div class="form-group col-md-4 col-xs-6" >
            <label for="fecha" class="col-md-3 control-label">Fecha</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; listado();"></i>
                </div>   
              </div>
            </div>  
          </div>
          
        </form>  
        <div class="col-md-4">
          <a class="btn btn-primary btn-sm" href="{{route('observacion.create')}}" data-toggle="modal" data-target="#CreaObservacion">Agregar</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12" id="listado">
            <!--table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr>
                  <th width="10%">Fecha</th>
                  <th width="10%">Hora</th>
                  <th width="10%">Usuario</th>
                  <th width="10%">Modifica</th>
                  <th >Observación</th>
                  <th width="10%">Acción</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($observaciones as $value)
                  <tr >
                    <td >{{substr($value->created_at,0,10)}}</td>
                    <td >{{substr($value->created_at,10,10)}}</td>
                    <td >{{$value->usuario_crea()->first()->nombre1}} {{$value->usuario_crea()->first()->apellido1}}</td>
                    <td >{{$value->usuario_modifica()->first()->nombre1}} {{$value->usuario_modifica()->first()->apellido1}}</td>
                    <td >{{$value->observacion}}</td>
                    <td>  
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="#" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-xs">
                          Actualizar
                          </a>
                    </td>
                </tr>
              @endforeach
              </tbody>
            </table-->
          </div>
        </div>
        <!--div class="row">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($observaciones)}} de {{$observaciones->total()}} Registros</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $observaciones->appends(Request::only(['id', 'apellidos', 'nombres']))->links()}}
            </div>
          </div>
        </div-->
      </div>
    </div>
  <!-- /.box-body -->
  </div>
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(function () {

    

        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$fecha}}',
            
            });
        /*$('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '',
            
            });*/
        $("#fecha").on("dp.change", function (e) {
            listado();
        });

        listado();

         /*$("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });*/
  });
  

  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })


  var listado = function ()
  {

    var fecha = document.getElementById('fecha').value;   
    var unix =  Math.round(new Date(fecha).getTime()/1000);
    
    $.ajax({
        type: 'get',
        url:'{{ url('observacion/search')}}/'+unix,
        success: function(data){
            $('#listado').empty().html(data);
        }
    })
    
  }

  vartiempo = setInterval(function(){ listado(); }, 5000);

  function activar(id){
    var confirmar = confirm("SI ACTIVA LA OBSERVACIÓN YA NO PODRÁ MODIFICARLA");
    if(confirmar){
      location.href = '{{url("observacion/activa")}}/'+id;
    }
  }
  



</script> 




@endsection