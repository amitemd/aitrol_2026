@extends('activosfijos.documentos.orden.base')
@section('action-content')
<style>
  .autocomplete {
    z-index: 999999 !important;
    z-index: 999999999 !important;
    z-index: 99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
  }

  .ui-autocomplete {
    z-index: 5000;
  }

  .ui-autocomplete {
    z-index: 999999;
    list-style: none;
    background-color: #FFFFFF;
    width: 300px;
    border: solid 1px #EEE;
    border-radius: 5px;
    padding-left: 10px;
    line-height: 2em;
  }
</style>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
    
      <li class="breadcrumb-item"><a href="#">{{trans('agenda.ordenes_')}}</a></li>
      <li class="breadcrumb-item">{{trans('contableM.listado')}}</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-8">
        <h3 class="box-title">{{trans('contableM.prefac')}}</h3>
      </div>

      <div class="col-md-1">
        <button onclick="" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>{{trans('contableM.neworden')}}
        </button>
      </div>

    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="id">{{trans('contableM.id')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
        </div>
        
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="secuencia_f">{{trans('contableM.secuencia')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="secuencia_f" name="secuencia_f" value="@if(isset($searchingVals)){{$searchingVals['secuencia']}}@endif" placeholder="Ingrese Secuencia..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('contableM.fecha')}}: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha_compra']}}@endif">
          </div>
        </div>

        <div class="col-md-offset-5 col-xs-2">
          <button type="submit" id="buscarEmpleado" class="btn btn-primary">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('contableM.listado')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class='well-dark'>
                        <th width="5%">{{trans('contableM.id')}}</th>
                        <th width="5%">{{trans('contableM.tipo')}}</th>
                        <th width="20%">{{trans('contableM.secuencia')}}</th>
                        <th width="10%">{{trans('contableM.fechacompra')}}</th>
                        <th width="20%">{{trans('contableM.proveedor')}}</th>
                        <th width="10%">{{trans('contableM.usuariocrea')}}</th>
                        <th width="12%">{{trans('contableM.estado')}}</th>
                        <th width="15%">{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                     @foreach($ordenes as $value)
                        <tr>
                            <td>{{$value->id_log}}</td>
                            <td>@if($value->tipo_log == 1) {{trans('contableM.OrdenActivoFijo')}} @else {{trans('contableM.ordenimpor')}} @endif</td>
                            <td>{{$value->secuencia_orden}}</td>
                            <td>{{$value->fecha_compra}}</td>
                            <td>{{$value->nombre_proveedor}}</td>
                            <td>{{$value->nombre_usuario}}</td>
                            <td>@if($value->estado_log == 1) {{trans('laboratorio.activo')}} @endif</td>
                            <td>
                                <a type="button" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                                <a type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                            </td>

                        </tr>
                     @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              
              
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">

          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false,
    'order': [
      [0, "desc"]
    ]
  });
  $(document).ready(function() {

    $("#nombre_proveedor").select2();
    $("#id_usuariocrea").select2();

    $('#fecha').datetimepicker({
      format: 'DD/MM/YYYY'
    });

    



  });


  
</script>
@endsection