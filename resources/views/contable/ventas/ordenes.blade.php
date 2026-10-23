@extends('contable.ventas.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

  <!-- Main content -->
  <section class="content">
  <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Contable</a></li>
    <li class="breadcrumb-item"><a href="#">Ventas</a></li>
    <li class="breadcrumb-item active">&Oacute;rdenes de Ventas</li>

  </ol>
</nav>
    <div class="box">
      <div class="box-header header_new">
            <div class="col-md-9">
              <h3 class="box-title">&Oacute;rdenes de Venta </h3>
            </div>
            <div class="col-md-1 text-right">
                <a href="{{route('orden_crear')}}" class="btn btn-success btn-gray" >
                <i aria-hidden="true"></i>Nueva Orden
                </a>
            </div>
            @php
              $empresa_labs = Sis_medico\Empresa::where('prioridad_labs', '1')->first();
              if(isset($empresa_labs)){
                $id_empresa_labs = $empresa_labs->id;
              }else{
                $id_empresa_labs = "";
              }
            @endphp
            @if($empresa->id == $id_empresa_labs)
            <div class="col-md-1 text-right">
                <a class="btn btn-success btn-gray" onclick="cargar_ordenes();">
                <i aria-hidden="true"></i>Cargar Ordenes
                </a>
            </div>
            @endif
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR &Oacute;RDENES DE VENTA</label>
          </div>
        </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('orden_venta') }}" >
          {{ csrf_field() }}
          <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="numero">Id: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="id" name="id" value="@if(isset($request)){{$request['id']}}@endif" placeholder="Ingrese Id..." />
            </div>
          
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="cliente">Cliente: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="nombre_cliente" name="nombre_cliente" value="@if(isset($request)){{$request['nombre_cliente']}}@endif" placeholder="Ingrese el nombre del cliente..." />
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="paciente">Paciente: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="nombres_paciente" name="nombres_paciente" value="@if(isset($request)){{$request['nombres_paciente']}}@endif" placeholder="Ingrese el nombre del paciente..." />
            </div>

            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="fechaDesde">Fecha Desde: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input autocomplete="off" type="text"  name="fecha_desde" class="form-control fecha" id="fecha_desde" value="@if(isset($request)){{$request['fecha_desde']}}@endif">
                  </div>
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="fechaHasta">Fecha Hasta: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input autocomplete="off" type="text"  name="fecha_hasta" class="form-control fecha" id="fecha_hasta" value="@if(isset($request)){{$request['fecha_hasta']}}@endif">
                  </div>
            </div>
            <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="nro_comprobante"> No. de Comprobante: </label>
            </div>
            <div class="form-group col-md-3 col-xs-10 container-4">
              <input class="form-control" type="text" id="nro_comprobante" name="nro_comprobante" value="@if(isset($request)){{$request['nro_comprobante']}}@endif" placeholder="Ingrese el no. de comprobante..." />
            </div>
  
            <div class="col-md-offset-6 col-xs-2">
              <button type="submit" id="buscarAsiento" class="btn btn-primary">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
              </button>
            </div>
        </form>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" >VENTAS</label>
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
                <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr class="well-dark">
                      <th width="5%">Codigo</th>
                      <th width="5%">Tipo</th>
                      <th width="10%">Fecha </th>
                      <th width="10%">Cliente</th>
                      <!--<th width="10%">RUC/CID</th>-->
                      <th width="10%">Paciente</th>
                      <th width="10%">Seguro</th>
                      <th width="10%">Procedimiento</th>
                      <th width="10%">Usuario Modifica</th>
                      <th width="10%">Estado</th>
                      <th width="10%">Acción</th>
                    </tr>
                  </thead>
                  <tbody>

                   @foreach($ventas as $value)
                   <tr class="well">
                        <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                        <td>VEN-OR</td>
                        <td>@if(!is_null($value->fecha)){{substr($value->fecha,0,11)}}@endif</td>
                        @php
                          $cliente = \Sis_medico\Ct_Clientes::where('identificacion',$value->id_cliente)->first();
                          $seguro = Sis_medico\Seguro::find($value->seguro_paciente);
                        @endphp
                        <td>@if(!is_null($cliente->nombre)){{$cliente->nombre}}@endif</td>
                       <!-- <td>@if(!is_null($cliente->cedula_representante)){{$cliente->cedula_representante}}@endif</td>-->
                        <td>@if(!is_null($value->nombres_paciente)){{$value->nombres_paciente}}@endif</td>
                        <td>@if(!is_null($seguro)){{$seguro->nombre}}@endif</td>
                        <td>@if(!is_null($value->procedimientos)){{$value->procedimientos}}@endif</td>

                        <td>@if(!is_null($value->id_usuariomod)){{$value->id_usuariomod}}@endif</td>

                        <td>@if(!is_null($value->fecha_procedimiento))@if($value->estado == "1") {{$value->estado_pago == "1" ? "Pagada" : "Por facturar"}} @else Anulada @endif @endif</td>
                        <!--<td>@if($value->estado == '1') Activo @endif</td>-->
                        <td style="padding-left: 1px;padding-right: 1px;">
                            <!--<a href="#" class="btn btn-success btn-gray" ><i class="glyphicon glyphicon-ok" aria-hidden="true"></i></a>-->
                            <a href="{{ route('orden_editar', ['id' => $value->id]) }}" class="btn btn-warning btn-gray" >
                              <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                            </a>
                            @if(($value->estado)==1)
                              <a href="javascript:anular_orden({{$value->id}});" class="btn btn-danger btn-gray"><i class="fa fa-trash"> </i> </a>
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
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($ventas->currentPage() - 1) * $ventas->perPage())}} / {{count($ventas) + (($ventas->currentPage() - 1) * $ventas->perPage())}} de {{$ventas->total()}} registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $ventas->appends(Request::only(['id', 'numero','id_asiento','fechaDesde','fechaHasta','nonbres_paciente','nombre_cliente']))->links() }}
                </div>
              </div>
            </div>
        </div>
            </div>
          </div>
        </div>
      </div>


    </div>
  </section>
  <script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
  function cargar_ordenes(){
    
    $.ajax({
      type: 'get',
      url:"{{ url('factura/labs/crea_ventas') }}",
      success: function(data){
        swal.fire({
          title: 'Ordenes Agregadas',
          //text: "You won't be able to revert this!",
          icon: "info",
          type: 'warning',
          buttons: true,
        
        }).then((result) => {
          if (result.value) {
            location.reload();
          }
        })
      },
      error: function(data){
        
      }
    }); 
  } 

        $(function() {

            $('#fecha_desde').datetimepicker({
                format: 'YYYY/MM/DD'
            });
            $('#fecha_hasta').datetimepicker({
                format: 'YYYY/MM/DD'
            });
            

  });
  function anular_orden(id) {
    Swal.fire({
      title: "¿Desea anular esta orden?",
      text: "{{trans('wfact_venta.no_revertir')}}",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: '{{trans('wfact_venta.si')}}'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/ventas/ordenes/eliminar/')}}/" + id,
          datatype: 'json',
          success: function(data) {
            Swal.fire("{{trans('wfact_venta.correcto')}}", "{{trans('wfact_venta.anulacion_correcta')}}", "success");
            location.href = "{{route('orden_venta')}}";
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
