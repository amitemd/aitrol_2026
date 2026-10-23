@extends('contable.balance_comprobacion.base')
@section('action-content')
<style>
  .select2-selection__choice{
    background-color: #024470  !important;
    border-color: #5DADE2  !important;
    color: white  !important;
  }

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

  .removethe {
    display: none;
  }

  .text-center {
    text-align: center;
  }

  .text-center {
    text-align: center;
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
      <li class="breadcrumb-item"><a href="#">Liquidacion de Comisiones</a></li>
    </ol>
  </nav>
  
  <div class="box" style=" background-color: white;">
    <form method="POST" id="reporte_master" action="{{ route('venta.informe_liquidaciones_comisiones') }}">
        {{ csrf_field() }}
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">LIQUIDACION COMISIONES</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="form-group col-md-4 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
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
          <div class="form-group col-md-4 col-xs-4">
            <label for="id_seguro" class="col-md-3 control-label"> Seguro: </label>
            <div class="col-md-9">
              <select class="form-control form-control-sm input-sm select2_seguros" style="width: 100%" data-placeholder="Seleccione seguro" name="id_seguro[]" id="id_seguro" multiple >
                <option value="">Todos...</option>
                @foreach($seguros as $seguro)
                <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div>


          <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          </div>
          <div class="form-group col-md-6 col-xs-9" style="text-align: right;">
          </div>
       <br>
        <div class="col-md-1">
            <dl>
              <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
            </dl>
        </div>
        <div class="col-md-3">
            <dl>
              <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
              <dd>&nbsp; {{$empresa->id}}</dd>
            </dl>
        </div>
        <div class="col-md-4">
            <h4 style="text-align: center;">LIQUIDACION DE COMISIONES</h4>
            <h5 style="text-align: center;"> @if(($fecha_desde!=null)) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}} @elseif($fecha_hasta!=null) AL - {{date("d-m-Y", strtotime($fecha_hasta))}} @endif</h5>
        </div>
      </div>

      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><strong> LIQUIDACION DE COMISIONES</strong></h3><br>
          <div class="col-md-12 has-error" style="padding: 15px; padding-left: 0px; font-size: 11px">
              <select id="productos" name="productos[]" class="form-control " multiple style="width: 100%;" >
                @foreach($productos_todos as $pro)
                <option selected value="{{$pro->codigo}}" >{{$pro->codigo}} | {{$pro->nombre}}</option>
                @endforeach
              </select>
          </div>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
          
          <div class="form-group col-md-5">
            <label for="id_plantilla" class="col-md-4 control-label"> Seleccione plantilla: </label>
            <div class="col-md-6">
              <select class="form-control form-control-sm input-sm" name="id_plantilla" id="id_plantilla">
                <option value="">Seleccione...</option>
                  <option @if($id_plantilla==1) selected @endif value="1" >COLONO / EDA</option>
                  <option @if($id_plantilla==2) selected @endif value="2" >TODOS</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary" id="boton_buscar" >
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
          </div>
          
          <!-- <div class="col-md-1">
            <button type="button" class="btn btn-warning" id="agregar_plantilla"   >
              <span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Crear Plantilla
            </button>
          </div> -->

        </div>
         
        <div class="box-body">
          <div id="index_pentax"></div>    
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-solid">
                    <table id="example2" class="display compact" style="font-size: 12px; width: 100%;">
                        <thead>
                          <tr>
                            <th class="text-center" colspan="1" style="font-size: 13px;">Todos <input type="checkbox" name="ctodos" id="ctodos" onclick="sel_todos()" ></th>
                            <th class="text-center" colspan="1" style="font-size: 13px;">No.</th>
                            <th class="text-center" rowspan="1" style="font-size: 13px;">Fecha Procedimiento</th>
                            <th class="text-center" colspan="1" style="font-size: 13px;">Identificaci&oacute;n</th>
                            <th class="text-center" colspan="1" style="font-size: 13px;">Paciente</th>
                            <th class="text-center" colspan="1" style="font-size: 13px;">{{trans('contableM.Procedimiento')}}</th>
                            <th class="text-center" colspan="1" style="font-size: 13px;">Seguro</th>
                            <th class="text-center" colspan="1" style="font-size: 13px;">Secuencia Factura</th>
                            <th class="text-center" colspan="1" style="font-size: 13px;">Valor Total</th>
                          </tr>
                        </thead> 
                        <tbody>
                          @php $total_final_1 = 0; $contador = 0; $total_final = 0; @endphp
                          @foreach($ventas as $value)
                            @if(($value)!=null)
                              @php $detalle_venta = Sis_medico\Ct_detalle_venta::where('id_ct_ventas', $value->id)->get(); @endphp
                              @foreach($detalle_venta as $value_detalle)
                                @if(($value_detalle)!=null)
                                  @if(!is_null($productos_buscados))
                                    @foreach($productos_buscados as $prod)
                                      @if(($prod)!=null)
                                        @if ($value_detalle->id_ct_productos == $prod) 
                                          @php 
                                            $contador++; $total_final+= $value_detalle->extendido;
                                            $validacion = Sis_medico\Ct_Comision_Detalle::where('estado', '1')->where('id_detalle_venta', $value_detalle->id)->first();
                                            if(is_null($validacion)){
                                              $total_final_1+= $value_detalle->extendido;
                                            }
                                          @endphp
                                          <tr>
                                            <td class="text-center">@if(is_null($validacion)) <input type="checkbox" name="check_liq[]" id="check_liq{{$value_detalle->id}}" value="{{$value_detalle->id}}" onchange="seleccion_check({{$value_detalle->id}});"> @endif </td>
                                            <td class="text-center">{{$contador}}</td>
                                            <td class="text-center">@if(($value->fecha_procedimiento)!=null) {{date("d-m-Y", strtotime($value->fecha_procedimiento))}} @endif</td>
                                            <td class="text-center">@if(($value->id_paciente)!=null) {{$value->id_paciente}} @endif</td>
                                            @if(($value->id_paciente!=null))
                                              @php $paciente = Sis_medico\Paciente::find($value->id_paciente); @endphp
                                              <td class="text-center"> {{$paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}} @endif </td>
                                            @else 
                                              <td class="text-center"></td>
                                            @endif
                                            <td class="text-center">@if(($value_detalle->nombre)!=null) {{$value_detalle->nombre}} @endif</td>
                                            @if(($value->seguro_paciente!=null))
                                              @php $seguro = Sis_medico\Seguro::find($value->seguro_paciente); @endphp
                                              <td class="text-center"> {{$seguro->nombre}}  </td>
                                            @else
                                              <td class="text-center"> </td>
                                            @endif
                                            <td class="text-center">@if(($value->nro_comprobante)!=null) {{$value->nro_comprobante}} @endif</td>
                                            <td style="text-align: right;">@if(($value_detalle->extendido)!=null) ${{number_format($value_detalle->extendido,2,'.',',')}} @endif</td>
                                          </tr>
                                        @endif
                                      @endif    
                                    @endforeach
                                  @endif   
                                @endif
                              @endforeach
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
                            <td class="text-center"><label>TOTALES:</label></td>
                            <td style="text-align: right;"><p>${{number_format($total_final,2,'.',',')}}</p></td>

                          </tr>
                        </tfoot>
                    </table>
                    <div class="col-md-8"><br>
                    <input type="hidden" name="total_final" id="total_final" value="{{$total_final_1}}">
                    <label style="font-size: 18px;">Calculo de Comision: &nbsp; &nbsp; &nbsp; </label>
                    <label>Subtotal: &nbsp;$</label><input type="text" style="width: 70px;height:30px;" name="subtotal1" id="subtotal1" value="0" readonly>
                    <input type="hidden" name="subtotal1_sin_decimal" id="subtotal1_sin_decimal" value="{{$total_final_1}}">
                    <label>&nbsp;&nbsp;X &nbsp;&nbsp; Porcentaje: &nbsp;</label><input type="text" style="width: 70px;height:30px;" name="porcentaje1" id="porcentaje1" value="" maxlength="5" placeholder="0.00" onkeyup="calcular_comision()" onkeypress="return isNumberKey(event)">     
                    <label>%&nbsp;&nbsp;= &nbsp;&nbsp;Total Comision: &nbsp;$</label><input type="text" style="width: 70px;height:30px;" name="total1" id="total1" value="" placeholder="0.00" readonly>   
                    </div>
                    <div class="col-md-2">
                      <button type="button" class="btn btn-success" id="boton_buscar" onclick="generar_comision();" style="margin-top: 15px">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Generar Comision
                      </button>
                    </div>   
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>

      <div class="box-body dobra">
        <table align="center" width="40%" border="1" style="text-align: center; font-size: 16px;">
          <tr>
            <th style="text-align: center;">Tabla de Comisiones</th>
            <th style="text-align: center;">% Comision</th>
          </tr>
          <tr>
            <th>Ventas de $25,001 A $35,000</th>
            <td>3,00%</td>
          </tr>
          <tr>
            <th>Ventas de $35,001 A $45,000</th>
            <td>3,50%</td>
          </tr>
          <tr>
            <th>Ventas de $45,001 A $ 55,000</th>
            <td>4,00%</td>
          </tr>
          <tr>
            <th>Ventas de $55,001 A $ 75,000</th>
            <td>5,00%</td>
          </tr>
          <tr>
            <th>DE $75,0001 EN ADELANTE</th>
            <td>6,00%</td>
          </tr>
        </table>
      </div>
    </form>
    <div class="col-md-12"><label style="font-size: 20px;">Comisiones Generadas</label></div>
    <div class="box-body">
      <div id="index_pentax"></div>    
      <div id="example3_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">
              <table id="example3" class="display compact" style="font-size: 12px; width: 100%;">
                  <thead>
                    <tr>
                      <th class="text-center" colspan="1" style="font-size: 13px;">Id</th>
                      <th class="text-center" colspan="1" style="font-size: 13px;">Fecha Inicio</th>
                      <th class="text-center" rowspan="1" style="font-size: 13px;">Fecha Fin</th>
                      <th class="text-center" rowspan="1" style="font-size: 13px;">Tipo Plantilla</th>
                      <th class="text-center" colspan="1" style="font-size: 13px;">Total</th>
                      <th class="text-center" colspan="1" style="font-size: 13px;">% Comision</th>
                      <th class="text-center" colspan="1" style="font-size: 13px;">Total Comision</th>
                      <th class="text-center" colspan="1" style="font-size: 13px;">Usuario Crea</th>
                      <th class="text-center" colspan="1" style="font-size: 13px;">Accion</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $contador1 = 0; @endphp
                    @foreach($comision_cabecera as $comi_cabecera)
                      @if(($comi_cabecera)!=null)
                        <tr>
                          <td class="text-center">@if(!is_null($comi_cabecera->id)) {{$comi_cabecera->id}} @endif</td>
                          <td class="text-center">@if(!is_null($comi_cabecera->fecha_inicio)) {{$comi_cabecera->fecha_inicio}} @endif</td>
                          <td class="text-center">@if(!is_null($comi_cabecera->fecha_fin)) {{$comi_cabecera->fecha_fin}} @endif</td>
                          <td class="text-center">@if(!is_null($comi_cabecera->id_plantilla)) @if($comi_cabecera->id_plantilla == '1') COLONO/EDA @elseif($comi_cabecera->id_plantilla == '2') TODOS @endif @endif</td>
                          <td class="text-center">@if(!is_null($comi_cabecera->total)) $ {{$comi_cabecera->total}} @endif</td>
                          <td class="text-center">@if(!is_null($comi_cabecera->porcentaje)) {{$comi_cabecera->porcentaje}} % @endif</td>
                          <td class="text-center">@if(!is_null($comi_cabecera->total_comision)) $ {{$comi_cabecera->total_comision}} @endif</td>
                          @php $usuario = Sis_medico\user::find($comi_cabecera->id_usuariocrea); @endphp
                          <td class="text-center">@if(!is_null($usuario)) {{$usuario->nombre1}} {{$usuario->apellido1}} @endif</td>
                          <td class="text-center">
                            <a class="btn btn-success" href="{{route('ventas.pdf_liquidacion_comision',['id'=>$comi_cabecera->id])}}" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                            <a class="btn btn-danger" onclick="eliminar('{{$comi_cabecera->id}}')"><i class="fa fa-trash-o"></i></a>
                          </td>
                        </tr>
                      @endif
                    @endforeach
                  </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  

