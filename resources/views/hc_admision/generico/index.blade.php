@extends('hc_admision.generico.base')
@section('action-content')

<style type="text/css">
  
  .table-hover>tbody>tr:hover{
    background-color: #ccffff;
  }
</style>



  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

    <!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-4">
          <h3 class="box-title">LISTADO DE MEDICINAS GENÉRICAS</h3>
        </div>
         <div class="col-md-5 col-sm-5 col-xs-5">
          <form method="POST" action="{{ route('generico.search',['agenda' => $agenda]) }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-10" >
              <label for="nombre" class="col-md-2 control-label" style="padding: 0px;">Nombre</label>
              <div class="col-md-10">
                <div class="input-group">
                  <input value="@if($nombre!=''){{$nombre}}@endif" type="text" class="form-control input-sm" name="nombre" id="nombre" >
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombre').value = '';"></i>
                  </div>
                </div>  
              </div>
            </div>
            <div class="form-group col-md-1" >
              <button type="submit" class="btn btn-primary" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
            </div>
          </form>
        </div>
        <div class="col-md-3 col-sm-3 col-xs-3">
          <a href="{{route('generico.create',['agenda' => $agenda])}}"><button class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus"></span> Crear Generico</button></a>
          <a type="button" href="{{route('medicina.index', ['id' => $agenda])}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a>
        </div>
        
      </div>
    </div>
   
    
    <div class="box-body">
      <div class="col-md-12"> 
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
          <div class="table-responsive col-md-12 col-xs-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <th>Nombre</th>
                <th>Descripción</th> 
              </thead>
              <tbody>
                @foreach($genericos as $generico)
                  <tr class='clickable-row' data-href='{{ route("generico.edit", ['agenda' => $agenda, 'id' => $generico->id])}}'>
                    <td>{{$generico->nombre}}</td>
                    <td>{{$generico->descripcion}}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-5 col-xs-12">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($genericos->currentPage()-1)*$genericos->perPage()}}  / @if(($genericos->currentPage()*$genericos->perPage())<$genericos->total()){{($genericos->currentPage()*$genericos->perPage())}} @else {{$genericos->total()}} @endif de {{$genericos->total()}} registros</div>
            </div>
            <div class="col-md-7 col-xs-12">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $genericos->appends(Request::only(['fecha','cedula', 'nombres', 'proc_consul', 'pentax', 'fecha_hasta', 'id_doctor1', 'id_seguro', 'id_procedimiento', 'espid']))->links() }}
              </div>
            </div>
      </div> 
      
      
    
      
    </div>
  </div>

</section>

<script type="text/javascript">



$(document).ready(function () {

   $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
    });

   $(".clickable-row").click(function() {
    
        window.location = $(this).data("href");
    });
   

});



</script> 
@endsection