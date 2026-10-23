@extends('archivo_plano.factura_mesplano.base')
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
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-8">
        <h3 class="box-title">Archivo Plano</h3>
      </div>
      <div class="col-md-2 text-right">
          <a href="{{ route('nuevoArchivoPlano.create') }}" class="btn btn-success btn-gray">
              <i class="fa fa-plus-circle" aria-hidden="true"></i> Subir Archivo
          </a>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">Buscador Archivo</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="frm" action="{{ route('nuevoArchivoPlano.search_archivo_factura') }}">
        {{ csrf_field() }}
        <div class="col-md-12">
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="id">ID: </label>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese Id..." />
          </div>
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="mes_plano">Mes Plano: </label>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <input type="text" name="mes_plano" class="form-control" id="mes_plano" value="@if(isset($searchingVals)){{$searchingVals['mes_plano']}}@endif">
          </div>
          <div class="form-group col-md-2 col-xs-2">
            <label class="texto" for="mes_plano">Empresa: </label>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <select class="form-control" id="id_empresa" name="id_empresa">
              <option value="">Seleccione...</option>
              @foreach ($empresas as $value)
              <option @if(isset($empresa)) @if($empresa->id == $value->id ) selected @endif @endif value="{{ $value->id }}">{{ $value->nombrecomercial }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-xs-2">
            <button type="submit" id="buscar" class="btn btn-primary">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
            </button>
          </div>
        </div>
        
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('contableM.listadofactura')}}</label>
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
                        <th width="20%">Nombre</th>
                        <th width="8%">Empresa</th>
                        <th width="10%">Mes Plano</th>
                        <th width="10%">{{trans('contableM.usuariocrea')}}</th>
                        <th width="10%">{{trans('contableM.estado')}}</th>
                        <th width="15%">{{trans('contableM.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($facturas as $value)
                      <tr class="well">
                        <td>{{$value->id}}</td>
                        <td>{{$value->nombre_original}}</td>
                        <td>{{$value->empresa->nombrecomercial}}</td>
                        <td>{{$value->mes_plano}}</td>
                        <td>{{$value->user_crea->nombre1}} {{$value->user_crea->apellido1}}</td>
                        <td>@if($value->estado == '1') {{trans('laboratorio.activo')}} @elseif($value->estado =='0') {{trans('contableM.anulada')}} @else {{trans('laboratorio.activo')}} @endif</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('nuevoArchivoPlano.mostrar_pdf', ['id' => $value->id]) }}" target="_blank" class="btn btn-success btn-gray btn-xs">
                            <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                          </a>
                          @if($value->estado == '1')
                          <a onclick="anular('{{$value->id}}')" class="btn btn-danger btn-gray btn-xs"> 
                            <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                          </a>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($facturas->currentPage() - 1) * $facturas->perPage())}} / {{count($facturas) + (($facturas->currentPage() - 1) * $facturas->perPage())}} {{trans('laboratorio.de')}} {{$facturas->total()}} {{trans('laboratorio.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $facturas->appends(Request::only(['nombre_proveedor', 'observacion', 'secuencia','tipo_comprobante','fecha','id','secuencia_f','asiento']))->links() }}
                  </div>
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
    $("#empresas").select2();
  });


  function anular(id) {
    Swal.fire({
      title: `{{trans('contableM.anularfactura')}}`,
      text: "{{trans('contableM.norevertiraccion')}}",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: `{{ url('archivo_plano/factura/eliminar_archivo/${id}')}}`,
          datatype: 'json',
          data: {
            'id': id
          },
          success: function(data) {
            Swal.fire("Correcto!", "{{trans('contableM.anulacioncorrecta')}}", "success");
            setTimeout(function() {
              location.reload();
            }, 1000)
          },
          error: function(data) {
            console.log(data);
          }
        });
      }
    })
  }
</script>
@endsection