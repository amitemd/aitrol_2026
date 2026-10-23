@extends('archivo_plano.archivo.base')
@section('action-content')
<style >
    .table>tbody>tr>td{
    padding: 0;
    }
    .control-label{
        padding: 1;
        align-content: left;
        font-size: 14px;
    }
    .form-group{
        padding: 0;
        margin-bottom: 4px;
        font-size: 14px
    }
    table.dataTable thead > tr > th {
    padding-right: 10px;
    } 
    td{
        font-size: 12px;
    }
    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 12px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-autocomplete
    {
        opacity : 1;
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
        _width: 470px !important;
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
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
</style>
<div class="modal fade" id="codigo_proceso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<section class="content">
	<div class="box">
		<div class="box-header">
		    <div class="row">
		        <div class="col-sm-8">
		          <h3 class="box-title">Generación Archivo Plano MSP</h3>
		        </div>
                <div class="col-sm-2">
                  <a href="{{route('aparchivo.total_agrupado')}}" class="btn btn-primary btn-xs">Total Agrupados</a>
                </div>
		    </div>
		</div>
    <div class="box-body">
      <form id="busq_mes_empresa" method="POST" action="{{route('genera_ap_msp_excel.planilla')}}">
        {{ csrf_field() }}
        <div class="row">
            <!--<div class="form-group col-md-4">
              <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
              <div class="col-md-7">
                  <select id="mes_plano" name="mes_plano" class="form-control input-sm">
                      <option value="">Seleccione...</option>
                      @foreach($mes_plan as $value)
                      <option  value="{{$value->mes_plano}}">{{$value->mes_plano}}</option>
                      @endforeach
                  </select>
              </div>
            </div>-->
            <div class="form-group col-md-4 ">
              <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
              <div class="col-md-7">
                  <input id="mes_plano" type="text" class="form-control input-sm" name="mes_plano" value="{{$mes_plano}}">
              </div>
            </div>
            <div class="form-group col-md-4">
              <label for="id_empresa" class="col-md-4 control-label">Empresa:</label>
              <div class="col-md-7">
                  <select id="id_empresa" name="id_empresa" class="form-control input-sm">
                      <option value="">Seleccione...</option>
                      @foreach($empresas as $value)
                        <option  value="{{$value->id}}" @if($empresa==$value->id) selected="selected" @endif>{{$value->nombrecomercial}}</option>
                      @endforeach
                  </select>
              </div>
            </div>
            <div class="form-group col-md-4">                     
              <div class="col-md-3">
                  <button type="submit" formaction="{{ route('genera_ap_msp.planilla')}}" class="btn btn-primary" id="boton_buscar">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                  </button>
              </div>
              <div class="col-md-4">
                <button type="button" onclick="descargar_reporte();" class="btn btn-primary" id="btn_exportar">
                  <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar-Msp
                </button> 
              </div>
              <div class="col-md-2">
                <!--<button type="button" onclick="reporte_consolidado();" class="btn btn-primary" id="btn_exportar">
                  <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span>Consolidado
                </button>-->
                <button type="button" class="btn btn-primary" id="btn_consolidado">
                  <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Consolidado
                </button>
              </div>
            </div>
          </div>
      </form>
      <br>
      <!--div style="border-radius: 8px;" id="det_busqueda">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
            <thead style="background-color: #4682B4">
              <tr >
                <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">TIPO SEG</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">TIPO SEGURO</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">N_EXP</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">BASE_0</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">BASE_IVA</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">V_IVA</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">GAST_AMD_10</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">TOTAL_M_IVA</th>
                  <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">ACCION</th>
              </tr>
            </thead>
            <tbody>
              @php
                    $total_0=0;
                    $total_iva=0;
                    $total_v_iva=0;
                    $total_10=0;
                @endphp

                @foreach($cant_pac as $value)
                @php
                    if(isset($arr_base_0[$value->id])) {
                        $total_0=$arr_base_0[$value->id];
                    }
                    if(isset($arr_base_iva[$value->id])) {
                        $total_iva=$arr_base_iva[$value->id];
                    }
                    if (isset($arr_v_iva[$value->id])) {
                        $total_v_iva=$arr_v_iva[$value->id];
                    }
                    
                    if (isset($arr_amd_10[$value->id])) {
                        $total_10=$arr_amd_10[$value->id];
                    }
                    
                    $total_m_iva=$total_0+$total_iva+$total_v_iva+$total_10;

                    $ap_agrupado = Sis_medico\Ap_Agrupado::where('mes_plano',$mes_plano)->where('empresa',$empresa)->where('seguro','5')->where('id_tipo_seg',$value->id)->first();
                @endphp
                <tr>
                    <input value="5" class="hidden" type="text" id="seguro" >
                    <td>{{$value->tipo}} <input value="{{$value->id}}" class="hidden" type="text" id="tipo_seguro" ></td>
                    <td>{{$value->nombre}} <input value="{{$value->nombre}}" class="hidden" type="text" id="nombre_tseg"></td>
                    <td>{{$value->cantidad}} <input value="{{$value->cantidad}}" class="hidden" type="text" id="cantidad_{{$value->id}}"></td>
                    <td>@if(isset($arr_base_0[$value->id])) ${{round($arr_base_0[$value->id],2)}} <input value="{{round($arr_base_0[$value->id],2)}}" class="hidden" type="text" id="base_0_{{$value->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="base_0_{{$value->id}}"> @endif </td>
                    <td>@if(isset($arr_base_iva[$value->id])) ${{round($arr_base_iva[$value->id],2)}} <input value="{{round($arr_base_iva[$value->id],2)}}" class="hidden" type="text" id="base_iva_{{$value->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="base_iva_{{$value->id}}"> @endif</td>
                    <td>@if(isset($arr_v_iva[$value->id])) ${{round($arr_v_iva[$value->id],2)}} <input value="{{round($arr_v_iva[$value->id],2)}}" class="hidden" type="text" id="v_iva_{{$value->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="v_iva_{{$value->id}}">  @endif</td>
                    <td>@if(isset($arr_amd_10[$value->id])) ${{round($arr_amd_10[$value->id],2)}} <input value="{{round($arr_amd_10[$value->id],2)}}" class="hidden" type="text" id="amd_10_{{$value->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="amd_10_{{$value->id}}"> @endif</td>
                    <td>${{round($total_m_iva,2)}} <input value="{{round($total_m_iva,2)}}" class="hidden" type="text" id="total_iva_{{$value->id}}"> </td>
                  
                    <td>
                        <input value="1" class="hidden" type="text" id="tipo">
                        @if(isset($ap_agrupado))  <button class="btn btn-primary btn-xs">GUARDADO</button> @else
                        <a id="codigo_proceso" class="btn btn-info btn-xs" href="{{route('archivo.codigo_proceso',['id' =>$value->id])}}" data-toggle="modal" data-target="#codigo_proceso"> <span> CODIGO PROCESO </span></a>
                        @endif
                    </td>
                </tr>
                    
                @endforeach
            </tbody>
          </table>

          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
                    <thead style="background-color: #4682B4">
                        <tr >
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">TIPO SEG</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">TIPO SEGURO</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">N_EXP</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">BASE_0</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">BASE_IVA</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">V_IVA</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">GAST_AMD_10</th>
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">VALOR COBRADO</th>
                        
                          <th style="width: 6%;height:8px;color: white;text-align: center; font-size: 12px;">ACCION</th>
                        </tr>
                    </thead>
                <tbody>
                @php
                    $total_0=0;
                    $total_iva=0;
                    $total_v_iva=0;
                    $total_10=0;
                @endphp

                @foreach($cant_pac as $value2)
                @php
                    if(isset($arr_base_0[$value2->id])) {
                        $total_0=$arr_base_0[$value2->id];
                    }
                    if(isset($arr_base_iva[$value2->id])) {
                        $total_iva=$arr_base_iva[$value2->id];
                    }
                    if (isset($arr_v_iva[$value2->id])) {
                        $total_v_iva=$arr_v_iva[$value2->id];
                    }
                    
                    if (isset($arr_amd_10[$value2->id])) {
                        $total_10=$arr_amd_10[$value2->id];
                    }
                    
                    $total_m_iva=$total_0+$total_iva+$total_v_iva+$total_10;

                    $ap_agrupado = Sis_medico\Ap_Agrupado::where('mes_plano',$mes_plano)->where('empresa',$empresa)->where('seguro','5')->where('id_tipo_seg',$value2->id)->where('estado_pago','2')->where('valor_cobrado','>','0')->first();
                    //dd($ap_agrupado);

                    $ap_agrup = Sis_medico\Ap_Agrupado::where('mes_plano',$mes_plano)->where('empresa',$empresa)->where('seguro','5')->where('id_tipo_seg',$value2->id)->where('tipo','2')->first();
                @endphp

                    @if(isset($ap_agrupado))
                        <tr>
                            
                            <td>{{$value2->tipo}} <input value="{{$value2->id}}" class="hidden" type="text" id="tipo_seguro" ></td>
                            <td>{{$value2->nombre}} <input value="{{$value2->nombre}}" class="hidden" type="text" id="nombre_tseg"></td>
                            <td>{{$value2->cantidad}} <input value="{{$value2->cantidad}}" class="hidden" type="text" id="cantidad_{{$value2->id}}"></td>
                            <td>@if(isset($arr_base_0[$value2->id])) ${{round($arr_base_0[$value2->id],2)}} <input value="{{round($arr_base_0[$value2->id],2)}}" class="hidden" type="text" id="base_0_{{$value2->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="base_0_{{$value2->id}}"> @endif </td>
                            <td>@if(isset($arr_base_iva[$value2->id])) ${{round($arr_base_iva[$value2->id],2)}} <input value="{{round($arr_base_iva[$value2->id],2)}}" class="hidden" type="text" id="base_iva_{{$value2->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="base_iva_{{$value2->id}}"> @endif</td>
                            <td>@if(isset($arr_v_iva[$value2->id])) ${{round($arr_v_iva[$value2->id],2)}} <input value="{{round($arr_v_iva[$value2->id],2)}}" class="hidden" type="text" id="v_iva_{{$value2->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="v_iva_{{$value2->id}}">  @endif</td>
                            <td>@if(isset($arr_amd_10[$value2->id])) ${{round($arr_amd_10[$value2->id],2)}} <input value="{{round($arr_amd_10[$value2->id],2)}}" class="hidden" type="text" id="amd_10_{{$value2->id}}"> @else {{'$0,00'}} <input value="0" class="hidden" type="text" id="amd_10_{{$value2->id}}"> @endif</td>
                            <td> ${{$ap_agrupado->valor_cobrado}} <input value="{{$ap_agrupado->valor_cobrado}}" class="hidden" type="text" id="valor_cobrado_{{$value2->id}}"></td>
                            <td>
                                <input value="2" class="hidden" type="text" id="tipo2">
                                @if(isset($ap_agrup)) <button class="btn btn-primary btn-xs">GUARDADO</button> @else
                                <a id="codigo_proceso" class="btn btn-info btn-xs" href="{{route('archivo.codigo_proceso2',['id' =>$value2->id])}}" data-toggle="modal" data-target="#codigo_proceso"> <span> OBJETAR </span></a> @endif
                            </td>
                        </tr>
                @endif
                    
                @endforeach
            </tbody>
            <tfoot>
            </tfoot>
          </table>
        </div>
      </div-->
      <div class="table-responsive col-md-12" >
              @if($sumatoria != null)
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
                    <thead style="background-color: #4682B4">
                        <tr >
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">TIPO SEGURO</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">BASE 0</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">BASE 12</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">IVA</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">ADMIN</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">MSP</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">TOTAL</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">ACCION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>     
                            <td style="text-align: left;">MSP</td>
                            <td style="text-align: right;">{{number_format($sumatoria['base0'], 2, ',', ' ')}}</td>
                            <td style="text-align: right;">{{number_format($sumatoria['base12'], 2, ',', ' ')}}</td>
                            <td style="text-align: right;">{{number_format($sumatoria['iva'], 2, ',', ' ')}} </td>
                            <td style="text-align: right;">{{number_format($sumatoria['admin'], 2, ',', ' ')}}</td>
                            <td style="text-align: right;">{{number_format($sumatoria['msp'], 2, ',', ' ')}}</td>
                            <td style="text-align: right;">{{number_format($sumatoria['total'], 2, ',', ' ')}}</td>
                            <td style="text-align: center;">
                                <a class="btn btn-info btn-xs oculto" href="{{route('aparchivo.plano_contable_ingresar',['aniomes' => $mes_plano, 'tipo' => 'MSP', 'seg' => '5', 'cobertura' => '0', 'empresa' => $empresa ])}}" data-toggle="modal" data-target="#codigo_proceso" id="boton_ingresarMSP"> <span> INGRESAR </span></a>
                                <button class="btn btn-info btn-xs" onclick="ingresar_js('MSP')"><span> INGRESAR </span></button>  
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
              @endif 
            </div>

            <div class="table-responsive col-md-12" >

                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
                    <thead style="background-color: #4682B4">
                        <tr >
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">TIPO SEGURO</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">TRAMITE</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">PRESENTADO</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">FACTURADO</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">OBJETADO</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">GLOSAS %</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">LEVANTAR</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">ACEPTADO</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">ESTADO</th>
                            <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">ACCION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agrupados as $key => $grupo)
                            <tr>     
                                <td style="text-align: left;">{{$grupo->id_tipo_seg}}</td>
                                <td style="text-align: right;">{{$grupo->cod_proceso}} </td>
                                <td style="text-align: right;">{{number_format($grupo->valor_cobrado, 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($grupo->valor_facturado, 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($grupo->valor_objetado, 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($grupo->porcentaje_glosa, 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($grupo->valor_levantar, 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($grupo->valor_aceptado, 2, ',', ' ')}}</td>
                                <td style="text-align: center;">
                                    @if($grupo->estado_pago=='0')
                                        ENTREGADO
                                    @elseif($grupo->estado_pago=='1')
                                        PENDIENTE DE RESPONDER
                                    @elseif($grupo->estado_pago=='2')
                                        POR ENVIAR
                                    @elseif($grupo->estado_pago=='3')
                                        SE ACEPTA OBJECION
                                    @elseif($grupo->estado_pago=='4')
                                        PENDIENTE DE RECIBIR LIQUIDACION
                                    @else
                                        PAGADO    
                                    @endif    
                                </td>
                                <td style="text-align: center;">
                                    <a class="btn btn-info btn-xs" href="{{route('aparchivo.plano_contable_editar',['id' => $grupo->id ])}}" data-toggle="modal" data-target="#codigo_proceso"> <span> EDITAR </span></a> 
                                    <button class="btn btn-danger btn-xs" onclick="eliminar('{{$grupo->id}}')">ELIMINAR</button> 
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
    </div>
  </div>
</section>
<form method="POST" id="xls_reporte_consolidado" action="{{ route('genera_msp_rp_consol.planilla') }}">
  {{ csrf_field() }}
    <input type="hidden" name="id_empr" id="id_empr" value="{{@$id_empresa}}">
    <input type="hidden" name="mes_plan" id="mes_plan" value="{{@$mes_plano}}">
    <!--<input type="hidden" name="btn_exportar" id="btn_exportar" value="0">-->
    <input type="hidden" name="btn_consolidado" id="btn_consolidado" value="">
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    $(document).ready(function(){

        $('#codigo_proceso').on('hidden.bs.modal', function(){
      
          $(this).removeData('bs.modal');
          $('#boton_buscar').click();
        });    
        
      $('#example2').DataTable({
        'language': {
        'emptyTable': '<span class="label label-primary" style="font-size:14px;">No se encontraron registros.</span>'
      },
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });
    });

    $("#mes_plano").autocomplete({
        source: function( request, response ) {
            
            $.ajax({
                url:"{{route('search.mes_plano')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {term: request.term},
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    //console.log(data);

                }
            })
        },
        minLength: 2,
    } );

    function ingresar_js(key){
        var confirmar = confirm("Desea Ingresar Valores para el tipo: "+key);
        if(confirmar){
            $('#boton_ingresar'+key).trigger("click");    
        }
    }

    function eliminar(id){
        var confirmar = confirm("Desea Eliminar el registro");
        if(confirmar){
            $.ajax({
                type: 'get',
                url: "{{ url('plano_contable/eliminar/registro')}}/"+id,
                datatype: 'html',
                success: function(datahtml){
                    location.reload();               
                },
                error:  function(){
                    alert('error al cargar');
                }
            });
        }    
    }

    function descargar_reporte() {
      $( "#busq_mes_empresa" ).submit(); 
    }

    $( "#btn_consolidado" ).click(function() {
      $("#id_empr").val($("#id_empresa").val());
      $("#mes_plan").val($("#mes_plano").val());
      $( "#xls_reporte_consolidado" ).submit(); 
    });


    /*function reporte_consolidado(){
       
      var formulario = document.forms["busq_mes_empresa"];
      var mes_pla = formulario.mes_plano.value;
      var id_empresa = formulario.id_empresa.value;

      var msj = "";

      if(mes_pla == ""){
        msj = msj + "Por favor, Seleccione el Mes del Plano<br/>";
      }

      if(id_empresa == ""){
        msj = msj + "Por favor, Seleccione la Empresa<br/>";
      }

      if(msj != ""){
        swal({
          title: "Error!",
          type: "error",
          html: msj
        });
        return false;
      }

      $.ajax({
          type: 'post',
          url:"{{route('genera_msp_rp_consol.planilla')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#busq_mes_empresa").serialize(),
          success: function(data){
            console.log(data);
            //$("#det_busqueda").html(data);
          },
          error: function(data){
            console.log(data);
          }
      });
    
    }*/
/*
    function buscar_mes_empresa(){

        var formulario = document.forms["busq_mes_empresa"];
        var mes_pla = formulario.mes_plano.value;
        var id_empresa = formulario.id_empresa.value;
      
        var msj = "";

        if(mes_pla == ""){
          msj = msj + "Por favor, Seleccione el Mes del Plano<br/>";
        }

        if(id_empresa == ""){
          msj = msj + "Por favor, Seleccione la Empresa<br/>";
        }

        if(msj != ""){
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
        }

        $.ajax({
          type: 'post',
          url:"{{route('buscar.mes_plano')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#busq_mes_empresa").serialize(),
          success: function(data){
            $("#det_busqueda").html(data);
          },
          error: function(data){
            console.log(data);
          }
        });

    }*/

    function guardar(id){

        //alert(id);
        
        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();
        var cantidad_exp = $('#cantidad_'+id).val();
        var base_0 = $('#base_0_'+id).val(); 
        var base_iva = $('#base_iva_'+id).val(); 
        var v_iva = $('#v_iva_'+id).val();  
        var amd_10 = $('#amd_10_'+id).val();
        var total_iva = $('#total_iva_'+id).val();
        var codigo =$('#codigo_'+id).val();
        var tipo =$('#tipo').val();

        console.log(mes_plano, empresa, seguro, id, cantidad_exp, base_0, base_iva);

        $.ajax({
            type: 'post',
            url:"{{ route('archivo.guardar_agrupado')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {
                "tipo_seguro":id,
                "mes_plano":mes_plano,
                "seguro":seguro,
                "empresa":empresa,
                "cantidad":cantidad_exp,
                "base_0":base_0,
                "base_iva":base_iva,
                "v_iva":v_iva,
                "amd_10":amd_10,
                "total_iva":total_iva,
                "codigo":codigo,
                "tipo":tipo,
            },
            success: function(data){
                console.log(data);
                $( "#boton_buscar" ).click();
                if(data == "ok"){
                    swal({
                        title: "Datos Guardados",
                        icon: "success",
                        type: 'success',
                        buttons: true,
                    })
                    
                };
            },
            error: function(data){
                console.log(data);
            }
            });
    }

    function guardar_objetar(id){

        //alert(id);
        
        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();
        var cantidad_exp = $('#cantidad_'+id).val();
        var base_0 = $('#base_0_'+id).val(); 
        var base_iva = $('#base_iva_'+id).val(); 
        var v_iva = $('#v_iva_'+id).val();  
        var amd_10 = $('#amd_10_'+id).val();
        var total_iva = $('#valor_cobrado_'+id).val();
        var codigo =$('#codigo_'+id).val();
        var valor_cobrado = $('#valor_cobrado_'+id).val();
        var tipo =$('#tipo2').val();


        console.log(mes_plano, empresa, seguro, id, cantidad_exp, base_0, base_iva);

        $.ajax({
            type: 'post',
            url:"{{ route('archivo.guardar_agrupado')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {
                "tipo_seguro":id,
                "mes_plano":mes_plano,
                "seguro":seguro,
                "empresa":empresa,
                "cantidad":cantidad_exp,
                "base_0":base_0,
                "base_iva":base_iva,
                "v_iva":v_iva,
                "amd_10":amd_10,
                "total_iva":total_iva,
                "codigo":codigo,
                "valor_cobrado":valor_cobrado,
                "tipo":tipo,
            },
            success: function(data){
                console.log(data);
                $( "#boton_buscar" ).click();
                if(data == "ok"){
                    swal({
                        title: "Datos Guardados",
                        icon: "success",
                        type: 'success',
                        buttons: true,
                    })
                    
                };
            },
            error: function(data){
                console.log(data);
            }
            });
    }

</script>
@endsection