@extends('contable.clientes_retenciones.informes.cartera_vencida.base')
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

  .c_r {
    color: red;
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
      <li class="breadcrumb-item"><a href="#">Clientes</a></li>
      <li class="breadcrumb-item"><a href="#">Carteras por pagar</a></li>
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
      <form method="POST" id="reporte_master" action="{{ route('clientes.deudas_vencidas.index') }}">
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

        <div class="form-group col-md-6 col-xs-12" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
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
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_cliente">Cliente: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select name="id_cliente" id="id_cliente" class="form-control select2" style="width: 100%;">
            <option value="">Seleccione ...</option>
          </select>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="tipo">Tipo: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select class="form-control" onchange="tipo_factura();" name="tipo" id="tipo">
            <option value="0">Seleccione...</option>
            <option value="1">VEN-FA</option>
          </select>

        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="concepto">Concepto: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <input class="form-control" type="text" id="concepto" onchange="observacion()" name="concepto" placeholder="Ingrese concepto..." />
        </div>
        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
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
      <input type="hidden" name="id_cliente2" id="id_cliente2" value="{{$id_cliente}}">
      <input type="hidden" name="tipo2" id="tipo2" value="{{$tipo}}">
      <input type="hidden" name="observacion2" id="observacion2" value="{{$observacion}}">
    </form>

    @if(count($informe)!='[]')
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>

            <div class="box-body table-responsive">
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
                <h4 style="text-align: center;">CUENTAS POR COBRAR</h4>
                @if(($fecha_desde!=null))
                <h5 style="text-align: center;">Desde {{date("d/m/Y", strtotime($fecha_desde))}} - Hasta
                  {{date("d/m/Y", strtotime($fecha_hasta))}}
                </h5>
                @else
                <h5 style="text-align: center;">Al {{date("d/m/Y", strtotime($fecha_hasta))}}</h5>
                @endif
              </div>
              <table id="example2" class="display compact " style="font-size: 12px; width: 100%;">
                <thead>
                  <tr>
                    <th tabindex="0" aria-controls="example2" rowspan="1">Fecha</th>
                    <th tabindex="0" aria-controls="example2" rowspan="1">Días de Credito</th>
                    <th tabindex="0" aria-controls="example2" rowspan="1">Vencimiento</th>
                    <th tabindex="0" aria-controls="example2" rowspan="1">Días Vencidos</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">Tipo</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">Número</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">Detalle</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">Valor</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">IN</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">RT</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">DEBITO</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">CRUCE</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">N/C</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">Saldo</th>
                    <th tabindex="0" aria-controls="example2" colspan="1">Total en vencimiento</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                  $total1=0;
                  $total2=0;
                  $acumulador=0;
                  $acumulador2=0;
                  $tota_saldo_pendiente = 0;
                  $saldo_parcial = 0;
                  $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
                  $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                  @endphp
                  @foreach($informe as $val)
                  @php
                  //dd($val);
                  @endphp
                  @if(($val->facturas!="[]"))
                  <tr>
                    <td>
                      <div><span> <b>{{$val->identificacion}} | {{$val->nombre}} </b> </span> </div>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  @foreach($val->facturas as $value)
                  @php
                  $dias = 0;
                  if($value->valor_contable != 0){
                  $date1 = new DateTime(date("Y-m-d H:i:s", strtotime($value->fecha."+ $value->dias_plazo days")));
                  $date2 = new DateTime("now");
                  $diff = $date2->diff($date1);
                  if($diff->invert == 1 ){
                  $dias = "<label class='label label-danger'>- {$diff->days} Dias </label>";
                  }else{
                  $dias = "<label class='label label-info'> {$diff->days} Dias </label>";
                  }
                  }else{
                  $dias = "<label class='label label-success'> 0 Dia </label>";
                  }
                  @endphp

                  @if($value->estado!=0)
                  @if(!is_null($value))
                  @php $acumulador+=$value->total_final; $acumulador2+=$value->valor_contable; $total1+=$value->total_final; $total2+=$value->valor_contable; @endphp
                  @if(!is_null($value))
                  <tr>
                    <td>@if(($value)!=null) {{$meses[date('n',strtotime($value->fecha)) -1 ]}}, {{$dias[date('w',strtotime($value->fecha))]}} {{date("d/m/Y", strtotime($value->fecha))}} @endif </td>
                    @php
                    //dd($value);
                    //changes for Paola date : 17 de Nov
                    $days="0 dias";
                    $days2="0 dias";
                    $daysf=0;
                    if(!is_null($value->fecha_termino)){
                    $fec=new DateTime($value->f_autorizacion);
                    $fec2=new DateTime($value->fecha_termino);
                    $diff = $fec->diff($fec2);
                    $days= $diff->days. ' Dias ';
                    $fech=new DateTime($value->fecha_termino);
                    $fech2=new DateTime($fecha_hasta);
                    $diff2 = $fech2->diff($fech);
                    $days2= $diff2->days. ' dias ';
                    $daysf=$diff2->format("%r%a");
                    }

                    //dd($value->id_asiento_cabecera);
                    $modulo= json_encode(Sis_medico\Contable::getmodulev($value->id),TRUE);
                    $modulo= json_decode($modulo,TRUE);

                    $valor_egreso = "0.00";
                    $valor_retenciones = "0.00";
                    $valor_debitob_a = "0.00";
                    $valor_debito_a = "0.00";
                    $valor_cruce_valores = "0.00";
                    $valor_credito_a ="0.00";
                    $id_usuario = Auth::user()->id;

                    if(!is_null($value->id_asiento)){
                    if(isset($modulo['venta'])){
                    if(isset($modulo['module'])){

                    if(isset($modulo['module']['INGRESO'])){
                    if($modulo['module']['INGRESO'] == "INGRESO"){
                    $valor_egreso = $modulo['total']['INGRESO'];
                    }
                    }
                    if(isset($modulo['module']['RETENCIONES'])){
                    $valor_retenciones = $modulo['total']['RETENCIONES'];
                    }
                    if(isset($modulo['module']["DEBITO"])){
                    $valor_debitob_a = $modulo['total']['DEBITO'];
                    }
                    if(isset($modulo['module']["CRUCE"])){
                    $valor_cruce_valores = $modulo['total']['CRUCE'];
                    }
                    if(isset($modulo['module']["CREDITO"])){
                    $valor_credito_a = $modulo['total']['CREDITO'];
                    }
                    }
                    }


                    }
                    @endphp
                    <!--  <td> <label class="label @if($days>=0 && $days<=10) label-info @elseif($days>10 && $days<=20) label-warning @else label-danger @endif">{{$days}} </label> </b></td> -->
                    <td style="text-align:left;"> {{$value->dias_plazo}} dias</td>
                    <td style="text-align: center;"> &nbsp; @if(($value->fecha)!=null) {{date("d/m/Y",strtotime($value->fecha."+ $value->dias_plazo days"))}} @else &nbsp; @endif</td>
                    <td style="text-align:left;"> @php echo $dias; @endphp</td>
                    <td style="text-align:left;">{{$value->tipo}} </td>
                    <td>@if(($value)!=null) {{$value->nro_comprobante}} @endif</td>
                    <td class="text-left">@if(($value)!=null) Fact: # {{$value->id}} Ref: {{$value->nro_comprobante}} {{$value->observacion}} <br> <label style="font-size:11px;" class="label label-success">Id. Asiento: {{$value->id_asiento}} ID: {{$value->id}}</label>@endif</td>
                    <td style="text-align:center;">@if(($value)!=null) {{number_format($value->total_final,2,'.',',')}} @endif </td>

                    <td style="text-align:center;">{{number_format($valor_egreso,2)}}</td>
                    <td style="text-align:center;">{{number_format($valor_retenciones,2)}}</td>
                    <td style="text-align:center;">{{number_format($valor_debitob_a,2)}}</td>
                    <td style="text-align:center;">{{number_format($valor_cruce_valores,2)}}</td>
                    <td style="text-align:center;">{{number_format($valor_credito_a,2)}}</td>

                    <td style="text-align:center;">
                      @if(($value!=null)) {{number_format($value->valor_contable,2,'.',',')}} @endif
                    </td>
                    <td style="text-align:center;color:red;">
                      @php
                      $saldo_vencido = "";

                      if($value!=null){
                      $fecha_vencimiento = date("Y-m-d",strtotime($value->fecha."+ $value->dias_plazo days"));
                      $fecha_actual = date("Y-m-d");
                      if(strtotime($fecha_vencimiento) < strtotime($fecha_actual)){ $tota_saldo_pendiente +=$value->valor_contable;
                        $saldo_vencido = number_format($value->valor_contable,2,'.',',');
                        $saldo_parcial += $value->valor_contable;
                        }
                        else{
                        $saldo_parcial = 0;
                        }
                        }
                        @endphp
                        @if($saldo_parcial>0)
                        {{number_format($saldo_parcial,2,'.',',')}}
                        @endif
                    </td>
                  </tr>
                  @endif
                  @endif
                  @endif
                  @endforeach

                  <tr>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:center;"> </td>
                    <td><label>Total: </label></td>
                    <td style="text-align:center; font-weight: bold;"> {{number_format($acumulador,2,'.',',')}} </td>
                    <td style="text-align:center; font-weight: bold;"> {{number_format($acumulador2,2,'.',',')}} </td>
                    <td></td>
                  </tr>
                  @php $acumulador=0; $acumulador2=0; @endphp
                  @endif

                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:center;"><label>GRAN TOTAL:</label></td>
                    <td></td>
                    <td style="text-align:center; font-weight: bold;"> {{number_format($total1,2,'.',',')}} </td>
                    <td style="text-align:center; font-weight: bold;">{{number_format($total2,2,'.',',')}} </td>
                    <td></td>
                    <td style="text-align:center; font-weight: bold;"> {{number_format($tota_saldo_pendiente,2,'.',',')}} </td>
                  </tr>
                </tfoot>
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
      'paging': false,
      "scrollX": true,
      "scrollY": 450,
      dom: 'lBrtip',
      'lengthChange': false,
      'searching': true,
      'ordering': false,
      responsive: true,
      'info': false,
      'autoWidth': true,

      buttons: [{
          extend: 'copyHtml5',
          footer: true
        },
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'CUENTAS POR COBRAR {{$empresa->nombrecomercial}} @if($fecha_desde!=null) {{date("d/m/Y",strtotime($fecha_desde))}} @endif -  {{date("d/m/Y",strtotime($fecha_hasta))}}',
          customize: function(doc) {
            var sheet = doc.xl.worksheets['sheet1.xml'];
            //$('row c[r^="D"]', sheet).attr( 's', '64' );
            //console.log($('row c[r^="O"]',sheet))
            var row = 0;
            $('row', sheet).each(function() {
              // Get the value
              var text = $('is t', this).text();
              var number = $(this).text();
              //console.log(text);
              console.log(number);
              if (text.includes('|')) {
                $('row c', this).attr('s', '47');
                //codigos formatos
                /*
                  0:sin-formato
                  1:color-white
                  2:negrita
                  3:cursiva
                  4:subrayado
                  5:background-grey
                  6:background-grey-color-white
                  7:background-grey-bold
                  8:background-grey-italic
                  9:background-grey-underline
                  10:background-red
                  11:background-red-color-white
                  12:background-red-bold
                  13:background-red-italic
                  14:background-red-underline
                  15:background-green
                  16:background-green-color-white
                  17:background-green-bold
                  18:background-green-italic
                  19:background-green-underline
                  20:background-blue
                  21:background-blue-color-white
                  22:background-blue-bold
                  23:background-blue-italic
                  24:background-blue-underline
                  25:background-white-border
                  26:background-white-color-white-border
                  27:background-white-bold-border
                  28:background-white-italic-border
                  29:background-white-underline-border
                  30:background-grey-border
                  31:background-grey-color-white-border
                  32:background-grey-bold-border
                  33:background-grey-italic-border
                  34:background-grey-underline-border
                  35:background-red-border
                  36:background-red-color-white-border
                  37:background-red-bold-border
                  38:background-red-italic-border
                  39:background-red-underline-border
                  40:background-green-border
                  41:background-green-color-white-border
                  42:background-green-bold-border
                  43:background-green-italic-border
                  44:background-green-underline-border
                  45:background-blue-border
                  46:background-blue-color-white-border
                  47:background-blue-bold-border
                  48:background-blue-italic-border
                  49:background-blue-underline-border
                  50:align-left
                  51:align-center
                  52:align-right
                  53:bucle
                  54:orientation-90
                  55:without-orientation
                  56:% sin decimales
                  57:$ moneda dolar
                  58:E moneda ingles
                  59:E moneda euro
                  60:% decimal 1
                  61:formato personalizada
                  62:formato personalizada 2 decimales
                  63:formato numero
                  64:formato numero 2 decimales
                  65:formato numero
                  66:formato numero 2 decimales
                  67:fecha d/m/yyyy
                */
              } else {
                $('row c[r^="O"]', this).attr('s', '12');
              }
            });
          }
        },
        {
          extend: 'csvHtml5',
          footer: true
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          title: function() {
            return "ABCDE List";
          },
          pageSize: 'A3',
          footer: true,
          title: 'CUENTAS POR COBRAR {{$empresa->nombrecomercial}}',
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

    function esEntero(numero) {
      return Number.isInteger(numero);
    }

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
  $("#nombre_cliente").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{route('retenciones.autocompletar.cliente')}}",
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

  function cambiar_nombre_cliente() {
    $.ajax({
      type: 'post',
      url: "{{route('retenciones.autocompletar.cliente')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'nombre': $("#nombre_cliente").val()
      },
      success: function(data) {
        if (data.value != "no") {
          $('#id_cliente').val(data.value);
          $('#id_cliente2').val(data.value);
          $('#direccion_id_cliente').val(data.direccion);
        } else {
          $('#id_cliente').val("");
          $('#id_cliente2').val("");
          $('#direccion_cliente').val("");
        }

      },
      error: function(data) {
        console.log(data);
      }
    });
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

  function observacion() {
    var observacion = $("#concepto").val();

    $("#observacion2").val(observacion);
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