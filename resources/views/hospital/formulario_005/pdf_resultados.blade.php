<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 200px 70px;
        }

        #header {
            position: fixed;
            left: 0px;
            top: -180px;
            right: 0px;
            height: 150px;
            text-align: center;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: -220px;
            right: 0px;
            height: 150px;
        }
    </style>

</head>

<body>

    <script type="text/php">
        if (isset($pdf)) {
          $font = $fontMetrics->getFont("Arial", "bold");
          $pdf->page_text(555, 80, "{PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
        }
  </script>


    <div id="header">

        <div class="col-md-12" style="width: 1122px;border-bottom: 0.5px solid #009a98;border-top: 0.5px solid #ec6c25;">
            <img src="{{base_path().'/public/imagenes/labs_res.jpg'}}" align=center>
        </div>
        <div style="border-bottom: 1px solid #009a98;padding: 0px;width: 1122px;">
            <table style="font-size: 16px;color: #009a98;">
                <tbody>
                    <tr role="row">
                        <td width="50" style="background-color: #e5f5f5;height: 20px;"><b>PACIENTExxx</b></td>
                        <td width="206" style="color: black;">{{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif {{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif</td>
                        <td width="50" style="background-color: #e5f5f5;"><b>{{trans('hospitalizacion.CEDULA')}}</b></td>
                        <td width="40" style="color: black;">{{$orden->id_paciente}}</td>
                        <td width="45" style="background-color: #e5f5f5;"><b>{{trans('hospitalizacion.EDAD')}}</b></td>
                        <td width="40" style="color: black;">{{$age}}</td>
                        <td width="50" style="background-color: #e5f5f5;"><b>SEXO</b></td>
                        <td width="40">@if($orden->paciente->sexo=='1')Masculino @elseif($orden->paciente->sexo=='2') Femenino @endif</td>
                        <td width="50" style="background-color: #e5f5f5;"></td>
                    </tr>
                    <tr role="row" style="background-color: #e5f5f5;">
                        <td width="50"></td>
                        <td width="200"></td>
                        <td width="50"></td>
                        <td width="40"></td>
                        <td width="50"></td>
                        <td width="40"></td>
                        <td width="50"></td>
                        <td width="40"></td>
                        <td width="50"></td>
                    </tr>
                    <tr role="row">
                        <td style="background-color: #e5f5f5;height: 20px;"><b>ANÁLISIS</b></td>
                        <td>&nbsp;</td>
                        <td style="background-color: #e5f5f5;"><b>{{trans('hospitalizacion.FECHA')}}</b></td>
                        <td style="color: black;">@if($orden->seguro->tipo == '0')@if($orden->fecha_convenios == null){{substr($orden->fecha_orden,0,10)}}@else{{$orden->fecha_convenios}}@endif @else {{substr($orden->fecha_orden,0,10)}} @endif</td>
                        <td style="background-color: #e5f5f5;"><b>{{trans('hospitalizacion.HORA')}}</b></td>
                        <td style="color: black;">{{substr($orden->created_at,10,10)}}</td>
                        <td style="background-color: #e5f5f5;"><b>{{trans('hospitalizacion.PÁGINA')}}</b></td>
                        <td>&nbsp;</td>
                        <td width="50" style="background-color: #e5f5f5;"></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <div align="right" class="col-md-12" style="font-size: 18px">
        @php
        if($orden->completo == '0'){
        if($pct<'100'){ echo "INFORME PARCIAL" ; } } @endphp </div>

            <div id="footer">
                <img style='position: absolute; top: -100px; left: 450px;' width=320 height=150 src="{{base_path().'/storage/app/logo/labs.png'}}" align=center hspace=12>
                <div align="right">
                    <p style="font-size: 12px;">{{substr($ucreador->nombre1,0,1)}}{{$ucreador->apellido1}}</p>
                </div>
            </div>


            <div id="content">

                @foreach($agrupador as $value)
                @php $i_agrupador=0; @endphp
                @foreach($detalle as $value_detalle)
                @php
                $i=0;
                if($orden->seguro->tipo == '0' ){
                $id_agrupador = $value_detalle->examen->id_agrupador;
                }else{
                $agrupador_part = DB::table('examen_agrupador_sabana')->where('id_examen',$value_detalle->examen->id)->first();

                $id_agrupador = 0;
                if(!is_null($agrupador_part)){
                $id_agrupador = $agrupador_part->id_examen_agrupador_labs;
                }
                }
                @endphp
                @if($value_detalle->examen->no_resultado=='0')
                @if($id_agrupador == $value->id)
                @php

                if($value_detalle->examen->sexo_n_s=='0'){
                $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                }else{
                $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                }

                @endphp
                <div class="col-md-12">
                    @if($i_agrupador == 0)
                    <div style="text-align: center;border-bottom: 1px solid #009a98;padding: 0px;background-color: #e5f5f5;width: 1122px;">
                        <b>{{$value->nombre}}</b>
                        @php $i_agrupador=1; @endphp
                    </div>
                    <div style="width: 30%;float:left;font-size: 19px;"><b>{{trans('hospitalizacion.NOMBRE')}}</b></div>
                    <div style="width: 20%;float:left;font-size: 19px;"><b>{{trans('hospitalizacion.RESULTADO')}}</b></div>
                    <div style="width: 15%;float:left;font-size: 19px;"><b>{{trans('hospitalizacion.UNIDADES')}}</b></div>
                    <div style="width: 35%;float:left;font-size: 19px;"><b>{{trans('hospitalizacion.REFERENCIA')}}</b></div>

                    <div style="clear:both;"></div>
                    @endif
                    @if($i == 0)
                    @if($parametro_nuevo->count() > 1)
                    <div style="border-bottom: 1px solid #009a98;padding: 0px;clear:both;">
                        <b style="font-size: 20px;">{{$value_detalle->examen->nombre}}</b>
                    </div>
                    @endif
                    @php $i = 1; @endphp
                    @endif
                    <div style="clear:both;"></div>
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 18px;table-layout: fixed;width: 100%;">
                            <tbody>
                                @if($value_detalle->id_examen=='661')
                                <tr style="font-size: 19px;">
                                    <td style="width: 30%;"><b>{{trans('hospitalizacion.GRADO')}}</b></td>
                                    <td style="width: 20%;"><b>{{trans('hospitalizacion.%CELULAS')}}</b></td>
                                    <td style="width: 15%;"><b>{{trans('hospitalizacion.L.A.P.PUNTOS')}}</b></td>
                                    <td style="width: 35%;"><b>{{trans('hospitalizacion.REFERENCIA')}}</b></td>
                                </tr>
                                @endif
                                @if($value_detalle->id_examen=='661')
                                @foreach($parametro_nuevo as $value_agrupador)
                                @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                @php
                                $resultado = $resultados->where('id_parametro', $value_agrupador->id)->first();
                                @endphp
                                <tr role="row">
                                    <td style="width: 30%;">{{$value_agrupador->nombre}}</td>
                                    @php
                                    $hermano = DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','% CELULAS')->first();
                                    $resultado_2 = null;
                                    if(!is_null($hermano)){
                                    $resultado_2 = $resultados->where('id_parametro', $hermano->id)->first();
                                    }

                                    @endphp
                                    <td style="width: 20%;">
                                        <div style="word-wrap: break-word;">
                                            @if(!is_null($hermano))
                                            @if(!is_null($resultado_2)) @if($resultado_2->certificado=='1') {{$resultado_2->valor}}@else{{"0"}} @endif @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td style="width: 15%;">@if(!is_null($resultado)) {{$resultado->valor}}@else{{"0"}} @endif</td>
                                    <td style="width: 35%;">@if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>

                                </tr>

                                @endif
                                @endforeach
                                @else
                                @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' || $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->id!='1543' && $orden->id!='1291'))
                                    @foreach($parametro_nuevo as $value_agrupador)
                                    @php
                                    $resultado = $resultados->where('id_parametro', $value_agrupador->id)->first();
                                    @endphp

                                    @if(!is_null($resultado))
                                    @if($resultado->certificado=='1')
                                    <tr role="row">
                                        <td style="width: 30%;padding-left: 3px;@if($parametro_nuevo->count() == 1) font-size: 20px;font-weight: 800;padding-left: -3px; @endif">{{$value_agrupador->nombre}}</td>
                                        <td style="width: 20%;">
                                            <div style="word-wrap: break-word;">
                                                @if(!is_null($resultado)){{$resultado->valor}}@else{{"0"}}@endif
                                            </div>
                                        </td>
                                        <td style="width: 15%;">{{$value_agrupador->unidad1}}</td>
                                        <td style="width: 35%;">@if($value_agrupador->texto_referencia == ""){{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}@else <?php echo $value_agrupador->texto_referencia; ?> @endif</td>

                                    </tr>
                                    @endif
                                    @endif

                                    @endforeach
                                    @if($value_detalle->examen->tiene_detalle=='1')
                                    @php
                                    $sub_resultados = DB::table('examen_sub_resultado')->where('id_orden',$orden->id)->where('estado','1')->where('id_examen',$value_detalle->id_examen)->get();
                                    @endphp
                                    <tr>
                                        <td colspan="4" id="sub_tabla">
                                            <table class="table table-bordered table-hover dataTable">
                                                <tbody>
                                                    @foreach($sub_resultados as $sub)
                                                    <tr>
                                                        <td style="width: 300px !important;">{{$sub->campo1}}</td>
                                                        <td style="width: 300px !important;">{{$sub->campo2}}</td>
                                                        <td style="width: 300px !important;">{{$sub->campo3}}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>

                                    @endif
                                    @endif
                                    @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                @endif
                @endif
                @endforeach
                @endforeach

            </div>


</body>

</html>