@extends('contable.flujo_efectivo_comparativo.base')
@section('action-content')

<style>
  #maincontainer {
    width:100%;
    height: 100%;
  }

  #leftcolumn {
    float:left;
    display:inline-block;
    width: 100px;
    height: 100%;  
  }

  #contentwrapper {
    float:left;
    display:inline-block;
    width: -moz-calc(100% - 100px);
    width: -webkit-calc(100% - 100px);
    width: calc(100% - 100px);
    height: 100%; 
  }
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

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->
  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Contabilidad</a></li> 
      <li class="breadcrumb-item"><a href="../">Flujo de efectivo comparativo</a></li> 
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
        <form method="POST" id="reporte_master" action="{{ route('flujoefectivocomparativo.show') }}" >
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Mes 1:</label>
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
          <label for="fecha_hasta" class="texto col-md-3 control-label">Mes 2:</label>
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

      @include('contable.flujo_efectivo_comparativo.show')

      

    </div>
  </section>

  <form method="POST" id="print_reporte_master" action="{{ route('flujoefectivocomparativo.show') }}" target="_blank">
          {{ csrf_field() }}
        <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
        <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
        <input type="hidden" name="exportar" id="exportar" value="">
        <input type="hidden" name="imprimir" id="imprimir" value="">
  </form>
  <!-- /.content -->
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">

    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
            });
        }); 

    $('#seguimiento').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });


    $('#cuenta').on('select2:select', function (e) {
        var cuenta = $('#cuenta').val();
        $('#nombre').val(cuenta);
        $('#nombre').select2().trigger('change');
      });


    $('#nombre').on('select2:select', function (e) {
        var nombre = $('#nombre').val();
        $('#cuenta').val(nombre);
        $('#cuenta').select2().trigger('change');
      });

    $( "#btn_imprimir" ).click(function() { 
      $( "#print_reporte_master" ).submit();
      // document.getElementById("print_reporte_master").submit(); 
    });

    $(document).ready(function(){


    });

    $(function () {
        $('#fecha_desde').datetimepicker({
            format: 'YYYY-MM',
            defaultDate: '{{ @$fecha_desde }}',
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY-MM',
            defaultDate: '{{ @$fecha_hasta }}',

            });
        $("#fecha_desde").on("dp.change", function (e) { 
            // verifica_fechas();
        });

        $("#fecha_hasta").on("dp.change", function (e) {
            // verifica_fechas();
        });
 
  });
    // function verifica_fechas(){
    //     if(Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())){
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Oops...',
    //             text: 'Verifique el rango de fechas y vuelva consultar'
    //         });
    //     }
    //     var x = $("#fecha_desde").val();
    //     let datex = new Date(x);
    //     var mesx = datex.getMonth();

    //     var y = $("#fecha_hasta").val();
    //     let datey = new Date(y);
    //     var mesy = datey.getMonth(); 

    //     if(mesx == mesy){
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Oops...',
    //             text: 'La fecha hasta debe ser mayor a la fecha desde'
    //         });
    //     }
    // }
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }  
  $( "#btn_exportar" ).click(function() { 
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        // if($("#mostrar_detalles").prop("checked")){
        //   $("#filmostrar_detalles").val(1);
        // }else{
        //   $("#filmostrar_detalles").val("");
        // }
        // alert($("#cuentas_detalle").prop("checked")); return;
        // $("#filmostrar_detalles").val($("#mostrar_detalles").val());  
        $("#exportar").val(1);  
        $("#print_reporte_master" ).submit();
    });
  /*function imprimir(){ alert("imprimir");
    $( "print_reporte_master" ).submit();
  }*/
</script>
@endsection
