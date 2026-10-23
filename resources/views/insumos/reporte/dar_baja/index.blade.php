@extends('insumos.reporte.dar_baja.base')
@section('action-content')
    <!-- Main content -->
    <link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="modal fade" id="buscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 60%;">
    <div class="modal-content">

    </div>
  </div>
</div>

<section class="content">
  <div class="box">
    <div class="box-header">
    </div>
    <div class="box-body">
      <form method="POST" id="reporte_master" action="{{route('descarga.dar_baja.index')}}" >
        {{ csrf_field() }}
        
        <div class="form-group col-md-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="col-md-3 control-label">{{trans('winsumos.fecha_desde')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="date" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>
            
        <div class="form-group col-md-4" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="col-md-3 control-label">{{trans('winsumos.fecha_hasta')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="date" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>


        <div class="form-group col-md-2 col-xs-6" style="text-align: right;">
          <button type="submit" formaction="{{route('descarga.dar_baja.index')}}" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos.Buscar')}}&nbsp;</span></button>
        </div>

        <div class="form-group col-md-2 col-xs-6" >
          <button type="submit" formaction="{{route('reporte.dar_baja')}}" class="btn btn-primary btn-sm" id="boton_descargar">
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Descargar&nbsp;</span></button>
        </div>
      </form>

      <table id="dar" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                <tr>

                                         <th>Fecha</th>
                                        <th> Insumo </th>
                                        <th> Serie </th>
                                        <th> Cantidad </th>
                                        <th> Observación </th>
                                        <th> Marca </th>
                                       
                                    </tr>
                                </thead>
                           
                              @foreach ($dar_baja as $dar_ba)

                                <tr>

                                        <td>{{date('Y-m-d', strtotime($dar_ba->updated_at))}}</td>
                                        <td>{{$dar_ba->nombre}}</td>
                                        <td>{{$dar_ba->serie}}</td>
                                        <td>{{$dar_ba->cantidad}}</td>
                                        <td>{{$dar_ba->referencia}}</td>
                                        <td>{{$dar_ba->nombre_marca}}</td>

                                </tr>
                                @endforeach 
                                 
                            




      </table>
     

      </div>
    </div>
  </div>
</section>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

  
@endsection

