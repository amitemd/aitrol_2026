@extends('contable.balance_comprobacion.base')
@section('action-content')
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
  .right_text{
    text-align: right;
  }
  .table-striped>thead>tr>th>td, .table-striped>tbody>tr>th>td, .table-striped>tfoot>tr>th>td, .table-striped>thead>tr>td, .table-striped>tbody>tr>td, .table-striped>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }
  .boldi{
    font-weight: bold;
  }
  </style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
  <!-- Main content -->
  <section class="content">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
        <li class="breadcrumb-item"><a href="#">Informe de Saldos</a></li> 
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
        <form method="POST" id="reporte_master" action="{{ route('saldos_acreedor.index') }}" >
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
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group col-md-2 col-xs-2">
              <label class="texto" for="nombre_proveedor">Proveedor: </label>
            </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
         <select class="form-control select2_cuentas" name="id_proveedor" id="id_proveedor" style="width: 100%;">
                  <option value="">Seleccione...</option>
              @foreach($proveedores as $value)
                  <option @if(!is_null($id_proveedor)) @if(($id_proveedor)==$value->id) selected='selected' @endif @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
              @endforeach
          </select>
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
          <button type="button" onclick="excel();" class="btn btn-primary" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button> 
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">
          
        </div>
      </form> 
      </div>
      <form method="POST" id="print_reporte_master" action="{{ route('saldos_informe.excel') }}" target="_blank">
          {{ csrf_field() }}
        <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
        <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
        <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}" >
      </form>
      <!-- /.box-body -->

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
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:85px;height:35px;" id="logo_empresa"></dd>
                    <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h4 style="text-align: center;">ACREEDORES-Informe de Saldos</h4>
                  <h4 style="text-align: center;">Periodo {{$fecha_desde}} - {{$fecha_hasta}}</h4>
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
                  <h4 style="text-align: center;">Informe de Saldos</h4>
                  <h5 style="text-align: center;"> @if($fecha_desde!=null) Desde {{date("d-m-Y", strtotime($fecha_desde))}} Hasta - {{date("d-m-Y", strtotime($fecha_hasta))}} @else Al  {{date("d-m-Y", strtotime($fecha_hasta))}} @endif</h5>
                </div>
                <div class="col-md-4"> 
                </div>  
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box   arreglar 21 de mayo hice edte cambio para que restara bien pero no resta bien porque las facturas están dañadas desde un comienzo -->
          </div>
        </div> 
        <div class="row">
          <div class="table-responsive col-md-12">
          <div class="content">
            <table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr class='well-dark'>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="2" >Código</th>
                  <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="2" >Cuenta</th>
                  <th width="20%" class="right_text" tabindex="0" aria-controls="example2" colspan="1">Deudas</th>
                  <th width="20%" class="right_text" tabindex="0" aria-controls="example2" colspan="1">Anticipos</th>
                  <th width="20%" class="right_text" tabindex="0" aria-controls="example2" colspan="1">Saldo</th>
                </tr>
              </thead>
                <tbody>
                    @php
                     $valor_base=0;
                     $anticipo=0;
                     $retenciones=0;
                     $totales=0;
                     $totalrete=0;
                     $totales2=0;
                     $totales3=0;

                     //dd($informe);
                    @endphp
                    @foreach($informe as $value)
                        
                        <tr>
                          @php  
                           $anticipo=0;
                           $retenciones=0; 
                           $totalrete=0;
                           $consulta= DB::table('ct_comprobante_egreso')->where('id_proveedor',$value->proveedor)->get(); 
                           if($consulta!=null){ 
                             foreach($consulta as $val){ 
                               $anticipo+= $val->valor_pago; 
                               $detalles= DB::table('ct_detalle_comprobante_egreso')->where('id_comprobante',$val->id)->get(); 
                               if($detalles!=null){  
                                 foreach($detalles as $x){ 
                                  $consulta_retenciones= DB::table('ct_retenciones')->where('id_compra',$x->id_compra)->first();   
                                    if($consulta_retenciones!=null){ 
                                      $retenciones+=$consulta_retenciones->valor_fuente+$consulta_retenciones->valor_iva; 
                                    } 
                                 }
                                }    
                             } 
                           } @endphp
                          @php 
                          $anticipof=$anticipo;  
                          $proveedor= DB::table('proveedor')->where('id',$value->proveedor)->first(); 
                          $totalrete=$value->total-$retenciones; 
                          $totales+=$totalrete; 
                          $totales2+=$anticipo;   
                          @endphp
                          <td>{{$value->proveedor}}</td>
                          <td>@if(($proveedor!=null)) {{$proveedor->nombrecomercial}} @endif</td>
                          <td class="right_text" >{{number_format($totalrete,2,'.',',')}}</td>
                          <td class="right_text">{{number_format($anticipof,2,'.',',')}}</td>
                          @php $valor_base= $totalrete-$anticipof; $totales3+=$valor_base;  @endphp
                          <td class="right_text" @if($valor_base<0) style="color:red;" @endif >{{number_format($valor_base,2,'.',',')}}</td>
                        </tr>

                    @endforeach
                    
                </tbody>
                <tfoot>
                        <tr>
                            <td></td>
                            <td><label>Total:</label></td>
                            <td class="right_text boldi">{{number_format($totales,2,'.',',')}}</td>
                            <td class="right_text boldi">{{number_format($totales2,2,'.',',')}}</td>
                            <td class="right_text boldi"  @if($totales3<0) style="color:red;" @endif>{{number_format($totales3,2,'.',',')}}</td>
                        </tr>
                </tfoot>
            </table>
          </div> 
            
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
    $("#nombre_proveedor").autocomplete({
    source: function( request, response ) {
        $.ajax( {
        url: "{{route('compra_buscar_nombreproveedor')}}",
        dataType: "json",
        data: {
            term: request.term
        },
        success: function( data ) {
            response(data);
        }
        } );
    },
    minLength: 2,
    } );
    function cambiar_nombre_proveedor(){
        $.ajax({
            type: 'post',
            url:"{{route('compra_buscar_proveedornombre')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre_proveedor").val()},
            success: function(data){
                if(data.value != "no"){
                    $('#id_proveedor').val(data.value);
                    $('#id_proveedor2').val(data.value);
                    $('#direccion_id_proveedor').val(data.direccion);
                }else{
                    $('#id_proveedor').val("");
                    $('#id_proveedor2').val("");
                    $('#direccion_proveedor').val("");
                }

            },
            error: function(data){
                console.log(data);
            }
        });
    }
    $(document).ready(function(){

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
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_desde}}',
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha_desde").on("dp.change", function (e) {
            verifica_fechas();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            verifica_fechas();
        });
 
  });
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }  
  function excel(){
    $("#print_reporte_master" ).submit();
  }
  function verifica_fechas(){
    if(Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
      });
    } 
  }

</script>
@endsection
