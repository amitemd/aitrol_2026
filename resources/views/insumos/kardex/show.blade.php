@extends('contable.balance_general.base')
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
    margin-left: 60px;
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

  .td_center {
    text-align: center;
  }

  .td_der{
    text-align: right;
  }

  .table-condensed>thead>tr>th>td,
  .table-condensed>tbody>tr>th>td,
  .table-condensed>tfoot>tr>th>td,
  .table-condensed>thead>tr>td,
  .table-condensed>tbody>tr>td,
  .table-condensed>tfoot>tr>td {

    padding: 0;
    font-size: 14px !important;
    line-height: 1;
  }
  .text_tam {
        font-size: 12px;
  } 

</style>

<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Insumos</a></li>
      <li class="breadcrumb-item"><a href="../">Kardex</a></li>
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
      <form method="POST" id="reporte_master" action="{{ route('insumos.kardex.show') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Producto:</label>
          <div class="col-md-9">
            <select id="id_producto" name="id_producto" class="form-control select2_cuentas" style="width: 100%;">
              <option> </option>
              @foreach($productos as $value)
              <option value="{{$value->nombre}}"> {{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
        </div>

        
        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Bodega:</label>
          <div class="col-md-9">
              <select id="id_bodega" name="id_bodega"  class="form-control select2_cuentas" style="width: 100%;">
                  <option> </option>
                  @foreach($bodegas as $value)
                      <option value="{{$value->id}}"> {{$value->nombre}} @if(isset($value->empresa))- {{$value->empresa->nombrecomercial}}@endif</option>
                  @endforeach
              </select>
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
            <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="@if(isset($fecha_desde)) {{date('d/m/Y',strtotime($fecha_desde))}} @else {{ date('d/m/Y') }} @endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="@if(isset($fecha_hasta)) {{date('d/m/Y',strtotime($fecha_hasta))}} @else {{ date('d/m/Y') }} @endif" required autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>


        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
          <button type="button" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>
        </div>
      </form>
    </div>
    <!-- /.box-body -->
    {{-- <b>Compras del período:</b> --}}
    <div class="content" id="contenedor">
      <div id="tbl_compras_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
          <div class="table table-responsive">
            <table id="tbl_compras" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="tbl_compras_info">
              <thead>
                <tr class="well-dark">
                  <th colspan="8">Detalles</th>
                  <th colspan="3" style="text-align:center">Entradas</th>
                  <th colspan="3" style="text-align:center">Salidas</th>
                  <th colspan="3" style="text-align:center">Saldos</th>
                  <th colspan="2" style="text-align:center">Observacion</th>
                </tr>
                <tr class="well-dark">
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                  <th width="30%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Referencia</th>
                  <th width="30%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Marca</th>
                  <th width="30%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Producto</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Serie</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Lote</th>
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">F.Vence</th> 
                  <th width="10%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Pedido</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Documento</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cantidad</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor unitario</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cantidad</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor unitario</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cantidad</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor unitario</th>
                  <th width="5%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total</th>
                  <th width="20%" tabindex="0" aria-controls="tbl_compras" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                </tr>
              </thead>
              <tbody>
                {{-- <tr class="well">
                  <td>&nbsp;</td>
                  <td style="font-weight: bold;" colspan="3"><h6>DETALLES INICIALES DE PRODUCTOS*</h6>
                      <ul>
                    </ul>
                  </td> 
                  <td>&nbsp;</td>
                  <td>&nbsp;</td> 
                </tr> --}}
                @php
                $getPrice=0;
                $getPriceant=0;
                $getCount=0;
                $getTotal=0;
                $cantidadant=0;
                $anterior= 0;
                if(is_null($anterior)){$anterior=0;}
                $cantidad=$anterior;
                $anteriorprecio= 0;
                if(is_null($anteriorprecio)){$anteriorprecio=0;}
                $anteriortotal=0;
                if(is_null($anteriortotal)){$anteriortotal=0;}
                //dd($anteriorprecio);
                $totalCosto=$anteriortotal;
                $precioCosto=$anteriorprecio;
                $contador=0;
                //dd($kardex);
                @endphp
                @foreach ($kardex as $value)
                  @php
                    $observ="";
                    if($value->documento_bodega->tipo_movimiento->tipo=='I' and $value->documento_bodega->tipo_movimiento->tipo=='R'){
                      $cantidad+=$value->cantidad;
                    }else{
                      $cantidad= $cantidad-$value->cantidad;
                    }
                    $getPrice+=$value->valor_unitario;
                    $getTotal+=$value->total;
                    $pedido = null; 
                  @endphp
                  

                <tr class="well">
                  <td class="text_tam">{{ date('d-m-Y', strtotime($value->fecha))}}</td>
                  <td class="text_tam">{{ $value->producto->codigo}}  </td>
                  <td class="text_tam">@if(isset($value->producto->marca)){{ $value->producto->marca->nombre}}@endif  </td>
                  <td class="text_tam">{{ $value->producto->nombre}}  </td>
                  <td class="text_tam"> 
                    @if (isset($value->documento_origen))   
                         - {{$value->documento_origen->serie}} 
                         @php 
                            $movimiento = \Sis_medico\Movimiento::where('serie',$value->documento_origen->serie)->orderBy('id', 'asc')->first(); 
                            if (isset($movimiento->pedido)) {
                              $pedido = $movimiento->pedido;  
                            }
                          @endphp
                    @endif
                  </td>
                  <td class="text_tam"> 
                    @if (isset($value->documento_origen))   
                          {{$value->documento_origen->lote}} 
                          
                    @endif
                  </td>
                  <td class="text_tam"> 
                    @if (isset($value->documento_origen))   
                          {{$value->documento_origen->fecha_vence}} 
                    @endif
                  </td>
                  <td class="text_tam"> @if(isset($pedido->id)) {{ $pedido->pedido}} @endif </td>
                  <td class="text_tam">{{ $value->descripcion}} {{ $value->bodega->nombre }} 
                    @if (isset($value->documento_origen->cabecera))  
                      @if (isset($value->documento_origen->cabecera->pedido))
                            || PED: {{$value->documento_origen->cabecera->pedido->pedido}}
                        @if ($value->documento_origen->cabecera->pedido->proveedor)
                            || PRO: {{$value->documento_origen->cabecera->pedido->proveedor->nombrecomercial}}
                        @endif
                      @endif
                    @endif
                   </td>
                  @if($value->documento_bodega->tipo_movimiento->tipo=='I' and $value->documento_bodega->tipo_movimiento->tipo=='R' )
                    @php
                    $observ= DB::table('ct_compras')->find($value->id_movimiento);
                    $totalCosto+=$value->total;
                    if($cantidad>0){
                      $precioCosto=$totalCosto/$cantidad;
                    }else{
                      $precioCosto=0;
                    }


                    @endphp
                    <td class="td_der text_tam">{{ $value->cantidad}}</td>
                    <td class="td_der text_tam">{{ number_format($precioCosto,2) }}</td>
                    <td class="td_der text_tam">{{ $value->total }}</td>

                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  @else
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    @php
                    $totalCosto= $precioCosto*$cantidad;
                    $tots=$precioCosto*$value->cantidad;
                    $observ= DB::table('ct_ventas')->find($value->id_movimiento);
                    
                    @endphp
                    <td class="td_der text_tam">{{ $value->cantidad}}</td>
                    <td class="td_der text_tam">{{ number_format($precioCosto,2) }}</td>
                    <td class="td_der text_tam">{{ number_format($tots,2) }}</td>
                  @endif
                  <td class="td_der text_tam" @if($cantidad<0) style="color:red;" @php $cantidad = 0; @endphp @endif> {{$cantidad}}</td>
                  <td class="td_der text_tam">{{ number_format($precioCosto,2)}}</td>
                  <td class="td_der text_tam" @if($totalCosto<0) style="color: red;" @php $totalCosto = $totalCosto * -1; @endphp @endif>{{ number_format($totalCosto,2)}}</td>
                  <td class="text_tam"> {{$value->referencia}} 
                    @if (isset($value->documento_origen->cabecera))
                      @php 
                        $hcp = null; $paciente = null; $recibo_cobro = null; $factura=null;
                        if ($value->documento_origen->cabecera->id_hc_procedimientos) {
                          $hcp = \Sis_medico\hc_procedimientos::find($value->documento_origen->cabecera->id_hc_procedimientos);
                          $hc = $hcp->historia;
                          $agenda = $hc->agenda;
                          $paciente = $agenda->paciente; 
                          if (isset($agenda->id)) {
                            $recibo_cobro = \Sis_medico\Ct_Orden_Venta::where('id_agenda',$agenda->id)->where('estado',1)->first();
                            if (isset($recibo_cobro->id)) {
                              $factura = \Sis_medico\Ct_ventas::where('orden_venta',$recibo_cobro->id)->first();
                            }
                          }
                          
                        } 
                      @endphp
                      @if(!is_null($hcp) and isset($hcp->hc_procedimiento_final->procedimiento)) || PRO: {{$hcp->hc_procedimiento_final->procedimiento->nombre}} @endif
                      @if(!is_null($paciente)) || PAC: {{$paciente->nombre1}} {{$paciente->apellido1}} {{$paciente->apellido2}} @endif
                      @if(!is_null($factura)) || FACT: {{$factura->nro_comprobante}} @endif
                      @if(!is_null($recibo_cobro)) || SEG: {{$recibo_cobro->seguro->nombre}} @endif
                    @endif
                  </td>
                  {{-- <td class="text_tam">@if($value->documento_bodega->tipo_movimiento->tipo=='I' and $value->documento_bodega->tipo_movimiento->tipo=='R') @if($observ!="") {{$observ->observacion}} @endif @else @if($observ!="") {{$observ->nombres_paciente}} procedimiento: {{$observ->procedimientos}} @endif @endif</td> --}}
                </tr>
                @php
                $observ="";
                //$cantidad=$value->cantidad;
                if($value->documento_bodega->tipo_movimiento->tipo=='I' and $value->documento_bodega->tipo_movimiento->tipo=='R' ){

                $cantidadant+=$value->cantidad;

                }else{

                $cantidadant= $cantidad-$value->cantidad;

                }
                $getTotalant=0;
                $getPriceant+=$value->valor_unitario;
                $getTotalant+=$value->total;
                @endphp
                @php
                $contador++;
                //dd($getTotal);
                @endphp
                @endforeach
                {{-- <tr class="well">
                  <td>&nbsp;</td>
                  <td colspan="8" style="font-weight: bold;">Cantidad actual en stock: {{ number_format($cantidad,2)}} unidades</td>
                </tr> --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {

        $('#tbl_compras').DataTable({
          'paging': false,
          'lengthChange': false,
          'searching': false,
          'ordering': false,
          'info': false,
          'autoWidth': false,
          "scrollY": 300,
          "scrollX": true,
          'scrollCollapse': true,
          responsive:'true',
          dom: 'Bfrtilp',
          buttons:[ 
              {
                extend:    'excelHtml5',
                text:      '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-success'
              },
              {
                extend:    'pdfHtml5',
                text:      '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-danger'
              },
              {
                extend:    'print',
                text:      '<i class="fa fa-print"></i> ',
                titleAttr: 'Imprimir',
                className: 'btn btn-info'
              },
            ]	
        });

        tinymce.init({
          selector: '#hc'
        });


      });
    </script>
  </div>
</section>

<form method="POST" id="print_reporte_master" action="{{ route('insumos.kardex.exportar') }}" target="_blank">
  {{ csrf_field() }}
  <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{@$fecha_desde}}">
  <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{@$fecha_hasta}}">
  <input type="hidden" name="filid_producto" id="filid_producto" value="{{@$id_producto}}">
  <input type="hidden" name="filid_bodega" id="filid_bodega" value="{{@$id_bodega}}">
  <input type="hidden" name="exportar" id="exportar" value="0">
</form>
<!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
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
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filcuentas_detalle").val($("#cuentas_detalle").val());
    $("#filmostrar_detalles").val($("#mostrar_detalles").val());
    $("#print_reporte_master").submit();
  });

  $("#btn_exportar").click(function() { 
    $("#filfecha_desde").val($("#fecha_desde").val());
    $("#filfecha_hasta").val($("#fecha_hasta").val());
    $("#filid_producto").val($("#id_producto").val());
    $("#filid_bodega").val($("#id_bodega").val());
    //alert($("#cuentas_detalle").prop("checked"));
    $("#exportar").val(1);
    $("#print_reporte_master").submit();
  });

  $(document).ready(function() {
    $('#id_producto').val('{{$id_producto}}');
    $('#id_producto').select2().trigger('change');
    $('#id_bodega').val({{$id_bodega}});
    $('#id_bodega').select2().trigger('change');

    tinymce.init({
      selector: '#hc'
    });

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
      // format: 'YYYY/MM/DD',
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha_desde}}',
    });
    $('#fecha_hasta').datetimepicker({
      // format: 'YYYY/MM/DD',
      format: 'DD/MM/YYYY',
      // defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

    //alert('{{$fecha_hasta}}');

  });

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  /*function imprimir(){ alert("imprimir");
    $( "print_reporte_master" ).submit();
  }*/
</script>
@endsection