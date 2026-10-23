@extends('laboratorio.protocolo.base')
@section('action-content')

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
</style>
<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
        <div class="col-md-6">
          <h3 class="box-title">Listado de Protocolos</h3>
        </div>
        <div class="form-group col-md-3">
          <a class="btn btn-primary" href="{{ route('protocolo.create')}}"> Agregar</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('examen.search')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-6">
          <label for="examen" class="col-md-2 control-label">Nombre</label>
          <div class="col-md-10">
            <input id="nombre" type="text" class="form-control input-sm" name="nombre" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
          </div>
        </div> 
        <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th >Nombre</th>
                <th >Amb/Hosp</th>
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($protocolos as $value)
              <tr role="row">
                <td>{{$value->nombre}}</td>
                <td>@if($value->est_amb_hos=='1') HOSPITALIZADO @else AMBULATORIO @endif</td>
                <td><div class="form-group col-md-4">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('protocolo.edit', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" >
                        <span class="glyphicon glyphicon-edit"></span> Editar
                        </a>  
                      </div>
                <!--div class="form-group col-md-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('examen.parametro',['id_examen' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" >
                        <span class="glyphicon glyphicon-edit"></span> Parametro
                        </a>  
                      </div--></td>          
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($protocolos)}} de {{$protocolos->total()}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $protocolos->appends(Request::only(['id', 'apellidos', 'nombres']))->links()}}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  

<script type="text/javascript">

  $(document).ready(function($){

    $(".breadcrumb").append('<li class="active">Protocolos</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 

</script>  

@endsection