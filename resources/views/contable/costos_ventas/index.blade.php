@extends('contable.costos_ventas.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
  <!-- Main content -->
  <section class="content">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="#">Costos</a></li>

      </ol>
    </nav>

    <div class="box" style=" background-color: white;">
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
          </div>
        </div>

      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{route('reportecostosventas.index_costos_ventas')}}" >
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}</label>
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
          <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}</label>
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
          <button type="submit" class="btn btn-primary" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
          <!--button formaction="{{route('reporte.reporte_global_costos')}}" id="btn_exportar" name="btn_exportar" formtarget="_blank" class="btn btn btn-primary"><i class="glyphicon glyphicon-save-file">{{trans('contableM.Exportar')}}</i></button-->
        </div>

      </form> 
      </div>
      @if(count($planillas)>0)
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">

              <div class="box-body">
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
                  <h4 style="text-align: center;"> Informe de Costos Vs Ventas </h4>
                  <h5 style="text-align: center;"> @if(($fecha_desde)!=null)  {{date("d-m-Y", strtotime($fecha_desde))}} - {{date("d-m-Y", strtotime($fecha_hasta))}} @else {{trans('laboratorio.al')}} - {{date("d-m-Y",strtotime($fecha_hasta))}} @endif </h5>
                </div>
               

              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <table id="example2" class="display compact" style="font-size: 12px!important">
                      <thead>
                        <tr >
                          <th style="text-align:center;"  >{{trans('contableM.fecha')}}</th>
                          <th style="text-align:center;" > Paciente </th>
                          <th  style="text-align:center;" > Procedimiento </th>
                          <th  style="text-align:center;" > Seguro </th>
                          <th style="text-align:rigth;" > Total Planilla </th>
                          <th style="text-align:rigth;" > Valor Público </th>
                          <th style="text-align:rigth;" > Valor Privado </th>
                          <th style="text-align:rigth;" > Utilidad  </th>
                          <th style="text-align:rigth;"> Detalle en PDF </th>

                        </tr>
                      </thead>
                      <tbody>
                            @php 
                            
                            $sum_total = 0;
                            $total_ap = 0;
                            $acum_precio = 0;
                            $acum_planilla = 0;
                            $acum_venta = 0;
                            @endphp
                            @foreach($planillas as $value)

                                @php 
                                    
                                    $utilidad = 0;
                                    $procedimiento_final = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_hc_procedimiento)->first();
                                    $precio = \Sis_medico\Planilla_Detalle::where('id_planilla_cabecera',$value->id)->where('check',1)->where('estado',1)->select('id','precio','cantidad')->get();
                                    $total = 0;
                                    $total_total = 0;
                                    
                                    foreach($precio as $value1){
                                      $total = $value1->precio * $value1->cantidad;
                                      $total_total += $total;
                                    }
                                    $acum_planilla += $total_total;
                                    
                                @endphp

                                <tr>
                                    <td style="text-align:center;">{{date("d-m-Y", strtotime($value->fecha))}}</td>
                                    <td style="text-align:center;">@if(($value->observacion)!=null) {{$value->observacion}} @endif</td>
                                    <td style="text-align:center;"> 
                                    @if(!is_null($procedimiento_final)) 
                                    @if(!is_null($procedimiento_final->procedimiento))
                                    @if(!is_null($procedimiento_final->procedimiento->nombre)){{$procedimiento_final->procedimiento->nombre}}@endif @else CONSULTA @endif @else CONSULTA @endif </td>

                                    @php
                                    $hc_procedimiento = \Sis_medico\hc_procedimientos::where('id', $value->id_hc_procedimiento)->first();
                                    
                                    $hc_protocolo   = \Sis_medico\hc_protocolo::where('id_hc_procedimientos',$hc_procedimiento->id)->first();    
                                    $historia_clinica = $hc_procedimiento->historia;
                                    $seguro  = $historia_clinica->seguro;     
                                    $archivo_plano =  \Sis_medico\Archivo_Plano_Cabecera::where('id_hc_procedimimentos',$hc_procedimiento->id)->where('estado',1)->first(); 
                                    $hcpf       = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_procedimiento->id)->get();
                                    $agenda   = $historia_clinica->agenda;
                                    $paciente = $agenda->paciente;
                                    $empresa  = \Sis_medico\Empresa::find($agenda->id_empresa);
                                    $detalles = '[]';
                                    $valor_vent =0;
                                    if($seguro->id != 2 or $seguro->id !=3 or $seguro->id !=5 or $seguro->id !=6){
                                      if (isset($agenda->id)) {
                                        $ordenes = \Sis_medico\Ct_Orden_Venta::where('id_agenda', $agenda->id)->where('estado', '1')->get();
                                        $vt_detalles = []; $cont = 0;
                                        foreach ($ordenes as $orden) {
                                            $venta = \Sis_medico\Ct_ventas::where('orden_venta', $orden->id)->first();
                                            if (isset($venta->id)) {
                                              $valor_vent = $venta->total_final;
                                              $acum_venta += $valor_vent;
                                            }
                                        }
                                      }
                                    }else{
                                      $valor_vent = "";
                                    }
                                    $valor_ap = 0;
                                    if(!is_null($archivo_plano)){
                                      $valor_ap = $archivo_plano->valor;
                                      $total_ap  += $valor_ap;
                                    }
                                    if(!is_null($archivo_plano)){
                                      $utilidad = $valor_ap - $total_total;
                                    }else{
                                      $utilidad = $valor_vent - $acum_planilla;
                                    } 
                                    @endphp
                                    <td style="text-align:center;"> {{$seguro->nombre}} </td>
                                    <td style="text-align:center;">  @if(($total_total)!=null) {{$total_total}} @endif  </td>   
                                    <td style="text-align:center;"> @if(!is_null($archivo_plano)) {{$valor_ap}} @endif </td> 
                                    <td style="text-align:center;"> @if(!is_null($valor_vent)) {{$valor_vent}} @endif </td>
                                    <td style="text-align:center;">  @if(!is_null($archivo_plano)) @if($valor_ap - $total_total > 0 ){{$valor_ap - $total_total}} @endif @endif @if(!is_null($valor_vent)) @if($valor_vent - $total_total > 0 ) {{$valor_vent - $total_total}} @endif @endif  </td> 
                                    @if($seguro->id == 2 or $seguro->id ==3 or $seguro->id ==5 or $seguro->id ==6)
                                    <td style="text-align:center;"> <a class="btn btn-success btn-xs" href="{{route("ap_planilladetalle.planilla_detalle_contab_pdf_vs",['id' => $hc_procedimiento->id,'id_procedimiento' => $hc_procedimiento->id])}}"> PDF </a>  </td>                    
                                    @else
                                    <td style="text-align:center;"> <a class="btn btn-success btn-xs" href="{{route("planilla.venta.vs.compra.detalle.pdf",['id' => $hc_procedimiento->id,'id_procedimiento' => $hc_procedimiento->id])}}"> PDF </a>  </td>
                                    @endif
                                </tr>
                            @endforeach
                            @php
                            $sum_total += $acum_precio;
                            @endphp
                           
                        </tbody>
                        <tfoot>
                                 <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align: center; font-weight: bold;">{{trans('contableM.totales')}}:</td>
                                    <td style="text-align: center; font-weight: bold;">{{number_format($sum_total,4,'.',',')}}</td>
                                    <td style="text-align: center; font-weight: bold;">{{number_format($total_ap,4,'.',',')}}</td>
                                    <td style="text-align: center; font-weight: bold;">{{number_format($acum_venta,4,'.',',')}}</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                        </tfoot>
                    </table>

                </div>
                
              </div>
              </div>
            </div>
          </div>
        </div> 
      </div>
      @endif
    </div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">

    $('#example2').DataTable({
        'paging': false,
         dom: 'Bfrtip',
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
          title: '{{trans('contableM.reportereten-')}} {{$empresa->nombrecomercial}}'
        },
        {
          extend: 'csvHtml5',
          footer: true
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          footer: true,
          title: '{{trans('contableM.reportereten-')}} {{$empresa->nombrecomercial}}',
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
          text: `{{trans('winsumos.verifiquerangofechayveuvlaconsultar')}}`
      });
    } 
  }

</script>
@endsection
