@extends('insumos.reporte.buscador_master.base')
@section('action-content')
    <!-- Main content -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<section class="content">
  <div class="box">
      <div class="box-header">
      </div>
       <div class="box-body">
          <form method="POST" id="reporte_master" action="{{ route('reporte.reporte_master') }}" >
            {{ csrf_field() }}

              <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                  <label for="fecha" class="col-md-3 control-label">{{trans('winsumos.fecha_desde')}}</label>
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

                <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                  <label for="fecha_hasta" class="col-md-3 control-label">{{trans('winsumos.fecha_hasta')}}</label>
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

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="cedula" class="col-md-3 control-label">{{trans('winsumos.codigo_producto')}}</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($codigo!=''){{$codigo}}@endif" type="text" class="form-control input-sm" name="codigo" id="codigo" placeholder="{{trans('winsumos.codigo_producto')}}" >
	                <div class="input-group-addon">
	                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('codigo').value = ''; buscar();"></i>
	                  </div>
	                </div>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="nombres" class="col-md-3 control-label">{{trans('winsumos.ingrese_nombre_producto')}}</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($nombre!=''){{$nombre}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="ingrese_nombre_producto" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = ''; buscar();"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="bodega" class="col-md-3 control-label">{{trans('winsumos.bodegas')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm select2_cuentas" name="bodega" id="bodega" onchange="buscar();">
                  <option value="">{{trans('winsumos.seleccione')}}</option>
                  	@foreach($bodega_nombre as $value)
	                  <option @if($value->id==$bodega) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
	                @endforeach
                </select>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="proveedor" class="col-md-3 control-label">{{trans('winsumos.proveedores')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm select2_cuentas" name="proveedor" id="proveedor" onchange="buscar();">
                  <option value="">{{trans('winsumos.seleccione')}}</option>
                    @foreach($proveedor as $value)
                    <option @if($value->id==$proveedores) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="marca" class="col-md-3 control-label">{{trans('winsumos.marca')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm select2_cuentas" name="marca" id="marca" onchange="buscar();">
                  	<option value="">{{trans('winsumos.seleccione')}}</option>
                	@foreach($marca_nombre as $value)
	                  <option @if($value->id==$marca) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
	                @endforeach
                </select>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="tipo_producto" class="col-md-3 control-label">{{trans('winsumos.tipo_producto')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm select2_cuentas" name="tipo_producto" id="tipo_producto" onchange="buscar();">
                    <option value="">{{trans('winsumos.seleccione')}}</option>
                  @foreach($tipo_producto as $value)
                    <option @if($value->id==$tipo_prod) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
              </div>
            </div>
          
            <div class="form-group col-md-3 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
              <label for="tipo" class="col-md-3 control-label"> Tipo Documento </label>
              <div class="col-md-9">
                <select class="form-control input-sm select2_cuentas" name="tipo" id="tipo" onchange="buscar();">
                    <option value="">{{trans('winsumos.seleccione')}}</option>
                    <option @if($tipo==1) selected @endif value="1">{{trans('winsumos.guia_remision')}}</option>
                    <option @if($tipo==3) selected @endif value="3">{{trans('winsumos.fact_contra_entrega')}}</option>
                </select>
              </div>
            </div>

            <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="tipo" class="col-md-3 control-label">{{trans('winsumos.estado')}}</label>
              <div class="col-md-9">
                <select class="form-control input-sm select2_cuentas" name="caducados" id="caducados" onchange="buscar();">
                    <option  value="">{{trans('winsumos.seleccione')}}</option>
                    <option  value="1">{{trans('winsumos_reportes.caducados')}}</option>
                    <option  value="2">{{trans('winsumos_reportes.no_caducados')}}</option>
                </select>
              </div>
            </div>

            <div class="form-group col-md-2 col-xs-5">
              <button type="submit" formaction="{{ route('reporte.buscador_master')}}" class="btn btn-primary btn-sm" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos.Buscar')}}&nbsp;</span></button>
            </div>

            <div class="form-group col-md-4 col-xs-4" >
              <button type="submit" formation="{{route('reporte.reporte_master')}}" class="btn btn-primary btn-sm" id="boton_buscar">
                <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('winsumos_reportes.descargar')}}&nbsp;</span></button>
            </div>

            <div class="table-responsive col-md-12">
  			      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  			        <thead>
  			          <tr>
  			            <th>{{trans('winsumos.codigo')}}</th>
  			            <th>{{trans('winsumos.nombre')}}</th>
  			            <th>{{trans('winsumos.descripcion')}}</th>

                    <th>{{trans('winsumos.bodega')}}  </th>
                    <th>{{trans('winsumos.cantidad')}}</th>
                    <th> {{trans('winsumos.Tipo')}} </th>
                    <th>{{trans('winsumos.serie')}}</th>
                    <th>{{trans('winsumos.pedidos')}}</th>
        
  			            <th>{{trans('winsumos.proveedores')}}</th>
             

                    <th>{{trans('winsumos.marca')}}</th>
  			            <th>{{trans('winsumos.tipo_producto')}}</th>      
                    
  			            <th>{{trans('winsumos.fecha_vencimiento')}}</th>

  			            <th>{{trans('winsumos.estado')}}</th>
  			          </tr>
  			        </thead>
  			        <tbody>
                  
  			          @foreach ($productos[0] as $value)
                      <tr>
                        <td >@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                        <td >@if(!is_null($value->nombre_producto)){{$value->nombre_producto}}@endif</td>
                        <td >@if(!is_null($value->descripcion)){{$value->descripcion}}@endif</td>
                        
                        <td >@if(!is_null($value->nombre_bodega)){{$value->nombre_bodega}}@endif </td>
                        <td >@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                        @php
                        if(!is_null($value->tipo)){
                          if($value->tipo == 'I'){
                          $tipo = "Ingreso";
                          }else if($value->tipo == 'E'){
                          $tipo = "Egreso";
                          }elseif($value->tipo == 'T'){
                          $tipo = "Traslado";
                          }
                        }
                        @endphp
                        <td>{{$tipo}} </td>
                        <td >@if(!is_null($value->serie)){{$value->serie}}@endif</td>
                        <td >@if(!is_null($value->pedido)){{$value->pedido}}@endif</td>

                        <td >@if(!is_null($value->nombrecomercial)){{$value->nombrecomercial}}@endif</td>
         
                        <td >@if(!is_null($value->nombre_marca)){{$value->nombre_marca}}@endif</td>
                        <td >@if(!is_null($value->nombre_tipop)){{$value->nombre_tipop}}@endif</td>   
                        <td >@if(!is_null($value->fecha_vence)){{$value->fecha_vence}}@endif</td>
                        @if($value->fecha_vence <= $fecha_hoy)
                        <td style="background-color: red; color: white">{{trans('winsumos.ProductoCaducado')}}</td>
                        @else
                        <td style="background-color: yellow; color: black">{{trans('winsumos.ProductoNoCaducado')}}</td>
                        @endif
                      </tr>
  		            @endforeach
                                   
  			          @foreach ($productos[1] as $value)
                      <tr>
                        <td >@if(!is_null($value->codigo)){{$value->codigo}}@endif</td>
                        <td >@if(!is_null($value->nombre_producto)){{$value->nombre_producto}}@endif</td>
                        <td >@if(!is_null($value->descripcion)){{$value->descripcion}}@endif</td>
                        
                        <td >@if(!is_null($value->nombre_bodega)){{$value->nombre_bodega}}@endif </td>
                        <td >@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                        @php
                        if(!is_null($value->tipo)){
                          if($value->tipo == 'I'){
                          $tipo = "Ingreso";
                          }else if($value->tipo == 'E'){
                          $tipo = "Egreso";
                          }elseif($value->tipo == 'T'){
                          $tipo = "Traslado";
                          }
                        }
                        @endphp
                        <td>{{$tipo}} </td>
                        <td >@if(!is_null($value->serie)){{$value->serie}}@endif</td>
                        <td >@if(!is_null($value->pedido)){{$value->pedido}}@endif</td>

                        <td >@if(!is_null($value->nombrecomercial)){{$value->nombrecomercial}}@endif</td>
         
                        <td >@if(!is_null($value->nombre_marca)){{$value->nombre_marca}}@endif</td>
                        <td >@if(!is_null($value->nombre_tipop)){{$value->nombre_tipop}}@endif</td>   
                        <td >@if(!is_null($value->fecha_vence)){{$value->fecha_vence}}@endif</td>
                        @if($value->fecha_vence <= $fecha_hoy)
                        <td style="background-color: red; color: white">{{trans('winsumos.ProductoCaducado')}}</td>
                        @else
                        <td style="background-color: yellow; color: black">{{trans('winsumos.ProductoNoCaducado')}}</td>
                        @endif
                      </tr>
  		            @endforeach
  			        </tbody>
  			        <tfoot>
  			        </tfoot>
  			      </table>
			      </div>

      			<div class="row">
      	          <div class="col-sm-5">
                  {{--<div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} 1 {{trans('winsumos.a')}} {{count($productos)}} {{trans('winsumos.de')}} {{$productos->total()}} {{trans('winsumos.registros')}}</div>
      	          </div>
      	          <div class="col-sm-7">
      	            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
      	              {{ $productos->appends(Request::only(['desde_inicio', 'hasta_fin', 'codigo', 'nombres', 'bodega', 'marca', 'tipo_producto', 'proveedor']))->links() }}
      	            </div>
      	          </div>
      	    </div>--}}
          </form>
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
          </div>
        </div>
  </div>
</section>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

  <script type="text/javascript">
    $('.select2_cuentas').select2({
            tags: false
        }); 

  	function desc_reporte_master (){
      $('#reporte_master').submit();
    }

    $(function () {
      $('#fecha').datetimepicker({
          format: 'YYYY/MM/DD',
          defaultDate: '{{$fecha}}',

          });
      $('#fecha_hasta').datetimepicker({
          format: 'YYYY/MM/DD',
          defaultDate: '{{$fecha_hasta}}',

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