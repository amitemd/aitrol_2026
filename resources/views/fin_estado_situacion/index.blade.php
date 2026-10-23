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
      <li class="breadcrumb-item"><a href="../">Estado de situaci&oacute;n</a></li>
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
        <form method="POST" id="reporte_master" action="{{ route('financiero.estadosituacion.show') }}" >
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
            <button type="button" class="btn btn-primary" id="boton_buscar">
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

      @if(count($activos)>0 or count($pasivos)>0 or count($patrimonio)>0)
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
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa">
                    {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
                    <dd>&nbsp; {{$empresa->id}}</dd>  --}}
                  </dl>
                </div>
                <div class="col-md-3">
                  <dl>
                    <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                    <dd>&nbsp; {{$empresa->id}}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h4 style="text-align: center;">Estado de situaci&oacute;n financiera</h4>

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

          <div class="content col-md-12">
              <table id="example2" class="table table-condensed table-hover dataTable col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
                <thead>
                  <tr class='well-dark'>
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                    <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                    @for ($i = $fecha_desde; $i <= $fecha_hasta; $i++)
                      <th width="10%" style="text-align: right;" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{$i}}</th>
                    @endfor
                      {{-- <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">%</th> --}}
                  </tr>
                </thead>
                <tbody>
                  {{-- ACTIVOS --}}
                    @php $valor100 = 0; $j = 0; @endphp
                    @foreach($activos[$fecha_desde] as $value)
                    @php
                      $cont = 0;  $sangria = ""; $t = "";
                      $cont = substr_count($value['cuenta'],".");
                      if($cont==0){   $sangria = "class=s1"; $t = "class=t1";  }
                      if($cont==1){   $sangria = "class=s2"; $t = "class=t2";  }
                      if($cont==2){   $sangria = "class=s3"; $t = "class=t3";  }
                      if($cont>=3){   $sangria = "class=s4"; $t = "class=t3";  }
                    @endphp
                      <tr>
                        <td><p {{ $sangria }} >{{$value['cuenta']}}</p></td>
                        <td style="@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{$value['nombre']}}</p></td>
                        @for ($i = $fecha_desde; $i <= $fecha_hasta; $i++)
                          <td style="text-align: right; @if($activos[$i][$j]['saldo'] < 0) color:red; @endif" ><p {{ $t }}>{{number_format($activos[$i][$j]['saldo'] ,2)}}</p></td>
                        @endfor
                      </tr>
                    @php $j++; @endphp
                    @endforeach
                </tbody>
              </table>
          </div>

          <div class="content col-md-12">
            <table id="example2" class="table table-condensed table-hover dataTable col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
              <thead>
                <tr class='well-dark'>
                  <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                  <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                  @for ($i = $fecha_desde; $i <= $fecha_hasta; $i++)
                    <th width="10%" style="text-align: right;" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{$i}}</th>
                  @endfor
                    {{-- <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">%</th> --}}
                </tr>
              </thead>
              <tbody>
                {{-- PASIVOS --}}
                  @php $valor100 = 0; $j = 0; @endphp
                  @foreach($pasivos[$fecha_desde] as $value)
                  @php
                    $cont = 0;  $sangria = ""; $t = "";
                    $cont = substr_count($value['cuenta'],".");
                    if($cont==0){   $sangria = "class=s1"; $t = "class=t1";  }
                    if($cont==1){   $sangria = "class=s2"; $t = "class=t2";  }
                    if($cont==2){   $sangria = "class=s3"; $t = "class=t3";  }
                    if($cont>=3){   $sangria = "class=s4"; $t = "class=t3";  }
                  @endphp
                    <tr>
                      <td><p {{ $sangria }} >{{$value['cuenta']}}</p></td>
                      <td style="@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{$value['nombre']}}</p></td>
                      @for ($i = $fecha_desde; $i <= $fecha_hasta; $i++)
                        <td style="text-align: right; @if($pasivos[$i][$j]['saldo'] < 0) color:red; @endif" ><p {{ $t }}>{{number_format($pasivos[$i][$j]['saldo'] ,2)}}</p></td>
                      @endfor
                    </tr>
                  @php $j++; @endphp
                  @endforeach
              </tbody>
            </table>
          </div>

          <div class="content col-md-12">
            <table id="example2" class="table table-condensed table-hover dataTable col-md-6" role="grid" aria-describedby="example2_info" style="font-size: 10px;">
              <thead>
                <tr class='well-dark'>
                  <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                  <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                  @for ($i = $fecha_desde; $i <= $fecha_hasta; $i++)
                    <th width="10%" style="text-align: right;" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{$i}}</th>
                  @endfor
                    {{-- <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">%</th> --}}
                </tr>
              </thead>
              <tbody>
                {{-- PATRIMONIO --}}
                  @php $valor100 = 0; $j = 0; @endphp
                  @foreach($patrimonio[$fecha_desde] as $value)
                  @php
                    $cont = 0;  $sangria = ""; $t = "";
                    $cont = substr_count($value['cuenta'],".");
                    if($cont==0){   $sangria = "class=s1"; $t = "class=t1";  }
                    if($cont==1){   $sangria = "class=s2"; $t = "class=t2";  }
                    if($cont==2){   $sangria = "class=s3"; $t = "class=t3";  }
                    if($cont>=3){   $sangria = "class=s4"; $t = "class=t3";  }

                  @endphp
                    <tr>
                      <td><p {{ $sangria }} >{{$value['cuenta']}}</p></td>
                      <td style="@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{$value['nombre']}}</p></td>
                      @for ($i = $fecha_desde; $i <= $fecha_hasta; $i++)
                      @php
                      if(trim($value['cuenta'])=='3'){$patrimonio[$i][$j]['saldo'] = $patrimonio[$i][$j]['saldo'] + $totpyg[$i];}
                      if(trim($value['cuenta'])=='3.07'){$patrimonio[$i][$j]['saldo'] = $totpyg[$i];}
                      if(trim($value['cuenta'])=='3.07.01' ){$patrimonio[$i][$j]['saldo'] = $totpyg[$i];}
                      if(trim($value['cuenta'])=='3.07.02' ){$patrimonio[$i][$j]['saldo'] = $totpyg[$i];}
                      @endphp
                        <td style="text-align: right; @if($patrimonio[$i][$j]['saldo'] < 0) color:red; @endif" ><p {{ $t }}>{{number_format($patrimonio[$i][$j]['saldo'] ,2)}}</p></td>
                      @endfor
                    </tr>
                  @php $j++; @endphp
                  @endforeach

              </tbody>
            </table>
          </div>


          </div>
        </div>
      </div>
      @endif

  </div>
  </section>
  <form method="POST" id="print_reporte_master" action="{{ route('estadoresultados.show') }}" target="_blank">
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
            format: 'YYYY',
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
    var fecha_desde = ($("#fecha_desde").val());
    var fecha_hasta = ($("#fecha_hasta").val());
    if(Date.parse(fecha_desde) > Date.parse(fecha_hasta)){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
      });
      return false;
    }else{
      return true;
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
    $( "#boton_buscar").click(function() {
      if(verifica_fechas()){
        $("#reporte_master" ).submit();
      }
    });
</script>
@endsection
