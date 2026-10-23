@extends('contable.balance_comprobacion.base')
@section('action-content')
<style>
  p.s1 {
    margin-left: 10px;
    font-size: 12px;
    font-weight: bold;
  }

  p.s2 {
    margin-left: 20px;
    font-size: 12px;
    font-weight: bold;
  }

  p.s3 {
    margin-left: 30px;
    font-size: 10px;
    font-weight: bold;
  }

  p.s4 {
    margin-left: 40px;
    font-size: 10px;
  }

  p.t1 {
    font-size: 14px;
    font-weight: bold;
  }

  p.t2 {
    font-size: 12px;
    font-weight: bold;
  }

  p.t3 {
    font-size: 10px;
  }

  .table-striped>thead>tr>th>td,
  .table-striped>tbody>tr>th>td,
  .table-striped>tfoot>tr>th>td,
  .table-striped>thead>tr>td,
  .table-striped>tbody>tr>td,
  .table-striped>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }

  .secundario {
    left: 10px;
  }

  .table {
    margin-bottom: -10px;
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

  .hidden-paginator {

    display: none;

  }

  .removethe {
    display: none;
  }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Main content -->
<section class="content">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="#">Ventas</a></li>
    </ol>
  </nav>

  <div class="box dobra" style=" background-color: white;">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->
    <div class="row head-title">
      <div class="col-md-12 header">
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body ">
      <form method="POST" id="reporte_master" action="{{ route('venta.informe_ventas') }}">
        {{ csrf_field() }}
        <div class="row">
          <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" required>
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="id_tipo" class="col-md-3 control-label">{{ trans('new_recibo.Tiposeguro') }}</label>
            <div class="input-group col-md-6">
              <select class="form-control input-sm" name="id_tipo" id="id_tipo" required onchange="cargar_seguro();">
                <option value="">{{trans('mcaja.seleccione')}}...</option>
                @foreach ($tipos_seguros as $ts)
                <option value="{{ $ts->id }}" @if($ts->id == $tipo_seguro_sel) selected @endif>{{ $ts->nombre }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="id_seguro" class="col-md-3 control-label">{{ trans('new_recibo.Seguro') }}</label>
            <div class="input-group col-md-6">
              <select class="form-control input-sm js-seguro" name="id_seguro" id="id_seguro" required onchange="cargar_nivel();">
                <option value="">{{trans('mcaja.seleccione')}}...</option>
                @foreach ($seguros as $seguro)

                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="nombre_proveedor" class="col-md-3 control-label">{{trans('contableM.cliente')}}: </label>
            <div class="input-group col-md-6">
              <select class="form-control select2_cliente" style="width: 100%;" name="id_cliente" id="id_cliente">
                <option value="">Seleccione...</option>

              </select>

            </div>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label class="col-md-3 control-label" for="nombre_proveedor">{{trans('contableM.secuencia')}}: </label>
            <div class="input-group col-md-6">
              <input type="hidden" name="excelF" id="excelF" value="0">
              <input type="text" class="form-control" name="secuencia" id="secuencia" value="{{$secuencia}}" placeholder="Ingrese secuencia de factura ---">
            </div>
          </div>
          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label class="col-md-3 control-label" for="estado">Estado: </label>
            <div class="input-group col-md-6">
              <select class="form-control" name="activo" id="activo">
                <option value="">Seleccione...</option>
                <option @if($activo=='1' ) selected @endif value="1">Activo</option>
                <option @if($activo=='0' ) selected @endif value="2">Inactivo</option>
              </select>
            </div>
          </div>
          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label class="col-md-3 control-label" for="nombre_proveedor">Sucursal: </label>
            <div class="input-group col-md-6">
              <select class="form-control" name="sucursal" id="sucursal">
                <option value="">Todas</option>
                @foreach($sucursales as $value_s)
                @php
                $dato = $value_s->sucursal->codigo_sucursal.'-'.$value_s->codigo_caja;
                @endphp
                <option @if($dato==$sucursal) selected @endif value="{{$dato}}">{{$dato}} ({{$value_s->nombre_caja}})</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <!-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="mostrar_detalles" class="texto col-md-5 control-label" >Mostrar resumen</label>
            <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(old('mostrar_detalles')=="1") checked @endif>
        </div> -->

        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <!-- <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span>
          </button>
          <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" name="btn_imprimir">
            <span class="glyphicon glyphicon-print" aria-hidden="true" style="font-size: 16px">&nbsp;Imprimir&nbsp;</span>
          </button> -->
          <button type="button" class="btn btn-primary" onclick="buscarf();" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>

          <!-- <button type="button" class="btn btn-primary" onclick="printDiv()">
            <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
          </button> -->
          <!--           <button type="button" class="btn btn-primary" onclick="excel();" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button> -->
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">

        </div>
      </form>
    </div>
    <!-- /.box-body -->
    <form method="POST" id="print_reporte_master" action="{{ route('venta.excel_ventas') }}" target="_blank">
      {{ csrf_field() }}
      <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
      <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
      <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}">
      <input type="hidden" name="secuencia2" id="secuencia2" value="{{$secuencia}}">
      <input type="hidden" name="tipo2" id="tipo2" value="{{$tipo}}">
      <input type="hidden" name="seguro_sel" id="seguro_sel" value="{{$seguro_sel}}">
      <input type="hidden" name="tipo_seguro_sel" id="tipo_seguro_sel" value="{{$tipo_seguro_sel}}">
    </form>

    @if(count($informe)>0)
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>
            <!-- /.box-header -->
            {{-- <div class="box-body">
                <div class="col-md-4">
                  <dl>
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
            <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">INFORME DE VENTAS</h4>
            <h4 style="text-align: center;">{{$fecha_desde}} - {{$fecha_hasta}}</h4>
          </div>
          <div class="col-md-4">
            <dl>
              <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
              <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
              <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
            </dl>
          </div>
        </div> --}}

        <div class="box-body">
          <div class="col-md-1">
            <dl>
              <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
              {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
            </dl>
          </div>
          <div id="imprimir">



            <div class="col-md-3">
              <dl>
                <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                <dd>&nbsp; {{$empresa->id}}</dd>
              </dl>
            </div>
            <div class="col-md-4">
              <h4 style="text-align: center;">INFORME DE VENTAS</h4>
              <h5 style="text-align: center;"> @if(($fecha_desde!=null)) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}} @else AL - {{date("d-m-Y", strtotime($fecha_hasta))}} @endif</h5>
            </div>
            <div class="col-md-4">
            </div>
            <div class="table table-responsive">
              <table id="example2" class="display compact" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align:center;">
                <thead>
                  <tr>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1">{{trans('contableM.fecha')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1">{{trans('contableM.numero')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1">{{trans('contableM.autorizacion')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">CI</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.cliente')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Seguro</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.tipo')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.detalle')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.SubTotal12')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.SubTotal0')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.subtotal')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.descuento')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.impuesto')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.total')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.estado')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.creadopor')}}</th>
                    <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">{{trans('contableM.anuladopor')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                  //$totales=0;
                  //$subtotal12=0;
                  //$subtotal0=0;
                  //$subtotal=0;.
                  //$descuento=0;
                  //$impuesto=0;
                  @endphp
                  @foreach($informe as $value)

                  @if(($value)!=null)
                  @php
                  $subtotalf=0;
                  $id_usuario = Auth::user()->id;
                  //$subtotalf= $value->subtotal_12+$value->subtotal_0+$value->descuento;
                  $subtotalf= $value->subtotal_12+$value->subtotal_0;
                  //$subtotal+= $value->subtotal_0+ $value->subtotal_12;
                  //$totales+= $value->total_final;
                  //$impuesto+=$value->impuesto;
                  //$descuento+=$value->descuento;


                  if($activo==1){
                  if($value->estado!=0){
                  //$totales+= $value->total_final;
                  //$subtotal0+=$value->subtotal_0;
                  //$subtotal+= $value->subtotal_0+$value->subtotal_12;
                  //$subtotal12+=$value->subtotal_12;
                  //$descuento+=$value->descuento;
                  //$impuesto+=$value->impuesto;
                  }
                  }else{

                  //$totales+= $value->total_final;
                  //$subtotal0+=$value->subtotal_0;
                  //$subtotal+= $value->subtotal_0+$value->subtotal_12;
                  //$subtotal12+=$value->subtotal_12;
                  //$descuento+=$value->descuento;
                  //$impuesto+=$value->impuesto;
                  }

                  @endphp
                  <tr>

                    <td>@if(($value)!=null) {{date("d-m-Y", strtotime($value->fecha))}} @endif</td>
                    <td>@if(($value)!=null) {{$value->nro_comprobante}} @endif</td>
                    <td>@if(($value)!=null) {{$value->nro_autorizacion}} @endif</td>
                    <td>@if(($value->id_cliente)!=null) {{$value->id_cliente}} @endif</td>
                    <td style="text-align:left;"> @if(($value->cliente!=null)) {{$value->cliente->nombre}} @endif </td>
                    <td style="text-align:left;">@if(isset($value->seguro)) {{$value->seguro->nombre}} @endif </td>
                    <td style="text-align:left;"> {{$value->tipo}} </td>
                    <td style="text-align:left;">@if(($value)!=null) # {{$value->concepto}} Asiento # {{$value->id_asiento}} @endif</td>
                    <td style="text-align:center;">{{number_format($value->subtotal_12,2,'.',',')}}</td>
                    <td style="text-align:center;">{{number_format($value->subtotal_0,2,'.',',')}}</td>
                    <td style="text-align:center;">@if(($value)!=null) {{number_format($subtotalf,2,'.',',')}} @endif </td>
                    <td style="text-align:center;">@if(($value)!=null) {{$value->descuento}} @endif </td>
                    <td style="text-align:center;">@if(($value)!=null) {{$value->impuesto}} @endif </td>
                    <td style="text-align:center;">@if(($value)!=null) @if($value->electronica==0) {{number_format($value->total_final,2,'.',',')}} @else @php $sg= $value->total_final; @endphp {{number_format($sg,2,'.',',')}} @endif @endif </td>
                    <td>@if(($value)!=null) @if(($value->estado)==0) ANULADA @else ACTIVA @endif @endif </td>
                    <td>@if(($value)!=null) @if(($value->usuario)!=null) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @endif @endif </td>
                    <td>@if(($value)!=null) @if(isset($value->usuariomod)) @if(($value->estado)==0) {{$value->usuariomod->nombre1}} {{$value->usuariomod->apellido1}} @endif @endif @endif </td>
                  </tr>
                  @endif


                  @endforeach

                  @php
                  //$subtotal = 0;
                  //$subtotal = $subtotal_0 + $subtotal_12;
                  //$totales = $subtotal + $descuento + $impuesto;
                  @endphp

                </tbody>
                <tfoot>
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td> </td>
                    <td>&nbsp;</td>
                    <td><label>TOTALES:</label></td>
                    @php
                    if(Auth::user()->id == "0957258056"){
                    //dd($totales);
                    }
                    @endphp
                    <td style="text-align:center; font-weight: bold; ">
                      <p>{{number_format($subtotal12,2,'.',',')}}</p>
                    </td>
                    <td style="text-align:center; font-weight: bold; ">
                      <p>{{number_format($subtotal0,2,'.',',')}}</p>
                    </td>
                    <td style="text-align:center; font-weight: bold; ">
                      <p>{{number_format($subtotal,2,'.',',')}}</p>
                    </td>
                    <td style="text-align:center; font-weight: bold; ">
                      <p>{{number_format($descuento,2,'.',',')}}</p>
                    </td>
                    <td style="text-align:center; font-weight: bold; ">
                      <p>{{number_format($impuesto,2,'.',',')}}</p>
                    </td>
                    <td style="text-align:center; font-weight: bold; ">
                      <p>{{number_format($totales,2,'.',',')}}</p>
                    </td>
                    <td> </td>
                    <td> </td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <!--
            <div class="infinite-scroll">
              @if($informe!='[]' || $informe!=null)
              <div id="paginationLinks" class="hidden-paginator">{{ $informe}}</div>
              <table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align: center;">
                <thead>
                  <tr>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" rowspan="1"></th>
                    <th width="7.25%" class="" tabindex="0" aria-controls="example2" rowspan="1"></th>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="8.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="5.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="6.25%" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                    <th width="6.25%" class="" tabindex="0" aria-controls="example2" colspan="1"></th>
                  </tr>
                </thead>

              </table>
              @endif
            </div> -->
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>


  </div>
  @endif

  </div>
