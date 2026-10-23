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

  .text-left {
    text-align: left;
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
      <li class="breadcrumb-item"><a href="#">Ordenes Pendientes</a></li>
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
      <form method="POST" id="reporte_master" action="{{ route('venta.informe_ordenes_pendientes') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
              
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
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
             
            </div>
          </div>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="seguro">Seguro: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select name="id_seguro" id="id_seguro" class="form-control " style="width: 100%;">
            <option value="">Seleccione ...</option>
            @foreach($seguros as $value)
            <option  value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
          </select>

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
          <!--<button type="button" class="btn btn-primary" onclick="excel();" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>-->
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">

        </div>
      </form>
    </div>
    <!-- /.box-body -->
    <form method="POST" id="print_reporte_master" action="{{ route('clientes.deudas.pendientes.excel') }}" target="_blank">
      {{ csrf_field() }}
      <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
      <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
 
      
    </form>

    @if(count($informe)!='[]')
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
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image"
            style="width:80px;height:80px;" id="logo_empresa"></dd>
            <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">DEUDAS PENDIENTES</h4>
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
          <div class="col-md-3">
            <dl>
              <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
              <dd>&nbsp; {{$empresa->id}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">INFORME ORDENES PENDIENTES</h4>
            @if(($fecha_desde!=null))
            <h5 style="text-align: center;">Desde {{date("d/m/Y", strtotime($fecha_desde))}} - Hasta
              {{date("d/m/Y", strtotime($fecha_hasta))}}
            </h5>
            @else
            <h5 style="text-align: center;">Al {{date("d/m/Y", strtotime($fecha_hasta))}}</h5>
            @endif
          </div>
          <table id="example2" class="display compact" style="font-size: 12px; width: 100%;">
              <thead>
                    <tr>
                      <th  tabindex="0" aria-controls="example2" rowspan="1">Fecha</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">Tipo</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">Paciente</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">Seguro</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">Procedimiento</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">SubTotal12</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">SubTotal0</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">Descuento</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">Impuesto</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">Total Copago</th>
                      <th  tabindex="0" aria-controls="example2" colspan="1">Estado</th>
                    </tr>
              </thead>
              <tbody>
                @foreach($informe as $value)
                  @if(($value)!=null)
                    <tr>
                      <td>@if(($value)!=null) {{date("d-m-Y", strtotime($value->fecha))}} @endif</td>
                      <td style="text-align:left;"> {{$value->tipo}} </td>
                      <td>@if(($value->nombres_paciente)!=null) {{$value->nombres_paciente}} @endif</td>
                      @if(($value->seguro_paciente!=null))
                        @php $seguro = Sis_medico\Seguro::find($value->seguro_paciente); @endphp
                        <td style="text-align:left;"> {{$seguro->nombre}}  </td>
                      @else
                        <td style="text-align:left;"> </td>
                      @endif
                      <td style="text-align:center;">@if(!is_null($value->procedimientos)){{$value->procedimientos}} @endif</td>
                      <td style="text-align:center;">@if(!is_null($value->subtotal_12)) {{number_format($value->subtotal_12,2,'.',',')}} @endif</td>
                      <td style="text-align:center;">@if(!is_null($value->subtotal_0)) {{number_format($value->subtotal_0,2,'.',',')}} @endif</td>
                      <td style="text-align:center;">@if(($value)!=null) {{$value->descuento}} @endif </td>
                      <td style="text-align:center;">@if(($value)!=null) {{$value->impuesto}} @endif </td>
                      <td style="text-align:center;">@if(($value)!=null) {{$value->copago}} @endif </td>
                      <td style="text-align:center;">@if(!is_null($value->fecha_procedimiento))@if($value->estado == "1") {{$value->estado_pago == "1" ? "Pagada" : "Por facturar"}} @else Anulada @endif @endif</td>
                    </tr>
                  @endif
                @endforeach
              </tbody>
            </table>
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
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#example2').DataTable({
        'paging': true,
        "scrollX": true,
        "scrollY": 450,
         dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': true,
        responsive: true,
        'info': false,
        'autoWidth': true,
        
        buttons: [
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'REPORTE ORDENES PENDIENTES {{$empresa->nombrecomercial}} @if($fecha_desde!=null) {{date("d/m/Y",strtotime($fecha_desde))}} @endif -  {{date("d/m/Y",strtotime($fecha_hasta))}}',
          customize: function(doc) {
            var sheet = doc.xl.worksheets['sheet1.xml'];
            //$('row c[r^="D"]', sheet).attr( 's', '64' );
            //console.log($('row c[r^="C"]',sheet))
            
            $('row', sheet).each( function () {
                    //console.log('entra aqui');
                    // Get the value
                    // console.log($('is t', this))
                    var text=  $('is t', this).text();
                    if (text.includes('|')) {
                        $('row c',this).attr('s','47');
                    }else{
                     
                    }
            });
          }
        },
        
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          title : function() {
            return "ABCDE List";
          },
          pageSize: 'A3',
          footer: true,
          title: 'REPORTE ORDENES PENDIENTES {{$empresa->nombrecomercial}}',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
    })
    $('.select2_cuentas').select2({
      tags: false
    });
    $('#fact_contable_check').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      increaseArea: '20%' // optional
    });
    $('.select2').select2({
      ajax: {
        url: '{{route("get_sources.cliente")}}',
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
        }
      }
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
    $("#print_reporte_master").submit();
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
      //verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      //verifica_fechas();
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
</script>
@endsection