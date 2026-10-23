@extends('contable.estado_resultados.base')
@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->
<style>

p.s1 {
  margin-left:  10px;
  font-size:    14px;
  font-weight:  bold;
}
p.s2 {
  margin-left:  20px;
  font-size:    12px;
  font-weight:  bold;
}
p.s3 {
  margin-left:  30px;
  font-size:    10px;
  font-weight:  bold;
}
p.s4 {
  margin-left:  40px;
  font-size:    10px;
}
p.t1 {
  font-size:    14px;
  font-weight:  bold;
}
p.t2 {
  font-size:    12px;
  font-weight:  bold;
}
p.t3 {
  font-size:    10px;
}
.table-condensed>thead>tr>th>td, .table-condensed>tbody>tr>th>td, .table-condensed>tfoot>tr>th>td, .table-condensed>thead>tr>td, .table-condensed>tbody>tr>td, .table-condensed>tfoot>tr>td {
  padding: 0.5px;
  line-height: 1;
}
</style>
  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Financiero</a></li>

      <li class="breadcrumb-item"><a href="../">Estado de resultado consolidado</a></li>
    </ol>
  </nav>
  <div class="box">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">BUSCADOR</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('estadoresultados.resultado') }}" >
        {{ csrf_field() }}

          <div class="form-group col-md-6 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="{{$fecha_desde}}" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="{{$fecha_hasta}}"  autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="lbl_cuentas_detalle" class="texto col-md-5 control-label" >Mostrar cuentas de detalle</label>
              <input type="checkbox" id="cuentas_detalle" class="flat-green" name="cuentas_detalle" value=""  @if(@$cuentas_detalle=="1") checked @endif>
          </div>

          {{-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="mostrar_detalles" class="texto col-md-5 control-label" >Mostrar resumen</label>
              <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(@$mostrar_detalles=="1") checked @endif>
          </div> --}}

          <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
            <button type="submit" class="btn btn-primary" id="boton_buscar">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
            <button type="button" class="btn btn-primary" id="btn_imprimir" name="btn_imprimir">
                  <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
            </button>
            <button type="button" class="btn btn-primary" id="btn_exportar">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
            </button>
          </div>

        </form>
      </div>

      @if(count($ingresos)>0 || count($costos)>0 || count($gastos)>0)
      <div id="example2_wrapper" class="form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">
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
                  <h4 style="text-align: center;">Estado de resultado consolidado</h4>
                  <h4 style="text-align: center;">Periodo {{$fecha_desde}} - {{$fecha_hasta}}</h4>
                </div>
                <div class="col-md-4">
                  {{-- <dl>
                    <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
                    <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
                    <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
                  </dl> --}}
                </div>


              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="table-responsive col-md-12">
          <div class="content">

            <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr class='well-dark'>
                  <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                  <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                  @php $anios=0; @endphp

                  @foreach($fechagrupo as $fecha)
                    @php $anios++; $fechault=$fecha @endphp
                    <th width="10%" class="" style="text-align: right; " tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo {{$fecha}}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3"><strong>INGRESOS</strong></td>
                </tr>

                @php
                  $cont = 0;  $esp = ""; $index=0;
                  //dd($ingresos['2020']);
                @endphp
                @foreach($ingresos[$fechault] as $value)
                @php
                //dd($value['cuenta']);
                  $cont = 0;  $esp = "";
                  $cont = substr_count($value['cuenta'],".");
                  if($cont > 3){$cont = 3;}
                  if($cont<>0){   $esp = str_repeat("&nbsp;",($cont*2));  }
                  if($value['cuenta']=="4") {$toting = $value['saldo'];}
                @endphp
                  <tr>
                    <td>{{$value['cuenta']}}</td>
                    <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif">{{$esp . $value['nombre']}}</td>
                    @if ($anios==1)
                    <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                    @else
                      @foreach($fechagrupo as $fecha)
                      <td style="font-size: 10px;text-align: right; @if($ingresos[$fecha][$index]['saldo'] < 0) color:red; @endif" >{{number_format($ingresos[$fecha][$index]['saldo'],2)}}</td>
                      @endforeach
                    @endif
                  </tr>
                @php $index++; @endphp
                @endforeach

                <tr>
                  <td colspan="3">&nbsp;</td>
                </tr>
                {{-- COSTOS --}}
                <tr>
                  <td colspan="3"><strong>GASTOS</strong></td>
                </tr>
                @php
                //dd($costos);
                $cont = 0;  $esp = ""; $index=0;
                @endphp
                @foreach($costos[$fechault] as $value)
                @php
                  $cont = 0;  $esp = "";
                  $cont = substr_count($value['cuenta'],".");
                  if($cont > 3){$cont = 3;}
                  if($cont<>0){   $esp = str_repeat("&nbsp;",($cont*2));  }
                  //if($value['cuenta']=="5") {$totgas = $value['saldo'];}
                @endphp
                  <tr>
                    <td>{{$value['cuenta']}}</td>
                    <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif">{{$esp . $value['nombre']}}</td>
                    @foreach($fechagrupo as $fecha)
                    <td style="font-size: 10px;text-align: right; @if($costos[$fecha][$index]['saldo'] < 0) color:red; @endif" >{{number_format($costos[$fecha][$index]['saldo'],2)}}</td>
                    @endforeach
                  </tr>
                @php $index++; @endphp
                @endforeach

                <tr>
                  <td style="font-size: 10px;" colspan="2"><strong>Utilidad / Perdida del Periodo:</strong></td>
                  @foreach($fechagrupo as $fecha)
                  <td style="font-size: 10px;text-align: right; @if(($totpyg[$fecha][0]) < 0) color:red; @endif"  >{{number_format(($totpyg[$fecha][0]),2)}}</td>
                  @endforeach
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
  <form method="POST" id="print_reporte_master" action="{{ route('estadoresultados.resultado') }}" target="_blank">
    {{ csrf_field() }}
    <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
    <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
    <input type="hidden" name="filcuentas_detalle" id="filcuentas_detalle" value="{{@$cuentas_detalle}}">
    <input type="hidden" name="filmostrar_detalles" id="filmostrar_detalles" value="{{@$mostrar_detalles}}">
    <input type="hidden" name="exportar" id="exportar" value="0">
    <input type="hidden" name="imprimir" id="imprimir" value="">
  </form>

  <!-- /.content -->
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $('#seguimiento').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

    $(document).ready(function(){

      // $('#example2').DataTable({
      //   'paging'      : false,
      //   'lengthChange': false,
      //   'searching'   : false,
      //   'ordering'    : true,
      //   'info'        : false,
      //   'autoWidth'   : false
      // });

      tinymce.init({
        selector: '#hc'
      });

      $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
      });

      $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
      });

    });

    $(function () {
        $('#fecha_desde').datetimepicker({
            format: 'YYYY',
            //defaultDate: '{{$fecha_desde}}',
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM',
            //defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha_desde").on("dp.change", function (e) {
            //buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            //buscar();
        });
        $( "#btn_imprimir").click(function() {
          $("#filfecha_desde").val($("#fecha_desde").val());
          $("#filfecha_hasta").val($("#fecha_hasta").val());
          // $("#filcuentas_detalle").val($("#cuentas_detalle").val()); alert($("#cuentas_detalle").val());
          if($("#cuentas_detalle").prop("checked")){
            $("#filcuentas_detalle").val(1);
          }else{
            $("#filcuentas_detalle").val("");
          }
          // $("#filmostrar_detalles").val($("#mostrar_detalles").val());
          $("#exportar").val(0);
          $( "#print_reporte_master" ).submit();
        });
  });
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  function verifica_fechas(){
    var fecha_desde = Date($("#fecha_desde").val() + '01-01');
    var fecha_hasta = Date($("#fecha_hasta").val() + '-30');
    if(Date.parse(fecha_desde) > Date.parse(fecha_hasta)){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }
  $( "#btn_exportar").click(function() {
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        if($("#cuentas_detalle").prop("checked")){
          $("#filcuentas_detalle").val(1);
        }else{
          $("#filcuentas_detalle").val("");
        }
         //alert($("#cuentas_detalle").prop("checked"));
        $("#filmostrar_detalles").val($("#mostrar_detalles").val());
        $("#exportar").val(1);
        $("#print_reporte_master" ).submit();
    });
</script>
@endsection
