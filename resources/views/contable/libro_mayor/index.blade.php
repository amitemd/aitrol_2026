@extends('contable.libro_mayor.base')
@section('action-content')

<style>
  p.s1 {
    margin-left: 10px;
    font-size: 14px;
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

  .modal_asiento:hover {
    cursor: pointer;
  }

  .table-condensed>thead>tr>th>td,
  .table-condensed>tbody>tr>th>td,
  .table-condensed>tfoot>tr>th>td,
  .table-condensed>thead>tr>td,
  .table-condensed>tbody>tr>td,
  .table-condensed>tfoot>tr>td {
    padding: 1 px;
    line-height: 1;
  }
</style>


@php

$granTotalDebe = 0;
$granTotalHaber= 0;
$granTotalDebeHaber= 0;


@endphp

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="datos_factura">

    </div>
  </div>
</div>
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="../">Libro mayor</a></li>
    </ol>
  </nav>
  <div class="box" style=" background-color: white;">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR</label>
      </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('libro_mayor.index') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-4 control-label">Fecha desde:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha" id="fecha" value="@if(@$fecha) {{@$fecha}}@endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-4 control-label">Fecha hasta:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="@if(@$fecha_hasta) {{@$fecha_hasta}}@endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>


        @php
        if(count($cuentas) > 0){
        $id_plan_c = $cuentas[0]->plan;
        }else{
        $id_plan_c = '0';
        }
        @endphp

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-4 control-label">Cta. Desde:</label>
          <div class="col-md-9">
            <select id="cuenta" name="cuenta" class="form-control select2_cuentas" style="width: 100%;" required>
              <option> </option>
              @foreach($scuentas as $value)
              @if(isset($value->pempresa))
              @php
              $selected = "";
              if($filcuenta != ""){
              if($filcuenta == $value->id or $filcuenta == $value->pempresa->plan){
              $selected = "selected";
              }
              }
              @endphp
              <option {{$selected}} value="{{$value->id}}">{{$value->pempresa->plan}}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-4 control-label">Nom. Cta. Desde:</label>
          <div class="col-md-9">
            <select id="nombre" name="nombre" class="form-control select2_cuentas" style="width: 100%;" required>
              <option> </option>
              @foreach($scuentas as $value)
              @if(isset($value->pempresa))
              @php
              $selected = "";
              if($filcuenta != ""){
              if($filcuenta == $value->id or $filcuenta == $value->pempresa->plan){
              $selected = "selected";
              }
              }
              @endphp
              <option {{$selected}} value="{{$value->id}}">{{$value->pempresa->nombre}}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-4 control-label">Cta. hasta:</label>
          <div class="col-md-9">
            <select id="cuenta_hasta" name="cuenta_hasta" class="form-control select2_cuentas" style="width: 100%;">
              <option> </option>
              @foreach($scuentas as $value)
              @if(isset($value->pempresa))
              @php
              $selected = "";
              if($filcuentahasta != ""){
              if($filcuentahasta == $value->id or $filcuentahasta == $value->pempresa->plan){
              $selected = "selected";
              }
              }
              @endphp
              <option {{$selected}} value="{{$value->id}}">{{$value->pempresa->plan}}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-4 control-label">Nom.Cta. Hasta:</label>
          <div class="col-md-9">
            <select id="nombre_hasta" name="nombre_hasta" class="form-control select2_cuentas" style="width: 100%;">
              <option> </option>
              @foreach($scuentas as $value)
              @php
              $selected = "";
              if($filcuentahasta != ""){
              if($filcuentahasta == $value->id or $filcuentahasta == $value->pempresa->plan){
              $selected = "selected";
              }
              }
              @endphp
              <option {{$selected}} value="{{$value->id}}">{{$value->pempresa->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>


        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <!-- <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span>
            </button>
             <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" name="btn_imprimir">
              <span class="glyphicon glyphicon-print" aria-hidden="true" style="font-size: 16px">&nbsp;Imprimir&nbsp;</span>
            </button> -->
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
          <button type="button" onclick="validar()" class="btn btn-primary" id="btn_imprimir">
            <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
          </button>
          <button type="button" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>
        </div>

      </form>

    </div>
    <!-- /.box-body -->

    @if(count(@$cuentas)>0)
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>
            <!-- /.box-header -->

            <div class="box-body">
              <div class="col-md-1">
                <dl>
                  <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:85px;height:35px;" id="logo_empresa"></dd>
                  {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
                </dl>
              </div>
              <div class="col-md-3">
                <dl>
                  <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                  <dd>&nbsp; {{$empresa->id}}</dd>
                </dl>
              </div>
              <div class="col-md-4">
                <h4>Mayor de cuenta</h4>
                <h5>del {{$fecha}} al {{$fecha_hasta}} </h5>
              </div>
              <div class="col-md-4">
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
      
      @foreach($cuentas as $cuenta)
      @php

      $fi = str_replace ('/', '-', $fecha);
      $ff = str_replace ('/', '-', $fecha_hasta);
      $fi = date('Y-m-d', strtotime($fi));
      $ff = date('Y-m-d', strtotime($ff));
      // echo ($cuenta->id); echo "<br>";

      $queryConsul = \Sis_medico\Ct_Asientos_Detalle::join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
      ->join('plan_cuentas_empresa as pe', 'pe.id_plan', 'p.id')
      ->where('pe.id_empresa', $id_empresa)
      ->where('p.id', '=', $cuenta->id)
      ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
      ->where('c.id_empresa', $id_empresa);


      $registros = $queryConsul->whereBetween('c.fecha_asiento', [ $fi . ' 00:00:00', $ff . ' 23:59:59'])
      ->orderBy('fecha_asiento', 'ASC')
      ->get();
      $saldo = 0;



      $saldoanterior = \Sis_medico\Ct_Asientos_Detalle::join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
      ->join('plan_cuentas_empresa as pe', 'pe.id_plan', 'p.id')
      ->where('pe.id_empresa', $id_empresa)
      ->where('p.id', '=', $cuenta->id)
      ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
      ->where('c.id_empresa', $id_empresa)
      ->where('c.fecha_asiento', '<', $fi.' 00:00:00') ->where('c.fecha_asiento', '>', '1999-01-01 00:00:00')
        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'));



        $saldoanterior = $saldoanterior->first();


        //dd($saldoanterior);


        @endphp

        @php
        if(Auth::user()->id == "0922729587"){
        //dd($cuenta);
        }
        @endphp

        
        <div id="imprimir_libro" class="row">
          <div class="table-responsive col-md-12">
            <h4>Cuenta: {{$cuenta->nombre_plan}}</h4>
            <table id="example2" class="table table-condensed table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr class='well-dark'>
                  <th width="9%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                  <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">ID</th>
                  <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Documento</th>
                  <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Asiento</th>
                  <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Modulo</th>
                  <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">&nbsp;</th>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombre Cuenta</th>
                  <th width="13%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                  <th width="11%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" style="text-align: right">Debe</th>
                  <th width="11%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" style="text-align: right">Haber</th>
                  <th width="12%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" style="text-align: right">Saldo</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3"></td>
                  <td><b>{{$cuenta->plan}}</b></td>
                  <td><b>{{$cuenta->nombre_plan}}</b></td>
                  <td><b>&nbsp;</b></td>
                  <td><b>&nbsp;</b></td>
                  <td><b>&nbsp;</b></td>
                  <td><b>Saldo Anterior - {{$fecha}}</b></td>
                  <td colspan="3" @if($saldo < 0) style="color:red;" @endif>
                    <p style="text-align: right">$ {{ number_format($saldoanterior->saldo, 2, '.', '')}}</p>
                  </td>
                </tr>
                @if((count($registros)>0 ) or ($cuenta->plan == '2.01.07.05.01'))
                @php
                $totalDebe=0;
                $totalHaber =0;
                $saldo = $saldoanterior->saldo;
                @endphp
                @foreach($registros as $value)
                @php

                $granTotalDebe +=$value->debe;
                $granTotalHaber += $value->haber;
                $saldo = ($saldo + $value->debe) - $value->haber;
                $totalDebe = ($value->debe)+$totalDebe;
                $totalHaber = ($value->haber)+$totalHaber;
                $modulo= json_encode( Sis_medico\Contable::recovery_by_asiento($value->cabecera->id),TRUE);
                $modulo= json_decode($modulo,TRUE);
                $compra="";
                $venta="";
                $banco="";
                $numFacturaRenetncion='';
                if(isset($modulo['original']['compra']['module'])){
                $compra= $modulo['original']['compra']['module'];
                }
                if(isset($modulo['original']['venta']['module'])){
                $venta= $modulo['original']['venta']['module'];
                }
                if(isset($modulo['original']['bancos']['module'])){
                $banco= $modulo['original']['bancos']['module'];
                }
                if(isset($modulo['original']['venta']['nro_comprobante'])){
                $numFacturaRenetncion= $modulo['original']['venta']['nro_comprobante'];
                }

                @endphp

                @php
                $observacion = "";
                $id_documento = '';
                $factura = null;
                //if($value->fact_numero == ''){
                if(!is_null($value->cabecera)){
                $factura = Sis_medico\Ct_ventas::where('id_asiento',$value->cabecera->id)->first();
                if(!is_null($factura)){
                $observacion = 'Fact # '.$factura->nro_comprobante;
                $id_documento = $factura->id;
                }
                else{
                $observacion = $value->cabecera->observacion;
                }
                }
                else{
                $observacion = $value->cabecera->observacion;
                }
                /*}
                else{
                $observacion = $value->cabecera->observacion;
                }*/
                @endphp
                <tr>
                  <td>{{$value->fecha_asiento}}</td>
                  <!-- ID -->
                  <td>
                    @if($id_documento!='')
                    <a target="_blank" href="{{ route('ventas_editar',['id'=>$id_documento])}}" style="color:#E57059;">
                      {{$id_documento}}
                    </a>
                    @else
                    {{$id_documento}}
                    @endif
                  </td>
                  <!-- Asientos -->
                  <td>{{isset($factura->nro_comprobante)?$factura->nro_comprobante:($numFacturaRenetncion!=''?$numFacturaRenetncion:'')}}</td>
                  @if(isset($value->cabecera))
                  @if(!is_null($value->cabecera->id))
                  <td>
                    <a target="_blank" href="{{ route('librodiario.edit',['id'=>$value->cabecera->id])}}" style="color:#E57059;">
                      {{$value->cabecera->id}}
                    </a>
                  </td>
                  @endif
                  @endif
                  <!-- Asientos -->
                  <td>@if($compra!=null) <label class="label label-warning"> {{$compra}} </label>@endif @if($venta!=null) <label class="label label-info">{{$venta}} </label> @endif @if($banco!=null) <label class="label label-info">{{$banco}} </label> @endif</td>
                  <td>{{isset($compra->numero)?$compra->numero:''}}</td>
                  <td>{{$value->cuenta_empresa->plan}}</td>
                  <td>{{$value->descripcion}}</td>
                  <td>Detalle: {{$observacion}}</td>
                  <td>
                    <p style="text-align: right">$ {{$value->debe}}</p>
                  </td>
                  <td>
                    <p style="text-align: right">$ {{$value->haber}}</p>
                  </td>
                  <td @if($saldo < 0) style="color:red;" @endif>
                    <p style="text-align: right">$ {{ number_format($saldo, 4, '.', '')}}</p>
                  </td>
                </tr>
                @endforeach
                @if($cuenta->id == '2.01.07.01.11')
                @php
                $impuesto = \Sis_medico\EstadoResultado::impuesto_causado($fi, $ff, $id_empresa);
                @endphp
                @if($impuesto > 0)
                @php
                $saldo =$saldo - $impuesto;
                @endphp
                <tr>
                  <td colspan="6"></td>
                  <td>2.01.07.01.11</td>
                  <td>Impuesto a la Renta por pagar</td>
                  <td>Detalle: Impuesto Generado Estado de resultados</td>
                  <td>
                    <p style="text-align: right">$ 0.00</p>
                  </td>
                  <td>
                    <p style="text-align: right">$ {{$impuesto}}</p>
                  </td>
                  <td @if($saldo < 0) style="color:red;" @endif>
                    <p style="text-align: right">$ {{ number_format($saldo, 2, '.', '')}}</p>
                  </td>
                </tr>
                @endif
                @endif
                @if($cuenta->id == '2.01.07.05.01')
                @php
                $participacion = \Sis_medico\EstadoResultado::trabajadores($fi, $ff, $id_empresa);
                @endphp
                @if($participacion > 0)
                @php
                $saldo = $saldo - $participacion;
                @endphp
                <tr>
                  <td colspan="3"></td>
                  <td>2.01.07.05.01</td>
                  <td>Impuesto a la Renta por pagar</td>
                  <td>Detalle: Impuesto Generado Estado de resultados</td>
                  <td>
                    <p style="text-align: right">$ 0.00</p>
                  </td>
                  <td>
                    <p style="text-align: right">$ {{$participacion}}</p>
                  </td>
                  <td @if($saldo < 0) style="color:red;" @endif>
                    <p style="text-align: right">$ {{ number_format($saldo, 2, '.', '')}}</p>
                  </td>
                </tr>
                @endif
                @endif
                <tr>
                  <td colspan="3"></td>
                  <td><b>{{$value->plan}}</b></td>
                  <td><b>{{$cuenta->nombre_plan}}</b></td>
                  <td><b>&nbsp;</b></td>
                  <td><b>Saldo Final - {{$fecha_hasta}}</b></td>
                  <td style="text-align: right;"><b>$ {{ number_format($totalDebe, 2, '.', '')}}</td>
                  <td style="text-align: right;"><b>$ {{ number_format($totalHaber, 2, '.', '')}}</td>
                  <td colspan="3" @if($saldo < 0) style="color:red;" @endif>
                    <p style="text-align: right">$ {{ number_format($saldo, 2, '.', '')}}</p>
                  </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>

        {{-- <br> --}}
        @endforeach
    </div>
    @else
    <div class="col-md-12">
      <center style="font-size: 25px;font-weight: 500;">

        No se encontraron resultado

      </center>
    </div>
    @endif
    <input type="hidden" name="granTotalDebe" value={{$granTotalDebe}}>
    <input type="hidden" name="granTotalHaber" value={{$granTotalHaber}}>

    @php $granTotalDebeHaber = $granTotalDebe - $granTotalHaber; @endphp

    <input type="hidden" name="granTotalDebeHaber" value="{{$granTotalDebeHaber}}">

</section>
<form method="POST" id="print_reporte_master" action="{{ route('libro_mayor.index') }}" target="_blank">
  {{ csrf_field() }}
  <input type="hidden" name="filfecha" id="filfecha_desde" value="{{$fecha}}">
  <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
  <input type="hidden" name="filcuenta" id="filcuenta" value="">
  <input type="hidden" name="filcuenta_hasta" id="filcuenta_hasta" value="">
  <input type="hidden" name="exportar" id="exportar" value="0">
  <input type="hidden" name="imprimir" id="imprimir" value="">
</form>
<!-- /.content -->
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
  });

  $('#seguimiento').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });


  $('#cuenta').select2().trigger('change');


  $('#nombre').select2().trigger('change');


  $('#cuenta_hasta').select2().trigger('change');


  $('#nombre_hasta').select2().trigger('change');


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


  $('#cuenta_hasta').on('select2:select', function(e) {
    var cuenta_hasta = $('#cuenta_hasta').val();
    $('#nombre_hasta').val(cuenta_hasta);
    $('#nombre_hasta').select2().trigger('change');
  });


  $('#nombre_hasta').on('select2:select', function(e) {
    var nombre_hasta = $('#nombre_hasta').val();
    $('#cuenta_hasta').val(nombre_hasta);
    $('#cuenta_hasta').select2().trigger('change');
  });

  /*$("#btn_imprimir").click(function() {
    $("#print_reporte_master").submit();
    // document.getElementById("print_reporte_master").submit();
  });*/

  $(document).ready(function() {
    if ($("#example2_wrapper").length > 0) {
      document.getElementById("btn_imprimir").style.display = "inline-block";
    } else {
      document.getElementById("btn_imprimir").style.display = "none";
    }

  });

  $(function() {

    var selectedValues = new Array();
    selectedValues[0] = "1";
    selectedValues[1] = "1.01.01.1";

    $('#fecha').datetimepicker({
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha}}',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha").on("dp.change", function(e) {
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

  function switAlert(icon, title, text) {
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      text: `${text}`
    });
  }

  function verifica_fechas() {
    if (Date.parse($("#fecha").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }
  /*function imprimir(){ alert("imprimir");
    $( "print_reporte_master" ).submit();
  }*/
  $("#btn_exportar").click(function() {
    console.log('aa');
    $("#filfecha_desde").val($("#fecha").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filcuenta").val($("#cuenta").val());
    $("#filcuenta_hasta").val($("#cuenta_hasta").val()); //alert($("#cuenta_hasta").val());

    $("#exportar").val(1);
    $("#print_reporte_master").submit();
  });



  function validar() {
    let fecha = document.getElementById('fecha').value;
    let fecha_hasta = document.getElementById('fecha_hasta').value;
    let cuenta = document.getElementById('cuenta').value;
    if (fecha == "") {
      switAlert("error", "Opss......", "Debe seleccionar una fecha");
    } else if (fecha_hasta == "") {
      switAlert("error", "Opss......", "Debe seleccionar una fecha hasta");
    } else if (cuenta == "") {
      switAlert("error", "Opss......", "Debe seleccionar una cuenta");
    } else {
      imprimirElemento();
    }
  }

  function imprimirElemento() {
    var ventana = window.open('', 'PRINT');
    var elemento = document.getElementById('example2_wrapper').innerHTML;
    ventana.document.write('<html><head><title>' + document.title + '</title>');
    ventana.document.write(estilos()); //Aquí agregué la hoja de estilos
    ventana.document.write('</head><body >');
    ventana.document.write(elemento);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.focus();
    ventana.onload = function() {
      ventana.print();
      ventana.close();
    };
    return true;
  }

  function showmodal(id_orden) {
    $.ajax({
      type: 'get',
      url: "{{url('contable/getmodal/type')}}/" + id_orden,
      datatype: 'json',
      success: function(data) {
        $('#datos_factura').empty().html(data);
        $('#modal_datosfacturas').modal();
      },
      error: function(data) {
        //console.log(data);
      }
    });
  }

  function estilos() {
    let estilos_tablas = `
      <style>
      .well-dark {
        background-color: #aaa;
        width: 100%;
        color: #fafafa;
      }
      th{
        text-align: start;
      }
      .box-body{
        display: flex!important;
      }
      dl{
        margin-top: 39px;
        margin-right: 38px;
      }
      .col-md-4 h4, h5{
        margin: 0;
      }
      .col-md-4{
          margin-top: 35px;
      }

      </style>`;
    return estilos_tablas;
  }
</script>
@endsection