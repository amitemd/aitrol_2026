@extends('laboratorio.examen_costo.base')
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
          <h3 class="box-title">Listado de Exámenes de Laboratorio</h3>
        </div>
        <div class="form-group col-md-3">
          <!--a class="btn btn-primary" href="{{ route('examen.create')}}"> Agregar</a-->
        </div>
        <div class="form-group col-md-3 col-xs-3">
            <a type="button" class="btn btn-primary" href="{{route('examen_costo.reporte')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar</a>
          </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      
      <form method="POST" action="{{route('examen_costo.search')}}">
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
                <!--th >Agrupador</th-->
                @foreach($niveles as $nivel)
                @if($nivel->id=='4')
                <th >HM</th>
                @else  
                <th >N.{{$nivel->id}}</th>
                @endif
                @endforeach
                <th >PART</th>
                <th >REAC</th>
                <th >IMPL</th>
                <th >ST</th>                 
                @foreach($niveles as $nivel)
                @if($nivel->id=='4')
                <th >I.H</th>
                @else    
                <th >I.{{$nivel->id}}</th>
                @endif
                <th >%</th>
                @endforeach
                <th >I.P</th>
                <th >%</th>
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($examenes as $value)
              <tr role="row">
                <td>{{$value->descripcion}}</td>
                <!--td>{{$value->eanombre}}</td-->
                @foreach($niveles as $nivel)  
                <td @if($nivel_seg[$value->id][$nivel->id] <= 0) style="background-color: yellow;" @endif>{{$nivel_seg[$value->id][$nivel->id]}}</td>
                @endforeach 
                <td @if($value->valor <= 0) style="background-color: yellow;" @endif>{{$value->valor}}</td>
                <td @if($value->valor_reactivo <= 0) style="background-color: yellow;" @else style="background-color: #ccffff;" @endif>{{$value->valor_reactivo}}</td>
                <td @if($value->valor_implementos <= 0) style="background-color: yellow;" @else style="background-color: #ccffff;" @endif>{{$value->valor_implementos}}</td>
                @php $subtotal = $value->valor_reactivo + $value->valor_implementos; @endphp
                <td @if($subtotal <= 0) style="background-color: yellow;" @endif>{{$subtotal}}</td>
                @foreach($niveles as $nivel)
                @php $valornivel = $nivel_seg[$value->id][$nivel->id] - $subtotal;  
                if($subtotal >0) { $prc = round($valornivel / $subtotal * 100,2); } else { $prc = 0; } @endphp  
                <td @if($valornivel <= 0) style="background-color: yellow;" @else style="background-color: #ccffff;" @endif>{{$valornivel}}</td>
                <td @if($prc <= 0) style="background-color: yellow;" @endif>{{$prc}}</td>
                @endforeach 
                @php $valor_p = $value->valor - $subtotal;  
                if($subtotal >0) { $prc_p = round($valor_p / $subtotal * 100,2); } else { $prc_p = 0; } @endphp  
                <td @if($valor_p <= 0) style="background-color: yellow;" @else style="background-color: #ccffff;" @endif>{{$valor_p}}</td>
                <td @if($prc_p <= 0) style="background-color: yellow;" @endif>{{$prc_p}}</td>
                <td><div class="form-group col-md-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('examen_costo.edit', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" >
                        <span class="glyphicon glyphicon-edit"></span></a>  
                      </div>
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
            {{ $examenes->appends(Request::only(['id', 'apellidos', 'nombres']))->links()}}
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
      'autoWidth'   : false
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 

</script>  

@endsection