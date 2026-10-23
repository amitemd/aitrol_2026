@extends('laboratorio.examen.base')
@section('action-content')
@php 
  setlocale(LC_MONETARY, 'en_US');
@endphp
<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding-left: 0;
    padding-top: 0;
    padding-right: 3px;
    padding-bottom: 0;
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
          <h3 class="box-title">@lang('laboratorio.listadoExamenesLaboratorio')</h3>
        </div>
        <!--div class="form-group col-md-3">
          <a class="btn btn-primary" href="{{ route('examen.create')}}"> @lang('laboratorio.agregar')</a>
        </div-->
        <!--div class="form-group col-md-3 col-xs-3">
            <a type="button" class="btn btn-primary" href="{{route('examen.reporte')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> @lang('laboratorio.descargar')</a>
          </div-->
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('examen.listado_examenes')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-3">
          <label for="nombres" class="col-md-4 control-label">@lang('laboratorio.nombre')</label>
          <div class="col-md-8">
            <input id="nombres" type="text" class="form-control input-sm" name="nombres" value="{{$nombres}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
          </div>
        </div> 
          
        <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> @lang('laboratorio.buscar')</button>
      </form>
      <br>
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">@lang('laboratorio.examenesdelformato') HumanLabs</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body no-padding">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <thead>
                    <tr role="row">
                      <th >@lang('laboratorio.nombre')</th>
                      <th >@lang('laboratorio.accion')</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($examenes as $value)
                    <tr role="row">
                      <td style="font-size: 10px;">
                       {{$value->nombre}} - {{$value->descripcion}} - {{$value->nombre_largo}}
                      </td>  
                      <td>   
                        <a href="{{ route('examen.parametro',['id_examen' => $value->id]) }}" class="btn btn-success btn-xs" >
                        @lang('laboratorio.parametros')
                        </a>
                      </td>          
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">@lang('laboratorio.mostrando') 1 @lang('laboratorio.al') {{count($examenes)}} @lang('laboratorio.de') {{$examenes->total()}} @lang('laboratorio.registros')</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $examenes->appends(Request::only(['nombres']))->links()}}
                </div>
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

    $(".breadcrumb").append('<li class="active">Exámenes</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      "order": [[ 0, 'asc' ]]
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 

</script>  

@endsection