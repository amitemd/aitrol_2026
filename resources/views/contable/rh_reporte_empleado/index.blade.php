@extends('contable.rh_reporte_empleado.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>


<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Nomina</a></li>
      <li class="breadcrumb-item active" aria-current="page">Reporte Datos Empleados</li>
    </ol>
  </nav>
  <div class="box">
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">REPORTE DATOS EMPLEADO POR EMPRESA</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_empleado">
        {{ csrf_field() }}
          @php 
            $id_empresa = Session::get('id_empresa');
          @endphp
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="id_empresa">{{trans('agenda.empresa')}}: </label>
          </div>
          <div class="form-group col-md-5 col-xs-10 container-4">
            <select class="form-control" id="id_empresa" name="id_empresa">
                <option value="">{{trans('agenda.seleccione')}}...</option> 
                @foreach($empresas as $value)
                  <option @if($value->id == $id_empresa) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                @endforeach
            </select>
          </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id_sucursal">Sucursal: </label>
        </div>
        <div class="form-group col-md-3 col-xs-3 container-2">
          <select class="form-control" id="id_sucursal" name="id_sucursal">
            
           
          </select>
        </div>
      <div class="form-group col-md-4 col-xs-4 pull-right">
        <button type="submit" class="btn btn-primary" formaction="{{route('reporte_datos_empl')}}">
          <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Datos Empleados
        </button>
      </div>
      </form>
  </div>
  <!--<div class="box box" style="border-radius: 8px;" id="area_trabajo">
      </div>-->
  </div>

  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>

</section> 


<script>
  window.onload = function() {
    sucursales()
  }
  const sucursales = () => {
    let id_empresa = document.getElementById("id_empresa").value;
    $.ajax({
      url: `{{ route("nomina.reporterol.buscarSucursales")}}`,
      type: "get",
      dataType: "json",
      data : {
        'id_empresa': id_empresa
      },
      success: function (data) {
        $("#id_sucursal").empty();
        $("#id_sucursal").append(data.option);
      }, error: function (data) {

      }
    })
  }
</script>

@endsection
