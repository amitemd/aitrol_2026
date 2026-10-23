@extends('contable.balance_comprobacion.base')
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

  .table-condensed>thead>tr>th>td,
  .table-condensed>tbody>tr>th>td,
  .table-condensed>tfoot>tr>th>td,
  .table-condensed>thead>tr>td,
  .table-condensed>tbody>tr>td,
  .table-condensed>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<!-- Main content -->
<section class="content">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
      <li class="breadcrumb-item"><a href="../">Balance de comprobaci&oacute;n</a></li>
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
    <div class="box-body dobra desaparecer" >
      <form method="POST" id="reporte_master" action="{{ route('balance_comprobacion.show') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="@if(isset($fecha_desde)) {{@$fecha_desde}} @else {{ date('d/m/Y') }} @endif" autocomplete="off" required>
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
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="@if(isset($fecha_hasta)) {{@$fecha_hasta}} @else {{ date('d/m/Y') }} @endif" autocomplete="off" required>
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="lbl_cuentas_detalle" class="texto col-md-5 control-label">Mostrar cuentas de detalle</label>
          <input type="checkbox" id="cuentas_detalle" class="flat-green" name="cuentas_detalle" value="1" @if(@$cuentas_detalle=="1" ) checked @endif>
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
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
          <button type="button" class="btn btn-primary" id="btn_imprimir">
            <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
          </button>
          <button type="button" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">

        </div>
      </form>
    </div>
    <!-- /.box-body -->

    @if(count($balance)>0)
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
                  @if(!is_null($empresa->logo))
                  <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:85px;height:35px;" id="logo_empresa"></dd>
                  @endif
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
                <h4 style="text-align: center;">BALANCE DE COMPROBACI&Oacute;N</h4>
                <h5 style="text-align: center;">DEL {{$fecha_desde}} AL {{$fecha_hasta}}</h5>
              </div>
              <div class="col-md-4">
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
      <div class="row">
        <div class="table-responsive col-md-12">
          <div class="content">
            <table id="example2" class="table table-striped table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr class='well-dark'>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="2">Cuenta</th>
                  <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="2">Detalle</th>
                  <th width="20%" class="" tabindex="0" aria-controls="example2" colspan="2" style="text-align: center">Saldo Inicial</th>
                  <th width="20%" class="" tabindex="0" aria-controls="example2" colspan="2" style="text-align: center">Periodo</th>
                  <th width="20%" class="" tabindex="0" aria-controls="example2" colspan="2" style="text-align: center">Saldo Final</th>
                </tr>
                <tr class='well-dark'>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" style="text-align: center">Deudor</th>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" style="text-align: center">Acreedor</th>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" style="text-align: center">Deudor</th>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" style="text-align: center">Acreedor</th>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" style="text-align: center">Deudor</th>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" style="text-align: center">Acreedor</th>
                </tr>
              </thead>
              <tbody>
                @php
                $i=0;
                $acum_debe1 = 0; $acum_haber1 = 0;
                $total_debe1 = 0; $total_haber1 = 0;
                $total_debe2 = 0; $total_haber2 = 0;
                $total_debe3 = 0; $total_haber3 = 0;
                @endphp
                @foreach($balance as $value)
                @php
                $saldo_debe = 0; $saldo_haber = 0; $saldo = 0;
                if(trim($value['cuenta'])=='2.01.07.05.01' ){$value['haber'] += $participacion;}
                if(trim($value['cuenta'])=='2.01.07.05.01' ){$balance_ant[$i]['haber'] += $participacionant;}

                $acum_debe1 = $balance_ant[$i]['debe'] + $value['debe'];
                $acum_haber1 = $balance_ant[$i]['haber'] + $value['haber'];
                $total_debe1 += $balance_ant[$i]['debe'];
                $total_haber1 += $balance_ant[$i]['haber'];
                $total_debe2 += $value['debe'];
                $total_haber2 += $value['haber'];
                $saldo = $acum_debe1 - $acum_haber1;
                if($saldo > 0){
                $saldo_debe = $saldo;
                $saldo_haber = 0;
                }else{
                $saldo_debe = 0;
                $saldo_haber = (-1)*$saldo;
                }
                $total_debe3 += $saldo_debe;
                $total_haber3 += $saldo_haber;


                @endphp

                <tr>
                  <td>
                    <p>{{$value['cuenta']}}</p>
                  </td>
                  <td style="font-size: 10px;">
                    <p>{{$value['nombre']}} &nbsp;</p>
                  </td>

                  <td style="font-size: 10px;text-align: right; @if($value['debe'] < 0) color:red; @endif">
                    <p>{{number_format($balance_ant[$i]['debe'],2)}}</p>
                  </td>
                  <td style="font-size: 10px;text-align: right; @if($value['haber'] < 0) color:red; @endif">
                    <p>{{number_format($balance_ant[$i]['haber'],2)}}</p>
                  </td>

                  <td style="font-size: 10px;text-align: right; @if($value['debe'] < 0) color:red; @endif">
                    <p>{{number_format($value['debe'],2)}}</p>
                  </td>
                  <td style="font-size: 10px;text-align: right; @if($value['haber'] < 0) color:red; @endif">
                    <p>{{number_format($value['haber'],2)}}</p>
                  </td>

                  <td style="font-size: 10px;text-align: right; @if($value['haber'] < 0) color:red; @endif">
                    <p>{{number_format($saldo_debe,2)}}</p>
                  </td>
                  <td style="font-size: 10px;text-align: right; @if($value['haber'] < 0) color:red; @endif">
                    <p>{{number_format($saldo_haber,2)}}</p>
                  </td>
                </tr>


                @php $i++; @endphp
                @endforeach
                <tr>
                  <td>&nbsp;</td>
                  <td style="font-size: 10px;">
                    <p style="font-weight: bold;">TOTALES &nbsp;</p>
                  </td>
                  <td style="font-size: 10px;text-align: right; @if($acum_debe1 < 0) color:red; @endif">
                    <p style="font-weight: bold;">{{number_format($total_debe1 ,2)}}</p>
                  </td>
                  <td style="font-size: 10px;text-align: right; @if($acum_haber1 < 0) color:red; @endif">
                    <p style="font-weight: bold;"> {{number_format($total_haber1 ,2)}}</p>
                  </td>
                  <td style="font-size: 10px;text-align: right; @if($total_debe2 < 0) color:red; @endif">
                    <p style="font-weight: bold;">{{number_format($total_debe2, 2)}}</p>
                  </td>
                  <td style="font-size: 10px;text-align: right; @if($total_haber2 < 0) color:red; @endif">
                    <p style="font-weight: bold;">{{number_format($total_haber2, 2)}}</p>
                  </td>
                  <td style="font-size: 10px;text-align: right; @if($total_debe3 < 0) color:red; @endif">
                    <p style="font-weight: bold;">{{number_format($total_debe3,2)}}</p>
                  </td>
                  <td style="font-size: 10px;text-align: right; @if($total_haber3 < 0) color:red; @endif">
                    <p style="font-weight: bold;">{{number_format($total_haber3,2)}}</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
