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
          <h3 class="box-title">Listado de Exámenes de Laboratorio</h3>
        </div>
        <div class="form-group col-md-3">
          <a class="btn btn-primary" href="{{ route('examen.create')}}"> Agregar</a>
        </div>
        <div class="form-group col-md-3 col-xs-3">
            <a type="button" class="btn btn-primary" href="{{route('examen.reporte')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar</a>
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
        <div class="form-group col-md-3">
          <label for="examen" class="col-md-4 control-label">Nombre</label>
          <div class="col-md-8">
            <input id="nombre" type="text" class="form-control input-sm" name="nombre" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
          </div>
        </div> 
        <div class="form-group col-md-3">
          <label for="estado" class="col-md-4 control-label">Estado</label>
          <div class="col-md-8">
            <select name="estado">
              <option @if($estado=='1') selected @endif value="1">Activo</option>
              <option @if($estado=='0') selected @endif value="0">Inactivo</option>
            </select>
          </div>
        </div> 
         <div class="form-group col-md-3">
          <label for="rnivel" class="col-md-4 control-label">Convenios</label>
          <div class="col-md-8">
            <select name="rnivel">
              <option @if($rnivel=='1') selected @endif value="1">Privados Grupo 1</option>
              <option @if($rnivel=='2') selected @endif value="2">Privados Grupo 2</option>
              <option @if($rnivel=='3') selected @endif value="3">Promociones</option>
              <option @if($rnivel=='0') selected @endif value="0">Públicos</option>
            </select>
          </div>
        </div> 
        <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
      </form>
      <br>
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Examenes del formato HumanLabs</h3>

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
                     
                      <th >Nombre</th>
                      <th >Código</th>
                      <th >Agrupador</th>
                      <th >A/I</th>
                      <th >PART.</th>    
                    @foreach($niveles as $nivel)  
                      <th >{{$nivel->nombre_corto}}</th>
                    @endforeach
                      <th >Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                  
                  @foreach ($examenes_part as $value)
                    <tr role="row">
                     
                       <td style="font-size: 10px;">
                        {{$value->descripcion}}
                      </td>
                      <td>{{$value->tarifario}}</td>
                      <td>{{substr($value->elnombre,0,15)}}</td>
                      <td>@if($value->estado == 0) I @else A @endif</td>
                      <td style="text-align: right; @if($value->valor <= 0) background-color: yellow; @endif">{{$value->valor}}</td>
                    @foreach($niveles as $nivel)  
                      <td style="text-align: right; @if(isset($nivel_seg[$value->id][$nivel->id])) @if($nivel_seg[$value->id][$nivel->id] <= 0) background-color: yellow; @endif @else background-color: yellow; @endif" >@if(isset($nivel_seg[$value->id][$nivel->id])) {{ $nivel_seg[$value->id][$nivel->id] }} @else 0 @endif</td>
                    @endforeach  
                      <td> 
                        <a href="{{ route('examen.edit', ['id' => $value->id]) }}" class="btn btn-warning btn-xs" >
                        <span class="glyphicon glyphicon-edit"></span>
                        </a>  
                        <a href="{{ route('examen.parametro',['id_examen' => $value->id]) }}" class="btn btn-success btn-xs" >
                          P
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($examenes_part)}} de {{$examenes_part->total()}} Registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $examenes_part->appends(Request::only(['nombre','estado', 'rnivel']))->links()}}
                </div>
              </div>
            </div>
          </div>
        </div>  
   
      </div>
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Examenes Publicos</h3>

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
                     
                      <th >Nombre</th>
                      <th >Código</th>
                      <th >Agrupador</th>
                      <th >A/I</th>
                      <th >PART.</th>    
                    @foreach($niveles as $nivel)  
                      <th >{{$nivel->nombre_corto}}</th>
                    @endforeach
                      <th >Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                  
                  @foreach ($examenes as $value)
                    <tr role="row">
                     
                       <td style="font-size: 10px;">
                        {{$value->descripcion}}
                      </td>
                      <td>{{$value->tarifario}}</td>
                      <td>{{substr($value->anombre,0,15)}}</td>
                      <td>@if($value->estado == 0) I @else A @endif</td>
                      <td style="text-align: right; @if($value->valor <= 0) background-color: yellow; @endif">{{$value->valor}}</td>
                    @foreach($niveles as $nivel)  
                      <td style="text-align: right; @if(isset($nivel_seg2[$value->id][$nivel->id])) @if($nivel_seg2[$value->id][$nivel->id] <= 0) background-color: yellow; @endif @else background-color: yellow; @endif" >@if(isset($nivel_seg2[$value->id][$nivel->id])) {{ $nivel_seg2[$value->id][$nivel->id] }} @else 0 @endif</td>
                    @endforeach  
                      <td> 
                        <a href="{{ route('examen.edit', ['id' => $value->id]) }}" class="btn btn-warning btn-xs" >
                        <span class="glyphicon glyphicon-edit"></span>
                        </a>  
                        <a href="{{ route('examen.parametro',['id_examen' => $value->id]) }}" class="btn btn-success btn-xs" >
                          P
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($examenes)}} de {{$examenes->total()}} Registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $examenes->appends(Request::only(['nombre','estado', 'rnivel']))->links()}}
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