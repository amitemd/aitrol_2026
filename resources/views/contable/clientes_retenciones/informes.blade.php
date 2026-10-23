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
  .table-condensed>thead>tr>th>td, .table-condensed>tbody>tr>th>td, .table-condensed>tfoot>tr>th>td, .table-condensed>thead>tr>td, .table-condensed>tbody>tr>td, .table-condensed>tfoot>tr>td {
    padding: 0.5px;
    text-align: center;
  }
  .hidden-paginator {

    display: none;

    }
  .table{
    margin-bottom: -5px;
  }
  .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
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
        <li class="breadcrumb-item"><a href="../">Acreedores</a></li> 
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
        <form method="POST" id="reporte_master" action="{{ route('informe_retenciones.index') }}" >
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
              <input class="form-control" type="text" id="nombre_proveedor" name="nombre_proveedor" onchange="cambiar_nombre_proveedor()" placeholder="Ingrese nombre de proveedor..." />
              <input type="hidden" name="id_proveedor" id="id_proveedor" >
              
            </div>
            <div class="form-group col-md-2 col-xs-2">
              <label class="texto" for="nombre_proveedor">Secuencia: </label>
            </div>
            <div class="form-group col-md-4 col-xs-4 container-4">
              <input class="form-control" type="text" id="secuencia" name="secuencia"  placeholder="Ingrese Secuencia..." />
          
              
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
          <button type="button" class="btn btn-primary" onclick="excel();" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
          </button> 
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">
          
        </div>
      </form> 
      </div>
      <!-- /.box-body -->
      <form method="POST" id="print_reporte_master" action="{{ route('retenciones.excel') }}" target="_blank">
          {{ csrf_field() }}
        <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
        <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
        <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}">
      </form>

      @if(count($retenciones)>0)
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
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                    <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h4 style="text-align: center;">Informe de Retenciones</h4>
                  <h4 style="text-align: center;">Periodo {{date("d-m-Y", strtotime($fecha_desde))}} - {{date("d-m-Y", strtotime($fecha_hasta))}}</h4>
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
                  <h4 style="text-align: center;">Informe de Retenciones</h4>
                  <h5 style="text-align: center;">Periodo {{date("d-m-Y", strtotime($fecha_desde))}} - {{date("d-m-Y", strtotime($fecha_hasta))}}</h5>
                </div>
                <div class="col-md-4"> 
                </div>  
                <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                      <thead>
                        <tr class='well-dark'>
                          <th width="5%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1" >Fecha</th>
                          <th width="8.33%" style="text-align:center;"  tabindex="0" aria-controls="example2" rowspan="1" >Número</th>
                          <th width="8.33%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Preimpresa</th>
                          <th width="3.33%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Tipo</th>
                          <th width="8%" style="text-align:center;"  tabindex="0" aria-controls="example2" colspan="1">Acreedor</th>
                          <th width="6.33%"  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Ruc</th>
                          <th width="12%"  style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Detalle</th>
                          <th width="6.33%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Total RFIR.</th>
                          <th width="2%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1" >P. RFIR</th>
                          <th width="6.33%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Total RFIVA</th>
                          <th width="2%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">P. RFIVA</th>
                          <th width="1%" style="text-align:center;"  tabindex="0" aria-controls="example2" colspan="1">Estado</th>
                          <th width="8.33%" style="text-align:center;"  tabindex="0" aria-controls="example2" colspan="1">Creado por</th>
                          <th width="8.33%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Anulado por</th>
                        </tr>
                      </thead>
                  </table>
                <div class="infinite-scroll">
                  <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <thead>
                        <tr >
                          <th width="5%"  tabindex="0" aria-controls="example2" rowspan="1" ></th>
                          <th width="8.33%"  tabindex="0" aria-controls="example2" rowspan="1" ></th>
                          <th width="8.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="3.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="8%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="6.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="12%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="6.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="2%"  tabindex="0" aria-controls="example2" rowspan="1" ></th>
                          <th width="6.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="2%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="1%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="8.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="8.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                        </tr>
                      </thead>
                        <tbody>
                            @foreach($retenciones as $value)
                                <tr>
                                    <td>{{date("d-m-Y", strtotime($value->created_at))}}</td>
                                    <td>@if(($value->secuencia)!=null) {{$value->secuencia}} @endif</td>
                                    <td>@if(($value->nro_comprobante)!=null) {{$value->nro_comprobante}} @endif</td>
                                    <td>ACR-RT</td>
                                    <td>@if(($value->proveedor)!=null) {{$value->proveedor->nombrecomercial}} @endif</td>
                                    <td>@if(($value->proveedor)!=null) {{$value->proveedor->id}} @endif</td>
                                    <td>@if(($value->descripcion)!=null){{$value->descripcion}}  @endif</td>
                                    <td>@if(($value->valor_fuente)!=null) {{$value->valor_fuente}}  @endif </td>
                                    <td>@if(($value->detalle)!=null) @foreach($value->detalle as $val) @if(($val->porcentajer->tipo)==2) {{$val->porcentajer->valor}}%@endif @endforeach  @endif</td>
                                    <td>@if(($value->valor_iva)!=null) {{$value->valor_iva}}  @endif @if(($value->detalle)!=null)  @endif</td>
                                    <td>@if(($value->detalle)!=null) @foreach($value->detalle as $val) @if(($val->porcentajer->tipo)==1) {{$val->porcentajer->valor}}%@endif @endforeach  @endif</td>
                                    <td>@if(($value->estado)==1) ACTIVO @else ANULADA @endif</td>
                                    <td>@if(($value->usuario)!=null) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @endif</td>
                                    <td>@if(($value->estado)==0) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @else  @endif</td>
                                </tr>
                            @endforeach
                            <div id="paginationLinks" class="hidden-paginator">{{ $retenciones->appends(Request::all())->render() }}</div>
                        </tbody>
                  </table>
            
                </div>
                <table id="example2" class="table table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                      <thead>
                      <tr >
                          <th width="5%"  tabindex="0" aria-controls="example2" rowspan="1" ></th>
                          <th width="8.33%"  tabindex="0" aria-controls="example2" rowspan="1" ></th>
                          <th width="8.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="3.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="8%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="6.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="12%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="6.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="2%"  tabindex="0" aria-controls="example2" rowspan="1" ></th>
                          <th width="6.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="2%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="1%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="8.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                          <th width="8.33%"  tabindex="0" aria-controls="example2" colspan="1"></th>
                      </tr>
                      </thead>
                      <tbody>
                        <tr >
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ><label>Total</label></td>
                            <td >{{number_format($totales,2,'.','')}}</td>
                            <td  ></td>
                            <td > </td>
                            <td ></td>
                            <td ></td>

                          </tr>
                      
                      </tbody>
                </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div> 
      </div>
      @endif

    </div>
  </div>
  <!-- /.content -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
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
    function excel(){
    $("#print_reporte_master" ).submit();
    }
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