</section>
<form method="POST" id="print_reporte_master" action="{{ route('balance_comprobacion.show') }}" target="_blank">
  {{ csrf_field() }}
  <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
  <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
  <input type="hidden" name="filcuentas_detalle" id="filcuentas_detalle" value="{{@$cuentas_detalle}}">
  <input type="hidden" name="filmostrar_detalles" id="filmostrar_detalles" value="{{@$mostrar_detalles}}">
  <input type="hidden" name="exportar" id="exportar" value="">
  <input type="hidden" name="imprimir" id="imprimir" value="">
</form>
<!-- /.content -->
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
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

  $(function() {
    $('#fecha_desde').datetimepicker({
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha_desde}}',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha_hasta}}',

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

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }

  $("#btn_exportar").click(function() {
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    if ($("#cuentas_detalle").prop("checked")) {
      $("#filcuentas_detalle").val(1);
    } else {
      $("#filcuentas_detalle").val("");
    }

    // if($("#mostrar_detalles").prop("checked")){
    //   $("#filmostrar_detalles").val(1);
    // }else{
    //   $("#filmostrar_detalles").val("");
    // }
    // alert($("#cuentas_detalle").prop("checked")); return;
    // $("#filmostrar_detalles").val($("#mostrar_detalles").val());  
    $("#exportar").val(1);
    $("#print_reporte_master").submit();
  });
  /*function imprimir(){ alert("imprimir");
    $( "print_reporte_master" ).submit();
  }*/
</script>
@endsection