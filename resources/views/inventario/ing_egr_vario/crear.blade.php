@extends('insumos.ingreso.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<style type="text/css">
  .ui-corner-all {
    -moz-border-radius: 4px 4px 4px 4px;
  }

  .ui-widget {
    font-family: Verdana, Arial, sans-serif;
    font-size: 15px;
  }

  .ui-menu {
    display: block;
    float: left;
    list-style: none outside none;
    margin: 0;
    padding: 2px;
  }

  .ui-autocomplete {
    overflow-x: hidden;
    max-height: 200px;
    width: 1px;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    float: left;
    display: none;
    min-width: 160px;
    _width: 160px;
    padding: 4px 0;
    margin: 2px 0 0 0;
    list-style: none;
    background-color: #fff;
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
    *border-right-width: 2px;
    *border-bottom-width: 2px;
  }

  .ui-menu .ui-menu-item {
    clear: left;
    float: left;
    margin: 0;
    padding: 0;
    width: 100%;
  }

  .ui-menu .ui-menu-item a {
    display: block;
    padding: 3px 3px 3px 3px;
    text-decoration: none;
    cursor: pointer;
    background-color: #ffffff;
  }

  .ui-menu .ui-menu-item a:hover {
    display: block;
    padding: 3px 3px 3px 3px;
    text-decoration: none;
    color: White;
    cursor: pointer;
    background-color: #006699;
  }

  .ui-widget-content a {
    color: #222222;
  }

  .td_der {
    text-align: right;
  }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
@php
  $tipo_usuario  = Auth::user()->id_tipo_usuario;
@endphp
<!-- Main content -->
<section class="content">
  <form method="POST" id="frm_ingreso" name="frm_ingreso">
    {{ csrf_field() }}
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="col-sm-4">
            <h3 class="box-title">{{trans('winsumos.ingreso')}} / {{trans('winsumos.egreso')}}</h3>
          </div>
          <div class="col-md-8" style="text-align: right;">
            <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
              <span class="glyphicon glyphicon-arrow-left">{{trans('winsumos.regresar')}}</span>
            </a>
          </div> 
        </div>
        <div class="row">
          <div class="panel-heading">
            <div class="row"> 
              <!-- update headers type date: 17 Nov 2020  -->
              <div class="form-group col-md-6{{ $errors->has('bodega_recibe') ? ' has-error' : '' }}">
                <label for="bodega_recibe" class="col-md-4 control-label">{{trans('winsumos.bodegas')}}</label>
                <div class="col-md-8">
                  <select name="bodega_recibe" class="form-control select2 " style="width: 100%;" required=""
                    name="bodega_recibe" id="bodega_recibe">
                    <!--l-->
                    @foreach($bodegas as $value) 
                    <option value="{{$value->id}}" @if(env('BODEGA_PRINCIPAL',1)==$value->id) selected
                      @endif>{{$value->nombre}}&nbsp;@if(isset($value->empresa))-&nbsp;{{$value->empresa->nombrecomercial}}@endif
                    @endforeach
                  </select>
                </div>
              </div>
              <!--div class="form-group col-md-6{{ $errors->has('tipo') ? ' has-error' : '' }}">
                <label for="tipo" class="col-md-4 control-label">{{trans('winsumos.Tipo')}}</label>
                <div class="col-md-8">
                  <select name="tipo" class="form-control select2 " style="width: 100%;" required="" name="tipo"
                    id="tipo">
                    <option value="I">{{trans('winsumos.ingreso')}}</option>
                    <option value="E">{{trans('winsumos.egreso')}}</option>
                    <option value="R">  Ingreso Por Regalo     </option>
                    <option value="C"> Egreso Uso Consumible </option>
                    <option value="N"> Egreso No Reuso </option>
                    <option value="G"> Ingreso Vario ER</option>
                    <option value="U"> Egreso Vario ER </option>
                    @if($tipo_usuario == 1)
                      <option value="P"> Egreso Por Planilla </option>
                    @endif
                    <option value="M"> Egreso por Devolucion Mercaderia </option>
                  </select>
                </div>
              </div-->
              <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                  <label for="id_proveedor" class="col-md-4 control-label">Tipo</label>
                  <div class="col-md-8">
                    <select name="tipo" style="width: 100%;" class="form-control select2_tipo" required="" id="tipo">
                        <option value="0">{{trans('winsumos.seleccione')}}</option>
                      @foreach($tipos_ing_egr as $value)
                        <option value="{{$value->abreviatura}}" >{{$value->descripcion}}</option>
                      @endforeach
                    </select>
                  </div>
              </div>
              <!-- Proveedor -->
              <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                  <label for="id_proveedor" class="col-md-4 control-label">{{trans('winsumos.proveedores')}}</label>
                  <div class="col-md-8">
                    <select name="id_proveedor" style="width: 100%;" class="form-control select2_proveedor" required="" id="id_proveedor">
                        <option value="0">{{trans('winsumos.seleccione')}}</option>
                      @foreach($proveedores as $value)
                        <option value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
                      @endforeach
                    </select>
                  </div>
              </div>
              <!-- Observaciones -->
              <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                <label for="observaciones" class="col-md-2 control-label">{{trans('winsumos.observacion')}}</label>
                <div class="col-md-10">
                  <input id="observaciones" type="text" class="form-control" name="observaciones"
                    value="{{ old('observaciones') }}" autofocus>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">{{trans('winsumos.datos_producto')}}</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="inputid" class="col-sm-3 control-label">{{trans('winsumos.codigo')}}</label>
                  <div class="col-sm-9">
                    <input value="" type="text" class="form-control" name="codigo" id="codigo" placeholder="{{trans('winsumos.codigo')}}"
                      style="text-transform:uppercase;">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="inputapellido" class="col-sm-3 control-label">{{trans('winsumos.nombre')}}</label>
                  <div class="col-sm-9">
                    <input value="" type="text" class="form-control" id="nombre" name="nombre"
                      placeholder="{{trans('winsumos.nombre')}}" style="text-transform:uppercase;">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="inputapellido" class="col-sm-3 control-label">{{trans('winsumos.serie')}}</label>
                  <div class="col-sm-9">
                    <input value="" type="text" class="form-control" id="serie" name="serie"
                      placeholder="{{trans('winsumos.serie')}}" style="text-transform:uppercase;">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- /.box-body -->
          <div class="box-footer">
            <button type="button" id="busqueda" class="btn btn-primary">
              {{trans('winsumos.agregar')}}
            </button>
          </div>
        </div>
        <div class="panel panel-default">

          <div class="general form-group col-md-12 ">
            <div class="row">
              <div class="col-md-6">
                <span class="help-block">
                  <strong id="lote_errores"></strong>
                </span>
              </div>
              <div class="col-md-6"> 
                <span class="help-block">
                  <strong id="fecha_errores"></strong>
                </span>
              </div>

            </div>

          </div>
          <div class="" id="dv_listado"></div>

          <div class="box-body">
            <div class="table-responsive col-md-12">
              <input name='contador' type="hidden" value="0" id="contador">
              <table id="example2" class="display compact responsive" role="grid" aria-describedby="example2_info"
                style="margin-top:0 !important; width: 100%!important;">
                <thead>
                  <tr role="row">
                    <th>{{trans('winsumos.codigo')}}</th>
                    <th>{{trans('winsumos.nombre')}}</th>
                    <th>{{trans('winsumos.cantidad')}}</th>
                    <th>{{trans('winsumos.serie')}}</th>
                    <th>{{trans('winsumos.bodegas')}}</th> 
                    <th>{{trans('winsumos.lote')}}</th>
                    <th>{{trans('winsumos.registro_sanitario')}}</th>
                    <th>{{trans('winsumos.fecha_vencimiento')}}</th>
                    <th>{{trans('winsumos.descuento')}}</th>
                    <th>{{trans('winsumos.precio_unitario')}}</th>
                    <th>{{trans('winsumos.precio_final')}}</th>
                    <th>{{trans('winsumos.accion')}}</th>

                  </tr>
                </thead>
                <tbody id="crear">

                </tbody>
                <tfoot>
                  @php
                    $conf = \Sis_medico\Ct_Configuraciones::find(3);
                  @endphp
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="2" class="td_der">{{trans('winsumos.subtotal')}} {{$conf->iva*100}}%:</td>
                        <td><input type="hidden" name="subtotal_12" id="subtotal_12"> <input  class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="subtotal_12_1"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="2" class="td_der">{{trans('winsumos.subtotal')}} 0%:</td>
                        <td><input type="hidden" name="subtotal_0" id="subtotal_0"> <input class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="subtotal_0_1"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="2" class="td_der">{{trans('winsumos.iva')}}</td>
                        <td><input type="hidden" name="iva" id="iva"> <input class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="iva_1"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="2" class="td_der">{{trans('winsumos.total')}}</td>
                        <td><input type="hidden" name="total" id="total"> <input class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="total_1"></td>
                        <td></td>
                      </tr>
                    </tfoot>
              </table>
            </div>
          </div>
          <div class="box-footer" style="text-align: center;">
            <button type="button" id="envio" class="btn btn-primary"
              data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
              <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('winsumos.guardar')}}
            </button>
          </div>
        </div>
      </div>

  </form>
</section>
<!-- /.content -->


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>

<script>
  $('.select2_proveedor').select2({
      tags: false
  });
</script>

@include('inventario.ing_egr_vario.partial')
@endsection