@extends('contable.rh_reporte_roles.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">

<style type="text/css">
  .valor {
    text-align: right;
  }
</style>

<div class="row head-title">
  <div class="col-md-12 cabecera">
    <label class="color_texto">ESTADISTICO DE HORAS EXTRAS</label>
  </div>
</div>

<div class="box-body dobra">
  <form method="POST" id="roles_pago" action="{{route('nuevo_rol.estadistico_horas_extras')}}">
    {{ csrf_field() }}
    @php $cantidadSucursales = count($sucursales); @endphp
    <div class="row">
    <div class="form-group col-md-3">
      <label class="col-md-6 control-label texto" for="mes">{{trans('winsumos.sucursal')}}</label>
      <div class="col-md-6 col-xs-4">

      <select id="sucursal" name="sucursal" class="form-control">
        @if($cantidadSucursales > 1)<option value="">Seleccione...</option>@endif
        @foreach ($sucursales as $sucursal)
        <option value="{{$sucursal->id}}" @if($request->id_sucursal == $sucursal->id) selected @endif>{{$sucursal->codigo_sucursal}}-{{$sucursal->nombre_sucursal}}</option>.
        @endforeach
      </select>

    </div>
    </div>
    <div class="form-group col-md-3">
      <label class="col-md-6 control-label texto" for="nombre_empleado">Nombre Empleado</label>
      <div class="col-md-6 col-xs-4">
      <input type="text" name="nombre_empleado" id="nombre_empleado" class="form-control" placeholder="Empleado" value="{{$request->nombre_empleado }}" >
    </div>

    </div>
    <div class="form-group col-md-3">
      <label class="col-md-6 control-label texto" for="anio_inicio">Año desde</label>
      <div class="col-md-6 col-xs-4">
      <input type="text" name="anio_inicio" id="anio_inicio" class="form-control" placeholder="Año" value="{{ $anio_inicio }}" >
    </div>
    </div>
    @php
    $Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    @endphp

    <div class="form-group col-md-3">
      <label class="col-md-6 control-label texto" for="mes_inicio">Mes desde</label>

      <div class="col-md-6 col-xs-4">
      <select id="mes_inicio" name="mes_inicio" class="form-control">
        <option value="">Seleccione...</option>
        @for ($i=1; $i<=12; $i++) <option value="{{$i}}" @if($i==$mes_inicio) selected @endif>{{$Meses[($i)-1]}}</option>
          @endfor
      </select>
    </div>

    </div>
    <div class="form-group col-md-3">
      <label class="col-md-6 control-label texto" for="anio_fin">Año hasta</label>
      <div class="col-md-6 col-xs-4">
      <input type="text" name="anio_fin" id="anio_fin" class="form-control" placeholder="Año" value="{{ $anio_fin }}" >
    </div>

    </div>

    <div class="form-group col-md-3">
      <label class="col-md-6 control-labeltexto" for="mes_fin">Mes hasta</label>
      <div class="col-md-6 col-xs-4">
      <select id="mes_fin" name="mes_fin" class="form-control">
        <option value="">Seleccione...</option>
        @for ($j=1; $j<=12; $j++) <option value="{{$j}}" @if($j==$mes_fin) selected @endif>{{$Meses[($j)-1]}}</option>
          @endfor
      </select>
    </div>
    </div>

    <div class="form-group col-md-3">
      <button type="submit" class="btn btn-primary">
        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
      </button>
    </div>
    </div>
  </form>
</div>

<div class="box-body dobra">
  <div class="form-group col-md-12">
    <div class="form-row">
      <div id="contenedor">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                  <tr class='well-dark'>
                    <th>Sucursales</th> <!--0-->
                    <th>Empleado</th>
                    <th>Periodo</th>
                    <th>Sueldo</th>
                    <th>Horas Extras 50%</th>
                    <th>Horas Extras 100%</th>
                    <th>Valor Horas Extras 50%</th>
                    <th>Valor Horas Extras 100%</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($roles as $rol)
                  @php

                  $sucursal = Sis_medico\Ct_Sucursales::find($rol->id_sucursal);

                  @endphp
                  <tr class="well">
                    <td class="">@if(!is_null($sucursal)){{$sucursal->nombre_sucursal}}@endif</td>
                    <!-- <td class="">{{$rol->nombres}}</td> -->
                    <td class="">{{$rol->apellido1}} {{$rol->apellido2}} {{$rol->nombre1}} {{$rol->nombre2}}</td>
                    <td class="">{{$rol->mes}}-{{$rol->anio}}</td>
                    <td class="">${{$rol->sueldo_mensual}}</td>

                    <td class="">{{$rol->cantidad_horas50}}</td>
                    <td class="">{{$rol->cantidad_horas100}}</td>


                    <td class="valor">${{number_format($rol->sobre_tiempo50,2)}}</td>
                    <td class="valor">${{number_format($rol->sobre_tiempo100,2)}}</td>



                  </tr>

                  @endforeach

                </tbody>
              </table>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>



<script type="text/javascript">
  $(document).ready(function() {

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': true,
      'searching': false,
      'ordering': true,
      'info': true,
      'autoWidth': false,
      'sInfoEmpty': true,
      'sInfoFiltered': true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      },
      dom: 'lBrtip',
      'buttons': [{
        extend: 'excelHtml5',
        footer: true,
        title: 'Reporte Horas Extras',
      }]
    });
  })
</script>

@endsection