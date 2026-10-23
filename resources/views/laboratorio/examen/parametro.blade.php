@extends('laboratorio.examen.base')
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
          <h3 class="box-title">Parámetros del {{$examen->nombre}}</h3>
        </div>
        <div class="form-group col-md-3">
          <a class="btn btn-primary" href="{{ route('examen.create_parametro',['id_examen' => $examen->id])}}"> Agregar</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th >Orden</th>
                <th >Nombre</th>
                <th >Mínimo</th>
                <th >Máximo</th>
                <th >Unidad</th>
                <th >Sexo</th>
                <th >Rango de Edad</th>
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($examen_parametros as $value)
              <tr role="row">
                <td>{{$value->orden}}</td>
                <td>{{$value->nombre}}</td>
                <td>{{$value->valor1}}</td>
                <td>{{$value->valor1g}}</td>
                <td>{{$value->unidad1}}</td>
                <td>@if($value->sexo == 1){{"Hombre"}}@endif @if($value->sexo == 2){{"Mujer"}}@endif @if($value->sexo == 3){{"Ambos"}}@endif</td>
                <td>{{$value->edad_ini}} - {{$value->edad_fin}}</td>
                <td><div class="form-group col-md-9">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('examen.edit_parametro', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" >
                        <span class="glyphicon glyphicon-edit"></span> Editar
                        </a>  
                      </div></td>  
              </tr>

            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($examen_parametros)}} de {{$examen_parametros->total()}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $examen_parametros->appends(Request::only(['id', 'apellidos', 'nombres']))->links()}}
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

    $(".breadcrumb").append('<li><a href="{{asset('/examen')}}"></i> Examen</a></li>');
    $(".breadcrumb").append('<li class="active">Parámetro</li>');

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