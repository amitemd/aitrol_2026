@extends('insumos.reporte.buscador_equipo.base')
@section('action-content')
    <!-- Main content -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<section class="content">
  <div class="box">
    <div class="box-header">
    </div>
    <div class="box-body">
      <form method="POST" id="reporte_master" action="{{ route('reporte.buscador_equipo_excel') }}" >
        {{ csrf_field() }}

        <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="col-md-3 control-label">Desde</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="nombre" class="col-md-3 control-label">Nombre</label>
          <div class="col-md-9">
            <div class="input-group">
              <input value="@if($nombre !=''){{$nombre}}@endif" type="text" class="form-control input-sm" name="nombre" id="nombre" placeholder="Nombre del Equipo" >
              <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombre').value = ''; buscar();"></i>
                </div>
              </div>
          </div>
        </div>

        <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="tipo" class="col-md-3 control-label">Tipo</label>
          <div class="col-md-9">
            <div class="input-group">
              <input value="@if($tipo!=''){{$tipo}}@endif" type="text" class="form-control input-sm" name="tipo" id="tipo" placeholder="Tipo de Producto" >
              <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('tipo').value = ''; buscar();"></i>
                </div>
              </div>
          </div>
        </div>

        <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="modelo" class="col-md-3 control-label">Modelo</label>
          <div class="col-md-9">
            <div class="input-group">
              <input value="@if($modelo!=''){{$modelo}}@endif" type="text" class="form-control input-sm" name="modelo" id="modelo" placeholder="Modelo de Producto" >
              <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('modelo').value = ''; buscar();"></i>
                </div>
              </div>
          </div>
        </div>

        <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="marca" class="col-md-3 control-label">Marca</label>
          <div class="col-md-9">
            <div class="input-group">
              <input value="@if($marca!=''){{$marca}}@endif" type="text" class="form-control input-sm" name="marca" id="marca" placeholder="Marca de Producto" >
              <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('marca').value = ''; buscar();"></i>
                </div>
              </div>
          </div>
        </div>

        <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="serie" class="col-md-3 control-label">Serie</label>
          <div class="col-md-9">
            <div class="input-group">
              <input value="@if($serie!=''){{$serie}}@endif" type="text" class="form-control input-sm" name="serie" id="serie" placeholder="Serie del Producto" >
              <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('serie').value = ''; buscar();"></i>
                </div>
              </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="text-align: right;">
          <button type="submit" formaction="{{ route('reporte.buscador_usos_equipo')}}" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span></button>
        </div>

        <div class="form-group col-md-6 col-xs-6" >
          <button type="button" onclick="desc_reporte_master();" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Descargar&nbsp;</span></button>
        </div>
      </form>

      <div class="table-responsive col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Serie</th>
                <th>Tipo</th>
                <th>Marca</th>
                <th>Prestamo</th>
                <th>Paciente</th>
                <th>Fecha_Procedimiento</th>
              </tr>
            </thead>
            <tbody>

              @foreach ($productos as $value)
                  <tr>
                    <td >@if(!is_null($value->nombre)){{$value->nombre}}@endif</td>
                    <td >@if(!is_null($value->serie)){{$value->serie}}@endif</td>
                    <td >@if(!is_null($value->tipo)){{$value->tipo}}@endif</td>
                    <td >@if(!is_null($value->marca)){{$value->marca}}@endif</td>
                    <td >@if($value->prestamo == 1) Si @else No @endif</td>
                    <td >@if(!is_null($value->apellido1)){{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}}@endif</td>
                    <td >@if(!is_null($value->fecha_atencion)){{$value->fecha_atencion}}@endif</td>
                  </tr>
              @endforeach
            </tbody>
            <tfoot>
            </tfoot>
        </table>
      </div>

      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($productos)}} de {{$productos->total()}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $productos->appends(Request::only(['desde_inicio', 'hasta_fin', 'nombre', 'tipo', 'modelo', 'marca', 'serie']))->links() }}
          </div>
        </div>
      </div>

      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">

      </div>
    </div>
  </div>
</section>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

  <script type="text/javascript">

    function desc_reporte_master (){
      $('#reporte_master').submit();
    }

    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '@if(!is_null($fecha)){{$fecha}}@else{{date("Y/m/d")}}@endif',
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '@if(!is_null($fecha_hasta)){{$fecha_hasta}}@else{{date("Y/m/d")}}@endif',

            });
        $("#fecha").on("dp.change", function (e) {
            //buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            //buscar();
        });
  });

  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });

function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}

 </script>
@endsection
