@extends('hospital_iess.hospitalizados.base')
@section('action-content')
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
          <h3 class="box-title">Pacientes Hospitalizados</h3>
        </div>
        <div class="form-group col-md-3">
          <a class="btn btn-primary" href="{{ route('hospitalizados.create')}}"><span class="glyphicon glyphicon-user"></span>  Agregar</a>
        </div>
        <div class="form-group col-md-3">
          <a class="btn btn-primary" href="{{ route('hospitalizados.altas')}}"><span class="glyphicon glyphicon-ok"></span>  Altas</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('hospitalizados.buscar')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-6">
          <label for="observaciones" class="col-md-2 control-label">Paciente</label>
          <div class="col-md-10">
            <input id="paciente" type="text" class="form-control input-sm" name="paciente" value="@if(!is_null($paciente)){{ $paciente }}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="NOMBRES Y APELLIDOS">
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
                <th width="10%">Ingreso</th>
                <th width="20%">Paciente</th>
                <th width="15%">Ubicacion - Sala</th>
                <th width="10%">Seguro</th>
                <th width="10%">Doctor</th>
                <th width="25%">Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($hospitalizados as $value)
                <tr role="row">
                  <td >{{ substr($value->fechaini,0,10)}}</td>
                  <td >{{$value->nombre1}} @if($value->nombre2!='(N/A)'){{$value->nombre2}}@endif {{$value->apellido1}} @if($value->apellido2!='(N/A)'){{$value->apellido2}}@endif</td>
                  <td >{{ $value->procedencia }} - {{ $value->sala_hospital }}</td>
                  <td >{{ $value->snombre }}</td>
                  <!--td >@if($value->estado=='0') ALTA @else HOSPITALIZADO @endif</td-->
                  <td>@if($value->id_doctor1!=null){{ $value->dnombre1 }} {{ $value->dapellido1 }}@endif</td>
                  <td>  
                      <div class="form-group col-md-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('hospitalizados.edit', ['id' => $value->id]) }}" class="btn btn-block btn-warning btn-xs" data-toggle="modal" data-target="#doctor">
                        <span class="glyphicon glyphicon-edit"></span> Editar
                        </a>  
                      </div>
                      <div class="form-group col-md-3">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('hospitalizados.alta', ['id' => $value->id]) }}" class="btn btn-block btn-success btn-xs" >
                        <span class="glyphicon glyphicon-ok"></span> Alta
                        </a>  
                      </div>  
                      <div class="form-group col-md-1">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('hospitalizados.inactivar', ['id' => $value->id]) }}" class="btn btn-block btn-danger btn-xs"><span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="" data-original-title="Eliminar"></span></a>  
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($hospitalizados)}} de {{$hospitalizados->total()}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $hospitalizados->appends(Request::only(['id', 'apellidos', 'nombres']))->links()}}
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

    $(".breadcrumb").append('<li class="active">Hospitalizados</li>');

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