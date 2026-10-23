@extends('manual.base')
@section('action-content')
@php $rolUsuario = Auth::user()->id_tipo_usuario; @endphp
<!--MODAL DE SUBIR COBERTURA-->
<div class="modal fade" id="SubirMan" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog " role="document"  style="width: 75%;">
    <div class="modal-content"  id="imprimir3" >
        
 
        
    </div>
  </div>
</div>
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista de Tarifarios</h3>
        </div>
        @if(in_array($rolUsuario, array(1, 4, 5)) == true)
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('manual.create')}}">Agregar Manual</a>
        </div>
        @endif
      </div>
    </div>
  <!-- /.box-header -->
    <div class="box-body">
      
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
              <thead>
                <tr >
                  <th >Nombre</th>
                  <th >Descripcion</th>
                  <th >Subido Por</th>
                  <th >Fecha Inicio</th>
                  <th >Fecha Expiración</th>

                  <th >Descargar</th>
                  
                  @if(in_array($rolUsuario, array(1, 4, 5)) == true)
                  <th >Acción</th>
                  @endif
                </tr>
              </thead>
              <tbody>
              @foreach ($manuales as $value)
                  <tr>
                    <td>{{$value->nombre}}</td>
                    <td>{{$value->descripcion}}</td>
                    <td>{{$value->usuario_crea()->first()->nombre1}} {{$value->usuario_crea()->first()->apellido1}}</td>
                    <td>{{substr( $value->fecha_inicio,0,10) }}</td>
                    <td>{{substr( $value->fecha_fin,0,10) }}</td> 
                    <td><a  href="{{route('manual.modal',['id' => $value->id])}}" alt="pdf" data-toggle="modal" data-target="#SubirMan">@if($value->archivo!==null)<i class="glyphicon glyphicon-download-alt" >@endif</i> {{$value->archivo}}</a></td>
                    @if(in_array($rolUsuario, array(1, 4, 5)) == true)
                    <td>  
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <a href="{{route('manual.edit',['id' => $value->id])}}" ><button class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-pencil"></i> Editar </button></a>
                      <a href="{{route('manual.subir',['id' => $value->id])}}" data-toggle="modal" data-target="#SubirMan"><button class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> Subir </button></a>
                    </td>
                    @endif
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($manuales->currentPage()-1)*$manuales->perPage()}}  / @if(($manuales->currentPage()*$manuales->perPage())<$manuales->total()){{($manuales->currentPage()*$manuales->perPage())}} @else {{$manuales->total()}} @endif de {{$manuales->total()}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $manuales->appends(Request::only(['id', 'apellidos', 'nombres']))->links()}}
          </div>
        </div>
      </div>
  
    </div>
  </div>
  <!-- /.box-body -->
</section>
    <!-- /.content -->
  
<script type="text/javascript">
  
  $('#SubirMan').on('hidden.bs.modal', function(){
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
</script>  
@endsection