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
  .table-striped>thead>tr>th>td, .table-striped>tbody>tr>th>td, .table-striped>tfoot>tr>th>td, .table-striped>thead>tr>td, .table-striped>tbody>tr>td, .table-striped>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }
  .right_text{
    text-align: right;
  }
  .span{
    color:black;
    font-weight: bold;
  }
  th{
    text-align: center;
  }
  </style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
  <!-- Main content -->
  <section class="content">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
        <li class="breadcrumb-item"><a href="#">Acreedores</a></li> 
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
        <form method="POST" id="reporte_master" action="{{ route('chequesa.index') }}" >
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

        <!--   <button type="button" class="btn btn-primary" onclick="excel();" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button>  -->
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">
          
        </div>
      </form> 
      </div>
      <!-- /.box-body -->
      <form method="POST" id="print_reporte_master" action="{{ route('chequesa.excel') }}" target="_blank">
          {{ csrf_field() }}
        <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
        <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
        <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}">
      </form>

      @if(count($cheques)>0)
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
                  <h4 style="text-align: center;">Informe de Cheque</h4>
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
                  <h4 style="text-align: center;">Informe de Cheques Girados</h4>
                  <h5 style="text-align: center;"> @if(($fecha_desde)!=null) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}} @else Al  {{date("d-m-Y", strtotime($fecha_hasta))}} @endif</h5>
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
            <table id="example2" class="display compact" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha Comprobante</th>
                  <th>Fecha Cheque</th>
                 
                  <th># Asiento</th>
                 
                  <th># Cheque</th>
                  <th>Nombre Proveedor</th>
                  <th>Banco</th>
                  <th>Valor</th>
		  <th>Accion</th>
                </tr>
              </thead>
                <tbody>
                  @php
                  $acumulador=0; 
                  @endphp
                    @foreach($cheques as $value)
                      @php 
                        $pro= Sis_medico\Proveedor::find($value->id_proveedor);
                        $banco= Sis_medico\Ct_Caja_Banco::find($value->banco);

                      @endphp
                      @if(!is_null($banco))
                        @if($banco->clase == '1')
                          @if($value->estado_egreso == 1)
                            @php $acumulador+=$value->valor; @endphp
                          @endif
                       <tr style="text-align: center;">
                         <td>{{date('d/m/Y',strtotime($value->fecha_comprobante))}}</td>
                         <td>{{date('d/m/Y',strtotime($value->fecha_cheque))}}</td>
                        
                         <td>{{$value->id_asiento_cabecera}}</td>
                         
                         <td>{{$value->cheque}}</td>
                         <!-- <td>{{$value->id_proveedor}}</td> -->
                         <td>@if(!is_null($pro)) {{$pro->razonsocial}} @else {{$value->id_proveedor}} @endif</td>
                         <td>@if(!is_null($banco)) {{$banco->nombre}} @endif</td>
                         <td style="text-align: right;">{{$value->valor}}</td>
                              <td>@if($value->estado_egreso == 1) <label style='font-size:12px' class='label label-success'>Activo</label> @else <label style='font-size:12px' class='label label-danger'>Anulado</label> @endif </td>
                       </tr>
                        @endif
                      @endif
                    @endforeach
                      &nbsp;
                      &nbsp;
                        <tr>
                  
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td></td>
                          <td><span class="span">TOTAL DE CHEQUES GIRADOS </span></td>
                          <td>&nbsp;</td>
                         
                          <td>&nbsp;</td>
                          <td style="text-align:right; font-weight: bold;">{{number_format($acumulador,2,'.',',')}}</td>
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
  </div>
  <!-- /.content -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
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

      $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
      }); 

      $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
      });
      $('#example2').DataTable({
         'paging': false,
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
            title: 'INFORME DE CHEQUES'
          },
          {
            extend: 'csvHtml5',
            footer: true
          },
          {
            extend: 'pdfHtml5',
            orientation: 'landscape',
            pageSize: 'TABLOID',
            footer: true,
            title: 'INFORME DE CHEQUES',
            customize: function(doc) {
              doc.styles.title = {
                color: 'black',
                fontSize: '16',
                alignment: 'center'
              }
            }
          }
        ],
      })
    });
    function excel(){
    $("#print_reporte_master" ).submit();
  }

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
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
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
