<!--Contenedor Listado de Examenes de Laboratorio-->
<div class="col-12" style="border-radius: 0px">
    <!--Div que contiene el Contenedor-->
    <div style="border: 2px solid #004AC1;background-color:#FFFFFF; border-radius: 10px;" id="{{$orden->id}}">
        <!--Div que contiene el Titulo-->
        <div class="col-12" class="box-header "
            style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; height: 35px; text-align: center;">
            <label class="box-title" style="background-color: #004AC1;  font-size: 16px">
                &nbsp;Listado de ex&aacute;menes de laboratorio
            </label>
        </div>
        <div class="col-12" style="padding: 7px;">
            <!--Realizamos la Consulta a la tabla Examen_Detalle-->
            <div class="row">
                @php
                
                $detalle = Sis_medico\Examen_Detalle::where('id_examen_orden',$orden->id)->join('examen as e','e.id','id_examen')->select('examen_detalle.*','e.secuencia')->orderBy('e.secuencia')->get();
                $resultados = $orden->resultados;
                @endphp
                @if($orden->seguro->tipo=='0')
                @php
                $agrupador = Sis_medico\Examen_Agrupador::all();
                @endphp
                @else
                @php
                $agrupador = Sis_medico\Examen_Agrupador_labs::orderBy('secuencia')->get();
                @endphp
                @endif
                @php
                $parametros = Sis_medico\Examen_Parametro::orderBy('orden')->get();
                @endphp
                <br>
                @foreach($agrupador as $value)
                @php
                $i_agrupador=0;
                $xycont = 0;
                @endphp
                @foreach($detalle as $value_detalle)
                @php
                $i=0;
                $xycont ++;
                if($orden->seguro->tipo == '0' ){
                $id_agrupador = $value_detalle->examen->id_agrupador;
                }else{
                $agrupador_part =
                DB::table('examen_agrupador_sabana')->where('id_examen',$value_detalle->examen->id)->first();
                $id_agrupador = 0;

                if(!is_null($agrupador_part)){
                $id_agrupador = $agrupador_part->id_examen_agrupador_labs;
                }
                }
                @endphp
                @if($value_detalle->examen->no_resultado=='0')
                @if($id_agrupador == $value->id)
                @if($value_detalle->examen->resultado_especial == null)
                @php
                if($value_detalle->examen->sexo_n_s=='0'){
                $parametro_nuevo = $parametros->where('id_examen', $value_detalle->id_examen)->where('sexo','3');
                }else{
                $parametro_nuevo = $parametros->where('id_examen',
                $value_detalle->id_examen)->where('sexo',$orden->paciente->sexo);
                }
                @endphp
                <div class="col-12">
                    @if($i_agrupador == 0)
                    <!--Contenedor Nombre Agrupador-->
                    <div
                        style="text-align: center;border-bottom: 1px solid #004AC1;padding: 0px;background-color: #004AC1;">
                        <!--Nombre Agrupador-->
                        <label style="color: white; padding-top: 2px;font-size: 12px;"> {{$value->nombre}}</label>
                        @php
                        $i_agrupador=1;
                        @endphp
                    </div>
                    <div style="width: 28%;float:left;color: #004AC1;"><b>NOMBRE</b></div>
                    <div style="width: 18%;float:left;color: #004AC1;"><b>RESULTADO</b></div>
                    <div style="width: 13%;float:left;color: #004AC1;"><b>UNIDADES</b></div>
                    <div style="width: 40%;float:left;color: #004AC1;"><b>REFERENCIA</b></div>
                    <div style="clear:both;"></div>
                    @endif
                    @if($i == 0)
                    @if($parametro_nuevo->count() > 1)
                    @if($value_detalle->id_examen!='639')
                    <div class="col-12" style="border-bottom: 1px solid #004AC1;padding: 0px">
                        <b>{{$value_detalle->examen->nombre}}</b>
                    </div>
                    @if($value_detalle->examen->texto!=null)
                    <div style="border-bottom: 1px solid #009a98;padding: 0px;">
                        <?php echo $value_detalle->examen->texto; ?>
                    </div>
                    @endif
                    @endif
                    @endif
                    @php
                    $i = 1;
                    @endphp
                    @endif
                    <div style="clear:both;"></div>
                    <div class="col-12" @if($parametro_nuevo->count() == 1) style="padding-left: 10px;" @endif>
                        <table id="example2" style="font-size: 12px;  width: 100%">
                            <tbody>
                                @if($value_detalle->id_examen=='661')
                                <tr style="font-size: 12px;">
                                    <td style="width: 20%;"><b>GRADO</b></td>
                                    <td style="width: 10%;"><b>% CELULAS</b></td>
                                    <td style="width: 8%;"><b>L.A.P. PUNTOS</b></td>
                                    <td style="width: 40%;"><b>REFERENCIA</b></td>
                                </tr>
                                @endif
                                @if($value_detalle->id_examen=='661')
                                @foreach($parametro_nuevo as $value_agrupador)
                                @if($value_agrupador->unidad1=='L.A.P. PUNTOS')
                                @php
                                $resultado = $orden->resultados->where('id_parametro', $value_agrupador->id)->first();
                                @endphp
                                <tr role="row">
                                    <td style="width: 30%;">
                                        {{$value_agrupador->nombre}}
                                    </td>
                                    @php
                                    $hermano =
                                    DB::table('examen_parametro')->where('id_examen',$value_detalle->id_examen)->where('orden',$value_agrupador->orden)->where('unidad1','%
                                    CELULAS')->first();
                                    $resultado_2 = null;
                                    if(!is_null($hermano)){
                                    $resultado_2 = $orden->resultados->where('id_parametro', $hermano->id)->first();
                                    }
                                    @endphp
                                    <td style="width: 20%;">
                                        <div style="word-wrap: break-word;">
                                            @if(!is_null($hermano))
                                            @if(!is_null($resultado_2))
                                            @if($resultado_2->certificado=='1')
                                            {{$resultado_2->valor}}
                                            @else
                                            {{"0"}}
                                            @endif
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td style="width: 10%;">
                                        @if(!is_null($resultado))
                                        {{$resultado->valor}}
                                        @else
                                        {{"0"}}
                                        @endif
                                    </td>
                                    <td style="width: 40%;">
                                        @if($value_agrupador->texto_referencia == "")
                                        {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                        @else
                                        <?php echo 
                                                                      $value_agrupador->texto_referencia; 
                                                                      ?>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach

                                @elseif($value_detalle->id_examen=='639')
                                <center>
                                    <b>ALERGIAS A ALIM. POR IGG - 59</b>
                                </center>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div style="background-color: #ACCBEE;font-size: 12px;text-align: center;">
                                            Reacción Leve</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div style="background-color: #8AB0DB;font-size: 12px;text-align: center;">
                                            Reacción Moderada</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            style="background-color: #376EAC;color: white;font-size: 12px;text-align: center;">
                                            Reacción Fuerte</div>
                                    </div>
                                </div>
                                <br>
                                @php $cuenta=0;
                                @endphp
                                <tr role="row">
                                    @foreach($parametro_nuevo as $value_agrupador)
                                    @php

                                    $rvalor=0;

                                    $resultado = $resultados->where('id_parametro',$value_agrupador->id)->first();

                                    if(!is_null($resultado)){
                                    $rvalor=$resultado->valor;

                                    }


                                    @endphp

                                    @if($value_agrupador->orden=='46' || $value_agrupador->orden=='48')
                                    @if($cuenta == 0)
                                <tr> @endif
                                    <td style="width: 3%; font-size: 10px; padding:0;">
                                        {{$value_agrupador->sec}}
                                    </td>
                                    <td style="width: 12%;  font-size: 10px;padding:0;">
                                        {{$value_agrupador->nombre}}
                                    </td>
                                    <td style="width: 3%;padding:0;">
                                    </td>
                                    <td style="width: 3%;padding:0;">
                                    </td>
                                    <td
                                        style="width: 3%; background-color:#A8A8A8; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">
                                        @if($value_agrupador->orden=='48') <span style="color: white">x</span> @else
                                        &nbsp; @endif
                                    </td>
                                    <td style="width: 3%;  font-size: 10px; padding:0;">
                                        &nbsp;
                                    </td>
                                    @php $cuenta++; @endphp
                                    @if($cuenta == 2)

                                </tr>
                                @endif
                                @else
                                @if($cuenta == 0) <tr> @endif
                                    <td style="width: 3%;  font-size: 10px; padding:0;">
                                        {{$value_agrupador->sec}}
                                    </td>
                                    <td style="width: 12%;  font-size: 10px;padding:0;">
                                        {{$value_agrupador->nombre}}
                                    </td>
                                    <td
                                        style="width: 3%; background-color:#ACCBEE ; border: 1px solid #FFF;text-align: center;font-size: 13px;padding:0;">
                                        @if($rvalor==1) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif
                                    </td>
                                    <td
                                        style="width: 3%; background-color:#8AB0DB; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">
                                        @if($rvalor==2) @if($resultado->certificado=='1') X @endif @else &nbsp; @endif
                                    </td>
                                    <td
                                        style="width: 3%; background-color:#376EAC; border: 1px solid #FFF;text-align: center; font-size: 13px;padding:0;">
                                        @if($rvalor==3) <span style="color: white">@if($resultado->certificado=='1') X
                                            @endif</span> @else &nbsp; @endif
                                    </td>
                                    <td style="width: 3%;  font-size: 10px; padding:0;">
                                        &nbsp;
                                    </td>
                                    @php $cuenta++; @endphp
                                    @if($cuenta == 2)
                                </tr>
                                @php
                                $cuenta=0;
                                @endphp
                                @endif
                                @endif
                                @endforeach

                                </tr>
                                <table>
                                    <tr role="row">
                                        <br>
                                        <br>
                                        <div>*19 Pescado Blanco Mix: Bacalao y Lenguado.</div>
                                        <div>*20 Pescado de Agua Dulce Mix: Salmón y Trucha.</div>
                                        <div>*22 Marisco Mix: Camarón, Langostino, Cangrejo, Langosta, Mejillones.</div>
                                        <div>*30 Mezclas de pimientos: rojo, verde y amarillo</div>
                                        <div>*31 Leguminosas: Arverjas, lentejas, fréjol, habas.</div>
                                        <div>*33 Melón Mix: Melón y Sandía</div>
                                        <div style="text-align: justify;"> Si sus resultados indican una reacción
                                            elevada al gluten, le recomendamos que evite todos los alimentos que
                                            contengan gliadina/gluten, aunque estos alimentos no muestren una respuesta
                                            positiva como el trigo, el centeno, la cebada, espelta, kamut, malta,
                                            esencia de malta, vinagre de malta, salvado, triticale, dextrina.</div>
                                        <div>Algunas personas con intolerancia al gluten son sensibles también a la
                                            avena. </div>


                                    </tr>
                                </table>

                                @else
                                @if(!(($value_detalle->id_examen == '414' || $value_detalle->id_examen == '412' ||
                                $value_detalle->id_examen == '680') && $orden->created_at<'2018-12-17' && $orden->
                                    id!='1543' && $orden->id!='1291'))



                                    @foreach($parametro_nuevo as $value_agrupador)
                                    @php
                                    $resultado = $orden->resultados
                                    ->where('id_parametro', $value_agrupador->id)
                                    ->first();
                                    @endphp

                                    @if(!is_null($resultado))
                                    @if($resultado->certificado=='1')
                                    <tr role="row">

                                        <td
                                            style="width: 30%;@if($parametro_nuevo->count() == 1) font-weight: 700;padding-left: -3px;font-size: 14px; @endif">
                                            {{$value_agrupador->nombre}}
                                        </td>

                                        <td style="width: 20%;">
                                            <div style="word-wrap: break-word;">
                                                @if(!is_null($resultado))
                                                {{$resultado->valor}}
                                                @else
                                                {{"0"}}
                                                @endif
                                            </div>
                                        </td>
                                        @php
                                        $fl_ref = 0;
                                        $ref_ant =
                                        Sis_medico\Examen_Parametro_Referencia_Anterior::where('id_parametro',$value_agrupador->id)->where('fecha_valida','>',$orden->fecha_orden)->orderBy('fecha_valida','asc')->first();
                                        if(!is_null($ref_ant)){
                                        $fl_ref = 1;
                                        }
                                        @endphp
                                        <td style="width: 10%;">
                                            @if($fl_ref) {{$ref_ant->unidad1}} @else {{$value_agrupador->unidad1}}
                                            @endif
                                        </td>

                                        <td style="width: 40%;">
                                            @if($fl_ref)
                                            @if($ref_ant->texto_referencia == "")
                                            {{$ref_ant->valor1}} - {{$ref_ant->valor1g}}
                                            @else
                                            <?php echo $ref_ant->texto_referencia; ?>
                                            @endif
                                            @else
                                            @if($value_agrupador->texto_referencia == "")
                                            {{$value_agrupador->valor1}} - {{$value_agrupador->valor1g}}
                                            @else
                                            <?php echo $value_agrupador->texto_referencia; ?>
                                            @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @endif
                                    @endforeach

                                    @if($value_detalle->examen->tiene_detalle=='1')
                                    @php
                                    $sub_resultados = DB::table('examen_sub_resultado')
                                    ->where('id_orden',$orden->id)
                                    ->where('estado','1')
                                    ->where('id_examen',$value_detalle
                                    ->id_examen)->get();
                                    @endphp
                                    <tr>
                                        <td colspan="4" id="sub_tabla">
                                            <table class="table table-bordered table-hover dataTable">
                                                <tbody>
                                                    @foreach($sub_resultados as $sub)
                                                    <tr>
                                                        <td
                                                            style="width: 300px !important;padding-top: 1px;padding-bottom: 1px;">
                                                            {{$sub->campo1}}</td>
                                                        <td
                                                            style="width: 300px !important;padding-top: 1px;padding-bottom: 1px;">
                                                            {{$sub->campo2}}</td>
                                                        <td
                                                            style="width: 300px !important;padding-top: 1px;padding-bottom: 1px;">
                                                            {{$sub->campo3}}</td>
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
                @else
                <!------------->
                <div class="col-md-12" style="text-align: center;">{{$value_detalle->examen->nombre}}</div>
                @php
                $parametros_nuevos = $parametros->where('id_examen',
                $value_detalle->id_examen)->where('id_parametro_ref','<>',null);
                    $xp_cant = 0; $limite2 = 0;
                    $ele_cant = count($parametros_nuevos);
                    @endphp


                    <table class="table table-striped" style="width: 95%">
                        <tr>
                            <td style="font-size: 13px;width:1%;"></td>
                            <td style="font-size: 13px;width:19%;"></td>
                            <td
                                style="font-size: 13px;border: 1px solid #FFF;text-align: center;font-size: 13px; width: 15%;">
                                NEGATIVO</td>
                            <td
                                style="font-size: 13px;border: 1px solid #FFF;text-align: center;font-size: 13px; width: 15%;">
                                POSITIVO</td>
                            <td style="font-size: 13px;width:1%;"></td>
                            <td style="font-size: 13px;width:19%;"></td>
                            <td
                                style="font-size: 13px;border: 1px solid #FFF;text-align: center;font-size: 13px; width: 15%;">
                                NEGATIVO</td>
                            <td
                                style="font-size: 13px;border: 1px solid #FFF;text-align: center;font-size: 13px; width: 15%;">
                                POSITIVO</td>
                        </tr>
                        <tr>
                            @foreach($parametros_nuevos as $px_n)
                            @php
                            $xresultado = $orden->resultados
                            ->where('id_parametro', $px_n->id)
                            ->where('certificado','1')
                            ->first();
                            if(!is_null($xresultado)){
                            $xp_cant ++; $limite2 ++;
                            }

                            $color1 = '#ACCBEE'; $color2 = '#8AB0DB';
                            if($px_n->id_parametro_ref == '1103'){
                            $color2 = '#76D7C4'; $color1 = '#A3E4D7';
                            }
                            if($px_n->id_parametro_ref == '1146'){
                            $color1 = '#FADBD8'; $color2 = '#FDEDEC';
                            }

                            @endphp
                            @if(!is_null($xresultado))
                            <td style="font-size: 13px;width:1%;"> {{$xp_cant}}</td>
                            <td style="font-size: 13px;width:19%;"> {{$xresultado->parametro->nombre}}</td>
                            <td
                                style="font-size: 13px;background-color:{{$color1}} ; border: 1px solid #FFF;text-align: center;font-size: 13px; width: 15%;">
                                @if($xresultado->valor == 'NEGATIVO') X @endif</td>
                            <td
                                style="font-size: 13px;background-color:{{$color2}} ; border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">
                                @if($xresultado->valor == 'POSITIVO') X @endif</td>
                            @if($limite2 == 2) @php $limite2 = 0; @endphp
                        </tr>
                        <tr> @endif
                            @endif
                            @endforeach
                    </table>

                    @php
                    $parametros_nuevos2 = $parametros->where('id_examen',
                    $value_detalle->id_examen)->where('id_parametro_ref',null);

                    @endphp


                    <table class="table table-striped" style="width: 50%">
                        <tr>
                            <td style="font-size: 15px;width:1%;"></td>
                            <td style="font-size: 15px;width:19%;"></td>
                            <td
                                style="font-size: 15px;border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">
                                NEGATIVO</td>
                            <td
                                style="font-size: 15px;border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">
                                POSITIVO</td>
                        </tr>

                        @foreach($parametros_nuevos2 as $px_n)
                        @php
                        $xresultado = $orden->resultados
                        ->where('id_parametro', $px_n->id)
                        ->where('certificado','1')
                        ->first();

                        if(!is_null($xresultado)){
                        $xp_cant ++;
                        }


                        $color1 = '#ACCBEE'; $color2 = '#8AB0DB';
                        if($px_n->id == '1103'){
                        $color2 = '#76D7C4'; $color1 = '#A3E4D7';
                        }
                        if($px_n->id == '1146'){
                        $color1 = '#FADBD8'; $color2 = '#FDEDEC';
                        }
                        @endphp
                        @if(!is_null($xresultado))
                        <tr>
                            <td style="font-size: 15px;width:1%;"> {{$xp_cant}} </td>
                            <td style="font-size: 15px;width:19%;"> {{$xresultado->parametro->nombre}} </td>
                            <td
                                style="font-size: 15px;background-color:{{$color1}} ; border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">
                                @if($xresultado->valor == 'NEGATIVO') X @endif </td>
                            <td
                                style="font-size: 15px;background-color:{{$color2}} ; border: 1px solid #FFF;text-align: center;font-size: 15px; width: 15%;">
                                @if($xresultado->valor == 'POSITIVO') X @endif </td>
                        </tr>
                        @endif
                        @endforeach
                    </table>

                    <!------------->
                    @endif
                    @endif
                    @endif
                    @endforeach
                    @endforeach
            </div>
        </div>
    </div>
</div>