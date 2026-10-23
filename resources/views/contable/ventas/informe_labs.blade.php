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
<!-- Main content -->
<section class="content">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="#">Ventas</a></li>
      <li class="breadcrumb-item"><a href="#">Informe de Venta Labs</a></l </ol>
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
      <form method="POST" id="reporte_master" action="{{ route('venta.infome_labs') }}">
        {{ csrf_field() }}

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
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_proveedor">Cliente: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select name="id_cliente" id="id_cliente" class="form-control select2_cuentas" style="width: 100%;">
            <option value="">Seleccione</option>
            @foreach($proveedores as $value)
            <option @if($id_proveedor==$value->identificacion) @endif value="{{$value->identificacion}}">{{$value->nombre}}</option>
            @endforeach
          </select>

        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_proveedor">Secuencia: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <input type="hidden" name="excelF" id="excelF" value="0">
          <input type="text" class="form-control" name="secuencia" id="secuencia" value="{{$secuencia}}" placeholder="Ingrese secuencia de factura ---">
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

          <button type="button" class="btn btn-primary" onclick="printDiv()">
            <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
          </button>
          <button type="button" class="btn btn-primary" onclick="excel();" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">

        </div>
      </form>
    </div>
    <!-- /.box-body -->
    <form method="POST" id="print_reporte_master_excel" action="{{ route('venta.infome_excel') }}" target="_blank">
      {{ csrf_field() }}
      <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
      <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
      <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}">
      <input type="hidden" name="secuencia2" id="secuencia2" value="{{$secuencia}}">
      <input type="hidden" name="tipo2" id="tipo2" value="{{$tipo}}">
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
              <h4 style="text-align: center;">REPORTE DE COMPROBANTES</h4>
              <h5 style="text-align: center;"> @if(($fecha_desde!=null)) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}} @else AL - {{date("d-m-Y", strtotime($fecha_hasta))}} @endif</h5>
            </div>
            <div class="col-md-4">
            </div>
            <table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align:center;width:100%">
              <thead>
                <tr class='well-dark'>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Fecha</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Cliente</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Cèdula</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Sub-Total</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Descuento</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Total</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Factura</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Efectivo</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">T.Credito</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">7% T/C</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Transf/Dep</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Cheque</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Pend Fc Seg</th>
                  <th  style="text-align:center;" tabindex="0" aria-controls="example2">Total Vta</th>
                </tr>
              </thead>
              <tbody>
                @php
                $suma_uno=0;
                $suma_dos =0;
                $suma_tres=0;
                $suma_cuatro = 0;
                $suma_cinco = 0;
                $suma_sei = 0;
                $suma_sette = 0;
                @endphp
                @foreach($informe as $value)
                @if(($value)!=null)
                @php
                $subtotalf=0;
                $subtotalf= $value->subtotal_12+$value->subtotal_0+$value->descuento;
                $detCompIngreso = Sis_medico\Ct_Detalle_Comprobante_Ingreso::where('id_factura',$value->id)->first();
                $pago=null;
                $porcTarjeta = null;
                $total_vta = null;
                if(!is_null($detCompIngreso)){
                $pago = Sis_medico\Ct_Detalle_Pago_Ingreso::where('id_comprobante',$detCompIngreso->id_comprobante)->first();
                $porcTarjeta = Sis_medico\Ct_Tipo_Pago::where('id',$pago->id_tipo)->first();
                $total_vta = $pago->total + $porcTarjeta->porcentaje;
                $suma_tres+=$porcTarjeta->porcentaje;
                $suma_uno += $pago->total;
                $suma_cuatro += $total_vta;
                if(empty($pago)){
                }elseif(($pago->id_tipo)==1){
                $suma_uno += $pago->total;
                }
                elseif(($pago->id_tipo)==4){
                $suma_dos += $pago->total;
                }
                elseif(($pago->id_tipo)== 5 && 3){
                $suma_cinco += $pago->total;
                }elseif(($pago->id_tipo)==2){
                $suma_sei += $pago->total;
                }
                elseif(($pago->id_tipo)==7){
                $suma_sette += $pago->total;
                }
                }
                @endphp
                <tr>
                  <td style="text-align:center;">@if(($value)!=null) {{$value->fecha}} @endif </td>
                  <td style="text-align:left;"> @if(($value->cliente!=null)) {{$value->cliente->nombre}} @endif </td>
                  <td style="text-align:center;">@if(($value->id_cliente)!=null) {{$value->id_cliente}} @endif</td>
                  <td style="text-align:center;">@if(($value)!=null) {{number_format($subtotalf,2,'.',',')}} @endif </td>
                  <td style="text-align:center;">@if(($value)!=null) {{$value->descuento}} @endif </td>
                  <td style="text-align:center;">@if(($value)!=null) @if($value->electronica==0) {{number_format($value->total_final,2,'.',',')}} @else @php $sg= $value->total_final @endphp {{number_format($sg,2,'.',',')}} @endif @endif </td>
                  <td style="text-align:center;">@if(($value)!=null) {{$value->nro_comprobante}} @endif </td>
                  <td style="text-align: center;">@if(empty($pago)) @elseif(($pago->id_tipo)==1) {{$pago->total}} @elseif(($pago->id_tipo ) == 5) - @elseif(($pago->id_tipo ) == 1) - @elseif(($pago->id_tipo ) == 4) - @elseif(($pago->id_tipo ) == 2 ) - @elseif(($pago->id_tipo ) == 6) - @elseif(($pago->id_tipo ) == 7) - @elseif(($pago->id_tipo ) == 3) -@endif</td>
                  <td style="text-align: center;">@if(empty($pago)) - @elseif(($pago->id_tipo ) == 5) - @elseif(($pago->id_tipo ) == 1) - @elseif(($pago->id_tipo ) == 4) {{$pago->total}} @elseif(($pago->id_tipo ) == 2 ) - @elseif(($pago->id_tipo ) == 6) - @elseif(($pago->id_tipo ) == 7) - @elseif(($pago->id_tipo ) == 3) -@endif</td>
                  <td style="text-align:center;">@if(($porcTarjeta)!=null) {{$porcTarjeta->porcentaje}} @endif </td>
                  <td style="text-align: center;">@if(empty($pago)) - @elseif(($pago->id_tipo ) == 5) {{$pago->total}} @elseif(($pago->id_tipo ) == 1) - @elseif(($pago->id_tipo ) == 4) - @elseif(($pago->id_tipo ) == 2 ) - @elseif(($pago->id_tipo ) == 6) - @elseif(($pago->id_tipo ) == 7) - @elseif(($pago->id_tipo ) == 3) {{$pago->total}} @endif</td>
                  <td style="text-align: center;">@if(empty($pago)) - @elseif(($pago->id_tipo ) == 5) - @elseif(($pago->id_tipo ) == 1) - @elseif(($pago->id_tipo ) == 4) - @elseif(($pago->id_tipo ) == 2 ) {{$pago->total}} @elseif(($pago->id_tipo ) == 6) - @elseif(($pago->id_tipo ) == 7) - @elseif(($pago->id_tipo ) == 3) - @elseif(($pago->id_tipo)==1) - @endif</td>
                  <td style="text-align: center;">@if(empty($pago)) - @elseif(($pago->id_tipo ) == 5) - @elseif(($pago->id_tipo ) == 1) - @elseif(($pago->id_tipo ) == 4) - @elseif(($pago->id_tipo ) == 2 ) - @elseif(($pago->id_tipo ) == 6) - @elseif(($pago->id_tipo ) == 7) {{$pago->total}} @elseif(($pago->id_tipo ) == 3) - @elseif(($pago->id_tipo)==1) - @endif</td>
                  <td style="text-align:center">{{$total_vta}}
                  </td>
                  <!-- Cambio--->
                </tr>
                @endif
                @endforeach
                <tr>
                <td></td>
                <td></td>
                <td style="font-weight:bold;text-align:center;">Total</td>
                <td>{{number_format($subtotal+$descuento,2,'.',',')}}</td>
                <td>{{number_format($descuento,2,'.',',')}}</td>
                <td>{{number_format($totales,2,'.',',')}}</td>
                <td></td>
                <td>{{$suma_uno}}</td>
                <td>{{$suma_dos}}</td>
                <td>{{$suma_tres}}</td>
                <td>{{$suma_cinco}}</td>
                <td>{{$suma_sei}}</td>
                <td>{{$suma_sette}}</td>
                <td>{{$suma_cuatro}}</td>   
                </tr>
              </tbody>
            </table>
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
            </div> --><!--
            <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align:center;">
              <thead>
                <tr>
                  <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1"></th>
                  <th width="7.25%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1"></th>
                  <th width="8.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="8.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="5.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="8%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                  <th width="6.25%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1"></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td style="font-weight:bold;text-align:center;">Total</td>
                  <td>
                    <p style="margin-left:64%;">{{number_format($subtotal+$descuento,2,'.',',')}}</p>
                  </td>
                  <td></td>
                  <td>
                    <p style="margin-left:-77%;">{{number_format($descuento,2,'.',',')}}</p>
                  </td>
                  <td>
                    <p style="margin-left:38%;">{{number_format($totales,2,'.',',')}}</p>
                  </td>
                  <td>

                  </td>
                  <td></td>
                  <td>
                    <p style="margin-left:-184%;">{{$suma_uno}}</p>
                  </td>
                  <td>
                    <p style="margin-left:-224%;">{{$suma_dos}}</p>
                  </td>
                  <td>
                    <p style="margin-left:-324%;">{{$suma_tres}}</p>
                    </td>
                  <td>
                  <p style="margin-left:55%;">
                  {{$suma_cuatro}}</p>
                  </td>
                </tr>
              </tbody>
            </table>-->
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
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
    $('#fact_contable_check').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      increaseArea: '20%' // optional
    });

  });
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
    $("#print_reporte_master_excel").submit();
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
</script>
@endsection