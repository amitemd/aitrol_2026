@extends('hc_admision.medicina.base')
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
  <div class="box box-primary">
    <div class="box-header with-border">
      
        <div class="col-md-2 col-sm-2 col-xs-2">
          <h3 class="box-title">LISTADO DE MEDICINAS</h3>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-5">
          <form method="POST" action="{{ route('medicina.search',['agenda' => $agenda]) }}" >
            {{ csrf_field() }}
            <div class="form-group col-md-10" style="padding-right: 0px;">
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
        <div class="col-md-5 col-sm-5 col-xs-5">
          
          <a href="{{route('medicina.create',['agenda' => $agenda])}}"><button class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus"></span> Crear Medicina</button></a>
          <a href="{{route('generico.index',['agenda' => $agenda])}}"><button class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-list-alt"></span> Genéricos</button></a>
          <a type="button" href="{{route('agenda.detalle', ['id' => $agenda])}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a>
        </div>
        
        
      
    </div>
   
    
    <div class="box-body">
      <div class="col-md-12"> 
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
          <div class="table-responsive col-md-12 col-xs-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 14px;">
              <thead>
                <th>Nombre</th>
                <th>Dosis</th>
                <th>Cantidad</th>
                <!--th>Concentración</th> 
                <th>Presentación</th-->
                <th>Genérico</th>
                <!--th>Tipo</th-->  
              </thead>
              <tbody>
                @foreach($medicinas as $medicina)
                  <tr class='clickable-row' data-href='{{ route("medicina.edit", ['agenda' => $agenda,'id' => $medicina->id])}}'>
                    <td>{{$medicina->nombre}}</td>
                    <td>{{$medicina->dosis}}</td>
                    <td>{{$medicina->cantidad}}</td>
                    <!--td>{{$medicina->concentracion}}</td>
                    <td>{{$medicina->presentacion}}</td-->
                    <td>
                      @php 
                        $medicina_principio = DB::table('medicina_principio')->where('id_medicina',$medicina->id)->get(); 
                      @endphp 
                      @foreach($medicina_principio as $md)    
                        <span class="label label-info">{{$genericos->where('id',$md->id_principio_activo)->first()->nombre}}</span>
                      @endforeach</td>
                    <!--td>@if($medicina->publico_privado=='0')Público @else Privado @endif</td-->
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-5 col-xs-12">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($medicinas->currentPage()-1)*$medicinas->perPage()}}  / @if(($medicinas->currentPage()*$medicinas->perPage())<$medicinas->total()){{($medicinas->currentPage()*$medicinas->perPage())}} @else {{$medicinas->total()}} @endif de {{$medicinas->total()}} registros</div>
            </div>
            <div class="col-md-7 col-xs-12">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $medicinas->appends(Request::only(['nombre']))->links() }}
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