</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
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
        "scrollY": 280,
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
          title: 'REPORTE LIQUIDACION COMISIONES {{$empresa->nombrecomercial}} \n Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}}',
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
        }
      ],
    })

    $('#example3').DataTable({
        'paging': false,
        "scrollX": true,
        "scrollY": 280,
         dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': false,
        responsive: true,
        'info': false,
        'autoWidth': true,
        buttons: [

      ],
    })

  });

  function sel_todos(){
      // Get the checkbox
      var checkBox = document.getElementById("ctodos");
      // If the checkbox is checked, display the output text
      if (checkBox.checked == true){
          $(':checkbox').prop('checked', true);  
          total_final = parseFloat($('#total_final').val());
          $('#subtotal1').val(total_final.toFixed(2));
          $('#subtotal1_sin_decimal').val(total_final.toFixed(2));
          $('#porcentaje1').val(0);
          $('#total1').val(0);
      } else {
          $(':checkbox').prop('checked', false);   
          $('#subtotal1').val(0);
          $('#subtotal1_sin_decimal').val(0);
          $('#porcentaje1').val(0);
          $('#total1').val(0); 
      }
  }

  function eliminar(id) {
    Swal.fire({
    title: '¿Desea Anular la Comision?',
    text: `{{trans('contableM.norevertiraccion')}}!`,
    icon: 'error',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
            type: 'get',
            url: "{{ url('reporte/liquidacion/comisiones/eliminar/') }}/" + id,
            datatype: 'json',
            success: function(data) {
                console.log(data);
                location.reload();
            },
            error: function(data) {
            }
        });
      }
    })
  }

  function seleccion_check(id) {

    $.ajax({
            type: 'get',
            url:"{{url('reporte/liquidacion/comisiones/buscar_precio')}}/"+id,
            datatype: 'json',
            success: function(data){
              if ($('#check_liq'+id).is(':checked')) {
                subtotal_parcial = parseFloat($('#subtotal1').val());
                subtotal = parseFloat(data.valor_total);
                subtotal_parcial = subtotal_parcial + subtotal;
                $('#subtotal1').val(subtotal_parcial.toFixed(2));
                $('#subtotal1_sin_decimal').val(subtotal_parcial);
                $('#porcentaje1').val(0);
                $('#total1').val(0);
              }else{
                subtotal_parcial = parseFloat($('#subtotal1').val());
                subtotal = parseFloat(data.valor_total);
                subtotal_parcial = subtotal_parcial - subtotal;
                $('#subtotal1').val(subtotal_parcial.toFixed(2));
                $('#subtotal1_sin_decimal').val(subtotal_parcial);
                $('#porcentaje1').val(0);
                $('#total1').val(0);
              }
            },
            error: function(data){
                    
            }
        })




  };

  $('.select2_seguros').select2({
    tags: false,
  });

  function generar_comision(){
    var confirmar = confirm('Desea realizar la liquidacion');
    if(confirmar){
      //alert("siiii");
        $.ajax({
            type: 'post',
            url: "{{ route('ventas.guardar_comision') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#reporte_master").serialize(),
            success: function(data) {
                //console.log(data);
                alertas(data.respuesta, data.titulos, data.msj);
                 if(data.respuesta == 'success'){
                     setTimeout(function(){
                         location.reload();
                     }, 1000);
                 }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
  }

  $('#productos').select2({
    placeholder: "Seleccione...",
    minimumInputLength: 2,
    ajax: {
        url: '{{route("venta.buscar_producto_codigo")}}',
        dataType: 'json',
        data: function (params) {
            //console.log(params);
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function (data) {
            //console.log(data);
            return {
                results: data
            };
        },
        cache: true
      },
        tags: true,
  });

  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

    return true;
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

  function btn_buscar() {
    $.ajax({
      type: 'post',
      url: "{{route('venta.informe_liquidaciones_comisiones')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        'fecha_desde': $("#fecha_desde").val(),
        'fecha_hasta': $("#fecha_hasta").val(),
        'productos': $("#productos").val(),
      },
      success: function(datahtml) {
      },
      error: function(data) {
        console.log(data);
      }
    });
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

  function calcular_comision() {
    comision = 0;
    subtotal = parseFloat($('#subtotal1').val());
    porcentaje = parseFloat($('#porcentaje1').val());
    comision = (subtotal * porcentaje)/100;
    $('#total1').val(comision.toFixed(2));
  }

  function alertas(icon, title, msj) {
    Swal.fire({
        icon: icon,
        title: title,
        html: msj
    })
  }

</script>
@endsection
