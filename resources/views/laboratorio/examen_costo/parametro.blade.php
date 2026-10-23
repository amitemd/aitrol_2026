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
                <th >Nombre</th>
                <th >Referencia</th>
                <th >Mínimo</th>
                <th >Máximo</th>
                <th >Unidad</th>
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($examen_parametros as $value)
              @php $cont='1'; @endphp
              @if($value->texto2!=null) @php $cont++; @endphp 
              @elseif($value->texto3!=null) @php $cont++; @endphp
              @elseif($value->texto4!=null) @php $cont++; @endphp
              @endif
              <tr role="row">
                <td rowspan="{{$cont}}">{{$value->nombre}}</td>
                <td>{{$value->texto1}}</td>
                <td>{{$value->valor1}}</td>
                <td>{{$value->valor1g}}</td>
                <td>{{$value->unidad1}}</td>
                <td rowspan="{{$cont}}"><div class="form-group col-md-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('examen.edit', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" >
                        <span class="glyphicon glyphicon-edit"></span> Editar
                        </a>  
                      </div></td>  
              </tr>
              @if($value->texto2!=null)
              <tr role="row">
                <td>{{$value->texto2}}</td>
                <td>{{$value->valor2}}</td>
                <td>{{$value->valor2g}}</td>
                <td>{{$value->unidad2}}</td>  
              </tr>
              @endif
              @if($value->texto3!=null)
              <tr role="row">
                <td>{{$value->texto3}}</td>
                <td>{{$value->valor3}}</td>
                <td>{{$value->valor3g}}</td>
                <td>{{$value->unidad3}}</td>  
              </tr>
              @endif
              @if($value->texto4!=null)
              <tr role="row">
                <td>{{$value->texto4}}</td>
                <td>{{$value->valor4}}</td>
                <td>{{$value->valor4g}}</td>
                <td>{{$value->unidad4}}</td>  
              </tr>
              @endif

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