</section>
</div>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    let seg = $('#seguro_sel').val();
    let tipo_seg = $('#tipo_seguro_sel').val();
    $('.select2_cuentas').select2({
      tags: false
    });
    $('#fact_contable_check').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      increaseArea: '20%' // optional,
    });
    $('.select2_cliente').select2({
      placeholder: "Escriba el nombre del cliente",
      allowClear: true,
      ajax: {
        url: '{{route("venta.clientesearch")}}',
        data: function(params) {
          var query = {
            search: params.term,
            type: 'public'
          }
          return query;
        },
        processResults: function(data) {
          // Transforms the top-level key of the response object from 'items' to 'results'
          console.log(data);
          return {
            results: data
          };
        },
        cache: true
      },
      minimumInputLength: 3,
    });
    cargar_seguro();
    cargar_nivel(); 
    
  });
  $('#example2').DataTable({
    'paging': false,
    dom: 'lBrtip',
    'lengthChange': false,
    'searching': true,
    'ordering': false,
    responsive: true,
    "scrollX": true,
    "scrollY": 450,
    'info': false,
    'autoWidth': true,
    buttons: [{
        extend: 'copyHtml5',
        footer: true
      },
      {
        extend: 'excelHtml5',
        footer: true,
        title: 'INFORME DE VENTAS'
      },
      {
        extend: 'csvHtml5',
        footer: true
      },
      {
        extend: 'pdfHtml5',
        orientation: 'landscape',
        pageSize: 'TABLOID',
        footer: true,
        title: 'INFORME DE VENTAS',
        customize: function(doc) {
          doc.styles.title = {
            color: 'black',
            fontSize: '16',
            alignment: 'center'
          }
        }
      }
    ],
  })
  $(function() {
    $('.infinite-scroll').jscroll({
      autoTrigger: true,
      loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
      padding: 0,
      nextSelector: '.pagination li.active + li a',
      contentSelector: 'div.infinite-scroll',
      callback: function() {
        $('div.paginationLinks').remove();

      }
    });
  });
  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });

  $('#cuenta').on('select2:select', function(e) {
    var cuenta = $('#cuenta').val();
    $('#nombre').val(cuenta);
    $('#nombre').select2().trigger('change');
  });


  $('#nombre').on('select2:select', function(e) {
    var nombre = $('#nombre').val();
    $('#cuenta').val(nombre);
    $('#cuenta').select2().trigger('change');
  });

  $("#btn_imprimir").click(function() {
    $("#print_reporte_master").submit();
    // document.getElementById("print_reporte_master").submit();
  });


  $(document).ready(function() {

    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

    $('input[type="checkbox"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass: 'iradio_flat-red'
    });

  });

  function excel() {
    $("#excelF").val(1);
    $("#reporte_master").submit();
  }

  function buscarf() {
    $("#excelF").val(0);
    $("#reporte_master").submit();
  }
  $("#nombre_proveedor").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{route('compra_buscar_nombreproveedor')}}",
        dataType: "json",
        data: {
          term: request.term
        },
        success: function(data) {
          response(data);
        }
      });
    },
    minLength: 2,
  });
  $('#fact_contable_check').on('ifChanged', function(event) {
    //aqui funciona si cambio el input time
    if ($(this).prop("checked")) {
      $("#esfac_contable").val(1);
      $("#es_fact_dos").val(1);
    } else {
      $("#esfac_contable").val(0);
    }

  });

  function cambiar_nombre_proveedor() {
    $.ajax({
      type: 'post',
      url: "{{route('compra_buscar_proveedornombre')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'nombre': $("#nombre_proveedor").val()
      },
      success: function(data) {
        if (data.value != "no") {
          $('#id_proveedor').val(data.value);
          $('#id_proveedor2').val(data.value);
          $('#direccion_id_proveedor').val(data.direccion);
        } else {
          $('#id_proveedor').val("");
          $('#id_proveedor2').val("");
          $('#direccion_proveedor').val("");
        }

      },
      error: function(data) {
        console.log(data);
      }
    });
  }

  function printDiv(nombreDiv) {
    var contenido = document.getElementById("imprimir").innerHTML;
    var contenidoOriginal = document.body.innerHTML;

    document.body.innerHTML = contenido;

    window.print();

    document.body.innerHTML = contenidoOriginal;
  }
  $(function() {
    $('#fecha_desde').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_desde}}',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

  });

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

  function tipo_factura() {
    var tipo = $("#tipo").val();
    if (isNaN(tipo)) {
      tipo = 0;
    }
    $("#tipo2").val(tipo);
  }

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }
      function cargar_nivel() {
        var id_emp = $('#id_empresa').val();
        var xseguro = $('#id_seguro').val();
        
        $.ajax({
          type: 'post',
          url: "{{ route('lista_nivel.seguro') }}",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {
            'id_seguro': xseguro,
            'id_empresa': id_emp
          },
          success: function(data) {
            //console.log("vt:");
            //console.log(data);
            if (data.value != 'no') {
              if (xseguro != 0 && xseguro!=null) {
                //Muestra el Nivel del Seguro  
                $("#id_nivel").empty();
                $.each(data, function(key, registro) {

                  $("#id_nivel").append('<option value=' + registro.id_nivel + ' >' + registro.nombre + '</option>');
                });

              } else {
                $("#id_nivel").empty();
              }
            } else {
              //Oculta el Nivel del Seguro
              document.getElementById("ident_nivel").style.visibility = "hidden";
            }
          },
          error: function(data) {}
        });
      }

      function cargar_seguro() {
        var id_tipo = $('#id_tipo').val();
        var seg = $('#seguro_sel').val();
        $.ajax({
          type: 'post',
          url: "{{ route('facturaagenda.tipifica_seguros') }}",
          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {
            'id_tipo': id_tipo,
            'id_seg': seg,
          },
          success: function(data) {
            if (data.value != 'no') {
              var contador = 0;
              //$("#id_seguro").empty();
              
              $.each(data, function(key, registro) {
                var select = '';
                if (registro.codigo_caja == '100')
                    select = 'selected';
                
                if (contador == 0) {
                  $("#id_seguro").append('<option value="' + registro.id + ' ' + select + '">' + registro.nombre + '</option>');
                  $('#id_seguro').val(registro.id);

                  contador++;
                } else {
                  $("#id_seguro").append('<option value="' + registro.id + ' ' + select + '">' + registro.nombre + '</option>');
                }
              });

              cargar_nivel();
            } else {
              $("#id_seguro").empty();
            }

          },
          error: function(data) {}
        });
      }
</script>
@endsection