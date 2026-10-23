@extends('archivo_plano.archivo.base')
@section('action-content')
<style>
    .table>tbody>tr>td {
        padding: 0;
    }

    .control-label {
        padding: 1;
        align-content: left;
        font-size: 14px;
    }

    .form-group {
        padding: 0;
        margin-bottom: 4px;
        font-size: 14px
    }

    table.dataTable thead>tr>th {
        padding-right: 10px;
    }

    td {
        font-size: 12px;
    }

    .ui-corner-all {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget {
        font-family: Verdana, Arial, sans-serif;
        font-size: 12px;
    }

    .ui-menu {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity: 1;
    }

    .ui-autocomplete {
        opacity: 1;
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

    .ui-menu .ui-menu-item {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .ui-menu .ui-menu-item a {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }

    .ui-menu .ui-menu-item a:hover {
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
                        <h3 class="box-title">Generación Archivo Plano IESS</h3>
                    </div>

                    <!--div class="col-sm-2">
                        <a href="{{route('aparchivo.total_agrupado')}}" class="btn btn-primary btn-xs">Total Agrupados</a>
                    </div-->

                              

                   

                </div>
            </div>
            <div class="box-body">
                <form method="POST" action="#">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-4 ">
                            <label for="mes_plano" class="col-md-4 control-label">Mes Plano:</label>
                            <div class="col-md-7">
                                <input id="mes_plano" value="@if(isset($mes_plano)) {{$mes_plano}} @endif" type="text" class="form-control input-sm" name="mes_plano" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="seguro" class="col-md-4 control-label">Seguro:</label>
                            <div class="col-md-7">
                                <select id="seguro" name="seguro" class="form-control input-sm">
                                    @foreach($seguros_publicos as $value)
                                    <option @if($seg==$value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    
                        <!--div class="form-group col-md-4 ">
                            <label for="id_cobertura_comp" class="col-md-4 control-label">Cobertura Compartida:</label>
                            <div class="col-md-7">
                                <select id="id_cobertura_comp" name="id_cobertura_comp" class="form-control input-sm">
                                    <option value="">NINGUNO</option>
                                    @foreach($seguros_publicos as $value)
                                    @if($value->id == 3 || $value->id == 6)
                                    <option @if($cob_compar==$value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div-->

                        <div class="form-group col-md-4 ">
                            <label for="id_empresa" class="col-md-4 control-label">Empresa:</label>
                            <div class="col-md-7">

                                <select id="id_empresa" name="id_empresa" class="form-control input-sm">
                                    @php $emp_prioridad = $empresas->where('prioridad','1')->first() @endphp
                                    @foreach($empresas as $value)
                                        @if($emp_prioridad->id == '0992704152001'  )
                                            @if($value->id=='0992704152001' || $value->id=='1307189140001')
                                            <option value="{{$value->id}}" @if($empresa==$value->id) selected="selected" @endif>{{$value->nombrecomercial}}</option>
                                            @endif
                                        @else
                                            @if($value->id == $emp_prioridad->id)
                                            <option value="{{$value->id}}" @if($empresa==$value->id) selected="selected" @endif>{{$value->nombrecomercial}}</option>
                                            @endif
                                        @endif    
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-2 ">
                            <div class="col-md-7">
                                <button type="submit" formaction="{{ route('aparchivo.resumen_planillas')}}" class="btn btn-primary" id="boton_buscar"><span class="glyphicon glyphicon-search"> Buscar</span></button>
                            </div>
                        </div>
                    </div>
                </form>

                @php
                $mes = substr($mes_plano,0,2);
                $anio = substr($mes_plano,2);
                $nmes = intval($mes);

                $tmes = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                $cobertura = $cob_compar;
                if($cob_compar==null){
                $cobertura = 0;
                }
                @endphp
                <span class="right badge badge-danger">@if(isset($tmes[$nmes])) {{$tmes[$nmes]}} @endif - {{ $anio }}</span>

                <div class="table-responsive col-md-12">

                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
                        <thead style="background-color: #4682B4">
                            <tr>
                                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">TIPO SEGURO</th>
                                <!--th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">P X Q</th-->
                                <!--th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">IVA</th>
                                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">TOTAL</th-->
                                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">BASE 0</th>
                                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">BASE 12</th>
                                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">IVA</th>
                                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">ADMIN</th>
                                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">TOTAL</th>
                                <th style="width: 10%;height:8px;color: white;text-align: center; font-size: 12px;">ACCION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sumatoria as $key => $value)
                            @php $totalap = $value['pxq'] + $value['iva']; $total_agr = $value['base0'] + $value['base12'] + $value['admin'] + $value['iva']; @endphp
                            @if($total_agr > 0)
                            <tr>
                                <td style="text-align: left;">{{$key}}</td>
                                <!--td style="text-align: right;">{{number_format($value['pxq'], 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($value['iva'], 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($totalap, 2, ',', ' ')}} </td-->
                                <td style="text-align: right;">{{number_format($value['base0'], 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($value['base12'], 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($value['iva'], 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($value['admin'], 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($total_agr, 2, ',', ' ')}}</td>
                                <td style="text-align: center;">
                                    <form method="POST" action="#">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id_seguro" value="{{$seg}}" />
                                    <input type="hidden" name="mes_plano" value="{{$mes_plano}}" />
                                    <input type="hidden" name="tipo" value="{{$key}}" />
                                    <input type="hidden" name="id_empresa" value="{{$empresa}}" />
                                    <input type="hidden" name="id_cobertura_comp" />
                                    <button type="submit" formaction="{{route('aparchivo.resumen_agrupado')}}" class="btn btn-primary"><span class="glyphicon glyphicon-download-alt"> Reporte</span></button>
                                    </form>
                                    <!--a class="btn btn-info btn-xs oculto" href="{{route('aparchivo.plano_contable_ingresar',['aniomes' => $mes_plano, 'tipo' => $key, 'seg' => '2', 'cobertura' => $cobertura, 'empresa' => $empresa ])}}" data-toggle="modal" data-target="#codigo_proceso" id="boton_ingresar{{$key}}"> <span> INGRESAR </span></a-->
                                    <!--button class="btn btn-info btn-xs" onclick="ingresar_js('{{$key}}')"><span> INGRESAR </span></button-->
                                </td>
                            </tr>
                            @endif
                            @endforeach
                            @foreach($sumatoria_issfa as $key => $value)
                            @php $totalap = $value['pxq'] + $value['iva']; $total_agr = $value['base0'] + $value['base12'] + $value['admin'] + $value['iva']; @endphp
                            @if($total_agr > 0)
                            <tr>
                                <td style="text-align: left;">{{$key}} ISSFA</td>
                                <!--td style="text-align: right;">{{number_format($value['pxq'], 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($value['iva'], 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($totalap, 2, ',', ' ')}} </td-->
                                <td style="text-align: right;">{{number_format($value['base0'], 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($value['base12'], 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($value['iva'], 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($value['admin'], 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($total_agr, 2, ',', ' ')}}</td>
                                <td style="text-align: center;">
                                    <form method="POST" action="#">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id_seguro" value="{{$seg}}" />
                                    <input type="hidden" name="mes_plano" value="{{$mes_plano}}" />
                                    <input type="hidden" name="tipo" value="{{$key}}" />
                                    <input type="hidden" name="id_empresa" value="{{$empresa}}" />
                                    <input type="hidden" name="id_cobertura_comp" value="3" />
                                    <button type="submit" formaction="{{route('aparchivo.resumen_agrupado')}}" class="btn btn-primary"><span class="glyphicon glyphicon-download-alt"> Reporte</span></button>
                                    </form>
                                    <!--a class="btn btn-info btn-xs oculto" href="{{route('aparchivo.plano_contable_ingresar',['aniomes' => $mes_plano, 'tipo' => $key, 'seg' => '2', 'cobertura' => $cobertura, 'empresa' => $empresa ])}}" data-toggle="modal" data-target="#codigo_proceso" id="boton_ingresar{{$key}}"> <span> INGRESAR </span></a-->
                                    <!--button class="btn btn-info btn-xs" onclick="ingresar_js('{{$key}}')"><span> INGRESAR </span></button-->
                                </td>
                            </tr>
                            @endif
                            @endforeach
                            @foreach($sumatoria_isspol as $key => $value)
                            @php $totalap = $value['pxq'] + $value['iva']; $total_agr = $value['base0'] + $value['base12'] + $value['admin'] + $value['iva']; @endphp
                            @if($total_agr > 0)
                            <tr>
                                <td style="text-align: left;">{{$key}} ISSPOL</td>
                                <!--td style="text-align: right;">{{number_format($value['pxq'], 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($value['iva'], 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($totalap, 2, ',', ' ')}} </td-->
                                <td style="text-align: right;">{{number_format($value['base0'], 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($value['base12'], 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($value['iva'], 2, ',', ' ')}} </td>
                                <td style="text-align: right;">{{number_format($value['admin'], 2, ',', ' ')}}</td>
                                <td style="text-align: right;">{{number_format($total_agr, 2, ',', ' ')}}</td>
                                <td style="text-align: center;">
                                    <form method="POST" action="#">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id_seguro" value="{{$seg}}" />
                                    <input type="hidden" name="mes_plano" value="{{$mes_plano}}" />
                                    <input type="hidden" name="tipo" value="{{$key}}" />
                                    <input type="hidden" name="id_empresa" value="{{$empresa}}" />
                                    <input type="hidden" name="id_cobertura_comp" value="6" />
                                    <button type="submit" formaction="{{route('aparchivo.resumen_agrupado')}}" class="btn btn-primary"><span class="glyphicon glyphicon-download-alt"> Reporte</span></button>
                                    </form>
                                    <!--a class="btn btn-info btn-xs oculto" href="{{route('aparchivo.plano_contable_ingresar',['aniomes' => $mes_plano, 'tipo' => $key, 'seg' => '2', 'cobertura' => $cobertura, 'empresa' => $empresa ])}}" data-toggle="modal" data-target="#codigo_proceso" id="boton_ingresar{{$key}}"> <span> INGRESAR </span></a-->
                                    <!--button class="btn btn-info btn-xs" onclick="ingresar_js('{{$key}}')"><span> INGRESAR </span></button-->
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>

                <!--div class="table-responsive col-md-12">

                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="text-align: center;">
                        <thead style="background-color: #4682B4">
                            <tr>
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
                </div-->


            </div>
        </div>
    </section>


<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
    function ingresar_js(key) {
        var confirmar = confirm("Desea Ingresar Valores para el tipo: " + key);
        if (confirmar) {
            $('#boton_ingresar' + key).trigger("click");
        }
    }

    function eliminar(id) {
        var confirmar = confirm("Desea Eliminar el registro");
        if (confirmar) {
            $.ajax({
                type: 'get',
                url: "{{ url('plano_contable/eliminar/registro')}}/" + id,
                datatype: 'html',
                success: function(datahtml) {
                    location.reload();
                },
                error: function() {
                    alert('error al cargar');
                }
            });
        }
    }
    /*$("#mes_plano").autocomplete({
        source: function( request, response ) {
            
            $.ajax({
                url:"{{route('search.mes_plano')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {term: request.term},
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    console.log(data);

                }
            })
        },
        minLength: 2,
    } );*/


    /*$(document).ready(function(){

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });

    });*/

    $('#example2').DataTable({
        'language': {
            'emptyTable': '<span class="label label-primary" style="font-size:14px;">No se encontraron registros.</span>'
        },
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': true,
        'info': false,
        'autoWidth': false
    })

    //Exportar Excel
    /*function exportar_iess_plano(){
        
        var formulario = document.forms["exporta_plano_iess"];
        var me_plano = formulario.mes_plano.value;
        var seg = formulario.seguro.value;
        var tip_seg = formulario.id_tipo_seguro.value;
        var cob_comp = formulario.id_cobertura_comp.value;
        var emp = formulario.id_empresa.value;
        
        var msj = "";

        if(me_plano == ""){
          msj = msj + "Por favor,Ingrese el mes de Plano<br/>";
        }

        if(seg == ""){
          msj = msj + "Por favor, Seleccione el Seguro<br/>";
        }

        if(tip_seg == ""){
          msj = msj + "Por favor, Seleccione el Tipo Seguro<br/>";
        }

        if(cob_comp == ""){
          msj = msj + "Por favor, Seleccione la Cobertura Compartida<br/>";
        }

        if(emp == ""){
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
        url:"{{route('planilla.genera_ap_excel')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#exporta_plano_iess").serialize(),
        success: function(data){
          console.log(data);
        },
        error: function(){
          console.log(data);
        }
      });
    
    }*/
    $('#codigo_proceso').on('hidden.bs.modal', function() {

        $(this).removeData('bs.modal');
        $('#boton_buscar').click();
    });

    function guardar(id) {

        //alert(id);

        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();
        var cantidad_exp = $('#cantidad_' + id).val();
        var base_0 = $('#base_0_' + id).val();
        var base_iva = $('#base_iva_' + id).val();
        var v_iva = $('#v_iva_' + id).val();
        var amd_10 = $('#amd_10_' + id).val();
        var total_iva = $('#total_iva_' + id).val();
        var codigo = $('#codigo_' + id).val();
        var tipo = $('#tipo').val();

        console.log(mes_plano, empresa, seguro, id, cantidad_exp, base_0, base_iva);

        $.ajax({
            type: 'post',
            url: "{{ route('archivo.guardar_agrupado')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                "tipo_seguro": id,
                "mes_plano": mes_plano,
                "seguro": seguro,
                "empresa": empresa,
                "cantidad": cantidad_exp,
                "base_0": base_0,
                "base_iva": base_iva,
                "v_iva": v_iva,
                "amd_10": amd_10,
                "total_iva": total_iva,
                "codigo": codigo,
                "tipo": tipo,
            },
            success: function(data) {
                console.log(data);
                $("#boton_buscar").click();
                if (data == "ok") {
                    swal({
                        title: "Datos Guardados",
                        icon: "success",
                        type: 'success',
                        buttons: true,
                    })

                };
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function guardar_objetar(id) {

        //alert(id);

        var mes_plano = $('#mes_plano').val();
        var seguro = $('#seguro').val();
        var empresa = $('#id_empresa').val();
        var cantidad_exp = $('#cantidad_' + id).val();
        var base_0 = $('#base_0_' + id).val();
        var base_iva = $('#base_iva_' + id).val();
        var v_iva = $('#v_iva_' + id).val();
        var amd_10 = $('#amd_10_' + id).val();
        var total_iva = $('#valor_cobrado_' + id).val();
        var codigo = $('#codigo_' + id).val();
        var valor_cobrado = $('#valor_cobrado_' + id).val();
        var tipo = $('#tipo2').val();


        console.log(mes_plano, empresa, seguro, id, cantidad_exp, base_0, base_iva);

        $.ajax({
            type: 'post',
            url: "{{ route('archivo.guardar_agrupado')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                "tipo_seguro": id,
                "mes_plano": mes_plano,
                "seguro": seguro,
                "empresa": empresa,
                "cantidad": cantidad_exp,
                "base_0": base_0,
                "base_iva": base_iva,
                "v_iva": v_iva,
                "amd_10": amd_10,
                "total_iva": total_iva,
                "codigo": codigo,
                "valor_cobrado": valor_cobrado,
                "tipo": tipo,
            },
            success: function(data) {
                console.log(data);
                $("#boton_buscar").click();
                if (data == "ok") {
                    swal({
                        title: "Datos Guardados",
                        icon: "success",
                        type: 'success',
                        buttons: true,
                    })

                };
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
</script>
@endsection